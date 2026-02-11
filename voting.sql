CREATE TABLE pemilih (
    id SERIAL PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    nif VARCHAR(20) UNIQUE,               
    no_telp VARCHAR(15) UNIQUE NOT NULL   
);

CREATE TABLE suara (
    id SERIAL PRIMARY KEY,
    kandidat_id INT NOT NULL,
    pemilih_id INT NOT NULL UNIQUE,
    CONSTRAINT fk_pemilih
        FOREIGN KEY (pemilih_id)
        REFERENCES pemilih(id)
        ON DELETE CASCADE
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
    p.nama AS nama_pemilih,
    p.nif,
    p.no_telp,
    CASE s.kandidat_id
        WHEN 1 THEN 'Afrizal'
        WHEN 2 THEN 'Nabila Rindi'
        WHEN 3 THEN 'Yassa Aji'
        ELSE 'Tidak diketahui'
    END AS kandidat_dipilih
FROM pemilih p
JOIN suara s ON p.id = s.pemilih_id
ORDER BY p.nama ASC;

--cek tabel pemilih & suara
select * from pemilih;
select * from suara;

--hapus data tabel
TRUNCATE TABLE suara RESTART IDENTITY CASCADE;
TRUNCATE TABLE pemilih RESTART IDENTITY CASCADE;



