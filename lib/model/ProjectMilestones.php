<?php

class Model_ProjectMilestones extends Model_Table {

    public $entity_code = 'project_milestones';

    function init() {
        parent::init();
        $this->addField('ms_name')->caption('Milestone Name');
        $this->hasOne('ProjectTypes');
        $this->addField('duration');
//        $ms = $this->hasOne('ProjectMilestones', 'parent_id', 'ms_name');

        $ms=$this->hasOne('ProjectMilestonesP', 'parent_id','ms_name');
//        $this->hasMany('Inception', 'parent_inception_id', null, 'Inception');
//        $this->hasMany('Inception_Children', 'parent_inception_id');

        if ($this->owner->short_name == 'parent_id') {
            $this->addCondition('project_types_id', $_GET['project_types_id']);
        }
    }

}