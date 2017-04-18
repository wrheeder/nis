<?php
class Page_ConfigData_History2G extends Page {
    function init(){
        parent::init();
        $hist_grid=$this->add('grid');
        $hist_model=$this->add('Model_CGI2ghistory');
        $hist_model->addCondition('cgi_2g_id',$_GET['cgi_2g_id']);
        $hist_grid->setModel($hist_model);
        
    }
}