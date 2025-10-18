<?php

session_start();

if (!isset($_SESSION["ssLoginPOS"])) {
    header("location: ../auth/login.php");
    exit();
}

require "../config/config.php";
require "../config/functions.php";
require "../module/mode-barang.php";

$title = "Laporan - snackinaja";
require "../template/header.php";
require "../template/navbar.php";
require "../template/sidebar.php";

$penjualan = getData("SELECT t.*, r.nama AS relasi_nama FROM tbl_transaksi t JOIN tbl_relasi r ON r.id_relasi = t.id_relasi WHERE t.tipe_transaksi = 'JUAL'");

?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Laporan Penjualan</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= $main_url ?>dashboard.php">Beranda</a></li>
                        <li class="breadcrumb-item active">Penjualan</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <!-- <?php if ($alert != '') {
                    echo $alert;
                } ?> -->
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-list fa-sm"></i> Data Penjualan</h3>
                    <button type="button" class="btn btn-sm btn-outline-primary float-right" data-toggle="modal" data-target="#mdlPeriodeBeli"><i class="fas fa-print"></i> Cetak</button>
                </div>
                <div class="card-body table-responsive p-3">
                    <table class="table table-hover text-nowrap" id="tblData">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>No Penjualan</th>
                                <th>Tgl Penjualan</th>
                                <th class="text-center">Pelanggan</th>
                                <th class="text-center">Total Penjualan</th>
                                <th style="width: 10%;" class="text-center">Opsi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            foreach ($penjualan as $jual) { ?>
                                <tr>
                                    <td>
                                        <?= $no++ ?>
                                    </td>
                                    <td><?= $jual['no_transaksi'] ?></td>
                                    <td><?= in_date($jual['tgl_transaksi']) ?></td>
                                    <td class="text-center"><?= $jual['relasi_nama'] ?></td>
                                    <td class="text-center"><?= number_format($jual['total'], 0, ',', '.') ?></td>
                                    <td class="text-center">
                                        <a href="detail-penjualan.php?id=<?=$jual['no_transaksi']?>&tgl=<?=in_date($jual['tgl_transaksi'])?>" class="btn btn-sm btn-info" title="Rincian Barang">Detail</a>
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>
<div class="modal fade" id="mdlPeriodeBeli">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Periode Penjualan</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
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
                <button type="button" class="btn btn-primary" onclick="printDoc()" ><i class="fas fa-print"></i>Cetak</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<script>
    let tgl1 = document.getElementById('tgl1');
    let tgl2 = document.getElementById('tgl2');
    function printDoc() {
        if (tgl1.value != "" && tgl2.value != "") {
            window.open("../report/r-jual.php?tgl1=" + tgl1.value + "&tgl2=" + tgl2.value, '_blank', 'width=900,height=600,left=100');
        }
    }
</script>
    <?php require "../template/footer.php"; ?>