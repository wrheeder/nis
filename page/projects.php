<?php

class Page_Projects extends Page {

    function init() {
        parent::init();
        $this->api->stickyGet('sel_node');
        $this->api->stickyGet('project_id');
        $this->api->stickyGet('selected_tab');
        $this->js(true)->_selector('#NRT_layout_fluid_index_form_sel_tab')->val($_GET['selected_tab']);
        //$parent_frm = $this->api->getElement('NRT_index');
        //die(var_dump($this->owner->owner));
        $site_id = $_GET['sel_node'];
        $projs = $this->add('Model_Project');
        $projs->addCondition('site_id', str_replace('[Site]', '', $site_id));
        $proj_grid = $this->add('GRID');
        $proj_grid->addClass('reloadable');
        $proj_grid->js("reloadpage", $proj_grid->js()->reload())->_selector('.reloadable');
//        $proj_grid->addButton('Reload Grid')->js('click',$proj_grid->js()->reload());
        $proj_grid->setModel($projs, array('name','project_start', 'project_end', 'duration', 'project_types', 'created_on', 'created_by', 'site','comments'));
        $proj_grid->controller->importField('email');
        $proj_grid->removeColumn('site');
        $tasks = $proj_grid->addColumn('expander', 'tasks');
        $frm = $this->add('Form');

        $frm->addButton('Add Project')->js('click')->univ()->frameURL('Add Project', array($this->api->url('AddProject'), 'sel_node' => $site_id));
    }

}
