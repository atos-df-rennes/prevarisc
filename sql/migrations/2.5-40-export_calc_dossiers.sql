SET NAMES 'utf8';

INSERT INTO `privileges`(`id_privilege`,`name`, `text`,`id_resource`) VALUES(210,"export_doss", "Dossiers",200);
UPDATE `dossiernatureliste` SET  `ID_DOSSIERTYPE` =  2 WHERE  `dossiernatureliste`.`ID_DOSSIERNATURE` = 19;
