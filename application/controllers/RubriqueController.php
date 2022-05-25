<?php

class RubriqueController extends Zend_Controller_Action
{

    public function init(){
        $this->_helper->viewRenderer->setNoRender();

    }

    public function getRowTableauAction()
        {
            echo("yes");
            /*
            $modelChamp = new Model_DbTable_Champ();
            $request = $this->getRequest();
            if ($request->isPost()) {
                $data = $request->getPost();
                $this->view->assign('listInput',$modelChamp->getAllFils($data['idParent']));
            }
            */
        }
}
?>