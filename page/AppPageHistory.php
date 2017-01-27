<?php
class Page_AppPageHistory extends Page{
    function init(){
        parent::init();
        
        $hist = $this->add('Model_AppPageLog');
        $hist->addCondition('site_id',str_replace("[Site]", "", $_GET['sel_node']));
        $hist->addCondition('app_page_id',$_GET['app_page_id']);
        $hist->addCondition('app_sec_id',$_GET['section']);
        $hist->addCondition('fld',$_GET['field']);
//        $hist->debug();
//        $hist->tryLoadBy('site_id',str_replace("[Site]", "", $_GET['sel_node']));
//        die(var_dump($hist->getRows()));
        $this->add('HistoryLister')->setModel($hist,array('changed_on','prev','curr','changed_by_text','system_comment'));
    }
}
