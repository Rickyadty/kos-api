<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTenantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'no_hp' => ['sometimes', 'string', 'max:20'],
            'alamat' => ['sometimes', 'string'],
            'no_identitas' => ['sometimes', 'string', 'max:50'],
            'pekerjaan' => ['sometimes', 'string', 'max:100'],
            'kontak_darurat' => ['sometimes', 'string', 'max:20'],
            'status' => ['sometimes', 'in:aktif,tidak_aktif'],
        ];
    }

    public function messages(): array
    {
        return [
            'status.in' => 'Status harus aktif atau tidak_aktif.',
        ];
    }
}
