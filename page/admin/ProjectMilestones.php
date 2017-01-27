<?php
class page_admin_ProjectMilestones extends Page{
    function init() {
        parent::init();
        $this->api->stickyGet('id');
        $this->api->stickyGet('project_types_id');
        $m=$this->add("Model_ProjectMilestones")->addCondition('project_types_id',$_GET['project_types_id']);
        $crud=$this->add("CRUD");
        $crud->setModel($m);
        
        if($crud->grid){
            
        }
    }
}