<?php

class Model_LeasePageLog extends Model_Table {

    public $table = "lease_page_log";

    function init() {
        parent::init();
        $this->hasOne('lease','lease_id');
        $this->addField('fld');
        $this->addField('prev');
        $this->addField('curr');
        $this->hasOne('Users','changed_by','email');
        $this->addField('changed_on')->type('datetime');
        $this->addField('system_comment')->type('text');
//        $this->add('dynamic_model/Controller_AutoCreator_MySQL');  
                
    }
    function history_push($lease_id,$field_changed,$prev,$curr,$comment,$changed_by){
        $this->set('lease_id',$lease_id);
        $this->set('fld',$field_changed);
        $this->set('prev',$prev);
        $this->set('curr',$curr);
        $this->set('changed_by',$changed_by);
        $this->set('changed_on',date('Y-m-d H:i:s'));
        $this->set('system_comment',$comment);   
        $this->set('changed_by',$changed_by);
        $this->saveAndUnload();
    }

}
