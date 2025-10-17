<?php

function generateNo(){
    global $koneksi;

    $queryNo = mysqli_query($koneksi, "SELECT max(no_transaksi) as maxno FROM tbl_transaksi WHERE tipe_transaksi = 'BELI'");
    $row = mysqli_fetch_assoc($queryNo);
    $maxno = $row["maxno"] ?? 'PB0000';

    $noUrut = (int) substr($maxno, 2, 4);
    $noUrut++;
    $maxno = 'PB' . sprintf("%04s", $noUrut);

    return $maxno;
}

function totalBeli($noBeli){
    global $koneksi;

    $totalBeli = mysqli_query($koneksi, "SELECT sum(jml_harga) AS total FROM tbl_transaksi_detail WHERE no_transaksi = '$noBeli'");
    $data  = mysqli_fetch_assoc($totalBeli);
    return $data["total"] ?? 0;
}

function insert($data){
    global $koneksi;

    $no       = mysqli_real_escape_string($koneksi, $data['noBeli']);
    $tgl      = mysqli_real_escape_string($koneksi, $data['tglNota']);
    $kode     = mysqli_real_escape_string($koneksi, $data['kodeBrg']);
    $nama     = mysqli_real_escape_string($koneksi, $data['namaBrg']);
    $qty      = mysqli_real_escape_string($koneksi, $data['qty']);
    $harga    = mysqli_real_escape_string($koneksi, $data['harga']);
    $jmlharga = mysqli_real_escape_string($koneksi, $data['jmlHarga']);

    if (empty($qty)) {
        echo "<script>alert('Qty barang tidak boleh kosong');</script>";
        return false;
    }
    $cekbrg = mysqli_query($koneksi, "SELECT * FROM tbl_transaksi_detail WHERE no_transaksi = '$no' AND kode_barang = '$kode'");
    if (mysqli_num_rows($cekbrg)) {
        echo "<script>alert('Barang sudah ada, hapus dulu jika ingin mengubah qty.');</script>";
        return false;
    }

    // Pastikan tgl_transaksi diisi
    $sqlBeli = "INSERT INTO tbl_transaksi_detail (no_transaksi, tgl_transaksi, kode_barang, nama_brg, qty, harga, jml_harga) VALUES ('$no', '$tgl', '$kode', '$nama', $qty, $harga, $jmlharga)";
    mysqli_query($koneksi, $sqlBeli);

    return mysqli_affected_rows($koneksi);
}

function delete($idbrg, $idbeli, $qty){
    global $koneksi;

    $sqlDel = "DELETE FROM tbl_transaksi_detail WHERE kode_barang = '$idbrg' AND no_transaksi = '$idbeli'";
    mysqli_query($koneksi, $sqlDel);

    // Update stock sudah otomatis via trigger, baris berikut bisa dihapus jika sudah pakai trigger
    // mysqli_query($koneksi, "UPDATE tbl_barang SET stock = stock - $qty WHERE id_barang = '$idbrg'");

    return mysqli_affected_rows($koneksi);
}

function simpan($data){
    global $koneksi;

    $noBeli     = mysqli_real_escape_string($koneksi, $data['noBeli']);
    $tgl        = mysqli_real_escape_string($koneksi, $data['tglNota']);
    $total      = mysqli_real_escape_string($koneksi, $data['total']);
    $supplier   = mysqli_real_escape_string($koneksi, $data['supplier']);
    $keterangan = mysqli_real_escape_string($koneksi, $data['ketr']);

    $sqlBeli    = "INSERT INTO tbl_transaksi (no_transaksi, tgl_transaksi, tipe_transaksi, relasi, total, keterangan) VALUES ('$noBeli','$tgl','BELI','$supplier',$total,'$keterangan')";
    mysqli_query($koneksi, $sqlBeli);

    return mysqli_affected_rows($koneksi);
}
