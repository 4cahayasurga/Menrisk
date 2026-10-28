# IFPRO Risk Command Center (Menrisk) — CodeIgniter 3 + SQL Server

Aplikasi **IFPRO Risk Command Center** dengan arsitektur penuh **CodeIgniter 3 MVC**, terintegrasi ke database **Microsoft SQL Server**, dilengkapi fitur **Otentikasi Login** dan **Role-Based Settings (User & Department)**.

---

## 🔐 Akun Default untuk Login

| Username | Password | Level / Role | Hak Akses |
| :--- | :--- | :--- | :--- |
| **admin** | `admin123` | **admin** | Full Access: Dashboard ERM, **Settings -> User**, **Settings -> Department** |
| **user** | `user123` | **user** | Restricted: Dashboard ERM (Akses menu Settings diblokir 403) |
| **it_risk** | `user123` | **user** | Restricted: Dashboard ERM |

---

## 🏗️ Struktur Proyek CodeIgniter 3

```
menrisk/
├── index.php                        # CI3 Front Controller bootstrap
├── .htaccess                        # URL Rewriting untuk clean URL
├── system/                          # CI3 Framework System Core
│
├── application/
│   ├── config/                      # Konfigurasi Lengkap
│   │   ├── config.php               # Auto-detect base_url, session, cookie
│   │   ├── database.php             # Koneksi SQL Server (sqlsrv)
│   │   ├── autoload.php             # Auto-load database, session, models, libraries, helpers
│   │   └── routes.php               # Routes: default, auth, settings/user, settings/department
│   │
│   ├── controllers/                 # Controller Layer
│   │   ├── Auth.php                 # Login, validasi bcrypt, logout
│   │   ├── Menrisk.php              # Dashboard utama ERM (Auth-protected)
│   │   └── Settings.php             # Pengelolaan User & Department (Admin Only)
│   │
│   ├── models/                      # Model Layer (SQL Server)
│   │   ├── User_model.php           # CRUD dbo.users & many-to-many dbo.user_departments
│   │   ├── Department_model.php     # CRUD dbo.departments & hitung total user terkait
│   │   ├── Risk_model.php           # Query tabel dbo.risks
│   │   └── Kri_model.php            # Query tabel dbo.kri
│   │
│   ├── libraries/
│   │   └── Risk_calculator.php      # Perhitungan matriks heatmap 5x5, KPI, & Risk Appetite
│   │
│   ├── helpers/
│   │   └── menrisk_helper.php       # Formatting warna skor, badge level, print_rr debugging
│   │
│   ├── views/                       # View Layer
│   │   ├── auth/
│   │   │   └── login_view.php       # Halaman Login modern
│   │   ├── settings/
│   │   │   ├── users_view.php       # Menu Settings -> User & modal Switch Department
│   │   │   └── departments_view.php # Menu Settings -> Department
│   │   └── menrisk_view.php         # Dashboard Executive BOD
│   │
│   └── errors/                      # Error Views (403 Forbidden, 404, dll)
│
├── assets/
│   ├── css/styles.css
│   └── js/app.js
│
├── database_sqlserver.sql           # DDL & Seed Data lengkap (risks, kri, users, departments, user_departments)
├── intruksi.md                      # Catatan kebutuhan fitur
└── README.md
```

---

## 🗄️ Skema Database SQL Server (`menrisk_db`)

1. **`dbo.users`**:
   - `id` (PK, INT IDENTITY)
   - `username` (VARCHAR(50), UNIQUE)
   - `password` (VARCHAR(255), Bcrypt)
   - `nama` (NVARCHAR(100))
   - `level` (VARCHAR(20), `'admin'` atau `'user'`)
   - `is_active` (TINYINT)

2. **`dbo.departments`**:
   - `id` (PK, INT IDENTITY)
   - `name` (NVARCHAR(100))
   - `code` (VARCHAR(50))
   - `description` (NVARCHAR(255))

3. **`dbo.user_departments` (Relasi Many-to-Many)**:
   - `id` (PK, INT IDENTITY)
   - `user_id` (FK -> `dbo.users.id` ON DELETE CASCADE)
   - `department_id` (FK -> `dbo.departments.id` ON DELETE CASCADE)
   - UNIQUE constraint pada `(user_id, department_id)`

4. **`dbo.risks` (Merelasi ke `dbo.departments`)**:
   - `id` (PK, VARCHAR(20))
   - `title` (NVARCHAR(255))
   - `owner` (NVARCHAR(100))
   - `department_id` (**FK -> `dbo.departments.id`** ON DELETE SET NULL)
   - `cat` (NVARCHAR(100))
   - `inherent`, `residual`, `progress` (INT)
   - `appetite`, `due`, `decision`, `mitigation`, `period`

5. **`dbo.kri` (Merelasi ke `dbo.departments`)**:
   - `id` (PK, INT IDENTITY)
   - `indicator` (NVARCHAR(150))
   - `value`, `threshold` (NVARCHAR)
   - `department_id` (**FK -> `dbo.departments.id`** ON DELETE SET NULL)
   - `status`, `period`

---


## 🌟 Fitur Sesuai Instruksi

1. **Fitur Login**:
   - URL: `/auth/login` atau `/login`
   - Validasi username & password menggunakan `password_verify` (Bcrypt) dengan fallback legacy.
   - Session aman (`user_id`, `username`, `nama`, `level`, `logged_in`).
   - Logout membersihkan session dan redirect kembali ke login.

2. **Menu Settings -> User**:
   - URL: `/settings/user` (atau `/settings/users`)
   - Tabel menampilkan daftar pengguna, level (`admin`/`user`), status aktif, dan daftar department yang dimiliki.
   - Tombol **+ Tambah User** & **Edit User**.
   - **Tombol Action "Atur Department"**:
     - Membuka modal yang menampilkan seluruh department dengan **Toggle Switch**.
     - Admin dapat langsung mengaktifkan atau menonaktifkan akses user ke suatu department (misal: mengaktifkan akses ke IT).
     - Perubahan disimpan otomatis via AJAX secara real-time ke tabel relasi `dbo.user_departments`.

3. **Menu Settings -> Department**:
   - URL: `/settings/department` (atau `/settings/departments`)
   - Pengelolaan data master department: Nama, Kode Singkatan, Deskripsi, dan jumlah user terdaftar.
   - Tambah, Edit, dan Hapus department.

4. **Proteksi Hak Akses (Role == admin)**:
   - Semua menu di dalam `Settings` (`/settings/*`) diproteksi ketat pada constructor controller `Settings.php`:
     - Jika belum login: diarahkan ke `/auth/login`.
     - Jika role `!= 'admin'`: HTTP 403 Forbidden (*"Akses Ditolak: Seluruh menu di dalam Settings hanya dapat diakses oleh Administrator"*).
   - Menu `Settings` di sidebar navigasi otomatis hanya ditampilkan kepada user dengan `level == 'admin'`.
