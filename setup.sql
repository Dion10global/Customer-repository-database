-- ============================================================
--  setup.sql  –  Run in phpMyAdmin or MySQL CLI
--  Uses INSERT IGNORE so re-running never fails on duplicates
-- ============================================================

CREATE DATABASE IF NOT EXISTS test_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE test_db;

CREATE TABLE IF NOT EXISTS customers (
    id         INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    name       VARCHAR(120)    NOT NULL,
    email      VARCHAR(255)    NOT NULL UNIQUE,
    created_at DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- INSERT IGNORE skips rows that would violate the UNIQUE email constraint
INSERT IGNORE INTO customers (name, email, created_at) VALUES
    ('Alice Moyo',     'alice@example.com',   '2024-01-15 08:30:00'),
    ('Brian Ncube',    'brian@example.com',   '2024-02-20 10:00:00'),
    ('Chipo Dube',     'chipo@example.com',   '2024-03-05 14:15:00'),
    ('Daniel Sibanda', 'daniel@example.com',  '2024-04-10 09:45:00'),
    ('Evelyn Mutasa',  'evelyn@example.com',  '2024-05-22 11:20:00');

SELECT * FROM customers;