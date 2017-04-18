<?php

class Model_Project extends Model_Table {

    public $entity_code = 'project';

    function init() {
        parent::init();
        $this->addField('name');
        $this->addField('project_start')->type('date');
        $this->addField('project_end')->type('date');
        $this->addExpression('duration')->set('CONCAT(DATEDIFF(project_end,project_start)," days")');
        $this->addField('created_on')->type('datetime');
        $site=$this->hasOne('Site','site_id','site_code');
        $usr=$this->hasOne('Users','created_by_id','email');
        $proj=$this->hasOne('ProjectTypes');
        $this->addField('comments')->type('text');
        
    }

}