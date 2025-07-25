<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RefundRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'reason' => ['required', 'string', 'in:wrong_item,quality_issue,shipping_delay,wrong_qty,other'],
            'details' => ['required', 'string', 'min:20', 'max:1000'],
            'refund_method' => ['required', 'string', 'in:vnpay,wallet'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'reason.required' => 'Vui lòng chọn lý do hoàn tiền',
            'reason.in' => 'Lý do không hợp lệ',
            'details.required' => 'Vui lòng nhập chi tiết lý do',
            'details.min' => 'Chi tiết lý do phải có ít nhất :min ký tự',
            'details.max' => 'Chi tiết lý do không được vượt quá :max ký tự',
            'refund_method.required' => 'Vui lòng chọn phương thức hoàn tiền',
            'refund_method.in' => 'Phương thức hoàn tiền không hợp lệ',
        ];
    }
}
