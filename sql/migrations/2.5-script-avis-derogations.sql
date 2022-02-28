CREATE TABLE `avisderogations` (
   `ID_AVIS_DEROGATION` BIGINT(20) NOT NULL AUTO_INCREMENT,
   `TYPE` VARCHAR(50) NOT NULL,
   `TITRE` VARCHAR(50) NOT NULL,
   `INFORMATIONS` TEXT DEFAULT NULL,
   `AVIS` INT(1) UNSIGNED DEFAULT NULL,
   `ID_DOSSIER_LIE` BIGINT(20) DEFAULT NULL,
   `DISPLAY_HISTORIQUE` TINYINT DEFAULT 0,
   `ID_DOSSIER` BIGINT(20) NOT NULL,
   PRIMARY KEY (`ID_AVIS_DEROGATION`),
   KEY `fk_avisderogation_avis1_idx` (`AVIS`),
   KEY `fk_avisderogation_dossierlie1_idx` (`ID_DOSSIER_LIE`),
   KEY `fk_avisderogation_dossier1_idx` (`ID_DOSSIER`),
   CONSTRAINT `fk_avisderogation_avis1` FOREIGN KEY (`AVIS`) REFERENCES `avis` (`ID_AVIS`) ON DELETE NO ACTION ON UPDATE CASCADE,
   CONSTRAINT `fk_avisderogation_dossierlie1_idx` FOREIGN KEY (`ID_DOSSIER_LIE`) REFERENCES `dossier` (`ID_DOSSIER`) ON DELETE NO ACTION ON UPDATE CASCADE,
   CONSTRAINT `fk_avisderogation_dossier1_idx` FOREIGN KEY (`ID_DOSSIER`) REFERENCES `dossier` (`ID_DOSSIER`) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Ajout de la resource
INSERT INTO `resources`(`name`, `text`) VALUES('avisderogations', 'Avis et dérogations');

-- Ajout du privilege
INSERT INTO `privileges`(`name`, `text`,`id_resource`) VALUES('avis_derogations', 'Lecture et écriture des avis et dérogations', (select `id_resource` from resources where `name` = 'avisderogations'));
