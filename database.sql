CREATE DATABASE no_more_waste;
USE no_more_waste;

-- Table des utilisateurs (incluant les admins)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    role ENUM('admin', 'volunteer', 'merchant') NOT NULL,
    join_date DATE,
    membership_exp DATE,
    address VARCHAR(100),
    city VARCHAR(100),
    country VARCHAR(100),
    language VARCHAR(10)
);

-- Table des lieux de stockage
CREATE TABLE storage_locations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    address VARCHAR(100) NOT NULL,
    city VARCHAR(100) NOT NULL,
    country VARCHAR(100) NOT NULL,
    contact_phone VARCHAR(20),
    contact_email VARCHAR(100)
);

-- Table des bénéficiaires
CREATE TABLE beneficiaries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    contact_person VARCHAR(100) NOT NULL,
    contact_email VARCHAR(100) NOT NULL,
    contact_phone VARCHAR(20),
    address VARCHAR(100),
    city VARCHAR(100),
    country VARCHAR(100),
    registration_date DATE NOT NULL,
    service_type ENUM('food', 'shelter', 'clothing', 'other') NOT NULL,
    notes TEXT
);

-- Table des services
CREATE TABLE services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    available BOOLEAN NOT NULL DEFAULT TRUE
);

-- Table des collectes
CREATE TABLE collections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    merchant_id INT,
    collection_date DATE NOT NULL,
    status ENUM('pending', 'scheduled', 'completed', 'canceled') NOT NULL,
    FOREIGN KEY (merchant_id) REFERENCES users(id)
);

-- Table des produits
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    barcode VARCHAR(100) UNIQUE,
    expiry_date DATE,
    quantity INT,
    collection_id INT,
    storage_date DATE,
    FOREIGN KEY (collection_id) REFERENCES collections(id)
);

-- Table des demandes de collecte
CREATE TABLE collection_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    merchant_id INT,
    request_date DATE NOT NULL,
    collection_date DATE NOT NULL,
    collection_time TIME NOT NULL,
    status ENUM('pending', 'assigned', 'completed', 'canceled') NOT NULL,
    merchant_address VARCHAR(100),
    storage_location_id INT,
    FOREIGN KEY (merchant_id) REFERENCES users(id),
    FOREIGN KEY (storage_location_id) REFERENCES storage_locations(id)
);

-- Table des livraisons
CREATE TABLE deliveries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    collection_request_id INT,
    beneficiary_id INT,
    delivery_date DATE NOT NULL,
    volunteer_id INT,
    storage_id INT,
    status ENUM('pending', 'in-progress', 'completed') NOT NULL,
    FOREIGN KEY (collection_request_id) REFERENCES collection_requests(id),
    FOREIGN KEY (beneficiary_id) REFERENCES beneficiaries(id),
    FOREIGN KEY (volunteer_id) REFERENCES users(id),
    FOREIGN KEY (storage_id) REFERENCES storage_locations(id)
);

-- Table des bénévoles
CREATE TABLE volunteers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    skills VARCHAR(100),
    availability VARCHAR(100),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Table des disponibilités des bénévoles
CREATE TABLE volunteer_availabilities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    volunteer_id INT,
    available_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    FOREIGN KEY (volunteer_id) REFERENCES volunteers(id)
);

-- Table des missions des bénévoles
CREATE TABLE volunteer_missions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    volunteer_id INT,
    mission_date DATE NOT NULL,
    mission_time TIME NOT NULL,
    mission_type ENUM('collection', 'delivery', 'service') NOT NULL,
    status ENUM('assigned', 'completed', 'canceled') NOT NULL,
    details TEXT,
    FOREIGN KEY (volunteer_id) REFERENCES volunteers(id)
);

-- Table des demandes de service
CREATE TABLE service_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    service_id INT,
    request_date DATE NOT NULL,
    status ENUM('pending', 'in-progress', 'completed') NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (service_id) REFERENCES services(id)
);

-- Table des suggestions de menus
CREATE TABLE menus (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    menu_suggestion TEXT,
    created_at DATE NOT NULL,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Table des véhicules
CREATE TABLE vehicles (
    license_plate VARCHAR(20) PRIMARY KEY,
    model VARCHAR(100) NOT NULL,
    brand VARCHAR(100) NOT NULL,
    capacity_liters INT NOT NULL,
    vehicle_type ENUM('car', 'van', 'truck') NOT NULL,
    purchase_date DATE,
    last_maintenance DATE,
    availability BOOLEAN NOT NULL DEFAULT TRUE,
    assigned_driver_id INT,
    FOREIGN KEY (assigned_driver_id) REFERENCES users(id)
);
