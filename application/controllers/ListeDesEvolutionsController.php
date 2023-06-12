<?php

class ListeDesEvolutionsController extends Zend_Controller_Action
{
    public function indexAction()
    {
        // $this->_helper->getLayoutInstance()->setLayout('menu_admin');
        /** @var Zend_Layout */
        $layout = $this->getHelper('layout');
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
        $this->view->assign('datas',$datas);
    }
}
