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

    function ensureDefaultCustomerId(): int {
        global $koneksi;
        $name = 'Umum';
        $res = mysqli_query($koneksi, "SELECT id_relasi FROM tbl_relasi WHERE tipe='CUSTOMER' AND nama='$name' LIMIT 1");
        if ($res && ($row = mysqli_fetch_assoc($res))) {
            return (int)$row['id_relasi'];
        }
        $sql = "INSERT INTO tbl_relasi (nama, telpon, alamat, deskripsi, tipe) VALUES ('$name', '-', '-', 'Customer default otomatis', 'CUSTOMER')";
        mysqli_query($koneksi, $sql);
        return (int) mysqli_insert_id($koneksi);
    }


function insert($data){
    global $koneksi;

        $no       = mysqli_real_escape_string($koneksi, $data['noJual']);
        $tglIn    = $data['tglNota'] ?? '';
        $tgl      = mysqli_real_escape_string($koneksi, (preg_match('/^\d{4}-\d{2}-\d{2}$/', $tglIn) ? $tglIn : date('Y-m-d')));
    $kode_barang = mysqli_real_escape_string($koneksi, $data['barcode']);
    $nama     = mysqli_real_escape_string($koneksi, $data['namaBrg']);
    $qty      = mysqli_real_escape_string($koneksi, $data['qty']);
    $harga    = mysqli_real_escape_string($koneksi, $data['harga']);
    $jmlharga = mysqli_real_escape_string($koneksi, $data['jmlHarga']);
    $stok     = mysqli_real_escape_string($koneksi, $data['stok']);
        $customer = isset($data['customer']) ? (int)$data['customer'] : 0;
        $keterangan = mysqli_real_escape_string($koneksi, $data['ketr'] ?? '');

    if (empty($qty)) {
        echo "<script>alert('Qty barang tidak boleh kosong');</script>";
        return false;
    } else {
        if ($qty > $stok) {
            echo "<script>alert('Stok tidak mencukupi');</script>";
            return false;
        }
    }

    $cekHeader = mysqli_query($koneksi, "SELECT 1 FROM tbl_transaksi WHERE no_transaksi = '$no' LIMIT 1");
    if (!$cekHeader || mysqli_num_rows($cekHeader) === 0) {
        if ($customer <= 0) {
            $customer = ensureDefaultCustomerId();
        } else {
            $cekCust = mysqli_query($koneksi, "SELECT 1 FROM tbl_relasi WHERE id_relasi = $customer AND tipe = 'CUSTOMER' LIMIT 1");
            if (!$cekCust || mysqli_num_rows($cekCust) === 0) {
                echo "<script>alert('Customer tidak valid/bukan CUSTOMER.');</script>";
                return false;
            }
        }
        $sqlHdr = "INSERT INTO tbl_transaksi (no_transaksi, tgl_transaksi, tipe_transaksi, id_relasi, total, bayar, kembalian, keterangan)
                   VALUES ('$no', STR_TO_DATE('$tgl','%Y-%m-%d'), 'JUAL', $customer, 0, 0, 0, '$keterangan')";
        $okHdr = mysqli_query($koneksi, $sqlHdr);
        if (!$okHdr) {
            echo "<script>alert('Gagal membuat header transaksi.');</script>";
            return false;
        }
    }

    $cekbrg = mysqli_query($koneksi, "SELECT * FROM tbl_transaksi_detail WHERE no_transaksi = '$no' AND kode_barang = '$kode_barang'");
    if (mysqli_num_rows($cekbrg)) {
        echo "<script>alert('Barang sudah ada, hapus dulu jika ingin mengubah qty.');</script>";
        return false;
    }

    $sqljual = "INSERT INTO tbl_transaksi_detail (no_transaksi, kode_barang, nama_brg, qty, harga, jml_harga) VALUES ('$no', '$kode_barang', '$nama', $qty, $harga, $jmlharga)";
    $ok = mysqli_query($koneksi, $sqljual);
    if (!$ok) {
        return false;
    }
    mysqli_query($koneksi, "UPDATE tbl_barang SET stock = GREATEST(stock - $qty, 0) WHERE id_barang = '$kode_barang' OR barcode = '$kode_barang'");
    return true;
}

function delete($id_barang, $idJual, $qty){
    global $koneksi;

    $sqlDel = "DELETE FROM tbl_transaksi_detail WHERE kode_barang = '$id_barang' AND no_transaksi = '$idJual'";
    $ok = mysqli_query($koneksi, $sqlDel);
    if ($ok) {
        mysqli_query($koneksi, "UPDATE tbl_barang SET stock = stock + $qty WHERE id_barang = '$id_barang' OR barcode = '$id_barang'");
        $resCnt = mysqli_query($koneksi, "SELECT COUNT(*) AS jml FROM tbl_transaksi_detail WHERE no_transaksi = '$idJual'");
        $rowCnt = $resCnt ? mysqli_fetch_assoc($resCnt) : ['jml' => 0];
        $jml = (int)($rowCnt['jml'] ?? 0);
        if ($jml === 0) {
            mysqli_query($koneksi, "DELETE FROM tbl_transaksi WHERE no_transaksi = '$idJual' AND tipe_transaksi = 'JUAL'");
        }
    }
    return mysqli_affected_rows($koneksi);
}

function simpan($data){
    global $koneksi;

    $noJual     = mysqli_real_escape_string($koneksi, $data['noJual']);
    $tglIn      = $data['tglNota'] ?? '';
    $tgl        = mysqli_real_escape_string($koneksi, (preg_match('/^\d{4}-\d{2}-\d{2}$/', $tglIn) ? $tglIn : date('Y-m-d')));
    $total      = mysqli_real_escape_string($koneksi, $data['total']);
    $customer   = mysqli_real_escape_string($koneksi, $data['customer']);
    $keterangan = mysqli_real_escape_string($koneksi, $data['ketr']);
    $bayar      = mysqli_real_escape_string($koneksi, $data['bayar']);
    $kembalian  = mysqli_real_escape_string($koneksi, $data['kembalian']);

    $resTotal   = mysqli_query($koneksi, "SELECT COALESCE(SUM(jml_harga),0) AS total FROM tbl_transaksi_detail WHERE no_transaksi = '$noJual'");
    $rowTotal   = $resTotal ? mysqli_fetch_assoc($resTotal) : ['total' => 0];
    $total      = $rowTotal['total'] ?? 0;
    $customer   = (int) ($data['customer'] ?? 0);
    $keterangan = mysqli_real_escape_string($koneksi, $data['ketr'] ?? '');
    $bayar      = (float) ($data['bayar'] ?? 0);
    $kembalian  = (float) ($data['kembalian'] ?? 0);

    $resCntDet = mysqli_query($koneksi, "SELECT COUNT(*) AS jml FROM tbl_transaksi_detail WHERE no_transaksi = '$noJual'");
    $rowCntDet = $resCntDet ? mysqli_fetch_assoc($resCntDet) : ['jml' => 0];
    if ((int)($rowCntDet['jml'] ?? 0) === 0) {
        mysqli_query($koneksi, "DELETE FROM tbl_transaksi WHERE no_transaksi = '$noJual' AND tipe_transaksi = 'JUAL'");
        return false;
    }

    $sqlJual    = "INSERT INTO tbl_transaksi (no_transaksi, tgl_transaksi, tipe_transaksi, id_relasi, total, bayar, kembalian, keterangan)
                   VALUES ('$noJual',STR_TO_DATE('$tgl','%Y-%m-%d'),'JUAL',$customer,$total,$bayar,$kembalian,'$keterangan')
                   ON DUPLICATE KEY UPDATE 
                        tgl_transaksi = STR_TO_DATE('$tgl','%Y-%m-%d'),
                        id_relasi = $customer,
                        total = $total,
                        bayar = $bayar,
                        kembalian = $kembalian,
                        keterangan = '$keterangan'";
    $ok = mysqli_query($koneksi, $sqlJual);
    if (!$ok) {
        return false;
    }
    return true;
}