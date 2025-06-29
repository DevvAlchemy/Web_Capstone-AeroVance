-- Update existing helicopter_marketplace database
-- This adds missing tables/columns without destroying existing data

USE `helicopter_marketplace`;

-- Add missing columns to helicopters table if they don't exist
ALTER TABLE `helicopters` 
ADD COLUMN IF NOT EXISTS `max_speed` int(11) DEFAULT NULL COMMENT 'in mph',
ADD COLUMN IF NOT EXISTS `range` int(11) DEFAULT NULL COMMENT 'in miles',
ADD COLUMN IF NOT EXISTS `passenger_capacity` int(11) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS `engine_type` varchar(100) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS `fuel_capacity` int(11) DEFAULT NULL COMMENT 'in gallons',
ADD COLUMN IF NOT EXISTS `specifications` json DEFAULT NULL,
ADD COLUMN IF NOT EXISTS `images` text DEFAULT NULL COMMENT 'comma-separated image URLs',
ADD COLUMN IF NOT EXISTS `stock_quantity` int(11) DEFAULT 1,
ADD COLUMN IF NOT EXISTS `seller_id` int(11) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS `featured` tinyint(1) DEFAULT 0,
ADD COLUMN IF NOT EXISTS `views` int(11) DEFAULT 0;

-- Create categories table if it doesn't exist
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL UNIQUE,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_slug` (`slug`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create orders table if it doesn't exist
CREATE TABLE IF NOT EXISTS `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `order_number` varchar(50) NOT NULL UNIQUE,
  `status` enum('pending','confirmed','processing','shipped','delivered','cancelled','refunded') DEFAULT 'pending',
  `payment_status` enum('pending','paid','failed','refunded','partial') DEFAULT 'pending',
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_transaction_id` varchar(255) DEFAULT NULL,
  `subtotal` decimal(12,2) NOT NULL,
  `tax_amount` decimal(10,2) DEFAULT 0.00,
  `shipping_amount` decimal(10,2) DEFAULT 0.00,
  `total_amount` decimal(12,2) NOT NULL,
  `currency` varchar(3) DEFAULT 'USD',
  `billing_address` json DEFAULT NULL,
  `shipping_address` json DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `shipped_at` timestamp NULL DEFAULT NULL,
  `delivered_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_order_number` (`order_number`),
  KEY `idx_status` (`status`),
  KEY `idx_payment_status` (`payment_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert categories if they don't exist
INSERT IGNORE INTO `categories` (`name`, `slug`, `description`, `sort_order`, `status`) VALUES
('Personal Use', 'personal', 'Helicopters for recreational and personal flying', 1, 'active'),
('Business', 'business', 'Commercial helicopters for business operations', 2, 'active'),
('Emergency Services', 'emergency', 'Helicopters for medical, rescue, and law enforcement', 3, 'active');

-- Add some sample helicopters if the table is empty
INSERT IGNORE INTO `helicopters` (`name`, `manufacturer`, `model`, `category`, `price`, `year`, `condition`, `max_speed`, `range`, `passenger_capacity`, `engine_type`, `fuel_capacity`, `description`, `featured`, `status`) VALUES
('Robinson R44 Raven II', 'Robinson', 'R44 Raven II', 'personal', 505000.00, 2023, 'new', 130, 348, 4, 'Lycoming IO-540-AE1A5', 78, 'The Robinson R44 Raven II is a four-seat light helicopter produced by Robinson Helicopter Company.', 1, 'available'),
('Bell 407GXi', 'Bell', '407GXi', 'business', 2900000.00, 2023, 'new', 140, 374, 7, 'Rolls-Royce M250-C47E', 113, 'The Bell 407GXi is a multi-purpose helicopter ideal for corporate transport.', 1, 'available'),
('Airbus H145', 'Airbus', 'H145', 'emergency', 9500000.00, 2023, 'new', 150, 431, 9, 'Twin Safran Arriel 2E', 183, 'The Airbus H145 is a twin-engine helicopter designed for emergency medical services.', 1, 'available');

SELECT 'Database updated successfully! 🚁' as message;