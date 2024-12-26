-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : mysql-container:3306
-- Généré le : jeu. 26 déc. 2024 à 12:53
-- Version du serveur : 5.7.44
-- Version de PHP : 8.2.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `biblio`
--

-- --------------------------------------------------------

--
-- Structure de la table `livre`
--

CREATE TABLE `livre` (
  `id` int(11) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `auteur` varchar(255) NOT NULL,
  `disponibilite` enum('true','false') NOT NULL DEFAULT 'true'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `livre`
--

INSERT INTO `livre` (`id`, `titre`, `auteur`, `disponibilite`) VALUES
(5, 'livre 1', 'med', 'false'),
(6, 'livre 2', 'yassine', 'false'),
(7, 'livre 3', 'yahya', 'false'),
(8, 'livre 4', 'zoubir', 'false'),
(9, 'livre 5', ' hamza', 'false'),
(10, 'livre 6', 'zineb ', 'false'),
(11, 'livre 7', 'hajar', 'true'),
(12, 'livre 8', 'akram', 'false'),
(13, 'livre 9 ', 'soufiane', 'false'),
(14, 'livre 10', 'ismail', 'true');

-- --------------------------------------------------------

--
-- Structure de la table `pret`
--

CREATE TABLE `pret` (
  `id` int(11) NOT NULL,
  `dateEmprunt` date NOT NULL,
  `dateRetour` date DEFAULT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `livre_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `pret`
--

INSERT INTO `pret` (`id`, `dateEmprunt`, `dateRetour`, `utilisateur_id`, `livre_id`) VALUES
(8, '2024-12-26', '2024-12-27', 13, 5),
(9, '2024-12-26', '2024-12-28', 13, 6),
(10, '2024-12-26', '2024-12-28', 14, 8),
(11, '2024-12-26', '2024-12-29', 15, 9),
(12, '2024-12-26', '2024-12-27', 15, 13),
(13, '2024-12-26', '2024-12-29', 16, 10),
(14, '2024-12-26', '2024-12-29', 16, 7),
(15, '2024-12-26', '2024-12-27', 16, 12);

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

CREATE TABLE `utilisateur` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('USER','ADMIN') NOT NULL DEFAULT 'USER'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`id`, `nom`, `email`, `password`, `role`) VALUES
(1, 'mhammed Elkhalfi', 'mhammedelkhalfi@gmail.com', 'password', 'ADMIN'),
(4, 'user', 'user@gmail.com', '$2y$10$JoGmi9VjsiuZc.bDgAuaz.N7JRnwwjratJZSvkVPgHrZLwKZCuio6', 'USER'),
(5, 'admin', 'admin@gmail.com', '$2y$10$5K4JFj7Djm9YNpMJM7mZJOhIYUic6zyT1jEf6ztfgUrBIqCGOONQm', 'ADMIN'),
(8, 'admin1', 'admin1@gmail.com', '$2y$10$sF8yF0jXNSdwrZ7KDsSRzuP4urTMxTyBLAhYDQlYEDRuIRdK9eYyG', 'ADMIN'),
(11, 'admin2', 'admin2@gmail.com', '$2y$10$FrTH50GTUN5M.2/sYYA4A.37NbCnWd9iwQyadmTsjpo81GKjkKH8m', 'ADMIN'),
(12, 'admin3', 'admin3@gmail.com', '$2y$10$LRig5.lFDS3Q25hAopJVi.wBrTt5TWFd1KV2Jpc39dgikUZrePNUK', 'ADMIN'),
(13, 'user1', 'user1@gmail.com', '$2y$10$3LMPOZYqqZtETQsveWXBHOq.dLJlmleh2qUNCUHO1IpseOP77rWj.', 'USER'),
(14, 'user2', 'user2@gmail.com', '$2y$10$n8QC3SEZg9Dd3.7fWDku7OIdAKnVzrjaO40jnn383wtXd4xSTappS', 'USER'),
(15, 'user3', 'user3@gmail.com', '$2y$10$Xcyb6akMXx1swSIP6nVTM.2vB0EYnNv.m4hJTifqy7O0k0Wu2hv7m', 'USER'),
(16, 'user4', 'user4@gmail.com', '$2y$10$uNzfVjdZCeDbjYesy86ZM.ZFMiOkDoOSIPXqVpAFtqCaeLLRx7.Hy', 'USER');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `livre`
--
ALTER TABLE `livre`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `pret`
--
ALTER TABLE `pret`
  ADD PRIMARY KEY (`id`),
  ADD KEY `utilisateur_id` (`utilisateur_id`),
  ADD KEY `livre_id` (`livre_id`);

--
-- Index pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `livre`
--
ALTER TABLE `livre`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT pour la table `pret`
--
ALTER TABLE `pret`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `pret`
--
ALTER TABLE `pret`
  ADD CONSTRAINT `pret_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`id`),
  ADD CONSTRAINT `pret_ibfk_2` FOREIGN KEY (`livre_id`) REFERENCES `livre` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
