# Conditional Display - Thuộc Tính Sách

## 🎯 Mục Đích

Tính năng **Conditional Display** giúp ẩn/hiện phần "Thuộc tính sách" chỉ khi người dùng chọn định dạng "Sách vật lý", tạo trải nghiệm người dùng tốt hơn và tránh nhầm lẫn.

## 🔧 Cách Hoạt Động

### Logic Điều Kiện
- **Khi chọn "Sách vật lý"**: Hiển thị phần "Thuộc tính sách"
- **Khi bỏ chọn "Sách vật lý"**: Ẩn phần "Thuộc tính sách"
- **Chỉ chọn "Ebook"**: Phần "Thuộc tính sách" vẫn ẩn

### Workflow
```
Người dùng vào trang thêm/sửa sách
↓
Phần "Thuộc tính sách" ẩn mặc định (trang create)
Hoặc hiển thị nếu đã có sách vật lý (trang edit)
↓
Người dùng tick checkbox "Sách vật lý"
↓
Phần "Thuộc tính sách" hiển thị
↓
Người dùng có thể thêm thuộc tính như màu sắc, kích thước...
```

## 💻 Implementation

### 1. HTML Structure

#### Trang Create
```html
<!-- Thuộc tính sách - Ẩn mặc định -->
<div class="card shadow-sm mb-4" id="attributes_section" style="display: none;">
    <div class="card-header bg-purple text-white">
        <h5 class="mb-0">
            <i class="ri-price-tag-3-line me-2"></i>Thuộc tính sách
        </h5>
    </div>
    <div class="card-body">
        <!-- Alert thông báo -->
        <div class="alert alert-info border-0">
            <strong>Thuộc tính sách chỉ áp dụng cho định dạng Sách Vật Lý.</strong>
        </div>
        <!-- Form thuộc tính -->
    </div>
</div>
```

#### Trang Edit
```html
<!-- Thuộc tính sách - Hiển thị theo trạng thái hiện tại -->
<div class="card shadow-sm mb-4" id="attributes_section">
    <!-- Nội dung tương tự -->
</div>
```

### 2. JavaScript Logic

```javascript
// Toggle format sections
function toggleFormatSections() {
    const physicalCheckbox = document.getElementById('has_physical');
    const physicalForm = document.getElementById('physical_format');
    const ebookCheckbox = document.getElementById('has_ebook');
    const ebookForm = document.getElementById('ebook_format');
    const attributesSection = document.getElementById('attributes_section');
    
    // Toggle form sách vật lý
    if (physicalCheckbox && physicalForm) {
        physicalForm.style.display = physicalCheckbox.checked ? 'block' : 'none';
    }
    
    // Toggle form ebook
    if (ebookCheckbox && ebookForm) {
        ebookForm.style.display = ebookCheckbox.checked ? 'block' : 'none';
    }
    
    // Toggle phần thuộc tính - CHỈ hiện khi chọn sách vật lý
    if (physicalCheckbox && attributesSection) {
        attributesSection.style.display = physicalCheckbox.checked ? 'block' : 'none';
    }
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    const physicalCheckbox = document.getElementById('has_physical');
    const ebookCheckbox = document.getElementById('has_ebook');
    
    if (physicalCheckbox) {
        physicalCheckbox.addEventListener('change', toggleFormatSections);
    }
    
    if (ebookCheckbox) {
        ebookCheckbox.addEventListener('change', toggleFormatSections);
    }
    
    // Khởi tạo trạng thái ban đầu
    toggleFormatSections();
});
```

## 📁 Files Đã Cập Nhật

### 1. Create Page
**File**: `resources/views/admin/books/create.blade.php`

**Thay đổi**:
- **Dòng 176**: Thêm `id="attributes_section"` và `style="display: none;"`
- **Dòng 505**: Thêm `const attributesSection = document.getElementById('attributes_section');`
- **Dòng 517-521**: Thêm logic ẩn/hiện thuộc tính

### 2. Edit Page
**File**: `resources/views/admin/books/edit.blade.php`

**Thay đổi**:
- **Dòng 182**: Thêm `id="attributes_section"`
- **Dòng 598**: Thêm `const attributesSection = document.getElementById('attributes_section');`
- **Dòng 609-613**: Thêm logic ẩn/hiện thuộc tính

## 🎨 User Experience

### Trước Khi Cải Tiến
❌ **Vấn đề**:
- Phần thuộc tính luôn hiển thị
- Người dùng có thể nhầm lẫn thêm thuộc tính cho ebook
- Giao diện rối mắt với quá nhiều thông tin

### Sau Khi Cải Tiến
✅ **Lợi ích**:
- Giao diện gọn gàng, chỉ hiện thông tin cần thiết
- Logic rõ ràng: thuộc tính chỉ cho sách vật lý
- Tránh nhầm lẫn và lỗi nhập liệu
- Trải nghiệm người dùng mượt mà

## 🔍 Test Cases

### Test Case 1: Trang Create
1. **Mở trang thêm sách mới**
   - ✅ Phần "Thuộc tính sách" ẩn
2. **Tick checkbox "Sách vật lý"**
   - ✅ Phần "Thuộc tính sách" hiển thị
3. **Bỏ tick checkbox "Sách vật lý"**
   - ✅ Phần "Thuộc tính sách" ẩn
4. **Chỉ tick "Ebook"**
   - ✅ Phần "Thuộc tính sách" vẫn ẩn

### Test Case 2: Trang Edit
1. **Mở sách có định dạng vật lý**
   - ✅ Phần "Thuộc tính sách" hiển thị
   - ✅ Checkbox "Sách vật lý" đã tick
2. **Mở sách chỉ có ebook**
   - ✅ Phần "Thuộc tính sách" ẩn
   - ✅ Checkbox "Sách vật lý" chưa tick
3. **Thay đổi từ vật lý sang ebook**
   - ✅ Phần "Thuộc tính sách" ẩn khi bỏ tick

## 🚀 Tính Năng Mở Rộng

### 1. Animation Smooth
```javascript
// Thêm hiệu ứng mượt mà
if (physicalCheckbox && attributesSection) {
    if (physicalCheckbox.checked) {
        attributesSection.style.display = 'block';
        attributesSection.style.opacity = '0';
        setTimeout(() => {
            attributesSection.style.transition = 'opacity 0.3s ease';
            attributesSection.style.opacity = '1';
        }, 10);
    } else {
        attributesSection.style.transition = 'opacity 0.3s ease';
        attributesSection.style.opacity = '0';
        setTimeout(() => {
            attributesSection.style.display = 'none';
        }, 300);
    }
}
```

### 2. Validation Conditional
```javascript
// Chỉ validate thuộc tính khi sách vật lý được chọn
function validateForm() {
    const physicalCheckbox = document.getElementById('has_physical');
    const attributeInputs = document.querySelectorAll('#attributes_section input');
    
    if (physicalCheckbox && physicalCheckbox.checked) {
        // Validate thuộc tính
        return validateAttributes(attributeInputs);
    }
    
    return true; // Bỏ qua validation thuộc tính
}
```

### 3. Auto-clear Data
```javascript
// Xóa dữ liệu thuộc tính khi bỏ chọn sách vật lý
if (!physicalCheckbox.checked) {
    const attributeInputs = document.querySelectorAll('#attributes_section input');
    attributeInputs.forEach(input => {
        if (input.type !== 'hidden') {
            input.value = '';
        }
    });
    
    // Xóa các thuộc tính đã chọn
    const selectedVariants = document.querySelectorAll('.selected-variants-container .variant-item');
    selectedVariants.forEach(item => item.remove());
}
```

## 📊 Performance Impact

### Tích Cực
- **Giảm DOM rendering**: Ít element hiển thị
- **Tăng tốc độ tải**: JavaScript ít phải xử lý
- **UX tốt hơn**: Giao diện responsive hơn

### Lưu Ý
- **JavaScript dependency**: Cần JavaScript để hoạt động
- **Fallback**: Nên có fallback cho trường hợp JS bị tắt

## 🔗 Tài Liệu Liên Quan

- [Sửa Lỗi Route và Cải Tiến Thuộc Tính](sua-loi-route-va-cai-tien-thuoc-tinh.md)
- [Phân Biệt Thuộc Tính và Định Dạng Sách](phan-biet-thuoc-tinh-va-dinh-dang-sach.md)
- [Quản Lý Quà Tặng và Thuộc Tính Sách](quan-ly-qua-tang-va-thuoc-tinh-sach.md)

---

**Tóm tắt**: Tính năng Conditional Display giúp tối ưu trải nghiệm người dùng bằng cách chỉ hiển thị phần thuộc tính sách khi thực sự cần thiết (khi chọn sách vật lý), tránh nhầm lẫn và tạo giao diện gọn gàng hơn.