-- Create database
CREATE DATABASE IF NOT EXISTS bags_ecommerce;
USE bags_ecommerce;

-- Users table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    status ENUM('active', 'disabled') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products table
CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    category VARCHAR(50),
    image_url VARCHAR(255),
    stock INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Product options table
CREATE TABLE product_options (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT,
    option_name VARCHAR(50) NOT NULL,
    option_value VARCHAR(50) NOT NULL,
    price_adjustment DECIMAL(10,2) DEFAULT 0,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Orders table
CREATE TABLE orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'processing', 'shipped') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Order items table
CREATE TABLE order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT,
    product_id INT,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Insert sample products
INSERT INTO products (name, price, category, image_url, stock) VALUES
('Grey Leather Crossbody', 49.99, 'Crossbody', 'assets/images/crossbody1.jpg', 10),
('Brown Louis Vutton Crossbody', 123.79, 'Crossbody', 'assets/images/crossbody2.jpg', 15),
('Brown Prada Crossbody', 54.99, 'Crossbody', 'assets/images/crossbody3.jpg', 10),
('Red Leather Handbag', 79.99, 'Handbag', 'assets/images/handbag1.jpg', 15),
('Floral Handbag', 69.99, 'Handbag', 'assets/images/handbag2.jpg', 8),
('Pink Handbag', 39.99, 'Handbag', 'assets/images/handbag3.jpg', 12),
('Chestnut Handbag', 29.99, 'Handbag', 'assets/images/handbag4.jpg', 20),
('Belted Colorful Handbag', 100.00, 'Handbag', 'assets/images/handbag5.jpg', 5),
('Red Leather Backpack', 185.00, 'Backpack', 'assets/images/backpack1.jpg', 15),
('Blue Backpack', 129.99, 'Backpack', 'assets/images/backpack2.jpg', 50),
('Green Hiking Backpack', 320.50, 'Backpack', 'assets/images/backpack3.jpg', 8),
('Black Backpack', 150.00, 'Backpack', 'assets/images/backpack4.jpg', 9),
('Brown and Grey Messenger Bag', 89.99, 'Messenger', 'assets/images/messenger1.jpg', 10),
('Dark Grey Messenger Bag', 109.99, 'Messenger', 'assets/images/messenger2.jpg', 13),
('Orange Suitcase', 187.00, 'Suitcase', 'assets/images/suitcase1.jpg', 55),
('4 Pink Suitcase Set', 725.00, 'Suitcase', 'assets/images/suitcase2.jpg', 2),
('3 Blue Suitcase Set', 555.55, 'Suitcase', 'assets/images/suitcase3.jpg', 5),
('Brown Tote Bag', 29.99, 'Tote', 'assets/images/tote1.jpg', 28),
('Black Tote Bag Rabbit design', 39.99, 'Tote', 'assets/images/tote2.jpg', 15),
('Plain Tote Bag', 29.99, 'Tote', 'assets/images/tote3.jpg', 21);


-- Insert sample product options
INSERT INTO product_options (product_id, option_name, option_value, price_adjustment) VALUES
(1, 'Color', 'Black', 0),
(1, 'Color', 'Navy', 0),
(1, 'Size', 'Small', -10),
(1, 'Size', 'Large', 10),
(2, 'Material', 'Genuine Leather', 0),
(2, 'Material', 'Faux Leather', -20),
(2, 'Color', 'Brown', 0),
(2, 'Color', 'Black', 0),
(3, 'Size', 'Medium', 0),
(3, 'Size', 'Large', 20),
(3, 'Color', 'Gray', 0),
(3, 'Color', 'Blue', 0);

-- Insert admin user (password: admin123)
INSERT INTO users (username, password, email, role) VALUES
('admin', 'admin123', 'admin@example.com', 'admin'); 