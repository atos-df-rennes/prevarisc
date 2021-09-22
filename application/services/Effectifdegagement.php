<?php

class Service_Effectifdegagement
{
    /**
     * Définition des balises.
     */
    const BALISES = array(
        '{activitePrincipaleEtablissement}' => array(
            'description' => "L'activité principale de l'établissement",
            'model' => 'informations',
            'champ' => 'LIBELLE_TYPEACTIVITE_PRINCIPAL',
        ),
        '{categorieEtablissement}' => array(
            'description' => "La catégorie de l'etablissement",
            'model' => 'informations',
            'champ' => 'LIBELLE_CATEGORIE',
        ),
        '{etablissementAvis}' => array(
            'description' => "L'avis de l'établissement",
            'model' => 'avis',
            'champ' => '',
        ),
        '{etablissementLibelle}' => array(
            'description' => "Le libelle de l'établissement",
            'model' => 'informations',
            'champ' => 'LIBELLE_ETABLISSEMENTINFORMATIONS',
        ),
        '{etablissementNumeroId}' => array(
            'description' => "Le numéro Id de l'établissement",
            'model' => 'general',
            'champ' => 'NUMEROID_ETABLISSEMENT',
        ),
        '{etablissementStatut}' => array(
            'description' => "Le statut (Ouvert ou Fermé) de l'établissement",
            'model' => 'informations',
            'champ' => 'LIBELLE_STATUT',
        ),
        '{typePrincipalEtablissement}' => array(
            'description' => "Le type principal de l'établissement",
            'model' => 'informations',
            'champ' => 'LIBELLE_TYPE_PRINCIPAL',
        ),

    );


    /**
     *  Retourne un changement via son Id précisé en argument.
     *
     * @param int $idChangement L'id du changement à retourner
     *
     * @return Zend_Db_Table_Row_Abstract Le résultat
     */
    public function get($idChangement)
    {
        $dbEffectifDegagement = new Model_DbTable_EffectifDegagement();

        return $dbEffectifDegagement->find($idChangement)->current();
    }

    /**
     * Sauvegarde les modifications apportées aux messages d'alerte
     * par défaut.
     *
     * @param array $data Les données envoyés en post
     */
    public function save($data)
    {
        if (is_array($data)) {
            foreach ($data as $key => $message) {
                $idChangement = explode('_', $key)[0];
                $changement = $this->get($idChangement);
                $changement->MESSAGE_CHANGEMENT = $message;
                $changement->save();
            }
        }
    }

    /**
     *  Retourne le tableau de balises.
     *
     * @return string[][] Les balises définies dans cette classe
     *
     * @psalm-return array{{activitePrincipaleEtablissement}:array{description:string, model:string, champ:string}, {categorieEtablissement}:array{description:string, model:string, champ:string}, {etablissementAvis}:array{description:string, model:string, champ:string}, {etablissementLibelle}:array{description:string, model:string, champ:string}, {etablissementNumeroId}:array{description:string, model:string, champ:string}, {etablissementStatut}:array{description:string, model:string, champ:string}, {typePrincipalEtablissement}:array{description:string, model:string, champ:string}}
     */
    public function getBalises(): array
    {
        return self::BALISES;
    }

 
    /**
     * Convertit les balises dans le message avec les bonnes valeurs.
     *
     * @param string $message Le message a envoyer avec des balises
     *
     * @return string Le message convertit
     */
    public function convertMessage($message, $ets)
    {
        $params = array();
        foreach (self::BALISES as $balise => $content) {
            $replacementstr = '';
            if ($content['model'] === 'avis') {
                $replacementstr = $this->getAvis($ets);
            } elseif (array_key_exists($content['model'], $ets)
                && array_key_exists($content['champ'], $ets[$content['model']])) {
                $replacementstr = $ets[$content['model']][$content['champ']];
            }
            $params[$balise] = $replacementstr;
        }

        return strtr($message, $params);
    }

}
