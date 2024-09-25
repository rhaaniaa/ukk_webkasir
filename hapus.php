<?php
    $id = $_GET['id'];
    $query = mysqli_query($koneksi, "DELETE FROM produk WHERE produk_id = '$id'");
    $query = mysqli_query($koneksi, "DELETE FROM detailpenjualan WHERE produk_id = '$id'");
    if ($query) {
        echo '<script>alert("Data berhasil dihapus!"); location.href="?page=produk"</script>';
    } else {    
        echo '<script>alert("Data gagal dihapus!")</script>';
    }