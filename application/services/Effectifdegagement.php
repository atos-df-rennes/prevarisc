<?php

class Service_Effectifdegagement
{
    

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
     *  Retourne un changement via son Id précisé en argument.
     *
     * @param int $idChangement L'id du changement à retourner
     *
     * @return Zend_Db_Table_Row_Abstract Le résultat
     */
    public function getByIDDossier($idDossier)
    {
        $dbEffectifDegagement = new Model_DbTable_EffectifDegagement();
        $res = null; 
        foreach ($dbEffectifDegagement->getIDEffectifDegagementByIDDossier($idDossier) as $row) {
            foreach ($row as $key => $value) {
                if($key == "ID_REF_EFFECTIF_DEGAGEMENT"){
                    $res = $value;
                }
            }
        }
        return $res;
    }




        /**
     *  Retourne un changement via son Id précisé en argument.
     *
     * @param int $idChangement L'id du changement à retourner
     *
     * @return Zend_Db_Table_Row_Abstract Le résultat
     */
    public function getByIDEtablissement($idEtablissement)
    {
        $dbEffectifDegagement = new Model_DbTable_EffectifDegagement();
        $res = null; 
        foreach ($dbEffectifDegagement->getEffectifDegagementByIDEtablissement($idEtablissement) as $row) {
            foreach ($row as $key => $value) {
                if($key == "ID_REF_EFFECTIF_DEGAGEMENT"){
                    $res = $value;
                }
            }
        }
        return $res;
    }






    /**
     * ajoute une ligne a la table dossierEffectifDegagement en retournant l identifiant
     */
    public function addRowDossierEffectifDegagement($idDossier){
            $modelEffectifDegagement = new Model_DbTable_EffectifDegagement();
            $modelDossierEffectifDegagement = new Model_DbTable_DossierEffectifDegagement();

            $rowEffectifDegagement = $modelEffectifDegagement->createRow();
            $rowEffectifDegagement->save();

            $rowDossierEff = $modelDossierEffectifDegagement->createRow();
            $rowDossierEff->ID_REF_DOSSIER = $idDossier;
            $rowDossierEff->ID_REF_EFFECTIF_DEGAGEMENT = $rowEffectifDegagement->ID_EFFECTIF_DEGAGEMENT;
            $rowDossierEff->save();

            return $rowEffectifDegagement->ID_EFFECTIF_DEGAGEMENT;
    }



    /**
     * ajoute une ligne a la table dossierEffectifDegagement en retournant l identifiant
     */
    public function addRowEtablissementEffectifDegagement($idEtablissement){
            $modelEffectifDegagement = new Model_DbTable_EffectifDegagement();
            $modelEtablissementEffectifDegagement = new Model_DbTable_EtablissementEffectifDegagement();

            $rowEffectifDegagement = $modelEffectifDegagement->createRow();
            $rowEffectifDegagement->save();

            $rowEtablissementEff = $modelEtablissementEffectifDegagement->createRow();
            $rowEtablissementEff->ID_REF_ETABLISSEMENT = $idEtablissement;
            $rowEtablissementEff->ID_REF_EFFECTIF_DEGAGEMENT = $rowEffectifDegagement->ID_EFFECTIF_DEGAGEMENT;
            $rowEtablissementEff->save();

            return $rowEffectifDegagement->ID_EFFECTIF_DEGAGEMENT;
    }


    public function saveFromDossier($idDossier,$data)
    {
        $idEffecifDegagement = $this->getByIDDossier($idDossier); 
        if($idEffecifDegagement == null){
            $idEffecifDegagement = $this->addRowDossierEffectifDegagement($idDossier);
        }
        $this->save($idEffecifDegagement,$data);
    }

    public function saveFromEtablissement($idEtablissement,$data)
    {
        $idEffecifDegagement = $this->getByIDEtablissement($idEtablissement); 
        if($idEffecifDegagement == null){
            $idEffecifDegagement = $this->addRowEtablissementEffectifDegagement($idEtablissement);
        }
        $this->save($idEffecifDegagement,$data);
    }

    /**
     * Sauvegarde les modifications apportées aux messages d'alerte
     * par défaut.
     *  @param id ==> identifiant de l effectif degagement
     * @param array $data Les données envoyés en post
     */
    public function save($idEffectifDegagement,$data)
    {
        if (is_array($data)) {
            $newValue = $this->get($idEffectifDegagement);
            foreach ($data as $key=> $newAttrValue ) {
                switch ($key) {
                    case 'DESCRIPTION_EFFECTIF':
                        $newValue->DESCRIPTION_EFFECTIF = $newAttrValue;
                        break;
                    case 'DESCRIPTION_DEGAGEMENT':
                        $newValue->DESCRIPTION_DEGAGEMENT = $newAttrValue;
                    break;   
                }
            }
            $newValue->save();
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
