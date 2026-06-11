USE sql_injection_db;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL
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

INSERT INTO users (username, password, permissions) VALUES
('admin', 'admin', 2),
('prof1', 'prof1', 1),
('alunno1', 'alunno1', 0);

INSERT INTO grades (student_id, grade, subject, date) VALUES
(1, 7, 'Matematica', '2026-03-25'),
(1, 4, 'Scienze', '2026-04-15'),
(1, 9, 'Storia', '2026-04-19'),
(2, 5, 'Matematica', '2026-03-10'),
(2, 3, 'Scienze', '2026-04-15'),
(2, 5, 'Storia', '2026-05-20'),
(3, 10, 'Scienze', '2026-06-10'),
(3, 2, 'Storia', '2026-07-15'),
(3, 9, 'Matematica', '2026-08-20');

