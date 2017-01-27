<?php
class Model_Cgi3GMapping extends Model_Table{
    public $table = 'cgi_3g_mapping';
    
    function init(){
        parent::init();
        $this->hasOne('Site',null,'site_code')->display(array('form'=>'autocomplete/Plus'));
        $this->hasOne('cgi3G')->display(array('form'=>'autocomplete/Plus'));
    }
}