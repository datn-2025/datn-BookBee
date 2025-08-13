
document.addEventListener('DOMContentLoaded', function () {
    const physicalCheckbox = document.getElementById('has_physical');
    const physicalForm = document.getElementById('physical_format');
    const ebookCheckbox = document.getElementById('has_ebook');
    const ebookForm = document.getElementById('ebook_format');
    const allowSampleCheckbox = document.getElementById('allow_sample_read');
    const sampleFileContainer = document.getElementById('sample_file_container');

    function toggleDisplay(checkbox, element) {
        if (checkbox && element) {
            if (checkbox.checked) {
                element.style.display = 'block';
            } else {
                element.style.display = 'none';
            }
        }
    }

    // Khởi tạo trạng thái ban đầu
    if (physicalCheckbox && physicalForm) {
        toggleDisplay(physicalCheckbox, physicalForm);
    }
    if (ebookCheckbox && ebookForm) {
        toggleDisplay(ebookCheckbox, ebookForm);
    }
    if (allowSampleCheckbox && sampleFileContainer) {
        toggleDisplay(allowSampleCheckbox, sampleFileContainer);
    }

    // Lắng nghe sự kiện thay đổi checkbox
    if (physicalCheckbox && physicalForm) {
        physicalCheckbox.addEventListener('change', function () {
            toggleDisplay(this, physicalForm);
        });
    }

    if (ebookCheckbox && ebookForm) {
        ebookCheckbox.addEventListener('change', function () {
            toggleDisplay(this, ebookForm);
        });
    }

    if (allowSampleCheckbox && sampleFileContainer) {
        allowSampleCheckbox.addEventListener('change', function () {
            toggleDisplay(this, sampleFileContainer);
        });
    }
});

document.addEventListener('DOMContentLoaded', function () {
    const attrCheckboxes = document.querySelectorAll('.physical-attr');
    const priceSection = document.getElementById('physical_price_section');

    // Chỉ thực hiện nếu các element tồn tại
    if (priceSection && attrCheckboxes.length > 0) {
        function togglePriceSection() {
            // Kiểm tra xem có checkbox thuộc tính nào được chọn không
            const anyChecked = Array.from(attrCheckboxes).some(cb => cb.checked);
            priceSection.style.display = anyChecked ? 'block' : 'none';
        }

        // Gán sự kiện change cho tất cả checkbox thuộc tính
        attrCheckboxes.forEach(cb => {
            cb.addEventListener('change', togglePriceSection);
        });

        // Khởi tạo trạng thái khi load trang
        togglePriceSection();
    }
});

document.addEventListener('DOMContentLoaded', function () {
    // Xử lý thuộc tính sản phẩm
    const attributeSelects = document.querySelectorAll('.attribute-select');

    attributeSelects.forEach(select => {
        // Xử lý sự kiện khi chọn thuộc tính
        select.addEventListener('change', function () {
            // Hiển thị giá trị của thuộc tính đã chọn
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption.value) {
                const valueName = selectedOption.getAttribute('data-value-name');
                const extraPriceInput = this.closest('.attribute-group').querySelector('.attribute-extra-price');

                // Hiển thị giá trị đã chọn và giá thêm trong input
                if (extraPriceInput) {
                    extraPriceInput.placeholder = `Giá thêm cho ${valueName}`;
                }
            }
        });
    });

    // Xử lý nút thêm thuộc tính
    document.querySelectorAll('.add-attribute-value').forEach(button => {
        button.addEventListener('click', function () {
            const attributeGroup = this.closest('.attribute-group');
            if (!attributeGroup) return;
            
            const select = attributeGroup.querySelector('.attribute-select');
            const extraPriceInput = attributeGroup.querySelector('.attribute-extra-price');
            const selectedValuesContainer = attributeGroup.querySelector('.selected-values');
            
            if (!select || !extraPriceInput || !selectedValuesContainer) {
                console.error('Không tìm thấy các element cần thiết trong attribute group');
                return;
            }

            const selectedOption = select.options[select.selectedIndex];
            if (!selectedOption.value) {
                alert('Vui lòng chọn một giá trị thuộc tính');
                return;
            }

            const attributeId = select.getAttribute('data-attribute-id');
            const attributeName = select.getAttribute('data-attribute-name');
            const valueId = selectedOption.value;
            const valueName = selectedOption.getAttribute('data-value-name');
            const extraPrice = extraPriceInput.value || 0;

            // Kiểm tra xem thuộc tính này đã được thêm chưa
            const existingValue = attributeGroup.querySelector(`input[name="attribute_values[${valueId}][id]"]`);
            if (existingValue) {
                alert(`Thuộc tính ${valueName} đã được thêm`);
                return;
            }

            // Tạo badge hiển thị thuộc tính đã chọn
            const badge = document.createElement('div');
            badge.className = 'badge bg-primary me-2 mb-2 p-2';
            badge.innerHTML = `
                ${valueName} (+${new Intl.NumberFormat('vi-VN').format(extraPrice)}đ)
                <button type="button" class="btn-close btn-close-white ms-2" aria-label="Close"></button>
            `;

            // Tạo input ẩn để lưu dữ liệu
            const hiddenInputId = document.createElement('input');
            hiddenInputId.type = 'hidden';
            hiddenInputId.name = `attribute_values[${valueId}][id]`;
            hiddenInputId.value = valueId;

            const hiddenInputExtraPrice = document.createElement('input');
            hiddenInputExtraPrice.type = 'hidden';
            hiddenInputExtraPrice.name = `attribute_values[${valueId}][extra_price]`;
            hiddenInputExtraPrice.value = extraPrice;

            // Thêm vào container
            selectedValuesContainer.appendChild(badge);
            selectedValuesContainer.appendChild(hiddenInputId);
            selectedValuesContainer.appendChild(hiddenInputExtraPrice);

            // Xử lý sự kiện xóa
            badge.querySelector('.btn-close').addEventListener('click', function () {
                badge.remove();
                hiddenInputId.remove();
                hiddenInputExtraPrice.remove();
            });

            // Reset form
            select.value = '';
            extraPriceInput.value = '0';
            extraPriceInput.placeholder = 'Giá thêm';
        });
    });
});

// chọn ảnh
const coverImageInput = document.getElementById('cover_image');
if (coverImageInput) {
    coverImageInput.addEventListener('change', function (e) {
        const preview = document.querySelector('#cover_preview .preview-container');
        if (preview) {
            preview.innerHTML = ''; // clear
            const file = e.target.files[0];
            if (file) {
                const img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                img.className = 'img-fluid';
                preview.appendChild(img);
            }
        }
    });
}

const imagesInput = document.getElementById('images');
if (imagesInput) {
    imagesInput.addEventListener('change', function (e) {
        const preview = document.getElementById('images_preview');
        if (preview) {
            preview.innerHTML = ''; // clear
            Array.from(e.target.files).forEach(file => {
                const col = document.createElement('div');
                col.className = 'col-4';
                const img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                img.className = 'img-fluid rounded';
                col.appendChild(img);
                preview.appendChild(col);
            });
        }
    });
}

// xử lý thùng rác
$(document).ready(function () {
    // Handle delete confirmation
    $('.delete-item').on('click', function (e) {
        e.preventDefault();
        var form = $(this).closest('form');

        Swal.fire({
            title: 'Bạn có chắc chắn?',
            text: 'Sách sẽ được chuyển vào thùng rác!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Xóa',
            cancelButtonText: 'Hủy',
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});

// xử lý thuộc tính
document.addEventListener('DOMContentLoaded', function () {
    // Attribute values management
    function initAttributeValues() {
        console.log('Initializing attribute values functionality');
        const container = document.getElementById('attribute-values-container');
        const addValueBtn = document.getElementById('add-value-btn');

        if (!container || !addValueBtn) {
            console.log('Attribute values container or add button not found');
            return;
        }

        console.log('Found attribute values container and add button');

        // Add new value field
        addValueBtn.addEventListener('click', function () {
            console.log('Add value button clicked');
            const valueField = document.createElement('div');
            valueField.className = 'input-group mb-2';
            valueField.innerHTML = `
                <input type="text" class="form-control" name="values[]" placeholder="Nhập giá trị thuộc tính" required>
                <button type="button" class="btn btn-danger remove-value"><i class="ri-delete-bin-line"></i></button>
            `;
            container.appendChild(valueField);

            // Enable all remove buttons if there's more than one value field
            if (container.querySelectorAll('.input-group').length > 1) {
                container.querySelectorAll('.remove-value').forEach(btn => {
                    btn.disabled = false;
                });
            }
        });

        // Remove value field
        container.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-value') || e.target.closest('.remove-value')) {
                console.log('Remove value button clicked');
                const button = e.target.classList.contains('remove-value') ? e.target : e.target.closest('.remove-value');
                const inputGroup = button ? button.closest('.input-group') : null;
                
                if (!inputGroup) {
                    console.error('Không tìm thấy input group');
                    return;
                }

                if (container.querySelectorAll('.input-group').length > 1) {
                    inputGroup.remove();

                    // Disable the remove button if only one field remains
                    if (container.querySelectorAll('.input-group').length === 1) {
                        container.querySelector('.remove-value').disabled = true;
                    }
                }
            }
        });

        // Initialize: disable remove button if only one field exists
        if (container.querySelectorAll('.input-group').length === 1) {
            const removeBtn = container.querySelector('.remove-value');
            if (removeBtn) {
                removeBtn.disabled = true;
            }
        }

        console.log('Attribute values functionality initialized');
    }

    // Initialize attribute values functionality
    initAttributeValues();
});

// xử lý thu gọn/xem thêm mô tả
function toggleDescription() {
    const desc = document.getElementById('mota');
    const btn = document.getElementById('toggleDescription');
    
    if (!desc || !btn) {
        console.error('Không tìm thấy element mô tả hoặc nút toggle');
        return;
    }

    if (desc.style.maxHeight === '150px' || desc.style.maxHeight === '') {
      desc.style.maxHeight = 'none'; // mở hết mô tả
      btn.textContent = 'Thu gọn';
    } else {
      desc.style.maxHeight = '150px'; // thu gọn lại
      btn.textContent = 'Xem thêm';
    }
}