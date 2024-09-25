<?php
// Koneksi ke database
include('koneksi.php');

// Ambil data dari permintaan POST (dari fetch)
$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['id_penjualan']) && isset($data['status'])) {
    $id_penjualan = $data['id_penjualan'];
    $status = $data['status'];

    // Update status pembayaran menjadi lunas
    $query = "UPDATE penjualan SET status_pembayaran = '$status' WHERE id_penjualan = '$id_penjualan'";
    $result = mysqli_query($koneksi, $query);

    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
}
?>
