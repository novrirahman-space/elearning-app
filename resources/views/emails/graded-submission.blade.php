<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Tugas Dinilai</title>
</head>
<body>
    <h2>Hai {{ $studentName }},</h2>
    <p>Tugas kamu pada mata kuliah <strong>{{ $courseName }}</strong> telah dinilai.</p>
    <p><strong>Judul Tugas:</strong> {{ $assignmentTitle }}</p>
    <p><strong>Nilai:</strong> {{ $score }}</p>
    <hr>
    <p>Terus semangat belajar!</p>
</body>
</html>
