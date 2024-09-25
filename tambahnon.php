<?php
// Mulai session jika belum dimulai
if (!isset($_SESSION)) {
    session_start();
}

// Koneksi ke database
$koneksi = mysqli_connect("localhost", "root", "", "cobaukk"); // Ganti sesuai database Anda

// Fungsi untuk generate ID Transaksi
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

// Generate ID Transaksi sekali untuk satu sesi transaksi
if (!isset($_SESSION['id_transaksi'])) {
    $_SESSION['id_transaksi'] = generateTransaksiID($koneksi); 
}
$id_transaksi = $_SESSION['id_transaksi']; // Gunakan id_transaksi yang sama untuk beberapa produk

// Tambahkan produk ke pembelian dan simpan ke database
if (isset($_POST['produk'])) {
    foreach ($_POST['produk'] as $index => $selected_produk_id) {
        $jumlah = $_POST['jumlah'][$index]; // Ambil jumlah produk

        // Ambil informasi produk
        $produk_info = mysqli_fetch_array(mysqli_query($koneksi, "SELECT * FROM produk WHERE produk_id = '$selected_produk_id'"));

        if ($produk_info) {
            // Periksa stok produk sebelum menambahkan ke pembelian
            if ($produk_info['stok'] >= $jumlah) {
                $subtotal = $produk_info['harga'] * $jumlah;
                $nama_produk = $produk_info['nama_produk'];

                // Periksa apakah produk sudah ada di dalam pembelian
                $existing_product = mysqli_fetch_array(mysqli_query($koneksi, "SELECT * FROM nonmemb WHERE id_transaksi = '$id_transaksi' AND nama_produk = '$nama_produk'"));

                if ($existing_product) {
                    // Jika produk sudah ada, update jumlah dan subtotal
                    $new_jumlah = $existing_product['jumlah_produk'] + $jumlah;
                    $new_subtotal = $produk_info['harga'] * $new_jumlah;
                    mysqli_query($koneksi, "UPDATE nonmemb SET jumlah_produk = '$new_jumlah', subtotal = '$new_subtotal' WHERE id_transaksi = '$id_transaksi' AND nama_produk = '$nama_produk'");
                } else {
                    // Jika produk belum ada, tambahkan ke pembelian
                    $query = mysqli_query($koneksi, "INSERT INTO nonmemb (id_transaksi, nama_produk, jumlah_produk, subtotal) 
                              VALUES ('$id_transaksi', '$nama_produk', '$jumlah', '$subtotal')");
                }

                // Update stok produk
                mysqli_query($koneksi, "UPDATE produk SET stok = stok - '$jumlah' WHERE produk_id = '$selected_produk_id'");

                if (!$query) {
                    echo '<script>alert("Gagal menambahkan produk: ' . $nama_produk . '!");</script>';
                }
            } else {
                echo '<script>alert("Stok tidak cukup untuk ' . $nama_produk . '!");</script>';
            }
        }
    }
}

// Proses pembayaran
if (isset($_POST['bayar'])) {
    $total_harga = $_POST['total_harga'];
    $uang_dibayarkan = (int)$_POST['uang_dibayarkan'];
    $kembalian = $uang_dibayarkan - $total_harga;

    if ($uang_dibayarkan < $total_harga) {
        echo '<script>alert("Uang yang dibayarkan kurang!");</script>';
    } else {
        // Simpan data pembayaran ke database
        $query_pembayaran = mysqli_query($koneksi, "
            INSERT INTO pembayaran (id_transaksi, total_harga, uang_dibayarkan, kembalian)
            VALUES ('$id_transaksi', '$total_harga', '$uang_dibayarkan', '$kembalian')
        ");

        if ($query_pembayaran) {
            echo '<script>alert("Pembayaran berhasil! Kembalian: Rp ' . number_format($kembalian, 0, ',', '.') . '");</script>';
            
            // Hapus sesi id_transaksi setelah pembayaran berhasil
            unset($_SESSION['id_transaksi']); 
            
            // Redirect ke halaman detail transaksi atau halaman baru
            echo '<script>window.location.href="?page=detailnon&id=' . $id_transaksi . '";</script>';
        } else {
            echo '<script>alert("Gagal menyimpan pembayaran!");</script>';
        }
    }
}

// Inisialisasi pencarian produk
$search_produk = '';
if (isset($_POST['search_produk'])) {
    $search_produk = $_POST['search_produk'];
}

// Ambil daftar produk berdasarkan pencarian
$query_produk = "SELECT * FROM produk WHERE nama_produk LIKE '%$search_produk%'";
$pro = mysqli_query($koneksi, $query_produk);

// Hapus produk dari daftar pembelian
if (isset($_POST['hapus_id'])) {
    $hapus_id = $_POST['hapus_id'];

    // Ambil data produk yang akan dihapus
    $item_hapus = mysqli_fetch_array(mysqli_query($koneksi, "SELECT jumlah_produk, nama_produk FROM nonmemb WHERE id_transaksi = '$id_transaksi' AND nama_produk = '$hapus_id'"));
    $jumlah_hapus = $item_hapus['jumlah_produk'];
    $nama_produk_hapus = $item_hapus['nama_produk'];

    // Query untuk menghapus item dari tabel nonmemb
    $query_hapus = mysqli_query($koneksi, "DELETE FROM nonmemb WHERE id_transaksi = '$id_transaksi' AND nama_produk = '$nama_produk_hapus'");

    if ($query_hapus) {
        // Update stok produk
        mysqli_query($koneksi, "UPDATE produk SET stok = stok + '$jumlah_hapus' WHERE nama_produk = '$nama_produk_hapus'");

        echo '<script>alert("Produk berhasil dihapus!"); window.location.href="?page=tambahnon";</script>';
    } else {
        echo '<script>alert("Gagal menghapus produk!");</script>';
    }
}
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Tambah Pembelian</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Tambah Pembelian</li>
    </ol>
    <a href="?page=pembelian" class="btn btn-danger"> Kembali</a>
    <hr>

    <form method="post" class="mb-3">
        <div class="mb-3">
            <label for="search_produk" class="form-label">Cari Produk</label>
            <input type="text" class="form-control" name="search_produk" value="<?php echo $search_produk; ?>" placeholder="Cari produk..." required>
            <button type="submit" class="btn btn-primary mt-2">Cari</button>
        </div>
    </form>

    <form method="post">
        <table class="table table-bordered">
            <tr>
                <td width="200">Nama Produk</td>
                <td width="1">:</td>
                <td>
                    <select class="form-control form-select" name="produk[]" required>
                        <option value="">Pilih Produk</option>
                        <?php
                        while ($produk = mysqli_fetch_array($pro)) {
                        ?>
                            <option value="<?php echo $produk['produk_id']; ?>">
                                <?php echo $produk['nama_produk'] . ' (Stok: ' . $produk['stok'] . ') - Harga: ' . $produk['harga']; ?>
                            </option>
                        <?php
                        }
                        ?>
                    </select>
                    <input class="form-control mt-2" type="number" name="jumlah[]" placeholder="Jumlah" min="1" required>
                    <button type="submit" class="btn btn-primary mt-2">+ Add</button>
                </td>
            </tr>
        </table>
    </form>

    <h3>List Pembelian</h3>
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
            <?php
            // Ambil daftar pembelian dari tabel nonmemb berdasarkan id_transaksi saat ini
            $query_pembelian = mysqli_query($koneksi, "SELECT * FROM nonmemb WHERE id_transaksi = '$id_transaksi'");
            while ($item = mysqli_fetch_array($query_pembelian)) {
                echo "<tr>
                <td>{$item['nama_produk']}</td>
                <td>{$item['jumlah_produk']}</td>
                <td>" . number_format($item['subtotal'], 0, ',', '.') . "</td>
                <td>
                    <form method='post'>
                        <input type='hidden' name='hapus_id' value='{$item['nama_produk']}'>
                        <button type='submit' class='btn btn-danger'>Hapus</button>
                    </form>
                </td>
            </tr>";
            }
            ?>
        </tbody>
    </table>

    <?php
    // Hitung total harga
    $result_total = mysqli_query($koneksi, "SELECT SUM(subtotal) as total_harga FROM nonmemb WHERE id_transaksi = '$id_transaksi'");
    $total = mysqli_fetch_array($result_total);
    $total_harga = $total['total_harga'];
    ?>

    <h3>Total Harga: Rp <?php echo number_format($total_harga, 0, ',', '.'); ?></h3>

    <form method="post">
        <input type="hidden" name="total_harga" value="<?php echo $total_harga; ?>">
        <div class="mb-3">
            <label for="uang_dibayarkan" class="form-label">Uang Dibayarkan</label>
            <input type="number" class="form-control" name="uang_dibayarkan" placeholder="Masukkan uang dibayarkan" required>
        </div>
        <button type="submit" name="bayar" class="btn btn-success">Bayar</button>
    </form>
</div>
