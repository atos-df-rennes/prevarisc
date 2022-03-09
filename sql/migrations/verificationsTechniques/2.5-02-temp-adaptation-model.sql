ALTER TABLE `valeur` DROP FOREIGN KEY `fk_valeur_etablissement`;
ALTER TABLE `valeur` DROP KEY `fk_valeur_etablissement_idx`;
ALTER TABLE `valeur` DROP COLUMN `ID_ETABLISSEMENT`;