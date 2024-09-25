<div class="container-fluid px-4">
    <h1 class="mt-4">Pembelian</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Pembelian</li>
    </ol>
    <a href="?page=tambahbeli" id="tambahPembelianBtn" class="btn btn-primary"> + Tambah Pembelian</a>
    <hr>
    <table id="tabelLaporan" class="table table-bordered">
        <tr>
            <th>ID Pembelian</th>
            <th>Tanggal Pembelian</th>
            <th>ID Pelanggan</th>
            <th>Pelanggan</th>
            <th>Total Harga</th>
            <th>Harga Setelah Diskon</th>
            <th class="aksi-column">Aksi</th>
        </tr>

        <?php
        // Query untuk mendapatkan data penjualan dan pelanggan
        $query = "SELECT * FROM penjualan 
                  LEFT JOIN pelanggan ON penjualan.id_pelanggan = pelanggan.id_pelanggan";

        $result = mysqli_query($koneksi, $query);

        while ($data = mysqli_fetch_array($result)) {
            $total_harga = $data['total_harga'] ? $data['total_harga'] : 0;
            $diskon = 0.10; // Diskon 15%
            $harga_diskon = $total_harga - ($total_harga * $diskon);

            // Cek apakah pembayaran sudah lunas
            $bayarClass = (isset($_SESSION['bayar_lunas'][$data['id_penjualan']])) 
                          ? 'btn-warning' 
                          : 'btn-primary'; // Status pembayaran

            $isLunas = isset($_SESSION['bayar_lunas'][$data['id_penjualan']]);

            // Format ID Pembelian menjadi trx-0001
            $idPenjualanFormatted = 'trx-' . str_pad($data['id_penjualan'], 4, '0', STR_PAD_LEFT);
            // Format ID Pelanggan menjadi plg-0001
            $idPelangganFormatted = 'plg-' . str_pad($data['id_pelanggan'], 4, '0', STR_PAD_LEFT);
        ?>
            <tr>
                <td><?php echo $idPenjualanFormatted; ?></td>
                <td><?php echo $data['tanggal_penjualan']; ?></td>
                <td><?php echo $idPelangganFormatted; ?></td>
                <td><?php echo htmlspecialchars($data['nama_pelanggan']); ?></td>
                <td><?php echo number_format($total_harga, 0, ',', '.'); ?></td>
                <td><?php echo number_format($harga_diskon, 0, ',', '.'); ?></td>
                <td class="aksi-column">
                    <a href="?page=detail&id=<?php echo $data['id_penjualan']; ?>" class="btn btn-secondary">Detail</a>
                    
                    <?php if ($isLunas): ?>
                        <button class="btn btn-warning" disabled>Pembayaran Selesai</button>
                    <?php else: ?>
                        <button id="bayarBtn-<?php echo $data['id_penjualan']; ?>" class="btn <?php echo $bayarClass; ?>" 
                                data-bs-toggle="modal" data-bs-target="#paymentModal" 
                                onclick="setTotalBayar(<?php echo $harga_diskon; ?>, <?php echo $data['id_penjualan']; ?>)">
                            Bayar
                        </button>
                    <?php endif; ?>

                    <!-- Tombol Hapus -->
                    <a href="?page=buang&id=<?php echo $data['id_penjualan']; ?>" class="btn btn-danger" onclick="return confirm('Anda yakin ingin menghapus pembelian ini?')">Hapus</a>
                </td>
            </tr>
        <?php
        }
        ?>
    </table>
</div>

<!-- Modal Pembayaran -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentModalLabel">Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Total yang harus dibayar: <strong id="totalPembayaran"></strong></p>
                <div class="mb-3">
                    <label for="uangDibayar" class="form-label">Masukkan Uang Dibayarkan</label>
                    <input type="number" id="uangDibayar" class="form-control" placeholder="Jumlah Uang" required>
                </div>
                <div id="kembalianDisplay" class="mt-2"></div>
            </div>
            <div class="modal-footer">
                <button onclick="hitungKembalian()" class="btn btn-success">Hitung Kembalian</button>
                <button onclick="prosesPembayaran()" class="btn btn-primary">Bayar</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
// Variabel global untuk menyimpan total bayar dan ID penjualan
let totalBayar = 0;
let idPenjualan = null;

// Fungsi untuk set total bayar dan id penjualan saat modal dibuka
function setTotalBayar(total, id) {
    totalBayar = parseFloat(total); // Pastikan ini angka
    idPenjualan = id;
    document.getElementById('totalPembayaran').innerText = 'Rp ' + totalBayar.toLocaleString('id-ID');
    document.getElementById('uangDibayar').value = ''; // Reset input uang dibayar
    document.getElementById('kembalianDisplay').innerText = ''; // Reset tampilan kembalian
}

// Fungsi untuk menghitung kembalian
function hitungKembalian() {
    let uangDibayar = document.getElementById('uangDibayar').value;
    
    if (uangDibayar === '') {
        document.getElementById('kembalianDisplay').innerText = 'Masukkan jumlah uang dibayar!';
        return;
    }

    uangDibayar = parseFloat(uangDibayar); // Konversi ke angka
    if (isNaN(uangDibayar) || uangDibayar < totalBayar) {
        document.getElementById('kembalianDisplay').innerText = 'Jumlah uang tidak mencukupi';
    } else {
        const kembalian = uangDibayar - totalBayar;
        document.getElementById('kembalianDisplay').innerText = 'Kembalian: Rp ' + kembalian.toLocaleString('id-ID');
    }
}

// Fungsi untuk memproses pembayaran
function prosesPembayaran() {
    const uangDibayar = parseFloat(document.getElementById('uangDibayar').value);

    if (isNaN(uangDibayar) || uangDibayar < totalBayar) {
        alert('Jumlah uang tidak mencukupi untuk melakukan pembayaran');
        return;
    }

    // Kirim data ke server dengan form post
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '';

    const idPenjualanInput = document.createElement('input');
    idPenjualanInput.type = 'hidden';
    idPenjualanInput.name = 'id_penjualan';
    idPenjualanInput.value = idPenjualan;

    const bayarInput = document.createElement('input');
    bayarInput.type = 'hidden';
    bayarInput.name = 'bayar';
    bayarInput.value = '1';

    const uangDibayarInput = document.createElement('input');
    uangDibayarInput.type = 'hidden';
    uangDibayarInput.name = 'uang_dibayarkan';
    uangDibayarInput.value = uangDibayar;

    form.appendChild(idPenjualanInput);
    form.appendChild(bayarInput);
    form.appendChild(uangDibayarInput);
    document.body.appendChild(form);
    form.submit();
}
</script>

<?php
// Handle pembayaran
if (isset($_POST['bayar'])) {
    $id_penjualan = mysqli_real_escape_string($koneksi, $_POST['id_penjualan']);
    $uang_dibayarkan = mysqli_real_escape_string($koneksi, $_POST['uang_dibayarkan']);

    // Update penjualan dengan jumlah uang dibayarkan
    $query = "UPDATE penjualan SET uang_dibayarkan = '$uang_dibayarkan' WHERE id_penjualan = '$id_penjualan'";
    mysqli_query($koneksi, $query);

    // Simpan ID pembayaran yang telah lunas ke session
    $_SESSION['bayar_lunas'][$id_penjualan] = true;

}

// Handle penghapusan pembelian
if (isset($_GET['page']) && $_GET['page'] === 'buang' && isset($_GET['id'])) {
    $id_penjualan = mysqli_real_escape_string($koneksi, $_GET['id']);
    
    // Hapus pembelian dari database
    $query = "DELETE FROM penjualan WHERE id_penjualan = '$id_penjualan'";
    
    if (mysqli_query($koneksi, $query)) {
        echo '<script>alert("Data pembelian berhasil dihapus!"); location.href="?page=pembelian";</script>';
    } else {
        echo '<script>alert("Gagal menghapus data pembelian!");</script>';
    }
}
?>
