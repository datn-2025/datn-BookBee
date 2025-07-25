@extends('layouts.backend')

@section('title', 'Thêm Voucher Mới')
@section('breadcrumb-parent', 'Voucher')
@section('breadcrumb-child', 'Thêm mới')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Thêm Voucher Mới</h3>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <h5>Có lỗi xảy ra:</h5>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form action="{{ route('admin.vouchers.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="code">Mã Voucher <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('code') is-invalid @enderror"
                                           id="code" name="code" value="{{ old('code') }}" >
                                    @error('code')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="description">Mô tả</label>
                                    <input type="text" class="form-control @error('description') is-invalid @enderror"
                                           id="description" name="description" value="{{ old('description') }}">
                                    @error('description')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="discount_percent">Phần trăm giảm (%) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('discount_percent') is-invalid @enderror"
                                           id="discount_percent" name="discount_percent" value="{{ old('discount_percent') }}"
                                           min="0" max="100" step="0.01" >
                                    @error('discount_percent')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="max_discount">Giảm tối đa (VNĐ) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('max_discount') is-invalid @enderror"
                                           id="max_discount" name="max_discount" value="{{ old('max_discount') }}" min="0" step="0.01" >
                                    @error('max_discount')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="min_order_value">Đơn tối thiểu (VNĐ) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('min_order_value') is-invalid @enderror"
                                           id="min_order_value" name="min_order_value" value="{{ old('min_order_value') }}" min="0" step="0.01" >
                                    @error('min_order_value')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="quantity">Số lượng <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('quantity') is-invalid @enderror"
                                           id="quantity" name="quantity" value="{{ old('quantity') }}" min="1" >
                                    @error('quantity')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="valid_from">Ngày bắt đầu <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('valid_from') is-invalid @enderror"
                                           id="valid_from" name="valid_from" value="{{ old('valid_from') }}" >
                                    @error('valid_from')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="valid_to">Ngày kết thúc <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('valid_to') is-invalid @enderror"
                                           id="valid_to" name="valid_to" value="{{ old('valid_to') }}" >
                                    @error('valid_to')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">Trạng thái <span class="text-danger">*</span></label>
                                    <select class="form-control @error('status') is-invalid @enderror"
                                            id="status" name="status"  style="width: auto; min-width: 150px;">
                                        <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Hoạt động</option>
                                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Không hoạt động</option>
                                    </select>
                                    @error('status')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="condition_type">Loại điều kiện áp dụng <span class="text-danger">*</span></label>
                                    <select class="form-control @error('condition_type') is-invalid @enderror"
                                            id="condition_type" name="condition_type" >
                                        <option value="">-- Chọn --</option>
                                        <option value="all" {{ old('condition_type') == 'all' ? 'selected' : '' }}>Tất cả sản phẩm</option>
                                        <option value="category" {{ old('condition_type') == 'category' ? 'selected' : '' }}>Theo danh mục</option>
                                        <option value="author" {{ old('condition_type') == 'author' ? 'selected' : '' }}>Theo tác giả</option>
                                        <option value="brand" {{ old('condition_type') == 'brand' ? 'selected' : '' }}>Theo thương hiệu</option>
                                        <option value="book" {{ old('condition_type') == 'book' ? 'selected' : '' }}>Theo sách</option>
                                    </select>
                                    @error('condition_type')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Danh sách đối tượng -->
                            <div class="col-md-12" id="condition_objects_container" style="display: none;">
                                <div class="form-group">
                                    <label id="condition_objects_label">Chọn đối tượng</label>
                                    <div class="input-group mb-2">
                                        <input type="text" class="form-control" id="search_object" placeholder="Tìm kiếm...">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button" id="select_all">Chọn tất cả</button>
                                            <button class="btn btn-outline-secondary" type="button" id="deselect_all">Bỏ chọn tất cả</button>
                                        </div>
                                    </div>
                                    <div class="border rounded p-2" style="max-height: 300px; overflow-y: auto;">
                                        <div id="condition_objects_list" class="row">
                                            <!-- Danh sách checkbox sẽ được thêm vào đây -->
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            Đã chọn: <span id="selected_count">0</span> đối tượng
                                        </small>
                                    </div>
                                    <small class="form-text text-muted">Có thể tìm kiếm và chọn nhiều đối tượng</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-3">
                            <button type="submit" class="btn btn-primary">Lưu</button>
                            <a href="{{ route('admin.vouchers.index') }}" class="btn btn-secondary">Hủy</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Helper function to get label
        function getLabelForType(type) {
            switch(type) {
                case 'category': return 'danh mục';
                case 'author': return 'tác giả';
                case 'brand': return 'thương hiệu';
                case 'book': return 'sách';
                default: return 'đối tượng';
            }
        }

        // Cập nhật số lượng đối tượng đã chọn
        function updateSelectedCount() {
            const count = $('#condition_objects_list input[type="checkbox"]:checked').length;
            $('#selected_count').text(count);
        }

        // Filter list based on search input
        function filterList() {
            try {
                const searchText = $('#search_object').val().toLowerCase();
                const listItems = $('#condition_objects_list .custom-control');

                if (listItems.length === 0) {
                    return; // Không có items để filter
                }

                listItems.each(function() {
                    const label = $(this).find('label');
                    if (label.length === 0) {
                        return; // Bỏ qua nếu không tìm thấy label
                    }
                    const text = label.text().toLowerCase();
                    $(this).toggle(text.includes(searchText));
                });

                updateSelectedCount();
            } catch (error) {
                console.error('Lỗi khi filter:', error);
            }
        }

        // Xử lý khi thay đổi loại điều kiện
        $('#condition_type').on('change', function() {
            console.log('1. Condition type changed');
            const type = $(this).val();
            console.log('2. Selected type:', type);

            const container = $('#condition_objects_container');
            const list = $('#condition_objects_list');
            const label = $('#condition_objects_label');

            // Xóa tất cả options cũ
            list.empty();
            $('#search_object').val('');
            console.log('3. Cleared old options');

            if (type === 'all' || type === '') {
                console.log('4. Hiding container for type:', type);
                container.hide();
                return;
            }

            // Hiển thị container
            container.show();
            label.text(`Đang tải ${getLabelForType(type)}...`);
            console.log('5. Container shown, loading label set');

            // Tạo URL
            const url = '{{ route("admin.vouchers.getConditionOptions") }}';
            console.log('6. Making AJAX request to:', url);

            // Gửi request
            $.ajax({
                url: url,
                method: 'GET',
                data: { condition_type: type },
                dataType: 'json',
                beforeSend: function() {
                    console.log('7. Sending AJAX request...');
                },
                success: function(response) {
                    console.log('8. Received response:', response);

                    if (!response.options || response.options.length === 0) {
                        console.log('9. No options found');
                        list.html('<div class="col-12"><p>Không tìm thấy đối tượng nào.</p></div>');
                        return;
                    }

                    console.log('10. Building options HTML');
                    let optionsHtml = '';
                    response.options.forEach(function(option) {
                        const isSelected = response.selected_ids && response.selected_ids.includes(option.id);
                        const statusClass = option.status === 'active' ? 'success' : 'secondary';
                        const statusText = option.status === 'active' ? 'Hoạt động' : 'Không hoạt động';

                        optionsHtml += `
                            <div class="col-md-4 mb-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input"
                                           id="obj_${option.id}"
                                           name="condition_objects[]"
                                           value="${option.id}"
                                           ${isSelected ? 'checked' : ''}>
                                    <label class="custom-control-label" for="obj_${option.id}">
                                        ${option.name}
                                        <span class="badge badge-${statusClass} ml-2">${statusText}</span>
                                    </label>
                                </div>
                            </div>
                        `;
                    });

                    console.log('11. Setting HTML content');
                    list.html(optionsHtml);
                    label.text(`Chọn ${getLabelForType(type)}`);
                    console.log('12. Process completed successfully');
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', {
                        status: status,
                        error: error,
                        response: xhr.responseText,
                        xhr: xhr
                    });
                    list.html('<div class="col-12"><p class="text-danger">Lỗi khi tải danh sách. Vui lòng thử lại.</p></div>');
                    label.text(`Lỗi khi tải ${getLabelForType(type)}`);
                }
            });
        });

        // Tìm kiếm
        $('#search_object').on('input', function() {
            console.log('Search input changed');
            filterList();
        });

        // Chọn tất cả
        $('#select_all').on('click', function() {
            $('#condition_objects_list input[type="checkbox"]:visible').prop('checked', true);
            updateSelectedCount();
        });

        // Bỏ chọn tất cả
        $('#deselect_all').on('click', function() {
            $('#condition_objects_list input[type="checkbox"]:visible').prop('checked', false);
            updateSelectedCount();
        });

        // Thêm sự kiện cho checkbox
        $(document).on('change', '#condition_objects_list input[type="checkbox"]', function() {
            updateSelectedCount();
        });

        // Thêm validation trước khi submit form
        $('form').on('submit', function(e) {
            const conditionType = $('#condition_type').val();
            if (conditionType !== 'all' && conditionType !== '') {
                const selectedOptions = $('input[name="condition_objects[]"]:checked').length;
                if (selectedOptions === 0) {
                    e.preventDefault();
                    alert('Vui lòng chọn ít nhất một đối tượng áp dụng.');
                    return false;
                }
            }
        });

        // Kích hoạt sự kiện change khi tải trang nếu có giá trị mặc định
        if($('#condition_type').val() !== '') {
            alert('Triggering initial change event'); // Test initial trigger
            $('#condition_type').trigger('change');
        }
    });
</script>
@endsection
