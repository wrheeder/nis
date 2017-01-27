<?php
class Model_ApplicationPages extends Model_Table{
    public $entity_code = 'application_pages';

    function init() {
        parent::init();
        $this->addField('page_name')->mandatory(true);
        $this->addField('table_name')->mandatory(true);
//        $this->hasOne('Project',null,'project_types');
    }
}