<?php

namespace App\Http\Requests\Owner;

use Illuminate\Foundation\Http\FormRequest;

class RoomRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'           => 'required|string|max:255',
            'type'           => 'required|in:standard,deluxe,suite,family,villa',
            'description'    => 'nullable|string',
            'capacity'       => 'required|integer|min:1|max:20',
            'price_per_night' => 'required|numeric|min:0',
            'weekend_price'  => 'nullable|numeric|min:0',
            'holiday_price'  => 'nullable|numeric|min:0',
            'total_rooms'    => 'required|integer|min:1',
            'bed_type'       => 'nullable|in:single,double,queen,king,twin',
            'bed_count'      => 'nullable|integer|min:1',
            'size_sqm'       => 'nullable|numeric|min:0',
            'has_wifi'       => 'boolean',
            'has_ac'         => 'boolean',
            'has_tv'         => 'boolean',
            'has_bathroom'   => 'boolean',
            'has_balcony'    => 'boolean',
            'is_active'      => 'boolean',
            'cover_image'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ];
    }
}
