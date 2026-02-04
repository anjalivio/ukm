
CREATE TABLE pemilih (
    id SERIAL PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    nif VARCHAR(20) UNIQUE NOT NULL
);

ALTER TABLE pemilih
ADD COLUMN no_telp VARCHAR(15);


CREATE TABLE kandidat (
    id SERIAL PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    foto VARCHAR(100),
    visi TEXT,
    misi TEXT
);

CREATE TABLE suara (
    id SERIAL PRIMARY KEY,
    nif VARCHAR(20) NOT NULL,
    kandidat_id INT NOT NULL,
    CONSTRAINT fk_kandidat
        FOREIGN KEY (kandidat_id)
        REFERENCES kandidat(id)
        ON DELETE CASCADE
);

-- Data 3 kandidat
INSERT INTO kandidat (nama, foto, visi, misi) VALUES
('kandidat 1', '1.jpg', 'Menjadikan UKM olahraga berprestasi', 'Latihan rutin dan turnamen aktif'),
('kandidat 2', '2.jpg', 'UKM olahraga solid dan profesional', 'Manajemen rapi dan prestasi nasional'),
('kandidat 3', '3.jpg', 'UKM olahraga inklusif dan kompetitif', 'Rekrutmen terbuka dan event rutin');

SELECT nama, nif, no_telp
FROM pemilih
ORDER BY nama ASC;

SELECT 
    p.nama   AS nama_pemilih,
    p.nif,
    p.no_telp,
    k.nama   AS kandidat_dipilih
FROM pemilih p
JOIN suara s ON p.nif = s.nif
JOIN kandidat k ON s.kandidat_id = k.id
ORDER BY p.nama;

TRUNCATE TABLE pemilih CASCADE;
TRUNCATE TABLE suara CASCADE;

select * from suara;
