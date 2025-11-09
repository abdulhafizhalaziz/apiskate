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

if ($msg == 'deleted') {
    $idbrg = $_GET['idbrg'];
    $idbeli = $_GET['idbeli'];
    $qty = $_GET['qty'];
    $tgl = $_GET['tgl'];
    
    if (delete($idbrg, $idbeli, $qty)) {
        echo "<script>document.location = '?tgl=$tgl&noBeli=$idbeli';</script>";
    } else {
        echo "<script>alert('Gagal menghapus barang. Silakan coba lagi.');</script>";
    }
}


$kode = isset($_GET['pilihbrg']) ? $_GET['pilihbrg'] : '';
if ($kode) {
    $selectBrg = getData("SELECT * FROM tbl_barang WHERE id_barang = '$kode'")[0];
}


$noBeli = isset($_GET['noBeli']) ? $_GET['noBeli'] : generateNo();

$tglNotaVal = (isset($_GET['tgl']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['tgl'])) ? $_GET['tgl'] : date('Y-m-d');

if (isset($_POST['addbrg'])) {
    $tgl = !empty($_POST['tglNota']) ? $_POST['tglNota'] : date('Y-m-d');
    $noBeli = $_POST['noBeli'];
    $kodeBrg = trim($_POST['kodeBrg']);
    $qty = trim($_POST['qty']);
    if ($kodeBrg == '' || $qty == '' || $qty <= 0) {
        echo "<script>alert('Barang dan Qty harus diisi dengan benar!');</script>";
    } else {
        if (insert($_POST)) {
            echo "<script>document.location = '?tgl=$tgl&noBeli=$noBeli';</script>";
        }
    }
}
if (isset($_POST['simpan'])) {
    $supplier = trim($_POST['supplier']);
    $noBeli = $_POST['noBeli'];
    $brgDetail = getData("SELECT COUNT(*) as jml FROM tbl_transaksi_detail WHERE no_transaksi = '$noBeli'");
    $jmlBrg = $brgDetail[0]['jml'] ?? 0;
    if ($supplier == '' || $jmlBrg == 0) {
        echo "<script>alert('Supplier harus dipilih dan minimal 1 barang ditambahkan!');</script>";
    } else {
        if (simpan($_POST)) {
            echo "<script>\n                alert('Data pembelian berhasil disimpan.');\n                document.location = 'index.php?msg=sukses';\n            </script>";
        } else {
            echo "<script>\n                alert('Gagal menyimpan pembelian. Pastikan supplier valid dan minimal 1 barang ditambahkan.');\n            </script>";
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
                        <li class="breadcrumb-item"><a href="<?= $main_url ?>dashboard.php">Beranda</a></li>
                        <li class="breadcrumb-item active">Tambah Pembelian</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section>
        <div class="container-fluid">
            <form action="" method="post">
                <?php if (!empty($_SESSION['last_error'])): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($_SESSION['last_error']); unset($_SESSION['last_error']); ?>
                </div>
                <?php endif; ?>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="card card-outline card-warning p-3">
                            <div class="form-group row mb-2">
                                <label for="noNota" class="col-sm-2 col-form-label">No Nota</label>
                                <div class="col-sm-4">
                                    <input type="text" name="noBeli" class="form-control" id="noNota" value="<?= $noBeli ?>" readonly>
                                </div>
                                <label for="tglNota" class="col-sm-2 col-form-label">Tgl Nota</label>
                                <div class="col-sm-4">
                                    <input type="date" name="tglNota" class="form-control" id="tglNota" value="<?= $tglNotaVal ?>" required>
                                </div>
                            </div>
                            <div class="form-group row mb-2">
                                <label for="kodeBrg" class="col-sm-2 col-form-label">SKU</label>
                                <div class="col-sm-10">
                                    <select id="kodeBrg" class="form-control select2" data-placeholder="-- Pilih atau ketik untuk mencari barang --">
                                        <option value="">-- Pilih atau ketik untuk mencari barang --</option>
                                        <?php
                                        $barang = getData("SELECT * FROM tbl_barang ORDER BY id_barang ASC");
                                        foreach($barang as $brg){  ?>
                                            <option value="<?= $brg['id_barang'] ?>" 
                                                    data-nama="<?= htmlspecialchars($brg['nama_barang']) ?>"
                                                    data-stock="<?= $brg['stock'] ?>"
                                                    data-harga="<?= $brg['harga_beli'] ?>"
                                                    data-satuan="<?= htmlspecialchars($brg['satuan']) ?>"
                                                    <?= isset($_GET['pilihbrg']) && $_GET['pilihbrg'] == $brg['id_barang'] ? 'selected' : '' ?>>
                                                <?= $brg['id_barang'] . " | " . $brg['nama_barang'] . " (Stok: " . $brg['stock'] . ")" ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card card-outline card-danger pt-3 px-3 pb-2">
                            <h6 class="font-weight-bold text-right">Total Pembelian</h6>
                            <h1 class="font-weight-bold text-right" style="font-size: 40pt">
                                <input type="hidden" name="total" value="<?= totalBeli($noBeli) ?>">
                                <?= number_format(totalBeli($noBeli), 0, ',', '.') ?>
                            </h1>
                        </div>
                    </div>
                </div>
                <div class="card pt-1 pb-2 px-3">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <input type="hidden" value="<?= $kode ?>" name="kodeBrg" id="kodeBrgHidden" required>
                                <label for="namaBrg">Nama Barang</label>
                                <input type="text" name="namaBrg" class="form-control form-control-sm" id="namaBrg" value="<?= $selectBrg['nama_barang'] ?? '' ?>" readonly>
                            </div>
                        </div>
                        <div class="col-lg-1">
                            <div class="form-group">
                                <label for="stok">Stok</label>
                                <input type="number" name="stok" class="form-control form-control-sm" id="stok" value="<?= $selectBrg['stock'] ?? '' ?>" readonly>
                            </div>
                        </div>
                        <div class="col-lg-1">
                            <div class="form-group">
                                <label for="satuan">Satuan</label>
                                <input type="text" name="satuan" class="form-control form-control-sm" id="satuan" value="<?= $selectBrg['satuan'] ?? '' ?>" readonly>
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group">
                                <label for="harga">Harga</label>
                                <input type="number" name="harga" class="form-control form-control-sm" id="harga" value="<?= $selectBrg['harga_beli'] ?? '' ?>" readonly>
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group">
                                <label for="qty">Qty</label>
                                <input type="number" name="qty" class="form-control form-control-sm" id="qty" value="<?= $kode ? 1 : '' ?>">
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group">
                                <label for="jmlHarga">Jumlah Harga</label>
                                <input type="number" name="jmlHarga" class="form-control form-control-sm" id="jmlHarga" value="<?= $selectBrg['harga_beli'] ?? '' ?>" readonly>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-sm btn-info btn-block" id="addbrgBtn" name="addbrg"><i class="fas fa-cart-plus fa-sm"></i> Tambah Barang</button>
                </div>
                <div class="card card-outline card-success table-responsive px-2">
                    <table class="table table-sm table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode Barang</th>
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
                            $brgDetail = getData("SELECT * FROM tbl_transaksi_detail WHERE no_transaksi = '$noBeli'");
                            foreach ($brgDetail as $detail) { ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= $detail['kode_barang'] ?></td>
                                    <td><?= $detail['nama_brg'] ?></td>
                                    <td class="text-right"><?= number_format($detail['harga'], 0, ',', '.') ?></td>
                                    <td class="text-right"><?= $detail['qty'] ?></td>
                                    <td class="text-right"><?= number_format($detail['jml_harga'], 0, ',', '.') ?></td>
                                    <td class="text-center">
                                        <a href="?idbrg=<?= $detail['kode_barang'] ?>&idbeli=<?= $detail['no_transaksi'] ?>&qty=<?= $detail['qty'] ?>&tgl=<?= $_GET['tgl'] ?? '' ?>&msg=deleted" class="btn btn-sm btn-danger" onclick="return confirm('Anda yakin akan menghapus barang ini ?')"><i class="fas fa-trash"></i></a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                    </div>

                    <div class="row">
                        <div class="col-lg-6 p-2">
                            <div class="form-group row mb-2">
                            <label for="supplier" class="col-sm-3 col-form-label col-form-label">Supplier</label>
                            <div class="col-sm-9">
                                <select name="supplier" id="supplier" class="form-control form-control-sm select2" data-placeholder="-- Pilih Supplier --">
                                    <option value="">-- Pilih Supplier --</option>
                                    <?php
                                        $suppliers = getData("SELECT id_relasi, nama FROM tbl_relasi WHERE tipe = 'SUPPLIER' ORDER BY nama ASC");
                                        foreach($suppliers as $supplier){  ?>
                                            <option value="<?= (int)$supplier['id_relasi'] ?>"><?= htmlspecialchars($supplier['nama']) ?></option>
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
                        <div class="col lg-6 p-2">
                            <button type="submit" name="simpan" id="simpan" class="btn btn-primary btn-sm btn-block" disabled><i class="fa fa-save"></i> Simpan</button>
                        </div>
                    </div>
                </div>
                </div>
            </form>
        </div>
    </section>

    <style>
        .select2-result-repository {
            padding: 4px;
        }
        .select2-result-repository__title {
            font-weight: bold;
            color: #333;
        }
        .select2-result-repository__description {
            font-size: 0.9em;
            color: #666;
            margin-top: 2px;
        }
        .select2-results__option--highlighted .select2-result-repository__title {
            color: #fff;
        }
        .select2-results__option--highlighted .select2-result-repository__description {
            color: #f8f9fa;
        }
        .select2-container--bootstrap4 .select2-results__option {
            padding: 8px 12px;
        }
        .form-group .col-form-label {
            position: relative;
        }
        .keyboard-hint {
            font-size: 0.75em;
            color: #6c757d;
            font-weight: normal;
            margin-left: 5px;
        }
    </style>

    <script>
        $(document).ready(function() {
            $('#kodeBrg').select2({
                theme: 'bootstrap4',
                width: '100%',
                placeholder: '-- Pilih atau ketik untuk mencari barang --',
                allowClear: true,
                minimumInputLength: 0,
                templateResult: function(item) {
                    if (item.loading) {
                        return item.text;
                    }
                    if (item.element && $(item.element).data('harga')) {
                        var $container = $(
                            "<div class='select2-result-repository clearfix'>" +
                            "<div class='select2-result-repository__title'></div>" +
                            "<div class='select2-result-repository__description'></div>" +
                            "</div>"
                        );
                        var idbarang = item.id || '';
                        var nama = $(item.element).data('nama') || '';
                        var harga = $(item.element).data('harga') || 0;
                        var stok = $(item.element).data('stock') || 0;
                        $container.find('.select2-result-repository__title').html('<b>' + idbarang + '</b> | ' + nama);
                        $container.find('.select2-result-repository__description').text('Harga Beli: Rp ' + numberFormat(harga) + ' | Stok: ' + stok);
                        return $container;
                    }
                    return item.text;
                },
                templateSelection: function(item) {
                    if (item.element) {
                        return item.id || '';
                    }
                    return item.text || item.id;
                }
            });

            $('#kodeBrg').on('select2:select', function (e) {
                var data = e.params.data;
                var $option = $(data.element);
                
                if (data.id && $option.length) {
                    $('input[name="kodeBrg"]').val(data.id);
                    $('input[name="namaBrg"]').val($option.data('nama') || '');
                    $('input[name="stok"]').val($option.data('stock') || '');
                    $('input[name="satuan"]').val($option.data('satuan') || '');
                    $('input[name="harga"]').val($option.data('harga') || '');
                    
                    var qty = 1;
                    var harga = $option.data('harga') || 0;
                    $('input[name="qty"]').val(qty).focus();
                    $('input[name="jmlHarga"]').val(qty * harga);
                }
            });

            $('#kodeBrg').on('select2:clear', function () {
                $('input[name="kodeBrg"]').val('');
                $('input[name="namaBrg"]').val('');
                $('input[name="stok"]').val('');
                $('input[name="satuan"]').val('');
                $('input[name="harga"]').val('');
                $('input[name="qty"]').val('');
                $('input[name="jmlHarga"]').val('');
            });
            
            $('#supplier').select2({
                theme: 'bootstrap4',
                width: '100%',
                placeholder: '-- Pilih Supplier --',
                allowClear: true
            });

            $('#kodeBrg').on('select2:open', function() {
                setTimeout(function() {
                    $('.select2-search__field').on('keyup', function() {
                        var searchTerm = $(this).val().toLowerCase();
                        if (searchTerm.length > 0) {
                            
                            
                        }
                    });
                }, 100);
            });
        });

        function numberFormat(num) {
            return parseInt(num).toLocaleString('id-ID');
        }

        document.getElementById('tglNota').addEventListener('change', function () {
            const newUrl = '?tgl=' + this.value + '&noBeli=<?= $noBeli ?>';
            window.history.replaceState(null, '', newUrl);
        });

        document.getElementById('qty').addEventListener('input', function () {
            const qty = parseInt(this.value) || 0;
            const harga = parseInt(document.getElementById('harga').value) || 0;
            document.getElementById('jmlHarga').value = qty * harga;
        });

        $(document).keydown(function(e) {
            if (e.altKey && e.keyCode === 66) {
                e.preventDefault();
                $('#kodeBrg').select2('open');
            }
        });
        
        function checkSimpanButton() {
            const supplier = document.getElementById('supplier').value;
            const table = document.querySelectorAll('table tbody tr');
            const simpanBtn = document.getElementById('simpan');
            if (supplier !== '' && table.length > 0) {
                simpanBtn.disabled = false;
            } else {
                simpanBtn.disabled = true;
            }
        }

        document.getElementById('supplier').addEventListener('change', checkSimpanButton);
        window.addEventListener('DOMContentLoaded', function() {
            checkSimpanButton();
        });
        setInterval(checkSimpanButton, 1000);
    </script>

<?php require "../template/footer.php";?>