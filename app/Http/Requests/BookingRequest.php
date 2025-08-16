<?php

namespace App\Http\Requests;

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'slots' => 'required|array|min:1',
            'slots.*.start_time' => 'required|date|before:slots.*.end_time',
            'slots.*.end_time' => 'required|date|after:slots.*.start_time',
        ];
    }
}
