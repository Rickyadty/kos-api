<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'room_bill_id' => ['required', 'integer', 'exists:room_bills,id'],
            'payer_tenant_id' => ['required', 'integer', 'exists:tenants,id'],
            'jumlah_bayar' => ['required', 'numeric', 'min:0'],
            'tanggal_bayar' => ['required', 'date'],
            'keterangan' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'room_bill_id.required' => 'Tagihan wajib dipilih.',
            'room_bill_id.exists' => 'Tagihan tidak ditemukan.',
            'payer_tenant_id.required' => 'Tenant pembayar wajib dipilih.',
            'payer_tenant_id.exists' => 'Tenant pembayar tidak ditemukan.',
            'jumlah_bayar.required' => 'Jumlah bayar wajib diisi.',
            'jumlah_bayar.numeric' => 'Jumlah bayar harus berupa angka.',
            'tanggal_bayar.required' => 'Tanggal bayar wajib diisi.',
            'tanggal_bayar.date' => 'Format tanggal bayar tidak valid.',
        ];
    }
}
