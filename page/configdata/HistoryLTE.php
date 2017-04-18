<?php
class Page_ConfigData_HistoryLTE extends Page {
    function init(){
        parent::init();
        $hist_grid=$this->add('grid');
        $hist_model=$this->add('Model_CGILTEhistory');
        $hist_model->addCondition('cgi_lte_id',$_GET['cgi_LTE_id']);
        $hist_grid->setModel($hist_model);
        
    }
}