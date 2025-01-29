CREATE DATABASE name_the_countries;

USE name_the_countries;

CREATE TABLE IF NOT EXISTS leaderboard (
    id INT AUTO_INCREMENT PRIMARY KEY,
    uuid VARCHAR(255) NOT NULL,
    count VARCHAR(255) NOT NULL,
    score INT NOT NULL
);