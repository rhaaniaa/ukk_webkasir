<?php
// Koneksi ke database
$koneksi = mysqli_connect("localhost", "root", "", "cobaukk"); // Ganti dengan nama database kamu

// Pengecekan koneksi
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Jika form disubmit
if (isset($_POST['nama_pelanggan'])) {
    $nama = $_POST['nama_pelanggan'];
    $alamat = $_POST['alamat'];
    $no_telp = $_POST['no_telp'];

    // Validasi panjang nomor telepon sebelum memasukkan ke database
    if (strlen($no_telp) >= 10 && strlen($no_telp) <= 14) {
        // Query INSERT tanpa menyertakan id_pelanggan (karena auto_increment)
        $query = mysqli_query($koneksi, "INSERT INTO pelanggan(nama_pelanggan, alamat, no_telp) VALUES ('$nama', '$alamat', '$no_telp')");
        
        // Cek apakah query berhasil dijalankan
        if ($query) {
            echo '<script>alert("Data berhasil ditambahkan!"); location.href="?page=pelanggan"</script>';
        } else {
            // Tampilkan pesan error jika query gagal
            echo '<script>alert("Data gagal ditambahkan! Error: ' . mysqli_error($koneksi) . '")</script>';
        }
    } else {
        echo '<script>alert("Nomor telepon harus terdiri dari angka 10 hingga 14 digit!")</script>';
    }
}
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Pelanggan Member</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Pelanggan</li>
    </ol>
    <a href="?page=tambah" class="btn btn-primary"> + Tambah Member</a>
    <hr>
    <table class="table table-bordered">
        <tr>
            <th>ID Pelanggan</th>
            <th>Nama</th>
            <th>Alamat</th>
            <th>Nomor Telepon</th>
            <th>Aksi</th>
        </tr>

        <?php
        // Query untuk mengambil data pelanggan dari database
        $query = mysqli_query($koneksi, "SELECT * FROM pelanggan");
        
        // Loop melalui setiap baris data yang diambil dari database
        while ($data = mysqli_fetch_array($query)) {
            // Format ID pelanggan menjadi plg-0001
            $id_formatted = '' . str_pad($data['id_pelanggan'], 4, '0', STR_PAD_LEFT);
        ?>
            <tr>
                <td><?php echo $id_formatted; ?></td> <!-- ID Pelanggan dengan format plg-0001 -->
                <td><?php echo $data['nama_pelanggan']; ?></td>
                <td><?php echo $data['alamat']; ?></td>
                <td><?php echo $data['no_telp']; ?></td>
                <td>
                    <a href="?page=ubah&&id=<?php echo $data['id_pelanggan']; ?>" class="btn btn-secondary">Edit</a>
                    <a href="?page=deleted&&id=<?php echo $data['id_pelanggan']; ?>" class="btn btn-danger">Hapus</a>
                </td>
            </tr>
        <?php
        }
        ?>
    </table>
</div>
