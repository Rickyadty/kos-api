<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTenantRequest;
use App\Http\Requests\UpdateTenantRequest;
use App\Http\Resources\TenantResource;
use App\Models\Tenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    /**
     * GET /api/tenants
     * Support: ?search=, ?status=, ?page=
     */
    public function index(Request $request): JsonResponse
    {
        $query = Tenant::query();

        // Filter by search (name, no_hp, no_identitas)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('no_hp', 'like', "%{$search}%")
                    ->orWhere('no_identitas', 'like', "%{$search}%")
                    ->orWhere('pekerjaan', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $tenants = $query->latest()->paginate(15);

        return response()->json([
            'success' => true,
            'message' => 'Data tenant berhasil diambil.',
            'data' => TenantResource::collection($tenants->items()),
            'meta' => [
                'current_page' => $tenants->currentPage(),
                'last_page' => $tenants->lastPage(),
                'per_page' => $tenants->perPage(),
                'total' => $tenants->total(),
            ],
        ]);
    }

    /**
     * POST /api/tenants
     */
    public function store(StoreTenantRequest $request): JsonResponse
    {
        $tenant = Tenant::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Tenant berhasil ditambahkan.',
            'data' => new TenantResource($tenant),
        ], 201);
    }

    /**
     * GET /api/tenants/{id}
     */
    public function show(Tenant $tenant): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Detail tenant berhasil diambil.',
            'data' => new TenantResource($tenant),
        ]);
    }

    /**
     * PUT /api/tenants/{id}
     */
    public function update(UpdateTenantRequest $request, Tenant $tenant): JsonResponse
    {
        $tenant->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Tenant berhasil diperbarui.',
            'data' => new TenantResource($tenant->fresh()),
        ]);
    }

    /**
     * DELETE /api/tenants/{id}
     */
    public function destroy(Tenant $tenant): JsonResponse
    {
        $tenant->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tenant berhasil dihapus.',
        ]);
    }
}
