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

$msg = isset($_GET['msg']) ? $_GET['msg'] : '';

if ($msg == 'deleted') {
    $barcode = $_GET['barcode'];
    $idJual = $_GET['idJual'];
    $qty = $_GET['qty'];
    $tgl = $_GET['tgl'];
    
    if (delete($barcode, $idJual, $qty)) {
        echo "<script>
        alert('Barang berhasil dihapus.');
        document.location = '?tgl=$tgl';
        </script>";
    } else {
        echo "<script>alert('Gagal menghapus barang. Silakan coba lagi.');</script>";
    }
}


$kode = @$_GET['barcode'] ?? '';

if ($kode) {
    $tgl = @$_GET['tgl'];
    $dataBrg = mysqli_query($koneksi, "SELECT * FROM tbl_barang WHERE barcode = '$kode'");
    $selectBrg = mysqli_fetch_assoc($dataBrg);
    if (!mysqli_num_rows($dataBrg)) {
        echo "<script>
        alert('Barang tidak ditemukan!');
        document.location='?tgl=$tgl'; 
        </script>";

        $selectBrg = [];
    }
}

$noJual = generateNo();

if (isset($_POST['addbrg'])) {
    $tgl = $_POST['tglNota'];
    if (insert($_POST)) {
        echo "<script>document.location = '?tgl=$tgl';</script>";
    }
}
if (isset($_POST['simpan'])) {
    $nota = $_POST['noJual'];
    if (simpan($_POST)) {
        echo "<script>
        alert('Data penjualan berhasil disimpan.');
        document.location = 'index.php?msg=sukses';
        </script>";
    }
}
?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Penjualan Barang</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= $main_url ?>Dashboard.php">Home</a></li>
                        <li class="breadcrumb-item active">Tambah Penjualan</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section>
        <div class="container-fluid">
            <form action="" method="post">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="card card-outline card-warning p-3">
                            <div class="form-group row mb-2">
                                <label for="noNota" class="col-sm-2 col-form-label">No Nota</label>
                                <div class="col-sm-4">
                                    <input type="text" name="noJual" class="form-control" id="noNota"
                                        value="<?= $noJual ?>">
                                </div>
                                <label for="tglNota" class="col-sm-2 col-form-label">Tgl Nota</label>
                                <div class="col-sm-4">
                                    <input type="date" name="tglNota" class="form-control" id="tglNota"
                                        value="<?= isset($_GET['tgl']) ? $_GET['tgl'] : date('Y-m-d') ?>" required>
                                </div>
                            </div>
                            <div class="form-group row mb-2">
                                <label for="barcode" class="col-sm-2 col-form-label">Barcode</label>
                                <div class="col-sm-10 input-group">
                                    <input type="text" name="barcode" class="form-control" id="barcode"
                                        placeholder="Masukkan barcode barang"
                                        value="<?= isset($_GET['barcode']) ? $_GET['barcode'] : '' ?>">
                                    <div class="input-group-append">
                                        <span class="input-group-text" id="icon-barcode">
                                            <i class="fas fa-barcode"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card card-outline card-danger pt-3 px-3 pb-2">
                            <h6 class="font-weight-bold text-right">Total Penjualan</h6>
                            <h1 class="font-weight-bold text-right" style="font-size: 40pt">
                                <input type="hidden" name="total" id="total" value="<?= totalJual($noJual) ?>">
                                <?= number_format(totalJual($noJual), 0, ',', '.') ?>
                            </h1>
                        </div>
                    </div>
                </div>
                <div class="card pt-1 pb-2 px-3">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <input type="hidden" value="<?= @$_GET['barcode'] ? $selectBrg['nama_barang'] : '' ?>"
                                    name="kodeBrg">
                                <label for="namaBrg">Nama Barang</label>
                                <input type="text" name="namaBrg" class="form-control form-control-sm" id="namaBrg"
                                    value="<?= @$_GET['barcode'] ? $selectBrg['nama_barang'] : '' ?>" readonly>
                            </div>
                        </div>
                        <div class="col-lg-1">
                            <div class="form-group">
                                <label for="stok">Stok</label>
                                <input type="number" name="stok" class="form-control form-control-sm" id="stok"
                                    value="<?= @$_GET['barcode'] ? $selectBrg['stock'] : '' ?>" readonly>
                            </div>
                        </div>
                        <div class="col-lg-1">
                            <div class="form-group">
                                <label for="satuan">Satuan</label>
                                <input type="text" name="satuan" class="form-control form-control-sm" id="satuan"
                                    value="<?= @$_GET['barcode'] ? $selectBrg['satuan'] : '' ?>" readonly>
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group">
                                <label for="harga">Harga</label>
                                <input type="number" name="harga" class="form-control form-control-sm" id="harga"
                                    value="<?= @$_GET['barcode'] ? $selectBrg['harga_jual'] : '' ?>" readonly>
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group">
                                <label for="qty">Qty</label>
                                <input type="number" name="qty" class="form-control form-control-sm" id="qty"
                                    value="<?= @$_GET['barcode'] ? 1 : '' ?>">
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group">
                                <label for="jmlHarga">Jumlah Harga</label>
                                <input type="number" name="jmlHarga" class="form-control form-control-sm" id="jmlHarga"
                                    value="<?= @$_GET['barcode'] ? $selectBrg['harga_jual'] : '' ?>" readonly>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-sm btn-info btn-block" name="addbrg"><i
                            class="fas fa-cart-plus fa-sm"></i> Tambah Barang</button>
                </div>
                <div class="card card-outline card-success table-responsive px-2">
                    <table class="table table-sm table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Barcode</th>
                                <th>Nama Barang</th>
                                <th class="text-right">Harga</th>
                                <th class="text-right">Qty</th>
                                <th class="text-right">Jumlah Harga</th>
                                <th class="text-center">Operasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            $brgDetail = getData("SELECT * FROM tbl_jual_detail WHERE no_jual = '$noJual'");
                            foreach ($brgDetail as $detail) { ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= $detail['barcode'] ?></td>
                                    <td><?= $detail['nama_brg'] ?></td>
                                    <td class="text-right"><?= number_format($detail['harga_jual'], 0, ',', '.') ?></td>
                                    <td class="text-right"><?= $detail['qty'] ?></td>
                                    <td class="text-right"><?= number_format($detail['jml_harga'], 0, ',', '.') ?></td>
                                    <td class="text-center">
                                        <a href="?barcode=<?= $detail['barcode'] ?>&idJual=<?= $detail['no_jual'] ?>&qty=<?= $detail['qty'] ?>&tgl=<?= $detail['tgl_jual'] ?>&msg=deleted"
                                            class="btn btn-sm btn-danger"
                                            onclick="return confirm('Anda yakin akan menghapus barang ini ?')"><i
                                                class="fas fa-trash"></i></a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <div class="row">
                    <div class="col-lg-4 p-2">
                        <div class="form-group row mb-2">
                            <label for="customer" class="col-sm-3 col-form-label col-form-label-sm">Customer</label>
                            <div class="col-sm-9">
                                <select name="customer" id="customer" class="form-control form-control-sm">
                                    <?php
                                    $customers = getData("SELECT * FROM tbl_customer");
                                    foreach ($customers as $customer) { ?>
                                        <option value="<?= $customer['nama'] ?>"><?= $customer['nama'] ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row mb-2">
                            <label for="ktr" class="col-sm-3 col-form-label">Keterangan</label>
                            <div class="col sm-9">
                                <textarea name="ketr" id="ketr" class="form-control form-control-sm"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 py-2 px-3">
                        <div class="form-group row mb-2">
                            <label for="" class="col-sm-3 col-form-label">Bayar</label>
                            <div class="col-sm-9">
                                <input type="number" name="bayar" class="form-control form-control-sm text-right"
                                    id="bayar" placeholder="Masukkan jumlah bayar">
                            </div>
                        </div>
                        <div class="form-group row mb-2">
                            <label for="kembalian" class="col-sm-3 col-form-label">Kembalian</label>
                            <div class="col-sm-9">
                                <input type="number" name="kembalian" class="form-control form-control-sm text-right"
                                    id="kembalian" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="col lg-4 p-2">
                        <button type="submit" name="simpan" id="simpan" class="btn btn-primary btn=sm btn-block"><i
                                class="fa fa-save"></i> Simpan</button>
                    </div>
            </form>
        </div>
    </section>
    <script>
        document.getElementById('barcode').addEventListener('change', function() {
            window.location.href = '?barcode=' + document.getElementById('barcode').value + '&tgl=' + document.getElementById('tglNota').value;
        });

        document.getElementById('qty').addEventListener('input', function () {
            const qty = parseInt(this.value);
            const harga = parseInt(document.getElementById('harga').value);
            document.getElementById('jmlHarga').value = qty * harga;
        });

        document.getElementById('bayar').addEventListener('input', function () {
            const bayar = parseInt(this.value) || 0;
            const total = parseInt(document.getElementById('total').value) || 0;
            const kembalian = bayar - total;
            document.getElementById('kembalian').value = kembalian >= 0 ? kembalian : 0;
        });
    </script>
    <?php require "../template/footer.php"; ?>