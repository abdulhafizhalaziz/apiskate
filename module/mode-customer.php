<?php

if (userLogin()['level'] == 3) {
    header("location: " . $mainurl . "error-page.php");
    exit();
}

// ======================================================
//  AUTO GENERATE CUSTOMER ID (Format: CST-001)
// ======================================================
function generateCustomerID() {
    global $koneksi;

    // Ambil ID terbesar dari database
    $query = mysqli_query($koneksi, 
        "SELECT id_customer FROM tbl_customer 
         ORDER BY id_customer DESC LIMIT 1"
    );

    $data = mysqli_fetch_assoc($query);

    if ($data) {
        // Ambil angka dari CST-001 → 001
        $lastNumber = (int) substr($data['id_customer'], 4);
        $newNumber = $lastNumber + 1;
    } else {
        // Jika belum ada data
        $newNumber = 1;
    }

    // Format: CST-001 (3 digit angka)
    return "CST-" . str_pad($newNumber, 3, "0", STR_PAD_LEFT);
}


// ======================================================
//  INSERT DATA CUSTOMER
// ======================================================
function insert($data){
    global $koneksi;

    $id_customer = generateCustomerID();  // Auto ID

    $nama    = mysqli_real_escape_string($koneksi, $data['nama']);
    $telpon  = mysqli_real_escape_string($koneksi, $data['telpon']);
    $alamat  = mysqli_real_escape_string($koneksi, $data['alamat']);
    $ketr    = mysqli_real_escape_string($koneksi, $data['ketr']);

    $sql = "INSERT INTO tbl_customer
            (id_customer, nama, telpon, deskripsi, alamat)
            VALUES
            ('$id_customer', '$nama', '$telpon', '$ketr', '$alamat')";

    mysqli_query($koneksi, $sql);

    return mysqli_affected_rows($koneksi);
}


// ======================================================
//  HAPUS DATA CUSTOMER
// ======================================================
function delete($id){
    global $koneksi;

    $sql = "DELETE FROM tbl_customer WHERE id_customer = '$id'";
    mysqli_query($koneksi, $sql);
    
    return mysqli_affected_rows($koneksi);
}


// ======================================================
//  UPDATE DATA CUSTOMER
// ======================================================
function update($data) {
    global $koneksi;

    $id      = mysqli_real_escape_string($koneksi, $data['id']);
    $nama    = mysqli_real_escape_string($koneksi, $data['nama']);
    $telpon  = mysqli_real_escape_string($koneksi, $data['telpon']);
    $alamat  = mysqli_real_escape_string($koneksi, $data['alamat']);
    $ketr    = mysqli_real_escape_string($koneksi, $data['ketr']);

    $sql = "UPDATE tbl_customer SET
                nama      = '$nama',
                telpon    = '$telpon',
                deskripsi = '$ketr',
                alamat    = '$alamat'
            WHERE id_customer = '$id'";

    mysqli_query($koneksi, $sql);
    
    return mysqli_affected_rows($koneksi);
}

?>
