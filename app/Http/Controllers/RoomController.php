<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoomRequest;
use App\Http\Requests\UpdateRoomRequest;
use App\Http\Resources\RoomResource;
use App\Models\Room;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RoomController extends Controller
{
    /**
     * GET /api/rooms
     */
    public function index(Request $request): JsonResponse
    {
        $query = Room::with('activeRentals');

        // Filter by lantai
        if ($request->filled('lantai')) {
            $query->where('lantai', $request->lantai);
        }

        // Filter by status (kosong, terisi, penuh)
        $rooms = $query->orderBy('nomor_kamar')->get();

        if ($request->filled('status')) {
            $rooms = $rooms->filter(function ($room) use ($request) {
                return $room->status_kamar === $request->status;
            })->values();
        }

        return response()->json([
            'success' => true,
            'message' => 'Data kamar berhasil diambil.',
            'data' => RoomResource::collection($rooms),
        ]);
    }

    /**
     * POST /api/rooms
     */
    public function store(StoreRoomRequest $request): JsonResponse
    {
        $validated = $request->validated();

        if ($request->hasFile('gambar')) {
            $validated['gambar'] = $request->file('gambar')->store('rooms', 'public');
        }

        $room = Room::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Kamar berhasil ditambahkan.',
            'data' => new RoomResource($room->load('activeRentals')),
        ], 201);
    }

    /**
     * GET /api/rooms/{id}
     */
    public function show(Room $room): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Detail kamar berhasil diambil.',
            'data' => new RoomResource($room->load('activeRentals')),
        ]);
    }

    /**
     * PUT /api/rooms/{id}
     */
    public function update(UpdateRoomRequest $request, Room $room): JsonResponse
    {
        $validated = $request->validated();

        if ($request->hasFile('gambar')) {
            if ($room->gambar) {
                Storage::disk('public')->delete($room->gambar);
            }
            $validated['gambar'] = $request->file('gambar')->store('rooms', 'public');
        }

        $room->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Kamar berhasil diperbarui.',
            'data' => new RoomResource($room->fresh()->load('activeRentals')),
        ]);
    }

    /**
     * DELETE /api/rooms/{id}
     */
    public function destroy(Room $room): JsonResponse
    {
        if ($room->gambar) {
            Storage::disk('public')->delete($room->gambar);
        }

        $room->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kamar berhasil dihapus.',
        ]);
    }
}
