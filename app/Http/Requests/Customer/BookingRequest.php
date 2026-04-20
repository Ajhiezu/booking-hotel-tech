<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class BookingRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'check_in'        => 'required|date|after_or_equal:today',
            'check_out'       => 'required|date|after:check_in',
            'guests'          => 'required|integer|min:1|max:20',
            'guest_name'      => 'required|string|max:255',
            'guest_email'     => 'required|email|max:255',
            'guest_phone'     => 'required|string|max:20',
            'payment_method'  => 'required|in:bank_transfer,credit_card,e_wallet,cash',
            'coupon_code'     => 'nullable|string|max:50',
            'special_requests' => 'nullable|string|max:1000',
        ];
    }
}
