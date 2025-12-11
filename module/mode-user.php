<?php

if (userLogin()['level'] != 1) {
    header("location:" . $main_url . "error-page.php");
    exit();
}

/* ============================================================
   GENERATE USER ID (VARCHAR) — USR-001, USR-002, dst
============================================================ */
function generateUserId($koneksi) {
    $q = mysqli_query($koneksi, "SELECT MAX(userid) AS maxid FROM tbl_user");
    $d = mysqli_fetch_assoc($q);

    if ($d['maxid'] == null) return "USR-001";

    $num = intval(substr($d['maxid'], 4));
    $new = $num + 1;

    return "USR-" . str_pad($new, 3, "0", STR_PAD_LEFT);
}


/* ============================================================
   INSERT USER
============================================================ */
function insert($data){
    global $koneksi;

    $username   = strtolower(mysqli_real_escape_string($koneksi, $data['username']));
    $fullname   = mysqli_real_escape_string($koneksi, $data['fullname']);
    $password   = mysqli_real_escape_string($koneksi, $data['password']);
    $password2  = mysqli_real_escape_string($koneksi, $data['password2']);
    $level      = mysqli_real_escape_string($koneksi, $data['level']);
    $address    = mysqli_real_escape_string($koneksi, $data['address']);

    if ($password !== $password2) {
        echo "<script>alert('Konfirmasi password tidak sesuai');</script>";
        return false;
    }

    $cekUsername = mysqli_query($koneksi, "SELECT username FROM tbl_user WHERE username='$username'");
    if (mysqli_num_rows($cekUsername) > 0) {
        echo "<script>alert('Username sudah digunakan');</script>";
        return false;
    }

    // cek upload
    if ($_FILES['image']['error'] === 4) {
        $gambar = "default.png";
    } else {
        $gambar = uploadimg();
        if (!$gambar) return false;
    }

    $pass = password_hash($password, PASSWORD_DEFAULT);
    $id   = generateUserId($koneksi);

    $sqlUser = "INSERT INTO tbl_user 
                (userid, username, fullname, password, address, level, foto)
                VALUES
                ('$id', '$username', '$fullname', '$pass', '$address', '$level', '$gambar')";

    mysqli_query($koneksi, $sqlUser);
    return mysqli_affected_rows($koneksi);
}


/* ============================================================
   DELETE USER
============================================================ */
function delete($id, $foto){
    global $koneksi;

    mysqli_query($koneksi, "DELETE FROM tbl_user WHERE userid='$id'");

    if ($foto != 'default.png') {
        @unlink('../asset/image/' . $foto);
    }

    return mysqli_affected_rows($koneksi);
}


/* ============================================================
   UPDATE USER
============================================================ */
function update($data){
    global $koneksi;

    $iduser   = mysqli_real_escape_string($koneksi, $data['id']);
    $username = strtolower(mysqli_real_escape_string($koneksi, $data['username']));
    $fullname = mysqli_real_escape_string($koneksi, $data['fullname']);
    $level    = mysqli_real_escape_string($koneksi, $data['level']);
    $address  = mysqli_real_escape_string($koneksi, $data['address']);
    $fotoLama = mysqli_real_escape_string($koneksi, $data['oldImg']);

    // cek username lama
    $cur = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT username FROM tbl_user WHERE userid='$iduser'"));
    if (!$cur) return false;

    $curUsername = $cur['username'];

    // jika username berubah, cek apakah sudah dipakai user lain
    if ($username !== $curUsername) {
        $cekUser = mysqli_query($koneksi, "SELECT username FROM tbl_user WHERE username='$username'");
        if (mysqli_num_rows($cekUser) > 0) {
            echo "<script>alert('Username sudah digunakan');</script>";
            return false;
        }
    }

    // cek foto
    if ($_FILES['image']['error'] === 4) {
        $imgUser = $fotoLama;
    } else {
        $imgUser = uploadimg();
        if ($fotoLama != "default.png") {
            @unlink('../asset/image/' . $fotoLama);
        }
    }

    $sql = "UPDATE tbl_user SET
            username='$username',
            fullname='$fullname',
            address='$address',
            level='$level',
            foto='$imgUser'
            WHERE userid='$iduser'";

    mysqli_query($koneksi, $sql);
    return mysqli_affected_rows($koneksi);
}


/* ============================================================
   SELECT LEVEL (helper untuk form edit)
============================================================ */
function selectUser1($level){
    return $level == 1 ? "selected" : "";
}
function selectUser2($level){
    return $level == 2 ? "selected" : "";
}
function selectUser3($level){
    return $level == 3 ? "selected" : "";
}

?>
