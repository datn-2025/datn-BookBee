# Hướng Dẫn Chạy Dự Án BookBee Bằng XAMPP Hoặc Laravel

Tài liệu này được viết sau khi rà soát cấu trúc dự án hiện tại. Dự án là ứng dụng Laravel 12, dùng MySQL/MariaDB, Composer, Node.js/NPM và Vite để build giao diện.

## 1. Thông tin dự án đã rà soát

- Framework backend: Laravel `^12.0`, lock hiện tại là Laravel `v12.18.0`.
- Yêu cầu PHP trong `composer.json`: `^8.2`.
- Khuyến nghị PHP thực tế: PHP `8.2`, `8.3` hoặc `8.4`. Không nên dùng PHP `8.5` cho dự án này vì `composer.lock` hiện có package giới hạn dưới PHP 8.5.
- Frontend: Vite, Tailwind, Laravel Echo, Pusher JS.
- Database khuyến nghị: MySQL/MariaDB qua XAMPP.
- Queue mặc định: `database`, nên chạy thêm queue worker nếu test email, thông báo, xử lý nền.
- Upload/file public: dự án dùng nhiều đường dẫn `asset('storage/...')`, cần chạy `php artisan storage:link`.
- Seeder có sẵn tài khoản mẫu sau khi chạy `php artisan migrate:fresh --seed`.

Các file chính đã kiểm tra:

- `composer.json`
- `composer.lock`
- `package.json`
- `.env.example`
- `config/database.php`
- `config/services.php`
- `config/broadcasting.php`
- `config/filesystems.php`
- `database/seeders/DatabaseSeeder.php`
- `routes/web.php`
- `routes/api.php`

## 2. Chuẩn bị môi trường

### 2.1. Phần mềm cần cài

Cài các phần mềm sau trước khi chạy dự án:

- XAMPP có PHP `8.2` đến `8.4`.
- Composer 2.x.
- Node.js từ `18` trở lên.
- NPM đi kèm Node.js.
- Git nếu tải source bằng `git clone`.

Kiểm tra phiên bản:

```bash
php -v
composer --version
node -v
npm -v
```

Lưu ý với Windows dùng XAMPP: nếu lệnh `php -v` không ra PHP của XAMPP hoặc ra PHP quá mới/quá cũ, kiểm tra lại biến môi trường `PATH`. PHP của XAMPP thường nằm ở:

```text
C:\xampp\php
```

Có thể kiểm tra PHP đang được terminal sử dụng bằng:

```bat
where php
```

Trên macOS/Linux:

```bash
which php
```

### 2.2. Extension PHP nên bật trong XAMPP

Mở file `php.ini` của XAMPP và đảm bảo các extension sau không bị comment:

```ini
extension=curl
extension=fileinfo
extension=gd
extension=intl
extension=mbstring
extension=openssl
extension=pdo_mysql
extension=zip
```

Sau khi sửa `php.ini`, restart Apache/MySQL trong XAMPP.

## 3. Tải source và cài dependencies

Mở terminal tại thư mục muốn chứa dự án.

Nếu dùng XAMPP trên Windows, thư mục thường là:

```text
C:\xampp\htdocs
```

Clone dự án:

```bash
git clone https://github.com/datn-2025/datn-BookBee.git
cd datn-BookBee
```

Nếu đã có source sẵn thì chỉ cần `cd` vào thư mục dự án:

```bash
cd /duong-dan-den/datn-BookBee
```

Cài package PHP:

```bash
composer install
```

Cài package frontend:

```bash
npm install
```

Trong workspace hiện tại sau khi rà soát chưa có thư mục `vendor` và `node_modules`, vì vậy hai lệnh trên là bắt buộc sau khi clone hoặc copy source mới.

## 4. Tạo database bằng XAMPP

Mở XAMPP Control Panel và start `MySQL`.

Vào phpMyAdmin:

```text
http://localhost/phpmyadmin
```

Tạo database mới:

```text
bookbee
```

Khuyến nghị chọn collation:

```text
utf8mb4_unicode_ci
```

Hoặc chạy SQL trực tiếp:

```sql
CREATE DATABASE bookbee CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Tài khoản MySQL mặc định của XAMPP thường là:

```text
DB_USERNAME=root
DB_PASSWORD=
```

Nếu máy bạn đã đặt mật khẩu MySQL thì điền đúng mật khẩu đó trong file `.env`.

## 5. Cấu hình file `.env`

Tạo file `.env` từ `.env.example`.

Windows CMD:

```bat
copy .env.example .env
```

PowerShell:

```powershell
Copy-Item .env.example .env
```

macOS/Linux:

```bash
cp .env.example .env
```

Mở file `.env` và chỉnh các dòng quan trọng như sau:

```dotenv
APP_NAME=BookBee
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000
APP_TIMEZONE=Asia/Ho_Chi_Minh

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bookbee
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=file
CACHE_STORE=database
QUEUE_CONNECTION=database
FILESYSTEM_DISK=public

BROADCAST_CONNECTION=log
MAIL_MAILER=log
```

Tạo app key:

```bash
php artisan key:generate
```

Lưu ý: `.env.example` hiện đang để `DB_CONNECTION=sqlite`, nhưng dự án này nên chạy bằng MySQL/MariaDB vì seeders có câu lệnh MySQL như `SET FOREIGN_KEY_CHECKS=0`.

## 6. Cấu hình dịch vụ ngoài tùy nhu cầu

Các biến dưới đây không bắt buộc nếu chỉ chạy giao diện, đăng nhập, dữ liệu mẫu cơ bản. Nếu test thanh toán, vận chuyển, đăng nhập Google, chatbot hoặc realtime thì cần cấu hình thêm.

### 6.1. Mail

Để chạy local đơn giản, dùng log:

```dotenv
MAIL_MAILER=log
MAIL_FROM_ADDRESS=no-reply@bookbee.local
MAIL_FROM_NAME="${APP_NAME}"
```

Nếu muốn gửi mail thật qua Gmail, dùng mật khẩu ứng dụng:

```dotenv
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your_email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

### 6.2. VNPay

Nếu test thanh toán VNPay sandbox:

```dotenv
VNP_TMNCODE=your_vnpay_tmn_code
VNP_HASHSECRET=your_vnpay_hash_secret
VNP_URL=https://sandbox.vnpayment.vn/paymentv2/vpcpay.html
```

Callback trong source:

- Đơn hàng thường: `/vnpay/return`
- Đặt trước sách: `/preorder/vnpay/return`
- Nạp ví: `/wallet/vnpay-return`

### 6.3. Google Login

Nếu test đăng nhập Google:

```dotenv
GOOGLE_CLIENT_ID=your_google_client_id
GOOGLE_CLIENT_SECRET=your_google_client_secret
GOOGLE_REDIRECT=http://127.0.0.1:8000/auth/google/callback
```

Nếu dùng VirtualHost XAMPP, đổi domain trong `GOOGLE_REDIRECT`, ví dụ:

```dotenv
GOOGLE_REDIRECT=http://bookbee.test/auth/google/callback
```

### 6.4. Pusher realtime

Nếu chỉ chạy local cơ bản, có thể để:

```dotenv
BROADCAST_CONNECTION=log
```

Khi đó một số tính năng realtime như chat/thông báo có thể không realtime đầy đủ.

Nếu cần realtime bằng Pusher:

```dotenv
BROADCAST_CONNECTION=pusher
BROADCAST_DRIVER=pusher

PUSHER_APP_ID=your_pusher_app_id
PUSHER_APP_KEY=your_pusher_app_key
PUSHER_APP_SECRET=your_pusher_app_secret
PUSHER_APP_CLUSTER=ap1

VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
```

Sau khi đổi biến `VITE_*`, cần restart `npm run dev`.

### 6.5. Gemini/OpenAI chatbot và AI summary

```dotenv
GEMINI_API_KEY=your_gemini_api_key
OPENAI_API_KEY=your_openai_api_key
```

Trong code hiện tại chatbot đang dùng `GEMINI_API_KEY`.

### 6.6. GHN vận chuyển

Qua rà soát, `config/services.php` đang đọc các biến sau:

```dotenv
GHN_API_URL=https://dev-online-gateway.ghn.vn
GHN_API_KEY=your_ghn_token
GHN_SHOP_ID=your_ghn_shop_id
GHN_SHOP_DISTRICT_ID=1442
GHN_SHOP_WARD_CODE=21211
```

Lưu ý quan trọng: `.env.example` hiện có các tên `GHN_TOKEN`, `GHN_FROM_DISTRICT_ID`, `GHN_FROM_WARD_CODE` và `GHN_API_URL` có kèm path `/shiip/public-api/v2`. Nhưng code hiện tại đọc `GHN_API_KEY`, `GHN_SHOP_DISTRICT_ID`, `GHN_SHOP_WARD_CODE` và tự nối path API trong `app/Services/GHNService.php`. Vì vậy nên dùng cấu hình ở trên để tránh lỗi gọi API sai đường dẫn hoặc thiếu token.

## 7. Khởi tạo database và dữ liệu mẫu

Chạy migration và seed dữ liệu mẫu:

```bash
php artisan migrate:fresh --seed
```

Cảnh báo: lệnh `migrate:fresh --seed` sẽ xóa toàn bộ bảng hiện có trong database rồi tạo lại từ đầu. Chỉ dùng cho lần setup đầu hoặc môi trường test.

Nếu database đã có dữ liệu thật và chỉ muốn chạy migration mới:

```bash
php artisan migrate
```

Tạo symbolic link cho file public:

```bash
php artisan storage:link
```

Xóa cache cấu hình sau khi sửa `.env`:

```bash
php artisan optimize:clear
```

## 8. Cách chạy khuyến nghị: Laravel dev server + XAMPP MySQL

Cách này đơn giản nhất khi phát triển local. XAMPP chỉ cần chạy `MySQL`; không bắt buộc chạy Apache.

Mở 3 terminal trong thư mục dự án.

Terminal 1, chạy Laravel:

```bash
php artisan serve
```

Mặc định website chạy tại:

```text
http://127.0.0.1:8000
```

Nếu cổng `8000` bị chiếm:

```bash
php artisan serve --port=8001
```

Terminal 2, chạy Vite:

```bash
npm run dev
```

Terminal 3, chạy queue worker:

```bash
php artisan queue:listen --tries=1
```

Nếu không test email/thông báo/job nền, có thể tạm bỏ qua terminal queue. Tuy nhiên dự án có cấu hình `QUEUE_CONNECTION=database`, nên một số việc xử lý nền sẽ không chạy nếu không có worker.

Có thể chạy gộp bằng script Composer sau khi đã `npm install`:

```bash
composer run dev
```

Script này sẽ chạy đồng thời:

- `php artisan serve`
- `php artisan queue:listen --tries=1`
- `php artisan pail --timeout=0`
- `npm run dev`

## 9. Cách chạy bằng Apache của XAMPP

Laravel phải trỏ web server vào thư mục `public`, không trỏ vào thư mục gốc của dự án.

### 9.1. Cách tốt nhất: tạo VirtualHost

Ví dụ dự án nằm tại:

```text
C:\xampp\htdocs\datn-BookBee
```

Mở file VirtualHost của Apache:

```text
C:\xampp\apache\conf\extra\httpd-vhosts.conf
```

Thêm cấu hình:

```apache
<VirtualHost *:80>
    ServerName bookbee.test
    DocumentRoot "C:/xampp/htdocs/datn-BookBee/public"

    <Directory "C:/xampp/htdocs/datn-BookBee/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

Mở file hosts bằng quyền Administrator:

```text
C:\Windows\System32\drivers\etc\hosts
```

Thêm dòng:

```text
127.0.0.1 bookbee.test
```

Trong Apache `httpd.conf`, đảm bảo module rewrite đã bật:

```apache
LoadModule rewrite_module modules/mod_rewrite.so
```

Nếu dòng này có dấu `#` ở đầu, bỏ dấu `#`, lưu file và restart Apache.

Sửa `.env`:

```dotenv
APP_URL=http://bookbee.test
```

Clear cache:

```bash
php artisan optimize:clear
```

Build asset frontend:

```bash
npm run build
```

Truy cập:

```text
http://bookbee.test
```

Nếu muốn hot reload khi phát triển giao diện, vẫn chạy thêm:

```bash
npm run dev
```

### 9.2. Cách tạm: truy cập qua thư mục `public`

Nếu không cấu hình VirtualHost, có thể truy cập:

```text
http://localhost/datn-BookBee/public
```

Khi dùng cách này, sửa `.env`:

```dotenv
APP_URL=http://localhost/datn-BookBee/public
```

Cách này chỉ nên dùng tạm vì URL không đẹp và dễ phát sinh lỗi đường dẫn asset/callback. Với Laravel, VirtualHost trỏ vào `public` vẫn là cách đúng hơn.

## 10. Tài khoản mẫu sau khi seed

Sau khi chạy:

```bash
php artisan migrate:fresh --seed
```

Có thể dùng các tài khoản sau.

Trang admin:

```text
http://127.0.0.1:8000/admin/login
```

Hoặc nếu dùng VirtualHost:

```text
http://bookbee.test/admin/login
```

Tài khoản admin:

```text
Email: admin1@example.com
Password: password
```

```text
Email: admin2@example.com
Password: password
```

Tài khoản staff:

```text
Email: staff1@example.com
Password: password
```

```text
Email: staff2@example.com
Password: password
```

```text
Email: staff3@example.com
Password: password
```

Trang đăng nhập người dùng:

```text
http://127.0.0.1:8000/login
```

Tài khoản user:

```text
Email: user1@example.com
Password: password
```

Các user khác có dạng:

```text
user2@example.com
user3@example.com
...
user10@example.com
```

Tất cả dùng password:

```text
password
```

Lưu ý: `ActiveUserSeeder` có tạo `admin@bookbee.com` và `user@bookbee.com`, nhưng ngay sau đó `RUserSeeder` truncate bảng `users`, nên sau khi chạy full `DatabaseSeeder`, tài khoản nên dùng là nhóm `admin1@example.com`, `user1@example.com`, `staff1@example.com`.

## 11. Các URL chính

Client:

```text
/
/books
/combos
/search
/contact
/login
/register
/cart
/wishlist
/news
```

Admin:

```text
/admin/login
/admin
/admin/books
/admin/orders
/admin/categories
/admin/users
/admin/staffs
/admin/vouchers
/admin/settings
```

API:

```text
/api/ghn/provinces
/api/chatbot/message
/api/login
```

## 12. Quy trình chạy nhanh từ đầu

Nếu muốn setup nhanh trên máy mới, chạy theo thứ tự:

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan storage:link
php artisan optimize:clear
```

Sửa `.env` về MySQL trước khi chạy migrate:

```dotenv
DB_CONNECTION=mysql
DB_DATABASE=bookbee
DB_USERNAME=root
DB_PASSWORD=
```

Chạy dự án:

```bash
php artisan serve
```

Terminal khác:

```bash
npm run dev
```

Terminal khác nếu cần queue:

```bash
php artisan queue:listen --tries=1
```

Truy cập:

```text
http://127.0.0.1:8000
```

## 13. Lỗi thường gặp và cách xử lý

### 13.1. `No application encryption key has been specified`

Chưa tạo `APP_KEY`.

Chạy:

```bash
php artisan key:generate
php artisan optimize:clear
```

### 13.2. `SQLSTATE[HY000] [1049] Unknown database`

Database trong `.env` chưa tồn tại.

Cách xử lý:

- Mở phpMyAdmin.
- Tạo database `bookbee`.
- Kiểm tra lại `DB_DATABASE=bookbee`.

### 13.3. `could not find driver`

PHP CLI chưa bật driver MySQL hoặc terminal đang dùng sai PHP.

Cách xử lý:

- Bật `extension=pdo_mysql` trong `php.ini`.
- Restart XAMPP.
- Kiểm tra `php -v` và `where php`.
- Đảm bảo terminal đang dùng PHP của XAMPP đúng version.

### 13.4. `Vite manifest not found`

Chưa chạy Vite hoặc chưa build asset.

Khi phát triển:

```bash
npm run dev
```

Khi chạy qua Apache không muốn mở Vite dev server:

```bash
npm run build
```

### 13.5. Trang trắng hoặc lỗi sau khi sửa `.env`

Clear cache:

```bash
php artisan optimize:clear
```

Nếu vẫn lỗi, xem log:

```bash
tail -f storage/logs/laravel.log
```

Trên Windows có thể mở trực tiếp file:

```text
storage\logs\laravel.log
```

### 13.6. Ảnh upload không hiển thị

Chưa tạo storage link.

Chạy:

```bash
php artisan storage:link
```

Nếu Windows không tạo được symlink, chạy terminal bằng quyền Administrator rồi chạy lại lệnh.

### 13.7. Lỗi quyền ghi `storage` hoặc `bootstrap/cache`

Trên macOS/Linux:

```bash
chmod -R 775 storage bootstrap/cache
```

Nếu vẫn lỗi quyền sở hữu:

```bash
sudo chown -R $USER:www-data storage bootstrap/cache
```

Tùy hệ điều hành, group web server có thể là `www-data`, `_www` hoặc user Apache/XAMPP tương ứng.

### 13.8. Không vào được route khi chạy Apache XAMPP

Nguyên nhân thường là Apache chưa bật rewrite hoặc VirtualHost chưa trỏ vào `public`.

Kiểm tra:

- `DocumentRoot` phải là thư mục `public`.
- `AllowOverride All` đã bật.
- `mod_rewrite` đã bật.
- Đã restart Apache.

### 13.9. Console báo thiếu `VITE_PUSHER_APP_KEY`

Frontend có file `resources/js/echo.js` kiểm tra biến Pusher. Nếu chưa cấu hình Pusher, console có thể báo lỗi nhưng web cơ bản vẫn chạy.

Nếu cần realtime, thêm:

```dotenv
VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
```

Sau đó restart:

```bash
npm run dev
```

### 13.10. Composer báo lỗi version PHP

Dự án yêu cầu PHP `^8.2`, nhưng theo lock hiện tại nên dùng PHP `8.2` đến `8.4`. Nếu Composer báo dependency không hỗ trợ PHP đang dùng, đổi PHP CLI về đúng version.

Kiểm tra:

```bash
php -v
composer check-platform-reqs --no-dev
```

### 13.11. Seed bị lỗi hoặc dữ liệu mẫu bị lặp

Với môi trường local/test, dùng:

```bash
php artisan migrate:fresh --seed
```

Lệnh này tạo lại toàn bộ database sạch từ đầu.

## 14. Lệnh kiểm tra sau khi setup

Kiểm tra app Laravel:

```bash
php artisan about
```

Kiểm tra route:

```bash
php artisan route:list
```

Kiểm tra package PHP:

```bash
composer validate --no-check-publish
```

Build frontend thử:

```bash
npm run build
```

Chạy test nếu cần:

```bash
php artisan test
```

## 15. Ghi chú dành cho nhóm phát triển

- Không commit file `.env`.
- Khi thêm biến môi trường mới, cập nhật cả `.env.example`.
- Nếu thay đổi `.env`, chạy `php artisan optimize:clear`.
- Nếu thay đổi package PHP, chạy `composer install` hoặc `composer update` theo đúng quy trình nhóm.
- Nếu thay đổi package frontend, chạy `npm install`.
- Khi deploy hoặc chạy qua Apache, luôn trỏ web root vào thư mục `public`.
- Khi dùng queue `database`, luôn có queue worker trong môi trường cần xử lý job.
- Với GHN, nên đồng bộ lại `.env.example` theo biến thực tế trong `config/services.php` để tránh người mới cấu hình nhầm.
