<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\ContactReplyMail;
use App\Models\Contact;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    // Danh sách liên hệ / khiếu nại (có phân trang, lọc trạng thái)
    public function index(Request $request)
    {
        $query = Contact::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        $contacts = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();
        return view('admin.contacts.index', compact('contacts'));
    }

    // Xem chi tiết liên hệ / khiếu nại
    public function show($id)
    {
        $contact = Contact::findOrFail($id);
        return view('admin.contacts.show', compact('contact'));
    }
    // Cập nhật trạng thái và ghi chú
    public function update(Request $request, $id)
    {
        $contact = Contact::findOrFail($id);

        $request->validate([
            'status' => 'required|in:new,processing,replied,closed',
            'note' => 'nullable|string',
        ]);

        $contact->status = $request->status;
        $contact->note = $request->note;
        $contact->save();
        Toastr::success('Cập nhật trạng thái thành công!');

        return redirect()->route('admin.contacts.index')->with('success', 'Cập nhật trạng thái thành công');
    }
    public function sendReply(Request $request, Contact $contact)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        // Gửi mail
        Mail::to($contact->email)->queue(new ContactReplyMail($contact, $request->message));

        // Cập nhật trạng thái là đã phản hồi
        $contact->status = 'replied';
        $contact->admin_reply = $request->message;
        $contact->save();

        Toastr::success('Đã gửi email phản hồi thành công!');
        return redirect()->route('admin.contacts.index')->with('success', 'Đã gửi email phản hồi thành công.');
    }

    // Xóa liên hệ
    public function destroy($id)
    {
        $contact = Contact::findOrFail($id);
        $contact->delete();
        Toastr::success('Đã xóa liên hệ thành công!');

        return redirect()->route('admin.contacts.index')->with('success', 'Đã xóa liên hệ thành công');
    }
}
