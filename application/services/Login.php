<?php

class Service_Login
{
    // login pour l'api

    public function login(string $username, string $password): array
    {
        // Reponse
        $reponse = [];

        try {
            // Modèles de données
            $model_utilisateurInformations = new Model_DbTable_UtilisateurInformations();
            $model_utilisateur = new Model_DbTable_Utilisateur();
            $model_groupe = new Model_DbTable_Groupe();
            $model_fonction = new Model_DbTable_Fonction();

            // Récupération de l'utilisateur
            $user = $model_utilisateur->fetchRow(
                $model_utilisateur->select()
                    ->where('USERNAME_UTILISATEUR = ?', $username)
                    ->where('PASSWD_UTILISATEUR = ?', $password)
            );

            // Si l'utilisateur n'est pas actif, on renvoie false
            if (
                !$user instanceof Zend_Db_Table_Row_Abstract
                || !$user->ACTIF_UTILISATEUR
                || md5($username.getenv('PREVARISC_SECURITY_SALT').$password) != $user->PASSWD_UTILISATEUR
            ) {
                $reponse = 'non_autorise';
                $results = [
                    'reponse' => $reponse,
                ];
            } else {
                $reponse = 'autorise';
                $results = [
                    'reponse' => $reponse,
                ];
            }

            // Stockage de l'utilisateur dans la session
            if ('autorise' === $reponse) {
                $row_utilisateurInformations = $model_utilisateurInformations->find($user->ID_UTILISATEURINFORMATIONS)->current();
                $row_groupe = $model_groupe->find($user->ID_GROUPE)->current();
                $row_fonction = $model_fonction->find($row_utilisateurInformations->ID_FONCTION)->current();
                $time = time();

                // les informations (ici: id de l'utilisateur et la date de création du jeton)
                // vont être transmis en clair dans un cookie  et ajouté au jeton pour être signé.
                // On pourra ainsi s'assurer de leur authenticité.
                $informations = $user->ID_UTILISATEUR;

                // On encode le jeton
                $token = hash('sha256', 'login'.($time + $informations));

                $results = [
                    'reponse' => $reponse,
                    'results' => [
                        'ID_UTILISATEUR' => $user->ID_UTILISATEUR,
                        'LIBELLE_FONCTION' => $row_fonction->LIBELLE_FONCTION,
                        'ID_UTILISATEURINFORMATIONS' => $user->ID_UTILISATEURINFORMATIONS,
                        'NOM_UTILISATEURINFORMATIONS' => $row_utilisateurInformations->NOM_UTILISATEURINFORMATIONS,
                        'PRENOM_UTILISATEURINFORMATIONS' => $row_utilisateurInformations->PRENOM_UTILISATEURINFORMATIONS,
                        'LIBELLE_GROUPE' => $row_groupe->LIBELLE_GROUPE,
                        'ID_GROUPE' => $row_groupe->ID_GROUPE,
                        'TOKEN' => $token,
                    ],
                ];
            }
        } catch (Exception $exception) {
            $reponse = 'erreur';
            $results = [
                'reponse' => $reponse,
            ];
        }

        return $results;
    }
}
