<?php

namespace Tests\Feature;

use App\Models\Payment;
use App\Models\Rental;
use App\Models\Room;
use App\Models\RoomBill;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KosApiTest extends TestCase
{
    use RefreshDatabase;

    // =========================================
    // Helper Methods
    // =========================================

    protected function createAdmin(): User
    {
        return User::factory()->create([
            'name' => 'Administrator',
            'email' => 'admin@kos.test',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);
    }

    protected function createRoom(array $overrides = []): Room
    {
        static $counter = 0;
        $counter++;

        return Room::create(array_merge([
            'nomor_kamar' => '10' . $counter,
            'lantai' => 1,
            'harga_bulanan' => 1000000,
            'kapasitas' => 2,
        ], $overrides));
    }

    protected function createTenant(array $overrides = []): Tenant
    {
        return Tenant::create(array_merge([
            'name' => 'Test Tenant ' . uniqid(),
            'no_hp' => '081234567890',
            'alamat' => 'Jl. Test No. 1',
            'no_identitas' => '3171' . rand(100000000000, 999999999999),
            'pekerjaan' => 'Karyawan',
            'kontak_darurat' => '081234567891',
            'status' => 'aktif',
        ], $overrides));
    }

    protected function createActiveRental(Tenant $tenant, Room $room): Rental
    {
        return Rental::create([
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'tanggal_masuk' => '2026-09-01',
            'tanggal_keluar' => null,
            'status' => 'aktif',
        ]);
    }

    protected function createRoomBill(Room $room, array $overrides = []): RoomBill
    {
        return RoomBill::create(array_merge([
            'room_id' => $room->id,
            'periode' => '2026-09',
            'jumlah_tagihan' => $room->harga_bulanan,
            'jatuh_tempo' => '2026-09-10',
            'status' => 'belum_bayar',
        ], $overrides));
    }

    protected function actingAsAdmin(): self
    {
        $admin = $this->createAdmin();
        return $this->actingAs($admin, 'sanctum');
    }

    // =========================================
    // TEST 1: Login berhasil
    // =========================================

    public function test_login_berhasil(): void
    {
        $this->createAdmin();

        $response = $this->postJson('/api/auth/login', [
            'email' => 'admin@kos.test',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'user' => ['id', 'name', 'email', 'role'],
                    'token',
                ],
            ])
            ->assertJson(['success' => true]);
    }

    // =========================================
    // TEST 2: Login gagal
    // =========================================

    public function test_login_gagal_password_salah(): void
    {
        $this->createAdmin();

        $response = $this->postJson('/api/auth/login', [
            'email' => 'admin@kos.test',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(401)
            ->assertJson(['success' => false]);
    }

    public function test_login_gagal_email_tidak_ditemukan(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'notexist@kos.test',
            'password' => 'password',
        ]);

        $response->assertStatus(401)
            ->assertJson(['success' => false]);
    }

    // =========================================
    // TEST 3: User dapat logout
    // =========================================

    public function test_user_dapat_logout(): void
    {
        $admin = $this->createAdmin();
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withToken($token)
            ->postJson('/api/auth/logout');

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    // =========================================
    // TEST 4: User tidak dapat mengakses API tanpa auth
    // =========================================

    public function test_akses_api_tanpa_token_ditolak(): void
    {
        $this->getJson('/api/dashboard')->assertStatus(401);
        $this->getJson('/api/tenants')->assertStatus(401);
        $this->getJson('/api/rooms')->assertStatus(401);
        $this->getJson('/api/rentals')->assertStatus(401);
        $this->getJson('/api/room-bills')->assertStatus(401);
        $this->getJson('/api/payments')->assertStatus(401);
    }

    // =========================================
    // TEST 5: Kamar maksimal 2 penghuni aktif
    // =========================================

    public function test_kamar_maksimal_2_penghuni_aktif(): void
    {
        $admin = $this->createAdmin();
        $room = $this->createRoom();

        $tenant1 = $this->createTenant();
        $tenant2 = $this->createTenant();
        $tenant3 = $this->createTenant();

        // Isi kamar dengan 2 tenant
        $this->createActiveRental($tenant1, $room);
        $this->createActiveRental($tenant2, $room);

        // Coba tambah tenant ke-3 ke kamar yang sama
        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/rentals', [
                'tenant_id' => $tenant3->id,
                'room_id' => $room->id,
                'tanggal_masuk' => '2026-09-01',
            ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Kamar sudah penuh.',
            ]);
    }

    // =========================================
    // TEST 6: Tenant tidak dapat punya 2 rental aktif
    // =========================================

    public function test_tenant_tidak_boleh_punya_2_rental_aktif(): void
    {
        $admin = $this->createAdmin();
        $tenant = $this->createTenant();
        $room1 = $this->createRoom(['nomor_kamar' => '101']);
        $room2 = $this->createRoom(['nomor_kamar' => '102']);

        // Buat rental aktif pertama
        $this->createActiveRental($tenant, $room1);

        // Coba buat rental aktif kedua untuk tenant yang sama
        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/rentals', [
                'tenant_id' => $tenant->id,
                'room_id' => $room2->id,
                'tanggal_masuk' => '2026-09-01',
            ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Tenant sudah memiliki rental aktif.',
            ]);
    }

    // =========================================
    // TEST 7: Checkout rental berhasil
    // =========================================

    public function test_checkout_rental_berhasil(): void
    {
        $admin = $this->createAdmin();
        $tenant = $this->createTenant();
        $room = $this->createRoom();
        $rental = $this->createActiveRental($tenant, $room);

        $response = $this->actingAs($admin, 'sanctum')
            ->putJson("/api/rentals/{$rental->id}/checkout", [
                'tanggal_keluar' => '2026-12-01',
            ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $rental->refresh();
        $this->assertEquals('selesai', $rental->status);
        $this->assertEquals('2026-12-01', $rental->tanggal_keluar->format('Y-m-d'));
    }

    // =========================================
    // TEST 8: Duplicate room bill ditolak
    // =========================================

    public function test_duplicate_room_bill_ditolak(): void
    {
        $admin = $this->createAdmin();
        $room = $this->createRoom(['nomor_kamar' => '101']);

        // Generate pertama
        $this->actingAs($admin, 'sanctum')
            ->postJson('/api/room-bills/generate', ['periode' => '2026-09']);

        // Generate kedua (duplicate)
        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/room-bills/generate', ['periode' => '2026-09']);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'total_generated' => 0,
                    'total_skipped' => 1,
                ],
            ]);
    }

    // =========================================
    // TEST 9: Pembayaran kurang ditolak
    // =========================================

    public function test_pembayaran_kurang_ditolak(): void
    {
        $admin = $this->createAdmin();
        $tenant = $this->createTenant();
        $room = $this->createRoom();
        $this->createActiveRental($tenant, $room);
        $bill = $this->createRoomBill($room);

        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/payments', [
                'room_bill_id' => $bill->id,
                'payer_tenant_id' => $tenant->id,
                'jumlah_bayar' => 500000, // kurang dari 1.000.000
                'tanggal_bayar' => '2026-09-05',
            ]);

        $response->assertStatus(422)
            ->assertJson(['success' => false]);
    }

    // =========================================
    // TEST 10: Pembayaran lebih ditolak
    // =========================================

    public function test_pembayaran_lebih_ditolak(): void
    {
        $admin = $this->createAdmin();
        $tenant = $this->createTenant();
        $room = $this->createRoom();
        $this->createActiveRental($tenant, $room);
        $bill = $this->createRoomBill($room);

        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/payments', [
                'room_bill_id' => $bill->id,
                'payer_tenant_id' => $tenant->id,
                'jumlah_bayar' => 1500000, // lebih dari 1.000.000
                'tanggal_bayar' => '2026-09-05',
            ]);

        $response->assertStatus(422)
            ->assertJson(['success' => false]);
    }

    // =========================================
    // TEST 11: Payment kedua pada bill yang sama ditolak
    // =========================================

    public function test_payment_kedua_pada_bill_yang_sama_ditolak(): void
    {
        $admin = $this->createAdmin();
        $tenant = $this->createTenant();
        $room = $this->createRoom();
        $this->createActiveRental($tenant, $room);
        $bill = $this->createRoomBill($room);

        // Payment pertama (sukses)
        $this->actingAs($admin, 'sanctum')
            ->postJson('/api/payments', [
                'room_bill_id' => $bill->id,
                'payer_tenant_id' => $tenant->id,
                'jumlah_bayar' => 1000000,
                'tanggal_bayar' => '2026-09-05',
            ]);

        // Payment kedua (harus ditolak)
        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/payments', [
                'room_bill_id' => $bill->id,
                'payer_tenant_id' => $tenant->id,
                'jumlah_bayar' => 1000000,
                'tanggal_bayar' => '2026-09-06',
            ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Tagihan ini sudah lunas.',
            ]);
    }

    // =========================================
    // TEST 12: Tenant bukan penghuni aktif tidak bisa bayar
    // =========================================

    public function test_tenant_bukan_penghuni_aktif_tidak_dapat_bayar(): void
    {
        $admin = $this->createAdmin();

        $room1 = $this->createRoom(['nomor_kamar' => '101']);
        $room2 = $this->createRoom(['nomor_kamar' => '102']);

        $tenantRoom1 = $this->createTenant();
        $tenantRoom2 = $this->createTenant();

        $this->createActiveRental($tenantRoom1, $room1);
        $this->createActiveRental($tenantRoom2, $room2);

        // Bill untuk room2
        $bill = $this->createRoomBill($room2, ['jumlah_tagihan' => 1000000]);

        // Tenant dari room1 coba bayar bill room2 (bukan penghuni aktif room2)
        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/payments', [
                'room_bill_id' => $bill->id,
                'payer_tenant_id' => $tenantRoom1->id, // bukan penghuni room2
                'jumlah_bayar' => 1000000,
                'tanggal_bayar' => '2026-09-05',
            ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Tenant bukan penghuni aktif kamar ini.',
            ]);
    }

    // =========================================
    // TEST 13: Payment berhasil mengubah bill menjadi lunas
    // =========================================

    public function test_payment_berhasil_mengubah_bill_menjadi_lunas(): void
    {
        $admin = $this->createAdmin();
        $tenant = $this->createTenant();
        $room = $this->createRoom();
        $this->createActiveRental($tenant, $room);
        $bill = $this->createRoomBill($room);

        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/payments', [
                'room_bill_id' => $bill->id,
                'payer_tenant_id' => $tenant->id,
                'jumlah_bayar' => 1000000,
                'tanggal_bayar' => '2026-09-05',
                'keterangan' => 'Pembayaran kos September 2026',
            ]);

        $response->assertStatus(201)
            ->assertJson(['success' => true]);

        // Verifikasi bill sudah lunas
        $this->assertDatabaseHas('room_bills', [
            'id' => $bill->id,
            'status' => 'lunas',
        ]);

        // Verifikasi payment tercreate
        $this->assertDatabaseHas('payments', [
            'room_bill_id' => $bill->id,
            'payer_tenant_id' => $tenant->id,
            'jumlah_bayar' => 1000000,
        ]);
    }
}
