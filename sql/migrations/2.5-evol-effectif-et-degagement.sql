-- Creation des tables
CREATE TABLE `effectifDegagement` (
   ID_EFFECTIF_DEGAGEMENT bigint PRIMARY KEY NOT NULL AUTO_INCREMENT,
   DESCRIPTION_EFFECTIF TEXT,
   DESCRIPTION_DEGAGEMENT TEXT
);

CREATE TABLE `dossierEffectifDegagement` (
   ID_DOSSIER_EFFECTIF_DEGAGEMENT bigint PRIMARY KEY NOT NULL AUTO_INCREMENT,
   ID_EFFECTIF_DEGAGEMENT bigint,
   ID_DOSSIER bigint,
   FOREIGN KEY(`ID_EFFECTIF_DEGAGEMENT`) REFERENCES effectifDegagement(`ID_EFFECTIF_DEGAGEMENT`),
   FOREIGN KEY(`ID_DOSSIER`) REFERENCES dossier(`ID_DOSSIER`)
);

CREATE TABLE `etablissementEffectifDegagement` (
   ID_ETABLISSEMENT_EFFECTIF_DEGAGEMENT bigint PRIMARY KEY NOT NULL AUTO_INCREMENT, 
   ID_EFFECTIF_DEGAGEMENT bigint,
   ID_ETABLISSEMENT bigint unsigned,
   FOREIGN KEY(`ID_EFFECTIF_DEGAGEMENT`) REFERENCES effectifDegagement(`ID_EFFECTIF_DEGAGEMENT`),
   FOREIGN KEY(`ID_ETABLISSEMENT`) REFERENCES etablissement(`ID_ETABLISSEMENT`) 
);

-- Ajout des privilèges dossiers
INSERT INTO `privileges` (`name`, `text`, `id_resource`) values ("view_effectifs_degagements", "Lecture des effectifs et dégagements", 9);
INSERT INTO `privileges` (`name`, `text`, `id_resource`) values ("edit_effectifs_degagements", "Modification des effectifs et dégagements", 9);

-- Ajout des privilèges établissements / cellules / habitations / IGH / EIC
INSERT INTO `privileges` (`name`, `text`, `id_resource`) values ("view_effectifs_degagements", "Lecture des effectifs et dégagements", 4);
INSERT INTO `privileges` (`name`, `text`, `id_resource`) values ("edit_effectifs_degagements", "Modification des effectifs et dégagements", 4);
INSERT INTO `privileges` (`name`, `text`, `id_resource`) values ("view_effectifs_degagements", "Lecture des effectifs et dégagements", 5);
INSERT INTO `privileges` (`name`, `text`, `id_resource`) values ("edit_effectifs_degagements", "Modification des effectifs et dégagements", 5);
INSERT INTO `privileges` (`name`, `text`, `id_resource`) values ("view_effectifs_degagements", "Lecture des effectifs et dégagements", 6);
INSERT INTO `privileges` (`name`, `text`, `id_resource`) values ("edit_effectifs_degagements", "Modification des effectifs et dégagements", 6);
INSERT INTO `privileges` (`name`, `text`, `id_resource`) values ("view_effectifs_degagements", "Lecture des effectifs et dégagements", 7);
INSERT INTO `privileges` (`name`, `text`, `id_resource`) values ("edit_effectifs_degagements", "Modification des effectifs et dégagements", 7);
INSERT INTO `privileges` (`name`, `text`, `id_resource`) values ("view_effectifs_degagements", "Lecture des effectifs et dégagements", 8);
INSERT INTO `privileges` (`name`, `text`, `id_resource`) values ("edit_effectifs_degagements", "Modification des effectifs et dégagements", 8);