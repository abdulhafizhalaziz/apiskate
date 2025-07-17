<?php

session_start();

if (!isset($_SESSION["ssLoginPOS"])) {
    header("location: ../auth/login.php");
    exit();
}

require "../config/config.php";
require "../config/functions.php";
require "../module/mode-password.php";

$title = "Change Password - snackinaja";
require "../template/header.php";
require "../template/navbar.php";
require "../template/sidebar.php";

// update password
if (isset($_POST['simpan'])) {
    if (update($_POST)) {
        echo "<script>
                alert('Password berhasil diperbaharui..');
                document.location='change-password.php';
        </script>";
    }
}

if (isset($_GET['msg'])) {
    $msg = $_GET['msg'];
} else {
    $msg = '';
}

$alert1 = '<small class="text-danger" pl-2 font-italic>Konfirmasi password tidak sesuai</small>';
$alert2 = '<small class="text-danger" pl-2 font-italic>Current password tidak sama</small>';

?>

<div class="content-wrapper">
  <!-- Content Header -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Password</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?= $main_url ?>dashboard.php">Home</a></li>
            <li class="breadcrumb-item active">Password</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <section class="content">
    <div class="container-fluid">
        <div class="card">
            <form action="" method="post">
                <div class="card-header">
                    <h3 class="card-title"><i class="fa-solid fa-key"></i> Change Password</h3>
                    <button type="submit" name="simpan" class="btn btn-primary btn-sm float-right"><i class="fa-solid fa-pen-to-square"></i> Submit</button>
                    <button type="reset" name="reset" class="btn btn-danger btn-sm float-right mr-1"><i class="fa-solid fa-circle-xmark"></i> Reset</button>
                </div>
                <div class="card-body">
                    <div class="col-lg-8 mb3">
                        <div class="form-grup">
                            <label for="curPass">Current Password</label>
                            <input type="Password" name="curPass" id="curPass" class="form-control" placeholder="Masukan Password Anda Saat Ini"
                             required>
                             <?php if($msg == 'err2'){
                                echo $alert2;
                             } ?>
                        </div>
                        <div class="form-grup">
                            <label for="newPass">New Password</label>
                            <input type="Password" name="newPass" id="newPass" class="form-control" placeholder="Masukan Password Baru Anda"
                             required>
                    </div>
                    <div class="form-grup">
                            <label for="confPass">Confrim Password</label>
                            <input type="Password" name="confPass" id="confPass" class="form-control" placeholder="Masukan Kembali Password Baru Anda"
                             required>
                             <?php if($msg == 'err1'){
                                echo $alert1;
                             } ?>
                </div>
            </form>
        </div>
    </div>
  </section>


<?php

require "../template/footer.php"

?>