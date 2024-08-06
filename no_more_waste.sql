-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : mar. 06 août 2024 à 22:13
-- Version du serveur : 8.2.0
-- Version de PHP : 8.2.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `no_more_waste`
--

-- --------------------------------------------------------

--
-- Structure de la table `beneficiaries`
--

DROP TABLE IF EXISTS `beneficiaries`;
CREATE TABLE IF NOT EXISTS `beneficiaries` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `contact_person` varchar(100) NOT NULL,
  `contact_email` varchar(100) NOT NULL,
  `contact_phone` varchar(20) DEFAULT NULL,
  `address` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `registration_date` date NOT NULL,
  `service_type` enum('food','shelter','clothing','other') NOT NULL,
  `notes` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `beneficiaries`
--

INSERT INTO `beneficiaries` (`id`, `name`, `contact_person`, `contact_email`, `contact_phone`, `address`, `city`, `country`, `registration_date`, `service_type`, `notes`) VALUES
(1, 'Les Enfants du Soleil', 'Jean Dupont', 'jean.dupont@enfantsdusoleil.fr', '1234567890', '5 rue des Lilas', 'Paris', 'France', '2024-01-15', 'clothing', 'Association pour les enfants'),
(4, 'The University of Texas at Dallas', 'James', 'james@gmail.com', '0766589279', '6843 Main St, Frisco,', 'TEXAS', 'US', '2024-08-06', 'other', 'School charity');

-- --------------------------------------------------------

--
-- Structure de la table `collection_requests`
--

DROP TABLE IF EXISTS `collection_requests`;
CREATE TABLE IF NOT EXISTS `collection_requests` (
  `id` int NOT NULL AUTO_INCREMENT,
  `merchant_id` int DEFAULT NULL,
  `request_date` date NOT NULL,
  `collection_date` date NOT NULL,
  `collection_time` time NOT NULL,
  `status` enum('pending','assigned','completed','canceled') NOT NULL,
  `merchant_address` varchar(100) DEFAULT NULL,
  `storage_location_id` int DEFAULT NULL,
  `volunteer_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `merchant_id` (`merchant_id`),
  KEY `storage_location_id` (`storage_location_id`),
  KEY `volunteer_id` (`volunteer_id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `collection_requests`
--

INSERT INTO `collection_requests` (`id`, `merchant_id`, `request_date`, `collection_date`, `collection_time`, `status`, `merchant_address`, `storage_location_id`, `volunteer_id`) VALUES
(1, 5, '2024-01-10', '2024-01-20', '10:00:00', 'completed', '23 rue Paris', 2, 4),
(2, 5, '2024-02-01', '2024-08-06', '14:00:00', 'canceled', '3 boulevard Saint-Germain', 4, 4),
(3, 7, '0000-00-00', '2024-08-14', '00:00:00', 'assigned', ', , ', 2, 4),
(4, 7, '0000-00-00', '2024-08-08', '00:00:00', 'completed', ', , ', 1, 4),
(5, 7, '0000-00-00', '2024-08-07', '00:00:00', 'completed', ', , ', 1, 0),
(6, 7, '0000-00-00', '2024-08-07', '10:16:00', 'completed', '23 rue Paris 2024', 2, 10),
(7, 7, '0000-00-00', '2024-08-09', '23:25:00', 'completed', ', , ', 1, 4),
(8, 5, '0000-00-00', '2024-08-30', '21:30:00', 'pending', NULL, 1, 4);

-- --------------------------------------------------------

--
-- Structure de la table `contact_requests`
--

DROP TABLE IF EXISTS `contact_requests`;
CREATE TABLE IF NOT EXISTS `contact_requests` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `message` text NOT NULL,
  `status` enum('pending','processed') NOT NULL DEFAULT 'pending',
  `admin_id` int DEFAULT NULL,
  `response` text,
  `submitted_at` datetime NOT NULL,
  `processed_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `admin_id` (`admin_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `contact_requests`
--

INSERT INTO `contact_requests` (`id`, `name`, `email`, `phone`, `message`, `status`, `admin_id`, `response`, `submitted_at`, `processed_at`) VALUES
(1, 'John Doe', 'john.doe@example.com', '6789012345', 'Question sur la collecte.', 'pending', NULL, NULL, '2024-01-10 10:00:00', NULL),
(2, 'Jane Smith', 'jane.smith@example.com', '7890123456', 'Réclamation sur la livraison.', 'processed', 1, 'Votre réclamation a été reçue.', '2024-01-15 12:00:00', '2024-01-16 09:00:00');

-- --------------------------------------------------------

--
-- Structure de la table `deliveries`
--

DROP TABLE IF EXISTS `deliveries`;
CREATE TABLE IF NOT EXISTS `deliveries` (
  `id` int NOT NULL AUTO_INCREMENT,
  `collection_request_id` int DEFAULT NULL,
  `beneficiary_id` int DEFAULT NULL,
  `delivery_date` date NOT NULL,
  `volunteer_id` int DEFAULT NULL,
  `storage_id` int DEFAULT NULL,
  `status` enum('pending','in-progress','completed') NOT NULL,
  PRIMARY KEY (`id`),
  KEY `collection_request_id` (`collection_request_id`),
  KEY `beneficiary_id` (`beneficiary_id`),
  KEY `volunteer_id` (`volunteer_id`),
  KEY `storage_id` (`storage_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `deliveries`
--

INSERT INTO `deliveries` (`id`, `collection_request_id`, `beneficiary_id`, `delivery_date`, `volunteer_id`, `storage_id`, `status`) VALUES
(1, 1, 1, '2024-01-21', 4, 1, 'completed'),
(4, 1, 4, '2024-08-07', 10, 1, 'completed'),
(3, 1, 1, '2024-08-07', 4, 1, 'completed'),
(5, 4, 1, '2024-08-08', 4, 2, 'pending'),
(6, 5, 1, '2024-08-07', 10, NULL, 'pending');

-- --------------------------------------------------------

--
-- Structure de la table `menus`
--

DROP TABLE IF EXISTS `menus`;
CREATE TABLE IF NOT EXISTS `menus` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_id` int DEFAULT NULL,
  `menu_suggestion` text,
  `created_at` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `menus`
--

INSERT INTO `menus` (`id`, `product_id`, `menu_suggestion`, `created_at`) VALUES
(1, 1, 'Pâtes à la bolognaise', '2024-01-16'),
(2, 2, 'Riz au curry', '2024-02-01');

-- --------------------------------------------------------

--
-- Structure de la table `products`
--

DROP TABLE IF EXISTS `products`;
CREATE TABLE IF NOT EXISTS `products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `barcode` varchar(100) DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `quantity` int DEFAULT NULL,
  `collection_request_id` int DEFAULT NULL,
  `storage_date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `collection_request_id` (`collection_request_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `services`
--

DROP TABLE IF EXISTS `services`;
CREATE TABLE IF NOT EXISTS `services` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text,
  `available` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `services`
--

INSERT INTO `services` (`id`, `name`, `description`, `available`) VALUES
(1, 'Distribution alimentaire', 'Distribution de denrées alimentaires aux familles dans le besoin.', 1),
(2, 'Atelier de couture', 'Atelier pour apprendre la couture aux personnes défavorisées.', 1);

-- --------------------------------------------------------

--
-- Structure de la table `service_requests`
--

DROP TABLE IF EXISTS `service_requests`;
CREATE TABLE IF NOT EXISTS `service_requests` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `service_id` int DEFAULT NULL,
  `request_date` date NOT NULL,
  `status` enum('pending','in-progress','completed') NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `service_id` (`service_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `service_requests`
--

INSERT INTO `service_requests` (`id`, `user_id`, `service_id`, `request_date`, `status`) VALUES
(1, 1, 1, '2024-01-01', 'pending'),
(2, 2, 2, '2024-01-15', 'in-progress');

-- --------------------------------------------------------

--
-- Structure de la table `storage_locations`
--

DROP TABLE IF EXISTS `storage_locations`;
CREATE TABLE IF NOT EXISTS `storage_locations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `address` varchar(100) NOT NULL,
  `city` varchar(100) NOT NULL,
  `country` varchar(100) NOT NULL,
  `contact_phone` varchar(20) DEFAULT NULL,
  `contact_email` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `storage_locations`
--

INSERT INTO `storage_locations` (`id`, `name`, `address`, `city`, `country`, `contact_phone`, `contact_email`) VALUES
(1, 'Stockage Paris', '10 rue de la République', 'Paris', 'France', '0123456789', 'contact@stockageparis.fr'),
(2, 'Stockage Lyon', '20 avenue de la Liberté', 'Lyon', 'France', '0987654321', 'contact@stockagelyon.fr'),
(4, 'Stockage New York', '6843 Main St, Frisco,', 'TEXAS', 'Etat Uni', '0766589279', 's.rakulan04@gmail.com');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` enum('admin','volunteer','merchant') NOT NULL,
  `join_date` date DEFAULT NULL,
  `membership_exp` date DEFAULT NULL,
  `address` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `language` varchar(10) DEFAULT NULL,
  `status` enum('pending','approved','blocked') NOT NULL DEFAULT 'pending',
  `rejection_reason` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `phone`, `role`, `join_date`, `membership_exp`, `address`, `city`, `country`, `language`, `status`, `rejection_reason`) VALUES
(1, 'Alice Dupont', 'alice.dupont@example.com', '$2y$10$Th/lhvdPvcQ8r0xjTZdgBeeLrvE8toCnMBy65xhBS3kcyZbbf7lrW', '0102030405', 'admin', '2023-01-15', '2024-01-15', '123 Rue de Paris', 'Paris', 'France', 'FR', 'blocked', ''),
(7, 'Ragepan Sivathasan', 's.ragepan06@gmail.com', '$2y$10$nTgJxob7kFfy1fuu/CLP/e1Piny495jMhNgJHI0RryK6dtIH5c1Ji', '0766543212', 'merchant', '2024-08-02', NULL, NULL, NULL, NULL, NULL, 'approved', NULL),
(9, 'Sivathasan Rakulan', 's.rakulan04@gmail.com', '$2y$10$znyMulIVx1okVAZ.L6xNHuzeLy9anTlYM7DlYdjkWEPQYvsgoK3XS', '0766589279', 'admin', '2024-08-02', NULL, NULL, NULL, NULL, NULL, 'approved', ''),
(4, 'David Chen', 'david.chen@example.com', '$2y$10$Th/lhvdPvcQ8r0xjTZdgBeeLrvE8toCnMBy65xhBS3kcyZbbf7lrW', '0809091012', 'volunteer', '2023-04-05', '2024-04-05', '123 Rue des Champs', 'Nantes', 'France', 'FR', 'approved', ''),
(5, 'Eva Green', 'eva.green@example.com', '$2y$10$Th/lhvdPvcQ8r0xjTZdgBeeLrvE8toCnMBy65xhBS3kcyZbbf7lrW', '0901011121', 'merchant', '2023-05-10', '2024-05-10', '456 Route de la Gare', 'Dublin', 'Ireland', 'EN', 'blocked', 'test'),
(10, 'Frisco Chamber of Commerce', 's.rakulan0@gmail.com', '$2y$10$VgeAhIYNvGG0NmgNiB03IupDpyzxPvVNN9448m9z72sCdkN8NIVrq', '', 'volunteer', '2024-08-02', NULL, NULL, NULL, NULL, NULL, 'approved', '');

-- --------------------------------------------------------

--
-- Structure de la table `vehicles`
--

DROP TABLE IF EXISTS `vehicles`;
CREATE TABLE IF NOT EXISTS `vehicles` (
  `license_plate` varchar(20) NOT NULL,
  `model` varchar(100) NOT NULL,
  `brand` varchar(100) NOT NULL,
  `capacity_liters` int NOT NULL,
  `vehicle_type` enum('car','van','truck') NOT NULL,
  `purchase_date` date DEFAULT NULL,
  `last_maintenance` date DEFAULT NULL,
  `availability` tinyint(1) NOT NULL DEFAULT '1',
  `assigned_driver_id` int DEFAULT NULL,
  PRIMARY KEY (`license_plate`),
  KEY `assigned_driver_id` (`assigned_driver_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `vehicles`
--

INSERT INTO `vehicles` (`license_plate`, `model`, `brand`, `capacity_liters`, `vehicle_type`, `purchase_date`, `last_maintenance`, `availability`, `assigned_driver_id`) VALUES
('AB-123-CD', 'Transit', 'Ford', 3000, 'van', '2023-05-01', '2024-01-01', 1, 1),
('EF-456-GH', 'Sprinter', 'Mercedes', 5000, 'truck', '2022-08-01', '2024-01-01', 1, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `volunteers`
--

DROP TABLE IF EXISTS `volunteers`;
CREATE TABLE IF NOT EXISTS `volunteers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `skills` varchar(100) DEFAULT NULL,
  `availability` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `volunteers`
--

INSERT INTO `volunteers` (`id`, `user_id`, `skills`, `availability`) VALUES
(1, 2, 'Distribution alimentaire, communication', 'Lundi à Vendredi 9h-17h');

-- --------------------------------------------------------

--
-- Structure de la table `volunteer_availabilities`
--

DROP TABLE IF EXISTS `volunteer_availabilities`;
CREATE TABLE IF NOT EXISTS `volunteer_availabilities` (
  `id` int NOT NULL AUTO_INCREMENT,
  `volunteer_id` int DEFAULT NULL,
  `available_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  PRIMARY KEY (`id`),
  KEY `volunteer_id` (`volunteer_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `volunteer_availabilities`
--

INSERT INTO `volunteer_availabilities` (`id`, `volunteer_id`, `available_date`, `start_time`, `end_time`) VALUES
(1, 1, '2024-01-20', '09:00:00', '17:00:00'),
(2, 1, '2024-02-15', '09:00:00', '17:00:00');

-- --------------------------------------------------------

--
-- Structure de la table `volunteer_missions`
--

DROP TABLE IF EXISTS `volunteer_missions`;
CREATE TABLE IF NOT EXISTS `volunteer_missions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `volunteer_id` int DEFAULT NULL,
  `mission_date` date NOT NULL,
  `mission_time` time NOT NULL,
  `mission_type` enum('collection','delivery','service') NOT NULL,
  `status` enum('assigned','completed','canceled') NOT NULL,
  `details` text,
  PRIMARY KEY (`id`),
  KEY `volunteer_id` (`volunteer_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `volunteer_missions`
--

INSERT INTO `volunteer_missions` (`id`, `volunteer_id`, `mission_date`, `mission_time`, `mission_type`, `status`, `details`) VALUES
(1, 1, '2024-01-20', '10:00:00', 'collection', 'completed', 'Collecte de produits alimentaires pour les Enfants du Soleil'),
(2, 1, '2024-02-15', '14:00:00', 'delivery', 'assigned', 'Livraison de vêtements au Refuge des Chats');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
