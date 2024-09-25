<?php
    if (isset($_POST['nama_pelanggan'])) {
        $nama = $_POST['nama_pelanggan'];
        $alamat = $_POST['alamat'];
        $no_telp = $_POST['no_telp'];
        
        // Ambil ID pelanggan terakhir
        $query_last_id = mysqli_query($koneksi, "SELECT id_pelanggan FROM pelanggan ORDER BY id_pelanggan DESC LIMIT 1");
        $data_last_id = mysqli_fetch_array($query_last_id);
        
        // Jika ada pelanggan sebelumnya, ambil angka terakhir, jika tidak mulai dari 0001
        if ($data_last_id) {
            $last_id = (int)substr($data_last_id['id_pelanggan'], 3); // Ambil angka dari plg000X
            $new_id = "plg" . str_pad($last_id + 1, 4, "0", STR_PAD_LEFT); // Tambahkan 1 dan buat id baru
        } else {
            $new_id = "plg0001"; // Jika belum ada pelanggan, mulai dari plg0001
        }

        // Validasi panjang nomor telepon sebelum memasukkan ke database
        if (strlen($no_telp) >= 10 && strlen($no_telp) <= 14) {
            // Query INSERT dengan id_pelanggan yang di-generate secara otomatis
            $query = mysqli_query($koneksi, "INSERT INTO pelanggan(id_pelanggan, nama_pelanggan, alamat, no_telp) VALUES ('$new_id', '$nama', '$alamat', '$no_telp')");
            if ($query) {
                echo '<script>alert("Data berhasil ditambahkan!"); location.href="?page=pelanggan"</script>';
            } else {
                echo '<script>alert("Data gagal ditambahkan!")</script>';
            }
        } else {
            echo '<script>alert("Nomor telepon harus terdiri dari angka 10 hingga 14 digit!")</script>';
        }
    }
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
                <td><input class="form-control" type="text" name="nama_pelanggan" required></td>
            </tr>
            <tr>
                <td>Alamat </td>
                <td>:</td>
                <td> <textarea name="alamat" rows="5" class="form-control" required></textarea></td>
            </tr>
            <tr>
                <td>Nomor Telepon </td>
                <td>:</td>
                <td><input class="form-control" type="text" name="no_telp" required></td>
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
