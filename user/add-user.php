<?php

session_start();

if (!isset($_SESSION["ssLoginPOS"])) {
    header("location: ../auth/login.php");
    exit();
}

require "../config/config.php";
require "../config/functions.php";
require "../module/mode-user.php";

$title  = "Tambah User - snackinaja";
require "../template/header.php";
require "../template/navbar.php";
require "../template/sidebar.php";

if (isset($_POST['simpan'])) {
    if (insert($_POST) > 0) {
      echo "<script>
                alert('user baru berhasil diregistrasi !');
            </script>";
    }
}

?>

<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Users</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="<?= $main_url ?>dashboard.php">Beranda</a></li>
              <li class="breadcrumb-item"><a href="<?= $main_url ?>user/data-user.php">Users</a></li>
              <li class="breadcrumb-item active">Add User</li>
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
              <h3 class="card-title"><i class="fas fa-plus fa-sm"></i> Add User</h3>
              <button type="submit" name="simpan" class="btn btn-primary btn-sm float-right"><i class="fa-solid fa-floppy-disk"></i> Simpan</button>
              <button type="reset" class="btn btn-danger btn-sm float-right mr-1">Atur ulang</button>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-lg-8 mb3">
                  <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" name="username" class="form-control" id="username" placeholder="masukan username" autofocus autocomplete="off" require>
                  </div>
                    <div class="form-group">
                    <label for="fullname">Fullname</label>
                    <input type="text" name="fullname" class="form-control" id="fullname" placeholder="masukan nama lengkap" require>
                      <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" class="form-control" id="password" placeholder="masukan password" require>
                      <div class="form-group">
                    <label for="password2">Konfirmasi Password</label>
                    <input type="password" name="password2" class="form-control" id="password2" placeholder="masukan kembali password anda" require>
                     <div class="form-group">
                    <label for="level">Level</label>
                    <select name="level" id="level" class="form-control" require>
                      <option value="">-- level user -- </option>
                      <option value="1">Administrator</option>
                      <option value="2">Supervisor</option>
                      <option value="3">Operator</option>  
                    </select>
                </div>
                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea name="address" id="address" cols="" rows="3" class="form-control" placeholder="masukan alamat user" require></textarea>
                </div>
            </div> 

                <div class="col-lg-4 text-center">
                    <img src="<?= $main_url ?>asset/image/default.png" class="profile-user-img img-circle mb-3" alt="">
                    <input type="file" class="form-control" name="image">
                    <span class="text-sm"> Type file gambar JPG | PNG | GIF</span><br>
                    <span class="text-sm"> Width = Height</span>

                </div>
              </div>
            </div>
            </form>
          </div>
        </div>

    </section>

<?php

require "../template/footer.php";

?>