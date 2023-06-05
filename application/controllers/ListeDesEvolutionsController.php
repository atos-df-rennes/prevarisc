<?php
class ListeDesEvolutionsController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $this->_helper->layout->setLayout('menu_admin');
        $json = file_get_contents('/home/prv/current/httpd/conf/prevarisc/liste-evols.json');
        $parsjson = json_decode($json);
        $datas = (array) $parsjson;
        if(!empty($_GET)){
            foreach($datas as $key=>$data){
                $datas[$key] = isset($_GET[$key]) ? true : false;
            }
        }
        file_put_contents('/home/prv/current/httpd/conf/prevarisc/liste-evols.json', json_encode($datas));
        $this->view->datas = $datas;
    }
}
?>
