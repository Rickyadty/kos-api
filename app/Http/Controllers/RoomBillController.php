<?php

namespace App\Http\Controllers;

use App\Http\Requests\GenerateRoomBillRequest;
use App\Http\Resources\RoomBillResource;
use App\Models\Room;
use App\Models\RoomBill;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoomBillController extends Controller
{
    /**
     * GET /api/room-bills
     */
    public function index(Request $request): JsonResponse
    {
        $query = RoomBill::with(['room', 'payment']);

        if ($request->filled('periode')) {
            $query->where('periode', $request->periode);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('room_id')) {
            $query->where('room_id', $request->room_id);
        }

        $bills = $query->latest()->paginate(20);

        return response()->json([
            'success' => true,
            'message' => 'Data tagihan berhasil diambil.',
            'data' => RoomBillResource::collection($bills->items()),
            'meta' => [
                'current_page' => $bills->currentPage(),
                'last_page' => $bills->lastPage(),
                'per_page' => $bills->perPage(),
                'total' => $bills->total(),
            ],
        ]);
    }

    /**
     * GET /api/room-bills/{id}
     */
    public function show(RoomBill $roomBill): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Detail tagihan berhasil diambil.',
            'data' => new RoomBillResource($roomBill->load(['room', 'payment.payer', 'payment.receiver'])),
        ]);
    }

    /**
     * POST /api/room-bills/generate
     * Generate tagihan bulanan untuk semua kamar.
     */
    public function generate(GenerateRoomBillRequest $request): JsonResponse
    {
        $periode = $request->periode;

        // Parse periode untuk jatuh tempo (tanggal 10 bulan tersebut)
        $periodeDate = Carbon::createFromFormat('Y-m', $periode);
        $jatuhTempo = $periodeDate->copy()->day(10)->format('Y-m-d');

        $rooms = Room::all();
        $generated = [];
        $skipped = [];

        foreach ($rooms as $room) {
            // Cek duplicate
            $exists = RoomBill::where('room_id', $room->id)
                ->where('periode', $periode)
                ->exists();

            if ($exists) {
                $skipped[] = $room->nomor_kamar;
                continue;
            }

            $bill = RoomBill::create([
                'room_id' => $room->id,
                'periode' => $periode,
                'jumlah_tagihan' => $room->harga_bulanan,
                'jatuh_tempo' => $jatuhTempo,
                'status' => 'belum_bayar',
            ]);

            $generated[] = new RoomBillResource($bill->load('room'));
        }

        return response()->json([
            'success' => true,
            'message' => sprintf(
                'Generate tagihan selesai. %d tagihan dibuat, %d dilewati (sudah ada).',
                count($generated),
                count($skipped)
            ),
            'data' => [
                'generated' => $generated,
                'skipped_rooms' => $skipped,
                'total_generated' => count($generated),
                'total_skipped' => count($skipped),
            ],
        ], 201);
    }
}
