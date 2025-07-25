<!DOCTYPE html>
<html>
<head>
    <title>Thông báo thay đổi thông tin tài khoản</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            margin-bottom: 30px;
        }
        .content {
            margin-bottom: 30px;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Kính gửi {{ $user->name }},</h2>
        </div>

        <div class="content">
            <p>Chúng tôi xin thông báo rằng thông tin tài khoản của bạn trong hệ thống đã được cập nhật thành công.</p>

            @if($user->role->name !== $oldRole)
            <p><strong>Thông tin về vai trò:</strong></p>
            <p>Vai trò trước đây: {{ $oldRole }}</p>
            <p>Vai trò hiện tại: {{ $user->role->name }}</p>

            <p>Việc thay đổi này nhằm đảm bảo quyền truy cập và chức năng phù hợp hơn với nhu cầu sử dụng và sự phân quyền trong hệ thống của chúng tôi.</p>
            @endif

            @if($user->status !== $oldStatus)
            <p><strong>Thông tin về trạng thái tài khoản:</strong></p>
            <p>Trạng thái trước đây: {{ $oldStatus }}</p>
            <p>Trạng thái hiện tại: {{ $user->status }}</p>
            @endif

            <p>Nếu bạn có bất kỳ thắc mắc nào hoặc cần hỗ trợ thêm, xin vui lòng liên hệ với chúng tôi qua email: tvan19525@gmail.com hoặc số điện thoại hỗ trợ: 1900-xxxx.</p>
        </div>

        <div class="footer">
            <p>Trân trọng cảm ơn bạn đã đồng hành cùng chúng tôi!</p>
            <p>Thân mến,<br>
            Đội ngũ quản trị BookBee<br>
            Bộ phận Quản lý người dùng<br>
            BookBee store<br>
            Email: tvan19525@gmail.com</p>
        </div>
    </div>
</body>
</html>