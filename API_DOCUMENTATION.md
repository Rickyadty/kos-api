# API Documentation - Sistem Manajemen Kos

## Base URL

```
http://localhost:8000/api
```

## Authentication

API ini menggunakan **Laravel Sanctum Token Authentication**.

Setelah login, sertakan token di setiap request:

```
Authorization: Bearer {token}
```

---

## AUTH

### POST /api/auth/login

Login ke sistem.

**Request Body:**
```json
{
    "email": "admin@kos.test",
    "password": "password"
}
```

**Response 200 (Berhasil):**
```json
{
    "success": true,
    "message": "Login berhasil.",
    "data": {
        "user": {
            "id": 1,
            "name": "Administrator",
            "email": "admin@kos.test",
            "role": "admin",
            "created_at": "2026-09-03T00:00:00.000000Z",
            "updated_at": "2026-09-03T00:00:00.000000Z"
        },
        "token": "1|abc123..."
    }
}
```

**Response 401 (Gagal):**
```json
{
    "success": false,
    "message": "Email atau password salah."
}
```

**Axios Example:**
```javascript
const response = await axios.post('/api/auth/login', {
    email: 'admin@kos.test',
    password: 'password'
});
const token = response.data.data.token;
// Simpan token ke localStorage
localStorage.setItem('token', token);
```

---

### POST /api/auth/logout

Logout (revoke token).

**Headers:** `Authorization: Bearer {token}`

**Response 200:**
```json
{
    "success": true,
    "message": "Logout berhasil."
}
```

**Axios Example:**
```javascript
await axios.post('/api/auth/logout', {}, {
    headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
});
localStorage.removeItem('token');
```

---

### GET /api/auth/me

Mendapatkan data user yang sedang login.

**Headers:** `Authorization: Bearer {token}`

**Response 200:**
```json
{
    "success": true,
    "message": "Data user berhasil diambil.",
    "data": {
        "id": 1,
        "name": "Administrator",
        "email": "admin@kos.test",
        "role": "admin",
        "created_at": "2026-09-03T00:00:00.000000Z",
        "updated_at": "2026-09-03T00:00:00.000000Z"
    }
}
```

---

## DASHBOARD

### GET /api/dashboard

Mendapatkan statistik dashboard.

**Headers:** `Authorization: Bearer {token}`

**Response 200:**
```json
{
    "success": true,
    "message": "Data dashboard berhasil diambil.",
    "data": {
        "total_kamar": 20,
        "kamar_kosong": 8,
        "kamar_terisi": 5,
        "kamar_penuh": 7,
        "total_tenant_aktif": 13,
        "tagihan_belum_lunas": 35,
        "tagihan_lunas": 5,
        "total_pembayaran_bulan_ini": 4600000,
        "recent_payments": [
            {
                "id": 5,
                "room_bill_id": 12,
                "payer_tenant_id": 8,
                "jumlah_bayar": 800000,
                "tanggal_bayar": "2026-08-09",
                "diterima_oleh": 1,
                "keterangan": "Pembayaran kos Agustus 2026",
                "room_bill": { "..." },
                "payer": { "..." },
                "receiver": { "..." }
            }
        ],
        "unpaid_bills": [
            {
                "id": 6,
                "room_id": 1,
                "periode": "2026-09",
                "jumlah_tagihan": 1000000,
                "jatuh_tempo": "2026-09-10",
                "status": "belum_bayar",
                "room": { "..." }
            }
        ]
    }
}
```

**Axios Example:**
```javascript
const response = await axios.get('/api/dashboard', {
    headers: { Authorization: `Bearer ${token}` }
});
```

---

## TENANTS

### GET /api/tenants

Mendapatkan daftar tenant dengan pagination.

**Headers:** `Authorization: Bearer {token}`

**Query Parameters:**
| Parameter | Type   | Description                         |
|-----------|--------|-------------------------------------|
| search    | string | Cari berdasarkan nama/no_hp/no_identitas |
| status    | string | Filter: `aktif` atau `tidak_aktif`  |
| page      | int    | Halaman (default: 1)                |

**Response 200:**
```json
{
    "success": true,
    "message": "Data tenant berhasil diambil.",
    "data": [
        {
            "id": 1,
            "name": "Budi Santoso",
            "no_hp": "08111111001",
            "alamat": "Jl. Merdeka No. 1, Jakarta",
            "no_identitas": "3171010101010001",
            "pekerjaan": "Karyawan Swasta",
            "kontak_darurat": "08111111101",
            "status": "aktif",
            "created_at": "2026-09-03T00:00:00.000000Z",
            "updated_at": "2026-09-03T00:00:00.000000Z"
        }
    ],
    "meta": {
        "current_page": 1,
        "last_page": 1,
        "per_page": 15,
        "total": 15
    }
}
```

**Axios Example:**
```javascript
const response = await axios.get('/api/tenants', {
    params: { search: 'budi', status: 'aktif', page: 1 },
    headers: { Authorization: `Bearer ${token}` }
});
```

---

### POST /api/tenants

Membuat tenant baru.

**Headers:** `Authorization: Bearer {token}`

**Request Body:**
```json
{
    "name": "Ahmad Baru",
    "no_hp": "08198765432",
    "alamat": "Jl. Baru No. 5, Jakarta",
    "no_identitas": "3171234567890001",
    "pekerjaan": "Mahasiswa",
    "kontak_darurat": "08111111200",
    "status": "aktif"
}
```

**Response 201:**
```json
{
    "success": true,
    "message": "Tenant berhasil ditambahkan.",
    "data": { "..." }
}
```

**Validation Errors (422):**
```json
{
    "success": false,
    "message": "Validasi gagal.",
    "errors": {
        "name": ["Nama tenant wajib diisi."],
        "no_hp": ["Nomor HP wajib diisi."]
    }
}
```

---

### GET /api/tenants/{id}

Mendapatkan detail tenant.

**Response 200:** Data tenant tunggal.

**Response 404:**
```json
{
    "success": false,
    "message": "Data tidak ditemukan."
}
```

---

### PUT /api/tenants/{id}

Update data tenant (semua field opsional).

**Request Body:** (semua field opsional)
```json
{
    "status": "tidak_aktif"
}
```

---

### DELETE /api/tenants/{id}

Hapus tenant.

**Response 200:**
```json
{
    "success": true,
    "message": "Tenant berhasil dihapus."
}
```

---

## ROOMS

### GET /api/rooms

Mendapatkan daftar kamar beserta status dinamis.

**Headers:** `Authorization: Bearer {token}`

**Query Parameters:**
| Parameter | Type   | Description                             |
|-----------|--------|-----------------------------------------|
| lantai    | int    | Filter lantai: `1` atau `2`             |
| status    | string | Filter: `kosong`, `terisi`, atau `penuh` |

**Response 200:**
```json
{
    "success": true,
    "message": "Data kamar berhasil diambil.",
    "data": [
        {
            "id": 1,
            "nomor_kamar": "101",
            "lantai": 1,
            "harga_bulanan": 1000000,
            "kapasitas": 2,
            "keterangan": "Kamar lantai 1, fasilitas standar",
            "jumlah_penghuni_aktif": 2,
            "status_kamar": "penuh",
            "created_at": "2026-09-03T00:00:00.000000Z",
            "updated_at": "2026-09-03T00:00:00.000000Z"
        }
    ]
}
```

> **Note:** `status_kamar` dihitung secara dinamis:
> - `0` penghuni → `"kosong"`
> - `1` penghuni → `"terisi"`
> - `2` penghuni → `"penuh"`

**Axios Example:**
```javascript
const response = await axios.get('/api/rooms', {
    params: { lantai: 1, status: 'kosong' },
    headers: { Authorization: `Bearer ${token}` }
});
```

---

### POST /api/rooms

Membuat kamar baru.

**Request Body:**
```json
{
    "nomor_kamar": "111",
    "lantai": 1,
    "harga_bulanan": 1000000,
    "kapasitas": 2,
    "keterangan": "Kamar baru"
}
```

**Validation Rules:**
- `lantai`: hanya boleh `1` atau `2`
- `kapasitas`: maksimal `2`
- `nomor_kamar`: harus unik

---

### GET /api/rooms/{id}

Detail kamar termasuk `jumlah_penghuni_aktif` dan `status_kamar`.

---

### PUT /api/rooms/{id}

Update data kamar.

---

### DELETE /api/rooms/{id}

Hapus kamar.

---

## RENTALS

### GET /api/rentals

Mendapatkan daftar rental.

**Query Parameters:**
| Parameter | Type   | Description                   |
|-----------|--------|-------------------------------|
| status    | string | `aktif` atau `selesai`        |
| tenant_id | int    | Filter by tenant              |
| room_id   | int    | Filter by kamar               |
| page      | int    | Halaman pagination            |

**Response 200:**
```json
{
    "success": true,
    "message": "Data rental berhasil diambil.",
    "data": [
        {
            "id": 3,
            "tenant_id": 1,
            "room_id": 1,
            "tanggal_masuk": "2026-07-01",
            "tanggal_keluar": null,
            "status": "aktif",
            "tenant": {
                "id": 1,
                "name": "Budi Santoso",
                "..."
            },
            "room": {
                "id": 1,
                "nomor_kamar": "101",
                "..."
            }
        }
    ],
    "meta": { "..." }
}
```

---

### POST /api/rentals

Membuat rental baru.

**Request Body:**
```json
{
    "tenant_id": 1,
    "room_id": 5,
    "tanggal_masuk": "2026-09-01"
}
```

**Response 201 (Berhasil):**
```json
{
    "success": true,
    "message": "Rental berhasil dibuat.",
    "data": { "..." }
}
```

**Response 422 - Kamar penuh:**
```json
{
    "success": false,
    "message": "Kamar sudah penuh."
}
```

**Response 422 - Tenant sudah punya rental aktif:**
```json
{
    "success": false,
    "message": "Tenant sudah memiliki rental aktif."
}
```

---

### GET /api/rentals/{id}

Detail rental beserta data tenant dan kamar.

---

### PUT /api/rentals/{id}

Update data rental (terbatas).

---

### DELETE /api/rentals/{id}

Hapus rental.

---

## CHECKOUT

### PUT /api/rentals/{id}/checkout

Proses checkout tenant dari kamar.

**Headers:** `Authorization: Bearer {token}`

**Request Body:**
```json
{
    "tanggal_keluar": "2026-12-01"
}
```

**Response 200:**
```json
{
    "success": true,
    "message": "Checkout berhasil.",
    "data": {
        "id": 3,
        "status": "selesai",
        "tanggal_keluar": "2026-12-01",
        "tenant": { "..." },
        "room": { "..." }
    }
}
```

**Response 422 - Sudah checkout:**
```json
{
    "success": false,
    "message": "Rental sudah selesai / sudah checkout."
}
```

**Axios Example:**
```javascript
const response = await axios.put(`/api/rentals/${rentalId}/checkout`, {
    tanggal_keluar: '2026-12-01'
}, {
    headers: { Authorization: `Bearer ${token}` }
});
```

---

## ROOM BILLS

### GET /api/room-bills

Mendapatkan daftar tagihan.

**Query Parameters:**
| Parameter | Type   | Description                          |
|-----------|--------|--------------------------------------|
| periode   | string | Format `YYYY-MM`, contoh: `2026-09`  |
| status    | string | `belum_bayar` atau `lunas`           |
| room_id   | int    | Filter by kamar                      |
| page      | int    | Halaman pagination                   |

**Response 200:**
```json
{
    "success": true,
    "message": "Data tagihan berhasil diambil.",
    "data": [
        {
            "id": 1,
            "room_id": 1,
            "periode": "2026-09",
            "jumlah_tagihan": 1000000,
            "jatuh_tempo": "2026-09-10",
            "status": "belum_bayar",
            "room": { "..." },
            "payment": null
        }
    ],
    "meta": { "..." }
}
```

---

### GET /api/room-bills/{id}

Detail tagihan beserta payment (jika sudah dibayar).

---

### POST /api/room-bills/generate

Generate tagihan bulanan untuk semua kamar.

**Headers:** `Authorization: Bearer {token}`

**Request Body:**
```json
{
    "periode": "2026-10"
}
```

**Response 201:**
```json
{
    "success": true,
    "message": "Generate tagihan selesai. 20 tagihan dibuat, 0 dilewati (sudah ada).",
    "data": {
        "generated": [ "..." ],
        "skipped_rooms": [],
        "total_generated": 20,
        "total_skipped": 0
    }
}
```

**Note:** Tagihan yang sudah ada tidak akan dibuat ulang (tidak ada duplikat).

**Validation Errors (422):**
```json
{
    "success": false,
    "message": "Validasi gagal.",
    "errors": {
        "periode": ["Format periode harus YYYY-MM (contoh: 2026-09)."]
    }
}
```

**Axios Example:**
```javascript
const response = await axios.post('/api/room-bills/generate', {
    periode: '2026-10'
}, {
    headers: { Authorization: `Bearer ${token}` }
});
```

---

## PAYMENTS

### GET /api/payments

Mendapatkan daftar pembayaran.

**Query Parameters:**
| Parameter       | Type   | Description                       |
|-----------------|--------|-----------------------------------|
| payer_tenant_id | int    | Filter by tenant pembayar         |
| tanggal_bayar   | date   | Filter by tanggal bayar           |
| bulan           | string | Filter by periode `YYYY-MM`       |
| page            | int    | Halaman pagination                |

**Response 200:**
```json
{
    "success": true,
    "message": "Data pembayaran berhasil diambil.",
    "data": [
        {
            "id": 1,
            "room_bill_id": 1,
            "payer_tenant_id": 1,
            "jumlah_bayar": 1000000,
            "tanggal_bayar": "2026-08-05",
            "diterima_oleh": 1,
            "keterangan": "Pembayaran kos Agustus 2026",
            "room_bill": {
                "id": 1,
                "periode": "2026-08",
                "jumlah_tagihan": 1000000,
                "status": "lunas",
                "room": { "..." }
            },
            "payer": {
                "id": 1,
                "name": "Budi Santoso"
            },
            "receiver": {
                "id": 1,
                "name": "Administrator",
                "role": "admin"
            }
        }
    ],
    "meta": { "..." }
}
```

---

### GET /api/payments/{id}

Detail pembayaran.

---

### POST /api/payments

Mencatat pembayaran baru.

**Headers:** `Authorization: Bearer {token}`

**Request Body:**
```json
{
    "room_bill_id": 21,
    "payer_tenant_id": 1,
    "jumlah_bayar": 1000000,
    "tanggal_bayar": "2026-09-05",
    "keterangan": "Pembayaran kos September 2026"
}
```

**Response 201 (Berhasil):**
```json
{
    "success": true,
    "message": "Pembayaran berhasil dicatat.",
    "data": {
        "id": 6,
        "room_bill_id": 21,
        "payer_tenant_id": 1,
        "jumlah_bayar": 1000000,
        "tanggal_bayar": "2026-09-05",
        "diterima_oleh": 1,
        "keterangan": "Pembayaran kos September 2026",
        "room_bill": { "...", "status": "lunas" },
        "payer": { "..." },
        "receiver": { "..." }
    }
}
```

**Response 422 - Bill sudah lunas:**
```json
{
    "success": false,
    "message": "Tagihan ini sudah lunas."
}
```

**Response 422 - Sudah ada payment:**
```json
{
    "success": false,
    "message": "Tagihan ini sudah memiliki pembayaran."
}
```

**Response 422 - Bukan penghuni aktif:**
```json
{
    "success": false,
    "message": "Tenant bukan penghuni aktif kamar ini."
}
```

**Response 422 - Jumlah bayar tidak sesuai:**
```json
{
    "success": false,
    "message": "Jumlah bayar harus sama dengan jumlah tagihan (Rp 1.000.000)."
}
```

**Axios Example:**
```javascript
const response = await axios.post('/api/payments', {
    room_bill_id: 21,
    payer_tenant_id: 1,
    jumlah_bayar: 1000000,
    tanggal_bayar: '2026-09-05',
    keterangan: 'Pembayaran kos September 2026'
}, {
    headers: { Authorization: `Bearer ${token}` }
});
```

---

## HTTP Status Codes

| Code | Description                                      |
|------|--------------------------------------------------|
| 200  | OK - Request berhasil                            |
| 201  | Created - Data berhasil dibuat                   |
| 401  | Unauthorized - Token tidak valid / belum login   |
| 404  | Not Found - Data tidak ditemukan                 |
| 422  | Unprocessable Entity - Validasi gagal / business rule dilanggar |
| 500  | Internal Server Error - Error server             |

---

## Standard API Response Format

### Success Response
```json
{
    "success": true,
    "message": "Pesan sukses",
    "data": {}
}
```

### Error Response
```json
{
    "success": false,
    "message": "Pesan error",
    "errors": {}
}
```

---

## Axios Base Configuration (Frontend)

```javascript
// src/lib/axios.js
import axios from 'axios';

const api = axios.create({
    baseURL: 'http://localhost:8000/api',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
    },
    withCredentials: true,
});

// Interceptor: Auto-attach token
api.interceptors.request.use((config) => {
    const token = localStorage.getItem('token');
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
});

// Interceptor: Handle 401 (redirect ke login)
api.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response?.status === 401) {
            localStorage.removeItem('token');
            window.location.href = '/login';
        }
        return Promise.reject(error);
    }
);

export default api;
```

---

## Test Credentials

| Email              | Password | Role    |
|--------------------|----------|---------|
| admin@kos.test     | password | admin   |
| pemilik@kos.test   | password | pemilik |

---

## Catatan Penting untuk Frontend

1. **Token Storage**: Simpan token di `localStorage` atau `sessionStorage`
2. **CORS**: Backend dikonfigurasi untuk menerima request dari `http://localhost:5173`
3. **Status Kamar**: Jangan simpan di state lokal — selalu ambil dari API (dihitung dinamis)
4. **Pagination**: Gunakan `meta.current_page` dan `meta.last_page` untuk navigasi halaman
5. **Payment**: `jumlah_bayar` harus TEPAT sama dengan `jumlah_tagihan` di room bill
6. **Checkout**: Hanya bisa dilakukan pada rental dengan status `aktif`
7. **Generate Bill**: Aman dipanggil berkali-kali untuk periode yang sama (idempotent — duplikat dilewati)
