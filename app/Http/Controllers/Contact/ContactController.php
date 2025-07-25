<?php

namespace App\Http\Controllers\Contact;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;

class ContactController extends Controller
{
  public function showForm()
  {
    return view('contact.contact');
  }

  public function submitForm(Request $request)
  {
    // Validate dữ liệu form
    $data = $request->validate([
      'name' => 'required|string|max:255',
      'email' => 'required|email',
      'phone' => 'required|string|max:20',
      'address' => 'nullable|string|max:255',
      'message' => 'required|string', // Thay đổi từ 'note' thành 'message'
    ]);

    // Xóa liên hệ cũ nếu email đã tồn tại
    Contact::where('email', $data['email'])->delete();

    // Tạo liên hệ mới
    Contact::create([
      'name' => $data['name'],
      'email' => $data['email'],
      'phone' => $data['phone'],
      'address' => $data['address'] ?? null,
      'note' => $data['message'], // Lưu message vào trường note
      'status' => 'new', // Trạng thái mặc định
    ]);

    // Gửi mail trong queue
    $fromAddress = config('mail.from.address');
    $fromName = config('mail.from.name');

    Queue::push(function () use ($data, $fromAddress, $fromName) {
      Mail::raw('Cảm ơn bạn đã góp ý cho chúng tôi!', function ($mail) use ($data, $fromAddress, $fromName) {
        $mail->from($fromAddress, $fromName)
          ->to($data['email'])
          ->subject('Cảm ơn bạn đã liên hệ BookBee!');
      });
    });

    return back()->with('success', 'Gửi liên hệ thành công! Email xác nhận sẽ được gửi đến bạn trong giây lát.');
  }
}
