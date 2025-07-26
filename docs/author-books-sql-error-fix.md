# Fix Lỗi SQL: Field 'id' doesn't have a default value trong bảng author_books

## Mô tả lỗi

**Lỗi gặp phải:**
```
SQLSTATE[HY000]: General error: 1364 Field 'id' doesn't have a default value 
(Connection: mysql, SQL: insert into `author_books` (`author_id`, `book_id`) values (...))
```

**Nguyên nhân:**
- Bảng `author_books` có trường `id` là UUID primary key
- Khi sử dụng raw SQL insert hoặc sync() method mà không có pivot model, Laravel không tự động tạo UUID cho trường `id`
- Code cũ trong `AdminBookController.php` sử dụng raw SQL insert với UUID thủ công, nhưng thiếu `created_at` và `updated_at`

## Giải pháp

### 1. Tạo Pivot Model AuthorBook

Tạo file `app/Models/AuthorBook.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Str;

class AuthorBook extends Pivot
{
    protected $table = 'author_books';
    
    protected $fillable = [
        'book_id',
        'author_id'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    protected $keyType = 'string';
    public $incrementing = false;
}
```

### 2. Cập nhật Model Book

Trong `app/Models/Book.php`, cập nhật relationship `authors()`:

```php
public function authors(): BelongsToMany
{
    return $this->belongsToMany(Author::class, 'author_books')
        ->using(AuthorBook::class);
}
```

### 3. Cập nhật Model Author

Trong `app/Models/Author.php`, cập nhật relationship `books()`:

```php
public function books()
{
    return $this->belongsToMany(Book::class, 'author_books')
        ->using(AuthorBook::class);
}
```

### 4. Sửa AdminBookController

Trong `app/Http/Controllers/Admin/AdminBookController.php`, thay thế raw SQL insert bằng sync():

**Trước (có lỗi):**
```php
if ($request->has('author_ids')) {
    foreach ($request->input('author_ids') as $authorId) {
        \DB::table('author_books')->insert([
            'id' => Str::uuid(),
            'book_id' => $book->id,
            'author_id' => $authorId,
        ]);
    }
}
```

**Sau (đã fix):**
```php
$book->authors()->sync($request->input('author_ids', []));
```

## Lợi ích của giải pháp

### 1. **Tự động tạo UUID**
- Pivot model tự động tạo UUID cho trường `id`
- Không cần tạo UUID thủ công

### 2. **Tự động quản lý timestamps**
- Laravel tự động thêm `created_at` và `updated_at`
- Không cần thêm thủ công

### 3. **Code sạch hơn**
- Sử dụng Eloquent thay vì raw SQL
- Dễ bảo trì và mở rộng

### 4. **Nhất quán**
- Cả `store()` và `update()` method đều sử dụng `sync()`
- Không có sự khác biệt trong cách xử lý

## Cách test

1. **Test tạo sách mới:**
   - Vào admin panel
   - Tạo sách mới với nhiều tác giả
   - Kiểm tra không có lỗi SQL

2. **Test cập nhật sách:**
   - Chỉnh sửa sách hiện có
   - Thay đổi danh sách tác giả
   - Kiểm tra cập nhật thành công

3. **Test database:**
   - Kiểm tra bảng `author_books` có UUID trong trường `id`
   - Kiểm tra `created_at` và `updated_at` được tự động thêm

## Lưu ý

- Pivot model `AuthorBook` kế thừa từ `Pivot` class, không phải `Model`
- Sử dụng `using()` method để chỉ định pivot model trong relationship
- Đảm bảo cả hai model `Book` và `Author` đều sử dụng cùng pivot model

---

**Ngày tạo:** $(date)
**Tác giả:** AI Assistant  
**Phiên bản:** 1.0