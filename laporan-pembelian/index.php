<?php
session_start();

if (!isset($_SESSION["ssLoginPOS"])) {
    header("location: ../auth/login.php");
    exit();
}

require "../config/config.php";
require "../config/functions.php";

$title = "Laporan Pembelian - snackinaja";
require "../template/header.php";
require "../template/navbar.php";
require "../template/sidebar.php";

/*
 |----------------------------------------------
 | AMBIL DATA PEMBELIAN
 |----------------------------------------------
 | ORDER BY p.tgl_beli ASC  -> PB0001 di atas, PB0004 di bawah
 | Kalau mau terbaru di atas, ganti ASC jadi DESC
 */
$pembelian = getData("
    SELECT p.*, s.nama AS supplier_nama
    FROM tbl_pembelian p
    JOIN tbl_supplier s ON s.id_supplier = p.id_supplier
    ORDER BY p.tgl_beli ASC, p.no_beli ASC
");
?>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Laporan Pembelian</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="<?= $main_url ?>dashboard.php">Beranda</a>
                        </li>
                        <li class="breadcrumb-item active">Pembelian</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-list fa-sm"></i> Data Pembelian
                    </h3>
                    <button type="button"
                            class="btn btn-sm btn-outline-primary float-right"
                            data-toggle="modal"
                            data-target="#mdlPeriodeBeli">
                        <i class="fas fa-print"></i> Cetak
                    </button>
                </div>

                <div class="card-body table-responsive p-3">
                    <table class="table table-hover text-nowrap" id="tblData">
                        <thead>
                        <tr>
                            <th style="width: 5%;">No</th>
                            <th style="width: 15%;">No Pembelian</th>
                            <th style="width: 15%;">Tgl Pembelian</th>
                            <th class="text-center" style="width: 25%;">Supplier</th>
                            <th class="text-center" style="width: 20%;">Total Pembelian</th>
                            <th class="text-center" style="width: 10%;">Opsi</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $no = 1;
                        foreach ($pembelian as $beli) { ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $beli['no_beli'] ?></td>
                                <td><?= date('d-m-Y', strtotime($beli['tgl_beli'])) ?></td>
                                <td class="text-center"><?= $beli['supplier_nama'] ?></td>
                                <td class="text-center">
                                    <?= number_format($beli['total'], 0, ',', '.') ?>
                                </td>
                                <td class="text-center">
                                    <a href="detail-pembelian.php?id=<?= $beli['no_beli'] ?>&tgl=<?= $beli['tgl_beli'] ?>"
                                       class="btn btn-sm btn-info"
                                       title="Rincian Barang">
                                        Detail
                                    </a>
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

<!-- MODAL CETAK PERIODE -->
<div class="modal fade" id="mdlPeriodeBeli">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">Periode Pembelian</h4>
                <button type="button" class="close" data-dismiss="modal"
                        aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">

                <div class="form-group row">
                    <label for="tgl1" class="col-sm-3 col-form-label">Tanggal Awal</label>
                    <div class="col-sm-9">
                        <input type="date" class="form-control" id="tgl1">
                    </div>
                </div>

                <div class="form-group row">
                    <label for="tgl2" class="col-sm-3 col-form-label">Tanggal Akhir</label>
                    <div class="col-sm-9">
                        <input type="date" class="form-control" id="tgl2">
                    </div>
                </div>

            </div>

            <div class="modal-footer">
                <button type="button"
                        class="btn btn-primary"
                        onclick="printDoc()">
                    <i class="fas fa-print"></i> Cetak
                </button>
            </div>

        </div>
    </div>
</div>

<script>
    function printDoc() {
        let tgl1 = document.getElementById('tgl1').value;
        let tgl2 = document.getElementById('tgl2').value;

        if (tgl1 !== "" && tgl2 !== "") {
            window.open(
                "../report/r-beli.php?tgl1=" + tgl1 + "&tgl2=" + tgl2,
                "_blank",
                "width=900,height=600,left=100"
            );
        } else {
            alert("Tanggal awal dan akhir harus diisi!");
        }
    }
</script>

<?php require "../template/footer.php"; ?>
