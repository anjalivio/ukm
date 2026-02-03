<?php
include "koneksi.php";

$query = "
    SELECT k.nama, COUNT(s.id) AS total_suara
    FROM kandidat k
    LEFT JOIN suara s ON k.id = s.kandidat_id
    GROUP BY k.nama
    ORDER BY total_suara DESC
";

$q = pg_query($conn, $query);

if (!$q) {
    die("Query gagal");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Hasil Pemilihan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-4">
    <h2 class="text-center mb-4">Hasil Pemilihan Ketua Umum UKM Olahraga</h2>

    <div class="card shadow">
        <div class="card-body">

            <table class="table table-bordered table-striped text-center">
                <thead class="table-dark">
                    <tr>
                        <th>Kandidat</th>
                        <th>Jumlah Suara</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($h = pg_fetch_assoc($q)) { ?>
                    <tr>
                        <td><?= $h['nama'] ?></td>
                        <td><?= $h['total_suara'] ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>

            <div class="text-center mt-3">
                <a href="index.php" class="btn btn-danger">Kembali ke Voting</a>
            </div>

        </div>
    </div>
</div>

</body>
</html>
