<?php

if (userLogin()['level'] == 3) {
    header("location: " . $mainurl . "error-page.php");
    exit();
}

function generateSupplierId() {
    global $koneksi;

    // Ambil ID terbesar saat ini
    $query = mysqli_query($koneksi, 
        "SELECT id_supplier FROM tbl_supplier ORDER BY id_supplier DESC LIMIT 1");

    $data = mysqli_fetch_assoc($query);

    if ($data) {
        $lastNumber = intval(substr($data['id_supplier'], 4));
        $newNumber = $lastNumber + 1;
    } else {
        $newNumber = 1;
    }

    return "SUP-" . str_pad($newNumber, 3, "0", STR_PAD_LEFT);
}

function insert($data){
    global $koneksi;

    $id_supplier = generateSupplierId();  // tambahkan ini

    $nama    = mysqli_real_escape_string($koneksi, $data['nama']);
    $telpon  = mysqli_real_escape_string($koneksi, $data['telpon']);
    $alamat  = mysqli_real_escape_string($koneksi, $data['alamat']);
    $ketr    = mysqli_real_escape_string($koneksi, $data['ketr']);

    $sql = "INSERT INTO tbl_supplier (id_supplier, nama, telpon, deskripsi, alamat)
            VALUES ('$id_supplier', '$nama', '$telpon', '$ketr', '$alamat')";

    mysqli_query($koneksi, $sql);

    return mysqli_affected_rows($koneksi);
}

function delete($id){
    global $koneksi;

    $sqlDelete = "DELETE FROM tbl_supplier WHERE id_supplier = '$id'";
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
                        WHERE id_supplier = '$id'    
                        ";
    mysqli_query($koneksi, $sqlSupplier);
    
    return mysqli_affected_rows($koneksi);
}

?>