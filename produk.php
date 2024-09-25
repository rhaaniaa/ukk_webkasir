<div class="container-fluid px-4">
    <h1 class="mt-4">Produk</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Produk</li>
    </ol>
    <a href="?page=tambahproduk" class="btn btn-primary"> + Tambah Produk</a>
    <hr>
    <table class="table table-bordered">
        <tr>
            <th>ID Produk</th>
            <th>Nama Produk</th>
            <th>Harga</th>
            <th>Stok</th>
            <th>Aksi</th>
        </tr>

        <?php
        // Query untuk mengambil data produk dari database
        $query = mysqli_query($koneksi, "SELECT * FROM produk");
        
        // Membuat variabel counter untuk urutan produk ID
        $counter = 1;

        // Loop melalui setiap baris data yang diambil dari database
        while ($data = mysqli_fetch_array($query)) {
            // Ambil 3 huruf pertama dari nama produk
            $prefix = strtolower(substr($data['nama_produk'], 0, 3)); // ambil 3 huruf pertama dan jadikan huruf kecil

            // Format angka dengan leading zeros (0001, 0002, dst)
            $id_produk = $prefix . '-' . str_pad($counter, 4, '0', STR_PAD_LEFT);

            // Increment counter untuk produk ID berikutnya
            $counter++;
        ?>
            <tr>
                <td><?php echo $id_produk; ?></td> <!-- Menampilkan ID produk -->
                <td><?php echo $data['nama_produk']; ?></td>
                <td><?php echo number_format($data['harga'], 0, ',', '.'); ?></td>
                <td><?php echo $data['stok']; ?></td>
                <td>
                    <a href="?page=ubahpro&&id=<?php echo $data['produk_id']; ?>" class="btn btn-secondary">Edit</a>
                    <a href="?page=hapus&&id=<?php echo $data['produk_id']; ?>" class="btn btn-danger">Hapus</a>
                </td>
            </tr>
        <?php
        }
        ?>
    </table>
</div>
