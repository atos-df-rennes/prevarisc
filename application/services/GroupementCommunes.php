<?php

class Service_GroupementCommunes
{
    /**
     * Récupération de tous les groupements.
     *
     * @param int numinsee Optionnel
     * @param null|mixed $num_insee
     *
     * @return array
     */
    public function findAll($num_insee = null)
    {
        $model_groupement = new Model_DbTable_Groupement();

        if (null !== $num_insee) {
            return $model_groupement->getGroupementParVille($num_insee);
        }

        $select = $model_groupement->select()->order('LIBELLE_GROUPEMENT ASC');

        return $model_groupement->fetchAll($select)->toArray();
    }

    public function findGroupementAndGroupementType($num_insee = null)
    {
        $model_groupement = new Model_DbTable_Groupement();

        if (null !== $num_insee) {
            return $model_groupement->getGroupementParVille($num_insee);
        }

        return $model_groupement->getAllWithTypes();
    }

    public function findGroupementForEtablissement(array $ids_etablissement = [])
    {
        $model_groupement = new Model_DbTable_Groupement();

        return $model_groupement->getByEtablissement($ids_etablissement);
    }

    public function findGroupementForGroupementType(array $types_groupement = [])
    {
        $model_groupement = new Model_DbTable_Groupement();

        return $model_groupement->getByGroupementType($types_groupement);
    }

    public function reaffectationPreventioniste(){
        $model_groupement = new Model_DbTable_EtablissementInformationsPreventionniste();
        $model_groupement->deletePreventioniste();
        $valeur1 = $model_groupement->getEtablissementsPreventioniste();
        $model_groupement->addPreventioniste($valeur1);
        $valeur2 = $model_groupement->getCellulesPreventioniste();
        $model_groupement->addPreventioniste( $valeur2);
        $valeur3 = $model_groupement->getSitesPreventioniste();
        $model_groupement->addPreventioniste( $valeur3);
    }
}
