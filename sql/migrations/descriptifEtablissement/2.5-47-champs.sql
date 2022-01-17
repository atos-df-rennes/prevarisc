SET NAMES 'utf8';

CREATE TABLE `listetypechamprubrique` (
    `ID_TYPECHAMP` bigint(20) NOT NULL AUTO_INCREMENT,
    `TYPE` varchar(50) NOT NULL,
    PRIMARY KEY (`ID_TYPECHAMP`)
);

INSERT INTO `listetypechamprubrique` VALUES
(1, 'Liste'),
(2, 'Champ texte'),
(3, 'Numérique'),
(4, 'Case à cocher');

CREATE TABLE `champ` (
    `ID_CHAMP` bigint(20) NOT NULL AUTO_INCREMENT,
    `NOM` varchar(255) NOT NULL,
    `ID_TYPECHAMP` bigint(20) NOT NULL,
    `ID_RUBRIQUE` bigint(20) NOT NULL,
    PRIMARY KEY (`ID_CHAMP`),
    CONSTRAINT `fk_champ_listetypechamprubrique` FOREIGN KEY (`ID_TYPECHAMP`) REFERENCES `listetypechamprubrique` (`ID_TYPECHAMP`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_champ_rubrique` FOREIGN KEY (`ID_RUBRIQUE`) REFERENCES `rubrique` (`ID_RUBRIQUE`) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE `listesprv` (
    `ID_LISTEPRV` bigint(20) NOT NULL AUTO_INCREMENT,
    `MODEL` varchar(255) NOT NULL,
    `NOM` varchar(255) NOT NULL,
    PRIMARY KEY (`ID_LISTEPRV`)
);

-- FIXME Ajouter toutes les listes qu'on a dans PRV
INSERT INTO `listesprv` VALUES
(1, 'Model_DbTable_AdresseCommune', 'Communes'),
(2, 'Model_DbTable_Fonction', 'Fonctions');