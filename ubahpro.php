<?php
    $id = $_GET['id'];
    if (isset($_POST['nama_produk'])) {
        $nama = $_POST['nama_produk'];
        $harga = $_POST['harga'];
        $stok = $_POST['stok'];
        
        // Query UPDATE tanpa menyentuh kolom id_pelanggan (AUTO_INCREMENT)
        $query = mysqli_query($koneksi, "UPDATE produk SET nama_produk = '$nama', harga = '$harga', stok = '$stok' WHERE produk_id = '$id'");
        if ($query) {
            echo '<script>alert("Perubahan berhasil!"); location.href="?page=produk"</script>';
        } else {
            echo '<script>alert("Perubahan gagal!")</script>';
        }
    }

    $query = mysqli_query($koneksi, "SELECT * FROM produk WHERE produk_id = '$id'");
    $data = mysqli_fetch_array($query);
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Produk</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Produk</li>
    </ol>
    <a href="?page=produk" class="btn btn-danger"> Kembali</a>
    <hr>
    
    <form method="post">
        <table class="table table-bordered">
            <tr>
                <td width="200">Nama Produk</td>
                <td width="1">:</td>
                <td><input class="form-control" value="<?php echo htmlspecialchars($data['nama_produk']); ?>" type="text" name="nama_produk" required></td>
            </tr>
            <tr>
                <td>Harga </td>
                <td>:</td>
                <td> <input class="form-control" type="text" value="<?php echo htmlspecialchars($data['harga']); ?>" name="harga" required></td>
            </tr>
            <tr>
                <td>Stok </td>
                <td>:</td>
                <td><input class="form-control" type="text" value="<?php echo htmlspecialchars($data['stok']); ?>" name="stok" required></td>
            </tr>
            <tr> 
                <td></td>
                <td></td>
                <td>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <button type="reset" class="btn btn-danger">Reset</button>
                </td>
            </tr>
        </table>
    </form>
</div>
