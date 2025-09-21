<?php
session_start();

if (!isset($_SESSION["ssLoginPOS"])) {
    header('HTTP/1.1 401 Unauthorized');
    exit();
}

require "../config/config.php";
require "../config/functions.php";

// Get search term
$search = isset($_GET['q']) ? $_GET['q'] : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10; // Limit results per page
$offset = ($page - 1) * $limit;

// Sanitize search input
$search = mysqli_real_escape_string($koneksi, $search);

// Build query with search
$whereClause = '';
if (!empty($search)) {
    $whereClause = "WHERE id_barang LIKE '%$search%' OR nama_barang LIKE '%$search%'";
}

// Get total count for pagination
$countQuery = "SELECT COUNT(*) as total FROM tbl_barang $whereClause";
$countResult = getData($countQuery);
$totalItems = $countResult[0]['total'];

// Get items with limit
$query = "SELECT id_barang, nama_barang, stock, harga_beli, satuan FROM tbl_barang $whereClause ORDER BY nama_barang ASC LIMIT $limit OFFSET $offset";
$barang = getData($query);

// Format results for Select2
$results = [];
foreach ($barang as $item) {
    $results[] = [
        'id' => $item['id_barang'],
        'text' => $item['id_barang'] . ' | ' . $item['nama_barang'] . ' (Stok: ' . $item['stock'] . ')',
        'nama_barang' => $item['nama_barang'],
        'stock' => $item['stock'],
        'harga_beli' => $item['harga_beli'],
        'satuan' => $item['satuan'] ?? 'PCS'
    ];
}

// Check if there are more results
$pagination = [
    'more' => ($offset + $limit) < $totalItems
];

// Return JSON response
header('Content-Type: application/json');
echo json_encode([
    'results' => $results,
    'pagination' => $pagination
]);
?>
