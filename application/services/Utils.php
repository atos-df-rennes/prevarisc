<?php

class Service_Utils {

    public static function getPjPath ($pjData)
    {
        if (array_key_exists('ID_PLATAU', $pjData)) {
            return
                implode(DS, [
                    REAL_DATA_PATH,
                    'uploads',
                    'pieces-jointes',
                    $pjData['ID_PIECEJOINTE'].$pjData['EXTENSION_PIECEJOINTE'],
                ]);

        }

        $store = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('dataStore');
        return $store->getFilePath($pjData, 'dossier', $pjData['ID_DOSSIER']);

    }

}
