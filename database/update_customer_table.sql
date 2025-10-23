-- Add cancelled_by column to customer table
ALTER TABLE customer ADD COLUMN cancelled_by ENUM('user', 'admin') DEFAULT NULL;