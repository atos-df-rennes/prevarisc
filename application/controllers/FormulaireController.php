<?php

class FormulaireController extends Zend_Controller_Action
{
    public function indexAction(): void
    {
        // Définition des layouts et scripts
        $this->_helper->layout->setLayout('menu_admin');
        $this->view->inlineScript()->appendFile('/js/formulaire/rubrique.js', 'text/javascript');

        // Définition des forms, models et services
        $form = new Form_CustomForm();
        $modelCapsuleRubrique = new Model_DbTable_CapsuleRubrique();
        $modelRubrique = new Model_DbTable_Rubrique();
        $serviceFormulaire = new Service_Formulaire();

        $capsulesRubriques = $serviceFormulaire->getAllCapsulesRubrique();

        // Récupération des rubriques pour chaque objet global
        // Le & devant $capsuleRubrique est nécessaire car on modifie une référence du tableau
        foreach ($capsulesRubriques as &$capsuleRubrique) {
            $capsuleRubrique['RUBRIQUES'] = $modelRubrique->getRubriquesByCapsuleRubrique($capsuleRubrique['NOM_INTERNE']);
        }

        // Assignation des variables à la vue
        $this->view->assign('form', $form);
        $this->view->assign('formulaires', $capsulesRubriques);

        // Sauvegarde des rubriques ajoutées
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

    public function deleteAction(): void
    {
        $modelRubrique = new Model_DbTable_Rubrique();
        $rubriqueId = intval($this->getParam('rubrique'));

        $rubrique = $modelRubrique->find($rubriqueId)->current();
        $rubrique->delete();

        $this->_helper->redirector('index');
    }
}