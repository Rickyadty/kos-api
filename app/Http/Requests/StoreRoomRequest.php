<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nomor_kamar' => ['required', 'string', 'max:10', 'unique:rooms,nomor_kamar'],
            'lantai' => ['required', 'integer', 'in:1,2'],
            'harga_bulanan' => ['required', 'numeric', 'min:0'],
            'kapasitas' => ['required', 'integer', 'min:1', 'max:2'],
            'keterangan' => ['nullable', 'string'],
            'gambar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'nomor_kamar.required' => 'Nomor kamar wajib diisi.',
            'nomor_kamar.unique' => 'Nomor kamar sudah digunakan.',
            'lantai.required' => 'Lantai wajib diisi.',
            'lantai.in' => 'Lantai hanya boleh 1 atau 2.',
            'harga_bulanan.required' => 'Harga bulanan wajib diisi.',
            'kapasitas.max' => 'Kapasitas maksimal 2 orang.',
        ];
    }
}
