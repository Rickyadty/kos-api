<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRentalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tenant_id' => ['required', 'integer', 'exists:tenants,id'],
            'room_id' => ['required', 'integer', 'exists:rooms,id'],
            'tanggal_masuk' => ['required', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'tenant_id.required' => 'Tenant wajib dipilih.',
            'tenant_id.exists' => 'Tenant tidak ditemukan.',
            'room_id.required' => 'Kamar wajib dipilih.',
            'room_id.exists' => 'Kamar tidak ditemukan.',
            'tanggal_masuk.required' => 'Tanggal masuk wajib diisi.',
            'tanggal_masuk.date' => 'Format tanggal masuk tidak valid.',
        ];
    }
}
