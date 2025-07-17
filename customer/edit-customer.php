<?php

session_start();

if (!isset($_SESSION["ssLoginPOS"])) {
    header("location: ../auth/login.php");
    exit();
}

require "../config/config.php";
require "../config/functions.php";
require "../module/mode-customer.php";

$title  = "Edit Customer - snackinaja";
require "../template/header.php";
require "../template/navbar.php";
require "../template/sidebar.php";

// jalankan fungsi update data
if (isset($_POST['update'])) {
    if (update($_POST)) {
        echo "<script>
                document.location.href = 'data-customer.php?msg=updated';
        </script>";
    }
}

$id = $_GET['id'];

$sqlEdit = "SELECT * FROM tbl_customer WHERE id_customer = $id";
$customer = getData($sqlEdit)[0];

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
            <li class="breadcrumb-item"><a href="<?= $main_url ?>customer/data-customer.php">Customer</a></li>
            <li class="breadcrumb-item active">Edit Customer</li>
            </ol>
        </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
    </div>

            <section class="content">
        <div class="container-fluid">
        <div class="card">
            <form action="" method="post" enctype="multipart/form-data">
            <div class="card-header">
            <h3 class="card-title"><i class="fas fa-plus fa-sm"></i> Edit Customer</h3>
            <button type="submit" name="update" class="btn btn-primary btn-sm float-right"><i class="fa-solid fa-floppy-disk"></i> Update</button>
            <button type="reset" class="btn btn-danger btn-sm float-right mr-1">Reset</button>
            </div>
            <div class="card-body">
                <div class="row">
                    <input type="hidden" name='id' value="<?= $customer['id_customer'] ?>">
                    <div class="col-lg-8 mb-3">
                        <div class="form-group">
                            <label for="name">Nama</label>
                            <input type="text" name="nama" class="form-control" id="nama" placeholder="Nama Customer" value="<?= $customer['nama'] ?>" autofocus required>
                        </div>
                        <div class="form-group">
                            <label for="telpon">Telpon</label>
                            <input type="text" name="telpon" class="form-control" id="telpon" placeholder="Nomor Telpon Customer" pattern="[0-9]{5,}" title="minimal 5 angka" value="<?= $customer['telpon'] ?>"required>
                        </div>
                        <div class="form-group">
                            <label for="ketr">Deskripsi</label>
                            <textarea name="ketr" id="ketr" rows="1" class="form-control" placeholder="Keterangan Customer" require><?= $customer['deskripsi'] ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="alamat">Alamat</label>
                            <textarea name="alamat" id="alamat" rows="3" class="form-control" placeholder="Alamat Customer" require><?= $customer['alamat'] ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
        </section>

<?php

require "../template/footer.php"

?>