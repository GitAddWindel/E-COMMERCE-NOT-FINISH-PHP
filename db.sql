-- Create the database if it doesn't exist
CREATE DATABASE IF NOT EXISTS paste_code;

-- Use the database
USE paste_code;

-- Create the users table
CREATE TABLE IF NOT EXISTS users (
    user_id VARCHAR(255) PRIMARY KEY,
    fullname VARCHAR(255) NOT NULL,
    username VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    contact VARCHAR(15) NOT NULL,
    password VARCHAR(255) NOT NULL
);

-- Create the profiles table
CREATE TABLE profiles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id VARCHAR(255) NOT NULL,
    profile_picture VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_user FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- Create an index on the user_id column for faster lookups
CREATE INDEX idx_user_id ON profiles (user_id);


CREATE TABLE IF NOT EXISTS apply (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id VARCHAR(255),
    businessName VARCHAR(255),
    businessDescription TEXT,
    completeAddress VARCHAR(255),
    businessNumber VARCHAR(20),
    businessEmail VARCHAR(255),
    businessCategory VARCHAR(50),
    validId VARCHAR(255),
    is_approved BOOLEAN DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- Table structure for 'products'
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id VARCHAR(255) NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    product_description TEXT,
    product_category VARCHAR(255),
    product_quantity INT,
    product_price DECIMAL(10, 2),
    product_img VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

CREATE TABLE IF NOT EXISTS ratings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id VARCHAR(255) NOT NULL, -- Assuming this is the user being reviewed
    reviewer_id VARCHAR(255) NOT NULL, -- Assuming this is the user who submitted the review
    rating INT NOT NULL,
    review_comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (reviewer_id) REFERENCES users(user_id)
);

