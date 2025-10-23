-- Create the database
CREATE DATABASE IF NOT EXISTS bookingappointment;
USE bookingappointment;

-- Create clients table
CREATE TABLE IF NOT EXISTS clients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create customer table for appointments
CREATE TABLE IF NOT EXISTS customer (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    address TEXT NOT NULL,
    phone_number VARCHAR(20) NOT NULL,
    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,
    service_type VARCHAR(100) NOT NULL,
    appointment_type VARCHAR(100) NOT NULL,
    status ENUM('pending', 'approved', 'cancelled', 'completed') DEFAULT 'pending',
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES clients(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create admin table
CREATE TABLE IF NOT EXISTS admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default admin account (password: admin123)
INSERT INTO admin (username, password) VALUES 
('admin', '$2y$10$8K1p/a0dL1LXMIhuF7qvpeLZg5kKxQgc5EMeESuXgMLwrCp.X6wvK');

-- Add indexes for better performance
CREATE INDEX idx_user_id ON customer(user_id);
CREATE INDEX idx_appointment_date ON customer(appointment_date);
CREATE INDEX idx_status ON customer(status);

-- Add some sample service types (optional)
CREATE TABLE IF NOT EXISTS service_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert some sample service types
INSERT INTO service_types (name, description) VALUES
('General Consultation', 'Initial consultation for general health concerns'),
('Follow-up Visit', 'Follow-up appointment for existing conditions'),
('Specialist Consultation', 'Consultation with a specialist doctor'),
('Medical Certificate', 'Issuance of medical certificates'),
('Laboratory Tests', 'Various laboratory tests and diagnostics');