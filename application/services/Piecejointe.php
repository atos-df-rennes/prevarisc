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
    public function piecejointe($parent = null)
    {
        // Récupération de la ressource cache à partir du bootstrap
        $cache = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('cacheSearch');

        // Identifiant de la recherche
        $search_id = 'search_piecejointes_'.md5(serialize(func_get_args()));

        if (($results = unserialize($cache->load($search_id))) === false) {

            // Création de l'objet recherche
            $select = new Zend_Db_Select(Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('db'));

            // Requête principale
            $select->from(array('p' => 'piecejointe'))
                ->columns(array(
                    'NB_ENFANTS' => new Zend_Db_Expr('( SELECT COUNT(piecejointelie.ID_FILS_PIECEJOINTE)
                        FROM piecejointe
                        INNER JOIN piecejointelie ON piecejointe.ID_PIECEJOINTE = piecejointelie.ID_PIECEJOINTE
                        WHERE piecejointe.ID_PIECEJOINTE = p.ID_PIECEJOINTE)')
                        ));
            // Critères : parent
            if ($parent !== null) {
                $select->where($parent == 0 ? 'piecejointelie.ID_PIECEJOINTE IS NULL' : 'piecejointelie.ID_PIECEJOINTE = ?', $parent);
            }
            // Gestion des pages et du count
            $select->limitPage($page, $count > 100 ? 100 : $count);

            // Construction du résultat
            $rows_counter = new Zend_Paginator_Adapter_DbSelect($select);
            $results = array(
            'results' => $select->query()->fetchAll(),
            'search_metadata' => array(
                'search_id' => $search_id,
                'current_page' => $page,
                'count' => count($rows_counter),
                ),
            );

        }

        return $results;
    }
}




