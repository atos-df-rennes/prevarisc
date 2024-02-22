SET NAMES 'utf8';

update `etablissementinformations` as `dest`,
(
	select
        eia.ID_ETABLISSEMENTINFORMATIONS as ID_ETAB_CELLULE,
		eia2.ID_CATEGORIE as CATEGORIE_ETAB
	from etablissementinformationsactuel eia
	inner join etablissementlie el on eia.ID_ETABLISSEMENT = el.ID_FILS_ETABLISSEMENT
	left join etablissementinformationsactuel eia2 on eia2.ID_ETABLISSEMENT = el.ID_ETABLISSEMENT
	where eia.ID_GENRE = 3
	and eia2.ID_GENRE = 2
	and eia.ID_CATEGORIE <> eia2.ID_CATEGORIE
) as `src`
set dest.ID_CATEGORIE = src.CATEGORIE_ETAB
where dest.ID_ETABLISSEMENTINFORMATIONS = src.ID_ETAB_CELLULE;