<?php
class Page_Admin_Sections_Fields extends Page_ApplicationPage{
    function init() {
        parent::init();
        $this->add('View_Info','Add Fields')->Set('Add Fields');
        $this->api->stickyGet('application_pages_sections_id');
        $m_sections_fields = $this->add('Model_ApplicationPagesDataFields');
        $m_sections_fields->addCondition('application_pages_sections_id', $_GET['application_pages_sections_id']);
        $c = $this->add('CRUD');
        $c->setModel($m_sections_fields, array('field_name','field_type'));
        if ($c->grid) {
            $c->grid->addClass("zebra bordered");
            $c->grid->addPaginator(10);
        }
        
    }
}