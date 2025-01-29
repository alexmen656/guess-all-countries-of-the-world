CREATE DATABASE guess_the_countries;

USE guess_the_countries;

CREATE TABLE IF NOT EXISTS leaderboard (
    id INT AUTO_INCREMENT PRIMARY KEY,
    uuid VARCHAR(255) NOT NULL,
    count VARCHAR(255) NOT NULL,
    score INT NOT NULL
);