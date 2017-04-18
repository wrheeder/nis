<?php

class Page_Projects_Tasks_History extends Page {

    function init() {
        parent::init();
        $h = $this->add('View_History')->set('History');
        $hg=$h->add('Grid');
        $tl=$this->add('Model_TaskLog');
        $tl->addCondition('tasks_id',$_GET['tasks_id']);
        $hg->setModel($tl);//,array('forecast_prev','actual_prev','changed_on','changed_by','system_comment')
        $hg->controller->importField('Email');
        $hg->RemoveColumn('tasks');
        $hg->RemoveColumn('baseline_prev');
//        $hg->RemoveColumn('actual_prev');
        
    }
}
