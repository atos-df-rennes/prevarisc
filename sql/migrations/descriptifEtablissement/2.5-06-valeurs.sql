SET NAMES 'utf8';

CREATE TABLE IF NOT EXISTS `valeur` (
    `ID_VALEUR` bigint(20) NOT NULL AUTO_INCREMENT,
    `VALEUR_STR` varchar(255) DEFAULT NULL,
    `VALEUR_LONG_STR` text DEFAULT NULL,
    `VALEUR_INT` int(10) DEFAULT NULL,
    `VALEUR_CHECKBOX` TINYINT(1) DEFAULT NULL,
    `ID_CHAMP` bigint(20) NOT NULL,
    `ID_ETABLISSEMENT` bigint(20) unsigned NOT NULL,
    PRIMARY KEY (`ID_VALEUR`),
    KEY `fk_valeur_champ_idx` (`ID_CHAMP`),
    KEY `fk_valeur_etablissement_idx` (`ID_ETABLISSEMENT`),
    CONSTRAINT `fk_valeur_champ` FOREIGN KEY (`ID_CHAMP`) REFERENCES `champ` (`ID_CHAMP`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_valeur_etablissement` FOREIGN KEY (`ID_ETABLISSEMENT`) REFERENCES `etablissement` (`ID_ETABLISSEMENT`) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS `etablissementvaleur` (
	`ID_ETABLISSEMENT` bigint(20) unsigned NOT NULL,
    `ID_VALEUR` bigint(20) NOT NULL,
    KEY `fk_etablissementvaleur_dossier_idx` (`ID_ETABLISSEMENT`),
    KEY `fk_etablissementvaleur_valeur_idx` (`ID_VALEUR`),
    CONSTRAINT `fk_etablissementvaleur_etablissement` FOREIGN KEY (`ID_ETABLISSEMENT`) REFERENCES `etablissement` (`ID_ETABLISSEMENT`) ON DELETE CASCADE ON UPDATE NO ACTION,
	CONSTRAINT `fk_etablissementvaleur_valeur` FOREIGN KEY (`ID_VALEUR`) REFERENCES `valeur` (`ID_VALEUR`) ON DELETE CASCADE ON UPDATE CASCADE
);
