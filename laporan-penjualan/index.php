<?php
session_start();

if (!isset($_SESSION["ssLoginPOS"])) {
    header("location: ../auth/login.php");
    exit();
}

require "../config/config.php";
require "../config/functions.php";

$title = "Laporan - snackinaja";
require "../template/header.php";
require "../template/navbar.php";
require "../template/sidebar.php";

$penjualan = getData("
    SELECT p.*, c.nama 
    FROM tbl_penjualan p
    LEFT JOIN tbl_customer c ON p.id_customer = c.id_customer
    ORDER BY p.tgl_jual DESC
");
?>
<div class="content-wrapper">

    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">Laporan Penjualan</h1>
        </div>
    </div>

<section class="content">
<div class="container-fluid">

<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-list"></i> Data Penjualan</h3>

        <button type="button" class="btn btn-sm btn-outline-primary float-right"
                data-toggle="modal" data-target="#mdlPeriode">
            <i class="fas fa-print"></i> Cetak
        </button>
    </div>

    <div class="card-body table-responsive">
        <table class="table table-hover" id="tblData">
            <thead>
                <tr>
                    <th>No</th>
                    <th>No Penjualan</th>
                    <th>Tanggal</th>
                    <th class="text-center">Pelanggan</th>
                    <th class="text-center">Total</th>
                    <th class="text-center">Detail</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach ($penjualan as $p) { ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $p['no_jual'] ?></td>
                    <td><?= in_date($p['tgl_jual']) ?></td>
                    <td class="text-center"><?= $p['nama'] ?></td>
                    <td class="text-center"><?= number_format($p['total'], 0, ',', '.') ?></td>
                    <td class="text-center">
                        <a href="detail-penjualan.php?id=<?= $p['no_jual'] ?>&tgl=<?= in_date($p['tgl_jual']) ?>" 
                           class="btn btn-info btn-sm">Detail</a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

</div>
</section>
</div>

<!-- Modal Cetak -->
<div class="modal fade" id="mdlPeriode">
<div class="modal-dialog">
<div class="modal-content">

    <div class="modal-header">
        <h4 class="modal-title">Periode Penjualan</h4>
        <button class="close" data-dismiss="modal">&times;</button>
    </div>

    <div class="modal-body">
        <div class="form-group row">
            <label class="col-sm-3 col-form-label">Tanggal Awal</label>
            <div class="col-sm-9">
                <input type="date" id="tgl1" class="form-control">
            </div>
        </div>

        <div class="form-group row">
            <label class="col-sm-3 col-form-label">Tanggal Akhir</label>
            <div class="col-sm-9">
                <input type="date" id="tgl2" class="form-control">
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button class="btn btn-primary" onclick="printDoc()">
            <i class="fas fa-print"></i> Cetak
        </button>
    </div>

</div>
</div>
</div>

<script>
function printDoc() {
    let t1 = document.getElementById('tgl1').value;
    let t2 = document.getElementById('tgl2').value;

    if (t1 !== "" && t2 !== "") {
        window.open("../report/r-jual.php?tgl1=" + t1 + "&tgl2=" + t2,
            "_blank", "width=900,height=600,left=200");
    }
}
</script>

<?php require "../template/footer.php"; ?>
