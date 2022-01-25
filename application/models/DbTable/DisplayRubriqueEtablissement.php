<?php

class Model_DbTable_DisplayRubriqueEtablissement extends Zend_Db_Table_Abstract
{
    protected $_name = 'displayrubriqueetablissement'; // Nom de la base
    protected $_primary = ['ID_ETABLISSEMENT', 'ID_RUBRIQUE']; // Clé primaire
}