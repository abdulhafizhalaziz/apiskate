<?php

function generateNo(){
    global $koneksi;

    $queryNo = mysqli_query($koneksi, "SELECT max(no_transaksi) as maxno FROM tbl_transaksi WHERE tipe_transaksi = 'JUAL'");
    $row = mysqli_fetch_assoc($queryNo);
    $maxno = $row["maxno"] ?? 'PJ0000';

    $noUrut = (int) substr($maxno, 2, 4);
    $noUrut++;
    $maxno = 'PJ' . sprintf("%04s", $noUrut);

    return $maxno;
}

function totalJual($noJual){
    global $koneksi;

    $totalJual = mysqli_query($koneksi, "SELECT sum(jml_harga) AS total FROM tbl_transaksi_detail WHERE no_transaksi = '$noJual'");
    $data  = mysqli_fetch_assoc($totalJual);
    return $data["total"] ?? 0;
}


function insert($data){
    global $koneksi;

    $no       = mysqli_real_escape_string($koneksi, $data['noJual']);
    $tgl      = mysqli_real_escape_string($koneksi, $data['tglNota']);
    $id_barang= mysqli_real_escape_string($koneksi, $data['barcode']); // barcode diisi ke id_barang
    $nama     = mysqli_real_escape_string($koneksi, $data['namaBrg']);
    $qty      = mysqli_real_escape_string($koneksi, $data['qty']);
    $harga    = mysqli_real_escape_string($koneksi, $data['harga']);
    $jmlharga = mysqli_real_escape_string($koneksi, $data['jmlHarga']);
    $stok     = mysqli_real_escape_string($koneksi, $data['stok']);

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
    $cekbrg = mysqli_query($koneksi, "SELECT * FROM tbl_transaksi_detail WHERE no_transaksi = '$no' AND id_barang = '$id_barang'");
    if (mysqli_num_rows($cekbrg)) {
        echo "<script>alert('Barang sudah ada, hapus dulu jika ingin mengubah qty.');</script>";
        return false;
    }

    $sqljual = "INSERT INTO tbl_transaksi_detail (no_transaksi, tgl_transaksi, id_barang, nama_brg, qty, harga, jml_harga) VALUES ('$no', '$tgl', '$id_barang', '$nama', $qty, $harga, $jmlharga)";
    mysqli_query($koneksi, $sqljual);

    // Update stock sudah otomatis via trigger
    return mysqli_affected_rows($koneksi);
}

function delete($id_barang, $idJual, $qty){
    global $koneksi;

    $sqlDel = "DELETE FROM tbl_transaksi_detail WHERE id_barang = '$id_barang' AND no_transaksi = '$idJual'";
    mysqli_query($koneksi, $sqlDel);

    // Update stock sudah otomatis via trigger
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
    $kembalian  = mysqli_real_escape_string($koneksi, $data['kembalian']);

    $sqlJual    = "INSERT INTO tbl_transaksi (no_transaksi, tgl_transaksi, tipe_transaksi, relasi, total, bayar, kembalian, keterangan) VALUES ('$noJual','$tgl','JUAL','$customer',$total,'$bayar','$kembalian','$keterangan')";
    mysqli_query($koneksi, $sqlJual);

    return mysqli_affected_rows($koneksi);
}