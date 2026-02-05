CREATE TABLE pemilih (
    id SERIAL PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    nif VARCHAR(20) UNIQUE NOT NULL,
    no_telp VARCHAR(15)
);

CREATE TABLE suara (
    id SERIAL PRIMARY KEY,
    nif VARCHAR(20) NOT NULL,
    kandidat_id INT NOT NULL
);

-- Lihat semua pemilih
SELECT nama, nif, no_telp
FROM pemilih
ORDER BY nama ASC;

-- Hitung suara per kandidat (untuk halaman hasil)
SELECT kandidat_id, COUNT(*) AS total_suara
FROM suara
GROUP BY kandidat_id
ORDER BY kandidat_id;

--data nama pemilih dan yang dipilih
SELECT
    p.nama     AS nama_pemilih,
    p.nif,
    p.no_telp,
    CASE s.kandidat_id
        WHEN 1 THEN 'Afrizal'
        WHEN 2 THEN 'Nabila Rindi'
        WHEN 3 THEN 'Yassa Aji'
        ELSE 'Tidak diketahui'
    END AS kandidat_dipilih
FROM pemilih p
JOIN suara s ON p.nif = s.nif
ORDER BY p.nama ASC;

--cek tabel pemilih & suara
select * from pemilih;
select * from suara;

--hapus data tabel
TRUNCATE TABLE suara;
TRUNCATE TABLE pemilih;
