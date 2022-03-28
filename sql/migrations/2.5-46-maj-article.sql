alter table etablissementinformations CHANGE R12320_ETABLISSEMENTINFORMATIONS R14320_ETABLISSEMENTINFORMATIONS tinyint;
UPDATE prescriptionarticleliste set LIBELLE_ARTICLE = 'R 143-20' where prescriptionarticleliste.LIBELLE_ARTICLE like 'R%123-20';
UPDATE prescriptionarticleliste set LIBELLE_ARTICLE = 'R 143-16' where prescriptionarticleliste.LIBELLE_ARTICLE like 'R%123-16';