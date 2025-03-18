CREATE DATABASE IF NOT EXISTS review_db;
USE review_db;

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2),
    image_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    rating INT NOT NULL,
    review_text TEXT,
    reviewer_name VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Insert sample products
INSERT INTO products (name, description, price, image_url) VALUES
('Smartphone X', 'Latest smartphone with amazing features', 999.99, 'images/phone.jpg'),
('Laptop Pro', 'Professional laptop for all your needs', 1499.99, 'images/laptop.jpg'),
('Wireless Headphones', 'High-quality wireless headphones', 199.99, 'images/headphones.jpg'); 