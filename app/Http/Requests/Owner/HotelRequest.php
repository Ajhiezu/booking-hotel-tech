<?php

namespace App\Http\Requests\Owner;

use Illuminate\Foundation\Http\FormRequest;

class HotelRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'           => 'required|string|max:255',
            'description'    => 'required|string',
            'address'        => 'required|string|max:500',
            'city'           => 'required|string|max:100',
            'province'       => 'required|string|max:100',
            'country'        => 'nullable|string|max:100',
            'postal_code'    => 'nullable|string|max:20',
            'phone'          => 'nullable|string|max:20',
            'email'          => 'nullable|email|max:255',
            'website'        => 'nullable|url|max:255',
            'star_rating'    => 'required|integer|between:1,5',
            'base_price'     => 'required|numeric|min:0',
            'cover_image'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'images.*'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'policies'       => 'nullable|string',
            'check_in_time'  => 'nullable|string',
            'check_out_time' => 'nullable|string',
            'min_stay'       => 'nullable|integer|min:1',
            'facilities'     => 'nullable|array',
            'facilities.*'   => 'exists:facilities,id',
            'latitude'       => 'nullable|numeric',
            'longitude'      => 'nullable|numeric',
        ];
    }
}
