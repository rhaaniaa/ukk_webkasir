<?php
// Koneksi ke database
$koneksi = mysqli_connect("localhost", "root", "", "cobaukk");

// Ambil ID dari URL
$id = $_GET['id'];

// Cek apakah ada penjualan terkait dengan pelanggan ini
$cek_penjualan = mysqli_query($koneksi, "SELECT * FROM penjualan WHERE id_pelanggan = '$id'");
if (mysqli_num_rows($cek_penjualan) > 0) {
    // Jika ada data penjualan terkait, tampilkan pesan error
    echo '<script>alert("Pelanggan tidak dapat dihapus karena memiliki penjualan terkait!"); location.href="?page=pelanggan"</script>';
} else {
    // Jika tidak ada penjualan terkait, lanjutkan penghapusan pelanggan
    $query = mysqli_query($koneksi, "DELETE FROM pelanggan WHERE id_pelanggan = '$id'");
    if ($query) {
        echo '<script>alert("Pelanggan berhasil dihapus!"); location.href="?page=pelanggan"</script>';
    } else {    
        echo '<script>alert("Pelanggan gagal dihapus!")</script>';
    }
}
?>
