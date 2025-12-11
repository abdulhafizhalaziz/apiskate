<?php
session_start();
if (!isset($_SESSION["ssLoginPOS"])) {
    header("location: ../auth/login.php");
    exit();
}

require "../config/config.php";
require "../config/functions.php";

$id = $_GET['id'];
$tgl = $_GET['tgl'];

$detail = getData("
    SELECT * FROM tbl_detail_jual 
    WHERE no_jual = '$id'
");
?>
<div class="content-wrapper">

<div class="content-header">
    <div class="container-fluid">
        <h1 class="m-0">Detail Penjualan</h1>
    </div>
</div>

<section class="content">
<div class="container-fluid">

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Rincian Barang</h3>
        <button class="btn btn-warning btn-sm float-right ml-1"><?= $id ?></button>
        <button class="btn btn-success btn-sm float-right"><?= $tgl ?></button>
    </div>

    <div class="card-body table-responsive">
        <table class="table table-hover text-nowrap">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Barcode</th>
                    <th>Nama Barang</th>
                    <th>Harga</th>
                    <th class="text-center">Qty</th>
                    <th class="text-center">Subtotal</th>
                </tr>
            </thead>

            <tbody>
            <?php $no = 1; foreach ($detail as $d) { ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $d['barcode'] ?></td>
                    <td><?= $d['nama_brg'] ?></td>
                    <td><?= number_format($d['harga_jual'], 0, ',', '.') ?></td>
                    <td class="text-center"><?= $d['qty'] ?></td>
                    <td class="text-center"><?= number_format($d['jml_harga'], 0, ',', '.') ?></td>
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
