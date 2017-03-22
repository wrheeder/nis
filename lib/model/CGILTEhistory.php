<?php
class Model_CgiLTEhistory extends Model_Table{
    public $table = 'cgi_LTE_history';
    
    function init() {
        parent::init();      
        $this->hasOne('cgiLTE','cgi_lte_id');
        $this->addField('fld');
        $this->addField('previous');
        $this->addField('current');
        $this->addField('changed_on')->type('datetime');
        $this->addField('cgi_filename');
    }
}