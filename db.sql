-- Create the database if it doesn't exist
CREATE DATABASE IF NOT EXISTS inventory_db;

-- Select the database
USE inventory_db;

-- Create the diecast_products table
CREATE TABLE IF NOT EXISTS diecast_products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    brand VARCHAR(100) NOT NULL,
    model VARCHAR(100) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    status ENUM('Available', 'Sold', 'Reserved') NOT NULL DEFAULT 'Available'
);
