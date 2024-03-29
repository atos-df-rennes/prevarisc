SET NAMES 'utf8';

CREATE TABLE IF NOT EXISTS `listetypechamprubrique` (
    `ID_TYPECHAMP` bigint(20) NOT NULL AUTO_INCREMENT,
    `TYPE` varchar(50) NOT NULL,
    PRIMARY KEY (`ID_TYPECHAMP`)
);

INSERT IGNORE INTO `listetypechamprubrique` VALUES
(1, 'Champ texte'),
(2, 'Champ texte long'),
(3, 'Liste'),
(4, 'Numérique'),
(5, 'Case à cocher');

CREATE TABLE IF NOT EXISTS `champ` (
    `ID_CHAMP` bigint(20) NOT NULL AUTO_INCREMENT,
    `NOM` varchar(255) NOT NULL,
    `ID_TYPECHAMP` bigint(20) NOT NULL,
    `ID_RUBRIQUE` bigint(20) NOT NULL,
    PRIMARY KEY (`ID_CHAMP`),
    CONSTRAINT `fk_champ_listetypechamprubrique` FOREIGN KEY (`ID_TYPECHAMP`) REFERENCES `listetypechamprubrique` (`ID_TYPECHAMP`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_champ_rubrique` FOREIGN KEY (`ID_RUBRIQUE`) REFERENCES `rubrique` (`ID_RUBRIQUE`) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS `champvaleurliste` (
    `ID_VALEURLISTE` bigint(20) NOT NULL AUTO_INCREMENT,
    `VALEUR` varchar(255) NOT NULL,
    `ID_CHAMP` bigint(20) NOT NULL,
    PRIMARY KEY (`ID_VALEURLISTE`),
    CONSTRAINT `fk_champvaleurliste_champ` FOREIGN KEY (`ID_CHAMP`) REFERENCES `champ` (`ID_CHAMP`) ON DELETE CASCADE ON UPDATE CASCADE
);