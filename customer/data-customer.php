<?php

session_start();

if (!isset($_SESSION["ssLoginPOS"])) {
    header("location: ../auth/login.php");
    exit();
}

require "../config/config.php";
require "../config/functions.php";
require "../module/mode-customer.php";

$title  = "Data Customer - snackinaja";
require "../template/header.php";
require "../template/navbar.php";
require "../template/sidebar.php";

if (isset($_GET['msg'])) {
    $msg = $_GET['msg'];
} else {
    $msg = '';
}

$alert = '';
if ($msg == 'deleted') {
    $alert = '<div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h5><i class="icon fas fa-check"></i> Alert!</h5>
                Supplier berhasil dihapus..
            </div>';
}

if ($msg == 'aborted') {
    $alert = '<div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h5><i class="icon fas fa-exclamation-triangle"></i> Alert!</h5>
                Customer gagal dihapus..
            </div>';
}

if ($msg == 'updated') {
    $alert = '<div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h5><i class="icon fas fa-check circle"></i> Alert!</h5>
                Customer berhasil diperbaharui..
            </div>';
}

?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">Customer</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?= $main_url ?>dashboard.php">Home</a></li>
            <li class="breadcrumb-item active">Data Customer</li>
            </ol>
        </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
    </div>

<section>
    <div class="container-fluid">
        <div class="card">
            <?php if ($alert !='') {
                echo $alert;
            } ?>
            <div class="card-header clearfix">
                <h3 class="card-title float-left mb-0">
                    <i class="fas fa-list fa-sm"></i> Data Customer
                </h3>
                <a href="<?= $main_url ?>customer/add-customer.php"
                class="btn btn-sm btn-primary float-right">
                    <i class="fas fa-plus fa-sm"></i> Add Customer
                </a>
            </div>
            <div class="card-body table-responsive p-3">
                <table class="table table-hover text-nowrap" id="tblData">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Telpon</th>
                            <th>Alamat</th>
                            <th>Deskripsi</th>
                            <th style="width: 10%;">Operasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        $customers = getData("SELECT * FROM tbl_relasi WHERE tipe = 'CUSTOMER' ORDER BY nama ASC");
                        foreach ($customers as $customer) :
                        ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $customer['nama'] ?></td>
                                <td><?= $customer['telpon'] ?></td>
                                <td><?= $customer['alamat'] ?></td>
                                <td><?= $customer['deskripsi'] ?></td>
                                <td>
                                    <a href="edit-customer.php?id=<?= $customer['id_relasi'] ?>" class="btn btn-sm btn-warning" title="edit customer"><i class="fas fa-pen"></i></a>
                                    <a href="del-customer.php?id=<?= $customer['id_relasi'] ?>" class="btn btn-sm btn-danger" title="hapus customer" onclick="return confirm('Anda yakin akan menghapus Customer ini ?')"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<?php

require "../template/footer.php"

?>