SET NAMES 'utf8';

INSERT INTO `listetypechamprubrique` VALUES (NULL, 'Parent');

ALTER TABLE `champ` ADD column `ID_PARENT` bigint(20);

ALTER TABLE `champ` 
	ADD CONSTRAINT `fk_ID_PARENT_CHAMP` 
    FOREIGN KEY (`ID_PARENT`) 
    REFERENCES `champ`(`ID_CHAMP`);