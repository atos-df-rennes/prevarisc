<?php

class ListeDesEvolutionsController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $layout = $this->getHelper('layout');
        if (!$layout instanceof Zend_Layout) {
            throw new Exception('Layout does not have the correct type.');
        }
        $layout->setLayout('menu_admin');

        $json = file_get_contents('/home/prv/current/httpd/conf/prevarisc/liste-evols.json');
        $parsjson = json_decode($json);
        $datas = (array) $parsjson;
        if (!empty($_GET)) {
            foreach (array_keys($datas) as $key) {
                $datas[$key] = isset($_GET[$key]);
            }
        }
        file_put_contents('/home/prv/current/httpd/conf/prevarisc/liste-evols.json', json_encode($datas));
        $this->view->assign('datas', $datas);
    }
}
