-- FOR SEAN TO PUT IN XAMPP
CREATE TABLE owners (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create the listings table
CREATE TABLE IF NOT EXISTS listings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    listing_title VARCHAR(255) NOT NULL,
    total_floor_area DECIMAL(10, 2) NOT NULL COMMENT 'Area in square meters',
    property_type ENUM('Office', 'Co-working space', 'Shared office', 'Commercial building') NOT NULL,
    address TEXT NOT NULL,
    monthly_rent DECIMAL(12, 2) NOT NULL COMMENT 'Monthly rent in PHP',
    status ENUM('available', 'rented', 'pending') DEFAULT 'available',
    owner_id INT DEFAULT NULL COMMENT 'References the owner who listed this property',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Add indexes for better query performance
CREATE INDEX idx_property_type ON listings(property_type);
CREATE INDEX idx_status ON listings(status);
CREATE INDEX idx_monthly_rent ON listings(monthly_rent);

-- Optional: Add foreign key if you have an owners table
ALTER TABLE listings ADD CONSTRAINT fk_listings_owner FOREIGN KEY (owner_id) REFERENCES owners(id) ON DELETE SET NULL;