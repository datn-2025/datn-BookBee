@extends('layouts.app')
@section('title', 'BookBee')


@section('content')
    <section class="w-full bg-cover bg-center bg-no-repeat py-40"
        style="background-image: url('{{asset('storage/images/banner-image-bg.jpg')}}')">
        <div class="grid grid-cols-1 md:grid-cols-2 items-center px-6 md:px-10 gap-10 max-w-screen-xl mx-auto">
            {{-- Left text --}}
            <div class="space-y-4 text-black">
                <h2 class="text-5xl md:text-6xl font-bold leading-tight">
                    Sách đặc biệt<br>Bộ sưu tập sách
                </h2>
                <p class="text-xl md:text-2xl">
                    Ưu đãi lớn - Giảm giá đến 30%. Mua ngay hôm nay!
                </p><br>
                <a href="#"
                    class="bg-red-400 text-white px-8 py-5 rounded-full text-sm font-semibold hover:bg-black transition duration-300 w-max">
                    Xem ngay
                </a>
            </div>
            {{-- Right image --}}
            <div class="flex  justify-center">
                <img src="{{asset('storage/images/banner-image2.png')}}" class="h-full object-contain" alt="">
            </div>
        </div>




    </section>






    <section class="bg-white py-10">
        <div class=" max-w-screen-xl mx-auto grid grid-cols-1 sm:grid-cols-2 sm:grid-cols-4 gap-8 px-6 py-10 text-center">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Giao hàng miễn phí</h3>
                <p class=" text-gray-600 text-sm mt-1">Miễn phí vận chuyển cho mọi đơn hàng toàn quốc.</p>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Cam kết chất lượng</h3>
                <p class=" text-gray-600 text-sm mt-1">Sản phẩm chính hãng, đảm bảo chất lượng 100%.</p>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Ưu đãi mỗi ngày</h3>
                <p class=" text-gray-600 text-sm mt-1">Khuyến mãi hấp dẫn cập nhật liên tục mỗi ngày.</p>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Thanh toán an toàn</h3>
                <p class=" text-gray-600 text-sm mt-1">Hỗ trợ nhiều hình thức thanh toán bảo mật cao.</p>
            </div>
        </div>
    </section>








    <section class="px-4 py-6 max-w-screen-xl mx-auto">
        {{-- Tabs lựa chọn bộ sưu tập --}}
        <div class="flex items-center justify-between mb-6">
            <div class="flex gap-2 flex-wrap text-sm font-medium uppercase">
                @foreach ($categories as $index => $category)
                    <button
                        class="tab-button px-4 py-1 border border-black {{$index === 0 ? 'bg-black text-white' : 'hover:bg-gray-100'}} cursor-pointer"
                        data-tab="tab-{{$category->id}}">
                        {{$category->name}}
                    </button>


                @endforeach
            </div>
            <a href="#" class="text-sm font-semibold uppercase border-b border-black hover:opacity-70">Xem tất cả</a>
        </div>
        {{-- Nội dung từng tab --}}
        @foreach ($categories as $index => $category)
            <div id="tab-tab-{{$category->id}}" class="tab-content {{ $index === 0 ? 'block' : 'hidden'}} relative">
                <div class="swiper categorySwiper" id="swiper-{{ $category->id}}">
                    <div class="swiper-wrapper">
                        @foreach ($category->books as $book)
                            <div class="swiper-slide pb-6">
                                <div onclick="window.location='{{ route('books.show', ['slug' => $book->slug]) }}'"
                                    class="group bg-white border border-transparent hover:border-black rounded transition duration-300 overflow-hidden flex flex-col h-[510px]">
                                    <div class="relative aspect-[1/1.05] bg-gray-100 overflow-hidden">
                                        <img src="{{asset('storage/images/' . $book->image)}}" alt="{{$book->title}}">
                                        <div class="absolute top-2 right-2 z-10">
                                            <i class="far fa-heart text-2xl text-gray-700 hover:text-red-500 cursor-pointer"></i>
                                        </div>
                                    </div>
                                    <div class="p-4 flex flex-col justify-between flex-1">
                                        <p class="text-red-500 font-bold">
                                            Giá tiền {{ number_format($book->formats->first()->price ?? 0, 0, ',', '.') }}₫
                                        </p>
                                        <h3 class="text-sm font-semibold mt-1">{{$book->title}}</h3>
                                        <p class="text-xs text-gray-500 mt-1">
                                            {{$category->name ?? 'Chưa có danh mục'}}
                                        </p>
                                        <a href="#"
                                            class="mt-4 inline-block bg-black text-white px-4 py-2 rounded text-sm hover:bg-gray-800 text-center w-full">
                                            Thêm vào giỏ hàng →
                                        </a>
                                    </div>


                                </div>
                            </div>


                        @endforeach
                    </div>
                    <div class="swiper-scrollbar mt-4 h-[4px] bg-black rounded overflow-hidden"
                        id="scrollbar-{{$category->id}}"></div>
                </div>
                {{-- Nút điều hướng --}}
                <div class="swiper-prev absolute -left-3 top-1/2 -translate-y-1/2 z-10 cursor-pointer"
                    id="prev-{{$category->id}}">
                    <i class="fas fa-chevron-left text-xl text-black bg-white rounded-full shadow p-2 hover:bg-gray-200"></i>
                </div>
                <div class="swiper-next absolute -right-3 top-1/2 -translate-y-1/2 z-10 cursor-pointer"
                    id="next-{{$category->id}}">
                    <i class="fas fa-chevron-right text-xl text-black bg-white rounded-full shadow p-2 hover:bg-gray-200"></i>
                </div>
            </div>


        @endforeach
    </section>




    <section class="w-full bg-cover bg-center bg-no-repeat py-40"
        style="background-image: url('{{asset('storage/images/banner-image-bg-1.jpg')}}')">
        <div class="grid grid-cols-1 md:grid-cols-2 items-center px-6 md:px-10 gap-10 max-w-screen-xl mx-auto">
            <!-- Ảnh sách bên trái -->
            <div class="flex  justify-center">
                <img src="{{asset('storage/images/banner-image3.png')}}" class="h-full object-contain" alt="">
            </div>
            <!-- Nội dung bên phải -->
            <div class="text-center md:text-left space-y-4 text-black">
                <h2 class="text-5xl md:text-6xl font-semibold leading-tight">
                    Giờ hiện tại
                </h2>
                <p class="text-xl md:text-2xl">Một hành trình tri thức đang chờ bạn khám phá!</p>


                <!-- Hiển thị thời gian -->
                <div class="text-5xl font-bold" id="clock-time">00:00:00</div>
                <div class="text-lg mt-1" id="clock-date">Thứ..., 00/00/0000</div>


                <a href="#"
                    class="inline-block bg-red-400 text-white px-8 py-4 rounded-full text-sm font-semibold hover:bg-red-600 transition duration-300">
                    Mua ngay
                </a>
            </div>




        </div>




    </section>




    <section class="bg-white py-16">
        <div class="max-w-screen-xl mx-auto px-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">


            <!-- Featured -->
            <div>
                <h3 class="text-xl font-bold mb-4">Nổi bật</h3>
                <div class="flex flex-col gap-y-6">
                    @foreach($featuredBooks as $book)
                        <div onclick="window.location='{{ route('books.show', ['slug' => $book->slug]) }}'"
                            class="min-h-[300px] flex flex-col justify-between border border-gray-200 rounded p-3 hover:border-black transition">
                            <img src="{{ $book->images->first() ? asset('storage/' . $book->images->first()->image_url) : asset('storage/default.jpg') }}"
                                alt="{{ $book->title }}" class="w-20 h-28 object-cover mb-2">
                            <p class="font-semibold text-sm leading-tight">Tiêu đề: {{ $book->title }}</p>
                            <p class="text-xs text-gray-500">Tác giả: {{ $book->author->name ?? 'Không rõ' }}</p>
                            <p class="text-red-500 font-bold">
                                Giá tiền: {{ number_format($book->formats->first()->price ?? 0, 0, ',', '.') }}₫
                            </p>
                        </div>
                    @endforeach
                </div>


            </div>


            <!-- Mới nhất -->
            <div>
                <h3 class="text-xl font-bold mb-4">Mới nhất</h3>
                <div class="flex flex-col gap-y-6">
                    @foreach($latestBooks as $book)
                        <div onclick="window.location='{{ route('books.show', ['slug' => $book->slug]) }}'"
                            class="min-h-[300px] flex flex-col justify-between border border-gray-200 rounded p-3 hover:border-black transition">
                            <img src="{{ asset('storage/' . ($book->images->first()->image_url ?? 'default.jpg')) }}"
                                alt="{{ $book->title }}" class="w-20 h-28 object-cover mb-2">
                            <p class="font-semibold text-sm leading-tight">Tiêu đề: {{ $book->title }}</p>
                            <p class="text-xs text-gray-500">Tác giả: {{ $book->author->name ?? 'Không rõ' }}</p>
                            <p class="text-red-500 font-bold">
                                Giá tiền {{ number_format($book->formats->first()->price ?? 0, 0, ',', '.') }}₫
                            </p>
                        </div>
                    @endforeach
                </div>


            </div>


            <!-- Đánh giá cao -->
            <div>
                <h3 class="text-xl font-bold mb-4">Đánh giá cao</h3>
                <div class="flex flex-col gap-y-6">
                    @foreach($bestReviewedBooks as $book)
                        @php
                            $rating = round($book->reviews->avg('rating'), 1);


                        @endphp
                        <div onclick="window.location='{{ route('books.show', ['slug' => $book->slug]) }}'"
                            class="min-h-[300px] flex flex-col justify-between border border-gray-200 rounded p-3 hover:border-black transition">
                            <img src="{{ asset('storage/' . ($book->images->first()->image_url ?? 'default.jpg')) }}"
                                alt="{{ $book->title }}" class="w-20 h-28 object-cover mb-2">
                            <p class="font-semibold text-sm leading-tight">Tiêu đề: {{ $book->title }}</p>
                            <p class="text-xs text-gray-500">Tác giả: {{ $book->author->name ?? 'Không rõ' }}</p>
                            <p class="text-red-500 font-bold">
                                Giá tiền: {{ number_format($book->formats->first()->price ?? 0, 0, ',', '.') }}₫
                            </p>
                        </div>
                    @endforeach
                </div>


            </div>


            <!-- Giảm giá -->
            <div>
                <h3 class="text-xl font-bold mb-4">Giảm giá</h3>
                <div class="flex flex-col gap-y-6">
                    @foreach($saleBooks as $book)
                        @php
                            $fomat = $book->formats->first();
                            $oldPrice = $fomat->price ?? 0;
                            $discount = $fomat->discount ?? 0;
                            $newPrice = $oldPrice - ($oldPrice * ($discount / 100));
                        @endphp
                        <div onclick="window.location='{{ route('books.show', ['slug' => $book->slug]) }}'"
                            class="min-h-[300px] flex flex-col justify-between border border-gray-200 rounded p-3 hover:border-black transition">
                            <img src="{{ asset('storage/' . ($book->images->first()->image_url ?? 'default.jpg')) }}"
                                alt="{{ $book->title }}" class="w-20 h-28 object-cover mb-2">
                            <p class="font-semibold text-sm leading-tight">Tiêu đề: {{ $book->title }}</p>
                            <p class="text-xs text-gray-500">Tác giả: {{ $book->author->name ?? 'Không rõ' }}</p>
                            {{-- 💸 Giá có giảm --}}
                            <div class="text-sm mt-1">
                                <span class="line-through text-gray-700 mr-2">
                                    Giá tiền: {{ number_format($oldPrice, 0, ',', '.') }}₫
                                </span>
                                <span class="text-red-600 font-bold">
                                    Giá tiền: {{ number_format($newPrice, 0, ',', '.') }}₫
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>


            </div>


        </div>
    </section>










    <section class="px-4 py-10 max-w-screen-xl mx-auto">
        <h2 class="text-2xl md:text-3xl font-bold uppercase mb-6">🆕 Sản phẩm mới nhất</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6 items-stretch">
            @forelse($books as $book)
                <div onclick="window.location='{{ route('books.show', ['slug' => $book->slug]) }}'"
                    class="bg-white rounded shadow-sm overflow-hidden transition-all duration-200 hover:border hover:border-black group flex flex-col h-full">
                    <div class="relative aspect-[1/1.05] bg-gray-100 overflow-hidden">
                        <img src="{{asset('storage/images/' . $book->image)}}"
                            class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                            alt="{{$book->title}}">
                        <div class="absolute top-2 right-2 z-10">
                            <i class="far fa-heart text-2xl text-gray-700 hover:text-red-500 cursor-pointer"></i>
                        </div>
                    </div>
                    <div class="p-4 bg-white flex flex-col flex-1 justify-between h-[180px]">
                        <h3 class="text-base font-semibold text-gray-800">{{$book->title}}</h3>
                        <p class="text-sm text-gray-500">{{$book->author?->name ?? 'Không rõ'}}</p>
                        <p class="text-red-500 font-bold">
                            Giá tiền {{ number_format($book->formats->first()->price ?? 0, 0, ',', '.') }}₫
                        </p>
                        <a href="#"
                            class="mt-4 inline-block bg-black text-white px-4 py-2 rounded text-sm hover:bg-gray-800 text-center w-full">
                            Thêm vào giỏ hàng →
                        </a>
                    </div>


                </div>
            @empty
                <p class="col-span-4 text-center text-gray-500">Chưa có sản phẩm nào.</p>
            @endforelse
        </div>
    </section>






    <section class="py-20 bg-cover bg-center bg-no-repeat"
        style="background-image: url('{{ asset('storage/images/banner-image-bg.jpg') }}')">
        <h2 class="text-center text-3xl md:text-4xl font-bold mb-10 text-gray-800">Khách hàng nói gì?</h2>


        <div class="swiper reviewSwiper max-w-4xl mx-auto px-4">
            <div class="swiper-wrapper">
                @foreach ($reviews as $review)
                    <div
                        class="swiper-slide bg-white p-6 rounded shadow text-center flex flex-col justify-between min-h-[340px]">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">
                                Đánh giá cho sách: <span
                                    class="font-medium text-gray-700">{{ $review->book->title ?? 'Không xác định' }}</span>
                            </p>
                            <p class="text-xs text-gray-400 mb-2">
                                Ngày: {{ $review->created_at?->format('d/m/Y') ?? 'Không rõ' }}
                            </p>




                            <p class="text-gray-700 italic mb-4">“{{ $review->comment ?? 'Không có nội dung.' }}”</p>
                            <div class="mb-2 text-yellow-400">
                                @for ($i = 0; $i < $review->rating; $i++)
                                    ★
                                @endfor
                            </div>
                            <p class="text-black font-semibold">
                                {{ $review->user->name ?? 'Ẩn danh' }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
            <br><br>
            <div class="swiper-pagination mt-6"></div>
        </div>
    </section>




    <section class="px-4 py-16 max-w-screen-xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl md:text-3xl font-bold uppercase flex items-center gap-2">📰 Tin tức mới nhất</h2>
            <a href="#"
                class="bg-red-400 text-white px-6 py-2 rounded-full text-sm hover:bg-red-600 transition duration-300">
                Xem tất cả
            </a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
            @forelse($articles as $article)
                <div class="bg-white rounded shadow overflow-hidden hover:shadow-lg transition">
                    <img src="{{asset('storage/' . $article->thumbnail)}}" alt="{{$article->title}}"
                        class="w-full h-48 object-cover">
                    <div class="p-4 flex flex-col min-h-[270px]">
                        <div class="flex flex-col flex-grow">
                            <p class=" text-sm text-pink-500 font-medium mb-1">
                                {{ $article->category ?? ' Tin tức' }}
                            </p>
                            <h3 class=" text-lg font-bold mb-2 leading-snug">{{$article->title}}</h3>
                            <p class=" text-sm text-gray-600 mb-4">{{Str::limit($article->summary, 100)}}</p>


                        </div>
                        <div class="mt-4">
                            <a href="#" class=" inline-block text-sm text-red-500 hover:underline font-semibold">
                                Đọc thêm →
                            </a>
                        </div>


                    </div>
                </div>
            @empty
                <p class="col-span-4 text-center text-gray-500">Chưa có bài viết nào.</p>
            @endforelse
        </div>
    </section>








@endsection

