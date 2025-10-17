<?php

if (userLogin()['level'] == 3) {
    header("location: " . $mainurl . "error-page.php");
    exit();
}

function insert($data){
    global $koneksi;

    $nama    = mysqli_real_escape_string($koneksi, $data['nama']);
    $telpon  = mysqli_real_escape_string($koneksi, $data['telpon']);
    $alamat  = mysqli_real_escape_string($koneksi, $data['alamat']);
    $ketr    = mysqli_real_escape_string($koneksi, $data['ketr']);
    $tipe    = isset($data['tipe']) ? mysqli_real_escape_string($koneksi, $data['tipe']) : 'SUPPLIER';

    $sqlRelasi = "INSERT INTO tbl_relasi (nama, telpon, alamat, deskripsi, tipe) VALUES ('$nama', '$telpon', '$alamat', '$ketr', '$tipe')";
    mysqli_query($koneksi, $sqlRelasi);

    return mysqli_affected_rows($koneksi);
}

function delete($id){
    global $koneksi;

    $sqlDelete = "DELETE FROM tbl_supplier WHERE id_supplier = $id";
    mysqli_query($koneksi, $sqlDelete);
    
    return mysqli_affected_rows($koneksi);
}

function update($data) {
    global $koneksi;

    $id      = mysqli_real_escape_string($koneksi, $data['id']);
    $nama    = mysqli_real_escape_string($koneksi, $data['nama']);
    $telpon  = mysqli_real_escape_string($koneksi, $data['telpon']);
    $alamat  = mysqli_real_escape_string($koneksi, $data['alamat']);
    $ketr    = mysqli_real_escape_string($koneksi, $data['ketr']);

    $sqlSupplier    = "UPDATE tbl_supplier SET
                        nama    = '$nama',
                        telpon  = '$telpon',
                        deskripsi = '$ketr',
                        alamat  = '$alamat'
                        WHERE id_supplier = $id    
                        ";
    mysqli_query($koneksi, $sqlSupplier);
    
    return mysqli_affected_rows($koneksi);
}

?>