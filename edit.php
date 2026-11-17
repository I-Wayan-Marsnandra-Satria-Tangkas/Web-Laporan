<?php
include 'koneksi.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$id   = (int)$_GET['id'];
$role = $_SESSION['role'];
$data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM pengaduan WHERE id=$id"));

if (!$data) {
    header("Location: dashboard.php");
    exit;
}

// Validasi Hak Akses
if ($role === 'siswa' && ($data['user_id'] != $_SESSION['user_id'] || $data['status'] !== 'Menunggu')) {
    header("Location: dashboard.php");
    exit;
}

if (isset($_POST['update'])) {
    if ($role === 'siswa') {
        $judul    = mysqli_real_escape_string($conn, $_POST['judul']);
        $kategori = mysqli_real_escape_string($conn, $_POST['kategori']);
        $isi      = mysqli_real_escape_string($conn, $_POST['isi']);
        mysqli_query($conn, "UPDATE pengaduan SET judul='$judul', kategori='$kategori', isi='$isi' WHERE id=$id");
    } else {
        $status    = mysqli_real_escape_string($conn, $_POST['status']);
        $tanggapan = mysqli_real_escape_string($conn, $_POST['tanggapan']);
        mysqli_query($conn, "UPDATE pengaduan SET status='$status', tanggapan='$tanggapan' WHERE id=$id");
    }
    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Update Pengaduan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light py-4">
<div class="container col-md-6">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4><?= $role === 'admin' ? 'Tindak Lanjut Pengaduan' : 'Edit Pengaduan' ?></h4>
            <form method="POST">
                <?php if ($role === 'siswa'): ?>
                    <div class="mb-3">
                        <label>Judul Laporan</label>
                        <input type="text" name="judul" class="form-control" value="<?= htmlspecialchars($data['judul']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label>Kategori</label>
                        <select name="kategori" class="form-select">
                            <?php foreach(['Fasilitas','Kebersihan','Keamanan','Lainnya'] as $kat): ?>
                                <option value="<?= $kat ?>" <?= $data['kategori'] == $kat ? 'selected' : '' ?>><?= $kat ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Isi Laporan</label>
                        <textarea name="isi" rows="4" class="form-control" required><?= htmlspecialchars($data['isi']) ?></textarea>
                    </div>
                <?php else: ?>
                    <div class="mb-3">
                        <label>Judul:</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($data['judul']) ?>" disabled>
                    </div>
                    <div class="mb-3">
                        <label>Status Pengaduan</label>
                        <select name="status" class="form-select">
                            <option value="Menunggu" <?= $data['status'] == 'Menunggu' ? 'selected' : '' ?>>Menunggu</option>
                            <option value="Diproses" <?= $data['status'] == 'Diproses' ? 'selected' : '' ?>>Diproses</option>
                            <option value="Selesai" <?= $data['status'] == 'Selesai' ? 'selected' : '' ?>>Selesai</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Tanggapan Admin</label>
                        <textarea name="tanggapan" rows="4" class="form-control" placeholder="Tulis tindak lanjut atau keterangan..."><?= htmlspecialchars($data['tanggapan'] ?? '') ?></textarea>
                    </div>
                <?php endif; ?>
                <button type="submit" name="update" class="btn btn-primary">Simpan Perubahan</button>
                <a href="dashboard.php" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>