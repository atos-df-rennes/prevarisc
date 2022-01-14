SET NAMES 'utf8';

CREATE TABLE `capsulerubrique` (
    `ID_CAPSULERUBRIQUE` bigint(20) NOT NULL AUTO_INCREMENT,
    `NOM_INTERNE` varchar(255) NOT NULL,
    `NOM` varchar(255) NOT NULL,
    PRIMARY KEY (`ID_CAPSULERUBRIQUE`)
);

INSERT INTO `capsulerubrique`(`NOM_INTERNE`,`NOM`) VALUES
('descriptifEtablissement', 'Descriptif de l''établissement'),
('verificationsTechniquesDossier', 'Vérifications techniques des visites');

CREATE TABLE `capsulerubriqueetablissement` (
    `ID_ETABLISSEMENT` bigint(20) unsigned NOT NULL,
    `ID_CAPSULERUBRIQUE` bigint(20) NOT NULL,
    PRIMARY KEY (`ID_ETABLISSEMENT`,`ID_CAPSULERUBRIQUE`),
    KEY `fk_capsuleRubriqueEtablissement_etablissement_idx` (`ID_ETABLISSEMENT`),
    KEY `fk_capsuleRubriqueEtablissement_capsulerubrique_idx` (`ID_CAPSULERUBRIQUE`),
    CONSTRAINT `fk_capsuleRubriqueEtablissement_etablissement` FOREIGN KEY (`ID_ETABLISSEMENT`) REFERENCES `etablissement` (`ID_ETABLISSEMENT`) ON DELETE CASCADE ON UPDATE NO ACTION,
    CONSTRAINT `fk_capsuleRubriqueEtablissement_capsulerubrique` FOREIGN KEY (`ID_CAPSULERUBRIQUE`) REFERENCES `capsulerubrique` (`ID_CAPSULERUBRIQUE`) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE `capsulerubriquedossier` (
    `ID_DOSSIER` bigint(20) NOT NULL,
    `ID_CAPSULERUBRIQUE` bigint(20) NOT NULL,
    PRIMARY KEY (`ID_DOSSIER`,`ID_CAPSULERUBRIQUE`),
    KEY `fk_capsuleRubriqueDossier_dossier_idx` (`ID_DOSSIER`),
    KEY `fk_capsuleRubriqueEtablissement_capsulerubrique_idx` (`ID_CAPSULERUBRIQUE`),
    CONSTRAINT `fk_capsuleRubriqueDossier_dossier` FOREIGN KEY (`ID_DOSSIER`) REFERENCES `dossier` (`ID_DOSSIER`) ON DELETE CASCADE ON UPDATE NO ACTION,
    CONSTRAINT `fk_capsuleRubriqueDossier_capsulerubrique` FOREIGN KEY (`ID_CAPSULERUBRIQUE`) REFERENCES `capsulerubrique` (`ID_CAPSULERUBRIQUE`) ON DELETE CASCADE ON UPDATE CASCADE
);