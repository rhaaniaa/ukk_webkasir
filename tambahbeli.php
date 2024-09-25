<?php
// Mulai session jika belum dimulai
if (!isset($_SESSION)) {
    session_start();
}

// Koneksi ke database
$koneksi = mysqli_connect("localhost", "root", "", "cobaukk"); // Ganti dengan detail koneksi Anda

if (!$koneksi) {
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}

// Proses memilih pelanggan
if (isset($_POST['select_customer'])) {
    $id_pelanggan = $_POST['id_pelanggan'];
    $_SESSION['id_pelanggan'] = $id_pelanggan;
}

// Proses menambahkan produk ke session
if (isset($_POST['add_to_cart'])) {
    if (!isset($_SESSION['id_pelanggan'])) {
        echo "Pilih pelanggan terlebih dahulu.";
        exit();
    }

    $produk_id = $_POST['produk_id'];
    $jumlah = $_POST['jumlah'];

    // Ambil informasi produk dari database
    $result = mysqli_query($koneksi, "SELECT * FROM produk WHERE produk_id = '$produk_id'");
    $produk = mysqli_fetch_array($result);

    if ($produk && $jumlah > 0 && $jumlah <= $produk['stok']) {
        // Periksa apakah produk sudah ada di keranjang
        if (isset($_SESSION['cart'][$produk_id])) {
            // Jika sudah ada, tambahkan jumlah baru ke jumlah yang ada
            $_SESSION['cart'][$produk_id]['jumlah'] += $jumlah;
            $_SESSION['cart'][$produk_id]['subtotal'] = $_SESSION['cart'][$produk_id]['jumlah'] * $_SESSION['cart'][$produk_id]['harga'];
        } else {
            // Tambahkan produk baru ke keranjang
            $_SESSION['cart'][$produk_id] = [
                'nama_produk' => $produk['nama_produk'],
                'stok' => $produk['stok'],
                'jumlah' => $jumlah,
                'harga' => $produk['harga'],
                'subtotal' => $jumlah * $produk['harga']
            ];
        }
    }
}

// Proses menghapus produk dari cart
if (isset($_POST['remove_from_cart'])) {
    $produk_id = $_POST['produk_id'];
    unset($_SESSION['cart'][$produk_id]);
}

// Proses simpan ke database
if (isset($_POST['save_cart']) && isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    if (!isset($_SESSION['id_pelanggan'])) {
        echo "Pilih pelanggan terlebih dahulu.";
        exit();
    }

    $id_pelanggan = $_SESSION['id_pelanggan'];
    $total_harga = 0;

    // Validasi apakah pelanggan ada di tabel pelanggan
    $check_pelanggan = mysqli_query($koneksi, "SELECT * FROM pelanggan WHERE id_pelanggan = '$id_pelanggan'");

    if (mysqli_num_rows($check_pelanggan) == 0) {
        // Jika pelanggan tidak ditemukan
        echo "Pelanggan tidak ditemukan. Silakan pilih pelanggan yang valid.";
        exit();
    }

    // Hitung total harga dari keranjang
    foreach ($_SESSION['cart'] as $item) {
        $total_harga += $item['subtotal'];
    }

    // Simpan data ke tabel penjualan
    $tanggal_pembelian = date('Y-m-d'); // Mendapatkan tanggal saat ini
    $status_pembayaran = 'Belum Lunas'; // Status default
    $jumlah_bayar = 0.00; // Default jumlah bayar

    $query_pembelian = "INSERT INTO penjualan (tanggal_penjualan, total_harga, status_pembayaran, jumlah_bayar, id_pelanggan) 
                        VALUES ('$tanggal_pembelian', '$total_harga', '$status_pembayaran', '$jumlah_bayar', '$id_pelanggan')";

    if (mysqli_query($koneksi, $query_pembelian)) {
        // Dapatkan ID penjualan yang baru saja dimasukkan
        $id_pembelian = mysqli_insert_id($koneksi);

        // Simpan item-item ke tabel detail_penjualan
        foreach ($_SESSION['cart'] as $produk_id => $item) {
            $jumlah = $item['jumlah'];
            $subtotal = $item['subtotal'];
            $query_detail = "INSERT INTO detailpenjualan (id_penjualan, produk_id, jumlah_produk, subtotal) 
                             VALUES ('$id_pembelian', '$produk_id', '$jumlah', '$subtotal')";
            mysqli_query($koneksi, $query_detail);

            // Kurangi stok produk
            $stok_terbaru = $item['stok'] - $jumlah;
            mysqli_query($koneksi, "UPDATE produk SET stok = '$stok_terbaru' WHERE produk_id = '$produk_id'");
        }

        // Bersihkan keranjang dan session id_pelanggan
        unset($_SESSION['cart']);
        unset($_SESSION['id_pelanggan']);

        // Redirect ke halaman pembelian setelah berhasil
        header("Location: ?page=pembelian");
        exit();
    } else {
        echo "Gagal menyimpan data pembelian. Error: " . mysqli_error($koneksi);
    }
}

// Proses Pencarian Produk
$search_produk = isset($_POST['search_produk']) ? $_POST['search_produk'] : '';
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Tambah Pembelian</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Tambah Pembelian</li>
    </ol>
    <a href="?page=pembelian" class="btn btn-danger"> Kembali</a>
    <hr>

    <!-- Tombol Search Produk di Pojok Kanan -->
    <div class="row mb-3">
        <div class="col text-end">
            <form method="post" class="d-inline-flex">
                <input type="text" name="search_produk" class="form-control form-control-sm" placeholder="Cari Produk..." style="max-width: 200px;">
                <button type="submit" class="btn btn-secondary btn-sm ms-2">Search</button>
            </form>
        </div>
    </div>

    <!-- Form Memilih Pelanggan -->
    <?php if (!isset($_SESSION['id_pelanggan'])) { ?>
        <form method="post" action="">
            <table class="table table-bordered">
                <tr>
                    <td width="200">Nama Pelanggan</td>
                    <td width="1">:</td>
                    <td>
                        <select class="form-control form-select" name="id_pelanggan" required>
                            <option value="">Pilih Pelanggan</option>
                            <?php
                            // Ambil daftar pelanggan dari database
                            $p = mysqli_query($koneksi, "SELECT * FROM pelanggan");
                            if (mysqli_num_rows($p) > 0) {
                                while ($pel = mysqli_fetch_array($p)) {
                            ?>
                                    <option value="<?php echo $pel['id_pelanggan']; ?>">
                                        <?php echo $pel['nama_pelanggan']; ?>
                                    </option>
                            <?php
                                }
                            } else {
                                echo "<option value=''>Data pelanggan tidak ditemukan</option>";
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td colspan="3">
                        <button type="submit" class="btn btn-primary mt-2" name="select_customer">Pilih Pelanggan</button>
                    </td>
                </tr>
            </table>
        </form>
    <?php } ?>

    <!-- Form Menambahkan Produk ke Keranjang -->
    <?php if (isset($_SESSION['id_pelanggan'])) { ?>
        <form method="post" action="">
            <table class="table table-bordered">
                <tr>
                    <td>Nama Produk</td>
                    <td>:</td>
                    <td>
                        <select class="form-control form-select" name="produk_id" required>
                            <?php
                            // Ambil daftar produk dari database berdasarkan pencarian
                            $query_produk = "SELECT * FROM produk WHERE nama_produk LIKE '%$search_produk%' LIMIT 10";
                            $pro = mysqli_query($koneksi, $query_produk);
                            while ($produk = mysqli_fetch_array($pro)) {
                            ?>
                                <option value="<?php echo $produk['produk_id']; ?>">
                                    <?php echo $produk['nama_produk'] . ' (Stok: ' . $produk['stok'] . ')'; ?>
                                </option>
                            <?php
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Jumlah</td>
                    <td>:</td>
                    <td>
                        <input class="form-control" type="number" name="jumlah" placeholder="Jumlah" required>
                    </td>
                </tr>
                <tr>
                    <td colspan="3">
                        <button type="submit" class="btn btn-primary mt-2" name="add_to_cart">+ Add</button>
                    </td>
                </tr>
            </table>
        </form>
    <?php } ?>

    <!-- Tabel Sementara (Daftar Produk yang Dipilih) -->
    <?php if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) { ?>
        <h3>Daftar Produk yang Dipilih</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nama Produk</th>
                    <th>Jumlah</th>
                    <th>Subtotal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($_SESSION['cart'] as $produk_id => $item) { ?>
                    <tr>
                        <td><?php echo $item['nama_produk']; ?></td>
                        <td><?php echo $item['jumlah']; ?></td>
                        <td><?php echo number_format($item['subtotal'], 0, ',', '.'); ?></td>
                        <td>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="produk_id" value="<?php echo $produk_id; ?>">
                                <button type="submit" class="btn btn-danger btn-sm" name="remove_from_cart">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <!-- Tombol Simpan Produk ke Database -->
        <form method="post">
            <button type="submit" class="btn btn-success" name="save_cart">Simpan</button>
        </form>
    <?php } ?>
</div>
