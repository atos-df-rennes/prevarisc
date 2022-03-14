SET NAMES 'utf8';

INSERT IGNORE INTO `capsulerubrique` VALUES
(2, 'descriptifVerificationsTechniques', 'Vérifications techniques du dossier');

CREATE TABLE IF NOT EXISTS `displayrubriquedossier` (
    `ID_DOSSIER` bigint(20) NOT NULL,
    `ID_RUBRIQUE` bigint(20) NOT NULL,
    `USER_DISPLAY` TINYINT(1) DEFAULT 0,
    PRIMARY KEY (`ID_DOSSIER`,`ID_RUBRIQUE`),
    KEY `fk_displayRubriqueDossier_dossier_idx` (`ID_DOSSIER`),
    KEY `fk_displayRubriqueDossier_rubrique_idx` (`ID_RUBRIQUE`),
    CONSTRAINT `fk_displayRubriqueDossier_dossier` FOREIGN KEY (`ID_DOSSIER`) REFERENCES `dossier` (`ID_DOSSIER`) ON DELETE CASCADE ON UPDATE NO ACTION,
    CONSTRAINT `fk_displayRubriqueDossier_rubrique` FOREIGN KEY (`ID_RUBRIQUE`) REFERENCES `rubrique` (`ID_RUBRIQUE`) ON DELETE CASCADE ON UPDATE CASCADE
); 

CREATE TABLE IF NOT EXISTS `dossiervaleur` (
	`ID_DOSSIER` bigint(20) NOT NULL,
    `ID_VALEUR` bigint(20) NOT NULL,
    KEY `fk_dossierValeur_dossier_idx` (`ID_DOSSIER`),
    KEY `fk_dossierValeur_valeur_idx` (`ID_VALEUR`),
    CONSTRAINT `fk_dossierValeur_dossier` FOREIGN KEY (`ID_DOSSIER`) REFERENCES `dossier` (`ID_DOSSIER`) ON DELETE CASCADE ON UPDATE NO ACTION,
	CONSTRAINT `fk_dossierValeur_valeur` FOREIGN KEY (`ID_VALEUR`) REFERENCES `valeur` (`ID_VALEUR`) ON DELETE CASCADE ON UPDATE CASCADE
);