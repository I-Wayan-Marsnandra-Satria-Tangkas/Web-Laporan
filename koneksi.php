<?php
session_start();
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_pengaduan"; // Pastikan ini 'db_pengaduan', BUKAN 'db_pengaduan_sekolah'

$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>