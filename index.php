<?php
include "koneksi.php";

if (isset($_POST['vote'])) {
    $nama     = $_POST['nama'];
    $nif      = $_POST['nif'];
    $no_telp  = $_POST['no_telp'];
    $kandidat = $_POST['kandidat'];

    // Simpan pemilih
    pg_query_params(
        $conn,
        "INSERT INTO pemilih (nama, nif, no_telp)
         VALUES ($1, $2, $3)
         ON CONFLICT (nif) DO NOTHING",
        [$nama, $nif, $no_telp]
    );

    // Cek apakah sudah memilih
    $cek = pg_query_params(
        $conn,
        "SELECT 1 FROM suara WHERE nif = $1",
        [$nif]
    );

    if (pg_num_rows($cek) == 0) {
        pg_query_params(
            $conn,
            "INSERT INTO suara (nif, kandidat_id)
             VALUES ($1, $2)",
            [$nif, $kandidat]
        );
    }

    header("Location: index.php");
    exit;
}
?>


<!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
            <title>Pemilihan Ketua Umum UKM Olah Raga</title>

            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
            body {
                min-height: 100vh;
                display: flex;
                flex-direction: column;
            }

            .header-ukm {
                background-color: #ffd900;
            }

            .btn-check { display: none; }

            .kandidat-card {
                cursor: pointer;
                transition: 0.3s;
                position: relative;
            }

            .btn-check:checked + .kandidat-card {
                border: 3px solid #00b327;
                background-color: #9efa9e41;
                transform: scale(1.02);
            }

            .terpilih-badge {
                display: none;
                position: absolute;
                top: 10px;
                right: 10px;
            }

            .btn-check:checked + .kandidat-card .terpilih-badge {
                display: inline-block;
            }

            footer {
                margin-top: 10px;
                background-color: #fcdf82;
            }

            .header-ukm {
                border-bottom: 2px solid #747474;
                border-top: 2px solid #747474;
            }

            .form-control::placeholder {
                font-size: 0.85rem;
                color: #6c757d;
            }
        </style>
    </head>
    <body class="bg-light">
        <!-- HEADER -->
        <header class="header-ukm py-3 mb-4" >
            <div class="container">
                <div class="d-flex align-items-center justify-content-between">
                    <!-- Logo UKM -->
                    <img src="logo_ukm.png"
                        alt="Logo UKM"
                        style="height:90px;">
                    <!-- Judul -->
                    <div class="text-center flex-grow-1">
                        <h4 class="fw-bold mb-0">
                            Pemilihan Ketua Umum UKM Olah Raga
                        </h4>
                        <small>Periode 2026/2027</small>
                    </div>
                    <!-- Logo Polinema -->
                    <img src="logo_polinema.png"
                        alt="Logo Polinema"
                        style="height:90px;">
                </div>
            </div>
        </header>
        <!-- CONTENT -->
        <div class="container">
            <div class="card shadow">
                <div class="card-body">
                    <form method="POST" id="formVote">
                <div class="mb-3">
                    <label class="form-label">Nama Pemilih</label>
                    <input type="text" name="nama" id="nama" class="form-control" placeholder="contoh: Iqbal Aldiansyah">
                </div>

                <div class="mb-3">
                    <label class="form-label">NIF</label>
                    <input type="text" name="nif" id="nif" class="form-control" placeholder="contoh: 240206_062">
                </div>
                <div class="mb-3">
                    <label class="form-label">Nomor Telepon (WA)</label>
                    <input type="text" name="no_telp" id="no_telp" class="form-control" placeholder="contoh: 088991370642">
                </div>

                <h4 class="text-center mt-4 mb-3">Pilih Kandidat</h4>
            <div class="row">
                <?php $q = pg_query($conn, "SELECT * FROM kandidat ORDER BY id"); while ($k = pg_fetch_assoc($q)) { ?>
                    <div class="col-md-4 mb-3">
                        <input type="radio" class="btn-check" name="kandidat" id="kandidat<?= $k['id'] ?>" value="<?= $k['id'] ?>">
                        
                        <label class="card kandidat-card shadow-sm" for="kandidat<?= $k['id'] ?>">
                            <span class="badge bg-success terpilih-badge">TERPILIH</span>
                            <div class="card-body text-center">
                                <img src="<?= $k['foto'] ?>" class="img-fluid rounded mb-3" style="max-height:200px">
                                <h5><?= $k['nama'] ?></h5>

                                <p class="mb-1"><strong>Visi</strong></p>
                                <p><?= $k['visi'] ?></p>

                                <p class="mb-1"><strong>Misi</strong></p>
                                <p><?= $k['misi'] ?></p>
                            </div>
                        </label>
                    </div>
                <?php } ?>
            </div>

        <!-- TOMBOL KIRIM -->
        <div class="text-center mt-3">
            <button type="button" class="btn btn-warning px-5" onclick="cekForm()">
                Kirim Suara
            </button>
        </div>
<!-- MODAL KONFIRMASI -->
<div class="modal fade" id="modalKonfirmasi" tabindex="-1">
<div class="modal-dialog modal-dialog-centered">
<div class="modal-content">

<div class="modal-header bg-warning">
    <h5 class="modal-title fw-bold">Konfirmasi Pemilihan</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body text-center">
    <p>Apakah Anda yakin dengan pilihan Anda?</p>
    <p class="text-danger small">Suara tidak dapat diubah.</p>
</div>

<div class="modal-footer justify-content-center">
    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
        Batal
    </button>

    <button type="submit" name="vote" class="btn btn-warning">
        Ya, Kirim Suara
    </button>
</div>

</div>
</div>
</div>

</form>

</div>
</div>
</div>

<footer class="py-3 text-center">
<small>© 2026 UKM Olah Raga Politeknik Negeri Malang</small>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
function cekForm() {
    const nama = document.getElementById("nama").value.trim();
    const nif  = document.getElementById("nif").value.trim();
    const kandidat = document.querySelector('input[name="kandidat"]:checked');

    if (nama === "" || nif === "") {
        alert("Nama dan NIF wajib diisi!");
        return;
    }

    if (!kandidat) {
        alert("Silakan pilih kandidat terlebih dahulu!");
        return;
    }

    const modal = new bootstrap.Modal(
        document.getElementById("modalKonfirmasi")
    );
    modal.show();
}
</script>

</body>
</html>
