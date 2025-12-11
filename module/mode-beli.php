<?php

// ===============================
// GENERATE NO FAKTUR PEMBELIAN
// ===============================
function generateNo() {
    global $koneksi;

    $queryNo = mysqli_query($koneksi,
        "SELECT MAX(no_beli) AS maxno FROM tbl_pembelian"
    );

    $row   = mysqli_fetch_assoc($queryNo);
    $maxno = $row["maxno"] ?? 'PB0000';

    $noUrut = (int) substr($maxno, 2, 4);
    $noUrut++;

    return 'PB' . sprintf("%04s", $noUrut);
}

// ===============================
// HITUNG TOTAL PEMBELIAN
// ===============================
function totalBeli($noBeli) {
    global $koneksi;

    $totalBeli = mysqli_query($koneksi,
        "SELECT SUM(jml_harga) AS total 
         FROM tbl_detail_beli 
         WHERE no_beli = '$noBeli'"
    );

    $data = mysqli_fetch_assoc($totalBeli);
    return $data["total"] ?? 0;
}

// ===============================
// PASTIKAN HEADER PEMBELIAN ADA
// ===============================
function ensureHeaderExists($noBeli, $tgl) {
    global $koneksi;

    // Cek sudah ada header-nya atau belum
    $cek = mysqli_query($koneksi,
        "SELECT no_beli FROM tbl_pembelian WHERE no_beli = '$noBeli'"
    );

    if (mysqli_num_rows($cek)) {
        return true; // sudah ada, aman
    }

    // Ambil 1 supplier sebagai default (biar FK id_supplier tidak error)
    $qSupp = mysqli_query($koneksi,
        "SELECT id_supplier FROM tbl_supplier ORDER BY id_supplier LIMIT 1"
    );

    if (!mysqli_num_rows($qSupp)) {
        echo "<script>alert('Belum ada data supplier. Tambah minimal 1 supplier dulu di master supplier.');</script>";
        return false;
    }

    $rSupp       = mysqli_fetch_assoc($qSupp);
    $defaultSupp = $rSupp['id_supplier'];

    // Insert header kosong dulu
    $sqlHeader = "
        INSERT INTO tbl_pembelian
        (no_beli, id_supplier, tgl_beli, total, keterangan)
        VALUES
        ('$noBeli', '$defaultSupp', '$tgl', 0, '')
    ";

    mysqli_query($koneksi, $sqlHeader) or die("HEADER ERROR: " . mysqli_error($koneksi));
    return true;
}

// ===============================
// INSERT DETAIL PEMBELIAN
// ===============================
function insert($data) {
    global $koneksi;

    $no       = mysqli_real_escape_string($koneksi, $data['noBeli']);
    $tgl      = mysqli_real_escape_string($koneksi, $data['tglNota']);
    $kode     = mysqli_real_escape_string($koneksi, $data['kodeBrg']);   // id_barang
    $nama     = mysqli_real_escape_string($koneksi, $data['namaBrg']);
    $qty      = (int) $data['qty'];
    $harga    = (int) $data['harga'];
    $jmlharga = (int) $data['jmlHarga'];

    if ($kode == '' || $qty <= 0) {
        echo "<script>alert('Barang dan qty harus diisi dengan benar!');</script>";
        return false;
    }

    // Pastikan header sudah ada → biar FK no_beli tidak error
    if (!ensureHeaderExists($no, $tgl)) {
        return false;
    }

    // CEK DUPLIKAT BARANG BERDASARKAN id_barang
    $cekbrg = mysqli_query($koneksi,
        "SELECT * FROM tbl_detail_beli 
         WHERE no_beli = '$no' AND id_barang = '$kode'"
    );

    if (mysqli_num_rows($cekbrg)) {
        echo "<script>alert('Barang sudah ada. Hapus dulu jika ingin mengubah qty.');</script>";
        return false;
    }

    // INSERT DETAIL BELI (kolom sesuai struktur DB)
    $sqlBeli = "
        INSERT INTO tbl_detail_beli
        (no_beli, tgl_beli, id_barang, nama_brg, qty, harga_beli, jml_harga)
        VALUES 
        ('$no', '$tgl', '$kode', '$nama', $qty, $harga, $jmlharga)
    ";

    mysqli_query($koneksi, $sqlBeli) or die('DETAIL ERROR: ' . mysqli_error($koneksi));

    // UPDATE STOK BARANG
    mysqli_query($koneksi,
        "UPDATE tbl_barang 
         SET stock = stock + $qty 
         WHERE id_barang = '$kode'"
    ) or die('STOCK ERROR: ' . mysqli_error($koneksi));

    return true;
}

// ===============================
// DELETE DETAIL PEMBELIAN
// ===============================
function delete($id_barang, $no_beli, $qty) {
    global $koneksi;

    // HAPUS DETAIL
    mysqli_query($koneksi,
        "DELETE FROM tbl_detail_beli 
         WHERE id_barang = '$id_barang' AND no_beli = '$no_beli'"
    ) or die('DELETE DETAIL ERROR: ' . mysqli_error($koneksi));

    // KEMBALIKAN STOK
    mysqli_query($koneksi,
        "UPDATE tbl_barang 
         SET stock = stock - $qty 
         WHERE id_barang = '$id_barang'"
    ) or die('STOCK ROLLBACK ERROR: ' . mysqli_error($koneksi));

    return true;
}

// ===============================
// SIMPAN TRANSAKSI PEMBELIAN
// ===============================
function simpan($data) {
    global $koneksi;

    $noBeli     = mysqli_real_escape_string($koneksi, $data['noBeli']);
    $tgl        = mysqli_real_escape_string($koneksi, $data['tglNota']);
    $supplier   = mysqli_real_escape_string($koneksi, $data['supplier']);  // id_supplier
    $total      = (int) $data['total'];
    $keterangan = mysqli_real_escape_string($koneksi, $data['ketr']);

    // pastikan header sudah ada dulu
    if (!ensureHeaderExists($noBeli, $tgl)) {
        return false;
    }

    // UPDATE HEADER (jangan INSERT lagi)
    $sqlBeli = "
        UPDATE tbl_pembelian SET
            id_supplier = '$supplier',
            tgl_beli    = '$tgl',
            total       = $total,
            keterangan  = '$keterangan'
        WHERE no_beli = '$noBeli'
    ";

    mysqli_query($koneksi, $sqlBeli) or die('SAVE HEADER ERROR: ' . mysqli_error($koneksi));

    return true;
}
