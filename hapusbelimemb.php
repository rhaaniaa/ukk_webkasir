<?php
    $id = $_GET['id'];
    $query = mysqli_query($koneksi, "DELETE FROM nonmemb WHERE id_transaksi = '$id'");
    if ($query) {
        echo '<script>alert("Data berhasil dihapus!"); location.href="?page=transaksi_nonmemb"</script>';
    } else {    
        echo '<script>alert("Data gagal dihapus!")</script>';
    }