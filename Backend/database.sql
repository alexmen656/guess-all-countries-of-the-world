CREATE DATABASE name_the_countries;

USE name_the_countries;

CREATE TABLE IF NOT EXISTS leaderboard (
    id INT AUTO_INCREMENT PRIMARY KEY,
    uuid VARCHAR(255) NOT NULL,
    name VARCHAR(255) NOT NULL,
    count INT NOT NULL
);