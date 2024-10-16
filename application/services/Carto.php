<?php

class Service_Carto
{
    public $cache;

    /**
     * @var mixed|Model_DbTable_CoucheCarto
     */
    public $repository;

    /**
     * Initialisation des ressources exterieures.
     */
    public function __construct()
    {
        $this->cache = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('cache');
        $this->repository = new Model_DbTable_CoucheCarto();
    }

    /**
     * Récupération de toutes les couches cartographiques.
     *
     * @return array
     */
    public function getAll()
    {
        if (($couches_carto = unserialize($this->cache->load('couches_cartographiques'))) === false) {
            // On récupère l'ensemble des couches
            $couches_carto = $this->repository->getAll();

            // On stocke en cache
            $this->cache->save(serialize($couches_carto));
        }

        return $couches_carto;
    }

    /**
     * Récupération d'une couche cartographique.
     *
     * @param int $id_couche_cartographique
     *
     * @return array
     */
    public function findById($id_couche_cartographique)
    {
        return $this->repository->find($id_couche_cartographique)->current()->toArray();
    }

    /**
     * Édition d'une couche cartographique.
     *
     * @param array $data
     * @param int   $id_couche_cartographique Optionnel
     */
    public function save($data, $id_couche_cartographique = null): void
    {
        $couche_cartographique = null == $id_couche_cartographique ? $this->repository->createRow() : $this->repository->find($id_couche_cartographique)->current();
        $couche_cartographique->setFromArray(array_intersect_key($data, $this->repository->info('metadata')))->save();

        $this->cache->remove('couches_cartographiques');
    }

    /**
     * Suppression d'une couche cartographique.
     *
     * @param int $id_couche_cartographique
     */
    public function delete($id_couche_cartographique): void
    {
        $this->repository->delete('ID_COUCHECARTO = '.$id_couche_cartographique);

        $this->cache->remove('couches_cartographiques');
    }
}
