<?php

function uploadimg($url = null, $name = null) {
    $namafile   = $_FILES['image']['name'];
    $ukuran     = $_FILES['image']['size'];
    $tmp        = $_FILES['image']['tmp_name'];

    // Validasi ekstensi file
    $ekstensiGambarValid = ['jpg', 'png', 'jpeg', 'gif'];
    $ekstensiGambar = explode('.', $namafile);
    $ekstensiGambar = strtolower(end($ekstensiGambar));
    
    if (!in_array($ekstensiGambar, $ekstensiGambarValid)) {
        if ($url != null) {
            echo '<script>
                    alert("File yang Anda upload bukan gambar, data gagal diupdate!");
                    document.location.href = "' . $url . '";
                </script>';
            die();
        } else {
            echo "<script>
                    alert('File yang Anda upload bukan gambar, data gagal ditambahkan!');
                </script>";
            return false;
        }
    }

    // Validasi ukuran gambar max 1 MB
    if ($ukuran > 1000000) {
        if ($url != null) {
            echo '<script>
                    alert("Ukuran gambar melebihi 1 MB, data gagal diupdate!");
                    document.location.href = "' . $url . '";
                </script>';
            die();
        } else {
            echo '<script>
                    alert("Ukuran gambar tidak boleh melebihi 1 MB");
                </script>';
            return false;
        }
    }

    if ($name != null) {
        $namaFileBaru = $name . '.' . $ekstensiGambar;
    } else {
        $namaFileBaru = rand(10, 1000) . '-' . $namafile;
    }
    
    move_uploaded_file($tmp, '../asset/image/' . $namaFileBaru);
    return $namaFileBaru;
}

// Fungsi untuk mengambil data dari database
function getData($sql) {
    global $koneksi;

    $result = mysqli_query($koneksi, $sql);
    $rows = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }

    return $rows;
}

function userLogin() {
    $userActive = $_SESSION["ssUserPOS"];
    $dataUser   = getData("SELECT * FROM tbl_user WHERE username = '$userActive'")[0];
    return $dataUser;
}

function userMenu() {
    $uri_path       = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $uri_segments   = explode('/', $uri_path);
    $menu           = $uri_segments[2];
    return $menu;
}

function menuHome() {
    if (userMenu() == 'dashboard.php') {
        $result = 'active';
    } else {
        $result = null;
    }
    return $result;
}

function menuSetting() {
    if (usermenu() == 'user') {
        $result = 'menu-is-opening menu-open';
    } else {
        $result = null;
    }
    return $result;
}

function menuMaster() {
    if (userMenu() == 'supplier' or userMenu() == 'customer' or userMenu() == 'barang') {
        $result = 'menu-is-opening menu-open';
    } else {
        $result = null;
    }
    return $result;
}

function menuUser() {
    if (userMenu() == 'user') {
        $result = 'active';
    } else {
        $result = null;
    }
    return $result;
}

function menuSupplier() {
    if (userMenu() == 'supplier') {
        $result = 'active';
    } else {
        $result = null;
    }
    return $result;
}

function menuCustomer() {
    if (userMenu() == 'customer') {
        $result = 'active';
    } else {
        $result = null;
    }
    return $result;
}

function menuBarang() {
    if (userMenu() == 'barang') {
        $result = 'active';
    } else {
        $result = null;
    }
    return $result;
}

function laporanStock() {
    if (userMenu() == 'stock') {
        $result = 'active';
    } else {
        $result = null;
    }
    return $result;
}

function laporanBeli(): string|null {
    if (userMenu() == 'laporan-pembelian') {
        $result = 'active';
    } else {
        $result = null;
    }
    return $result;
}

function in_date($tgl) {
    $tg = substr($tgl, 8, 2);
    $bln = substr($tgl, 5, 2);
    $thn = substr($tgl, 0, 4);
    return $tg . '-' . $bln . '-' . $thn;
}

?>