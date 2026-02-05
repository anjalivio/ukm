<?php
include "koneksi.php";

$kandidatList = [
    1 => "Afrizal",
    2 => "Nabila Rindi",
    3 => "Yassa Aji",
];

$hasil = [];

foreach ($kandidatList as $id => $nama) {
    $q = pg_query_params(
        $conn,
        "SELECT COUNT(*) FROM suara WHERE kandidat_id = $1",
        [$id]
    );

    $total = pg_fetch_result($q, 0, 0);

    $hasil[] = [
        "nama"  => $nama,
        "total" => $total
    ];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Hasil Pemilihan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container py-4">
    <h2 class="text-center mb-4">Hasil Pemilihan Ketua Umum UKM Olah Raga</h2>

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
                <?php foreach ($hasil as $h) { ?>
                    <tr>
                        <td><?= $h['nama'] ?></td>
                        <td><?= $h['total'] ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>
