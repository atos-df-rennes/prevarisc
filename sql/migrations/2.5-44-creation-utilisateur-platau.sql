SET NAMES 'utf8';

INSERT INTO `utilisateurinformations`(`NOM_UTILISATEURINFORMATIONS`, `ID_FONCTION`) VALUES('Service Plat''AU', 99);
INSERT INTO `utilisateur`(`USERNAME_UTILISATEUR`, `ID_UTILISATEURINFORMATIONS`, `ID_GROUPE`) VALUES('platau', (SELECT `ID_UTILISATEURINFORMATIONS` from `utilisateurinformations` where `NOM_UTILISATEURINFORMATIONS` = 'Service Plat''AU'), 1);
INSERT INTO `utilisateurpreferences`(`ID_UTILISATEUR`, `DASHBOARD_BLOCS`) VALUES ((SELECT `ID_UTILISATEUR` from `utilisateur` where `USERNAME_UTILISATEUR` = 'platau'), NULL);