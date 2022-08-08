<?php

class FormulaireController extends Zend_Controller_Action
{
    public function init()
    {
        $this->modelChamp = new Model_DbTable_Champ();
        $this->modelChampValeurListe = new Model_DbTable_ChampValeurListe();
        $this->modelListeTypeChampRubrique = new Model_DbTable_ListeTypeChampRubrique();
        $this->modelRubrique = new Model_DbTable_Rubrique();

        $this->serviceFormulaire = new Service_Formulaire();
    }

    public function indexAction(): void
    {
        // Définition des layouts et scripts
        $this->_helper->layout->setLayout('menu_admin');

        /** @var Zend_View_Helper_InlineScript */
        $viewInlineScript = $this->view;
        $viewInlineScript->inlineScript()->appendFile('/js/formulaire/capsule-rubrique.js', 'text/javascript');
        $viewInlineScript->inlineScript()->appendFile('/js/formulaire/ordonnancement/ordonnancement.js', 'text/javascript');
        $viewInlineScript->inlineScript()->appendFile('/js/formulaire/ordonnancement/Sortable.js', 'text/javascript');

        /** @var Zend_View_Helper_HeadLink */
        $viewHeadLink = $this->view;
        $viewHeadLink->headLink()->appendStylesheet('/css/formulaire/formulaire.css', 'all');

        $form = new Form_CustomForm();

        $capsulesRubriques = $this->serviceFormulaire->getAllCapsuleRubrique();

        // Récupération des rubriques pour chaque objet global
        // Le & devant $capsuleRubrique est nécessaire car on modifie une référence du tableau
        foreach ($capsulesRubriques as &$capsuleRubrique) {
            $capsuleRubrique['RUBRIQUES'] = $this->modelRubrique->getRubriquesByCapsuleRubrique($capsuleRubrique['NOM_INTERNE']);
        }

        // Assignation des variables à la vue
        $this->view->assign('form', $form);
        $this->view->assign('formulaires', $capsulesRubriques);
    }

    public function addRubriqueAction(): void
    {
        $this->_helper->viewRenderer->setNoRender(true);

        // Sauvegarde des rubriques ajoutées
        /** @var Zend_Controller_Request_Http */
        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = $request->getPost();
            $post['idx'] = $this->modelRubrique->getNbRubriqueOfDesc($post['capsule_rubrique']);

            $idRubrique = $this->serviceFormulaire->insertRubrique($post);
            $insertedRowAsArray = $this->modelRubrique->find($idRubrique)->current()->toArray();

            echo json_encode($insertedRowAsArray);
        }
    }

    public function editRubriqueAction(): void
    {
        $this->_helper->layout->setLayout('menu_admin');

        /** @var Zend_View_Helper_InlineScript */
        $viewInlineScript = $this->view;
        $viewInlineScript->inlineScript()->appendFile('/js/formulaire/rubrique.js', 'text/javascript');
        $viewInlineScript->inlineScript()->appendFile('/js/formulaire/ordonnancement/ordonnancement.js', 'text/javascript');
        $viewInlineScript->inlineScript()->appendFile('/js/formulaire/ordonnancement/Sortable.js', 'text/javascript');

        /** @var Zend_View_Helper_HeadLink */
        $viewHeadLink = $this->view;
        $viewHeadLink->headLink()->appendStylesheet('/css/formulaire/formulaire.css', 'all');

        $fieldForm = new Form_CustomFormField();

        $idRubrique = intval($this->getParam('rubrique'));
        $rubrique = $this->modelRubrique->find($idRubrique)->current();

        $champs = $this->modelChamp->getChampsByRubrique($rubrique['ID_RUBRIQUE']);
        foreach ($champs as &$champ) {
            if ('Liste' === $champ['TYPE']) {
                $champ['VALEURS'] = $this->modelChampValeurListe->getValeurListeByChamp($champ['ID_CHAMP']);
            }
            if ('Parent' === $champ['TYPE']) {
                $champ['LIST_CHAMP'] = $this->modelChamp->getChampsFromParent($champ['ID_CHAMP']);
            }
        }

        $this->view->assign('fieldForm', $fieldForm);
        $this->view->assign('rubrique', $rubrique);
        $this->view->assign('champs', $champs);

        /** @var Zend_Controller_Request_Http */
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $post = $request->getPost();

                $rubrique->NOM = $post['nom_rubrique'];
                $rubrique->DEFAULT_DISPLAY = $post['afficher_rubrique'];
                $rubrique->save();

                $this->_helper->redirector('index');
            } catch (Exception $e) {
                $this->_helper->flashMessenger(['context' => 'error', 'title' => 'Erreur lors de la sauvegarde', 'message' => 'La rubrique n\'a pas été modifiée. Veuillez rééssayez. ('.$e->getMessage().')']);
            }
        }
    }

    public function deleteRubriqueAction(): void
    {
        $idRubrique = intval($this->getParam('rubrique'));

        $rubrique = $this->modelRubrique->find($idRubrique)->current();
        $rubrique->delete();

        $this->_helper->redirector('index');
    }

    public function addChampAction(): void
    {
        /** @var Zend_Controller_Action_Helper_ViewRenderer */
        $viewRenderer = $this->_helper->viewRenderer;
        $viewRenderer->setNoRender(true);

        $idRubrique = intval($this->getParam('rubrique'));
        $rubrique = $this->modelRubrique->find($idRubrique)->current()->toArray();

        /** @var Zend_Controller_Request_Http */
        $request = $this->getRequest();

        if ($request->isPost()) {
            $post = $request->getPost();
            $post['idx'] = $request->getParam('ID_CHAMP_PARENT') ? $this->modelChamp->getNbChampOfParent($request->getParam('ID_CHAMP_PARENT')) : $this->modelChamp->getNbChampOfRubrique($idRubrique);

            $idListe = $this->modelListeTypeChampRubrique->getIdTypeChampByName('Liste')['ID_TYPECHAMP'];

            $isParent = filter_var($request->getParam('isParent', false), FILTER_VALIDATE_BOOL);
            $champ = $this->serviceFormulaire->insertChamp($post, $rubrique, $isParent);

            $idChamp = intval($champ['ID_CHAMP']);
            $idTypeChamp = intval($champ['ID_TYPECHAMP']);

            $insertedRowAsArray = $this->modelChamp->getChampAndJoins($idChamp, ($idTypeChamp === $idListe));

            echo json_encode($insertedRowAsArray);
        }
    }

    public function editChampAction(): void
    {
        $this->_helper->layout->setLayout('menu_admin');

        /** @var Zend_View_Helper_InlineScript */
        $viewInlineScript = $this->view;
        $viewInlineScript->inlineScript()->appendFile('/js/formulaire/rubrique.js', 'text/javascript');
        $viewInlineScript->inlineScript()->appendFile('/js/formulaire/champ.js', 'text/javascript');
        $viewInlineScript->inlineScript()->appendFile('/js/formulaire/ordonnancement/ordonnancement.js', 'text/javascript');
        $viewInlineScript->inlineScript()->appendFile('/js/formulaire/ordonnancement/Sortable.js', 'text/javascript');

        /** @var Zend_View_Helper_HeadLink */
        $viewHeadLink = $this->view;
        $viewHeadLink->headLink()->appendStylesheet('/css/formulaire/formulaire.css', 'all');

        $idChamp = intval($this->getParam('champ'));
        $champ = $this->modelChamp->find($idChamp)->current();
        $champType = $this->modelChamp->getTypeChamp($idChamp);

        if (null !== $champ['ID_PARENT']) {
            $this->view->assign('infosParent', $this->modelChamp->getInfosParent($champ['ID_CHAMP']));
        }

        $idListe = $this->modelListeTypeChampRubrique->getIdTypeChampByName('Liste')['ID_TYPECHAMP'];
        if ($champ['ID_TYPECHAMP'] === $idListe) {
            $valeursChamp = $this->modelChampValeurListe->getValeurListeByChamp($champ['ID_CHAMP']);
            $this->view->assign('valeursChamp', $valeursChamp);
        }

        $rubrique = $this->modelRubrique->find($champ['ID_RUBRIQUE'])->current();
        $listeTypeChampRubrique = $this->serviceFormulaire->getAllListeTypeChampRubrique();

        if ('Parent' === $champType['TYPE']) {
            $listChamps = $this->modelChamp->getChampsFromParent($idChamp);

            foreach ($listChamps as &$listChamp) {
                if ('Liste' === $listChamp['TYPE']) {
                    $listChamp['VALEURS'] = $this->modelChampValeurListe->getValeurListeByChamp($listChamp['ID_CHAMP']);
                }
            }

            $this->view->assign('listChamp', $listChamps);
            $this->view->assign('listType', $this->modelListeTypeChampRubrique->getTypeWithoutParent());
            $this->view->assign(
                'formChamp',
                new Form_FormChampFromParent(
                    [
                        'champParentID' => $this->getRequest()->getParam('champ'),
                        'rubriqueID' => $this->getRequest()->getParam('rubrique'),
                    ]
                )
            );
        }

        $this->view->assign('champ', $champ);
        $this->view->assign('rubrique', $rubrique);
        $this->view->assign('listeTypeChampRubrique', $listeTypeChampRubrique);
        $this->view->assign('type', $champType['TYPE']);

        /** @var Zend_Controller_Request_Http */
        $request = $this->getRequest();

        if ($request->isPost()) {
            $post = $request->getPost();

            // Modification de valeur
            // On récupère les valeurs de la liste séparément des autres champs
            $listFieldValueArray = array_filter($post, function ($key) {
                return 0 === strpos($key, 'valeur-champ-');
            }, ARRAY_FILTER_USE_KEY);

            foreach ($listFieldValueArray as $key => $value) {
                $explodedKey = explode('-', $key);
                $idValue = intval(end($explodedKey));

                $valueField = $this->modelChampValeurListe->find($idValue)->current();
                $valueField->VALEUR = $value;
                $valueField->save();
            }

            // Ajout de valeur
            // On récupère les valeurs de la liste séparément des autres champs
            $listValueArray = array_filter($post, function ($key) {
                return 0 === strpos($key, 'valeur-ajout-');
            }, ARRAY_FILTER_USE_KEY);

            foreach ($listValueArray as $listValue) {
                $this->modelChampValeurListe->insert([
                    'VALEUR' => $listValue,
                    'ID_CHAMP' => $champ['ID_CHAMP'],
                ]);
            }

            $champ->NOM = $post['nom_champ'];
            $champ->save();
            $this->_helper->redirector('edit-rubrique', null, null, ['rubrique' => $rubrique['ID_RUBRIQUE']]);
        }
    }

    public function deleteChampAction(): void
    {
        $idChamp = intval($this->getParam('champ'));
        $champ = $this->modelChamp->find($idChamp)->current();
        $idRubrique = $champ['ID_RUBRIQUE'];
        $champ->delete();
        if ($this->getParam('ID_PARENT')) {
            $this->_helper->redirector('edit-champ', null, null, ['rubrique' => $idRubrique, 'champ' => $this->getParam('ID_PARENT')]);
        } else {
            $this->_helper->redirector('edit-rubrique', null, null, ['rubrique' => $idRubrique]);
        }
    }

    public function deleteValeurListeAction(): void
    {
        $idValeurListe = $this->getParam('liste');

        $valeurListe = $this->modelChampValeurListe->find($idValeurListe)->current();
        $valeurListe->delete();
    }

    public function updateChampIdxAction(): void
    {
        $this->_helper->viewRenderer->setNoRender(true);

        /** @var Zend_Controller_Request_Http */
        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = $request->getPost();
            $this->modelChamp->updateNewIdx($post);
        }
    }

    public function updateRubriqueIdxAction(): void
    {
        $this->_helper->viewRenderer->setNoRender(true);

        /** @var Zend_Controller_Request_Http */
        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = $request->getPost();
            $this->modelRubrique->updateNewIdx($post);
        }
    }
}
