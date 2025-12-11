<?php
session_start();

if (!isset($_SESSION["ssLoginPOS"])) {
    header("location: ../auth/login.php");
    exit();
}

require "../config/config.php";
require "../config/functions.php";

$title = "Detail Pembelian - Snackinaja";
require "../template/header.php";
require "../template/navbar.php";
require "../template/sidebar.php";

$id = $_GET['id']; // no_beli
$tgl = $_GET['tgl']; // formatted date

// AMBIL DETAIL BARANG DENGAN JOIN
$pembelian = getData("
    SELECT d.*, b.nama_barang 
    FROM tbl_detail_beli d
    JOIN tbl_barang b ON d.id_barang = b.id_barang
    WHERE d.no_beli = '$id'
");
?>

<div class="content-wrapper">

    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">Detail Pembelian</h1>

            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="<?= $main_url ?>dashboard.php">Beranda</a></li>
                <li class="breadcrumb-item"><a href="<?= $main_url ?>laporan-pembelian">Laporan Pembelian</a></li>
                <li class="breadcrumb-item active">Detail</li>
            </ol>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">

            <div class="card">

                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-list fa-sm"></i> Rincian Barang</h3>

                    <button class="btn btn-sm btn-success float-right"><?= $tgl ?></button>
                    <button class="btn btn-sm btn-warning float-right mr-1"><?= $id ?></button>
                </div>

                <div class="card-body table-responsive p-3">

                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode Barang</th>
                                <th>Nama Barang</th>
                                <th class="text-right">Harga Beli</th>
                                <th class="text-center">Qty</th>
                                <th class="text-right">Jumlah Harga</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php
                            $no = 1;
                            foreach ($pembelian as $row) { ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= $row['id_barang'] ?></td>
                                    <td><?= $row['nama_barang'] ?></td>
                                    <td class="text-right"><?= number_format($row['harga_beli'], 0, ',', '.') ?></td>
                                    <td class="text-center"><?= $row['qty'] ?></td>
                                    <td class="text-right"><?= number_format($row['jml_harga'], 0, ',', '.') ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>

                    </table>

                </div>

            </div>

        </div>
    </section>

</div>

<?php require "../template/footer.php"; ?>
