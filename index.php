<?php
include "koneksi.php";

$kandidatList = [
    ["id" => 1, "nama" => "Afrizal", "foto" => "Kandidat1.jpg"],
    ["id" => 2, "nama" => "Nabila Rindi", "foto" => "Kandidat2.jpg"],
    ["id" => 3, "nama" => "Yassa Aji", "foto" => "Kandidat3.jpg"],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nama     = trim($_POST['nama'] ?? '');
    $nif      = trim($_POST['nif'] ?? '');
    $no_telp  = trim($_POST['no_telp'] ?? '');
    $kandidat = $_POST['kandidat'] ?? '';

    if (!preg_match("/^[A-Za-z\s]{3,}$/", $nama)) {
        die("Nama tidak valid");
    }

    if ($nif !== '' && !preg_match("/^[0-9]{2}0206_[0-9]{3}$/", $nif)) {
        die("Format NIF salah. Contoh: 240206_123");
    }

    if (!preg_match("/^08[0-9]{8,11}$/", $no_telp)) {
        die("Nomor WA tidak valid");
    }

    if (!in_array($kandidat, [1,2,3])) {
        die("Kandidat tidak valid");
    }

    $result = pg_query_params(
        $conn,
        "INSERT INTO pemilih (nama, nif, no_telp)
         VALUES ($1, NULLIF($2, ''), $3)
         ON CONFLICT (no_telp)
         DO UPDATE SET nama = EXCLUDED.nama
         RETURNING id",
        [$nama, $nif, $no_telp]
    );

    $pemilih = pg_fetch_assoc($result);
    $pemilih_id = $pemilih['id'];

    $cekVote = pg_query_params(
        $conn,
        "SELECT 1 FROM suara WHERE pemilih_id = $1",
        [$pemilih_id]
    );

    if (pg_num_rows($cekVote) > 0) {
        die("Anda sudah melakukan voting!");
    }

    pg_query_params(
        $conn,
        "INSERT INTO suara (pemilih_id, kandidat_id)
         VALUES ($1, $2)",
        [$pemilih_id, $kandidat]
    );

    header("Location: index.php?sukses=1");
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
            border-bottom: 2px solid #747474;
            border-top: 2px solid #747474;
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

        .form-control::placeholder {
            font-size: 0.85rem;
            color: #6c757d;
        }
    </style>
</head>

<body class="bg-light">

<!-- HEADER -->
<header class="header-ukm py-3 mb-4">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between">
            <img src="logo_ukm.png" alt="Logo UKM" style="height:90px;">

            <div class="text-center flex-grow-1">
                <h4 class="fw-bold mb-0">Pemilihan Ketua Umum UKM Olah Raga</h4>
                <small>Periode 2026/2027</small>
            </div>

            <img src="logo_polinema.png" alt="Logo Polinema" style="height:90px;">
        </div>
    </div>
</header>

<!-- CONTENT -->
<div class="container">
    <div class="card shadow">
        <div class="card-body">

            <form method="POST" id="formVote">

                <div class="mb-3">
                    <label class="form-label">Nama</label>
                        <input type="text"
                            name="nama"
                            id="nama"
                            class="form-control"
                            placeholder="contoh: Anjali Violita Pramestri"
                            required
                            minlength="5"
                            pattern="[A-Za-z\s]+"
                            title="Nama hanya boleh huruf dan spasi">
                </div>

               <div class="mb-3">
                    <label class="form-label">NIF</label>
                        <input type="text"
                                name="nif"
                                id="nif"
                                class="form-control"
                                placeholder="contoh: 240206_123"
                                pattern="^[0-9]{2}0206_[0-9]{3}$"
                                title="Format NIF: YY0206_XXX (contoh: 240206_123)">
                </div>

               <div class="mb-3">
                    <label class="form-label">Nomor Telepon (WA)</label>
                        <input  type="tel"
                                name="no_telp"
                                id="no_telp"
                                class="form-control"
                                placeholder="contoh: 088991370642"
                                required
                                pattern="^08[0-9]{8,11}$"
                                title="Nomor WA harus diawali 08 dan hanya angka">
                </div>

                <h4 class="text-center mt-4 mb-3">Pilih Kandidat</h4>

                <div class="row">
                    <?php foreach ($kandidatList as $k) { ?>
                        <div class="col-md-4 mb-3">
                            <input type="radio" class="btn-check"
                                   name="kandidat"
                                   id="kandidat<?= $k['id'] ?>"
                                   value="<?= $k['id'] ?>">

                            <label class="card kandidat-card shadow-sm"
                                   for="kandidat<?= $k['id'] ?>">
                                <span class="badge bg-success terpilih-badge">TERPILIH</span>

                                <div class="card-body text-center">
                                    <img src="<?= $k['foto'] ?>"
                                         class="img-fluid rounded mb-3"
                                         style="max-height:200px">
                                    <h5><?= $k['nama'] ?></h5>
                                </div>
                            </label>
                        </div>
                    <?php } ?>
                </div>

                <div class="text-center mt-3">
                    <button type="button" class="btn btn-warning px-5" onclick="cekForm()">
                        Kirim Suara
                    </button>
                </div>

                <!-- MODAL -->
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
    const telp = document.getElementById("no_telp").value.trim();
    const kandidat = document.querySelector('input[name="kandidat"]:checked');

    const regexNama = /^[A-Za-z\s]{5,}$/;
    const regexNif  = /^[0-9]{2}0206_[0-9]{3}$/;
    const regexTelp = /^08[0-9]{8,11}$/;

    if (!regexNama.test(nama)) {
        alert("Nama hanya boleh huruf dan minimal 5 karakter.");
        return;
    }

    if (nif !== '' && !regexNif.test(nif)) {
        alert("Format NIF salah! Contoh: 240206_062");
        return;
    }

    if (!regexTelp.test(telp)) {
        alert("Nomor WA tidak valid!");
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
