USE no_more_waste;

-- Insertion des utilisateurs (admins, bénévoles, commerçants)
INSERT INTO users (name, email, password, phone, role, join_date, membership_exp, address, city, country, language) VALUES
('Alice Dupont', 'alice.dupont@example.com', 'password123', '0102030405', 'admin', '2023-01-15', '2024-01-15', '123 Rue de Paris', 'Paris', 'France', 'FR'),
('Bob Martin', 'bob.martin@example.com', 'password123', '0607080910', 'volunteer', '2023-02-20', '2024-02-20', '456 Avenue du Général', 'Lyon', 'France', 'FR'),
('Carla Lopez', 'carla.lopez@example.com', 'password123', '0708091011', 'merchant', '2023-03-12', '2024-03-12', '789 Boulevard de la Liberté', 'Marseille', 'France', 'FR'),
('David Chen', 'david.chen@example.com', 'password123', '0809091012', 'volunteer', '2023-04-05', '2024-04-05', '123 Rue des Champs', 'Nantes', 'France', 'FR'),
('Eva Green', 'eva.green@example.com', 'password123', '0901011121', 'merchant', '2023-05-10', '2024-05-10', '456 Route de la Gare', 'Dublin', 'Ireland', 'EN');

-- Insertion des produits
INSERT INTO products (name, barcode, expiry_date, quantity, collection_id, storage_date) VALUES
('Apples', '1234567890123', '2024-08-15', 50, 1, '2024-07-30'),
('Bread', '1234567890124', '2024-07-31', 30, 1, '2024-07-30'),
('Milk', '1234567890125', '2024-08-10', 20, 2, '2024-07-30'),
('Canned Beans', '1234567890126', '2025-01-20', 100, 2, '2024-07-30'),
('Rice', '1234567890127', '2025-05-10', 75, 3, '2024-07-30');

-- Insertion des collectes
INSERT INTO collections (merchant_id, collection_date, status) VALUES
(3, '2024-07-31', 'scheduled'),
(5, '2024-08-02', 'pending'),
(3, '2024-08-05', 'pending'),
(1, '2024-08-10', 'scheduled'),
(2, '2024-08-12', 'pending');

-- Insertion des lieux de stockage
INSERT INTO storage_locations (name, address, city, country, contact_phone, contact_email) VALUES
('Storage A', '789 Rue des Archives', 'Paris', 'France', '0102030406', 'storageA@example.com'),
('Storage B', '456 Route des Lilas', 'Lyon', 'France', '0607080911', 'storageB@example.com'),
('Storage C', '123 Avenue de la République', 'Marseille', 'France', '0708091013', 'storageC@example.com'),
('Storage D', '987 Boulevard Saint-Germain', 'Nantes', 'France', '0809091014', 'storageD@example.com'),
('Storage E', '654 Route de Green', 'Dublin', 'Ireland', '0901011122', 'storageE@example.com');

-- Insertion des demandes de collecte
INSERT INTO collection_requests (merchant_id, request_date, collection_date, collection_time, status, merchant_address, storage_location_id) VALUES
(3, '2024-07-25', '2024-07-31', '10:00:00', 'assigned', '789 Boulevard de la Liberté', 1),
(5, '2024-07-26', '2024-08-02', '09:00:00', 'pending', '456 Route de la Gare', 2),
(3, '2024-07-27', '2024-08-05', '11:00:00', 'pending', '789 Boulevard de la Liberté', 3),
(1, '2024-07-28', '2024-08-10', '12:00:00', 'assigned', '123 Rue des Champs', 4),
(2, '2024-07-29', '2024-08-12', '08:00:00', 'pending', '456 Avenue du Général', 5);

-- Insertion des livraisons
INSERT INTO deliveries (collection_request_id, beneficiary_id, delivery_date, volunteer_id, storage_id, status) VALUES
(1, 1, '2024-07-31', 2, 1, 'completed'),
(2, 2, '2024-08-02', 4, 2, 'in-progress'),
(3, 3, '2024-08-05', 2, 3, 'pending'),
(4, 4, '2024-08-10', 4, 4, 'completed'),
(5, 5, '2024-08-12', 2, 5, 'in-progress');

-- Insertion des services
INSERT INTO services (name, description, available) VALUES
('Cooking Classes', 'Learn to cook with our expert chefs.', TRUE),
('Gardening Services', 'Professional gardening services for your home.', TRUE),
('Plumbing', 'Certified plumbing services.', TRUE),
('Electrician', 'Electrical services for home and office.', TRUE),
('Carpentry', 'Custom carpentry services.', TRUE);

-- Insertion des bénévoles
INSERT INTO volunteers (user_id, skills, availability) VALUES
(2, 'Driving, Cooking', 'Weekends'),
(4, 'Plumbing, Carpentry', 'Weekdays'),
(2, 'Electrician, Gardening', 'Flexible'),
(4, 'Driving, Plumbing', 'Weekdays and Weekends'),
(2, 'Cooking, Gardening', 'Evenings');

-- Insertion des disponibilités des bénévoles
INSERT INTO volunteer_availabilities (volunteer_id, available_date, start_time, end_time) VALUES
(2, '2024-07-31', '08:00:00', '16:00:00'),
(4, '2024-08-01', '09:00:00', '17:00:00'),
(2, '2024-08-02', '10:00:00', '18:00:00'),
(4, '2024-08-03', '08:00:00', '16:00:00'),
(2, '2024-08-04', '09:00:00', '17:00:00');

-- Insertion des missions des bénévoles
INSERT INTO volunteer_missions (volunteer_id, mission_date, mission_time, mission_type, status, details) VALUES
(2, '2024-07-31', '10:00:00', 'collection', 'assigned', 'Collecting products from merchant.'),
(4, '2024-08-02', '11:00:00', 'delivery', 'completed', 'Delivering products to beneficiary.'),
(2, '2024-08-05', '12:00:00', 'collection', 'assigned', 'Collecting products from merchant.'),
(4, '2024-08-10', '13:00:00', 'delivery', 'completed', 'Delivering products to beneficiary.'),
(2, '2024-08-12', '14:00:00', 'collection', 'assigned', 'Collecting products from merchant.');

-- Insertion des demandes de service
INSERT INTO service_requests (user_id, service_id, request_date, status) VALUES
(1, 1, '2024-07-25', 'pending'),
(2, 2, '2024-07-26', 'in-progress'),
(3, 3, '2024-07-27', 'completed'),
(4, 4, '2024-07-28', 'pending'),
(5, 5, '2024-07-29', 'in-progress');

-- Insertion des suggestions de menus
INSERT INTO menus (product_id, menu_suggestion, created_at) VALUES
(1, 'Apple Pie', '2024-07-30'),
(2, 'Bread Pudding', '2024-07-30'),
(3, 'Milkshake', '2024-07-30'),
(4, 'Bean Salad', '2024-07-30'),
(5, 'Rice Pilaf', '2024-07-30');

-- Insertion des bénéficiaires
INSERT INTO beneficiaries (name, contact_person, contact_email, contact_phone, address, city, country, registration_date, service_type, notes) VALUES
('Charity A', 'John Doe', 'johndoe@charitya.org', '0102030406', '123 Rue de l\'Eglise', 'Paris', 'France', '2023-01-01', 'food', 'Food distribution center.'),
('Charity B', 'Jane Smith', 'janesmith@charityb.org', '0203040507', '456 Avenue des Fleurs', 'Lyon', 'France', '2023-02-01', 'shelter', 'Shelter for homeless.'),
('Charity C', 'Alice Brown', 'alicebrown@charityc.org', '0304050608', '789 Boulevard de la Mer', 'Marseille', 'France', '2023-03-01', 'clothing', 'Clothing donation center.'),
('Charity D', 'Bob White', 'bobwhite@charityd.org', '0405060708', '123 Route des Champs', 'Nantes', 'France', '2023-04-01', 'food', 'Food bank.'),
('Charity E', 'Charlie Black', 'charlieblack@charitye.org', '0506070809', '456 Rue de la Liberté', 'Dublin', 'Ireland', '2023-05-01', 'shelter', 'Shelter for women and children.');

-- Insertion des véhicules
INSERT INTO vehicles (license_plate, model, brand, capacity_liters, vehicle_type, purchase_date, last_maintenance, availability, assigned_driver_id) VALUES
('AB-123-CD', 'Kangoo', 'Renault', 1000, 'van', '2020-01-01', '2023-07-01', TRUE, 2),
('EF-456-GH', 'Transit', 'Ford', 1500, 'truck', '2019-02-01', '2023-06-01', TRUE, 4),
('IJ-789-KL', 'Sprinter', 'Mercedes', 2000, 'van', '2021-03-01', '2023-05-01', TRUE, 2),
('MN-012-OP', 'Caddy', 'Volkswagen', 1200, 'car', '2018-04-01', '2023-04-01', TRUE, 4),
('QR-345-ST', 'Partner', 'Peugeot', 1100, 'van', '2022-05-01', '2023-03-01', TRUE, 2);
