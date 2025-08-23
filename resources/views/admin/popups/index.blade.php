    @extends('layouts.backend')

@section('title', 'Quản lý Popup')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
      <h4 class="mb-sm-0 text-dark fw-semibold">Popup quảng cáo</h4>
      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item"><a href="{{ route('admin.popups.index') }}">Popups</a></li>
          <li class="breadcrumb-item active">Danh sách</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Danh sách</h5>
        @permission('popup.create')
        <a href="{{ route('admin.popups.create') }}" class="btn btn-primary"><i class="ri-add-line me-1"></i>Thêm popup</a>
        @endpermission
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead class="table-light">
              <tr>
                <th>#</th>
                <th>Tiêu đề</th>
                <th>Hiển thị</th>
                <th>Khoảng thời gian</th>
                <th>Độ ưu tiên</th>
                <th>Tần suất</th>
                <th>Trang áp dụng</th>
                <th class="text-end">Tác vụ</th>
              </tr>
            </thead>
            <tbody>
              @forelse($popups as $i => $popup)
              <tr>
                <td>{{ $popups->firstItem() + $i }}</td>
                <td>{{ $popup->title ?: '—' }}</td>
                <td>
                  <span class="badge {{ $popup->active ? 'bg-success' : 'bg-secondary' }}">{{ $popup->active ? 'Bật' : 'Tắt' }}</span>
                </td>
                <td>
                  <small class="text-muted">
                    {{ $popup->start_at ? $popup->start_at->format('d/m/Y H:i') : 'Không' }}
                    —
                    {{ $popup->end_at ? $popup->end_at->format('d/m/Y H:i') : 'Không' }}
                  </small>
                </td>
                <td>{{ $popup->priority }}</td>
                <td>{{ $popup->frequency }}</td>
                <td>
                  @if(empty($popup->pages))
                    <span class="badge bg-info">Tất cả</span>
                  @else
                    <span class="text-muted small">{{ implode(', ', $popup->pages) }}</span>
                  @endif
                </td>
                <td class="text-end">
                  <div class="btn-group">
                    @permission('popup.edit')
                    <form action="{{ route('admin.popups.toggle', $popup) }}" method="post" class="me-2">
                      @csrf
                      @method('PATCH')
                      <button class="btn btn-sm {{ $popup->active ? 'btn-outline-secondary' : 'btn-outline-success' }}" type="submit">
                        {{ $popup->active ? 'Tắt' : 'Bật' }}
                      </button>
                    </form>
                    <a href="{{ route('admin.popups.edit', $popup) }}" class="btn btn-sm btn-outline-primary me-2">Sửa</a>
                    @endpermission
                    @permission('popup.delete')
                    <form action="{{ route('admin.popups.destroy', $popup) }}" method="post" onsubmit="return confirm('Xóa popup này?');">
                      @csrf
                      @method('DELETE')
                      <button class="btn btn-sm btn-outline-danger" type="submit">Xóa</button>
                    </form>
                    @endpermission
                  </div>
                </td>
              </tr>
              @empty
              <tr><td colspan="8" class="text-center text-muted">Chưa có popup</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
        @if($popups->hasPages())
        <div class="d-flex justify-content-end mt-3">{!! $popups->links('layouts.pagination') !!}</div>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection
