<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\VoucherRequest;
use App\Models\Author;
use App\Models\Book;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VoucherController extends Controller
{
    public function index(Request $request)
    {
        $query = Voucher::with([
            'conditions',
            'conditions.categoryCondition',
            'conditions.authorCondition',
            'conditions.brandCondition',
            'conditions.bookCondition'
        ])->withCount('appliedVouchers');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        // Filter by discount range
        if ($request->filled('min_discount') && $request->filled('max_discount')) {
            $query->whereBetween('discount_percent', [$request->min_discount, $request->max_discount]);
        }

        // Filter by validity period
        if ($request->filled('date_from')) {
            $query->where('valid_from', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('valid_to', '<=', $request->date_to);
        }

        // Filter by status
        if ($request->has('status') && in_array($request->status, ['active', 'inactive'])) {
            $query->where('status', $request->status);
        }

        $vouchers = $query->latest()->paginate(10);

        // Thống kê số lượng voucher theo trạng thái đơn giản (chỉ active/inactive)
        $totalVouchers = Voucher::count();
        $activeVouchers = Voucher::where('status', 'active')->count();
        $inactiveVouchers = Voucher::where('status', 'inactive')->count();
        $usedVouchersCount = \App\Models\AppliedVoucher::count();

        return view('admin.vouchers.index', compact(
            'vouchers',
            'totalVouchers',
            'activeVouchers',
            'inactiveVouchers',
            'usedVouchersCount'
        ));
    }

    public function create()
    {
        $categories = Category::all();
        $authors = Author::all();
        $brands = Brand::all();
        $books = Book::all();

        return view('admin.vouchers.create', compact('categories', 'authors', 'brands', 'books'));
    }

    public function store(VoucherRequest $request)
    {
        try {
            DB::beginTransaction();

            // Clean up numeric inputs
            $validated = $request->validated();
            $validated['max_discount'] = (float) str_replace(',', '', $validated['max_discount']);
            $validated['min_order_value'] = (float) str_replace(',', '', $validated['min_order_value']);

            // Create voucher
            $voucher = Voucher::create([
                'code' => strtoupper($validated['code']),
                'description' => $validated['description'] ?? null,
                'discount_percent' => $validated['discount_percent'],
                'max_discount' => $validated['max_discount'],
                'min_order_value' => $validated['min_order_value'],
                'quantity' => $validated['quantity'],
                'valid_from' => $validated['valid_from'],
                'valid_to' => $validated['valid_to'],
                'status' => $validated['status']
            ]);

            // Create voucher conditions
            if ($validated['condition_type'] === 'all') {
                $voucher->conditions()->create(['type' => 'all']);
            } else {
                $conditionObjects = $request->input('condition_objects', []);
                if (!empty($conditionObjects)) {
                    foreach ($conditionObjects as $conditionObjectId) {
                        $voucher->conditions()->create([
                            'type' => $validated['condition_type'],
                            'condition_id' => $conditionObjectId
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()
                ->route('admin.vouchers.index')
                ->with('success', 'Thêm voucher thành công');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Voucher creation error: ' . $e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra khi tạo voucher')
                ->withInput();
        }
    }

    public function show(Voucher $voucher)
    {
        $voucher->load([
            'conditions.categoryCondition',
            'conditions.authorCondition',
            'conditions.brandCondition',
            'conditions.bookCondition'
        ]);

        $recentUsage = $voucher->appliedVouchers()
            ->with(['order.user'])
            ->latest()
            ->paginate(10);

        return view('admin.vouchers.show', compact('voucher', 'recentUsage'));
    }

    public function edit(Voucher $voucher)
    {
        $voucher->load([
            'conditions.categoryCondition',
            'conditions.authorCondition',
            'conditions.brandCondition',
            'conditions.bookCondition'
        ]);

        $categories = Category::select('id', 'name')
            ->orderBy('name', 'asc')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'status' => 'active'
                ];
            });

        $authors = Author::select('id', 'name')
            ->orderBy('name', 'asc')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'status' => 'active'
                ];
            });

        $brands = Brand::select('id', 'name')
            ->orderBy('name', 'asc')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'status' => 'active'
                ];
            });

        $books = Book::select('id', 'title')
            ->orderBy('title', 'asc')
            ->with(['authors:id,name', 'brand:id,name'])
            ->get()
            ->map(function ($book) {
                $authorInfo = $book->authors->isNotEmpty() ? ' - ' . $book->authors->first()->name : '';
                $brandInfo = $book->brand ? ' (' . $book->brand->name . ')' : '';
                return [
                    'id' => $book->id,
                    'name' => $book->title . $authorInfo . $brandInfo,
                    'status' => 'active'
                ];
            });

        return view('admin.vouchers.edit', compact('voucher', 'categories', 'authors', 'brands', 'books'));
    }

    public function update(VoucherRequest $request, Voucher $voucher)
    {
        try {
            DB::beginTransaction();

            // Clean up numeric inputs
            $validated = $request->validated();
            $validated['max_discount'] = (float) str_replace(',', '', $validated['max_discount']);
            $validated['min_order_value'] = (float) str_replace(',', '', $validated['min_order_value']);

            // Update voucher (bỏ qua trường code)
            $voucher->update([
                'description' => $validated['description'] ?? null,
                'discount_percent' => $validated['discount_percent'],
                'max_discount' => $validated['max_discount'],
                'min_order_value' => $validated['min_order_value'],
                'quantity' => $validated['quantity'],
                'valid_from' => $validated['valid_from'],
                'valid_to' => $validated['valid_to'],
                'status' => $validated['status']
            ]);

            // Delete existing conditions
            $voucher->conditions()->delete();

            // Create new conditions
            if ($validated['condition_type'] === 'all') {
                $voucher->conditions()->create(['type' => 'all']);
            } else {
                $conditionObjects = $request->input('condition_objects', []);
                if (!empty($conditionObjects)) {
                    foreach ($conditionObjects as $conditionObjectId) {
                        $voucher->conditions()->create([
                            'type' => $validated['condition_type'],
                            'condition_id' => $conditionObjectId
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()
                ->route('admin.vouchers.index')
                ->with('success', 'Cập nhật voucher thành công');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Voucher update error: ' . $e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra khi cập nhật voucher');
        }
    }

    public function destroy(Voucher $voucher)
    {
        try {
            if ($voucher->appliedVouchers()->exists()) {
                return back()->with('error', 'Không thể xóa voucher đã được sử dụng');
            }

            $voucher->delete();
            return redirect()
                ->route('admin.vouchers.index')
                ->with('success', 'Xóa voucher thành công');
        } catch (\Exception $e) {
            Log::error('Voucher deletion error: ' . $e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra khi xóa voucher');
        }
    }

    public function trash()
    {
        $vouchers = Voucher::onlyTrashed()
            ->withCount('appliedVouchers')
            ->latest()
            ->paginate(10);
        return view('admin.vouchers.trash', compact('vouchers'));
    }

    public function restore($id)
    {
        try {
            $voucher = Voucher::onlyTrashed()->findOrFail($id);
            $voucher->restore();
            return redirect()
                ->route('admin.vouchers.trash')
                ->with('success', 'Khôi phục voucher thành công');
        } catch (\Exception $e) {
            Log::error('Voucher restoration error: ' . $e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra khi khôi phục voucher');
        }
    }

    public function forceDelete($id)
    {
        try {
            Log::info('Force delete voucher called with ID: ' . $id);

            $voucher = Voucher::onlyTrashed()->findOrFail($id);
            Log::info('Found voucher: ' . $voucher->code);
            Log::info('Applied vouchers count: ' . $voucher->appliedVouchers()->count());

            Log::info('Proceeding with force delete');

            // Xóa applied vouchers trước (nếu có)
            $appliedCount = $voucher->appliedVouchers()->count();
            if ($appliedCount > 0) {
                $voucher->appliedVouchers()->delete();
                Log::info('Deleted ' . $appliedCount . ' applied vouchers');
            }

            // Kiểm tra xem có conditions không
            $conditionsCount = $voucher->conditions()->count();
            Log::info('Conditions count: ' . $conditionsCount);

            // Xóa conditions trước
            if ($conditionsCount > 0) {
                $voucher->conditions()->delete();
                Log::info('Deleted conditions');
            }

            // Thử force delete
            Log::info('About to force delete voucher');
            $result = $voucher->forceDelete();
            Log::info('Force delete result: ' . ($result ? 'true' : 'false'));
            Log::info('Force delete completed successfully');

            return redirect()
                ->route('admin.vouchers.trash')
                ->with('success', 'Xóa vĩnh viễn voucher thành công');
        } catch (\Exception $e) {
            Log::error('Voucher force deletion error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return back()->with('error', 'Có lỗi xảy ra khi xóa vĩnh viễn voucher: ' . $e->getMessage());
        }
    }

    // Method test để debug
    public function testForceDelete($id)
    {
        try {
            $voucher = Voucher::onlyTrashed()->findOrFail($id);
            $voucher->forceDelete();
            return response()->json(['success' => true, 'message' => 'Force delete successful']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function getConditionOptions(Request $request)
    {
        try {
            $type = $request->input('condition_type');
            $voucherId = $request->input('voucher_id');

            if (!$type) {
                return response()->json(['error' => 'Loại điều kiện không được để trống'], 400);
            }

            $options = collect();
            $selectedIds = [];

            // Lấy danh sách đã chọn nếu đang edit voucher
            if ($voucherId) {
                try {
                    $voucher = Voucher::findOrFail($voucherId);
                    $selectedIds = $voucher->conditions()
                        ->where('type', $type)
                        ->pluck('condition_id')
                        ->toArray();
                } catch (\Exception $e) {
                    // Nếu không tìm thấy voucher, coi như đang tạo mới
                    $selectedIds = [];
                }
            }

            switch ($type) {
                case 'category':
                    $options = Category::select('id', 'name')
                        ->orderBy('name')
                        ->get()
                        ->map(function ($item) {
                            return [
                                'id' => $item->id,
                                'name' => $item->name,
                                'status' => 'active'
                            ];
                        });
                    break;

                case 'author':
                    $options = Author::select('id', 'name')
                        ->orderBy('name')
                        ->get()
                        ->map(function ($item) {
                            return [
                                'id' => $item->id,
                                'name' => $item->name,
                                'status' => 'active'
                            ];
                        });
                    break;

                case 'brand':
                    $options = Brand::select('id', 'name')
                        ->orderBy('name')
                        ->get()
                        ->map(function ($item) {
                            return [
                                'id' => $item->id,
                                'name' => $item->name,
                                'status' => 'active'
                            ];
                        });
                    break;

                case 'book':
                    try {
                        $options = Book::select('id', 'title')
                            ->with(['authors', 'brand'])
                            ->orderBy('title')
                            ->get()
                            ->map(function ($item) {
                                $authorInfo = $item->authors->first() ? " - " . $item->authors->first()->name : "";
                                $brandInfo = $item->brand ? " (" . $item->brand->name . ")" : "";
                                return [
                                    'id' => $item->id,
                                    'name' => $item->title . $authorInfo . $brandInfo,
                                    'status' => 'active'
                                ];
                            });
                    } catch (\Exception $e) {
                        return response()->json(['error' => 'Lỗi khi lấy danh sách sách: ' . $e->getMessage()], 500);
                    }
                    break;

                default:
                    return response()->json(['error' => 'Loại điều kiện không hợp lệ'], 400);
            }

            if ($options->isEmpty()) {
                return response()->json(['error' => 'Không tìm thấy đối tượng nào'], 404);
            }

            return response()->json([
                'options' => $options,
                'selected_ids' => $selectedIds
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Lỗi: ' . $e->getMessage()], 500);
        }
    }

    public function search(Request $request)
    {
        $term = $request->get('term');

        // Tìm kiếm trong sách
        $books = Book::where('title', 'like', "%{$term}%")
            ->select('id', 'title as name')
            ->get()
            ->map(function ($item) {
                $item->type = 'Sách';
                return $item;
            });

        // Tìm kiếm trong tác giả
        $authors = Author::where('name', 'like', "%{$term}%")
            ->select('id', 'name')
            ->get()
            ->map(function ($item) {
                $item->type = 'Tác giả';
                return $item;
            });

        // Tìm kiếm trong NXB
        $brands = Brand::where('name', 'like', "%{$term}%")
            ->select('id', 'name')
            ->get()
            ->map(function ($item) {
                $item->type = 'NXB';
                return $item;
            });

        // Tìm kiếm trong danh mục
        $categories = Category::where('name', 'like', "%{$term}%")
            ->select('id', 'name')
            ->get()
            ->map(function ($item) {
                $item->type = 'Danh mục';
                return $item;
            });

        // Gộp kết quả
        $results = $books->concat($authors)->concat($brands)->concat($categories);

        return response()->json($results);
    }
}