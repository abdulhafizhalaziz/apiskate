<?php
session_start();

if (!isset($_SESSION["ssLoginPOS"])) {
    header("location: ../auth/login.php");
    exit();
}

require "../config/config.php";
require "../config/functions.php";

$nota = $_GET['nota'];

// Ambil data header transaksi
$dataJual = getData("SELECT * FROM tbl_penjualan WHERE no_jual = '$nota'")[0];

// Ambil semua item transaksi
$itemJual = getData("
    SELECT d.*, b.satuan 
    FROM tbl_detail_jual d
    LEFT JOIN tbl_barang b ON d.id_barang = b.id_barang
    WHERE d.no_jual = '$nota'
");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Struk Belanja</title>

    <style>
        body {
            font-family: monospace;
            font-size: 13px;
        }
        .center {
            text-align: center;
        }
        table {
            width: 240px;
        }
        hr {
            border: none;
            border-top: 1px dashed black;
            margin: 5px 0;
        }
    </style>
</head>

<body>

    <!-- HEADER TOKO -->
    <table class="center">
        <tr><td><b>Snackinaja POS</b></td></tr>
        <tr><td>No Nota : <?= $nota ?></td></tr>
        <tr><td><?= date('d-m-Y H:i:s') ?></td></tr>
        <tr><td>Kasir : <?= userLogin()['username'] ?></td></tr>
    </table>

    <hr>

    <!-- DETAIL ITEM -->
    <table>
        <?php foreach ($itemJual as $item) { ?>
            <tr>
                <td colspan="3"><?= $item['nama_brg'] ?></td>
            </tr>

            <tr>
                <td><?= $item['qty'] ?> <?= $item['satuan'] ?></td>
                <td style="text-align:right;">x <?= number_format($item['harga_jual'], 0, ',', '.') ?></td>
                <td style="text-align:right;"><?= number_format($item['jml_harga'], 0, ',', '.') ?></td>
            </tr>

            <tr><td colspan="3"><hr></td></tr>
        <?php } ?>
    </table>

    <!-- TOTAL BAYAR -->
    <table>
        <tr>
            <td>Total</td>
            <td style="text-align:right;"><b><?= number_format($dataJual['total'], 0, ',', '.') ?></b></td>
        </tr>
        <tr>
            <td>Bayar</td>
            <td style="text-align:right;"><b><?= number_format($dataJual['bayar'], 0, ',', '.') ?></b></td>
        </tr>
        <tr>
            <td>Kembali</td>
            <td style="text-align:right;"><b><?= number_format($dataJual['kembalian'], 0, ',', '.') ?></b></td>
        </tr>
    </table>

    <hr>

    <!-- FOOTER -->
    <div class="center">
        Terima Kasih sudah berbelanja<br>
        ~ Snackinaja ~
    </div>

    <script>
        // Auto print setelah 0.5 detik
        setTimeout(function(){
            window.print();
        }, 500);
    </script>

</body>
</html>
