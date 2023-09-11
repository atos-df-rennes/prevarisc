-- Creation des tables
CREATE TABLE `effectifdegagement` (
   ID_EFFECTIF_DEGAGEMENT bigint PRIMARY KEY NOT NULL AUTO_INCREMENT,
   DESCRIPTION_EFFECTIF TEXT,
   DESCRIPTION_DEGAGEMENT TEXT
);

CREATE TABLE `dossiereffectifdegagement` (
   ID_DOSSIER_EFFECTIF_DEGAGEMENT bigint PRIMARY KEY NOT NULL AUTO_INCREMENT,
   ID_EFFECTIF_DEGAGEMENT bigint,
   ID_DOSSIER bigint,
   FOREIGN KEY(`ID_EFFECTIF_DEGAGEMENT`) REFERENCES `effectifdegagement`(`ID_EFFECTIF_DEGAGEMENT`) ON DELETE CASCADE ON UPDATE NO ACTION,
   FOREIGN KEY(`ID_DOSSIER`) REFERENCES `dossier`(`ID_DOSSIER`) ON DELETE CASCADE ON UPDATE NO ACTION
);

CREATE TABLE `etablissementeffectifdegagement` (
   ID_ETABLISSEMENT_EFFECTIF_DEGAGEMENT bigint PRIMARY KEY NOT NULL AUTO_INCREMENT, 
   ID_EFFECTIF_DEGAGEMENT bigint,
   ID_ETABLISSEMENT bigint unsigned,
   FOREIGN KEY(`ID_EFFECTIF_DEGAGEMENT`) REFERENCES `effectifdegagement`(`ID_EFFECTIF_DEGAGEMENT`) ON DELETE CASCADE ON UPDATE NO ACTION,
   FOREIGN KEY(`ID_ETABLISSEMENT`) REFERENCES `etablissement`(`ID_ETABLISSEMENT`) ON DELETE CASCADE ON UPDATE NO ACTION
);

-- Ajout des ressources dossiers
INSERT INTO `resources` (`name`, `text`) values ("effectifs_degagements", "Effectifs et Dégagements");

INSERT INTO `privileges` (`name`, `text`, `id_resource`)
   values ("effectifs_degagements_ets", "Etablissements", (select id_resource from resources where name = 'effectifs_degagements'));
INSERT INTO `privileges` (`name`, `text`, `id_resource`)
   values ("effectifs_degagements_doss", "Dossiers", (select id_resource from resources where name = 'effectifs_degagements'));