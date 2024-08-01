SET NAMES 'utf8';

delete `etablissement`
from `etablissement`
inner join `etablissementinformations` ei on etablissement.ID_ETABLISSEMENT = ei.ID_ETABLISSEMENT
where ei.LIBELLE_ETABLISSEMENTINFORMATIONS LIKE BINARY 'Etablissement Test%';