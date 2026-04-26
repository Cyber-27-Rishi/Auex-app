-- ============================================
-- Aurex - Premium Streetwear eCommerce
-- Database Setup
-- ============================================

CREATE DATABASE IF NOT EXISTS auex_store;
USE auex_store;

-- --------------------------------------------
-- Users Table
-- --------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE,
    phone VARCHAR(20) UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------
-- OTP Verification Table
-- --------------------------------------------
CREATE TABLE IF NOT EXISTS otp_verification (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(150),
    phone VARCHAR(20),
    otp_code VARCHAR(6) NOT NULL,
    purpose ENUM('register', 'login', 'reset') DEFAULT 'register',
    is_verified TINYINT(1) DEFAULT 0,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------
-- Products Table
-- --------------------------------------------
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    gender ENUM('Men', 'Women') NOT NULL DEFAULT 'Men',
    category ENUM('T-Shirt', 'Sweatshirt', 'Hoodie') NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    discount DECIMAL(5,2) DEFAULT 0,
    description TEXT,
    image VARCHAR(255),
    sizes_available VARCHAR(100) DEFAULT 'S,M,L,XL,XXL',
    stock INT DEFAULT 100,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------
-- Orders Table
-- FIX 1: Added 'order_confirm' and 'order_ship' to status ENUM
-- FIX 2: Added 'razorpay' already existed, kept all payment methods
-- --------------------------------------------
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    full_name VARCHAR(150) NOT NULL,
    email VARCHAR(150),
    phone VARCHAR(20) NOT NULL,
    address TEXT NOT NULL,
    city VARCHAR(100) NOT NULL,
    state VARCHAR(100),
    pincode VARCHAR(20),
    payment_method ENUM('cod', 'upi', 'card', 'razorpay') DEFAULT 'cod',
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled', 'order_confirm', 'order_ship') DEFAULT 'pending',
    razorpay_order_id VARCHAR(100),
    razorpay_payment_id VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------
-- Order Items Table
-- FIX 3: Removed hard foreign key on product_id
-- so deleted/missing products don't block order inserts
-- --------------------------------------------
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT,
    quantity INT NOT NULL DEFAULT 1,
    size VARCHAR(10) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    product_name VARCHAR(200),
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------
-- Admin Table
-- --------------------------------------------
CREATE TABLE IF NOT EXISTS admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------
-- Insert Default Admin (password: admin123)
-- --------------------------------------------
INSERT INTO admin (username, password) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- --------------------------------------------
-- Sample Products
-- --------------------------------------------
INSERT INTO products (name, gender, category, price, discount, description, image) VALUES
('Shadow Oversized Tee', 'Men', 'T-Shirt', 1299.00, 15.00, 'Premium 240 GSM cotton oversized tee with shadow graphic print. Dropped shoulders and ribbed collar for that premium streetwear fit.', 'uploads/shadow-tee.jpg'),
('Midnight Black Tee', 'Men', 'T-Shirt', 999.00, 0.00, 'Classic fit tee in deep midnight black. 200 GSM combed cotton with a soft-washed finish. Minimal Aurex logo on chest.', 'uploads/midnight-tee.jpg'),
('Phantom Graphic Tee', 'Men', 'T-Shirt', 1499.00, 10.00, 'Heavyweight graphic tee featuring original phantom artwork. Oversized cut with side-seam construction.', 'uploads/phantom-tee.jpg'),
('Obsidian Crewneck', 'Men', 'Sweatshirt', 2499.00, 20.00, 'Premium 380 GSM fleece-lined crewneck in obsidian black. Embroidered logo on chest and back. Double-stitched seams.', 'uploads/obsidian-crew.jpg'),
('Stealth Pullover', 'Men', 'Sweatshirt', 2199.00, 0.00, 'Minimalist fleece pullover with tonal branding. Brushed interior for maximum comfort. Relaxed fit.', 'uploads/stealth-pullover.jpg'),
('Void Embroidered Sweat', 'Men', 'Sweatshirt', 2799.00, 10.00, 'Luxury heavyweight sweatshirt with intricate void embroidery. 400 GSM French terry. Boxy fit with dropped shoulders.', 'uploads/void-sweat.jpg'),
('Darkside Zip Hoodie', 'Men', 'Hoodie', 3499.00, 15.00, 'Full-zip hoodie in washed black with premium YKK zipper. 450 GSM fleece with double-layered hood. Kangaroo pockets.', 'uploads/darkside-hoodie.jpg'),
('Eclipse Pullover Hoodie', 'Men', 'Hoodie', 2999.00, 0.00, 'Classic pullover hoodie in eclipse grey. 400 GSM French terry with embroidered back panel. Adjustable drawcord.', 'uploads/eclipse-hoodie.jpg'),
('Phantom Oversized Hoodie', 'Men', 'Hoodie', 3999.00, 10.00, 'Oversized heavyweight hoodie with phantom all-over print. 500 GSM premium fleece. Oversized fit with ribbed cuffs.', 'uploads/phantom-hoodie.jpg'),
('Aurora Crop Tee', 'Women', 'T-Shirt', 1199.00, 10.00, 'Soft crop tee in aurora white. 200 GSM combed cotton with a relaxed fit. Minimal Aurex logo on chest.', 'uploads/aurora-tee.jpg'),
('Nova Oversized Tee', 'Women', 'T-Shirt', 1299.00, 0.00, 'Premium oversized tee in nova grey. 240 GSM cotton with dropped shoulders.', 'uploads/nova-tee.jpg'),
('Celestial Crewneck', 'Women', 'Sweatshirt', 2299.00, 15.00, 'Cozy crewneck in celestial pink. 350 GSM fleece with embroidered logo.', 'uploads/celestial-crew.jpg'),
('Lunar Hoodie', 'Women', 'Hoodie', 2799.00, 10.00, 'Soft pullover hoodie in lunar lavender. 380 GSM French terry with adjustable drawcord.', 'uploads/lunar-hoodie.jpg');