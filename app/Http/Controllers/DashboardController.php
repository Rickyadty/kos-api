<?php

namespace App\Http\Controllers;

use App\Http\Resources\PaymentResource;
use App\Http\Resources\RoomBillResource;
use App\Models\Payment;
use App\Models\Rental;
use App\Models\Room;
use App\Models\RoomBill;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    /**
     * GET /api/dashboard
     */
    public function index(): JsonResponse
    {
        // Total kamar dan status
        $rooms = Room::withCount([
            'activeRentals as penghuni_aktif_count',
        ])->get();

        $totalKamar = $rooms->count();
        $kamarKosong = $rooms->filter(fn ($r) => $r->penghuni_aktif_count === 0)->count();
        $kamarTerisi = $rooms->filter(fn ($r) => $r->penghuni_aktif_count === 1)->count();
        $kamarPenuh = $rooms->filter(fn ($r) => $r->penghuni_aktif_count >= 2)->count();

        // Total tenant aktif
        $totalTenantAktif = Tenant::where('status', 'aktif')->count();

        // Tagihan
        $tagihanBelumLunas = RoomBill::where('status', 'belum_bayar')->count();
        $tagihanLunas = RoomBill::where('status', 'lunas')->count();

        // Total pembayaran bulan ini
        $bulanIni = Carbon::now()->format('Y-m');
        $totalPembayaranBulanIni = Payment::whereHas('roomBill', function ($q) use ($bulanIni) {
            $q->where('periode', $bulanIni);
        })->sum('jumlah_bayar');

        // Recent payments (5 terbaru)
        $recentPayments = Payment::with(['roomBill.room', 'payer', 'receiver'])
            ->latest()
            ->limit(5)
            ->get();

        // Unpaid bills (5 terbaru)
        $unpaidBills = RoomBill::with('room')
            ->where('status', 'belum_bayar')
            ->latest()
            ->limit(5)
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Data dashboard berhasil diambil.',
            'data' => [
                'total_kamar' => $totalKamar,
                'kamar_kosong' => $kamarKosong,
                'kamar_terisi' => $kamarTerisi,
                'kamar_penuh' => $kamarPenuh,
                'total_tenant_aktif' => $totalTenantAktif,
                'tagihan_belum_lunas' => $tagihanBelumLunas,
                'tagihan_lunas' => $tagihanLunas,
                'total_pembayaran_bulan_ini' => (float) $totalPembayaranBulanIni,
                'recent_payments' => PaymentResource::collection($recentPayments),
                'unpaid_bills' => RoomBillResource::collection($unpaidBills),
            ],
        ]);
    }
}
