<?php
include 'koneksi.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'siswa') {
    header("Location: dashboard.php");
    exit;
}

if (isset($_POST['submit'])) {
    $user_id  = $_SESSION['user_id'];
    $judul    = mysqli_real_escape_string($conn, $_POST['judul']);
    $kategori = mysqli_real_escape_string($conn, $_POST['kategori']);
    $isi      = mysqli_real_escape_string($conn, $_POST['isi']);
    $tanggal  = date('Y-m-d');
    $foto     = "";

    // Upload Foto Bukti (Opsional)
    if (!empty($_FILES['foto']['name'])) {
        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $foto = time() . '_' . rand(100,999) . '.' . $ext;
        move_uploaded_file($_FILES['foto']['tmp_name'], 'uploads/' . $foto);
    }

    mysqli_query($conn, "INSERT INTO pengaduan (user_id, judul, kategori, isi, foto, tanggal) 
                         VALUES ('$user_id', '$judul', '$kategori', '$isi', '$foto', '$tanggal')");
    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buat Pengaduan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light py-4">
<div class="container col-md-6">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4>Form Pengaduan Baru</h4>
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label>Judul Laporan</label>
                    <input type="text" name="judul" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Kategori</label>
                    <select name="kategori" class="form-select" required>
                        <option value="Fasilitas">Fasilitas</option>
                        <option value="Kebersihan">Kebersihan</option>
                        <option value="Keamanan">Keamanan</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Isi Laporan</label>
                    <textarea name="isi" rows="4" class="form-control" required></textarea>
                </div>
                <div class="mb-3">
                    <label>Foto Bukti (Opsional)</label>
                    <input type="file" name="foto" class="form-control" accept="image/*">
                </div>
                <button type="submit" name="submit" class="btn btn-primary">Kirim Laporan</button>
                <a href="dashboard.php" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>