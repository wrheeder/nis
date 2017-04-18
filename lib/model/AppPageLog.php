<?php

class Model_AppPageLog extends Model_Table {

    public $table = "app_page_log";

    function init() {
        parent::init();
        $this->hasOne('site','site_id');
        $this->addField('fld');
        $this->addField('prev');
        $this->addField('curr');
        $this->hasOne('Users','changed_by','email');
        $this->addField('changed_on')->type('datetime');
        $this->addField('system_comment')->type('text');
        $this->hasOne('ApplicationPages','app_page_id');
        $this->hasOne('ApplicationPagesSections','app_sec_id');
                
    }
    function history_push($site_id,$field_changed,$prev,$curr,$comment,$changed_by,$app_page_id,$app_sec_id){
        $this->set('site_id',$site_id);
        $this->set('fld',$field_changed);
        $this->set('prev',$prev);
        $this->set('curr',$curr);
        $this->set('changed_by',$changed_by);
        $this->set('changed_on',date('Y-m-d H:i:s'));
        $this->set('system_comment',$comment);   
        $this->set('app_page_id',$app_page_id);
        $this->set('app_sec_id',$app_sec_id);
        $this->set('changed_by',$changed_by);
        $this->saveAndUnload();
    }

}
