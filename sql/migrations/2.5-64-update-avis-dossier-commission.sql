-- Mise à jour des libellés en fonction de l'ID
UPDATE `dossier`
SET `AVIS_DOSSIER_COMMISSION_LIBELLE` = 
    CASE 
        WHEN `AVIS_DOSSIER_COMMISSION` = 1 THEN 'Favorable'
        WHEN `AVIS_DOSSIER_COMMISSION` = 2 THEN 'Défavorable'
    END
WHERE `AVIS_DOSSIER_COMMISSION` IN (1, 2);

-- Changement de l'ID 2 à 3 après la mise à jour des libellés
UPDATE `dossier`
SET `AVIS_DOSSIER_COMMISSION` = 3
WHERE `AVIS_DOSSIER_COMMISSION` = 2;
