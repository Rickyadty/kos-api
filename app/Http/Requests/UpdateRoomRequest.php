<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $roomId = $this->route('room');

        return [
            'nomor_kamar' => ['sometimes', 'string', 'max:10', Rule::unique('rooms', 'nomor_kamar')->ignore($roomId)],
            'lantai' => ['sometimes', 'integer', 'in:1,2'],
            'harga_bulanan' => ['sometimes', 'numeric', 'min:0'],
            'kapasitas' => ['sometimes', 'integer', 'min:1', 'max:2'],
            'keterangan' => ['nullable', 'string'],
            'gambar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'nomor_kamar.unique' => 'Nomor kamar sudah digunakan.',
            'lantai.in' => 'Lantai hanya boleh 1 atau 2.',
            'kapasitas.max' => 'Kapasitas maksimal 2 orang.',
        ];
    }
}
