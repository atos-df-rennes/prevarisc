<?php

class ListeDesEvolutionsController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $layout = $this->getHelper('layout');
        if (!$layout instanceof Zend_Layout_Controller_Action_Helper_Layout) {
            throw new Exception('Layout does not have the correct type.');
        }
        $layout->getLayoutInstance()->setLayout('menu_admin');
        $json = file_get_contents(CONFIG_PATH.DS.'liste-evols.json');
        $parsjson = json_decode($json);
        $datas = (array) $parsjson;
        $this->view->assign('datas', $datas);
    }

    public function editAction()
    {
        $viewRenderer = $this->getHelper('viewRenderer');
        if (!$viewRenderer instanceof Zend_Controller_Action_Helper_ViewRenderer) {
            throw new Exception('View Renderer does not have the correct type.');
        }
        $viewRenderer->setNoRender();
        $json = file_get_contents(CONFIG_PATH.DS.'liste-evols.json');
        $parsjson = json_decode($json);
        $datas = (array) $parsjson;
        if (!empty($_POST)) {
            foreach (array_keys($datas) as $key) {
                $datas[$key][0] = isset($_POST[$key]);
            }
            file_put_contents(CONFIG_PATH.DS.'liste-evols.json', json_encode($datas));
        }
        $this->_helper->redirector('index', null, null, []);
    }
}
