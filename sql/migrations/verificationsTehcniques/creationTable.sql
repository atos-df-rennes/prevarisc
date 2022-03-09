CREATE TABLE `capsulerubriquedossier` (

    `ID_DOSSIER` bigint(20) NOT NULL,

    `ID_CAPSULERUBRIQUE` bigint(20) NOT NULL,

    PRIMARY KEY (`ID_DOSSIER`,`ID_CAPSULERUBRIQUE`),

    KEY `fk_capsuleRubriqueDossier_dossier_idx` (`ID_DOSSIER`),

    KEY `fk_capsuleRubriqueDossier_capsulerubrique_idx` (`ID_CAPSULERUBRIQUE`),

    CONSTRAINT `fk_capsuleRubriqueDossier_dossier` FOREIGN KEY (`ID_DOSSIER`) REFERENCES `dossier` (`ID_DOSSIER`) ON DELETE CASCADE ON UPDATE NO ACTION,

    CONSTRAINT `fk_capsuleRubriqueDossier_capsulerubrique` FOREIGN KEY (`ID_CAPSULERUBRIQUE`) REFERENCES `capsulerubrique` (`ID_CAPSULERUBRIQUE`) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE `displayrubriqueDossier` (
    `ID_DOSSIER` bigint(20) NOT NULL,
    `ID_RUBRIQUE` bigint(20) NOT NULL,
    `USER_DISPLAY` TINYINT(1) DEFAULT 0,
    PRIMARY KEY (`ID_DOSSIER`,`ID_RUBRIQUE`),
    KEY `fk_displayRubriqueDossier_dossier_idx` (`ID_DOSSIER`),
    KEY `fk_displayRubriqueDossier_rubrique_idx` (`ID_RUBRIQUE`),
    CONSTRAINT `fk_displayRubriqueDossier_dossier` FOREIGN KEY (`ID_DOSSIER`) REFERENCES `dossier` (`ID_DOSSIER`) ON DELETE CASCADE ON UPDATE NO ACTION,
    CONSTRAINT `fk_displayRubriqueDossier_rubrique` FOREIGN KEY (`ID_RUBRIQUE`) REFERENCES `rubrique` (`ID_RUBRIQUE`) ON DELETE CASCADE ON UPDATE CASCADE
); 

create table 'dossierValeur'(
	'ID_DOSSIER' BIGINT not null,
    'ID_VALEUR' BIGINT not null,
    FOREIGN KEY ('ID_DOSSIER') REFERENCES dossier('ID_DOSSIER'),
	FOREIGN KEY ('ID_VALEUR') REFERENCES valeur('ID_VALEUR')
);