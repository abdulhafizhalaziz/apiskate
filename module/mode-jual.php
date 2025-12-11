<?php

/* ===============================
   GENERATE NO FAKTUR PENJUALAN
=================================*/
function generateNo(){
    global $koneksi;

    $q = mysqli_query($koneksi, "SELECT MAX(no_jual) AS maxno FROM tbl_penjualan");
    $row = mysqli_fetch_assoc($q);
    $max = $row["maxno"] ?? 'PJ0000';
    
    $n = (int) substr($max, 2, 4);
    $n++;

    return 'PJ' . sprintf("%04d", $n);
}

/* ===============================
   HITUNG TOTAL
=================================*/
function totalJual($noJual){
    global $koneksi;

    $q = mysqli_query($koneksi,
        "SELECT SUM(jml_harga) AS total 
         FROM tbl_detail_jual 
         WHERE no_jual = '$noJual'"
    );

    $d = mysqli_fetch_assoc($q);
    return $d["total"] ?? 0;
}

/* ===============================
   INSERT DETAIL JUAL (FINAL)
=================================*/
function insert($data){
    global $koneksi;

    $no       = mysqli_real_escape_string($koneksi, $data['noJual']);
    $tgl      = mysqli_real_escape_string($koneksi, $data['tglNota']);
    $kode     = mysqli_real_escape_string($koneksi, $data['kodeBrg']);
    $nama     = mysqli_real_escape_string($koneksi, $data['namaBrg']);
    $barcode  = $data['barcode'];
    $qty      = (int)$data['qty'];
    $harga    = (int)$data['harga'];
    $stok     = (int)$data['stok'];
    $jml      = $qty * $harga;

    // ========== DEBUG KE JAVASCRIPT ==========
    echo "
    <script>
        console.log('===== DEBUG INSERT() =====');
        console.log('noJual      : $no');
        console.log('tglNota     : $tgl');
        console.log('id_barang   : $kode');
        console.log('nama_barang : $nama');
        console.log('barcode     : $barcode');
        console.log('qty         : $qty');
        console.log('harga       : $harga');
        console.log('stok        : $stok');
        console.log('subtotal    : $jml');
    </script>
    ";
    // ==========================================


    if ($kode == "" || $qty <= 0) {
        echo "<script>alert('Barang atau qty belum benar!');</script>";
        return false;
    }

    if ($qty > $stok){
        echo "<script>alert('Stok tidak mencukupi!');</script>";
        return false;
    }

    $cek = mysqli_query($koneksi,
        "SELECT * FROM tbl_detail_jual 
         WHERE no_jual='$no' AND id_barang='$kode'"
    );

    if (mysqli_num_rows($cek)){
        echo "<script>alert('Barang sudah ada, hapus dulu untuk ubah qty!');</script>";
        return false;
    }

    $sql = "
        INSERT INTO tbl_detail_jual
        (no_jual, tgl_jual, barcode, id_barang, nama_brg, qty, harga_jual, jml_harga)
        VALUES
        ('$no', '$tgl', '$barcode', '$kode', '$nama', $qty, $harga, $jml)
    ";

    mysqli_query($koneksi, $sql) or die('DETAIL ERROR: ' . mysqli_error($koneksi));

    mysqli_query($koneksi,
        "UPDATE tbl_barang SET stock = stock - $qty WHERE id_barang='$kode'"
    ) or die('STOCK ERROR: ' . mysqli_error($koneksi));;

    return true;
}


/* ===============================
   DELETE BARANG
=================================*/
function delete($id_brg, $noJual, $qty){
    global $koneksi;

    mysqli_query($koneksi,
        "DELETE FROM tbl_detail_jual
         WHERE id_barang='$id_brg' AND no_jual='$noJual'"
    );

    mysqli_query($koneksi,
        "UPDATE tbl_barang SET stock = stock + $qty 
         WHERE id_barang='$id_brg'"
    );

    return mysqli_affected_rows($koneksi);
}

/* ===============================
   SIMPAN TRANSAKSI
=================================*/
function simpan($data){
    global $koneksi;

    $no      = $data['noJual'];
    $tgl     = $data['tglNota'];
    $cust    = $data['customer'];
    $total   = (int)$data['total'];
    $bayar   = (int)$data['bayar'];
    $kembali = (int)$data['kembalian'];
    $ket     = mysqli_real_escape_string($koneksi, $data['ketr']);

    $sql = "
        INSERT INTO tbl_penjualan
        (no_jual, tgl_jual, id_customer, total, bayar, kembalian, keterangan)
        VALUES
        ('$no', '$tgl', '$cust', $total, $bayar, $kembali, '$ket')
    ";

    mysqli_query($koneksi, $sql);

    return mysqli_affected_rows($koneksi);
}

?>
