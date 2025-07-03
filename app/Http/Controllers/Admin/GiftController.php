<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\BookGift;

class GiftController extends Controller
{
    // public function index()
    // {
    //     $books = Book::with('gifts')->get();
    //     return view('admin.gifts.index', compact('books'));
    // }

    // public function create()
    // {
    //     $books = \App\Models\Book::all();
    //     return view('admin.gifts.create', compact('books'));
    // }

    // public function store(Request $request)
    // {
    //     $data = $request->validate([
    //         'book_id' => 'required|exists:books,id',
    //         'gift_name' => 'required|string|max:255',
    //         'gift_description' => 'nullable|string',
    //         'quantity' => 'required|integer|min=0',
    //         'gift_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
    //     ]);
    //     if ($request->hasFile('gift_image')) {
    //         $data['gift_image'] = $request->file('gift_image')->store('gifts', 'public');
    //     }
    //     \App\Models\BookGift::create($data);
    //     return redirect()->route('admin.gifts.index')->with('success', 'Thêm quà tặng thành công!');
    // }

    // public function edit($id)
    // {
    //     $gift = BookGift::findOrFail($id);
    //     return view('admin.gifts.edit', compact('gift'));
    // }

    // public function update(Request $request, $id)
    // {
    //     $gift = BookGift::findOrFail($id);
    //     $data = $request->validate([
    //         'gift_name' => 'required|string|max:255',
    //         'gift_description' => 'nullable|string',
    //         'quantity' => 'required|integer|min=0',
    //         'gift_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
    //     ]);
    //     if ($request->hasFile('gift_image')) {
    //         $data['gift_image'] = $request->file('gift_image')->store('gifts', 'public');
    //     }
    //     $gift->update($data);
    //     return redirect()->route('admin.gifts.index')->with('success', 'Cập nhật quà tặng thành công!');
    // }

    // public function destroy($id)
    // {
    //     $gift = BookGift::findOrFail($id);
    //     $gift->delete();
    //     return redirect()->route('admin.gifts.index')->with('success', 'Xóa quà tặng thành công!');
    // }
}
