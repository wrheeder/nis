<?php
class Model_ApplicationPagesSections extends Model_Table{
    public $entity_code = 'application_pages_sections';

    function init() {
        parent::init();
        $this->addField('section_name')->mandatory(true);
        $this->hasOne('ApplicationPages');
    }
}