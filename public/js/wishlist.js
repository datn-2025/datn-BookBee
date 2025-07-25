// View state management
let isGridView = localStorage.getItem("wishlistView") === "grid";
let currentSort = localStorage.getItem("wishlistSort") || "date-desc";

document.addEventListener("DOMContentLoaded", () => {
    updateViewState();
    initializeKeyboardShortcuts();
    initializeSortingDropdown();
});

function updateViewState() {
    const container = document.getElementById("books-container");
    const icon = document.getElementById("view-icon");
    const text = document.getElementById("view-text");

    if (!container || !icon || !text) return;

    container.style.opacity = "0";

    setTimeout(() => {
        if (isGridView) {
            container.classList.remove("grid-cols-1");
            container.classList.add(
                "grid-cols-2",
                "md:grid-cols-3",
                "lg:grid-cols-2"
            );
            icon.classList.remove("fa-th-large");
            icon.classList.add("fa-list");
            text.textContent = "Dạng danh sách";
        } else {
            container.classList.remove(
                "grid-cols-2",
                "md:grid-cols-3",
                "lg:grid-cols-2"
            );
            container.classList.add("grid-cols-1");
            icon.classList.remove("fa-list");
            icon.classList.add("fa-th-large");
            text.textContent = "Dạng lưới";
        }

        container.style.opacity = "1";
    }, 300);
}

function toggleView() {
    isGridView = !isGridView;
    localStorage.setItem("wishlistView", isGridView ? "grid" : "list");
    updateViewState();
}

function initializeKeyboardShortcuts() {
    document.addEventListener("keydown", (e) => {
        if (e.key.toLowerCase() === "g") toggleView();
        if (e.key === "?") toggleShortcutsModal();
        if (e.ctrlKey && e.key.toLowerCase() === "d") {
            e.preventDefault();
            removeAllFromWishlist();
        }
        if (e.key.toLowerCase() === "s") {
            document.getElementById("sort-dropdown")?.click();
        }
    });
}

function initializeSortingDropdown() {
    const sortSelect = document.getElementById("sort-select");
    if (sortSelect) {
        sortSelect.value = currentSort;
        sortSelect.addEventListener("change", handleSort);
    }
}

function handleSort(e) {
    const sortBy = e.target.value;
    currentSort = sortBy;
    localStorage.setItem("wishlistSort", sortBy);

    const container = document.getElementById("books-container");
    if (!container) return;

    const items = Array.from(container.children);

    items.sort((a, b) => {
        const dateA = new Date(a.dataset.date);
        const dateB = new Date(b.dataset.date);
        const titleA = a.dataset.title;
        const titleB = b.dataset.title;

        switch (sortBy) {
            case "date-desc":
                return dateB - dateA;
            case "date-asc":
                return dateA - dateB;
            case "title-asc":
                return titleA.localeCompare(titleB);
            case "title-desc":
                return titleB.localeCompare(titleA);
            default:
                return 0;
        }
    });

    container.style.opacity = "0";
    setTimeout(() => {
        container.innerHTML = "";
        items.forEach((item) => container.appendChild(item));
        container.style.opacity = "1";
    }, 300);
}

function toggleShortcutsModal() {
    const modal = document.getElementById("shortcuts-modal");
    if (modal) {
        modal.classList.toggle("hidden");
    }
}

function showLoading() {
    const loadingOverlay = document.getElementById("loading-overlay");
    if (loadingOverlay) {
        loadingOverlay.classList.remove("hidden");
    }
}

function hideLoading() {
    const loadingOverlay = document.getElementById("loading-overlay");
    if (loadingOverlay) {
        loadingOverlay.classList.add("hidden");
    }
}

// Toastr options
if (typeof toastr !== "undefined") {
    toastr.options = {
        closeButton: true,
        progressBar: true,
        positionClass: "toast-top-right",
        timeOut: 3000,
        extendedTimeOut: 1000,
        preventDuplicates: true,
        newestOnTop: true,
        showEasing: "swing",
        hideEasing: "linear",
        showMethod: "fadeIn",
        hideMethod: "fadeOut",
    };
}

function showNotification(message, type = "success") {
    if (typeof toastr === "undefined") return;

    const icons = {
        success: '<i class="fas fa-check-circle mr-2"></i>',
        error: '<i class="fas fa-exclamation-circle mr-2"></i>',
        warning: '<i class="fas fa-exclamation-triangle mr-2"></i>',
        info: '<i class="fas fa-info-circle mr-2"></i>',
    };
    if (window.notificationTimeout) clearTimeout(window.notificationTimeout);
    window.notificationTimeout = setTimeout(
        () => toastr[type](icons[type] + message),
        100
    );
}

function showLoadingNotification(message = "Đang xử lý...") {
    if (typeof toastr === "undefined") return null;

    return toastr.info(
        '<i class="fas fa-spinner fa-spin mr-2"></i>' + message,
        null,
        {
            timeOut: 0,
            extendedTimeOut: 0,
            closeButton: false,
            progressBar: false,
        }
    );
}

const debounce = (fn, delay) => {
    let timeoutId;
    return (...args) => {
        if (timeoutId) clearTimeout(timeoutId);
        timeoutId = setTimeout(() => fn(...args), delay);
    };
};

async function handleApiCall(url, options = {}, loadingMsg = "Đang xử lý...") {
    const loadingToast = showLoadingNotification(loadingMsg);
    try {
        const response = await fetch(url, {
            ...options,
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content"),
                ...options.headers,
            },
        });
        const data = await response.json();
        if (loadingToast) toastr.clear(loadingToast);
        if (!response.ok) throw new Error(data.message || "Có lỗi xảy ra");
        return data;
    } catch (error) {
        if (loadingToast) toastr.clear(loadingToast);
        throw error;
    }
}

async function removeAllFromWishlist() {
    if (typeof Swal === "undefined") return;

    const confirmResult = await Swal.fire({
        title: "Xóa tất cả?",
        text: "Bạn có chắc chắn muốn xóa tất cả sách khỏi danh sách yêu thích?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Xóa tất cả",
        cancelButtonText: "Hủy",
        confirmButtonColor: "#EF4444",
        cancelButtonColor: "#6B7280",
    });
    if (!confirmResult.isConfirmed) return;

    try {
        const items = document.querySelectorAll(".book-card");
        items.forEach((item, index) => {
            setTimeout(() => {
                item.style.transform = "scale(0.8)";
                item.style.opacity = "0";
            }, index * 100);
        });
        const data = await handleApiCall(
            "/wishlist/delete-all",
            { method: "POST" },
            "Đang xóa tất cả..."
        );
        if (data.success) {
            showNotification(
                "Đã xóa tất cả sách khỏi danh sách yêu thích",
                "success"
            );
            setTimeout(() => location.reload(), 800);
        }
    } catch (error) {
        console.error("Error:", error);
        showNotification(error.message || "Đã có lỗi xảy ra", "error");
        const items = document.querySelectorAll(".book-card");
        items.forEach((item) => {
            item.style.transform = "";
            item.style.opacity = "";
        });
    }
}

async function addToCart(bookId, bookFormatId = null, attributes = null) {
    try {
        const bodyData = { book_id: bookId };
        if (bookFormatId) bodyData.book_format_id = bookFormatId;
        if (attributes) bodyData.attributes = attributes;

        const response = await fetch("/wishlist/add-to-cart", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content"),
            },
            body: JSON.stringify(bodyData),
        });
        const data = await response.json();
        if (data.success) {
            showNotification(
                data.message || "Đã thêm sách vào giỏ hàng",
                "success"
            );
        } else {
            showNotification(data.message || "Thêm giỏ hàng thất bại", "error");
        }
    } catch (error) {
        showNotification("Lỗi kết nối server", "error");
    }
}

function removeFromWishlist(bookId) {
    if (
        !confirm("Bạn có chắc chắn muốn xóa sách này khỏi danh sách yêu thích?")
    )
        return;

    fetch("/wishlist/delete", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content"),
        },
        body: JSON.stringify({ book_id: bookId }),
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                showNotification("Đã xóa sách khỏi danh sách yêu thích");
                const bookElement = document.querySelector(
                    `[data-book-id="${bookId}"]`
                );
                if (bookElement) {
                    bookElement.classList.add("fade-out");
                    setTimeout(() => location.reload(), 500);
                } else {
                    location.reload();
                }
            } else {
                showNotification(data.message, "error");
            }
        })
        .catch((error) => {
            console.error("Error:", error);
            showNotification("Đã có lỗi xảy ra", "error");
        });
}
