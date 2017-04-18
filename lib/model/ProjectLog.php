<?php

class Model_ProjectLog extends Model_Table {

    public $table = "project_log";

    function init() {
        parent::init();
        $this->hasOne('project','project_id');
        $this->addField('fld');
        $this->addField('prev');
        $this->addField('curr');
        $this->hasOne('Users','changed_by','email');
        $this->addField('changed_on')->type('datetime');
        $this->addField('system_comment')->type('text');
                
    }
    function history_push($project_id,$field_changed,$prev,$curr,$comment,$changed_by){
        $this->set('project_id',$project_id);
        $this->set('fld',$field_changed);
        $this->set('prev',$prev);
        $this->set('curr',$curr);
        $this->set('changed_by',$changed_by);
        $this->set('changed_on',date('Y-m-d H:i:s'));
        $this->set('system_comment',$comment);     
        $this->saveAndUnload();
    }

}
