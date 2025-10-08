<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Tugas Baru</title>
</head>
<body>
    <h2>Tugas Baru di Mata Kuliah {{ $courseName }}</h2>
    <p><strong>Judul:</strong> {{ $title }}</p>
    <p><strong>Deskripsi:</strong> {{ $description }}</p>
    <p><strong>Batas Waktu:</strong> {{ \Carbon\Carbon::parse($deadline)->format('d M Y H:i') }}</p>
    <hr>
    <p>Silakan buka aplikasi E-Learning untuk mengumpulkan tugas Anda.</p>
</body>
</html>
