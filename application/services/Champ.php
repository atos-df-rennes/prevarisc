<?php

class Service_Champ
{
    public function isTableau(Zend_Db_Table_Row_Abstract $champ): bool
    {
        return filter_var($champ['tableau'], FILTER_VALIDATE_BOOL);
    }
}
