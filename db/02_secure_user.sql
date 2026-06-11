-- Creazione di un database separato per l'app sicura
CREATE DATABASE IF NOT EXISTS sql_injection_secure_db;
USE sql_injection_secure_db;

-- Ricreazione delle tabelle (uguali a 01_init.sql)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    telefono VARCHAR(20),
    permissions INT(1) NOT NULL DEFAULT 0
);

CREATE TABLE grades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT,
    grade INT,
    subject VARCHAR(50),
    date DATE DEFAULT CURRENT_DATE,
    FOREIGN KEY (student_id) REFERENCES users(id)
);

-- Inserimento degli stessi dati iniziali
INSERT INTO users (username, password, email, telefono, permissions) VALUES
('admin_supremo', 'Sup3rS3cr3t!', 'admin@scuola.it', '333-0000000', 2),
('prof_rossi', 'rossi123', 'mario.rossi@scuola.it', '333-1234567', 1),
('prof_bianchi', 'bianchi_math', 'luigi.bianchi@scuola.it', '333-7654321', 1),
('alunno_verdi', 'verdi2026', 'giuseppe.verdi@studenti.it', '333-1112223', 0),
('alunno_neri', 'password_debole', 'paolo.neri@studenti.it', '333-4445556', 0),
('alunno_gialli', 'qwerty', 'marco.gialli@studenti.it', '333-7778889', 0);

INSERT INTO grades (student_id, grade, subject, date) VALUES
(4, 7, 'Matematica', '2026-03-25'),
(4, 4, 'Scienze', '2026-04-15'),
(4, 9, 'Storia', '2026-04-19'),
(4, 8, 'Inglese', '2026-05-02'),
(5, 5, 'Matematica', '2026-03-10'),
(5, 3, 'Scienze', '2026-04-15'),
(5, 5, 'Storia', '2026-05-20'),
(5, 4, 'Inglese', '2026-05-25'),
(6, 10, 'Scienze', '2026-06-10'),
(6, 2, 'Storia', '2026-07-15'),
(6, 9, 'Matematica', '2026-08-20'),
(6, 8, 'Inglese', '2026-09-01');

-- Creazione di un utente con privilegi limitati (Principio dei Minimi Privilegi)
CREATE USER IF NOT EXISTS 'lab_user_secure'@'%' IDENTIFIED BY 'lab_password_secure';

-- Concediamo solo i permessi strettamente necessari SUL NUOVO DATABASE (niente DROP, niente DELETE)
GRANT SELECT, INSERT, UPDATE ON sql_injection_secure_db.* TO 'lab_user_secure'@'%';

FLUSH PRIVILEGES;
