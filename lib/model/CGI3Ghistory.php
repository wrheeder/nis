<?php
class Model_Cgi3Ghistory extends Model_Table{
    public $table = 'cgi_3g_history';
    
    function init() {
        parent::init();      
        $this->hasOne('cgi3g','cgi_3g_id');
        $this->addField('fld');
        $this->addField('previous');
        $this->addField('current');
        $this->addField('changed_on')->type('datetime');
        $this->addField('cgi_filename');
        
    }
}