<?php
class Page_ConfigData_History3G extends Page {
    function init(){
        parent::init();
        $hist_grid=$this->add('grid');
        $hist_model=$this->add('Model_CGI3ghistory');
        $hist_model->addCondition('cgi_3g_id',$_GET['cgi_3g_id']);
        $hist_grid->setModel($hist_model);
        
    }
}