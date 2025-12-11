<?php
session_start();

require "../config/config.php";
require "../config/functions.php";

$tgl1 = $_GET['tgl1'];
$tgl2 = $_GET['tgl2'];

$data = getData("
    SELECT p.*, s.nama AS supplier
    FROM tbl_pembelian p
    LEFT JOIN tbl_supplier s ON p.id_supplier=s.id_supplier
    WHERE p.tgl_beli BETWEEN '$tgl1' AND '$tgl2'
");
?>

<html>
<head>
<title>Laporan Pembelian</title>
</head>

<body onload="window.print()">

<div style="text-align:center;">
    <h2>Laporan Pembelian</h2>
    <h3>Snackinaja POS</h3>
</div>

<hr>

<table width="100%" border="1" cellspacing="0" cellpadding="5">
<thead>
<tr>
    <th>No</th>
    <th>Tanggal</th>
    <th>No Beli</th>
    <th>Supplier</th>
    <th>Total</th>
</tr>
</thead>

<tbody>
<?php
$no = 1;
foreach ($data as $d) { ?>
<tr>
    <td><?= $no++ ?></td>
    <td><?= date('d-m-Y', strtotime($d['tgl_beli'])) ?></td>
    <td><?= $d['no_beli'] ?></td>
    <td><?= $d['supplier'] ?></td>
    <td><?= number_format($d['total']) ?></td>
</tr>
<?php } ?>
</tbody>

</table>

</body>
</html>
