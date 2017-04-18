<?php
class page_admin_UserRegions extends Page{
    function init() {
        parent::init();
        $this->api->stickyGet('users_id');
        $m=$this->add("Model_UserRegions");
        $m->addCondition('users_id',$_GET['users_id']);
        $crud=$this->add("CRUD");
        $crud->setModel($m);
        
        if($crud->grid){
            
        }
    }
}