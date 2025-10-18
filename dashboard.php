<?php

session_start();

if (!isset($_SESSION["ssLoginPOS"])) {
  header("location: auth/login.php");
  exit();
}

require "config/config.php";
require "config/functions.php";

$title = "dashboard - snackinaja";
require "template/header.php";
require "template/navbar.php";
require "template/sidebar.php";

$users = getData("SELECT COUNT(*) as total FROM tbl_user");
$user_count = $users[0]['total'];

$suppliers = getData("SELECT COUNT(*) as total FROM tbl_relasi WHERE tipe = 'SUPPLIER'");
$supplier_count = $suppliers[0]['total'];

$customers = getData("SELECT COUNT(*) as total FROM tbl_relasi WHERE tipe = 'CUSTOMER'");
$customer_count = $customers[0]['total'];

$barang = getData("SELECT COUNT(*) as total FROM tbl_barang");
$barang_count = $barang[0]['total'];

?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Dashboard</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?= $main_url ?>dashboard.php">Home</a></li>
            <li class="breadcrumb-item active">Dashboard</li>
          </ol>
        </div><!-- /.col -->
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content-header -->

  <!-- Main content -->
  <div class="content">
    <div class="container-fluid">
      <!-- Small boxes (Stat box) -->
      <div class="row">
        <div class="col-lg-3 col-6">
          <!-- small box -->
          <div class="small-box bg-info">
            <div class="inner">
              <h3><?= $user_count ?></h3>

              <p>Users</p>
            </div>
            <div class="icon">
              <i class="fa-solid fa-user"></i>
            </div>
            <a href="<?= $main_url ?>user/data-user.php" class="small-box-footer">More info <i
                class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-6">
          <!-- small box -->
          <div class="small-box bg-success">
            <div class="inner">
              <h3><?= $supplier_count ?></h3>

              <p>Supplier</p>
            </div>
            <div class="icon">
              <i class="fa-solid fa-truck-field"></i>
            </div>
            <a href="<?= $main_url ?>supplier/data-supplier.php" class="small-box-footer">More info <i
                class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-6">
          <!-- small box -->
          <div class="small-box bg-warning">
            <div class="inner">
              <h3><?= $customer_count ?></h3>

              <p>Customer</p>
            </div>
            <div class="icon">
              <i class="fa-solid fa-users"></i>
            </div>
            <a href="<?= $main_url ?>customer/data-customer.php" class="small-box-footer">More info <i
                class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-6">
          <!-- small box -->
          <div class="small-box bg-danger">
            <div class="inner">
              <h3><?= $barang_count ?></h3>
              <p>Item Barang</p>
            </div>
            <div class="icon">
              <i class="fa-solid fa-cart-shopping"></i>
            </div>
            <a href="<?= $main_url ?>barang" class="small-box-footer">More info <i
                class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- ./col -->
      </div>
      <div class="row">
        <div class="col-lg-6">
          <div class="card card-outline card-danger">
            <div class="card-header text-info">
              <h5 class="card-title">Info Stok Barang</h5>
              <h5><a href="stock" class="float-right" title="laporan stock"><i class="fas fa-arrow-right"></i></a></h5>
            </div>
            <table class="table">
              <tbody>
                <?php
                $stok = getData("SELECT * FROM tbl_barang WHERE stock < stock_minimal");

                foreach ($stok as $key => $value) {
                  echo "<tr style='background-color: #f8d7da; color: #721c24;'>
          <td>" . $value['nama_barang'] . "</td>
          <td>Stok Kurang</td> 
        </tr>";
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="card card-outline-success">
            <div class="card-header text-info">
              <h5>Omzet Penjualan</h5>
              <div class="card-body text-primary">
                <h2><span class="h4">Rp </span> <?= omzet() ?></h2>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- /.content -->
  <?php

  require "template/footer.php";

  ?>