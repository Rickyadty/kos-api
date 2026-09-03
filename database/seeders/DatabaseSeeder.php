<?php

namespace Database\Seeders;

use App\Models\Payment;
use App\Models\Rental;
use App\Models\Room;
use App\Models\RoomBill;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // =========================================
        // USERS
        // =========================================
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@kos.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Pemilik Kos',
            'email' => 'pemilik@kos.test',
            'password' => Hash::make('password'),
            'role' => 'pemilik',
        ]);

        // =========================================
        // ROOMS - 20 kamar
        // =========================================
        // Lantai 1: Kamar 101-110 @ Rp 1.000.000
        // for ($i = 1; $i <= 10; $i++) {
        //     Room::create([
        //         'nomor_kamar' => '10' . $i,
        //         'lantai' => 1,
        //         'harga_bulanan' => 1000000,
        //         'kapasitas' => 2,
        //         'keterangan' => 'Kamar lantai 1, fasilitas standar',
        //     ]);
        // }

        // // Lantai 2: Kamar 201-210 @ Rp 800.000
        // for ($i = 1; $i <= 10; $i++) {
        //     Room::create([
        //         'nomor_kamar' => '20' . $i,
        //         'lantai' => 2,
        //         'harga_bulanan' => 800000,
        //         'kapasitas' => 2,
        //         'keterangan' => 'Kamar lantai 2, fasilitas standar',
        //     ]);
        // }

        // // =========================================
        // // TENANTS - 15 tenant
        // // =========================================
        // $tenants = [
        //     ['name' => 'Budi Santoso', 'no_hp' => '08111111001', 'alamat' => 'Jl. Merdeka No. 1, Jakarta', 'no_identitas' => '3171010101010001', 'pekerjaan' => 'Karyawan Swasta', 'kontak_darurat' => '08111111101', 'status' => 'aktif'],
        //     ['name' => 'Siti Rahayu', 'no_hp' => '08111111002', 'alamat' => 'Jl. Sudirman No. 2, Bandung', 'no_identitas' => '3171010101010002', 'pekerjaan' => 'Mahasiswi', 'kontak_darurat' => '08111111102', 'status' => 'aktif'],
        //     ['name' => 'Ahmad Fauzi', 'no_hp' => '08111111003', 'alamat' => 'Jl. Gatot Subroto No. 3, Surabaya', 'no_identitas' => '3171010101010003', 'pekerjaan' => 'Pedagang', 'kontak_darurat' => '08111111103', 'status' => 'aktif'],
        //     ['name' => 'Dewi Lestari', 'no_hp' => '08111111004', 'alamat' => 'Jl. Diponegoro No. 4, Semarang', 'no_identitas' => '3171010101010004', 'pekerjaan' => 'Guru', 'kontak_darurat' => '08111111104', 'status' => 'aktif'],
        //     ['name' => 'Rizky Pratama', 'no_hp' => '08111111005', 'alamat' => 'Jl. Ahmad Yani No. 5, Medan', 'no_identitas' => '3171010101010005', 'pekerjaan' => 'Mahasiswa', 'kontak_darurat' => '08111111105', 'status' => 'aktif'],
        //     ['name' => 'Nurhaliza', 'no_hp' => '08111111006', 'alamat' => 'Jl. Pemuda No. 6, Yogyakarta', 'no_identitas' => '3171010101010006', 'pekerjaan' => 'Karyawan Swasta', 'kontak_darurat' => '08111111106', 'status' => 'aktif'],
        //     ['name' => 'Hendra Wijaya', 'no_hp' => '08111111007', 'alamat' => 'Jl. Pahlawan No. 7, Malang', 'no_identitas' => '3171010101010007', 'pekerjaan' => 'Wiraswasta', 'kontak_darurat' => '08111111107', 'status' => 'aktif'],
        //     ['name' => 'Yuni Kartika', 'no_hp' => '08111111008', 'alamat' => 'Jl. Veteran No. 8, Solo', 'no_identitas' => '3171010101010008', 'pekerjaan' => 'Perawat', 'kontak_darurat' => '08111111108', 'status' => 'aktif'],
        //     ['name' => 'Doni Kusuma', 'no_hp' => '08111111009', 'alamat' => 'Jl. Kartini No. 9, Denpasar', 'no_identitas' => '3171010101010009', 'pekerjaan' => 'Mahasiswa', 'kontak_darurat' => '08111111109', 'status' => 'aktif'],
        //     ['name' => 'Fitri Handayani', 'no_hp' => '08111111010', 'alamat' => 'Jl. Imam Bonjol No. 10, Padang', 'no_identitas' => '3171010101010010', 'pekerjaan' => 'Karyawan Swasta', 'kontak_darurat' => '08111111110', 'status' => 'aktif'],
        //     ['name' => 'Arif Rahman', 'no_hp' => '08111111011', 'alamat' => 'Jl. Cut Nyak Dien No. 11, Aceh', 'no_identitas' => '3171010101010011', 'pekerjaan' => 'Teknisi', 'kontak_darurat' => '08111111111', 'status' => 'aktif'],
        //     ['name' => 'Rini Susanti', 'no_hp' => '08111111012', 'alamat' => 'Jl. Pangeran No. 12, Pontianak', 'no_identitas' => '3171010101010012', 'pekerjaan' => 'Bidan', 'kontak_darurat' => '08111111112', 'status' => 'aktif'],
        //     ['name' => 'Bagas Nugroho', 'no_hp' => '08111111013', 'alamat' => 'Jl. Sultan No. 13, Makassar', 'no_identitas' => '3171010101010013', 'pekerjaan' => 'Mahasiswa', 'kontak_darurat' => '08111111113', 'status' => 'tidak_aktif'],
        //     ['name' => 'Citra Amelia', 'no_hp' => '08111111014', 'alamat' => 'Jl. Raya No. 14, Manado', 'no_identitas' => '3171010101010014', 'pekerjaan' => 'Karyawan Swasta', 'kontak_darurat' => '08111111114', 'status' => 'tidak_aktif'],
        //     ['name' => 'Eko Prasetyo', 'no_hp' => '08111111015', 'alamat' => 'Jl. Jenderal No. 15, Kupang', 'no_identitas' => '3171010101010015', 'pekerjaan' => 'Pedagang', 'kontak_darurat' => '08111111115', 'status' => 'aktif'],
        // ];

        // foreach ($tenants as $tenantData) {
        //     Tenant::create($tenantData);
        // }

        // // =========================================
        // // RENTALS - Beberapa aktif dan selesai
        // // =========================================

        // // Rental SELESAI (tenant 13, 14 sudah tidak_aktif)
        // Rental::create([
        //     'tenant_id' => 13,
        //     'room_id' => 1,
        //     'tanggal_masuk' => '2026-01-01',
        //     'tanggal_keluar' => '2026-06-30',
        //     'status' => 'selesai',
        // ]);

        // Rental::create([
        //     'tenant_id' => 14,
        //     'room_id' => 2,
        //     'tanggal_masuk' => '2026-01-01',
        //     'tanggal_keluar' => '2026-07-31',
        //     'status' => 'selesai',
        // ]);

        // // Rental AKTIF
        // // Kamar 101 (id=1): tenant 1 dan 2 (penuh)
        // Rental::create([
        //     'tenant_id' => 1,
        //     'room_id' => 1,
        //     'tanggal_masuk' => '2026-07-01',
        //     'tanggal_keluar' => null,
        //     'status' => 'aktif',
        // ]);

        // Rental::create([
        //     'tenant_id' => 2,
        //     'room_id' => 1,
        //     'tanggal_masuk' => '2026-07-01',
        //     'tanggal_keluar' => null,
        //     'status' => 'aktif',
        // ]);

        // // Kamar 102 (id=2): tenant 3 (terisi)
        // Rental::create([
        //     'tenant_id' => 3,
        //     'room_id' => 2,
        //     'tanggal_masuk' => '2026-08-01',
        //     'tanggal_keluar' => null,
        //     'status' => 'aktif',
        // ]);

        // // Kamar 103 (id=3): tenant 4 dan 5 (penuh)
        // Rental::create([
        //     'tenant_id' => 4,
        //     'room_id' => 3,
        //     'tanggal_masuk' => '2026-08-01',
        //     'tanggal_keluar' => null,
        //     'status' => 'aktif',
        // ]);

        // Rental::create([
        //     'tenant_id' => 5,
        //     'room_id' => 3,
        //     'tanggal_masuk' => '2026-08-01',
        //     'tanggal_keluar' => null,
        //     'status' => 'aktif',
        // ]);

        // // Kamar 104 (id=4): tenant 6 (terisi)
        // Rental::create([
        //     'tenant_id' => 6,
        //     'room_id' => 4,
        //     'tanggal_masuk' => '2026-09-01',
        //     'tanggal_keluar' => null,
        //     'status' => 'aktif',
        // ]);

        // // Kamar 201 (id=11): tenant 7 (terisi)
        // Rental::create([
        //     'tenant_id' => 7,
        //     'room_id' => 11,
        //     'tanggal_masuk' => '2026-08-01',
        //     'tanggal_keluar' => null,
        //     'status' => 'aktif',
        // ]);

        // // Kamar 202 (id=12): tenant 8 dan 9 (penuh)
        // Rental::create([
        //     'tenant_id' => 8,
        //     'room_id' => 12,
        //     'tanggal_masuk' => '2026-07-15',
        //     'tanggal_keluar' => null,
        //     'status' => 'aktif',
        // ]);

        // Rental::create([
        //     'tenant_id' => 9,
        //     'room_id' => 12,
        //     'tanggal_masuk' => '2026-08-01',
        //     'tanggal_keluar' => null,
        //     'status' => 'aktif',
        // ]);

        // // Kamar 203 (id=13): tenant 10 (terisi)
        // Rental::create([
        //     'tenant_id' => 10,
        //     'room_id' => 13,
        //     'tanggal_masuk' => '2026-09-01',
        //     'tanggal_keluar' => null,
        //     'status' => 'aktif',
        // ]);

        // // Kamar 204 (id=14): tenant 11 dan 12 (penuh)
        // Rental::create([
        //     'tenant_id' => 11,
        //     'room_id' => 14,
        //     'tanggal_masuk' => '2026-08-01',
        //     'tanggal_keluar' => null,
        //     'status' => 'aktif',
        // ]);

        // Rental::create([
        //     'tenant_id' => 12,
        //     'room_id' => 14,
        //     'tanggal_masuk' => '2026-08-15',
        //     'tanggal_keluar' => null,
        //     'status' => 'aktif',
        // ]);

        // // Tenant 15 di kamar 205 (id=15)
        // Rental::create([
        //     'tenant_id' => 15,
        //     'room_id' => 15,
        //     'tanggal_masuk' => '2026-09-01',
        //     'tanggal_keluar' => null,
        //     'status' => 'aktif',
        // ]);

        // // =========================================
        // // ROOM BILLS - Generate untuk Agustus & September 2026
        // // =========================================
        // $rooms = Room::all();

        // // Generate tagihan Agustus 2026
        // foreach ($rooms as $room) {
        //     RoomBill::create([
        //         'room_id' => $room->id,
        //         'periode' => '2026-08',
        //         'jumlah_tagihan' => $room->harga_bulanan,
        //         'jatuh_tempo' => '2026-08-10',
        //         'status' => 'belum_bayar',
        //     ]);
        // }

        // // Generate tagihan September 2026
        // foreach ($rooms as $room) {
        //     RoomBill::create([
        //         'room_id' => $room->id,
        //         'periode' => '2026-09',
        //         'jumlah_tagihan' => $room->harga_bulanan,
        //         'jatuh_tempo' => '2026-09-10',
        //         'status' => 'belum_bayar',
        //     ]);
        // }

        // // =========================================
        // // PAYMENTS - Beberapa pembayaran (Agustus sudah lunas)
        // // =========================================
        // // Bayar tagihan Agustus kamar 101 (id=1) oleh tenant 1
        // $bill1 = RoomBill::where('room_id', 1)->where('periode', '2026-08')->first();
        // Payment::create([
        //     'room_bill_id' => $bill1->id,
        //     'payer_tenant_id' => 1,
        //     'jumlah_bayar' => 1000000,
        //     'tanggal_bayar' => '2026-08-05',
        //     'diterima_oleh' => $admin->id,
        //     'keterangan' => 'Pembayaran kos Agustus 2026',
        // ]);
        // $bill1->update(['status' => 'lunas']);

        // // Bayar tagihan Agustus kamar 102 (id=2) oleh tenant 3
        // $bill2 = RoomBill::where('room_id', 2)->where('periode', '2026-08')->first();
        // Payment::create([
        //     'room_bill_id' => $bill2->id,
        //     'payer_tenant_id' => 3,
        //     'jumlah_bayar' => 1000000,
        //     'tanggal_bayar' => '2026-08-06',
        //     'diterima_oleh' => $admin->id,
        //     'keterangan' => 'Pembayaran kos Agustus 2026',
        // ]);
        // $bill2->update(['status' => 'lunas']);

        // // Bayar tagihan Agustus kamar 103 (id=3) oleh tenant 4
        // $bill3 = RoomBill::where('room_id', 3)->where('periode', '2026-08')->first();
        // Payment::create([
        //     'room_bill_id' => $bill3->id,
        //     'payer_tenant_id' => 4,
        //     'jumlah_bayar' => 1000000,
        //     'tanggal_bayar' => '2026-08-07',
        //     'diterima_oleh' => $admin->id,
        //     'keterangan' => 'Pembayaran kos Agustus 2026',
        // ]);
        // $bill3->update(['status' => 'lunas']);

        // // Bayar tagihan Agustus kamar 201 (id=11) oleh tenant 7
        // $bill11 = RoomBill::where('room_id', 11)->where('periode', '2026-08')->first();
        // Payment::create([
        //     'room_bill_id' => $bill11->id,
        //     'payer_tenant_id' => 7,
        //     'jumlah_bayar' => 800000,
        //     'tanggal_bayar' => '2026-08-08',
        //     'diterima_oleh' => $admin->id,
        //     'keterangan' => 'Pembayaran kos Agustus 2026',
        // ]);
        // $bill11->update(['status' => 'lunas']);

        // // Bayar tagihan Agustus kamar 202 (id=12) oleh tenant 8
        // $bill12 = RoomBill::where('room_id', 12)->where('periode', '2026-08')->first();
        // Payment::create([
        //     'room_bill_id' => $bill12->id,
        //     'payer_tenant_id' => 8,
        //     'jumlah_bayar' => 800000,
        //     'tanggal_bayar' => '2026-08-09',
        //     'diterima_oleh' => $admin->id,
        //     'keterangan' => 'Pembayaran kos Agustus 2026',
        // ]);
        // $bill12->update(['status' => 'lunas']);
    }
}
