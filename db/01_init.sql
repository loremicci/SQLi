USE sql_injection_db;

CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(50) NOT NULL
);

CREATE TABLE grades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT,
    grade INT,
    subject VARCHAR(50),
    FOREIGN KEY (student_id) REFERENCES students(id)
);

INSERT INTO students (username, password) VALUES
('admin', 'SuperSegreta99!'),
('andrea', 'password123'),
('ospite', 'ospite');

INSERT INTO grades (student_id, grade, subject) VALUES
(1, 7, 'Math'),
(1, 8, 'Science'),
(1, 9, 'History'),
(2, 5, 'Math'),
(2, 6, 'Science'),
(2, 7, 'History'),
(3, 10, 'Science'),
(3, 8, 'History'),
(3, 9, 'Math');

