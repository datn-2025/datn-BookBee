<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookFormat;
use App\Models\Preorder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PreorderController extends Controller
{
    /**
     * Store a newly created preorder in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'book_id' => 'required|exists:books,id',
                'customer_name' => 'required|string|max:255',
                'customer_email' => 'required|email|max:255',
                'customer_phone' => 'required|string|max:20',
                'customer_address' => 'required|string',
                'quantity' => 'required|integer|min:1|max:10',
                'book_format_id' => 'nullable|exists:book_formats,id',
                'attributes' => 'nullable|array',
                'attributes_display' => 'nullable|array',
                'unit_price' => 'required|numeric|min:0',
                'total_price' => 'required|numeric|min:0',
            ]);

            $book = Book::findOrFail($request->book_id);
            $bookFormat = $request->book_format_id ? BookFormat::find($request->book_format_id) : null;

            $preorder = Preorder::create([
                'book_id' => $request->book_id,
                'book_title' => $book->title,
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'customer_phone' => $request->customer_phone,
                'customer_address' => $request->customer_address,
                'quantity' => $request->quantity,
                'unit_price' => $request->unit_price,
                'shipping_cost' => 30000, // Default shipping cost
                'book_total' => $request->unit_price * $request->quantity,
                'total_price' => $request->total_price,
                'book_format_id' => $request->book_format_id,
                'book_format_name' => $bookFormat ? $bookFormat->name : null,
                'attributes' => $request->attributes,
                'attributes_display' => $request->attributes_display,
                'status' => 'pending',
            ]);

            Log::info('Preorder created successfully', [
                'preorder_id' => $preorder->id,
                'book_id' => $request->book_id,
                'customer_email' => $request->customer_email
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Đặt sách trước thành công! Chúng tôi sẽ liên hệ với bạn khi sách có sẵn.',
                'preorder_id' => $preorder->id
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Failed to create preorder', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi đặt sách trước. Vui lòng thử lại sau.'
            ], 500);
        }
    }

    /**
     * Display the specified preorder.
     */
    public function show(Preorder $preorder)
    {
        $preorder->load(['book', 'bookFormat']);
        return response()->json([
            'success' => true,
            'preorder' => $preorder
        ]);
    }
}
