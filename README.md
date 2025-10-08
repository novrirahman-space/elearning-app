# 📚 E-Learning API — Laravel 12 Project Test

**E-Learning API** adalah aplikasi backend yang dibangun menggunakan **Laravel 12** sebagai bagian dari tes teknikal programmer.  
Project ini dirancang untuk mendemonstrasikan kemampuan membangun REST API yang terstruktur, aman, dan siap dikembangkan ke sistem pembelajaran daring (e-learning).

---

## 🚀 Fitur Utama

### 🔐 1. Autentikasi & Role
- Registrasi dan login menggunakan **Laravel Sanctum**  
- Role otomatis:
  - `teacher` → jika diisi saat registrasi  
  - `student` → default jika tidak diisi
- Endpoint:
  - `POST /api/register`
  - `POST /api/login`
  - `POST /api/logout`
  - `GET /api/me`

---

### 📘 2. Manajemen Mata Kuliah
- Dosen dapat menambahkan, mengedit, dan menghapus mata kuliah  
- Mahasiswa dapat melihat daftar dan *enroll* ke mata kuliah  
- Relasi:
  - Dosen **hasMany** Course  
  - Mahasiswa **belongsToMany** Course  
- Endpoint:  
  - `GET /api/courses`  
  - `POST /api/courses`  
  - `PUT /api/courses/{id}`  
  - `DELETE /api/courses/{id}` *(Soft Delete — lihat di bawah)*  
  - `POST /api/courses/{id}/enroll`

---

### 🔹 2.1 Soft Deletes (Penghapusan Aman)
- Implementasi **Eloquent SoftDeletes** pada model `Course`  
- Saat dosen menghapus mata kuliah, data **tidak dihapus permanen** dari database  
- Kolom `deleted_at` diisi otomatis oleh Laravel  
- Data dapat dipulihkan jika dibutuhkan  
- Keuntungan:
  - Aman terhadap kehilangan data
  - Cocok untuk audit log & riwayat kursus
- Endpoint terkait:
  - `DELETE /api/courses/{id}` → menandai course sebagai dihapus
  - (opsional) `GET /api/courses/trashed` → menampilkan course yang sudah dihapus (jika diaktifkan)

---

### 📂 3. Upload & Download Materi
- Dosen dapat mengunggah file materi  
- Mahasiswa dapat mengunduhnya  
- Disimpan menggunakan Laravel Storage  
- Endpoint:  
  - `POST /api/materials`  
  - `GET /api/materials/{id}/download`

---

### 📝 4. Tugas & Penilaian
- Dosen membuat tugas dengan deadline  
- Mahasiswa mengunggah jawaban  
- Dosen memberi nilai  
- Endpoint:  
  - `POST /api/assignments`  
  - `POST /api/submissions`  
  - `POST /api/submissions/{id}/grade`

---

### 💬 5. Forum Diskusi
- Dosen & Mahasiswa bisa membuat topik dan balasan diskusi  
- Real-time update menggunakan **Laravel Reverb (WebSocket)**  
- Event: `DiscussionCreated` dan `ReplyCreated`  
- Frontend listener: `forum.js` menggunakan **Laravel Echo + Pusher protocol**
- Endpoint:  
  - `POST /api/discussions`  
  - `POST /api/discussions/{id}/replies`  
  - Frontend live page: `/forum/{course}`

---

### 📈 6. Laporan & Statistik
- Statistik jumlah mahasiswa per mata kuliah  
- Statistik tugas yang sudah/belum dinilai  
- Rata-rata nilai mahasiswa  
- Menggunakan **Eloquent Aggregates (count, sum, avg)**  
- Endpoint:  
  - `GET /api/reports/courses`  
  - `GET /api/reports/assignments`  
  - `GET /api/reports/students/{id}`

---

### 📧 7. Notifikasi Email
- Menggunakan **Mailtrap (sandbox)** untuk pengujian email  
- Email dikirim otomatis:
  - Saat dosen membuat tugas baru  
  - Saat dosen memberi nilai  
- Mail menggunakan class `NewAssignmentNotification` dan `GradedSubmissionNotification`

---

### ⚡ 8. Real-Time Forum (Laravel Reverb)
- Implementasi **Laravel Reverb**, pengganti BeyondCode WebSockets  
- Private channel `course.{id}` untuk setiap mata kuliah  
- Token-based auth via `localStorage`  
- Tes real-time dilakukan via:
  - Postman (HTTP API)
  - Safari browser (client listener)
- Semua event tampil live tanpa reload

---

## 🧰 Teknologi yang Digunakan

| Komponen | Teknologi |
|-----------|------------|
| Framework | Laravel 12 |
| Database | MySQL |
| Auth | Laravel Sanctum |
| Realtime | Laravel Reverb + Echo |
| SoftDeletes | Eloquent Trait `Illuminate\Database\Eloquent\SoftDeletes` |
| Testing | Postman & manual verification |
| File Storage | Laravel Storage (local) |
| Email | Mailtrap SMTP |
| Frontend minimal | Blade + Vite |
