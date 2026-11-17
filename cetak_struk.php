<?php
require 'koneksi.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$id      = (int) $_GET['id'];
$user_id = (int) $_SESSION['user_id'];
$role    = $_SESSION['role'];

// Admin bisa mencetak semua laporan; Siswa hanya bisa mencetak miliknya sendiri
$where_role = ($role === 'admin') ? "p.id = $id" : "p.id = $id AND p.user_id = $user_id";
$query = "SELECT p.*, u.nama AS pelapor, u.username 
          FROM pengaduan p 
          JOIN users u ON p.user_id = u.id 
          WHERE $where_role";
$data = mysqli_fetch_assoc(mysqli_query($conn, $query));

if (!$data) {
    die("Data laporan tidak ditemukan atau Anda tidak memiliki akses.");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk Pengaduan #<?= str_pad($data['id'], 4, '0', STR_PAD_LEFT) ?></title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            background-color: #f4f4f4;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
            margin: 0;
        }
        .struk {
            background: #fff;
            width: 320px;
            padding: 20px;
            border: 1px dashed #333;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            font-size: 13px;
            color: #000;
        }
        .header {
            text-align: center;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
            margin-bottom: 12px;
        }
        .header h3 {
            margin: 0 0 4px 0;
            font-size: 15px;
        }
        .header small {
            font-size: 11px;
        }
        .row-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        .section {
            border-top: 1px dashed #000;
            padding-top: 10px;
            margin-top: 10px;
        }
        .label {
            font-weight: bold;
            display: block;
            margin-bottom: 3px;
        }
        .status-box {
            text-align: center;
            border: 1px solid #000;
            padding: 5px;
            font-weight: bold;
            margin: 12px 0;
            text-transform: uppercase;
        }
        .footer {
            text-align: center;
            border-top: 1px dashed #000;
            padding-top: 10px;
            margin-top: 12px;
            font-size: 11px;
        }
        .actions {
            margin-top: 15px;
            display: flex;
            gap: 10px;
        }
        .btn {
            padding: 8px 14px;
            border: none;
            cursor: pointer;
            font-family: sans-serif;
            font-size: 13px;
            border-radius: 4px;
            text-decoration: none;
        }
        .btn-print { background: #0d6efd; color: #fff; }
        .btn-back  { background: #6c757d; color: #fff; }

        @media print {
            body {
                background: #fff;
                padding: 0;
            }
            .struk {
                box-shadow: none;
                border: none;
                width: 100%;
                max-width: 80mm;
                padding: 5px;
            }
            .actions {
                display: none;
            }
        }
    </style>
</head>
<body onload="window.print()">

<div class="struk">
    <div class="header">
        <h3>BUKTI PENGADUAN SEKOLAH</h3>
        <small>Layanan Aspirasi & Sarana Prasarana</small>
    </div>

    <div class="row-info">
        <span>No. Tiket</span>
        <span><strong>#<?= str_pad($data['id'], 4, '0', STR_PAD_LEFT) ?></strong></span>
    </div>
    <div class="row-info">
        <span>Tanggal</span>
        <span><?= date('d/m/Y', strtotime($data['tanggal'])) ?></span>
    </div>
    <div class="row-info">
        <span>Pelapor</span>
        <span><?= htmlspecialchars($data['pelapor']) ?></span>
    </div>
    <div class="row-info">
        <span>Kategori</span>
        <span><?= htmlspecialchars($data['kategori']) ?></span>
    </div>

    <div class="section">
        <span class="label">Judul Laporan:</span>
        <div><?= htmlspecialchars($data['judul']) ?></div>
    </div>

    <div class="section">
        <span class="label">Detail Laporan:</span>
        <div><?= nl2br(htmlspecialchars($data['isi'])) ?></div>
    </div>

    <div class="status-box">
        STATUS: <?= htmlspecialchars($data['status']) ?>
    </div>

    <?php if (!empty($data['tanggapan'])): ?>
    <div class="section">
        <span class="label">Tanggapan Admin:</span>
        <div><?= nl2br(htmlspecialchars($data['tanggapan'])) ?></div>
    </div>
    <?php endif; ?>

    <div class="footer">
        Dicetak pada: <?= date('d/m/Y H:i') ?><br>
        Simpan struk ini sebagai bukti pelaporan.
    </div>
</div>

<div class="actions">
    <button onclick="window.print()" class="btn btn-print">Cetak / Simpan PDF</button>
    <a href="dashboard.php" class="btn btn-back">Kembali</a>
</div>

</body>
</html>