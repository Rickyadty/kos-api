<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenerateRoomBillRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'periode' => ['required', 'string', 'regex:/^\d{4}-\d{2}$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'periode.required' => 'Periode wajib diisi.',
            'periode.regex' => 'Format periode harus YYYY-MM (contoh: 2026-09).',
        ];
    }
}
