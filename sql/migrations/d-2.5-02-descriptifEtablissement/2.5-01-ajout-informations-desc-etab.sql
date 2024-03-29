SET NAMES 'utf8';

INSERT IGNORE INTO `capsulerubrique` VALUES
(1, 'descriptifEtablissement', 'Descriptif de l''établissement');

CREATE TABLE IF NOT EXISTS `displayrubriqueetablissement` (
    `ID_ETABLISSEMENT` bigint(20) unsigned NOT NULL,
    `ID_RUBRIQUE` bigint(20) NOT NULL,
    `USER_DISPLAY` TINYINT(1) DEFAULT 0,
    PRIMARY KEY (`ID_ETABLISSEMENT`,`ID_RUBRIQUE`),
    KEY `fk_displayRubriqueEtablissement_etablissement_idx` (`ID_ETABLISSEMENT`),
    KEY `fk_displayRubriqueEtablissement_rubrique_idx` (`ID_RUBRIQUE`),
    CONSTRAINT `fk_displayRubriqueEtablissement_etablissement` FOREIGN KEY (`ID_ETABLISSEMENT`) REFERENCES `etablissement` (`ID_ETABLISSEMENT`) ON DELETE CASCADE ON UPDATE NO ACTION,
    CONSTRAINT `fk_displayRubriqueEtablissement_rubrique` FOREIGN KEY (`ID_RUBRIQUE`) REFERENCES `rubrique` (`ID_RUBRIQUE`) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS `etablissementvaleur` (
	`ID_ETABLISSEMENT` bigint(20) unsigned NOT NULL,
    `ID_VALEUR` bigint(20) NOT NULL,
    KEY `fk_etablissementValeur_dossier_idx` (`ID_ETABLISSEMENT`),
    KEY `fk_etablissementValeur_valeur_idx` (`ID_VALEUR`),
    CONSTRAINT `fk_etablissementValeur_etablissement` FOREIGN KEY (`ID_ETABLISSEMENT`) REFERENCES `etablissement` (`ID_ETABLISSEMENT`) ON DELETE CASCADE ON UPDATE NO ACTION,
	CONSTRAINT `fk_etablissementValeur_valeur` FOREIGN KEY (`ID_VALEUR`) REFERENCES `valeur` (`ID_VALEUR`) ON DELETE CASCADE ON UPDATE CASCADE
);