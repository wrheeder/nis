<?php
class page_admin_UserAppPages extends Page{
    function init() {
        parent::init();
        $this->api->stickyGet('users_id');
        $m=$this->add("Model_UserAppPages");
        $m->addCondition('users_id',$_GET['users_id']);
        $crud=$this->add("CRUD");
        $crud->setModel($m);
        
        if($crud->grid){
            
        }
    }
}