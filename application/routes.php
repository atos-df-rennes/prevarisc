<?php

$router = Zend_Controller_Front::getInstance()->getRouter();

$matriceDesDroits = new Zend_Controller_Router_Route('legacy/admin/groupes/matrice-des-droits', [
    'controller' => 'legacy',
    'action' => 'matrice-des-droits',
    'resource' => 'gestion_parametrages',
    'privilege' => 'admin',
    'uri' => '/legacy/admin/groupes/matrice-des-droits',
]);
$sauvegarderUtilisateur = new Zend_Controller_Router_Route('legacy/admin/utilisateurs/sauvegarder', [
    'controller' => 'legacy',
    'action' => 'save-user',
    'resource' => 'gestion_parametrages',
    'privilege' => 'admin',
    'uri' => '/legacy/admin/utilisateurs/sauvegarder',
]);

$viderCacheCouchesCartographiques = new Zend_Controller_Router_Route('legacy/admin/couches-cartographiques/vider-cache', [
    'controller' => 'legacy',
    'action' => 'vider-cache-couches-cartographiques',
    'resource' => 'gestion_parametrages',
    'privilege' => 'admin',
    'uri' => '/legacy/admin/couches-cartographiques/vider-cache',
]);

$router->addRoute('legacy_matrice_des_droits', $matriceDesDroits);
$router->addRoute('legacy_sauvegarder_utilisateur', $sauvegarderUtilisateur);
$router->addRoute('legacy_vider_cache_couches_cartographiques', $viderCacheCouchesCartographiques);
