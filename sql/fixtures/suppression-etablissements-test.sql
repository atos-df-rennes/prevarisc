SET NAMES 'utf8';

delete
from `etablissementinformations`
where LIBELLE_ETABLISSEMENTINFORMATIONS LIKE BINARY 'Etablissement Test%';

delete `etablissement`
from `etablissement`
inner join `etablissementinformations` ei on etablissement.ID_ETABLISSEMENT = ei.ID_ETABLISSEMENT
where ei.LIBELLE_ETABLISSEMENTINFORMATIONS LIKE BINARY 'Etablissement Test%';