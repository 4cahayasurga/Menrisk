# IFPRO Risk Command Center (Menrisk) — CodeIgniter 3 + SQL Server

Aplikasi **IFPRO Risk Command Center** dengan arsitektur penuh **CodeIgniter 3 MVC**, terintegrasi ke database **Microsoft SQL Server**, dilengkapi fitur **Otentikasi Login** dan **Role-Based Settings (User & Department)**.

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
