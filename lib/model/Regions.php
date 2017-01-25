<?php
class Model_Regions extends Model_Table{
    public $entity_code = 'regions';

    function init() {
        parent::init();
        $this->addField('region');
    }
}