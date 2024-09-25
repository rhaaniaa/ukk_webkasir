<?php
// Mulai session jika belum dimulai
if (!isset($_SESSION)) {
    session_start();
}

// Koneksi database
$koneksi = mysqli_connect("localhost", "root", "", "cobaukk"); // Ganti dengan nama database kamu

// Fungsi untuk generate ID transaksi Non-member otomatis
function generateTransaksiID($koneksi)
{
    $query = "SELECT MAX(id_transaksi) as max_id FROM nonmemb";
    $result = mysqli_query($koneksi, $query);
    $data = mysqli_fetch_array($result);

    if ($data['max_id']) {
        $id_number = (int)str_replace('transnon-', '', $data['max_id']);
        $new_id = 'transnon-' . str_pad($id_number + 1, 4, '0', STR_PAD_LEFT);
    } else {
        $new_id = 'transnon-0001'; // ID pertama jika belum ada data
    }

    return $new_id;
}

// Setelah pembayaran, bersihkan session id_transaksi
if (isset($_POST['bayar'])) {
    // Proses logika pembayaran, kemudian:
    unset($_SESSION['id_transaksi']);
}
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Pelanggan Non-member</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Non-member</li>
    </ol>
    <a href="?page=tambahnon" class="btn btn-primary"> + Tambah Pelanggan</a>
    <hr>
    <table class="table table-bordered">
        <tr>
            <th>Tanggal Penjualan</th>
            <th>ID Transaksi</th>
            <th>Total Harga</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>

        <?php
        // Query untuk mengambil data transaksi non-member dari database
        $query = mysqli_query($koneksi, "SELECT id_transaksi, SUM(subtotal) as total_harga FROM nonmemb GROUP BY id_transaksi");

        // Loop melalui setiap baris data yang diambil dari database
        while ($data = mysqli_fetch_array($query)) {
        ?>
            <tr>
                <td><?php echo date("Y-m-d"); ?></td> <!-- Tanggal Penjualan -->
                <td><?php echo htmlspecialchars($data['id_transaksi']); ?></td>
                <td><?php echo number_format($data['total_harga'], 0, ',', '.'); ?></td> <!-- Total Harga -->
                <td>
                    <button class="btn btn-warning" disabled>Pembayaran Lunas</button>
                </td>
                <td>
                    <a href="?page=detailnon&id=<?php echo $data['id_transaksi']; ?>" class="btn btn-info">Detail</a>
                    <a href="?page=hapusbelimemb&id=<?php echo $data['id_transaksi']; ?>" class="btn btn-danger" onclick="return confirm('Anda yakin ingin menghapus transaksi ini?');">Hapus</a>
                </td>
            </tr>
        <?php
        }
        ?>
    </table>
</div>
