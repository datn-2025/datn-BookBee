@extends('layouts.backend')

@section('title', 'Tạo popup')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
      <h4 class="mb-sm-0 text-dark fw-semibold">Tạo Popup</h4>
      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item"><a href="{{ route('admin.popups.index') }}">Popups</a></li>
          <li class="breadcrumb-item active">Tạo mới</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-body">
        <form action="{{ route('admin.popups.store') }}" method="post">
          @csrf
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Tiêu đề</label>
              <input type="text" name="title" class="form-control" value="{{ old('title') }}">
            </div>
            <div class="col-md-3">
              <label class="form-label">Độ trễ (ms)</label>
              <input type="number" min="0" max="600000" name="delay_ms" class="form-control" value="{{ old('delay_ms', 1500) }}" required>
            </div>
            <div class="col-md-3">
              <label class="form-label">Tần suất</label>
              <select name="frequency" class="form-select" required>
                <option value="always" {{ old('frequency')=='always'?'selected':'' }}>Luôn hiển thị</option>
                <option value="once_per_session" {{ old('frequency')=='once_per_session'?'selected':'' }}>1 lần/phiên</option>
                <option value="once_per_day" {{ old('frequency')=='once_per_day'?'selected':'' }}>1 lần/ngày</option>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label">Thời gian ẩn khi đóng (giờ)</label>
              <input type="number" min="0" max="720" name="dismiss_hours" class="form-control" value="{{ old('dismiss_hours', 24) }}" required>
            </div>
            <div class="col-md-3">
              <label class="form-label">Ưu tiên</label>
              <input type="number" min="0" max="1000" name="priority" class="form-control" value="{{ old('priority', 10) }}" required>
            </div>
            <div class="col-md-3 d-flex align-items-end">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="active" name="active" {{ old('active', true) ? 'checked' : '' }}>
                <label class="form-check-label" for="active">Kích hoạt</label>
              </div>
            </div>
            <div class="col-md-3">
              <label class="form-label">Bắt đầu</label>
              <input type="datetime-local" name="start_at" class="form-control" value="{{ old('start_at') }}">
            </div>
            <div class="col-md-3">
              <label class="form-label">Kết thúc</label>
              <input type="datetime-local" name="end_at" class="form-control" value="{{ old('end_at') }}">
            </div>
            <div class="col-md-6">
              <label class="form-label">Ảnh (URL)</label>
              <input type="text" name="image_url" class="form-control" placeholder="/storage/banners/promo.jpg" value="{{ old('image_url') }}">
            </div>
            <div class="col-md-6">
              <label class="form-label">Liên kết (khi click)</label>
              <input type="text" name="link_url" class="form-control" placeholder="https://example.com" value="{{ old('link_url') }}">
            </div>
            <div class="col-12">
              <label class="form-label">Nội dung HTML</label>
              <textarea name="content_html" rows="6" class="form-control" placeholder="<h3>Khuyến mãi...</h3>">{{ old('content_html') }}</textarea>
              <small class="text-muted">Nếu có ảnh, nội dung HTML là tuỳ chọn.</small>
            </div>
            <div class="col-12">
              <label class="form-label">Trang áp dụng</label>
              <textarea name="pages" rows="3" class="form-control" placeholder="route:name hoặc pattern đường dẫn, mỗi dòng 1 mục
vd: home
vd: books/*
vd: /khuyen-mai/*">{{ old('pages') }}</textarea>
              <small class="text-muted">Để trống để áp dụng cho tất cả trang.</small>
            </div>
          </div>
          <div class="d-flex justify-content-end mt-3">
            <a href="{{ route('admin.popups.index') }}" class="btn btn-outline-secondary me-2">Huỷ</a>
            <button class="btn btn-primary" type="submit">Lưu</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
                    