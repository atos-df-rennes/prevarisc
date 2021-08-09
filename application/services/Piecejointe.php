<?php

class Service_PieceJointe
{
     
    /**
     * Recherche des piece jointes.
     *
     * @param int    $parent       Id d'une piéce jointe parent
     *
     * @return array
     */
    public function piecejointe($parent)
    {
        // Récupération de la ressource cache à partir du bootstrap
        $cache = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('cacheSearch');

        // Identifiant de la recherche
        $search_id = 'search_piecejointes_'.md5(serialize(func_get_args()));

        if (($results = unserialize($cache->load($search_id))) === false) {

            // Création de l'objet recherche
            $select = new Zend_Db_Select(Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('db'));

            // Requête principale
            $select->from(
                array('p' => 'piecejointe'),
                array('ID_PIECEJOINTE', 'NOM_PIECEJOINTE', 'EXTENSION_PIECEJOINTE', 'DESCRIPTION_PIECEJOINTE', 'DATE_PIECEJOINTE')
                )
                ->join('piecejointelie', 'p.ID_PIECEJOINTE = piecejointelie.ID_FILS_PIECEJOINTE', array())
                ->where('piecejointelie.ID_PIECEJOINTE = ?', $parent)
                ;

            // Construction du résultat
            $rows_counter = new Zend_Paginator_Adapter_DbSelect($select);
            $results = array(
            'results' => $select->query()->fetchAll(),
            'search_metadata' => array(
                'search_id' => $search_id,
                'count' => count($rows_counter),
                ),
            );
        }

        return $results;
    }
}
