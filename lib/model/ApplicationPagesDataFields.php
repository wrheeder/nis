<?php
class Model_ApplicationPagesDataFields extends Model_Table{
    public $entity_code = 'application_pages_data_fields';

    function init() {
        parent::init();
        $this->hasOne('ApplicationPagesSections',null,'section_name');
        $this->addField('field_name')->mandatory('Fieldname required');
        $this->addField('field_type')->mandatory('Field Type needs to be selected');
    }
}