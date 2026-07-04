-- phpMyAdmin SQL Dump
-- version 4.2.11
-- http://www.phpmyadmin.net
--
-- Client :  127.0.0.1
-- Généré le :  Mar 29 Mai 2018 à 14:51
-- Version du serveur :  5.6.21
-- Version de PHP :  5.6.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données :  `bd_gestockevent24`
--

-- --------------------------------------------------------

--
-- Structure de la table `access`
--

CREATE TABLE IF NOT EXISTS `access` (
  `idAccess` int(10) NOT NULL,
  `access_codeCompte` varchar(20) NOT NULL,
  `access_codeSousMenu` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `access`
--

INSERT INTO `access` (`idAccess`, `access_codeCompte`, `access_codeSousMenu`) VALUES
(0, 'CAISSE', 'MAJS'),
(0, 'ADMIN', 'MARB'),
(0, 'ADMIN', 'SKRT'),
(0, 'ADMIN', 'VTCP'),
(0, 'ADMIN', 'VTCR'),
(0, 'ADMIN', 'VTAV'),
(0, 'ADMIN', 'ANVT'),
(0, 'ADMIN', 'VLVT'),
(0, 'ADMIN', 'FCCP'),
(0, 'ADMIN', 'FCCR'),
(0, 'ADMIN', 'EREN'),
(0, 'ADMIN', 'COSTK'),
(0, 'ADMIN', 'TBBD'),
(0, 'ADMIN', 'MAJA'),
(0, 'ADMIN', 'MAJAT'),
(0, 'ADMIN', 'MAJC'),
(0, 'ADMIN', 'MAJCO'),
(0, 'ADMIN', 'MAJE'),
(0, 'ADMIN', 'MAJF'),
(0, 'ADMIN', 'MAJTA'),
(0, 'ADMIN', 'MAJU'),
(0, 'ADMIN', 'MAJMC'),
(0, 'ADMIN', 'MAJS'),
(0, 'ADMIN', 'LVSA');

-- --------------------------------------------------------

--
-- Structure de la table `annuler_facture`
--

CREATE TABLE IF NOT EXISTS `annuler_facture` (
  `idFacture` int(10) NOT NULL,
  `codeFacture` varchar(20) NOT NULL,
  `dateFacture` date NOT NULL,
  `quantiteAFacture` int(6) NOT NULL,
  `statutFacture` int(1) NOT NULL,
  `solvabiliteFacture` int(1) NOT NULL,
  `remiseFacture` double NOT NULL,
  `prixVenteFacture` double NOT NULL,
  `nbRegFacture` int(3) NOT NULL,
  `ligneFacture` int(5) NOT NULL,
  `tvaFacture` int(1) NOT NULL,
  `totalFacture` double NOT NULL,
  `cmdFacture` varchar(50) NOT NULL,
  `facture_codeModalite` varchar(20) NOT NULL,
  `facture_codeTypeF` varchar(20) NOT NULL,
  `facture_codeClient` varchar(20) NOT NULL,
  `facture_codeArticle` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `annuler_facture`
--

INSERT INTO `annuler_facture` (`idFacture`, `codeFacture`, `dateFacture`, `quantiteAFacture`, `statutFacture`, `solvabiliteFacture`, `remiseFacture`, `prixVenteFacture`, `nbRegFacture`, `ligneFacture`, `tvaFacture`, `totalFacture`, `cmdFacture`, `facture_codeModalite`, `facture_codeTypeF`, `facture_codeClient`, `facture_codeArticle`) VALUES
(60, 'FAC-2018-0032', '2018-02-01', 1, 0, 0, 0, 2000, 1, 100, 0, 2000, '', 'COMPTANT', 'COMPTANT', 'test', 'PSFR80W');

-- --------------------------------------------------------

--
-- Structure de la table `article`
--

CREATE TABLE IF NOT EXISTS `article` (
`idArticle` int(10) NOT NULL,
  `codeArticle` varchar(20) NOT NULL,
  `designationArticle` varchar(150) NOT NULL,
  `codeBarArticle` varchar(20) NOT NULL,
  `prixMinArticle` double NOT NULL,
  `seuilArticle` int(3) NOT NULL,
  `qteStockArticle` int(6) NOT NULL,
  `article_codeTypeA` varchar(20) NOT NULL,
  `statutArticle` varchar(5) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=latin1;

--
-- Contenu de la table `article`
--

INSERT INTO `article` (`idArticle`, `codeArticle`, `designationArticle`, `codeBarArticle`, `prixMinArticle`, `seuilArticle`, `qteStockArticle`, `article_codeTypeA`, `statutArticle`) VALUES
(1, 'test', 'test', '', 1, 50, 342, 'TEST', 'ON'),
(10, 'ART1', 'CODE ARTICLE1', '', 1200, 12, 50, 'TEST', 'ON'),
(11, 'PSFR80W', 'PLAQUE SOLAIRE FRANCE 80WATT', '', 2000, 50, 14, 'PSFR', 'ON'),
(12, 'PSFR100W', 'PLAQUE SOLAIRE FRANCE 100WATT', '', 110000, 60, 250, 'PSFR', 'ON'),
(13, 'PSGER80W', 'PLAQUE SOLAIRE GERMANY 80WATT', '', 90000, 65, 53, 'PSGER', 'ON'),
(14, 'ded', 'dede', '', 10000, 25, 0, 'TEST', 'ON');

-- --------------------------------------------------------

--
-- Structure de la table `client`
--

CREATE TABLE IF NOT EXISTS `client` (
`idClient` int(10) NOT NULL,
  `codeClient` varchar(20) NOT NULL,
  `nomClient` varchar(65) NOT NULL,
  `adresseClient` varchar(60) NOT NULL,
  `telClient` varchar(15) NOT NULL,
  `regimeClient` varchar(50) NOT NULL,
  `rccmClient` varchar(50) NOT NULL,
  `ifuClient` varchar(50) NOT NULL,
  `divisionClient` varchar(50) NOT NULL,
  `statutClient` varchar(5) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

--
-- Contenu de la table `client`
--

INSERT INTO `client` (`idClient`, `codeClient`, `nomClient`, `adresseClient`, `telClient`, `regimeClient`, `rccmClient`, `ifuClient`, `divisionClient`, `statutClient`) VALUES
(1, 'E-KAKA', 'Entre KONSEIGA ABDOUL\\''S KARIM', 'Pissy Secteur 27 Ouaga BF', '0022670422419-', 'infos bancaires en exemple kk', 'rccm en exemple kk', 'ifu en exemple kk', '', 'ON'),
(2, 'E-KAKApp', 'Entre KONSEIGA ABDOUL KARIM', 'Pissy Secteur 27 Ouaga BF', '0022670422419', 'infos bancaires en exemple kkpp', 'rccm en exemple kkpp', 'ifu en exemple kkpp', '', 'ON'),
(3, 'test', 'EKOLAF', 'OUAGA BOULMIOUGOU SECT 27 PISSY', '25436757', 'REEL NORMAL (RI)', 'BF OUA 2001 B249', '0003225J', 'DIRECTION DES GRANDES ENTREPRISES', 'ON'),
(4, 'ESERV', 'ENVIRO SERVICES', 'Zagtouli Secteur 29 Ouaga BF', '25437657', 'infos bancaires en exemple eserv', 'rccm en exemple eserv', 'ifu en exemple eserv', 'DIRECTION DES GRANDES ENTREPRISES', 'ON'),
(5, 'TEST1', 'EKOLAF', 'OUAGA BOULMIOUGOU SECT 27 PISSY ', '25436757', 'REEL NORMAL', 'BF OUA 2001 B249', '0003225J', 'DIRECTION DES GRANDES ENTREPRISES', 'ON');

-- --------------------------------------------------------

--
-- Structure de la table `clientbck`
--

CREATE TABLE IF NOT EXISTS `clientbck` (
`idClient` int(3) NOT NULL,
  `codeClient` varchar(20) NOT NULL,
  `nomClient` varchar(65) NOT NULL,
  `adresseClient` varchar(60) NOT NULL,
  `telClient` varchar(15) NOT NULL,
  `statutClient` varchar(5) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

--
-- Contenu de la table `clientbck`
--

INSERT INTO `clientbck` (`idClient`, `codeClient`, `nomClient`, `adresseClient`, `telClient`, `statutClient`) VALUES
(1, 'E-KAKA', 'Entre KONSEIGA ABDOUL\\''S KARIM', 'Pissy Secteur 27 Ouaga BF', '0022670422419-', 'ON'),
(2, 'E-KAKApp', 'Entre KONSEIGA ABDOUL KARIM', 'Pissy Secteur 27 Ouaga BF', '0022670422419', 'ON'),
(3, 'test', 'test', 'test', 'test', 'ON'),
(4, 'ESERV', 'ENVIRO SERVICES', 'Zagtouli Secteur 29 Ouaga BF', '25437657', 'ON');

-- --------------------------------------------------------

--
-- Structure de la table `compte`
--

CREATE TABLE IF NOT EXISTS `compte` (
`idCompte` int(10) NOT NULL,
  `codeCompte` varchar(20) NOT NULL,
  `libelleCompte` varchar(100) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

--
-- Contenu de la table `compte`
--

INSERT INTO `compte` (`idCompte`, `codeCompte`, `libelleCompte`) VALUES
(3, 'CAISSE', 'Opérations de caisse'),
(4, 'STOCK', 'Opérations sur le stock'),
(5, 'ADMIN', 'Administrateur Général');

-- --------------------------------------------------------

--
-- Structure de la table `drop_entreestock`
--

CREATE TABLE IF NOT EXISTS `drop_entreestock` (
  `idEntree` int(10) NOT NULL,
  `entree_codeArticle` varchar(20) NOT NULL,
  `entree_codeMagasin` varchar(20) NOT NULL,
  `entree_codeFournisseur` varchar(20) NOT NULL,
  `quantiteAEntree` int(6) NOT NULL,
  `dateEntree` date NOT NULL,
  `prixAchatEntree` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `drop_entreestock`
--

INSERT INTO `drop_entreestock` (`idEntree`, `entree_codeArticle`, `entree_codeMagasin`, `entree_codeFournisseur`, `quantiteAEntree`, `dateEntree`, `prixAchatEntree`) VALUES
(1, 'test', '', 'KONWZEI', 34, '2017-12-18', 45),
(7, 'test', '', 'KONWZEI', 1, '2018-01-18', 123),
(6, 'test', '', '0', 0, '2018-01-18', 0);

-- --------------------------------------------------------

--
-- Structure de la table `entete`
--

CREATE TABLE IF NOT EXISTS `entete` (
`id` int(10) NOT NULL,
  `logo` varchar(255) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `adresse` varchar(60) NOT NULL,
  `tel1` varchar(20) NOT NULL,
  `tel2` varchar(20) NOT NULL,
  `banque` varchar(100) NOT NULL,
  `rccm` varchar(50) NOT NULL,
  `ifu` varchar(50) NOT NULL,
  `ctva` double NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Contenu de la table `entete`
--

INSERT INTO `entete` (`id`, `logo`, `nom`, `adresse`, `tel1`, `tel2`, `banque`, `rccm`, `ifu`, `ctva`) VALUES
(1, 'dist/img/lelogo.jpg', 'TECH 24', '11 BP 1047 Ouagadougou 11', '70 20 58 74', '50 40 11 41', 'Orabank Burkina Faso N° 0BF171 01601 062330400201 43', 'BFOUA2014 B 6935', '00061996S', 0.18);

-- --------------------------------------------------------

--
-- Structure de la table `entreestock`
--

CREATE TABLE IF NOT EXISTS `entreestock` (
`idEntree` int(3) NOT NULL,
  `entree_codeArticle` varchar(20) NOT NULL,
  `entree_codeMagasin` varchar(20) NOT NULL,
  `entree_codeFournisseur` varchar(20) NOT NULL,
  `quantiteAEntree` int(6) NOT NULL,
  `dateEntree` date NOT NULL,
  `prixAchatEntree` double NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

--
-- Contenu de la table `entreestock`
--

INSERT INTO `entreestock` (`idEntree`, `entree_codeArticle`, `entree_codeMagasin`, `entree_codeFournisseur`, `quantiteAEntree`, `dateEntree`, `prixAchatEntree`) VALUES
(2, 'PSFR80W', '', 'KONWZEI', 250, '2017-12-19', 50000),
(3, 'PSGER80W', '', 'KONWZEI', 120, '2017-12-19', 72000),
(4, 'PSFR80W', '', 'KONWZEI', 200, '2017-12-26', 1000),
(5, 'PSFR100W', '', 'KONWZEI', 250, '2018-01-04', 50000),
(8, 'test', '', 'KONWZEI', 1, '2018-01-18', 1);

-- --------------------------------------------------------

--
-- Structure de la table `facture`
--

CREATE TABLE IF NOT EXISTS `facture` (
`idFacture` int(10) NOT NULL,
  `codeFacture` varchar(20) NOT NULL,
  `dateFacture` date NOT NULL,
  `quantiteAFacture` int(6) NOT NULL,
  `statutFacture` int(1) NOT NULL,
  `solvabiliteFacture` int(1) NOT NULL,
  `remiseFacture` double NOT NULL,
  `prixVenteFacture` double NOT NULL,
  `nbRegFacture` int(3) NOT NULL,
  `ligneFacture` int(5) NOT NULL,
  `tvaFacture` int(1) NOT NULL,
  `totalFacture` double NOT NULL,
  `cmdFacture` varchar(50) NOT NULL,
  `facture_codeModalite` varchar(20) NOT NULL,
  `facture_codeTypeF` varchar(20) NOT NULL,
  `facture_codeClient` varchar(20) NOT NULL,
  `facture_codeArticle` varchar(20) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=latin1;

--
-- Contenu de la table `facture`
--

INSERT INTO `facture` (`idFacture`, `codeFacture`, `dateFacture`, `quantiteAFacture`, `statutFacture`, `solvabiliteFacture`, `remiseFacture`, `prixVenteFacture`, `nbRegFacture`, `ligneFacture`, `tvaFacture`, `totalFacture`, `cmdFacture`, `facture_codeModalite`, `facture_codeTypeF`, `facture_codeClient`, `facture_codeArticle`) VALUES
(8, 'FAC-2017-0004', '2017-12-21', 14, 1, 1, 5000, 97500, 2, 100, 0, 1365000, '', '1M', 'CREDIT', 'E-KAKApp', 'PSFR80W'),
(9, 'FAC-2017-0005', '2017-12-21', 16, 2, 1, 20000, 200000, 1, 100, 0, 3200000, '', 'AVOIR', 'AVOIR', 'E-KAKA', 'PSFR80W'),
(10, 'FAC-2017-0006', '2017-12-21', 1, 1, 1, 10500, 120000, 1, 100, 0, 120000, '', 'COMPTANT', 'COMPTANT', 'test', 'PSFR80W'),
(11, 'FAC-2017-0007', '2017-12-23', 17, 1, 1, 11500, 95000, 1, 100, 0, 1615000, '', 'COMPTANT', 'COMPTANT', 'test', 'PSFR80W'),
(12, 'FAC-2017-0008', '2017-12-26', 20, 1, 1, 20000, 1000000, 1, 100, 0, 20000000, '', 'COMPTANT', 'COMPTANT', 'E-KAKA', 'PSFR80W'),
(13, 'FAC-2018-0001', '2018-01-04', 250, 2, 1, 0, 125000, 1, 100, 0, 31250000, '', 'AVOIR', 'AVOIR', 'E-KAKApp', 'PSFR80W'),
(14, 'FAC-2018-0002', '2018-01-04', 5, 1, 1, 50000, 110000, 1, 100, 0, 550000, '', 'COMPTANT', 'COMPTANT', 'E-KAKApp', 'PSGER80W'),
(15, 'FAC-2018-0002', '2018-01-04', 15, 1, 1, 50000, 100000, 1, 200, 0, 1500000, '', 'COMPTANT', 'COMPTANT', 'E-KAKApp', 'PSFR80W'),
(44, 'FAC-2018-0018', '2018-01-17', 1, 1, 0, 6.5, 123000, 3, 100, 0, 123000, '', '1M', 'CREDIT', 'test', 'test'),
(45, 'FAC-2018-0019', '2018-01-17', 1, 1, 0, 7995, 123000, 3, 100, 0, 123000, '', '1M', 'CREDIT', 'ESERV', 'test'),
(46, 'FAC-2018-0020', '2018-01-17', 1, 1, 0, 22945, 123000, 3, 100, 0, 123000, '', '1M', 'CREDIT', 'test', 'test'),
(47, 'FAC-2018-0020', '2018-01-17', 1, 1, 0, 22945, 230000, 3, 200, 0, 230000, '', '1M', 'CREDIT', 'test', 'PSFR80W'),
(48, 'FAC-2018-0021', '2018-01-17', 1, 1, 1, 12500, 125000, 1, 100, 0, 125000, '', 'COMPTANT', 'COMPTANT', 'ESERV', 'test'),
(49, 'FAC-2018-0022', '2018-01-17', 2, 1, 1, 17500, 125000, 1, 100, 0, 250000, '', 'AVOIR', 'AVOIR', 'test', 'PSFR80W'),
(50, 'FAC-2018-0023', '2018-01-18', 1, 1, 1, 1200, 120000, 1, 100, 0, 120000, 'deded', 'COMPTANT', 'COMPTANT', 'ESERV', 'test'),
(51, 'FAC-2018-0024', '2018-01-23', 1, 1, 1, 0, 12000, 1, 100, 1, 12000, '', 'AVOIR', 'AVOIR', 'test', 'test'),
(52, 'FAC-2018-0025', '2018-01-23', 1, 1, 0, 0, 12000, 1, 100, 0, 12000, '12 OUALP 3222P', 'AVOIR', 'AVOIR', 'ESERV', 'PSFR80W'),
(53, 'FAC-2018-0026', '2018-01-23', 1, 1, 1, 2500, 125000, 1, 100, 1, 125000, '', 'COMPTANT', 'COMPTANT', 'ESERV', 'test'),
(54, 'FAC-2018-0027', '2018-01-23', 1, 1, 0, 7000, 150000, 2, 100, 1, 150000, '', '1M', 'CREDIT', 'E-KAKApp', 'PSFR80W'),
(55, 'FAC-2018-0028', '2018-01-27', 1, 1, 0, 3000, 200000, 1, 100, 1, 200000, '', 'COMPTANT', 'COMPTANT', 'ESERV', 'PSFR80W'),
(56, 'FAC-2018-0029', '2018-01-29', 1, 1, 0, 4000, 200000, 1, 100, 1, 200000, '', 'COMPTANT', 'COMPTANT', 'test', 'PSFR80W'),
(57, 'FAC-2018-0030', '2018-01-29', 1, 1, 1, 100, 20000, 2, 100, 0, 20000, '', '1M', 'CREDIT', 'ESERV', 'PSFR80W'),
(58, 'FAC-2018-0031', '2018-01-30', 1, 1, 0, 1200, 120000, 1, 100, 1, 120000, '', 'COMPTANT', 'COMPTANT', 'ESERV', 'PSFR80W'),
(61, 'FAC-2018-0032', '2018-02-06', 1, 0, 0, 0, 2000, 1, 100, 1, 2000, '05 OUALP 2146', 'COMPTANT', 'COMPTANT', 'test', 'PSFR80W'),
(62, 'FAC-2018-0033', '2018-02-06', 2, 0, 0, 270, 2000, 2, 100, 1, 4000, '05 OUALP 2146P', '1M', 'CREDIT', 'ESERV', 'PSFR80W'),
(63, 'FAC-2018-0034', '2018-02-06', 3, 0, 0, 410, 2000, 1, 100, 1, 6000, '05 OUALP 2146PP', 'AVOIR', 'AVOIR', 'E-KAKApp', 'PSFR80W');

-- --------------------------------------------------------

--
-- Structure de la table `fournisseur`
--

CREATE TABLE IF NOT EXISTS `fournisseur` (
`idFournisseur` int(10) NOT NULL,
  `codeFournisseur` varchar(20) NOT NULL,
  `nomFournisseur` varchar(65) NOT NULL,
  `adresseFournisseur` varchar(60) NOT NULL,
  `telFournisseur` varchar(15) NOT NULL,
  `statutFournisseur` varchar(5) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

--
-- Contenu de la table `fournisseur`
--

INSERT INTO `fournisseur` (`idFournisseur`, `codeFournisseur`, `nomFournisseur`, `adresseFournisseur`, `telFournisseur`, `statutFournisseur`) VALUES
(4, 'KONWZEI', 'test', 'test', 'test', 'ON');

-- --------------------------------------------------------

--
-- Structure de la table `magasin`
--

CREATE TABLE IF NOT EXISTS `magasin` (
`idMagasin` int(10) NOT NULL,
  `codeMagasin` varchar(20) NOT NULL,
  `libelleMagasin` varchar(60) NOT NULL,
  `statutMagasin` varchar(5) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Contenu de la table `magasin`
--

INSERT INTO `magasin` (`idMagasin`, `codeMagasin`, `libelleMagasin`, `statutMagasin`) VALUES
(1, 'TEST', 'TEST', 'ON'),
(2, '2TEST', '2ième TEST', 'ON');

-- --------------------------------------------------------

--
-- Structure de la table `menu`
--

CREATE TABLE IF NOT EXISTS `menu` (
`idMenu` int(10) NOT NULL,
  `titreMenu` varchar(30) NOT NULL,
  `iconeMenu` varchar(60) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

--
-- Contenu de la table `menu`
--

INSERT INTO `menu` (`idMenu`, `titreMenu`, `iconeMenu`) VALUES
(1, 'Stock', 'fa fa-laptop'),
(2, 'Vente', 'fa fa-edit'),
(3, 'Caisse', 'fa fa-money'),
(4, 'Statistique', 'fa fa-bar-chart-o'),
(5, 'Paramétrage', 'fa fa-table');

-- --------------------------------------------------------

--
-- Structure de la table `menuitem`
--

CREATE TABLE IF NOT EXISTS `menuitem` (
`idSousMenu` int(10) NOT NULL,
  `codeSousMenu` varchar(10) NOT NULL,
  `titreSousMenu` varchar(35) NOT NULL,
  `lienSousMenu` varchar(60) NOT NULL,
  `menu_idMenu` int(10) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=latin1;

--
-- Contenu de la table `menuitem`
--

INSERT INTO `menuitem` (`idSousMenu`, `codeSousMenu`, `titreSousMenu`, `lienSousMenu`, `menu_idMenu`) VALUES
(1, 'MAJS', 'MAJ du Stock', 'majstock.php', 1),
(2, 'LVSA', 'Livrer Vente à avoir', 'stockavoir.php', 1),
(3, 'MARB', 'Mise au rebut', 'miserebut.php', 1),
(4, 'SKRT', 'Enregistrer un retour', 'stockretour.php', 1),
(5, 'VTCP', 'Vente au comptant', 'ventecomptant.php', 2),
(6, 'VTCR', 'Vente à crédit', 'ventecredit.php', 2),
(7, 'VTAV', 'Vente à avoir', 'venteavoir.php', 2),
(8, 'ANVT', 'Annulation Vente', 'annulfacture.php', 2),
(9, 'VLVT', 'Validation Vente', 'validfacture.php', 2),
(10, 'FCCP', 'Facture Comptant', 'caissecomptant.php', 3),
(11, 'FCCR', 'Facture Crédit', 'caissecredit.php', 3),
(12, 'EREN', 'Enregistrer une entrée', 'caisseentree.php', 3),
(13, 'EREN', 'Enregistrer une dépense', 'caissedepense.php', 3),
(14, 'COSTK', 'Codes & Stock', 'listecode.php', 4),
(15, 'TBBD', 'Tableau de bord', 'tabbord.php', 4),
(16, 'MAJA', 'MAJ Accès', 'majacces.php', 5),
(17, 'MAJAT', 'MAJ Articles', 'majarticle.php', 5),
(18, 'MAJC', 'MAJ Clients', 'majclient.php', 5),
(19, 'MAJCO', 'MAJ Comptes', 'majcompte.php', 5),
(20, 'MAJE', 'MAJ Entête', 'majentete.php', 5),
(21, 'MAJF', 'MAJ Fournisseurs', 'majfournisseur.php', 5),
(22, 'MAJTA', 'MAJ Types Articles', 'majtypearticle.php', 5),
(23, 'MAJU', 'MAJ Utilisateurs', 'majuser.php', 5),
(24, 'MAJMC', 'Mon Compte', 'moncompte.php', 5);

-- --------------------------------------------------------

--
-- Structure de la table `modalite`
--

CREATE TABLE IF NOT EXISTS `modalite` (
`idModalite` int(10) NOT NULL,
  `codeModalite` varchar(20) NOT NULL,
  `periodiciteModalite` varchar(30) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

--
-- Contenu de la table `modalite`
--

INSERT INTO `modalite` (`idModalite`, `codeModalite`, `periodiciteModalite`) VALUES
(1, '1M', 'Mensuelle'),
(2, '3M', 'Trimestrielle'),
(3, '6M', 'Semestrielle'),
(4, '12M', 'Annuelle'),
(5, 'CC', 'Convenance du client');

-- --------------------------------------------------------

--
-- Structure de la table `rebus`
--

CREATE TABLE IF NOT EXISTS `rebus` (
`idRebus` int(10) NOT NULL,
  `quantiteARebus` int(6) NOT NULL,
  `dateRebus` date NOT NULL,
  `motifRebus` varchar(100) NOT NULL,
  `rebus_codeArticle` varchar(20) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Contenu de la table `rebus`
--

INSERT INTO `rebus` (`idRebus`, `quantiteARebus`, `dateRebus`, `motifRebus`, `rebus_codeArticle`) VALUES
(2, 800, '2017-12-23', 'peremption', 'test');

-- --------------------------------------------------------

--
-- Structure de la table `reglement`
--

CREATE TABLE IF NOT EXISTS `reglement` (
`idReglement` int(10) NOT NULL,
  `dateReglement` date NOT NULL,
  `montantReglement` double NOT NULL,
  `objetReglement` varchar(255) NOT NULL,
  `statutReglement` varchar(5) NOT NULL,
  `reglement_codeFacture` varchar(20) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=latin1;

--
-- Contenu de la table `reglement`
--

INSERT INTO `reglement` (`idReglement`, `dateReglement`, `montantReglement`, `objetReglement`, `statutReglement`, `reglement_codeFacture`) VALUES
(3, '2018-01-27', 109500, 'exple dobjet', 'D', 'FAC-2017-0006'),
(4, '2017-12-21', 3180000, '', 'D', 'FAC-2017-0005'),
(24, '2018-01-23', 14160, '', 'C', 'FAC-2018-0024'),
(25, '2018-01-27', 144550, 'Règlement Facture', 'C', 'FAC-2018-0026'),
(26, '2018-01-23', 794, 'Règlement Facture', 'C', 'FAC-2018-0018'),
(27, '2018-01-23', 200, 'Règlement Facture', 'C', 'FAC-2018-0018'),
(28, '2018-01-27', 245000, 'EXPLE RE RECETTE', 'C', ''),
(29, '2018-01-27', 112500, 'Règlement Facture', 'C', 'FAC-2018-0021'),
(30, '2018-01-29', 232500, 'Règlement Facture', 'C', 'FAC-2018-0022'),
(31, '2018-01-29', 900, 'Règlement Facture', 'C', 'FAC-2018-0030'),
(32, '2018-01-29', 9000, 'Règlement Facture', 'C', 'FAC-2018-0030'),
(33, '2018-01-29', 1500, 'Règlement Facture', 'C', 'FAC-2018-0030'),
(34, '2018-01-29', 500, 'Règlement Facture', 'C', 'FAC-2018-0030'),
(35, '2018-01-29', 1200, 'Règlement Facture', 'C', 'FAC-2018-0030'),
(36, '2018-01-29', 800, 'Règlement Facture', 'C', 'FAC-2018-0030'),
(37, '2018-01-29', 500, 'Règlement Facture', 'C', 'FAC-2018-0030'),
(38, '2018-01-29', 2500, 'Règlement Facture', 'C', 'FAC-2018-0030'),
(39, '2018-01-29', 2000, 'Règlement Facture', 'C', 'FAC-2018-0030'),
(40, '2018-01-29', 1000, 'Règlement Facture', 'C', 'FAC-2018-0030'),
(41, '2018-01-30', 15000, 'Règlement Facture', 'C', 'FAC-2018-0019'),
(42, '2018-01-30', 50000, 'Règlement Facture', 'C', 'FAC-2018-0019'),
(43, '2018-01-30', 25000, 'Règlement Facture', 'C', 'FAC-2018-0019'),
(44, '2018-01-30', 22000, 'Règlement Facture', 'C', 'FAC-2018-0018'),
(45, '2018-02-06', 118800, 'Règlement Facture', 'C', 'FAC-2018-0023'),
(46, '2018-05-21', 35000, 'Règlement Facture', 'C', 'FAC-2018-0018');

-- --------------------------------------------------------

--
-- Structure de la table `retour`
--

CREATE TABLE IF NOT EXISTS `retour` (
`idRetour` int(10) NOT NULL,
  `quantiteARetour` int(6) NOT NULL,
  `dateRetour` date NOT NULL,
  `motifRetour` varchar(100) NOT NULL,
  `retour_codeArticle` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `typearticle`
--

CREATE TABLE IF NOT EXISTS `typearticle` (
`idTypeA` int(10) NOT NULL,
  `codeTypeA` varchar(20) NOT NULL,
  `designationTypeA` varchar(255) NOT NULL,
  `statutTypeA` varchar(5) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

--
-- Contenu de la table `typearticle`
--

INSERT INTO `typearticle` (`idTypeA`, `codeTypeA`, `designationTypeA`, `statutTypeA`) VALUES
(1, 'TEST', 'TYPE TEST', 'ON'),
(2, 'PSFR', 'PLAQUE SOLAIRE FRANCE', 'ON'),
(3, 'PSGER', 'PLAQUE SOLAIRE GERMANY', 'ON');

-- --------------------------------------------------------

--
-- Structure de la table `typefacture`
--

CREATE TABLE IF NOT EXISTS `typefacture` (
`idTypeF` int(10) NOT NULL,
  `codeTypeF` varchar(20) NOT NULL,
  `designationTypeF` varchar(60) NOT NULL,
  `statutTypeF` varchar(5) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

--
-- Contenu de la table `typefacture`
--

INSERT INTO `typefacture` (`idTypeF`, `codeTypeF`, `designationTypeF`, `statutTypeF`) VALUES
(1, 'test', 'A effacer', 'ON'),
(2, 'AVOIR', 'Facture AVOIR', 'OFF'),
(3, 'CREDIT', 'Facture à CREDIT', 'OFF'),
(4, 'COMPTANT', 'Facture au COMPTANT', 'OFF');

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
`idUser` int(10) NOT NULL,
  `nomUser` varchar(30) NOT NULL,
  `prenomUser` varchar(50) NOT NULL,
  `loginUser` varchar(20) NOT NULL,
  `mdpUser` varchar(255) NOT NULL,
  `statutCompteUser` int(1) NOT NULL,
  `user_codeCompte` varchar(20) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

--
-- Contenu de la table `user`
--

INSERT INTO `user` (`idUser`, `nomUser`, `prenomUser`, `loginUser`, `mdpUser`, `statutCompteUser`, `user_codeCompte`) VALUES
(4, 'LOMPO', 'PATRICK', 'lpatrick', '3daaa8dc6234369052e49688d2fd0c23', 1, 'ADMIN');

--
-- Index pour les tables exportées
--

--
-- Index pour la table `article`
--
ALTER TABLE `article`
 ADD PRIMARY KEY (`idArticle`);

--
-- Index pour la table `client`
--
ALTER TABLE `client`
 ADD PRIMARY KEY (`idClient`);

--
-- Index pour la table `clientbck`
--
ALTER TABLE `clientbck`
 ADD PRIMARY KEY (`idClient`);

--
-- Index pour la table `compte`
--
ALTER TABLE `compte`
 ADD PRIMARY KEY (`idCompte`);

--
-- Index pour la table `entete`
--
ALTER TABLE `entete`
 ADD PRIMARY KEY (`id`);

--
-- Index pour la table `entreestock`
--
ALTER TABLE `entreestock`
 ADD PRIMARY KEY (`idEntree`);

--
-- Index pour la table `facture`
--
ALTER TABLE `facture`
 ADD PRIMARY KEY (`idFacture`);

--
-- Index pour la table `fournisseur`
--
ALTER TABLE `fournisseur`
 ADD PRIMARY KEY (`idFournisseur`);

--
-- Index pour la table `magasin`
--
ALTER TABLE `magasin`
 ADD PRIMARY KEY (`idMagasin`);

--
-- Index pour la table `menu`
--
ALTER TABLE `menu`
 ADD PRIMARY KEY (`idMenu`);

--
-- Index pour la table `menuitem`
--
ALTER TABLE `menuitem`
 ADD PRIMARY KEY (`idSousMenu`);

--
-- Index pour la table `modalite`
--
ALTER TABLE `modalite`
 ADD PRIMARY KEY (`idModalite`);

--
-- Index pour la table `rebus`
--
ALTER TABLE `rebus`
 ADD PRIMARY KEY (`idRebus`);

--
-- Index pour la table `reglement`
--
ALTER TABLE `reglement`
 ADD PRIMARY KEY (`idReglement`);

--
-- Index pour la table `retour`
--
ALTER TABLE `retour`
 ADD PRIMARY KEY (`idRetour`);

--
-- Index pour la table `typearticle`
--
ALTER TABLE `typearticle`
 ADD PRIMARY KEY (`idTypeA`);

--
-- Index pour la table `typefacture`
--
ALTER TABLE `typefacture`
 ADD PRIMARY KEY (`idTypeF`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
 ADD PRIMARY KEY (`idUser`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `article`
--
ALTER TABLE `article`
MODIFY `idArticle` int(10) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=15;
--
-- AUTO_INCREMENT pour la table `client`
--
ALTER TABLE `client`
MODIFY `idClient` int(10) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT pour la table `clientbck`
--
ALTER TABLE `clientbck`
MODIFY `idClient` int(3) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT pour la table `compte`
--
ALTER TABLE `compte`
MODIFY `idCompte` int(10) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT pour la table `entete`
--
ALTER TABLE `entete`
MODIFY `id` int(10) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT pour la table `entreestock`
--
ALTER TABLE `entreestock`
MODIFY `idEntree` int(3) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT pour la table `facture`
--
ALTER TABLE `facture`
MODIFY `idFacture` int(10) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=64;
--
-- AUTO_INCREMENT pour la table `fournisseur`
--
ALTER TABLE `fournisseur`
MODIFY `idFournisseur` int(10) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT pour la table `magasin`
--
ALTER TABLE `magasin`
MODIFY `idMagasin` int(10) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT pour la table `menu`
--
ALTER TABLE `menu`
MODIFY `idMenu` int(10) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT pour la table `menuitem`
--
ALTER TABLE `menuitem`
MODIFY `idSousMenu` int(10) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=25;
--
-- AUTO_INCREMENT pour la table `modalite`
--
ALTER TABLE `modalite`
MODIFY `idModalite` int(10) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT pour la table `rebus`
--
ALTER TABLE `rebus`
MODIFY `idRebus` int(10) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT pour la table `reglement`
--
ALTER TABLE `reglement`
MODIFY `idReglement` int(10) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=47;
--
-- AUTO_INCREMENT pour la table `retour`
--
ALTER TABLE `retour`
MODIFY `idRetour` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `typearticle`
--
ALTER TABLE `typearticle`
MODIFY `idTypeA` int(10) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT pour la table `typefacture`
--
ALTER TABLE `typefacture`
MODIFY `idTypeF` int(10) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
MODIFY `idUser` int(10) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
