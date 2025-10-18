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

// Pastikan ada supplier default untuk header draft saat user belum memilih supplier
function ensureDefaultSupplierId(): int {
    global $koneksi;
    $name = 'Supplier Umum';
    // Cari jika sudah ada
    $res = mysqli_query($koneksi, "SELECT id_relasi FROM tbl_relasi WHERE tipe='SUPPLIER' AND nama='$name' LIMIT 1");
    if ($res && ($row = mysqli_fetch_assoc($res))) {
        return (int)$row['id_relasi'];
    }
    // Buat supplier default
    $sql = "INSERT INTO tbl_relasi (nama, telpon, alamat, deskripsi, tipe) VALUES ('$name', '-', '-', 'Supplier default otomatis', 'SUPPLIER')";
    mysqli_query($koneksi, $sql);
    return (int)mysqli_insert_id($koneksi);
}

function insert($data){
    global $koneksi;

    $no       = mysqli_real_escape_string($koneksi, $data['noBeli']);
    $tglIn    = $data['tglNota'] ?? '';
    $tgl      = mysqli_real_escape_string($koneksi, (preg_match('/^\d{4}-\d{2}-\d{2}$/', $tglIn) ? $tglIn : date('Y-m-d')));
    $kode     = mysqli_real_escape_string($koneksi, $data['kodeBrg']);
    $nama     = mysqli_real_escape_string($koneksi, $data['namaBrg']);
    $qty      = mysqli_real_escape_string($koneksi, $data['qty']);
    $harga    = mysqli_real_escape_string($koneksi, $data['harga']);
    $jmlharga = mysqli_real_escape_string($koneksi, $data['jmlHarga']);
    $supplier = isset($data['supplier']) ? (int)$data['supplier'] : 0;
    $keterangan = mysqli_real_escape_string($koneksi, $data['ketr'] ?? '');

    if (empty($qty)) {
        echo "<script>alert('Qty barang tidak boleh kosong');</script>";
        return false;
    }

    // Pastikan header transaksi sudah ada (karena FK pada detail mengarah ke header)
    $cekHeader = mysqli_query($koneksi, "SELECT 1 FROM tbl_transaksi WHERE no_transaksi = '$no' LIMIT 1");
    if (!$cekHeader || mysqli_num_rows($cekHeader) === 0) {
        // Header belum ada; jika supplier belum dipilih, gunakan supplier default
        if ($supplier <= 0) {
            $supplier = ensureDefaultSupplierId();
        } else {
            // Validasi supplier tipe SUPPLIER jika user sudah memilih
            $cekSupp = mysqli_query($koneksi, "SELECT 1 FROM tbl_relasi WHERE id_relasi = $supplier AND tipe = 'SUPPLIER' LIMIT 1");
            if (!$cekSupp || mysqli_num_rows($cekSupp) === 0) {
                $_SESSION['last_error'] = 'Supplier tidak valid/bukan SUPPLIER.';
                echo "<script>alert('Supplier tidak valid/bukan SUPPLIER.');</script>";
                return false;
            }
        }
        // Buat header awal dengan total 0, tipe BELI (supplier bisa default)
        $sqlHdr = "INSERT INTO tbl_transaksi (no_transaksi, tgl_transaksi, tipe_transaksi, id_relasi, total, keterangan)
                    VALUES ('$no', STR_TO_DATE('$tgl','%Y-%m-%d'), 'BELI', $supplier, 0, '$keterangan')";
        $okHdr = mysqli_query($koneksi, $sqlHdr);
        if (!$okHdr) {
            $_SESSION['last_error'] = 'Gagal membuat header transaksi: '.mysqli_error($koneksi);
            echo "<script>alert('Gagal membuat header transaksi.');</script>";
            return false;
        }
    }
    $cekbrg = mysqli_query($koneksi, "SELECT * FROM tbl_transaksi_detail WHERE no_transaksi = '$no' AND kode_barang = '$kode'");
    if (mysqli_num_rows($cekbrg)) {
        echo "<script>alert('Barang sudah ada, hapus dulu jika ingin mengubah qty.');</script>";
        return false;
    }

    // Skema baru: detail tidak memiliki kolom tgl_transaksi
    $sqlBeli = "INSERT INTO tbl_transaksi_detail (no_transaksi, kode_barang, nama_brg, qty, harga, jml_harga) VALUES ('$no', '$kode', '$nama', $qty, $harga, $jmlharga)";
    $okDet = mysqli_query($koneksi, $sqlBeli);
    if (!$okDet) {
        $_SESSION['last_error'] = 'Gagal menambah detail: '.mysqli_error($koneksi);
        return false;
    }
    // Tambah stok barang saat pembelian ditambahkan
    $upd = mysqli_query($koneksi, "UPDATE tbl_barang SET stock = stock + $qty WHERE id_barang = '$kode'");
    if (!$upd) {
        $_SESSION['last_error'] = 'Detail masuk, tetapi gagal update stok: '.mysqli_error($koneksi);
    }
    return true;
}

function delete($idbrg, $idbeli, $qty){
    global $koneksi;

    $sqlDel = "DELETE FROM tbl_transaksi_detail WHERE kode_barang = '$idbrg' AND no_transaksi = '$idbeli'";
    $okDel = mysqli_query($koneksi, $sqlDel);

    // Update stock sudah otomatis via trigger, baris berikut bisa dihapus jika sudah pakai trigger
    if ($okDel) {
        // Kurangi stok kembali saat detail pembelian dihapus
        mysqli_query($koneksi, "UPDATE tbl_barang SET stock = GREATEST(stock - $qty, 0) WHERE id_barang = '$idbrg'");
        // Jika tidak ada detail tersisa, hapus header pembelian
        $resCnt = mysqli_query($koneksi, "SELECT COUNT(*) AS jml FROM tbl_transaksi_detail WHERE no_transaksi = '$idbeli'");
        $rowCnt = $resCnt ? mysqli_fetch_assoc($resCnt) : ['jml' => 0];
        $jml = (int)($rowCnt['jml'] ?? 0);
        if ($jml === 0) {
            mysqli_query($koneksi, "DELETE FROM tbl_transaksi WHERE no_transaksi = '$idbeli' AND tipe_transaksi = 'BELI'");
        }
    }

    return mysqli_affected_rows($koneksi);
}

function simpan($data){
    global $koneksi;

    $noBeli     = mysqli_real_escape_string($koneksi, $data['noBeli']);
    $tglIn      = $data['tglNota'] ?? '';
    // Fallback tanggal jika kosong/tidak valid -> hari ini
    $tglPattern = '/^\d{4}-\d{2}-\d{2}$/';
    $tgl        = mysqli_real_escape_string($koneksi, (preg_match($tglPattern, $tglIn) ? $tglIn : date('Y-m-d')));
    // Jangan percaya input hidden total; hitung ulang dari detail di server
    $resTotal   = mysqli_query($koneksi, "SELECT COALESCE(SUM(jml_harga),0) AS total FROM tbl_transaksi_detail WHERE no_transaksi = '$noBeli'");
    $rowTotal   = $resTotal ? mysqli_fetch_assoc($resTotal) : ['total' => 0];
    $total      = $rowTotal['total'] ?? 0;
    // Pada skema baru, field yang disimpan adalah id_relasi (FK ke tbl_relasi)
    $supplier   = (int) ($data['supplier'] ?? 0); // id_relasi
    $keterangan = mysqli_real_escape_string($koneksi, $data['ketr'] ?? '');

    // Validasi supplier benar dan bertipe SUPPLIER
    if ($supplier <= 0) {
        $_SESSION['last_error'] = 'Supplier tidak valid';
        return false;
    }
    $cekSupp = mysqli_query($koneksi, "SELECT 1 FROM tbl_relasi WHERE id_relasi = $supplier AND tipe = 'SUPPLIER' LIMIT 1");
    if (!$cekSupp || mysqli_num_rows($cekSupp) === 0) {
        $_SESSION['last_error'] = 'Supplier tidak ditemukan atau bukan tipe SUPPLIER';
        return false;
    }

    // Validasi: harus ada minimal 1 detail untuk no_transaksi ini
    $resCntDet = mysqli_query($koneksi, "SELECT COUNT(*) AS jml FROM tbl_transaksi_detail WHERE no_transaksi = '$noBeli'");
    $rowCntDet = $resCntDet ? mysqli_fetch_assoc($resCntDet) : ['jml' => 0];
    if ((int)($rowCntDet['jml'] ?? 0) === 0) {
        // Tidak ada detail, pastikan header yatim dibersihkan dan batalkan simpan
        mysqli_query($koneksi, "DELETE FROM tbl_transaksi WHERE no_transaksi = '$noBeli' AND tipe_transaksi = 'BELI'");
        $_SESSION['last_error'] = 'Tidak ada detail barang pada transaksi ini.';
        return false;
    }

    // Simpan header transaksi; gunakan upsert agar aman jika nomor sudah pernah dibuat
    $sqlBeli = "INSERT INTO tbl_transaksi (no_transaksi, tgl_transaksi, tipe_transaksi, id_relasi, total, keterangan)
                VALUES ('$noBeli', STR_TO_DATE('$tgl','%Y-%m-%d'), 'BELI', $supplier, $total, '$keterangan')
                ON DUPLICATE KEY UPDATE 
                    tgl_transaksi = STR_TO_DATE('$tgl','%Y-%m-%d'),
                    id_relasi = $supplier,
                    total = $total,
                    keterangan = '$keterangan'";
    $ok = mysqli_query($koneksi, $sqlBeli);
    if (!$ok) {
        $_SESSION['last_error'] = mysqli_error($koneksi);
        return false;
    }
    return true;
}
