<?php

class Model_ProjectTypes extends Model_Table {

    public $entity_code = 'project_types';

    function init() {
        parent::init();
        $this->addField('name');
        $this->addField('duration')->type('number');
        $this->addField('multiple_allowed')->type('boolean')->defaultValue(false);
        //$this->hasMany('ProjectMilestones');
    }

}