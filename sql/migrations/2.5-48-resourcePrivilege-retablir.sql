SET NAMES 'utf8';

insert into resources values (NULL, 'rétablissement', 'Rétablissement des dossier set établissements supprimés');

insert into `privileges`(`name`, `text`, `id_resource`)
values (
    'rétablir', 
    'Retablissement dossiers, établissements', 
    (Select(resources.ID_RESOURCE) from resources where resources.name = 'rétablissement')
);