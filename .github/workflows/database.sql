-- Database schema for Pet Care Store
-- Run this script using MySQL client or phpMyAdmin to create the database and tables.

CREATE DATABASE IF NOT EXISTS petcare_store;
USE petcare_store;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(100),
    phone VARCHAR(20),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Products table
CREATE TABLE IF NOT EXISTS products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    image_url VARCHAR(255),
    stock INT DEFAULT 100,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Cart table
CREATE TABLE IF NOT EXISTS cart (
    cart_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_product (user_id, product_id)
) ENGINE=InnoDB;

-- Orders table
CREATE TABLE IF NOT EXISTS orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    payment_method VARCHAR(50),
    order_status VARCHAR(50) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Order items table
CREATE TABLE IF NOT EXISTS order_items (
    order_item_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Contacts table
CREATE TABLE IF NOT EXISTS contacts (
    contact_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(50) DEFAULT 'new'
) ENGINE=InnoDB;

-- Insert sample products (optional)
INSERT INTO products (name, description, price, image_url) VALUES
    ('Cute Bows', 'Stylish and cute bows for your pets', 30, 'images/bow.jpeg'),
    ('Cat Bowl', 'Durable stainless steel cat bowl', 10, 'images/bowl.jpeg'),
    ('Pet Brush', 'Gentle brush for pet grooming', 15, 'images/brush.jpeg'),
    ('Fashionable Pet Cap', 'Trendy cap for your furry friend', 25, 'images/cap.jpeg'),
    ('Pet Carrier', 'Portable carrier for safe pet transport', 30, 'images/carrier.jpeg'),
    ('Pet Comb', 'Fine-toothed comb for pet care', 8, 'images/comb.jpeg'),
    ('Pet Ear Cleaner', 'Safe ear cleaning solution for pets', 10, 'images/ear cleaner.jpeg'),
    ('Fashionable Hoodies', 'Warm and cozy hoodies for pets', 20, 'images/hoodies.jpeg'),
    ('Pet Shampoo', 'Gentle pet shampoo for all fur types', 12, 'images/shampoo.jpeg'),
    ('Pet Soap', 'Natural pet soap bar', 10, 'images/soap.jpeg'),
    ('Pet T-Shirt', 'Comfortable t-shirt for pets', 15, 'images/tshirt.jpeg');

-- End of schema
