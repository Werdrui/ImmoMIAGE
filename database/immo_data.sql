-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mar. 07 oct. 2025 à 21:25
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `immo_data`
--

-- --------------------------------------------------------

--
-- Structure de la table `departements_prix`
--

CREATE TABLE `departements_prix` (
  `code_departement` char(3) NOT NULL,
  `nom_departement` varchar(100) NOT NULL,
  `prix_m2_moyen` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `departements_prix`
--

INSERT INTO `departements_prix` (`code_departement`, `nom_departement`, `prix_m2_moyen`) VALUES
('01', 'Ain', 2950),
('02', 'Aisne', 1850),
('03', 'Allier', 1650),
('04', 'Alpes-de-Haute-Provence', 2600),
('05', 'Hautes-Alpes', 2800),
('06', 'Alpes-Maritimes', 5800),
('07', 'Ardèche', 2400),
('08', 'Ardennes', 1500),
('09', 'Ariège', 1700),
('10', 'Aube', 2000),
('11', 'Aude', 2300),
('12', 'Aveyron', 1900),
('13', 'Bouches-du-Rhône', 4700),
('14', 'Calvados', 2900),
('15', 'Cantal', 1500),
('16', 'Charente', 1700),
('17', 'Charente-Maritime', 3300),
('18', 'Cher', 1600),
('19', 'Corrèze', 1700),
('21', 'Côte-d\'Or', 2400),
('22', 'Côtes-d\'Armor', 2600),
('23', 'Creuse', 1150),
('24', 'Dordogne', 2000),
('25', 'Doubs', 2700),
('26', 'Drôme', 2800),
('27', 'Eure', 2400),
('28', 'Eure-et-Loir', 2600),
('29', 'Finistère', 2700),
('2A', 'Corse-du-Sud', 4400),
('2B', 'Haute-Corse', 3700),
('30', 'Gard', 2800),
('31', 'Haute-Garonne', 3300),
('32', 'Gers', 1900),
('33', 'Gironde', 4100),
('34', 'Hérault', 3300),
('35', 'Ille-et-Vilaine', 3400),
('36', 'Indre', 1450),
('37', 'Indre-et-Loire', 2700),
('38', 'Isère', 3200),
('39', 'Jura', 1900),
('40', 'Landes', 2800),
('41', 'Loir-et-Cher', 2000),
('42', 'Loire', 2300),
('43', 'Haute-Loire', 1800),
('44', 'Loire-Atlantique', 3800),
('45', 'Loiret', 2500),
('46', 'Lot', 2000),
('47', 'Lot-et-Garonne', 1900),
('48', 'Lozère', 1600),
('49', 'Maine-et-Loire', 2600),
('50', 'Manche', 2300),
('51', 'Marne', 2400),
('52', 'Haute-Marne', 1400),
('53', 'Mayenne', 1800),
('54', 'Meurthe-et-Moselle', 2200),
('55', 'Meuse', 1300),
('56', 'Morbihan', 3100),
('57', 'Moselle', 2000),
('58', 'Nièvre', 1500),
('59', 'Nord', 2500),
('60', 'Oise', 2700),
('61', 'Orne', 1800),
('62', 'Pas-de-Calais', 2000),
('63', 'Puy-de-Dôme', 2500),
('64', 'Pyrénées-Atlantiques', 3400),
('65', 'Hautes-Pyrénées', 1900),
('66', 'Pyrénées-Orientales', 2700),
('67', 'Bas-Rhin', 2900),
('68', 'Haut-Rhin', 2800),
('69', 'Rhône', 4600),
('70', 'Haute-Saône', 1600),
('71', 'Saône-et-Loire', 1900),
('72', 'Sarthe', 2100),
('73', 'Savoie', 3800),
('74', 'Haute-Savoie', 5200),
('75', 'Paris', 10300),
('76', 'Seine-Maritime', 2400),
('77', 'Seine-et-Marne', 3000),
('78', 'Yvelines', 4200),
('79', 'Deux-Sèvres', 1900),
('80', 'Somme', 1900),
('81', 'Tarn', 2100),
('82', 'Tarn-et-Garonne', 2200),
('83', 'Var', 4600),
('84', 'Vaucluse', 3200),
('85', 'Vendée', 2900),
('86', 'Vienne', 2000),
('87', 'Haute-Vienne', 1700),
('88', 'Vosges', 1600),
('89', 'Yonne', 1900),
('90', 'Territoire de Belfort', 1900),
('91', 'Essonne', 3700),
('92', 'Hauts-de-Seine', 7200),
('93', 'Seine-Saint-Denis', 3800),
('94', 'Val-de-Marne', 4700),
('95', 'Val-d\'Oise', 3200);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `departements_prix`
--
ALTER TABLE `departements_prix`
  ADD PRIMARY KEY (`code_departement`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
