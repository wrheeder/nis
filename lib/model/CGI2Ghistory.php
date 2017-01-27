<?php
class Model_Cgi2Ghistory extends Model_Table{
    public $table = 'cgi_2g_history';
    
    function init() {
        parent::init();      
        $this->hasOne('cgi2g','cgi_2g_id');
        $this->addField('fld');
        $this->addField('previous');
        $this->addField('current');
        $this->addField('changed_on')->type('datetime');
        $this->addField('cgi_filename');
        
    }
}