<?php

namespace App\Http\Controllers\Contact;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
  public function showForm()
  {
    return view('contact.contact');
  }

  public function submitForm(Request $request)
  {
    Log::info('=== CONTACT FORM SUBMISSION START ===');
    Log::info('Request method: ' . $request->method());
    Log::info('Request URL: ' . $request->url());
    Log::info('Request data: ', $request->all());
    
    try {
      // Validate dữ liệu form
      $data = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email',
        'phone' => 'required|string|max:20', // Thay đổi từ nullable thành required
        'address' => 'nullable|string|max:255',
        'message' => 'required|string', // Thay đổi từ 'note' thành 'message'
      ]);

      Log::info('Contact form data validated:', $data);

      // Xóa liên hệ cũ nếu email đã tồn tại
      $deleted = Contact::where('email', $data['email'])->delete();
      Log::info('Deleted old contacts:', ['count' => $deleted]);

      // Tạo liên hệ mới
      try {
        $contact = new Contact();
        $contact->name = $data['name'];
        $contact->email = $data['email'];
        $contact->phone = $data['phone'];
        $contact->address = $data['address'] ?? null;
        $contact->note = $data['message']; // Lưu message vào trường note
        $contact->status = 'new'; // Trạng thái mặc định
        $contact->save();

        Log::info('Contact created successfully:', ['id' => $contact->id, 'email' => $contact->email]);
      } catch (\Exception $e) {
        Log::error('Contact creation failed:', ['error' => $e->getMessage()]);
        return back()->withErrors(['error' => 'Có lỗi xảy ra khi lưu thông tin liên hệ.']);
      }

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

      Log::info('=== CONTACT FORM SUBMISSION SUCCESS ===');
      return back()->with('success', 'Gửi liên hệ thành công! Email xác nhận sẽ được gửi đến bạn trong giây lát.');
    } catch (\Exception $e) {
      Log::error('Contact form error:', [
        'error' => $e->getMessage(), 
        'trace' => $e->getTraceAsString(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
      ]);
      return back()->with('error', 'Có lỗi xảy ra khi gửi liên hệ. Vui lòng thử lại.');
    }
  }
}
