<?php

class Model_DbTable_GroupementContact extends Zend_Db_Table_Abstract
{
    protected $_name = 'groupementcontact'; // Nom de la base
    protected $_primary = ['ID_GROUPEMENT', 'ID_UTILISATEURINFORMATIONS']; // Clé primaire
}
