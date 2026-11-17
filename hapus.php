<?php
include 'koneksi.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$id      = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];
$role    = $_SESSION['role'];

$data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM pengaduan WHERE id=$id"));

if ($data) {
    // Admin bebas hapus; Siswa hanya bisa hapus miliknya jika status masih Menunggu
    if ($role === 'admin' || ($role === 'siswa' && $data['user_id'] == $user_id && $data['status'] === 'Menunggu')) {
        if ($data['foto'] && file_exists('uploads/' . $data['foto'])) {
            unlink('uploads/' . $data['foto']);
        }
        mysqli_query($conn, "DELETE FROM pengaduan WHERE id=$id");
    }
}
header("Location: dashboard.php");
exit;
?>