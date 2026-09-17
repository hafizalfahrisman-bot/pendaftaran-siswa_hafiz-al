<?php
// =====================================================
// PROGRAM SEDERHANA: PENDAFTARAN SISWA
// Nomor Absen : 16  (Tema No. 6 - Pendaftaran Siswa)
// Data yang di-CRUD : Calon siswa
// Fitur Tambahan    : Cetak bukti pendaftaran
// Dibuat menggunakan dasar-dasar PHP:
// - variabel & array
// - session (untuk menyimpan data sementara)
// - $_POST & $_GET (form handling)
// - percabangan (if/else)
// - perulangan (foreach)
// - function
// =====================================================

session_start();

// Inisialisasi array penyimpanan data calon siswa di session
if (!isset($_SESSION['siswa'])) {
    $_SESSION['siswa'] = array();
}

// Function untuk menambah data calon siswa (CREATE)
function tambahSiswa($nama, $nisn, $asal_sekolah, $kelas_tujuan) {
    $data = array(
        'nama'         => $nama,
        'nisn'         => $nisn,
        'asal_sekolah' => $asal_sekolah,
        'kelas_tujuan' => $kelas_tujuan,
        'no_pendaftaran' => 'PPDB-' . date('Y') . '-' . str_pad(count($_SESSION['siswa']) + 1, 3, '0', STR_PAD_LEFT),
        'tanggal_daftar' => date('d-m-Y H:i')
    );
    $_SESSION['siswa'][] = $data;
}

// Function untuk menghapus data calon siswa (DELETE)
function hapusSiswa($index) {
    if (isset($_SESSION['siswa'][$index])) {
        unset($_SESSION['siswa'][$index]);
        $_SESSION['siswa'] = array_values($_SESSION['siswa']); // reset ulang index array
    }
}

// Proses form tambah data
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['aksi']) && $_POST['aksi'] == 'tambah') {
    $nama         = htmlspecialchars($_POST['nama']);
    $nisn         = htmlspecialchars($_POST['nisn']);
    $asal_sekolah = htmlspecialchars($_POST['asal_sekolah']);
    $kelas_tujuan = htmlspecialchars($_POST['kelas_tujuan']);

    // Validasi sederhana: semua field wajib diisi
    if ($nama != "" && $nisn != "" && $asal_sekolah != "" && $kelas_tujuan != "") {
        tambahSiswa($nama, $nisn, $asal_sekolah, $kelas_tujuan);
        $pesan = "Data calon siswa berhasil didaftarkan!";
    } else {
        $pesan = "Semua kolom wajib diisi!";
    }
}

// Proses hapus data
if (isset($_GET['hapus'])) {
    hapusSiswa($_GET['hapus']);
}

// Proses cetak bukti pendaftaran (fitur tambahan)
$cetak_data = null;
if (isset($_GET['cetak'])) {
    $index = $_GET['cetak'];
    if (isset($_SESSION['siswa'][$index])) {
        $cetak_data = $_SESSION['siswa'][$index];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Program Pendaftaran Siswa Baru</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f6f9; margin: 0; padding: 20px; }
        .container { max-width: 800px; margin: auto; background: #fff; padding: 25px 30px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        h1 { color: #2c3e50; text-align: center; font-size: 22px; }
        h2 { color: #2c3e50; border-bottom: 2px solid #3498db; padding-bottom: 5px; font-size: 18px; }
        label { display: block; margin-top: 10px; font-weight: bold; }
        input[type=text] { width: 100%; padding: 8px; margin-top: 4px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
        button, .btn { background: #3498db; color: #fff; border: none; padding: 8px 14px; border-radius: 4px; cursor: pointer; text-decoration: none; font-size: 14px; }
        button:hover, .btn:hover { background: #2980b9; }
        .btn-hapus { background: #e74c3c; }
        .btn-hapus:hover { background: #c0392b; }
        .btn-cetak { background: #27ae60; }
        .btn-cetak:hover { background: #1e8449; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 14px; }
        th { background: #3498db; color: #fff; }
        .pesan { background: #dff0d8; color: #3c763d; padding: 10px; border-radius: 4px; margin-top: 10px; }
        .bukti { border: 2px dashed #2c3e50; padding: 20px; margin-top: 20px; background: #fdfdfd; }
        .bukti h3 { text-align: center; margin-top: 0; }
        .kosong { text-align: center; color: #888; padding: 15px; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
<div class="container">
    <h1>📋 Program Pendaftaran Siswa Baru (PPDB)</h1>
    <p style="text-align:center; color:#666;">Absen 16 - Tema: Pendaftaran Siswa</p>

    <?php if (isset($pesan)) { ?>
        <div class="pesan"><?php echo $pesan; ?></div>
    <?php } ?>

    <!-- FORM TAMBAH DATA -->
    <div class="no-print">
        <h2>Form Pendaftaran Calon Siswa</h2>
        <form method="POST" action="">
            <input type="hidden" name="aksi" value="tambah">

            <label>Nama Lengkap</label>
            <input type="text" name="nama" required>

            <label>NISN</label>
            <input type="text" name="nisn" required>

            <label>Asal Sekolah</label>
            <input type="text" name="asal_sekolah" required>

            <label>Kelas Tujuan</label>
            <input type="text" name="kelas_tujuan" placeholder="Contoh: VII-A" required>

            <br><br>
            <button type="submit">Daftarkan Siswa</button>
        </form>

        <h2>Daftar Calon Siswa</h2>
        <?php if (count($_SESSION['siswa']) == 0) { ?>
            <p class="kosong">Belum ada calon siswa yang mendaftar.</p>
        <?php } else { ?>
            <table>
                <tr>
                    <th>No</th>
                    <th>No. Pendaftaran</th>
                    <th>Nama</th>
                    <th>NISN</th>
                    <th>Asal Sekolah</th>
                    <th>Kelas Tujuan</th>
                    <th>Aksi</th>
                </tr>
                <?php
                $no = 1;
                foreach ($_SESSION['siswa'] as $index => $s) {
                ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo $s['no_pendaftaran']; ?></td>
                    <td><?php echo $s['nama']; ?></td>
                    <td><?php echo $s['nisn']; ?></td>
                    <td><?php echo $s['asal_sekolah']; ?></td>
                    <td><?php echo $s['kelas_tujuan']; ?></td>
                    <td>
                        <a class="btn btn-cetak" href="?cetak=<?php echo $index; ?>">Cetak Bukti</a>
                        <a class="btn btn-hapus" href="?hapus=<?php echo $index; ?>" onclick="return confirm('Yakin hapus data ini?')">Hapus</a>
                    </td>
                </tr>
                <?php } ?>
            </table>
        <?php } ?>
    </div>

    <!-- BUKTI PENDAFTARAN (FITUR TAMBAHAN) -->
    <?php if ($cetak_data != null) { ?>
        <div class="bukti">
            <h3>BUKTI PENDAFTARAN SISWA BARU</h3>
            <p><b>No. Pendaftaran</b> : <?php echo $cetak_data['no_pendaftaran']; ?></p>
            <p><b>Nama Lengkap</b> &nbsp;: <?php echo $cetak_data['nama']; ?></p>
            <p><b>NISN</b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <?php echo $cetak_data['nisn']; ?></p>
            <p><b>Asal Sekolah</b> : <?php echo $cetak_data['asal_sekolah']; ?></p>
            <p><b>Kelas Tujuan</b> : <?php echo $cetak_data['kelas_tujuan']; ?></p>
            <p><b>Tanggal Daftar</b> : <?php echo $cetak_data['tanggal_daftar']; ?></p>
            <p style="margin-top:30px;">Harap simpan bukti ini sebagai tanda telah mendaftar.</p>
            <button class="no-print" onclick="window.print()">🖨️ Cetak Halaman Ini</button>
        </div>
    <?php } ?>

</div>
</body>
</html>