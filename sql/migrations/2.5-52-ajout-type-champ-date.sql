SET NAMES 'utf8';

INSERT IGNORE INTO `listetypechamprubrique` VALUES (7, 'Date');
UPDATE `listetypechamprubrique` SET `TYPE` = 'Texte' WHERE `ID_TYPECHAMP` = 1;
UPDATE `listetypechamprubrique` SET `TYPE` = 'Texte long' WHERE `ID_TYPECHAMP` = 2;

ALTER TABLE `valeur` ADD column `VALEUR_DATE` DATE DEFAULT NULL;
