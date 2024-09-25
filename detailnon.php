<?php
// Koneksi database
$koneksi = mysqli_connect("localhost", "root", "", "cobaukk"); // Ganti dengan nama database kamu

// Mengambil ID Transaksi dari parameter URL
$id_transaksi = mysqli_real_escape_string($koneksi, $_GET['id']);

// Query untuk mengambil data transaksi non-member dan pembayaran berdasarkan id_transaksi
$query = mysqli_query($koneksi, "
    SELECT 
        SUM(n.subtotal) as total_harga, 
        p.uang_dibayarkan, 
        p.kembalian 
    FROM nonmemb n
    LEFT JOIN pembayaran p ON n.id_transaksi = p.id_transaksi
    WHERE n.id_transaksi = '$id_transaksi'
");

$data = mysqli_fetch_array($query);

// Pastikan data tersedia
$total_harga = $data['total_harga'] ?? 0;
$uang_dibayarkan = $data['uang_dibayarkan'] ?? 0;
$kembalian = $data['kembalian'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pembelian</title>
    <style>
        /* Media query untuk cetak */
        @media print {
            /* Sembunyikan tombol kembali dan cetak saat mencetak */
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid px-4">
        <h1 class="mt-4">Detail Pembelian</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item active">Detail Pembelian</li>
        </ol>
        <a href="?page=transaksi_nonmemb" class="btn btn-danger no-print">Kembali</a>
        <button onclick="window.print()" class="btn btn-secondary no-print">Cetak</button>
        <hr>

        <table class="table table-bordered">
            <tr>
                <td width="200">ID Transaksi</td>
                <td width="1">:</td>
                <td><?php echo htmlspecialchars($id_transaksi); ?></td>
            </tr>
            <tr>
                <td>Total Harga</td>
                <td>:</td>
                <td>Rp <?php echo number_format($total_harga, 0, ',', '.'); ?></td>
            </tr>
            <tr>
                <td>Uang Dibayarkan</td>
                <td>:</td>
                <td>Rp <?php echo number_format($uang_dibayarkan, 0, ',', '.'); ?></td>
            </tr>
            <tr>
                <td>Kembalian</td>
                <td>:</td>
                <td>Rp <?php echo number_format($kembalian, 0, ',', '.'); ?></td>
            </tr>
        </table>

        <h5>Detail Produk</h5>
        <table class="table table-bordered">
            <tr>
                <th>Nama Produk</th>
                <th>Jumlah Produk</th>
                <th>Subtotal</th>
            </tr>

            <?php
            // Query untuk mengambil detail produk yang dibeli berdasarkan id_transaksi
            $produk_query = mysqli_query($koneksi, "SELECT * FROM nonmemb WHERE id_transaksi = '$id_transaksi'");

            while ($produk = mysqli_fetch_array($produk_query)) {
            ?>
                <tr>
                    <td><?php echo htmlspecialchars($produk['nama_produk']); ?></td>
                    <td><?php echo $produk['jumlah_produk']; ?></td>
                    <td>Rp <?php echo number_format($produk['subtotal'], 0, ',', '.'); ?></td>
                </tr>
            <?php
            }
            ?>
        </table>
    </div>
</body>
</html>
