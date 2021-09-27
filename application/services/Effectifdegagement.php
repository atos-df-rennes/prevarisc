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
     * Sauvegarde les modifications apportées aux messages d'alerte
     * par défaut.
     *
     * @param array $data Les données envoyés en post
     */
    public function save($data)
    {
        if (is_array($data)) {
            //echo(explode('_',$data));

            foreach ($data as $key => $message) {
                //echo(explode('_',$key)[1]);
                $newValue = $this->get(explode('_',$key)[0]);


                /* 
                echo "<br>".$newValue->EFFECTIF;
                echo "<br>".$newValue->DEGAGEMENT;
                */
                switch (explode('_',$key)[1]){
                    case "EFFECTIF": 
                        $newValue->EFFECTIF = $message;
                        break;
                    case "DEGAGEMENT": 
                        $newValue->DEGAGEMENT = $message;
                        break;
                }

                /*echo "<br>".$newValue->EFFECTIF;
                echo "<br>".$newValue->DEGAGEMENT;
                */ 
                $newValue->save();

                /*
                $idChangement = explode('_', $key)[0];
                $changement = $this->get($idChangement);
                $changement->MESSAGE_CHANGEMENT = $message;
                $changement->save();
                */
            }
            /*
            foreach ($data as $key => $message) {
                $idChangement = explode('_', $key)[0];
                $changement = $this->get($idChangement);
                $changement->MESSAGE_CHANGEMENT = $message;
                $changement->save();
            }
            */
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
