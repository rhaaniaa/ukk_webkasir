<?php
    $id = $_GET['id'];
    if (isset($_POST['nama_pelanggan'])) {
        $nama = $_POST['nama_pelanggan'];
        $alamat = $_POST['alamat'];
        $no_telp = $_POST['no_telp'];
        
        // Query UPDATE tanpa menyentuh kolom id_pelanggan (AUTO_INCREMENT)
        $query = mysqli_query($koneksi, "UPDATE pelanggan SET nama_pelanggan = '$nama', alamat = '$alamat', no_telp = '$no_telp' WHERE id_pelanggan = '$id'");
        if ($query) {
            echo '<script>alert("Perubahan berhasil!"); location.href="?page=pelanggan"</script>';
        } else {
            echo '<script>alert("Perubahan gagal!")</script>';
        }
    }

    $query = mysqli_query($koneksi, "SELECT * FROM pelanggan WHERE id_pelanggan = '$id'");
    $data = mysqli_fetch_array($query);
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Pelanggan</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Pelanggan</li>
    </ol>
    <a href="?page=pelanggan" class="btn btn-danger"> Kembali</a>
    <hr>
    
    <form method="post">
        <table class="table table-bordered">
            <tr>
                <td width="200">Nama </td>
                <td width="1">:</td>
                <td><input class="form-control" value="<?php echo htmlspecialchars($data['nama_pelanggan']); ?>" type="text" name="nama_pelanggan" required></td>  
            </tr>
            <tr>
                <td>Alamat </td>
                <td>:</td>
                <td> <textarea name="alamat" rows="5" class="form-control" required><?php echo htmlspecialchars($data['alamat']); ?></textarea></td>
            </tr>
            <tr>
                <td>No.telp </td>
                <td>:</td>
                <td><input class="form-control" type="text" value="<?php echo htmlspecialchars($data['no_telp']); ?>" name="no_telp" required></td>
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
