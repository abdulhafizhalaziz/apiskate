<?php
session_start();
if (!isset($_SESSION["ssLoginPOS"])) {
    header("location: ../auth/login.php");
    exit();
}

require "../config/config.php";
require "../config/functions.php";
require "../module/mode-jual.php";

$title = "Transaksi - snackinaja";
require "../template/header.php";
require "../template/navbar.php";
require "../template/sidebar.php";

/* ============================================================
   AMBIL PARAMETER URL
============================================================== */
$noJual = isset($_GET['noJual']) ? $_GET['noJual'] : generateNo();
$tglNow = isset($_GET['tgl']) ? $_GET['tgl'] : date('Y-m-d');

/* ============================================================
   DELETE ITEM
============================================================== */
if (isset($_GET['msg']) && $_GET['msg'] == 'deleted') {

    $idBrg  = $_GET['idBrg'];
    $qty    = $_GET['qty'];
    $no     = $_GET['noJual'];
    $tgl    = $_GET['tgl'];

    delete($idBrg, $no, $qty);

    echo "<script>
        alert('Barang berhasil dihapus!');
        document.location='?tgl=$tgl&noJual=$no';
    </script>";
    exit;
}

/* ============================================================
   TAMBAH BARANG
============================================================== */
if (isset($_POST['addbrg'])) {

    $tgl     = $_POST['tglNota'];
    $noJual  = $_POST['noJual'];
    $kodeBrg = trim($_POST['kodeBrg']);
    $qty     = trim($_POST['qty']);

    // ========== DEBUG KE BROWSER ==========
    echo "
    <script>
        console.log('===== DEBUG ADD BARANG =====');
        console.log('tgl       : $tgl');
        console.log('noJual    : $noJual');
        console.log('kodeBrg   : $kodeBrg');
        console.log('qty       : $qty');
    </script>
    ";
    // =======================================

    if ($kodeBrg == '' || $qty == '' || $qty <= 0) {
        echo "<script>alert('Barang dan Qty harus diisi dengan benar!');</script>";
    } else {
       if (insert($_POST)) {

    header("Location: ?tgl=$tgl&noJual=$noJual");
    exit();

} else {
    echo "<script>console.log('INSERT STATUS: GAGAL');</script>";
}

    }
}


/* ============================================================
   SIMPAN TRANSAKSI
============================================================== */
if (isset($_POST['simpan'])) {

    if ($_POST['bayar'] < $_POST['total']) {
        echo "<script>alert('Pembayaran kurang!');</script>";
    } else {

        simpan($_POST);

        $nota = $_POST['noJual'];

        echo "<script>
            alert('Transaksi berhasil disimpan!');
            window.open('../report/r-struk.php?nota=$nota',
                        'Struk',
                        'width=300,height=500,left=50');
            location.href='index.php';
        </script>";
        exit;
    }
}
?>

<div class="content-wrapper">

<div class="content-header">
    <div class="container-fluid">
        <h1 class="m-0 mb-3">Penjualan Barang</h1>
    </div>
</div>

<section>
<div class="container-fluid">
<form method="post">

<div class="row">

<!-- ================= LEFT ================= -->
<div class="col-lg-6">
<div class="card card-outline card-warning p-3">

    <div class="form-group row mb-2">
        <label class="col-sm-2 col-form-label">No Nota</label>
        <div class="col-sm-4">
            <input type="text" name="noJual" class="form-control"
                   readonly value="<?= $noJual ?>">
        </div>

        <label class="col-sm-2 col-form-label">Tanggal</label>
        <div class="col-sm-4">
            <input type="date" name="tglNota" class="form-control"
                   value="<?= $tglNow ?>" required>
        </div>
    </div>

    <div class="form-group row mb-2">
        <label class="col-sm-2 col-form-label">Barang</label>
        <div class="col-sm-10">

            <select id="pilihBrg" class="form-control select2">
                <option value="">-- Pilih Barang --</option>

                <?php
                $barang = getData("SELECT * FROM tbl_barang ORDER BY nama_barang ASC");
                foreach ($barang as $b) { ?>
                    <option value="<?= $b['id_barang'] ?>"
                        data-barcode="<?= $b['barcode'] ?>"
                        data-nama="<?= $b['nama_barang'] ?>"
                        data-stock="<?= $b['stock'] ?>"
                        data-satuan="<?= $b['satuan'] ?>"
                        data-harga="<?= $b['harga_jual'] ?>">
                        <?= $b['barcode'] . " | " . $b['nama_barang'] . " | Stok:" . $b['stock'] ?>
                    </option>
                <?php } ?>
            </select>

        </div>
    </div>

</div>
</div>

<!-- ================= RIGHT ================= -->
<div class="col-lg-6">
<div class="card card-outline card-danger p-3">
    <h6 class="text-right">Total Penjualan</h6>
    <h1 class="text-right font-weight-bold">
        <?= number_format(totalJual($noJual)) ?>
    </h1>
    <input type="hidden" name="total" id="total" value="<?= totalJual($noJual) ?>">
</div>
</div>

</div> <!-- END ROW -->

<!-- ==========================================
     FORM DETAIL BARANG
========================================== -->
<div class="card p-3 mb-2">

    <input type="hidden" id="kodeBrg" name="kodeBrg">
    <input type="hidden" name="barcode">

    <div class="row">

        <div class="col-lg-4">
            <label>Nama Barang</label>
            <input type="text" name="namaBrg" class="form-control form-control-sm" readonly>
        </div>

        <div class="col-lg-1">
            <label>Stok</label>
            <input type="number" name="stok" class="form-control form-control-sm" readonly>
        </div>

        <div class="col-lg-1">
            <label>Satuan</label>
            <input type="text" name="satuan" class="form-control form-control-sm" readonly>
        </div>

        <div class="col-lg-2">
            <label>Harga</label>
            <input type="number" name="harga" id="harga" class="form-control form-control-sm" readonly>
        </div>

        <div class="col-lg-2">
            <label>Qty</label>
            <input type="number" name="qty" id="qty" class="form-control form-control-sm">
        </div>

        <div class="col-lg-2">
            <label>Jumlah</label>
            <input type="number" name="jmlHarga" id="jmlHarga" class="form-control form-control-sm" readonly>
        </div>

    </div>

    <button type="submit" name="addbrg" class="btn btn-info btn-sm btn-block mt-2">
        <i class="fas fa-cart-plus"></i> Tambah Barang
    </button>

</div>

<!-- ==========================================
     TABLE DETAIL
========================================== -->
<div class="card card-outline card-success p-2 table-responsive">

<table class="table table-sm table-hover">
<thead>
<tr>
    <th>No</th>
    <th>Barcode</th>
    <th>ID Barang</th>
    <th>Nama</th>
    <th class="text-right">Harga</th>
    <th class="text-right">Qty</th>
    <th class="text-right">Subtotal</th>
    <th class="text-center">Aksi</th>
</tr>
</thead>

<tbody>
<?php
$no = 1;
$detail = getData("SELECT * FROM tbl_detail_jual WHERE no_jual='$noJual'");
foreach ($detail as $d) { ?>
<tr>
    <td><?= $no++ ?></td>
    <td><?= $d['barcode'] ?></td>
    <td><?= $d['id_barang'] ?></td>
    <td><?= $d['nama_brg'] ?></td>
    <td class="text-right"><?= number_format($d['harga_jual']) ?></td>
    <td class="text-right"><?= $d['qty'] ?></td>
    <td class="text-right"><?= number_format($d['jml_harga']) ?></td>
    <td class="text-center">
        <a href="?msg=deleted&idBrg=<?= $d['id_barang'] ?>&noJual=<?= $noJual ?>&qty=<?= $d['qty'] ?>&tgl=<?= $tglNow ?>"
           class="btn btn-danger btn-sm">
            <i class="fas fa-trash"></i>
        </a>
    </td>
</tr>
<?php } ?>
</tbody>

</table>

</div>

<!-- ==========================================
     CUSTOMER & PEMBAYARAN
========================================== -->
<div class="row mt-3">

<div class="col-lg-6">
    <label>Customer</label>
    <select name="customer" class="form-control form-control-sm">
        <?php
        $cus = getData("SELECT * FROM tbl_customer");
        foreach ($cus as $c) { ?>
            <option value="<?= $c['id_customer'] ?>"><?= $c['nama'] ?></option>
        <?php } ?>
    </select>

    <label class="mt-2">Keterangan</label>
    <textarea name="ketr" class="form-control form-control-sm"></textarea>
</div>

<div class="col-lg-6">
    <label>Bayar</label>
    <input type="number" name="bayar" id="bayar" class="form-control form-control-sm text-right">

    <label class="mt-2">Kembalian</label>
    <input type="number" name="kembalian" id="kembalian" class="form-control form-control-sm text-right" readonly>

    <button type="submit" name="simpan" id="simpan"
            class="btn btn-primary btn-sm btn-block mt-3" disabled>
        <i class="fa fa-save"></i> Simpan Transaksi
    </button>
</div>

</div>

</form>
</div>
</section>

</div>

<!-- ==========================================
     JAVASCRIPT
========================================== -->
<script>
$('.select2').select2({ theme:'bootstrap4' });

// Saat barang dipilih
$('#pilihBrg').on('change', function() {

    let opt = $(this).find('option:selected');

    $('input[name="kodeBrg"]').val($(this).val());
    $('input[name="barcode"]').val(opt.data('barcode'));
    $('input[name="namaBrg"]').val(opt.data('nama'));
    $('input[name="stok"]').val(opt.data('stock'));
    $('input[name="satuan"]').val(opt.data('satuan'));
    $('input[name="harga"]').val(opt.data('harga'));

    $('#qty').val(1);
    $('#jmlHarga').val(opt.data('harga'));
});

// Hitung subtotal
$('#qty').on('input', function(){
    $('#jmlHarga').val( (parseInt($(this).val()) || 0) * (parseInt($('#harga').val()) || 0) );
});

// Enable tombol simpan
$('#bayar').on('input', function(){
    let bayar = parseInt($(this).val()) || 0;
    let total = parseInt($('#total').val()) || 0;

    $('#kembalian').val(bayar - total);
    $('#simpan').prop('disabled', !(bayar >= total && total > 0));
});
</script>

<?php require "../template/footer.php"; ?>
