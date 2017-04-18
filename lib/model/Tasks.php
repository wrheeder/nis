<?php
class Model_Tasks extends Model_Table{
    
    public $entity_code = 'tasks';
    
    function init() {
        parent::init();
        $this->hasOne('Project');
        $this->hasOne('ProjectMilestones','project_milestones_id','ms_name');
        $this->addField('baseline')->type('date');
        $this->addField('forecast')->type('date');
        $this->addField('actual')->type('date');
        $this->hasOne('Users','created_by','email');
        $this->addField('created_on')->type('date');
        
    }
}