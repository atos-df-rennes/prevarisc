SET NAMES 'utf8';

ALTER TABLE etablissement ADD COLUMN `DELETE_BY` bigint(20) unsigned DEFAULT NULL;
ALTER TABLE dossier ADD COLUMN `DELETE_BY` bigint(20) unsigned DEFAULT NULL;