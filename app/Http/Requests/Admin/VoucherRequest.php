<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VoucherRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'description' => ['nullable', 'string', 'max:255'],
            'discount_type' => ['required', Rule::in(['percent', 'fixed'])],
            // Các trường giảm giá sẽ được áp dụng điều kiện phía dưới
            'discount_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'fixed_discount' => ['nullable', 'numeric', 'min:0'],
            'max_discount' => ['nullable', 'numeric', 'min:0'],
            'min_order_value' => ['nullable', 'numeric', 'min:0'],
            'quantity' => ['required', 'integer', 'min:1'],
            'valid_from' => ['required', 'date'],
            'valid_to' => ['required', 'date', 'after:valid_from'],
            'status' => ['required', 'string', Rule::in(['active', 'inactive'])],
            'condition_type' => ['required', Rule::in(['all', 'book', 'author', 'brand', 'category'])],
        ];

        // Chỉ validate code khi tạo mới
        if (!$this->voucher) {
            $rules['code'] = ['required', 'string', 'max:50', 'unique:vouchers,code'];
        }

        if ($this->input('condition_type') !== 'all') {
            $rules['condition_objects'] = ['required', 'array', 'min:1'];
            $rules['condition_objects.*'] = ['required', 'exists:' . $this->getTableForType() . ',id'];
        }

        // Ràng buộc theo discount_type
        if ($this->input('discount_type') === 'percent') {
            // Yêu cầu phần trăm và max_discount có thể cần thiết tuỳ nghiệp vụ; giữ nullable nhưng bắt buộc percent
            $rules['discount_percent'] = ['required', 'numeric', 'min:0', 'max:100'];
            // Cho phép max_discount null (không trần), nếu có thì phải hợp lệ
        } elseif ($this->input('discount_type') === 'fixed') {
            $rules['fixed_discount'] = ['required', 'numeric', 'min:0'];
        }

        return $rules;
    }

    private function getTableForType()
    {
        return match($this->input('condition_type')) {
            'book' => 'books',
            'author' => 'authors',
            'brand' => 'brands',
            'category' => 'categories',
            default => 'books'
        };
    }

    public function messages()
    {
        return [
            'code.required' => 'Vui lòng nhập mã voucher',
            'code.unique' => 'Mã voucher đã tồn tại',
            'discount_type.required' => 'Vui lòng chọn loại giảm giá',
            'discount_type.in' => 'Loại giảm giá không hợp lệ',
            'discount_percent.required' => 'Vui lòng nhập phần trăm giảm giá',
            'discount_percent.min' => 'Phần trăm giảm giá phải lớn hơn 0',
            'discount_percent.max' => 'Phần trăm giảm giá không được vượt quá 100',
            'fixed_discount.required' => 'Vui lòng nhập số tiền giảm cố định',
            'max_discount.min' => 'Giảm giá tối đa phải lớn hơn 0',
            'min_order_value.required' => 'Vui lòng nhập giá trị đơn hàng tối thiểu',
            'min_order_value.min' => 'Giá trị đơn hàng tối thiểu phải lớn hơn 0',
            'quantity.required' => 'Vui lòng nhập số lượng voucher',
            'quantity.min' => 'Số lượng voucher phải lớn hơn 0',
            'valid_from.required' => 'Vui lòng chọn ngày bắt đầu',
            'valid_to.required' => 'Vui lòng chọn ngày kết thúc',
            'valid_to.after' => 'Ngày kết thúc phải sau ngày bắt đầu',
            'status.required' => 'Vui lòng chọn trạng thái',
            'condition_type.required' => 'Vui lòng chọn loại điều kiện',
            'condition_objects.required' => 'Vui lòng chọn ít nhất một đối tượng',
            'condition_objects.*.exists' => 'Đối tượng không tồn tại'
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->filled('max_discount')) {
            $this->merge([
                'max_discount' => str_replace(',', '', $this->max_discount)
            ]);
        }
        if ($this->filled('min_order_value')) {
            $this->merge([
                'min_order_value' => str_replace(',', '', $this->min_order_value)
            ]);
        }
        if ($this->filled('fixed_discount')) {
            $this->merge([
                'fixed_discount' => str_replace(',', '', $this->fixed_discount)
            ]);
        }
    }
}
