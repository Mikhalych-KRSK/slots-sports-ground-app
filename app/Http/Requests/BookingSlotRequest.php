<?php

namespace App\Http\Requests;

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookingSlotRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'start_time' => 'required|date|before:end_time',
            'end_time' => 'required|date|after:start_time',
        ];
    }
}
