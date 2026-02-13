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
SELECT nama, identitas, no_telp
FROM pemilih
ORDER BY nama ASC;

--cek tabel pemilih & suara
select * from pemilih;
select * from suara;

--hapus data tabel
TRUNCATE TABLE suara RESTART IDENTITY CASCADE;
TRUNCATE TABLE pemilih RESTART IDENTITY CASCADE;

--ganti kolom nif menjadi identitas di tabel pemilih(angkatan 25 input pakai nim)
ALTER TABLE pemilih
RENAME COLUMN nif TO identitas;

ALTER TABLE pemilih
ADD CONSTRAINT unique_identitas UNIQUE (identitas);


--Siapa Memilih Siapa + Kategori + Poin
SELECT
    p.nama AS nama_pemilih,
    p.identitas,
    p.no_telp,

    -- Kategori berdasarkan awalan
    CASE
        WHEN LEFT(p.identitas, 2) = '23' THEN 'SC'
        WHEN LEFT(p.identitas, 2) = '24' THEN 'OC'
        WHEN LEFT(p.identitas, 2) = '25' THEN 'Fungsionaris'
        ELSE 'Tidak diketahui'
    END AS kategori_pemilih,

    -- Kandidat dipilih
    CASE s.kandidat_id
        WHEN 1 THEN 'Afrizal'
        WHEN 2 THEN 'Yassa Aji'
        WHEN 3 THEN 'Nabila Rindi'
    END AS kandidat_dipilih,

    -- Bobot poin
    CASE
        WHEN LEFT(p.identitas, 2) = '24' THEN 3
        WHEN LEFT(p.identitas, 2) = '23' THEN 2
        WHEN LEFT(p.identitas, 2) = '25' THEN 1
        ELSE 0
    END AS poin_diberikan

FROM pemilih p
JOIN suara s ON p.id = s.pemilih_id
ORDER BY p.nama ASC;


--Hitung Total Poin Setiap Kandidat
SELECT
    CASE s.kandidat_id
        WHEN 1 THEN 'Afrizal'
        WHEN 2 THEN 'Yassa Aji'
        WHEN 3 THEN 'Nabila Rindi'
    END AS nama_kandidat,

    SUM(
        CASE
            WHEN LEFT(p.identitas, 2) = '24' THEN 3
            WHEN LEFT(p.identitas, 2) = '23' THEN 2
            WHEN LEFT(p.identitas, 2) = '25' THEN 1
            ELSE 0
        END
    ) AS total_poin

FROM suara s
JOIN pemilih p ON s.pemilih_id = p.id
GROUP BY s.kandidat_id
ORDER BY total_poin DESC;





