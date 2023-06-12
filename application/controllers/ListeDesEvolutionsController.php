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
        if (!empty($_GET)) {
            foreach (array_keys($datas) as $key) {
                $datas[$key][0] = isset($_GET[$key]);
            }
        }
        file_put_contents(CONFIG_PATH.DS.'liste-evols.json', json_encode($datas));
        $this->view->assign('datas', $datas);
    }
}
