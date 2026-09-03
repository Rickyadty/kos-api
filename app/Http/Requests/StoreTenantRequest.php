<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTenantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'no_hp' => ['required', 'string', 'max:20'],
            'alamat' => ['required', 'string'],
            'no_identitas' => ['required', 'string', 'max:50'],
            'pekerjaan' => ['required', 'string', 'max:100'],
            'kontak_darurat' => ['required', 'string', 'max:20'],
            'status' => ['sometimes', 'in:aktif,tidak_aktif'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama tenant wajib diisi.',
            'no_hp.required' => 'Nomor HP wajib diisi.',
            'alamat.required' => 'Alamat wajib diisi.',
            'no_identitas.required' => 'Nomor identitas wajib diisi.',
            'pekerjaan.required' => 'Pekerjaan wajib diisi.',
            'kontak_darurat.required' => 'Kontak darurat wajib diisi.',
            'status.in' => 'Status harus aktif atau tidak_aktif.',
        ];
    }
}
