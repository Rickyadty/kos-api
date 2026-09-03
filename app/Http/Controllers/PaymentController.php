<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentRequest;
use App\Http\Resources\PaymentResource;
use App\Models\Payment;
use App\Models\Rental;
use App\Models\RoomBill;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    /**
     * GET /api/payments
     */
    public function index(Request $request): JsonResponse
    {
        $query = Payment::with(['roomBill.room', 'payer', 'receiver']);

        if ($request->filled('payer_tenant_id')) {
            $query->where('payer_tenant_id', $request->payer_tenant_id);
        }

        if ($request->filled('tanggal_bayar')) {
            $query->whereDate('tanggal_bayar', $request->tanggal_bayar);
        }

        // Filter by bulan (format: YYYY-MM)
        if ($request->filled('bulan')) {
            $query->whereHas('roomBill', function ($q) use ($request) {
                $q->where('periode', $request->bulan);
            });
        }

        $payments = $query->latest()->paginate(20);

        return response()->json([
            'success' => true,
            'message' => 'Data pembayaran berhasil diambil.',
            'data' => PaymentResource::collection($payments->items()),
            'meta' => [
                'current_page' => $payments->currentPage(),
                'last_page' => $payments->lastPage(),
                'per_page' => $payments->perPage(),
                'total' => $payments->total(),
            ],
        ]);
    }

    /**
     * GET /api/payments/{id}
     */
    public function show(Payment $payment): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Detail pembayaran berhasil diambil.',
            'data' => new PaymentResource($payment->load(['roomBill.room', 'payer', 'receiver'])),
        ]);
    }

    /**
     * POST /api/payments
     * Semua proses payment menggunakan DB::transaction()
     */
    public function store(StorePaymentRequest $request): JsonResponse
    {
        $roomBill = RoomBill::with('room')->findOrFail($request->room_bill_id);

        // Validasi: bill belum lunas
        if ($roomBill->status === 'lunas') {
            return response()->json([
                'success' => false,
                'message' => 'Tagihan ini sudah lunas.',
            ], 422);
        }

        // Validasi: bill belum mempunyai payment
        if ($roomBill->payment()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Tagihan ini sudah memiliki pembayaran.',
            ], 422);
        }

        // Validasi: payer tenant adalah penghuni aktif kamar tersebut
        $isActiveResident = Rental::where('tenant_id', $request->payer_tenant_id)
            ->where('room_id', $roomBill->room_id)
            ->where('status', 'aktif')
            ->exists();

        if (! $isActiveResident) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant bukan penghuni aktif kamar ini.',
            ], 422);
        }

        // Validasi: jumlah_bayar harus sama persis dengan jumlah_tagihan
        if ((float) $request->jumlah_bayar !== (float) $roomBill->jumlah_tagihan) {
            return response()->json([
                'success' => false,
                'message' => sprintf(
                    'Jumlah bayar harus sama dengan jumlah tagihan (Rp %s).',
                    number_format($roomBill->jumlah_tagihan, 0, ',', '.')
                ),
            ], 422);
        }

        // DB Transaction
        $payment = DB::transaction(function () use ($request, $roomBill) {
            // 1. Create payment
            $payment = Payment::create([
                'room_bill_id' => $request->room_bill_id,
                'payer_tenant_id' => $request->payer_tenant_id,
                'jumlah_bayar' => $request->jumlah_bayar,
                'tanggal_bayar' => $request->tanggal_bayar,
                'diterima_oleh' => $request->user()->id,
                'keterangan' => $request->keterangan,
            ]);

            // 2. Update room_bill menjadi lunas
            $roomBill->update(['status' => 'lunas']);

            return $payment;
        });

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran berhasil dicatat.',
            'data' => new PaymentResource($payment->load(['roomBill.room', 'payer', 'receiver'])),
        ], 201);
    }
}
