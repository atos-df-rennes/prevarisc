<?php

class Model_DbTable_DisplayRubriqueDossier extends Zend_Db_Table_Abstract
{
    protected $_name = 'displayrubriqueDossier'; // Nom de la base
    protected $_primary = ['ID_DOSSIER', 'ID_RUBRIQUE']; // Clé primaire
}
