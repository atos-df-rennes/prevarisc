<?php

class FormulaireController extends Zend_Controller_Action
{
    public function indexAction(): void
    {
        $this->_helper->layout->setLayout('menu_admin');
        $this->view->inlineScript()->appendFile('/js/formulaire/rubrique.js', 'text/javascript');

        $form = new Form_CustomForm();
        $serviceFormulaire = new Service_Formulaire();
        $modelCapsuleRubrique = new Model_DbTable_CapsuleRubrique();
        $modelRubrique = new Model_DbTable_Rubrique();

        $this->view->assign('form', $form);
        $this->view->assign('formulaires', $serviceFormulaire->getAllCapsulesRubrique());

        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $post = $request->getPost();
                $capsuleRubriqueIdArray = $modelCapsuleRubrique->getCapsuleRubriqueIdByName($post['capsule_rubrique']);
                $capsuleRubriqueId = $capsuleRubriqueIdArray['ID_CAPSULERUBRIQUE'];

                $modelRubrique->insert(array(
                    'NOM' => $post['nom_rubrique'],
                    'DEFAULT_DISPLAY' => intval($post['afficher_rubrique']),
                    'ID_CAPSULERUBRIQUE' => $capsuleRubriqueId
                ));
            } catch (Exception $e) {
                $this->_helper->flashMessenger(array('context' => 'error', 'title' => 'Erreur lors de la sauvegarde', 'message' => 'Les rubriques n\'ont pas été ajoutées. Veuillez rééssayez. ('.$e->getMessage().')'));
            }
        }
    }
}