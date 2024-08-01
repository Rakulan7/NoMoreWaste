USE no_more_waste;

-- Insertion des utilisateurs (admins, bénévoles, commerçants)
INSERT INTO users (name, email, password, phone, role, join_date, membership_exp, address, city, country, language) VALUES
('Rakulan Sivathasan', 's.rakulan04@gmail.com', '$2y$10$Th/lhvdPvcQ8r0xjTZdgBeeLrvE8toCnMBy65xhBS3kcyZbbf7lrW', '0102030405', 'admin', '2023-01-15', '2024-01-15', '123 Rue de Paris', 'Paris', 'France', 'FR'),
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
(2, 2, '2024-08-02', 4, 2, 'pending'),
(3, 3, '2024-08-05', 5, 3, 'pending'),
(4, 4, '2024-08-10', 2, 4, 'pending'),
(5, 5, '2024-08-12', 4, 5, 'pending');

-- Insertion des services
INSERT INTO services (name, description, available) VALUES
('Cooking Classes', 'Learn how to cook with rescued ingredients.', TRUE),
('Vehicle Sharing', 'Share vehicles for pickups and deliveries.', TRUE),
('Repair Services', 'Get help with minor repairs at home.', TRUE),
('Gardening Advice', 'Tips and help with home gardening.', TRUE),
('Language Classes', 'Improve your language skills with our classes.', TRUE);

-- Insertion des bénévoles
INSERT INTO volunteers (user_id, skills, availability) VALUES
(2, 'Driving, Cooking', 'Weekdays 09:00-17:00'),
(4, 'Driving, Repairs', 'Weekends 10:00-16:00'),
(5, 'Cooking, Tutoring', 'Weekdays 08:00-14:00'),
(1, 'Driving, Organizing', 'Weekdays 09:00-18:00'),
(3, 'Cooking, Teaching', 'Weekends 09:00-15:00');

-- Insertion des disponibilités des bénévoles
INSERT INTO volunteer_availabilities (volunteer_id, available_date, start_time, end_time) VALUES
(2, '2024-07-31', '09:00:00', '17:00:00'),
(4, '2024-08-02', '10:00:00', '16:00:00'),
(5, '2024-08-05', '08:00:00', '14:00:00'),
(1, '2024-08-10', '09:00:00', '18:00:00'),
(3, '2024-08-12', '09:00:00', '15:00:00');

-- Insertion des missions des bénévoles
INSERT INTO volunteer_missions (volunteer_id, mission_date, mission_time, mission_type, status, details) VALUES
(2, '2024-07-31', '10:00:00', 'collection', 'completed', 'Collection from merchant at 789 Boulevard de la Liberté'),
(4, '2024-08-02', '09:00:00', 'collection', 'pending', 'Collection from merchant at 456 Route de la Gare'),
(5, '2024-08-05', '11:00:00', 'collection', 'pending', 'Collection from merchant at 789 Boulevard de la Liberté'),
(1, '2024-08-10', '12:00:00', 'delivery', 'pending', 'Delivery to beneficiary at 123 Rue des Champs'),
(3, '2024-08-12', '08:00:00', 'delivery', 'pending', 'Delivery to beneficiary at 456 Avenue du Général');

-- Insertion des bénéficiaires
INSERT INTO beneficiaries (name, contact_person, contact_email, contact_phone, address, city, country, registration_date, service_type, notes) VALUES
('Beneficiary A', 'John Doe', 'john.doe@example.com', '0203040506', '123 Boulevard Victor Hugo', 'Paris', 'France', '2024-07-15', 'food', 'Needs weekly food support.'),
('Beneficiary B', 'Jane Smith', 'jane.smith@example.com', '0708091011', '456 Rue des Lilas', 'Lyon', 'France', '2024-07-20', 'shelter', 'Single parent with two children.'),
('Beneficiary C', 'Paul Johnson', 'paul.johnson@example.com', '0901011121', '789 Route des Vignes', 'Marseille', 'France', '2024-07-25', 'clothing', 'Recently unemployed.'),
('Beneficiary D', 'Emily Davis', 'emily.davis@example.com', '0607080910', '321 Avenue des Champs', 'Nantes', 'France', '2024-07-30', 'food', 'Senior citizen, needs assistance.'),
('Beneficiary E', 'Michael Brown', 'michael.brown@example.com', '0203040505', '654 Rue du Bonheur', 'Dublin', 'Ireland', '2024-08-01', 'other', 'Disabled veteran in need of various support.');

-- Insertion des véhicules
INSERT INTO vehicles (license_plate, model, brand, capacity_liters, vehicle_type, purchase_date, last_maintenance, availability, assigned_driver_id) VALUES
('AB123CD', 'Transit Connect', 'Ford', 2500, 'van', '2023-06-01', '2024-01-15', TRUE, 2),
('EF456GH', 'Sprinter 316', 'Mercedes', 3500, 'van', '2022-11-20', '2023-12-01', TRUE, NULL),
('IJ789KL', 'Master L2H2', 'Renault', 3000, 'van', '2023-05-15', '2024-02-10', TRUE, NULL),
('MN012OP', 'Movano LWB', 'Opel', 3300, 'van', '2022-09-30', '2023-11-25', FALSE, 4),
('QR345ST', 'Boxer L3H2', 'Peugeot', 3200, 'van', '2023-07-10', '2024-03-01', TRUE, 5);
