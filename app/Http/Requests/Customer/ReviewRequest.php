<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class ReviewRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'rating'             => 'required|integer|between:1,5',
            'cleanliness_rating' => 'nullable|integer|between:1,5',
            'service_rating'     => 'nullable|integer|between:1,5',
            'location_rating'    => 'nullable|integer|between:1,5',
            'value_rating'       => 'nullable|integer|between:1,5',
            'title'              => 'nullable|string|max:255',
            'comment'            => 'required|string|min:10|max:2000',
        ];
    }
}
