<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRentalRequest;
use App\Http\Requests\StoreRentalRequest;
use App\Http\Resources\RentalResource;
use App\Models\Rental;
use App\Models\Room;
use App\Models\Tenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RentalController extends Controller
{
    /**
     * GET /api/rentals
     */
    public function index(Request $request): JsonResponse
    {
        $query = Rental::with(['tenant', 'room']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('tenant_id')) {
            $query->where('tenant_id', $request->tenant_id);
        }

        if ($request->filled('room_id')) {
            $query->where('room_id', $request->room_id);
        }

        $rentals = $query->latest()->paginate(15);

        return response()->json([
            'success' => true,
            'message' => 'Data rental berhasil diambil.',
            'data' => RentalResource::collection($rentals->items()),
            'meta' => [
                'current_page' => $rentals->currentPage(),
                'last_page' => $rentals->lastPage(),
                'per_page' => $rentals->perPage(),
                'total' => $rentals->total(),
            ],
        ]);
    }

    /**
     * POST /api/rentals
     */
    public function store(StoreRentalRequest $request): JsonResponse
    {
        $tenant = Tenant::findOrFail($request->tenant_id);
        $room = Room::findOrFail($request->room_id);

        // Business rule: Tenant hanya boleh punya 1 rental aktif
        $hasActiveRental = Rental::where('tenant_id', $tenant->id)
            ->where('status', 'aktif')
            ->exists();

        if ($hasActiveRental) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant sudah memiliki rental aktif.',
            ], 422);
        }

        // Business rule: Kamar maksimal 2 penghuni aktif
        $activeCount = Rental::where('room_id', $room->id)
            ->where('status', 'aktif')
            ->count();

        if ($activeCount >= $room->kapasitas) {
            return response()->json([
                'success' => false,
                'message' => 'Kamar sudah penuh.',
            ], 422);
        }

        $rental = Rental::create([
            'tenant_id' => $request->tenant_id,
            'room_id' => $request->room_id,
            'tanggal_masuk' => $request->tanggal_masuk,
            'tanggal_keluar' => null,
            'status' => 'aktif',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Rental berhasil dibuat.',
            'data' => new RentalResource($rental->load(['tenant', 'room'])),
        ], 201);
    }

    /**
     * GET /api/rentals/{id}
     */
    public function show(Rental $rental): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Detail rental berhasil diambil.',
            'data' => new RentalResource($rental->load(['tenant', 'room'])),
        ]);
    }

    /**
     * PUT /api/rentals/{id}
     */
    public function update(Request $request, Rental $rental): JsonResponse
    {
        $validated = $request->validate([
            'tanggal_masuk' => ['sometimes', 'date'],
            'keterangan' => ['nullable', 'string'],
        ]);

        $rental->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Rental berhasil diperbarui.',
            'data' => new RentalResource($rental->fresh()->load(['tenant', 'room'])),
        ]);
    }

    /**
     * DELETE /api/rentals/{id}
     */
    public function destroy(Rental $rental): JsonResponse
    {
        $rental->delete();

        return response()->json([
            'success' => true,
            'message' => 'Rental berhasil dihapus.',
        ]);
    }

    /**
     * PUT /api/rentals/{id}/checkout
     */
    public function checkout(CheckoutRentalRequest $request, Rental $rental): JsonResponse
    {
        if ($rental->status === 'selesai') {
            return response()->json([
                'success' => false,
                'message' => 'Rental sudah selesai / sudah checkout.',
            ], 422);
        }

        $rental->update([
            'status' => 'selesai',
            'tanggal_keluar' => $request->tanggal_keluar,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Checkout berhasil.',
            'data' => new RentalResource($rental->fresh()->load(['tenant', 'room'])),
        ]);
    }
}
