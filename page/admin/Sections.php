<?php

class Page_Admin_Sections extends Page_ApplicationPage {

    function init() {
        parent::init();
        
        
        $this->api->stickyGet('application_pages_id');
        
        $this->add('View_Info','Add Fields')->Set('Add Sections');
        $m_sections = $this->add('Model_ApplicationPagesSections');
        $m_sections->addCondition('application_pages_id', $_GET['application_pages_id']);
        $c = $this->add('CRUD');
        $c->setModel($m_sections, array('section_name'));
        if ($c->grid) {
            $c->grid->addClass("zebra bordered");
            $c->grid->addPaginator(10);
            $c->grid->addColumn('expander', 'Fields');   
        }
        
    }

}
