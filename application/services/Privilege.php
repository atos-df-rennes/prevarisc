<?php

class Service_Privilege {
    public function isAllowed(string $resource, string $privilege): bool
    {
        $cache = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('cache');

        return unserialize($cache->load('acl'))
            ->isAllowed(Zend_Auth::getInstance()->getIdentity()['group']['LIBELLE_GROUPE'], $resource, $privilege)
        ;
    }
}
