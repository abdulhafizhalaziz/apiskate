<?php

// =========================================
// ENABLE ERROR REPORTING (AGAR ERROR MUNCUL)
// =========================================
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

date_default_timezone_set('Asia/Jakarta');

$host       = 'localhost';
$user       = 'root';
$pass       = '';
$dbname     = 'db_snackinaja';

$koneksi    = mysqli_connect($host, $user, $pass, $dbname);

// OPTIONAL TEST CONNECTION
// if (!$koneksi) { die("Koneksi gagal: " . mysqli_connect_error()); }

$main_url   = 'http://localhost/snackinaja/';

?>
