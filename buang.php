<?php
    $id = $_GET['id'];
    $query = mysqli_query($koneksi, "DELETE FROM detailpenjualan WHERE id_penjualan = '$id'");
    $query = mysqli_query($koneksi, "DELETE FROM penjualan WHERE id_penjualan = '$id'");
    if ($query) {
        echo '<script>alert("Penjualan berhasil dihapus!"); location.href="?page=pembelian"</script>';
    } else {    
        echo '<script>alert("Penjualan gagal dihapus!")</script>';
    }