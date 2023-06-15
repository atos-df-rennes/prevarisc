<?php

class Model_DbTable_EtablissementInformationsPreventionniste extends Zend_Db_Table_Abstract
{
    protected $_name = 'etablissementinformationspreventionniste'; // Nom de la base
    protected $_primary = ['ID_ETABLISSEMENTINFORMATIONS', 'ID_UTILISATEUR']; // Cl� primaire

    public function getEtablissementsPreventioniste():array
    {
        $select = $this->select('ei.ID_ETABLISSEMENTINFORMATIONS','u.ID_UTILISATEUR')->setIntegrityCheck(false)
        ->from(['etablissement' => 'e'])
        ->leftjoin(['ea' => 'etablissementadresse'], 'ea.ID_ETABLISSEMENT = e.ID_ETABLISSEMENT')
        ->joinInner(['ei' =>'etablissementinformations'], 'e.ID_ETABLISSEMENT = ei.ID_ETABLISSEMENT AND ei.DATE_ETABLISSEMENTINFORMATIONS = ( SELECT MAX(etablissementinformations.DATE_ETABLISSEMENTINFORMATIONS) FROM etablissementinformations WHERE etablissementinformations.ID_ETABLISSEMENT = e.ID_ETABLISSEMENT )')
        ->leftjoin(['gc' => 'groupementcommune'], 'gc.NUMINSEE_COMMUNE = ea.NUMINSEE_COMMUNE')
        ->leftjoin(['g' => 'groupement'], 'g.ID_GROUPEMENT = gc.ID_GROUPEMENT')
        ->leftjoin(['gt' => 'groupementtype'], 'g.ID_GROUPEMENTTYPE = gt.ID_GROUPEMENTTYPE')
        ->leftjoin(['gp' => 'groupementpreventionniste'], 'gp.ID_GROUPEMENT = g.ID_GROUPEMENT')
        ->leftjoin(['u' => 'utilisateur'], 'u.ID_UTILISATEUR = gp.ID_UTILISATEUR')
        ->where('gt.LIBELLE_GROUPEMENTTYPE = "Secteur prévention" and ei.id_genre in (2)')
        ->group(1,2)
        ;
        return $this->fetchAll($select)->toArray();
    }
    public function getCellulesPreventioniste():array
    {
        $select = $this->select('ei.ID_ETABLISSEMENTINFORMATIONS','u.ID_UTILISATEUR')->setIntegrityCheck(false)
        ->from(['etablissement' => 'e'])
        ->leftjoin(['el' => 'etablissementlie'], 'e.ID_ETABLISSEMENT = el.ID_FILS_ETABLISSEMENT')
        ->leftjoin(['pere' =>'etablissement'], 'el.ID_ETABLISSEMENT = pere.ID_ETABLISSEMENT')
        ->leftjoin(['ea' => 'etablissementadresse'], 'ea.ID_ETABLISSEMENT = pere.ID_ETABLISSEMENT')
        ->joinInner(['ei' => 'etablissementinformations'], 'e.ID_ETABLISSEMENT = ei.ID_ETABLISSEMENT AND ei.DATE_ETABLISSEMENTINFORMATIONS = ( SELECT MAX(etablissementinformations.DATE_ETABLISSEMENTINFORMATIONS) FROM etablissementinformations WHERE etablissementinformations.ID_ETABLISSEMENT = e.ID_ETABLISSEMENT )')
        ->leftjoin(['gc' => 'groupementcommune'], 'gc.NUMINSEE_COMMUNE = ea.NUMINSEE_COMMUNE')
        ->leftjoin(['g' => 'groupement'], 'g.ID_GROUPEMENT = gc.ID_GROUPEMENT')
        ->leftjoin(['gt' => 'groupementtype'], 'g.ID_GROUPEMENTTYPE = gt.ID_GROUPEMENTTYPE')
        ->leftjoin(['gp' => 'groupementpreventionniste'], 'gp.ID_GROUPEMENT = g.ID_GROUPEMENT')
        ->leftjoin(['u' => 'utilisateur'], 'u.ID_UTILISATEUR = gp.ID_UTILISATEUR')
        ->where('gt.LIBELLE_GROUPEMENTTYPE = "Secteur prévention" and ei.id_genre in (2)')
        ->group(1,2)
        ;
        return $this->fetchAll($select)->toArray();
    }
    public function getSitesPreventioniste():array
    {
        $select = $this->select('ei.ID_ETABLISSEMENTINFORMATIONS','u.ID_UTILISATEUR')->setIntegrityCheck(false)
        ->from(['etablissement' => 'e'])
        ->leftjoin(['el' => 'etablissementlie'], 'e.ID_ETABLISSEMENT = el.ID_FILS_ETABLISSEMENT')
        ->leftjoin(['fils' =>'etablissement'], 'el.ID_FILS_ETABLISSEMENT = fils.ID_ETABLISSEMENT')
        ->leftjoin(['ea' => 'etablissementadresse'], 'ea.ID_ETABLISSEMENT = fils.ID_ETABLISSEMENT')
        ->joinInner(['ei' => 'etablissementinformations'], 'e.ID_ETABLISSEMENT = ei.ID_ETABLISSEMENT AND ei.DATE_ETABLISSEMENTINFORMATIONS = ( SELECT MAX(etablissementinformations.DATE_ETABLISSEMENTINFORMATIONS) FROM etablissementinformations WHERE etablissementinformations.ID_ETABLISSEMENT = e.ID_ETABLISSEMENT )')
        ->leftjoin(['gc' => 'groupementcommune'], 'gc.NUMINSEE_COMMUNE = ea.NUMINSEE_COMMUNE')
        ->leftjoin(['g' => 'groupement'], 'g.ID_GROUPEMENT = gc.ID_GROUPEMENT')
        ->leftjoin(['gt' => 'groupementtype'], 'g.ID_GROUPEMENTTYPE = gt.ID_GROUPEMENTTYPE')
        ->leftjoin(['gp' => 'groupementpreventionniste'], 'gp.ID_GROUPEMENT = g.ID_GROUPEMENT')
        ->leftjoin(['u' => 'utilisateur'], 'u.ID_UTILISATEUR = gp.ID_UTILISATEUR')
        ->where("gt.LIBELLE_GROUPEMENTTYPE = 'Secteur prévention' and ei.id_genre in (2)")
        ->group(1,2)
        ;
        return $this->fetchAll($select)->toArray();
    }
    public function deletePreventioniste():void
    {
        $this->delete('etablissementinformationspreventionniste');
    }
    public function addPreventioniste($valeur):void
    {
        $this->insert($valeur)
        //->into('etablissementinformationspreventionniste')
        //->values($valeur)
        ;
    }
}
