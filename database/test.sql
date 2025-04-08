CREATE DATABASE IF NOT EXISTS bags_ecommerce;
USE bags_ecommerce;

-- Create theme table
CREATE TABLE IF NOT EXISTS theme_settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    theme_name VARCHAR(50) NOT NULL,
    is_active BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert all available themes
INSERT INTO theme_settings (theme_name, is_active) VALUES 
    ('default', TRUE),
    ('pink', FALSE),
    ('dark', FALSE)
ON DUPLICATE KEY UPDATE theme_name = VALUES(theme_name);
