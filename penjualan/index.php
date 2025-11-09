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

$noJual = isset($_GET['noJual']) ? $_GET['noJual'] : generateNo();
$tglNotaVal = (isset($_GET['tgl']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['tgl'])) ? $_GET['tgl'] : date('Y-m-d');


if (isset($_POST['addbrg'])) {
    $tgl = !empty($_POST['tglNota']) ? $_POST['tglNota'] : date('Y-m-d');
    $noJual = $_POST['noJual'];
    $barcode = trim($_POST['barcode']);
    $qty = trim($_POST['qty']);
    if ($barcode == '' || $qty == '' || $qty <= 0) {
        echo "<script>alert('Barcode dan Qty harus diisi dengan benar!');</script>";
    } else {
        if (insert($_POST)) {
            echo "<script>document.location = '?tgl=$tgl&noJual=$noJual';</script>";
        }
    }
}


if (isset($_POST['simpan'])) {
    $nota = $_POST['noJual'];
    $bayar = trim($_POST['bayar']);
    if ($bayar == '' || $bayar <= 0) {
        echo "<script>alert('Jumlah bayar harus diisi!');</script>";
    } else {
        if (simpan($_POST)) {
            echo "<script>
            alert('Data penjualan berhasil disimpan.');
            var win = window.open('../report/r-struk.php?nota=$nota', 'Struk Belanja', 'width=260,height=400,left=10,top=10');
            if (win) { win.focus(); }
            window.location = 'index.php';
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
                    <h1 class="m-0">Penjualan Barang</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= $main_url ?>dashboard.php">Beranda</a></li>
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
                                        value="<?= $noJual ?>" readonly>
                                </div>
                                <label for="tglNota" class="col-sm-2 col-form-label">Tgl Nota</label>
                                <div class="col-sm-4">
                                    <input type="date" name="tglNota" class="form-control" id="tglNota"
                                        value="<?= $tglNotaVal ?>" required>
                                </div>
                            </div>
                            <div class="form-group row mb-2">
                                <label for="kodeBrg" class="col-sm-2 col-form-label">SKU</label>
                                <div class="col-sm-10">
                                    <select id="kodeBrg" class="form-control select2" data-placeholder="-- Pilih atau scan barcode untuk mencari barang --">
                                        <option value="">-- Pilih atau scan barcode untuk mencari barang --</option>
                                        <?php
                                        $barang = getData("SELECT * FROM tbl_barang ORDER BY id_barang ASC");
                                        foreach($barang as $brg){  ?>
                                            <option value="<?= $brg['id_barang'] ?>"
                                                data-barcode="<?= $brg['barcode'] ?>"
                                                data-nama="<?= htmlspecialchars($brg['nama_barang']) ?>"
                                                data-stock="<?= $brg['stock'] ?>"
                                                data-harga="<?= $brg['harga_jual'] ?>"
                                                data-satuan="<?= htmlspecialchars($brg['satuan']) ?>"
                                                <?= isset($_GET['pilihbrg']) && $_GET['pilihbrg'] == $brg['id_barang'] ? 'selected' : '' ?>>
                                                <?= $brg['barcode'] . " | " . $brg['id_barang'] . " | " . $brg['nama_barang'] . " (Stok: " . $brg['stock'] . ")" ?>
                                            </option>
                                        <?php } ?>
                                    </select>
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
                                <input type="hidden" name="kodeBrg" value="<?= @$_GET['barcode'] ? $selectBrg['id_barang'] : '' ?>">
                                <input type="hidden" name="barcode" id="barcode" value="<?= @$_GET['barcode'] ?? '' ?>">
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
                                <label for="hargaDisplay">Harga</label>
                                <input type="text" class="form-control form-control-sm" id="hargaDisplay"
                                    value="<?= @$_GET['barcode'] ? number_format($selectBrg['harga_jual'], 0, ',', '.') : '' ?>" readonly>
                                <input type="hidden" name="harga" id="harga"
                                    value="<?= @$_GET['barcode'] ? $selectBrg['harga_jual'] : '' ?>">
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
                                <label for="jmlHargaDisplay">Jumlah Harga</label>
                                <input type="text" class="form-control form-control-sm" id="jmlHargaDisplay"
                                    value="<?= @$_GET['barcode'] ? number_format($selectBrg['harga_jual'], 0, ',', '.') : '' ?>" readonly>
                                <input type="hidden" name="jmlHarga" id="jmlHarga"
                                    value="<?= @$_GET['barcode'] ? $selectBrg['harga_jual'] : '' ?>">
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
                            $brgDetail = getData("SELECT * FROM tbl_transaksi_detail WHERE no_transaksi = '$noJual'");
                            foreach ($brgDetail as $detail) { ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= $detail['kode_barang'] ?></td>
                                    <td><?= $detail['nama_brg'] ?></td>
                                    <td class="text-right"><?= number_format($detail['harga'], 0, ',', '.') ?></td>
                                    <td class="text-right"><?= $detail['qty'] ?></td>
                                    <td class="text-right"><?= number_format($detail['jml_harga'], 0, ',', '.') ?></td>
                                    <td class="text-center">
                                        <a href="?barcode=<?= $detail['kode_barang'] ?>&idJual=<?= $detail['no_transaksi'] ?>&qty=<?= $detail['qty'] ?>&tgl=<?= $tglNotaVal ?>&noJual=<?= $detail['no_transaksi'] ?>&msg=deleted"
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
                                    $customers = getData("SELECT id_relasi, nama FROM tbl_relasi WHERE tipe = 'CUSTOMER' ORDER BY nama ASC");
                                    foreach ($customers as $customer) { ?>
                                        <option value="<?= (int)$customer['id_relasi'] ?>"><?= htmlspecialchars($customer['nama']) ?></option>
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
                            <label for="bayarDisplay" class="col-sm-3 col-form-label">Bayar</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control form-control-sm text-right" id="bayarDisplay" placeholder="Masukkan jumlah bayar">
                                <input type="hidden" name="bayar" id="bayar" value="">
                            </div>
                        </div>
                        <div class="form-group row mb-2">
                            <label for="kembalianDisplay" class="col-sm-3 col-form-label">Kembalian</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control form-control-sm text-right" id="kembalianDisplay" readonly>
                                <input type="hidden" name="kembalian" id="kembalian" value="">
                            </div>
                        </div>
                    </div>
                    <div class="col lg-4 p-2">
            <button type="submit" name="simpan" id="simpan" class="btn btn-primary btn-sm btn-block" disabled><i
                class="fa fa-save"></i> Simpan</button>
                    </div>
            </form>
        </div>
    </section>
    <script>
        document.getElementById('tglNota').addEventListener('change', function () {
            const newUrl = '?tgl=' + this.value + '&noJual=<?= $noJual ?>';
            window.history.replaceState(null, '', newUrl);
        });
        $(document).ready(function() {
            $('#kodeBrg').select2({
                theme: 'bootstrap4',
                width: '100%',
                placeholder: '-- Pilih atau ketik untuk mencari barang --',
                allowClear: true,
                minimumInputLength: 0,
                matcher: function(params, data) {
                    if ($.trim(params.term) === '') {
                        return data;
                    }
                    var term = params.term.toLowerCase();
                    var text = (data.text || '').toLowerCase();
                    var barcode = '';
                    if (data.element) {
                        barcode = ($(data.element).data('barcode') || '').toString().toLowerCase();
                    }
                    if (text.indexOf(term) > -1 || barcode.indexOf(term) > -1) {
                        return data;
                    }
                    return null;
                },
                templateResult: function(item) {
                    if (item.loading) return item.text;
                    if (item.element && $(item.element).data('harga')) {
                        var $container = $(
                            "<div class='select2-result-repository clearfix'>" +
                            "<div class='select2-result-repository__title'></div>" +
                            "<div class='select2-result-repository__description'></div>" +
                            "</div>"
                        );
                        var barcode = $(item.element).data('barcode') || '';
                        var idbarang = item.id || '';
                        var nama = $(item.element).data('nama') || '';
                        var stok = $(item.element).data('stock') || 0;
                        $container.find('.select2-result-repository__title').html('<b>' + idbarang + '</b> | ' + barcode + ' | ' + nama);
                        $container.find('.select2-result-repository__description').text('Harga Jual: Rp ' + numberFormat($(item.element).data('harga')) + ' | Stok: ' + stok);
                        return $container;
                    }
                    return item.text;
                },
                templateSelection: function(item) {
                    if (item.element) {
                        var barcode = $(item.element).data('barcode') || '';
                        return barcode;
                    }
                    return item.text || item.id;
                }
            });

            $('#kodeBrg').on('select2:select', function (e) {
                var data = e.params.data;
                var $option = $(data.element);
                if (data.id && $option.length) {
                    $('input[name="kodeBrg"]').val(data.id);
                    $('input[name="barcode"]').val($option.data('barcode') || '');
                    $('input[name="namaBrg"]').val($option.data('nama') || '');
                    $('input[name="stok"]').val($option.data('stock') || '');
                    $('input[name="satuan"]').val($option.data('satuan') || '');
                    $('input[name="harga"]').val($option.data('harga') || '');
                    $('#hargaDisplay').val(numberFormat($option.data('harga') || 0));
                    var qty = 1;
                    var harga = $option.data('harga') || 0;
                    $('input[name="qty"]').val(qty).focus();
                    $('input[name="jmlHarga"]').val(qty * harga);
                    $('#jmlHargaDisplay').val(numberFormat(qty * harga));
                }
            });
            $('#kodeBrg').on('select2:clear', function () {
                $('input[name="kodeBrg"]').val('');
                $('input[name="barcode"]').val('');
                $('input[name="namaBrg"]').val('');
                $('input[name="stok"]').val('');
                $('input[name="satuan"]').val('');
                $('input[name="harga"]').val('');
                $('input[name="qty"]').val('');
                $('input[name="jmlHarga"]').val('');
                $('#hargaDisplay').val('');
                $('#jmlHargaDisplay').val('');
            });
        });

        function numberFormat(num) {
            return parseInt(num).toLocaleString('id-ID');
        }

        document.getElementById('qty').addEventListener('input', function () {
            const qty = parseInt(this.value) || 0;
            const harga = parseInt(document.getElementById('harga').value) || 0;
            const total = qty * harga;
            document.getElementById('jmlHarga').value = total;
            document.getElementById('jmlHargaDisplay').value = numberFormat(total);
        });

        function checkSimpanButton() {
            const bayar = parseInt(document.getElementById('bayar').value) || 0;
            const total = parseInt(document.getElementById('total').value) || 0;
            const simpanBtn = document.getElementById('simpan');
            if (bayar >= total && bayar > 0 && total > 0) {
                simpanBtn.disabled = false;
            } else {
                simpanBtn.disabled = true;
            }
        }

        document.getElementById('bayarDisplay').addEventListener('input', function () {
            // Ambil hanya digit dari input display
            const rawStr = this.value.replace(/\D/g, '');
            const bayar = rawStr === '' ? 0 : parseInt(rawStr, 10);
            // Set ke input hidden numerik
            document.getElementById('bayar').value = bayar;
            // Format tampilan kembali dengan titik ribuan
            this.value = bayar ? numberFormat(bayar) : '';

            const total = parseInt(document.getElementById('total').value) || 0;
            const kembalian = bayar - total;
            const kembalianSafe = kembalian >= 0 ? kembalian : 0;
            // Set hidden dan display kembalian
            document.getElementById('kembalian').value = kembalianSafe;
            document.getElementById('kembalianDisplay').value = kembalianSafe ? numberFormat(kembalianSafe) : '';

            checkSimpanButton();
        });

        // Inisialisasi tampilan saat load (jika ada nilai sebelumnya)
        window.addEventListener('DOMContentLoaded', function() {
            const bayarHidden = parseInt(document.getElementById('bayar').value) || 0;
            if (bayarHidden > 0) {
                document.getElementById('bayarDisplay').value = numberFormat(bayarHidden);
            }
            const kembalianHidden = parseInt(document.getElementById('kembalian').value) || 0;
            if (kembalianHidden > 0) {
                document.getElementById('kembalianDisplay').value = numberFormat(kembalianHidden);
            }
        });

        window.addEventListener('DOMContentLoaded', function() {
            checkSimpanButton();
        });
    </script>
    <?php require "../template/footer.php"; ?>