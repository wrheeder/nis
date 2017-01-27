<?php
class Model_Cgi2GExternal extends Model_Table{
    public $table = 'cgi_2g_external';
    
    function init(){
        parent::init();
        $this->hasOne('cgi2G');
        $this->addField('sector');
    }
}