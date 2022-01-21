<?php

class FormulaireController extends Zend_Controller_Action
{
    public function indexAction(): void
    {
        // Définition des layouts et scripts
        $this->_helper->layout->setLayout('menu_admin');
        $this->view->inlineScript()->appendFile('/js/formulaire/capsule-rubrique.js', 'text/javascript');

        // Définition des forms, models et services
        $form = new Form_CustomForm();
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
    }

    public function addRubriqueAction(): void
    {
        $modelCapsuleRubrique = new Model_DbTable_CapsuleRubrique();
        $modelRubrique = new Model_DbTable_Rubrique();

        // Sauvegarde des rubriques ajoutées
        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = $request->getPost();

            $idCapsuleRubriqueArray = $modelCapsuleRubrique->getCapsuleRubriqueIdByName($post['capsule_rubrique']);
            $idCapsuleRubrique = $idCapsuleRubriqueArray['ID_CAPSULERUBRIQUE'];

            $idRubrique = $modelRubrique->insert(array(
                'NOM' => $post['nom_rubrique'],
                'DEFAULT_DISPLAY' => intval($post['afficher_rubrique']),
                'ID_CAPSULERUBRIQUE' => $idCapsuleRubrique
            ));

            echo intval($idRubrique);
        }
    }

    public function editRubriqueAction(): void
    {
        $this->_helper->layout->setLayout('menu_admin');
        $this->view->inlineScript()->appendFile('/js/formulaire/rubrique.js', 'text/javascript');

        $fieldForm = new Form_CustomFormField();

        $modelRubrique = new Model_DbTable_Rubrique();
        $modelChamp = new Model_DbTable_Champ();
        $modelListeTypeChampRubrique = new Model_DbTable_ListeTypeChampRubrique();
        $modelChampValeurListe = new Model_DbTable_ChampValeurListe();

        $serviceFormulaire = new Service_Formulaire();

        $idRubrique = intval($this->getParam('rubrique'));
        $rubrique = $modelRubrique->find($idRubrique)->current();

        $champs = $modelChamp->getChampsByRubrique($rubrique['ID_RUBRIQUE']);
        foreach ($champs as &$champ) {
            if ($champ['TYPE'] === 'Liste') {
                $champ['VALEURS'] = $modelChampValeurListe->getValeurListeByChamp($champ['ID_CHAMP']);
            }
        }

        $this->view->assign('fieldForm', $fieldForm);
        $this->view->assign('rubrique', $rubrique);
        $this->view->assign('champs', $champs);

        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $post = $request->getPost();

                $rubrique->NOM = $post['nom_rubrique'];
                $rubrique->DEFAULT_DISPLAY = $post['afficher_rubrique'];
                $rubrique->save();

                $this->_helper->redirector('index');
            } catch (Exception $e) {
                $this->_helper->flashMessenger(array('context' => 'error', 'title' => 'Erreur lors de la sauvegarde', 'message' => 'La rubrique n\'a pas été modifiée. Veuillez rééssayez. ('.$e->getMessage().')'));
            }
        }
    }

    public function addChampAction(): void
    {
        $modelRubrique = new Model_DbTable_Rubrique();
        $modelChamp = new Model_DbTable_Champ();
        $modelListeTypeChampRubrique = new Model_DbTable_ListeTypeChampRubrique();
        $modelChampValeurListe = new Model_DbTable_ChampValeurListe();

        $idRubrique = intval($this->getParam('rubrique'));
        $rubrique = $modelRubrique->find($idRubrique)->current();

        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = $request->getPost();

            $idTypeChamp = intval($post['type_champ']);
            $idListe = $modelListeTypeChampRubrique->getIdTypeChampByName('Liste')['ID_TYPECHAMP'];

            $idChamp = $modelChamp->insert(array(
                'NOM' => $post['nom_champ'],
                'ID_TYPECHAMP' => $idTypeChamp,
                'ID_RUBRIQUE' => $rubrique['ID_RUBRIQUE']
            ));
            
            if ($idTypeChamp === $idListe) {
                // On récupère les valeurs de la liste séparément des autres champs
                $listValueArray = array_filter($post, function($key) {
                    return strpos($key, 'valeur-') === 0;
                }, ARRAY_FILTER_USE_KEY);

                foreach ($listValueArray as $listValue) {
                    $modelChampValeurListe->insert(array(
                        'VALEUR' => $listValue,
                        'ID_CHAMP' => $idChamp
                    ));
                }
            }

            $insertedRowAsArray = $modelChamp->getChampAndJoins($idChamp);
            echo json_encode($insertedRowAsArray);
        }
    }

    public function editChampAction(): void
    {
        $this->_helper->layout->setLayout('menu_admin');
        $this->view->inlineScript()->appendFile('/js/formulaire/champ.js', 'text/javascript');

        $modelChamp = new Model_DbTable_Champ();
        $modelRubrique = new Model_DbTable_Rubrique();
        $modelListeTypeChampRubrique = new Model_DbTable_ListeTypeChampRubrique();
        $modelChampValeurListe = new Model_DbTable_ChampValeurListe();

        $serviceFormulaire = new Service_Formulaire();

        $idChamp = intval($this->getParam('champ'));
        $champ = $modelChamp->find($idChamp)->current();
        
        $idListe = $modelListeTypeChampRubrique->getIdTypeChampByName('Liste')['ID_TYPECHAMP'];
        if ($champ['ID_TYPECHAMP'] === $idListe) {
            $valeursChamp = $modelChampValeurListe->getValeurListeByChamp($champ['ID_CHAMP']);
            $this->view->assign('valeursChamp', $valeursChamp);
        }

        $rubrique = $modelRubrique->find($champ['ID_RUBRIQUE'])->current();

        $listeTypeChampRubrique = $serviceFormulaire->getAllListeTypeChampRubrique();

        $this->view->assign('champ', $champ);
        $this->view->assign('rubrique', $rubrique);
        $this->view->assign('listeTypeChampRubrique', $listeTypeChampRubrique);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = $request->getPost();

            // Modification de valeur
            // On récupère les valeurs de la liste séparément des autres champs
            $listValueArray = array_filter($post, function($key) {
                return strpos($key, 'valeur-champ-') === 0;
            }, ARRAY_FILTER_USE_KEY);
            
            foreach ($listValueArray as $key => $value) {
                $explodedKey = explode('-', $key);
                $idValue = intval(end($explodedKey));

                $valueField = $modelChampValeurListe->find($idValue)->current();
                $valueField->VALEUR = $value;
                $valueField->save();
            }

            // Ajout de valeur
            // On récupère les valeurs de la liste séparément des autres champs
            $listValueArray = array_filter($post, function($key) {
                return strpos($key, 'valeur-') === 0;
            }, ARRAY_FILTER_USE_KEY);

            foreach ($listValueArray as $listValue) {
                $modelChampValeurListe->insert(array(
                    'VALEUR' => $listValue,
                    'ID_CHAMP' => $champ['ID_CHAMP']
                ));
            }
            
            $champ->NOM = $post['nom_champ'];
            $champ->save();
            $this->_helper->redirector('edit-rubrique', null, null, array('rubrique' => $rubrique['ID_RUBRIQUE']));
        }
    }

    public function deleteRubriqueAction(): void
    {
        $modelRubrique = new Model_DbTable_Rubrique();
        $idRubrique = intval($this->getParam('rubrique'));

        $rubrique = $modelRubrique->find($idRubrique)->current();
        $rubrique->delete();

        $this->_helper->redirector('index');
    }

    public function deleteChampAction(): void
    {
        $modelChamp = new Model_DbTable_Champ();
        $idChamp = intval($this->getParam('champ'));
        
        $champ = $modelChamp->find($idChamp)->current();
        $idRubrique = $champ['ID_RUBRIQUE'];
        $champ->delete();

        $this->_helper->redirector('edit-rubrique', null, null, array('rubrique' => $idRubrique));
    }

    public function deleteValeurListeAction(): void
    {
        $modelChampValeurListe = new Model_DbTable_ChampValeurListe();

        $idValeurListe = $this->getParam('liste');
        $valeurListe = $modelChampValeurListe->find($idValeurListe)->current();
        $valeurListe->delete();
    }
}