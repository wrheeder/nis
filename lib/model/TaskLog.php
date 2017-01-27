<?php

class Model_TaskLog extends Model_Table {

    public $table = "task_log";

    function init() {
        parent::init();
        $this->hasOne('tasks','tasks_id');
        $this->addField('baseline_prev')->type('datetime');
        $this->addField('forecast_prev')->type('datetime');
        $this->addField('actual_prev')->type('datetime');
        $this->hasOne('Users','changed_by','email');
        $this->addField('changed_on')->type('datetime');
        $this->addField('system_comment')->type('text');
                
    }
    function history_push($task_id,$field_changed,$prev,$curr,$comment,$changed_by){
        $this->set('tasks_id',$task_id);
        if($field_changed=='Baseline')
                $this->set('baselinge_prev',$prev);
        if($field_changed=='Forecast')
                $this->set('forecast_prev',$prev);
        if($field_changed=='Actual')
                $this->set('actual_prev',$prev);
        $this->set('changed_by',$changed_by);
        $this->set('changed_on',date('Y-m-d H:i:s'));
        $this->set('system_comment',$comment);     
        $this->saveAndUnload();
    }

}
