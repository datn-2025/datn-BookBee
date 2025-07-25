<?php

namespace App\Http\Controllers\Admin;

use App\Models\Collection;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;

class CollectionController extends Controller
{
    public function index(Request $request)
    {
        $query = Collection::with('books')->withCount('books');

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }
        if ($request->filled('book')) {
            $query->whereHas('books', function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->book . '%');
            });
        }
        if ($request->filled('price_from')) {
            $query->where('combo_price', '>=', $request->price_from);
        }
        if ($request->filled('price_to')) {
            $query->where('combo_price', '<=', $request->price_to);
        }
        if ($request->filled('date_range')) {
            $dates = preg_split('/\s*to\s*/', $request->date_range);
            if (count($dates) === 2) {
                // Chuyển d-m-Y về Y-m-d
                $start = \Carbon\Carbon::createFromFormat('d-m-Y', trim($dates[0]))->format('Y-m-d');
                $end = \Carbon\Carbon::createFromFormat('d-m-Y', trim($dates[1]))->format('Y-m-d');
                $query->where(function($q) use ($start, $end) {
                    $q->whereDate('start_date', '<=', $end)
                      ->whereDate('end_date', '>=', $start);
                });
            }
        }
        $collections = $query->paginate(10)->appends($request->all());
        return view('admin.collections.index', compact('collections'));
    }

    public function create()
    {
        $books = Book::orderBy('title')->get();
        return view('admin.collections.create', compact('books'));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'combo_price' => 'nullable|numeric|min:0',
            'books' => 'required|array|min:1',
            'books.*' => 'exists:books,id',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'description' => 'nullable|string',
        ]);
        $data = $request->only(['name', 'start_date', 'end_date', 'combo_price', 'description']);
        $data['slug'] = Str::slug($data['name']);
        $data['id'] = Str::uuid(); // Tạo UUID cho ID
        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('collections', 'public');
        }
        $collection = Collection::create($data);
        $collection->books()->sync(
            collect($request->books)->mapWithKeys(function ($bookId) use ($collection) {
                return [
                    $bookId => [
                        'id' => Str::uuid(),
                        'collection_id' => $collection->id,
                        'book_id' => $bookId,
                    ]
                ];
            })
        );
        Toastr::success('Tạo combo sách thành công!');
        return redirect()->route('admin.collections.index');
    }

    public function edit($id)
    {
        $collection = Collection::with('books')->findOrFail($id);
        $books = Book::orderBy('title')->get();
        $selectedBooks = $collection->books->pluck('id')->toArray();
        $startDate = $collection->start_date ? $collection->start_date->format('d-m-Y') : null;
        $endDate = $collection->end_date ? $collection->end_date->format('d-m-Y') : null;
        // dd($startDate, $endDate);
        return view('admin.collections.edit', compact('collection', 'books', 'selectedBooks', 'startDate', 'endDate'));
    }

    public function update(Request $request, $id)
    {
        $collection = Collection::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'combo_price' => 'nullable|numeric|min:0',
            'books' => 'required|array|min:1',
            'books.*' => 'exists:books,id',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'description' => 'nullable|string',
        ]);
        $data = $request->only(['name', 'start_date', 'end_date', 'combo_price', 'description']);
        $data['slug'] = Str::slug($data['name']);
        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('collections', 'public');
        }
        $collection->update($data);
        $collection->books()->sync(
            collect($request->books)->mapWithKeys(function ($bookId) use ($collection) {
                return [
                    $bookId => [
                        'id' => Str::uuid(),
                        'collection_id' => $collection->id,
                        'book_id' => $bookId,
                    ]
                ];
            })
        );
        Toastr::success('Cập nhật combo sách thành công!');
        return redirect()->route('admin.collections.index');
    }

    public function destroy($id)
    {
        $collection = Collection::findOrFail($id);
        $collection->books()->detach();
        $collection->delete(); // Xóa mềm
        Toastr::success('Đã xóa mềm combo sách!');
        return back();
    }

    public function forceDelete($id)
    {
        $collection = Collection::withTrashed()->findOrFail($id);
        $collection->books()->detach();
        $collection->forceDelete(); // Xóa cứng
        Toastr::success('Đã xóa vĩnh viễn combo sách!');
        return back();
    }

    public function show($id)
    {
        $collection = Collection::with('books')->findOrFail($id);
        return view('admin.collections.show', compact('collection'));
    }

    public function attachBooks(Request $request, $id)
    {
        $collection = Collection::findOrFail($id);
        $bookIds = $request->input('book_ids', []);
        $collection->books()->sync($bookIds);
        Toastr::success('Cập nhật sách cho tập thành công!');
        return back();
    }

    public function trash()
    {
        $collections = Collection::onlyTrashed()->with('books')->paginate(10);
        return view('admin.collections.trash', compact('collections'));
    }

    public function restore($id)
    {
        $collection = Collection::onlyTrashed()->findOrFail($id);
        $collection->restore();
        Toastr::success('Khôi phục combo thành công!');
        return back();
    }
}
