<?php

class FormulaireController extends Zend_Controller_Action
{
    /// Gestion des rubriques ///
    public function indexAction(): void
    {
        // Définition des layouts et scripts
        $this->_helper->layout->setLayout('menu_admin');
        $this->view->inlineScript()->appendFile('/js/formulaire/capsule-rubrique.js', 'text/javascript');

        // Définition des forms, models et services
        $form = new Form_CustomForm();
        $modelCapsuleRubrique = new Model_DbTable_CapsuleRubrique();
        $modelRubrique = new Model_DbTable_Rubrique();
        $serviceFormulaire = new Service_Formulaire();

        $capsulesRubriques = $serviceFormulaire->getAllCapsuleRubrique();

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

    public function editAction(): void
    {
        $this->_helper->layout->setLayout('menu_admin');
        $this->view->inlineScript()->appendFile('/js/formulaire/rubrique.js', 'text/javascript');

        $fieldForm = new Form_CustomFormField();
        $modelRubrique = new Model_DbTable_Rubrique();
        $modelChamp = new Model_DbTable_Champ();
        $serviceFormulaire = new Service_Formulaire();

        $rubriqueId = intval($this->getParam('rubrique'));
        $rubrique = $modelRubrique->find($rubriqueId)->current();

        $champs = $modelChamp->getChampsByRubrique($rubrique['ID_RUBRIQUE']);

        $this->view->assign('fieldForm', $fieldForm);
        $this->view->assign('rubrique', $rubrique);
        $this->view->assign('champs', $champs);

        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $post = $request->getPost();

                // FIXME Voir pour séparer les actions et faire une action spécifique pour l'ajout de champ
                // Cas de l'ajout d'un champ à la rubrique
                if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                    $modelChamp->insert(array(
                        'NOM' => $post['nom_champ'],
                        'ID_TYPECHAMP' => intval($post['type_champ']),
                        'ID_RUBRIQUE' => $rubrique['ID_RUBRIQUE']
                    ));
                } else {
                    // Cas de modification des informations de la rubrique
                    $rubrique->NOM = $post['nom_rubrique'];
                    $rubrique->DEFAULT_DISPLAY = $post['afficher_rubrique'];
                    $rubrique->save();

                    $this->_helper->redirector('index');
                }
            } catch (Exception $e) {
                $this->_helper->flashMessenger(array('context' => 'error', 'title' => 'Erreur lors de la sauvegarde', 'message' => 'La rubrique n\'a pas été modifiée. Veuillez rééssayez. ('.$e->getMessage().')'));
            }
        }
    }

    public function editChampAction(): void
    {
        $this->_helper->layout->setLayout('menu_admin');

        $modelChamp = new Model_DbTable_Champ();
        $modelRubrique = new Model_DbTable_Rubrique();
        $serviceFormulaire = new Service_Formulaire();

        $champId = intval($this->getParam('champ'));
        $champ = $modelChamp->find($champId)->current();

        $rubrique = $modelRubrique->find($champ['ID_RUBRIQUE'])->current();

        $listeTypeChampRubrique = $serviceFormulaire->getAllListeTypeChampRubrique();

        $this->view->assign('champ', $champ);
        $this->view->assign('rubrique', $rubrique);
        $this->view->assign('listeTypeChampRubrique', $listeTypeChampRubrique);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = $request->getPost();
            
            $champ->NOM = $post['nom_champ'];
            $champ->save();
            $this->_helper->redirector('edit', null, null, array('rubrique' => $rubrique['ID_RUBRIQUE']));
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

    public function deleteChampAction(): void
    {
        $modelChamp = new Model_DbTable_Champ();
        $champId = intval($this->getParam('champ'));
        
        $champ = $modelChamp->find($champId)->current();
        $rubriqueId = $champ['ID_RUBRIQUE'];
        $champ->delete();

        $this->_helper->redirector('edit', null, null, array('rubrique' => $rubriqueId));
    }
}