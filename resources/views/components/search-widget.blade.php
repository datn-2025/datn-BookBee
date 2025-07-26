<!-- Search Widget Component -->
<div class="search-widget">
    <h4 class="widget-title">Tìm kiếm nhanh</h4>
    <form method="GET" action="{{ route('books.search') }}" class="search-form">
        <div class="input-group">
            <input type="text" 
                   name="search" 
                   class="form-control search-input" 
                   placeholder="Tìm sách, tác giả, thương hiệu..."
                   value="{{ request('search') }}"
                   autocomplete="off">
            <button type="submit" class="btn btn-primary search-btn">
                <i class="fas fa-search"></i>
            </button>
        </div>
    </form>
    
    <!-- Quick Search Suggestions -->
    <div class="search-suggestions mt-3">
        <h6 class="text-muted small mb-2">Tìm kiếm phổ biến:</h6>
        <div class="d-flex flex-wrap gap-1">
            <a href="{{ route('books.search', ['search' => 'tiểu thuyết']) }}" 
               class="badge bg-light text-dark text-decoration-none">Tiểu thuyết</a>
            <a href="{{ route('books.search', ['search' => 'khoa học']) }}" 
               class="badge bg-light text-dark text-decoration-none">Khoa học</a>
            <a href="{{ route('books.search', ['search' => 'self-help']) }}" 
               class="badge bg-light text-dark text-decoration-none">Self-help</a>
            <a href="{{ route('books.search', ['search' => 'manga']) }}" 
               class="badge bg-light text-dark text-decoration-none">Manga</a>
        </div>
    </div>
</div>

<style>
.search-widget {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 10px;
    border: 1px solid #e9ecef;
}

.search-widget .widget-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #333;
    margin-bottom: 1rem;
}

.search-widget .search-input {
    border: 1px solid #ddd;
    border-radius: 5px 0 0 5px;
    padding: 0.75rem;
}

.search-widget .search-btn {
    border-radius: 0 5px 5px 0;
    padding: 0.75rem 1rem;
}

.search-widget .search-input:focus {
    border-color: #007bff;
    box-shadow: none;
}

.search-suggestions .badge {
    font-size: 0.8rem;
    padding: 0.4rem 0.8rem;
    margin: 0.1rem;
    transition: all 0.3s ease;
}

.search-suggestions .badge:hover {
    background-color: #007bff !important;
    color: white !important;
}
</style>
