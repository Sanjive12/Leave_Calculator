--Database creation
CREATE DATABASE leave_management;
--Use the database
USE leave_management;
--staff table
CREATE TABLE staff (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    leave_balance INT NOT NULL
);
--staff data
INSERT INTO staff (name, leave_balance) VALUES
    ('Staff A', 10),
    ('Staff B', 25),
    ('Staff C', 3);

--holidays table
CREATE TABLE holidays (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date DATE NOT NULL
);
--holiday data
INSERT INTO holidays (date) VALUES
    ('2020-01-15'),
    ('2020-02-07'),
    ('2020-05-01'),
    ('2020-08-15'),
    ('2020-10-02'),
    ('2020-12-25');
