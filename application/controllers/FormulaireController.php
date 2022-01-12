<?php

class FormulaireController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $this->_helper->layout->setLayout('menu_admin');

        $serviceFormulaire = new Service_Formulaire();

        $this->view->formulaires = $serviceFormulaire->getAllCapsulesRubrique();
    }
}