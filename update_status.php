<?php
include 'koneksi.php'; // Ganti dengan koneksi database yang sesuai

// Baca input JSON
$data = json_decode(file_get_contents('php://input'), true);

// Cek jika data berhasil dibaca
if (isset($data['id']) && isset($data['status'])) {
    $id = mysqli_real_escape_string($koneksi, $data['id']);
    $status = mysqli_real_escape_string($koneksi, $data['status']);

    // Update status pembayaran di database
    $query = "UPDATE penjualan SET status_pembayaran = '$status' WHERE id_penjualan = '$id'";
    $result = mysqli_query($koneksi, $query);

    // Mengembalikan respons
    echo json_encode(['success' => $result]);
} else {
    echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
}
?>
