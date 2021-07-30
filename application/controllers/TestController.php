<?php

class TestController extends Zend_Controller_Action
{
    // Modèles

    public function indexAction()
    {
        $DBused = new Model_DbTable_PieceJointe();
        $listePj = $DBused->affichagePieceJointe(null, 'piecejointe.ID_PIECEJOINTE ', 25851);
        $this->view->data = $listePj;

    }
}

