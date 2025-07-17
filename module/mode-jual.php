<?php

function generateNo(){
    global $koneksi;

    $queryNo = mysqli_query($koneksi, "SELECT max(no_jual) as maxno FROM tbl_jual_head");
    $row = mysqli_fetch_assoc($queryNo);
    $maxno = $row["maxno"] ?? 'PB0000';

    $noUrut = (int) substr($maxno, 2, 4);
    $noUrut++;
    $maxno = 'PJ' . sprintf("%04s", $noUrut);

    return $maxno;
}

function totalJual($noJual){
    global $koneksi;

    $totalJual = mysqli_query($koneksi, "SELECT sum(jml_harga) AS total FROM tbl_jual_detail WHERE no_jual = '$noJual'");
    $data  = mysqli_fetch_assoc($totalJual);
    return $data["total"] ?? 0;
}


function insert($data){
    global $koneksi;

    $no       = mysqli_real_escape_string($koneksi, $data['noJual']);
    $tgl      = mysqli_real_escape_string($koneksi, $data['tglNota']);
    $kode     = mysqli_real_escape_string($koneksi, $data['barcode']);
    $nama     = mysqli_real_escape_string($koneksi, $data['namaBrg']);
    $qty      = mysqli_real_escape_string($koneksi, $data['qty']);
    $harga    = mysqli_real_escape_string($koneksi, $data['harga']);
    $jmlharga = mysqli_real_escape_string($koneksi, $data['jmlHarga']);
    $stok = mysqli_real_escape_string($koneksi, $data['stok']);

    if (empty($qty)) {
        echo "<script>alert('Qty barang tidak boleh kosong');</script>";
        return false;
    } else {
        if ($qty > $stok) {
            echo "<script>alert('Stok tidak mencukupi');</script>";
            return false;
        }
    }

    // cek barang sudah diinput
    $cekbrg = mysqli_query($koneksi, "SELECT * FROM tbl_jual_detail WHERE no_jual = '$no' AND barcode = '$kode'");
    if (mysqli_num_rows($cekbrg)) {
        echo "<script>alert('Barang sudah ada, hapus dulu jika ingin mengubah qty.');</script>";
        return false;
    }

    $sqljual = "INSERT INTO tbl_jual_detail VALUES (null, '$no', '$tgl', '$kode', '$nama', $qty, $harga, $jmlharga)";
    mysqli_query($koneksi, $sqljual);

    mysqli_query($koneksi, "UPDATE tbl_barang SET stock = stock - $qty WHERE barcode = '$kode'");
    return mysqli_affected_rows($koneksi);
}

function delete($barcode, $idJual, $qty){
    global $koneksi;

    $sqlDel = "DELETE FROM tbl_jual_detail WHERE barcode = '$barcode' AND no_jual = '$idJual'";
    mysqli_query($koneksi, $sqlDel);

    mysqli_query($koneksi, "UPDATE tbl_barang SET stock = stock + $qty WHERE barcode = '$barcode'");

    return mysqli_affected_rows($koneksi);
}

function simpan($data){
    global $koneksi;

    $noJual     = mysqli_real_escape_string($koneksi, $data['noJual']);
    $tgl        = mysqli_real_escape_string($koneksi, $data['tglNota']);
    $total      = mysqli_real_escape_string($koneksi, $data['total']);
    $customer   = mysqli_real_escape_string($koneksi, $data['customer']);
    $keterangan = mysqli_real_escape_string($koneksi, $data['ketr']);
    $bayar      = mysqli_real_escape_string($koneksi, $data['bayar']);
    $kembalian = mysqli_real_escape_string($koneksi, $data['kembalian']);

    $sqlJual    = "INSERT INTO tbl_jual_head VALUES ('$noJual','$tgl','$customer',$total,'$keterangan', $bayar, $kembalian)";
    mysqli_query($koneksi, $sqlJual);

    return mysqli_affected_rows($koneksi);
}