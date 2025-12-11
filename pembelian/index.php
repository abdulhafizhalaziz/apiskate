<?php
session_start();

if (!isset($_SESSION["ssLoginPOS"])) {
    header("location: ../auth/login.php");
    exit();
}

require "../config/config.php";
require "../config/functions.php";
require "../module/mode-beli.php";

$title  = "Transaksi - snackinaja";
require "../template/header.php";
require "../template/navbar.php";
require "../template/sidebar.php";

$msg = isset($_GET['msg']) ? $_GET['msg'] : '';

/* ===============================
   DELETE DETAIL PEMBELIAN
   =============================== */
if ($msg == 'deleted') {
    $idbarang = $_GET['idbrg'];
    $noBeli   = $_GET['noBeli'];
    $qty      = $_GET['qty'];
    $tgl      = $_GET['tgl'];
    
    if (delete($idbarang, $noBeli, $qty)) {
        echo "<script>document.location = '?tgl=$tgl&noBeli=$noBeli';</script>";
    } else {
        echo "<script>alert('Gagal menghapus barang. Silakan coba lagi.');</script>";
    }
}

/* ===============================
   GENERATE NO PEMBELIAN
   =============================== */
$noBeli = isset($_GET['noBeli']) ? $_GET['noBeli'] : generateNo();

/* ===============================
   TAMBAH BARANG KE DETAIL PEMBELIAN
   =============================== */
if (isset($_POST['addbrg'])) {
    $tgl     = $_POST['tglNota'];
    $noBeli  = $_POST['noBeli'];
    $kodeBrg = trim($_POST['kodeBrg']);
    $qty     = trim($_POST['qty']);

    if ($kodeBrg == '' || $qty == '' || $qty <= 0) {
        echo "<script>alert('Barang dan Qty harus diisi dengan benar!');</script>";
    } else {
        if (insert($_POST)) {
            echo "<script>document.location = '?tgl=$tgl&noBeli=$noBeli';</script>";
        }
    }
}

/* ===============================
   SIMPAN TRANSAKSI PEMBELIAN
   =============================== */
if (isset($_POST['simpan'])) {

    $supplier = trim($_POST['supplier']);
    $noBeli   = $_POST['noBeli'];

    $brgDetail = getData("SELECT COUNT(*) AS jml FROM tbl_detail_beli WHERE no_beli = '$noBeli'");
    $jmlBrg = $brgDetail[0]['jml'] ?? 0;

    if ($supplier == '' || $jmlBrg == 0) {
        echo "<script>alert('Supplier harus dipilih dan minimal 1 barang ditambahkan!');</script>";
    } else {
        if (simpan($_POST)) {
            echo "<script>
                alert('Data pembelian berhasil disimpan.');
                document.location = 'index.php?msg=sukses';
            </script>";
        }
    }
}
?>

<div class="content-wrapper">

    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Pembelian Barang</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= $main_url ?>dashboard.php">Home</a></li>
                        <li class="breadcrumb-item active">Tambah Pembelian</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section>
        <div class="container-fluid">

            <form action="" method="post">

                <div class="row">
                    <!-- FORM KIRI -->
                    <div class="col-lg-6">
                        <div class="card card-outline card-warning p-3">

                            <div class="form-group row mb-2">
                                <label class="col-sm-2 col-form-label">No Nota</label>
                                <div class="col-sm-4">
                                    <input type="text" name="noBeli" class="form-control" value="<?= $noBeli ?>">
                                </div>

                                <label class="col-sm-2 col-form-label">Tgl Nota</label>
                                <div class="col-sm-4">
                                    <input type="date" name="tglNota" class="form-control" 
                                        value="<?= isset($_GET['tgl']) ? $_GET['tgl'] : date('Y-m-d') ?>" required>
                                </div>
                            </div>

                            <div class="form-group row mb-2">
                                <label class="col-sm-2 col-form-label">SKU</label>
                                <div class="col-sm-10">
                                    <select id="kodeBrg" class="form-control select2">
                                        <option value="">-- Pilih Barang --</option>

                                        <?php
                                        $barang = getData("SELECT * FROM tbl_barang ORDER BY id_barang ASC");
                                        foreach ($barang as $brg) { ?>
                                            <option value="<?= $brg['id_barang'] ?>"
                                                data-nama="<?= htmlspecialchars($brg['nama_barang']) ?>"
                                                data-stock="<?= $brg['stock'] ?>"
                                                data-harga="<?= $brg['harga_beli'] ?>"
                                                data-satuan="<?= htmlspecialchars($brg['satuan']) ?>">
                                                <?= $brg['id_barang'] . " | " . $brg['nama_barang'] . " (Stok: " . $brg['stock'] . ")" ?>
                                            </option>
                                        <?php } ?>

                                    </select>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- TOTAL PEMBELIAN -->
                    <div class="col-lg-6">
                        <div class="card card-outline card-danger pt-3 px-3 pb-2">
                            <h6 class="font-weight-bold text-right">Total Pembelian</h6>
                            <h1 class="font-weight-bold text-right" style="font-size: 40pt">
                                <?= number_format(totalBeli($noBeli), 0, ',', '.') ?>
                                <input type="hidden" name="total" value="<?= totalBeli($noBeli) ?>">
                            </h1>
                        </div>
                    </div>
                </div>

                <!-- FORM DETAIL BARANG -->
                <div class="card pt-1 pb-2 px-3">

                    <div class="row">
                        <div class="col-lg-4">
                            <input type="hidden" name="kodeBrg">
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
                            <label>Harga Beli</label>
                            <input type="number" name="harga" id="harga" class="form-control form-control-sm" readonly>
                        </div>

                        <div class="col-lg-2">
                            <label>Qty</label>
                            <input type="number" name="qty" id="qty" class="form-control form-control-sm">
                        </div>

                        <div class="col-lg-2">
                            <label>Jumlah Harga</label>
                            <input type="number" name="jmlHarga" id="jmlHarga" class="form-control form-control-sm" readonly>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-sm btn-info btn-block mt-2" name="addbrg">
                        <i class="fas fa-cart-plus"></i> Tambah Barang
                    </button>

                </div>

                <!-- DETAIL TABEL -->
                <div class="card card-outline card-success table-responsive px-2">

                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode Barang</th>
                                <th>Nama Barang</th>
                                <th class="text-right">Harga</th>
                                <th class="text-right">Qty</th>
                                <th class="text-right">Jumlah</th>
                                <th class="text-center">Operasi</th>
                            </tr>
                        </thead>

                        <tbody>
                        <?php
                        $no = 1;
                        $brgDetail = getData("SELECT * FROM tbl_detail_beli WHERE no_beli = '$noBeli'");

                        foreach ($brgDetail as $detail) { ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $detail['id_barang'] ?></td>
                                <td><?= $detail['nama_brg'] ?></td>
                                <td class="text-right"><?= number_format($detail['harga_beli'], 0, ',', '.') ?></td>
                                <td class="text-right"><?= $detail['qty'] ?></td>
                                <td class="text-right"><?= number_format($detail['jml_harga'], 0, ',', '.') ?></td>

                                <td class="text-center">
                                    <a href="?idbrg=<?= $detail['id_barang'] ?>&noBeli=<?= $detail['no_beli'] ?>&qty=<?= $detail['qty'] ?>&tgl=<?= $detail['tgl_beli'] ?>&msg=deleted"
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Yakin hapus barang ini?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>

                    </table>

                </div>

                <!-- SUPPLIER -->
                <div class="row">
                    <div class="col-lg-6 p-2">

                        <label>Supplier</label>
                        <select name="supplier" id="supplier" class="form-control form-control-sm select2">
                            <option value="">-- Pilih Supplier --</option>
                            <?php
                            $suppliers = getData("SELECT * FROM tbl_supplier");
                            foreach ($suppliers as $sup) { ?>
                                <option value="<?= $sup['id_supplier'] ?>"><?= $sup['nama'] ?></option>
                            <?php } ?>
                        </select>

                        <label class="mt-2">Keterangan</label>
                        <textarea name="ketr" class="form-control form-control-sm"></textarea>

                    </div>

                    <div class="col-lg-6 p-">
                        <button type="submit" name="simpan" id="simpan"
                                class="btn btn-primary btn-sm btn-block mt-4" disabled>
                            <i class="fa fa-save"></i> Simpan Pembelian
                        </button>
                    </div>
                </div>

            </form>

        </div>
    </section>

</div>

<script>
function checkSimpanButton() {
    const supplier = document.getElementById('supplier').value;
    const rows     = document.querySelectorAll('table tbody tr');
    const btn      = document.getElementById('simpan');

    btn.disabled = !(supplier !== '' && rows.length > 0);
}

$(document).ready(function () {

    $('.select2').select2({ theme: 'bootstrap4' });

    $('#kodeBrg').on('select2:select', function (e) {
        var data = e.params.data;
        var opt  = $(data.element);

        console.log("Selected:", data.id); // DEBUG

        $('input[name="kodeBrg"]').val(data.id);
        $('input[name="namaBrg"]').val(opt.data('nama'));
        $('input[name="stok"]').val(opt.data('stock'));
        $('input[name="satuan"]').val(opt.data('satuan'));
        $('input[name="harga"]').val(opt.data('harga'));

        let qty = 1;
        $('input[name="qty"]').val(qty);
        $('input[name="jmlHarga"]').val(qty * opt.data('harga'));
    });

    $('#qty').on('input', function () {
        let qty   = parseInt(this.value) || 0;
        let harga = parseInt($('#harga').val()) || 0;
        $('#jmlHarga').val(qty * harga);
    });

    $('#supplier').on('change', checkSimpanButton);
    setInterval(checkSimpanButton, 800);
});
</script>

<?php require "../template/footer.php"; ?>
