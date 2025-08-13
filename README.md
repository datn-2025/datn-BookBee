<h2>Triển khai hệ thống</h2>

<p>Hệ thống được cài đặt và triển khai thử nghiệm trên môi trường máy tính cá nhân, sử dụng lệnh <code>php artisan serve</code> để khởi chạy ứng dụng Laravel.</p>

<h3>Quy trình triển khai</h3>

<h4>Bước 1: Cài đặt và cấu hình môi trường làm việc</h4>
<ul>
    <li><strong>Cơ sở dữ liệu:</strong> MySQL (sử dụng XAMPP hoặc Laragon để khởi tạo dịch vụ MySQL và Apache).</li>
    <li><strong>Môi trường phát triển Laravel:</strong> Đảm bảo máy tính đã cài đặt PHP phiên bản ≥ 8.2 và Composer.</li>
    <li><strong>Node.js & NPM:</strong> Dùng để quản lý các gói JavaScript và build giao diện (yêu cầu Node.js phiên bản ≥ 18).</li>
</ul>

<h4>Bước 2: Tải mã nguồn và tạo thư mục lưu trữ</h4>
<ol>
    <li>Truy cập kho lưu trữ dự án tại:<br>
        GitHub: <a href="https://github.com/datn-2025/datn-BookBee.git" target="_blank">https://github.com/datn-2025/datn-BookBee.git</a>
    </li>
    <li>Mở thư mục chứa mã nguồn của web server:
        <ul>
            <li>Nếu dùng XAMPP: thư mục <code>htdocs</code></li>
            <li>Nếu dùng Laragon: thư mục <code>www</code></li>
        </ul>
    </li>
    <li>Tạo một thư mục mới để chứa dự án.</li>
    <li>Mở Command Prompt (CMD) hoặc Terminal tại thư mục vừa tạo và chạy lệnh:</li>
</ol>

<pre><code>git clone https://github.com/datn-2025/datn-BookBee.git</code></pre>

<p>→ Hệ thống sẽ tải toàn bộ mã nguồn về thư mục dự án.</p>

<h4>Bước 3: Cài đặt và chạy mã nguồn</h4>
<ol>
    <li><strong>Cấu hình cơ sở dữ liệu:</strong>
        <ul>
            <li>Mở file <code>.env</code> trong thư mục dự án.</li>
            <li>Chỉnh sửa thông tin kết nối MySQL (tên database, username, password).</li>
        </ul>
    </li>
    <li><strong>Khởi tạo database:</strong></li>
</ol>

<pre><code>php artisan migrate</code></pre>
<p>→ Lệnh này sẽ tạo các bảng dữ liệu cần thiết trong MySQL.</p>

<ol start="3">
    <li><strong>Cài đặt các gói Node.js:</strong>
        <ul>
            <li>Kiểm tra phiên bản:</li>
        </ul>
        <pre><code>npm -v
node -v</code></pre>
        <ul>
            <li>Cài đặt dependencies:</li>
        </ul>
        <pre><code>npm install</code></pre>
    </li>
    <li><strong>Chạy hệ thống:</strong>
        <ul>
            <li>Khởi chạy backend Laravel:</li>
        </ul>
        <pre><code>php artisan serve</code></pre>
        <ul>
            <li>Khởi chạy frontend (Livewire, Tailwind, JS):</li>
        </ul>
        <pre><code>npm run dev</code></pre>
    </li>
</ol>

<p>→ Hai lệnh này cần chạy song song để hệ thống hoạt động đầy đủ.</p>

<h4>Truy cập website:</h4>
<ul>
    <li>Sao chép đường dẫn hiển thị trong terminal sau khi chạy <code>php artisan serve</code> (mặc định: <a href="http://127.0.0.1:8000" target="_blank">http://127.0.0.1:8000</a>).</li>
    <li>Dán vào thanh URL của trình duyệt để truy cập hệ thống.</li>
</ul>

<h4>Lưu ý khi triển khai:</h4>
<ul>
    <li>Nếu phát sinh lỗi trong quá trình <code>migrate</code> hoặc <code>npm install</code>, cần kiểm tra lại phiên bản PHP, Composer và Node.js.</li>
</ul>
