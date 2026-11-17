<?php
require 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$session_user_id = (int) $_SESSION['user_id'];
$session_nama    = $_SESSION['nama'];
$session_role    = $_SESSION['role'];

// Pencarian & Filter
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, trim($_GET['search'])) : '';
$status = isset($_GET['status']) ? mysqli_real_escape_string($conn, trim($_GET['status'])) : '';

$where = [];
if ($session_role === 'siswa') {
    $where[] = "p.user_id = $session_user_id";
}
if ($search !== '') {
    $where[] = "(p.judul LIKE '%$search%' OR p.isi LIKE '%$search%' OR p.kategori LIKE '%$search%')";
}
if ($status !== '') {
    $where[] = "p.status = '$status'";
}

$where_sql = count($where) > 0 ? "WHERE " . implode(" AND ", $where) : "";
$query = "SELECT p.*, u.nama AS pelapor 
          FROM pengaduan p 
          JOIN users u ON p.user_id = u.id 
          $where_sql 
          ORDER BY p.id DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistem Pengaduan Sekolah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .tanggapan-box {
            background-color: #f1f3f5;
            border-left: 4px solid #198754;
            padding: 8px 12px;
            margin-top: 8px;
            border-radius: 4px;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

<!-- Navbar Biru -->
<nav class="navbar navbar-dark bg-primary py-3 mb-4 shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-semibold fs-4" href="dashboard.php">
            Sistem Pengaduan Sekolah (<?= strtoupper($session_role) ?>)
        </a>
        <div class="d-flex align-items-center text-white gap-3">
            <span>Halo, <strong><?= htmlspecialchars($session_nama) ?></strong> |</span>
            <a href="logout.php" class="btn btn-danger btn-sm px-3">Logout</a>
        </div>
    </div>
</nav>

<div class="container">
    <!-- Baris Judul & Tombol Buat Pengaduan (Khusus Siswa) -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Daftar Laporan Pengaduan</h3>
        <?php if ($session_role === 'siswa'): ?>
            <a href="tambah.php" class="btn btn-success px-3 py-2">+ Buat Pengaduan</a>
        <?php endif; ?>
    </div>

    <!-- Form Pencarian & Filter -->
    <form method="GET" action="dashboard.php" class="row g-2 mb-4">
        <div class="col-md-5">
            <input type="text" name="search" class="form-control" placeholder="Cari judul atau isi laporan..." value="<?= htmlspecialchars($search) ?>">
        </div>
        <div class="col-md-4">
            <select name="status" class="form-select">
                <option value="">-- Semua Status --</option>
                <option value="Menunggu" <?= $status === 'Menunggu' ? 'selected' : '' ?>>Menunggu</option>
                <option value="Diproses" <?= $status === 'Diproses' ? 'selected' : '' ?>>Diproses</option>
                <option value="Selesai" <?= $status === 'Selesai' ? 'selected' : '' ?>>Selesai</option>
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary px-3">Filter / Cari</button>
            <a href="dashboard.php" class="btn btn-secondary px-3">Reset</a>
        </div>
    </form>

    <!-- Tabel Data Pengaduan -->
    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-bordered align-middle mb-0 bg-white">
                <thead class="table-light">
                    <tr>
                        <th width="5%">No</th>
                        <th width="12%">Tanggal</th>
                        <?php if ($session_role === 'admin'): ?>
                            <th width="14%">Pelapor</th>
                        <?php endif; ?>
                        <th width="16%">Judul & Kategori</th>
                        <th>Isi & Tanggapan</th>
                        <th width="11%">Bukti</th>
                        <th width="10%">Status</th>
                        <th width="18%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($result && mysqli_num_rows($result) > 0): ?>
                    <?php $no = 1; while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($row['tanggal']) ?></td>
                            
                            <?php if ($session_role === 'admin'): ?>
                                <td><?= htmlspecialchars($row['pelapor']) ?></td>
                            <?php endif; ?>
                            
                            <td>
                                <div class="fw-bold mb-1"><?= htmlspecialchars($row['judul']) ?></div>
                                <span class="badge bg-info text-dark"><?= htmlspecialchars($row['kategori']) ?></span>
                            </td>
                            
                            <td>
                                <div><?= nl2br(htmlspecialchars($row['isi'])) ?></div>
                                <?php if (!empty($row['tanggapan'])): ?>
                                    <div class="tanggapan-box">
                                        <strong>Tanggapan Admin:</strong> <?= nl2br(htmlspecialchars($row['tanggapan'])) ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            
                            <td>
                                <?php if (!empty($row['foto'])): ?>
                                    <a href="uploads/<?= htmlspecialchars($row['foto']) ?>" target="_blank" class="btn btn-outline-secondary btn-sm">
                                        Lihat Foto
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted small">-</span>
                                <?php endif; ?>
                            </td>
                            
                            <td>
                                <?php
                                $badge = 'secondary';
                                if ($row['status'] === 'Menunggu') $badge = 'warning text-dark';
                                elseif ($row['status'] === 'Diproses') $badge = 'info text-dark';
                                elseif ($row['status'] === 'Selesai') $badge = 'success';
                                ?>
                                <span class="badge bg-<?= $badge ?> px-2 py-1"><?= htmlspecialchars($row['status']) ?></span>
                            </td>
                            
                            <td>
                                <div class="d-flex flex-wrap gap-1 align-items-center">
                                    <!-- Tombol Cetak Struk untuk Admin & Siswa -->
                                    <a href="cetak_struk.php?id=<?= $row['id'] ?>" target="_blank" class="btn btn-secondary btn-sm">Struk</a>

                                    <?php if ($session_role === 'admin'): ?>
                                        <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Proses</a>
                                        <a href="hapus.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus laporan ini?')">Hapus</a>
                                    <?php elseif ($session_role === 'siswa' && $row['status'] === 'Menunggu'): ?>
                                        <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                        <a href="hapus.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus laporan ini?')">Hapus</a>
                                    <?php else: ?>
                                        <span class="text-muted small ms-1">Terkunci</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="<?= $session_role === 'admin' ? 8 : 7 ?>" class="text-center text-muted py-4">
                            Belum ada data pengaduan.
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>