<?php
    // Mengambil ID penjualan
    $id = mysqli_real_escape_string($koneksi, $_GET['id']);

    // Query untuk mengambil data penjualan beserta data pelanggan
    $query = mysqli_query($koneksi, "SELECT * FROM penjualan LEFT JOIN pelanggan ON penjualan.id_pelanggan = pelanggan.id_pelanggan WHERE penjualan.id_penjualan = '$id'");
    $data = mysqli_fetch_array($query);

    // Menghitung diskon 15% dari total harga
    $diskon = 0.10;
    $harga_diskon = $data['total_harga'] - ($data['total_harga'] * $diskon);
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Detail Pembelian</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Detail Pembelian</li>
    </ol>
    <a href="?page=pembelian" class="btn btn-danger no-print">Kembali</a>
    <button onclick="window.print()" class="btn btn-secondary no-print">Cetak</button> <!-- Tombol Cetak -->
    <hr>

    <table class="table table-bordered">
        <tr>
            <td width="200">Nama Pelanggan</td>
            <td width="1">:</td>
            <td>
                <?php echo htmlspecialchars($data['nama_pelanggan']); ?>
            </td>
        </tr>

        <?php
        // Query untuk mengambil data detail penjualan beserta produk yang dibeli
        $pro = mysqli_query($koneksi, "SELECT * FROM detailpenjualan 
                                        LEFT JOIN produk ON produk.produk_id = detailpenjualan.produk_id 
                                        WHERE id_penjualan = '$id'");
        
        // Looping data produk yang terkait dengan penjualan
        while ($produk = mysqli_fetch_array($pro)) {
            // Ambil 3 huruf pertama dari nama produk
            $prefix = strtolower(substr($produk['nama_produk'], 0, 3));
            // Format ID Produk, tambahkan 4 digit angka dari produk_id
            $idProdukFormatted = $prefix . '-' . str_pad($produk['produk_id'], 4, '0', STR_PAD_LEFT);
        ?>
            <tr>
                <td><?php echo htmlspecialchars($produk['nama_produk']); ?> (ID: <?php echo $idProdukFormatted; ?>)</td>
                <td>:</td>
                <td>
                    Harga Satuan: <?php echo number_format($produk['harga'], 0, ',', '.'); ?> <br>
                    Jumlah: <?php echo $produk['jumlah_produk']; ?> <br>
                    SubTotal: <?php echo number_format($produk['subtotal'], 0, ',', '.'); ?><br>
                </td>
            </tr>
        <?php
        }
        ?>
        <tr>
            <td>Total</td>
            <td>:</td>
            <td> <?php echo number_format($data['total_harga'], 0, ',', '.'); ?></td>
        </tr>
        <tr>
            <td>Total Setelah Diskon</td>
            <td>:</td>
            <td> <?php echo number_format($harga_diskon, 0, ',', '.'); ?> (Diskon 10%)</td>
        </tr>
        <tr>
            <td>Uang Dibayarkan</td>
            <td>:</td>
            <td> 
                <?php echo number_format($data['uang_dibayarkan'], 0, ',', '.'); ?>
            </td>
        </tr>
        <tr>
            <td>Kembalian</td>
            <td>:</td>
            <td> 
                <?php 
                $kembalian = $data['uang_dibayarkan'] - $harga_diskon;
                echo number_format($kembalian, 0, ',', '.'); 
                ?>
            </td>
        </tr>
    </table>
</div>

<style>
    @media print {
        .no-print {
            display: none;
        }
    }
</style>
