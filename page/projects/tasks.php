<?php

class Page_Projects_Tasks extends Page {

    function init() {
        parent::init();
        $this->api->stickyGet('sel_node');
        $this->api->stickyGet('project_id');
        $tasks_grid = $this->add('Grid');
        $tasks_grid->addClass('reloadable_grid');
        $tasks = $this->add('Model_Tasks');
        $tasks->addCondition('project_id', $_GET['project_id']);
        $tasks_grid->setModel($tasks, array('project_milestones', 'baseline', 'forecast', 'actual','duration'));
        
       $this->js("reloadpage", $this->js()->_selector('.reloadable_grid')->reload());
        
        if($this->api->auth->get('can_update_actual'))
            $tasks_grid->addColumn('expander','ClaimActuals');
        if($this->api->auth->get('can_update_forecast'))
            //$tasks_grid->addColumn('grid/ReForecastButton','ReForecast');
        $tasks_grid->addColumn('expander', 'History');
        //$tasks_grid->addFormatter('actual','grid/inline');
//        if($_GET['field']=='actual'){
//            
//        }
    }
}
