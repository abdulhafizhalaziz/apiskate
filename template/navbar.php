<script src="https://kit.fontawesome.com/6d6e040a56.js" crossorigin="anonymous"></script>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="<?= $main_url ?>dashboard.php" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="<?= $main_url ?>dashboard.php" class="nav-link">Home</a>
      </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown">
            <a href="<?= $main_url ?>dashboard.php" class="nav-link dropdown-toggle"
            data-toggle="dropdown">
                <?= userLogin()['username'] ?><i class="fas fa-user-cog ml-2"></i>
            </a>
            <div class="dropdown-menu dropdown-menu dropdown-menu-right">
                            <a href="<?= $main_url ?>auth/change-password.php" class="dropdown-item text-right">
                                Change Password <i class="fas fa-key"></i>
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="<?= $main_url ?>auth/logout.php" class="dropdown-item text-right">
                                Log Out <i class="fas fa-sign-out-alt"></i>
                        </a>
                    </div>
                </li>

    </ul>
  </nav>
  <!-- /.navbar -->
