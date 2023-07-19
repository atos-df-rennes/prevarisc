<?php

class Service_Effectifdegagement
{
    /**
     * @var mixed|\Model_DbTable_EffectifDegagement
     */
    public $modelEffectifDegagement;

    /**
     * @var mixed|\Model_DbTable_DossierEffectifDegagement
     */
    public $modelEffectifDegagementDossier;

    /**
     * @var mixed|\Model_DbTable_EtablissementEffectifDegagement
     */
    public $modelEffectifDegagementEtablissement;

    /**
     * @var mixed|\Model_DbTable_Dossier
     */
    public $modelDossier;

    /**
     * @var mixed|\Model_DbTable_Etablissement
     */
    public $modelEtablissement;

    public function __construct()
    {
        $this->modelEffectifDegagement = new Model_DbTable_EffectifDegagement();
        $this->modelEffectifDegagementDossier = new Model_DbTable_DossierEffectifDegagement();
        $this->modelEffectifDegagementEtablissement = new Model_DbTable_EtablissementEffectifDegagement();

        $this->modelDossier = new Model_DbTable_Dossier();
        $this->modelEtablissement = new Model_DbTable_Etablissement();
    }

    public function get(int $idEffectifDegagement)
    {
        return $this->modelEffectifDegagement->find($idEffectifDegagement)->current();
    }

    public function addRowDossierEffectifDegagement(int $idDossier)
    {
        $rowEffectifDegagement = $this->modelEffectifDegagement->createRow();
        $rowEffectifDegagement->save();

        $this->modelEffectifDegagementDossier->insert([
            'ID_DOSSIER' => $idDossier,
            'ID_EFFECTIF_DEGAGEMENT' => $rowEffectifDegagement->ID_EFFECTIF_DEGAGEMENT,
        ]);

        return $rowEffectifDegagement->ID_EFFECTIF_DEGAGEMENT;
    }

    public function addRowEtablissementEffectifDegagement(int $idEtablissement)
    {
        $rowEffectifDegagement = $this->modelEffectifDegagement->createRow();
        $rowEffectifDegagement->save();

        $this->modelEffectifDegagementEtablissement->insert([
            'ID_ETABLISSEMENT' => $idEtablissement,
            'ID_EFFECTIF_DEGAGEMENT' => $rowEffectifDegagement->ID_EFFECTIF_DEGAGEMENT,
        ]);

        return $rowEffectifDegagement->ID_EFFECTIF_DEGAGEMENT;
    }

    public function saveFromDossier(int $idDossier, array $data)
    {
        $idEffectifDegagement = $this->modelDossier->getIdEffectifDegagement($idDossier)->ID_EFFECTIF_DEGAGEMENT;

        if (null === $idEffectifDegagement) {
            $idEffectifDegagement = $this->addRowDossierEffectifDegagement($idDossier);
        }

        $this->save($idEffectifDegagement, $data);
    }

    public function saveFromEtablissement(int $idEtablissement, array $data)
    {
        $idEffectifDegagement = $this->modelEtablissement->getIdEffectifDegagement($idEtablissement)->ID_EFFECTIF_DEGAGEMENT;

        if (null === $idEffectifDegagement) {
            $idEffectifDegagement = $this->addRowEtablissementEffectifDegagement($idEtablissement);
        }

        $this->save($idEffectifDegagement, $data);
    }

    public function save(int $idEffectifDegagement, array $data)
    {
        if (is_array($data)) {
            $newValue = $this->get($idEffectifDegagement);

            foreach ($data as $key => $newAttrValue) {
                $newValue->{$key} = $newAttrValue;
            }

            $newValue->save();
        }
    }
}
