CREATE DATABASE IF NOT EXISTS vet_clinic;
USE vet_clinic;

CREATE TABLE Owners (
    owner_id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    middle_name VARCHAR(50),
    last_name VARCHAR(50) NOT NULL,
    locality VARCHAR(100) NOT NULL
);

CREATE TABLE Mobile_Numbers (
    mobile_id INT AUTO_INCREMENT PRIMARY KEY,
    owner_id INT,
    mobile_number VARCHAR(15) NOT NULL,
    is_verified BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (owner_id) REFERENCES Owners(owner_id)
);

CREATE TABLE Pets (
    pet_id INT AUTO_INCREMENT PRIMARY KEY,
    owner_id INT,
    unique_id VARCHAR(6) UNIQUE NOT NULL,
    pet_name VARCHAR(50) NOT NULL,
    species ENUM('Canine', 'Feline', 'Avian', 'Tortoise', 'Exotic') NOT NULL,
    breed VARCHAR(50) NOT NULL,
    gender ENUM('Male', 'Female') NOT NULL,
    dob DATE NOT NULL,
    FOREIGN KEY (owner_id) REFERENCES Owners(owner_id)
);

CREATE TABLE Visits (
    visit_id INT AUTO_INCREMENT PRIMARY KEY,
    pet_id INT,
    visit_date DATE NOT NULL,
    next_visit_date DATE,
    FOREIGN KEY (pet_id) REFERENCES Pets(pet_id)
);

CREATE TABLE Pet_Images (
    image_id INT AUTO_INCREMENT PRIMARY KEY,
    pet_id INT,
    image_path VARCHAR(255) NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (pet_id) REFERENCES Pets(pet_id)
);

CREATE TABLE Settings (
    setting_id INT AUTO_INCREMENT PRIMARY KEY,
    table_name VARCHAR(50) NOT NULL,
    field_name VARCHAR(50) NOT NULL,
    field_type VARCHAR(20) NOT NULL,
    field_options TEXT,
    is_required BOOLEAN DEFAULT TRUE
);

-- Initial configurations
INSERT INTO Settings (table_name, field_name, field_type, field_options, is_required) VALUES
('Pets', 'species', 'ENUM', 'Canine,Feline,Avian,Tortoise,Exotic', TRUE),
('Pets', 'gender', 'ENUM', 'Male,Female', TRUE),
('Pets', 'breed', 'VARCHAR', NULL, TRUE),
('Owners', 'first_name', 'VARCHAR', NULL, TRUE),
('Owners', 'middle_name', 'VARCHAR', NULL, FALSE);