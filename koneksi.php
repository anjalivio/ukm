<?php
$conn = pg_connect("
    host=localhost
    port=5432
    dbname=evoting_ukm
    user=postgres
    password=12345
");

if (!$conn) {
    die("Koneksi database gagal");
}
?>
