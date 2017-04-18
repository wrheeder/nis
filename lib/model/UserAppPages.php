<?php
class Model_UserAppPages extends Model_Table{
    
    public $entity_code = 'users_app_pages';
    function init() {
        parent::init();
        $this->api->stickyGet('users_id');
        //echo $_GET['users_id'];
        $this->hasOne('Users','users_id','email');
        $this->hasOne('ApplicationPages','application_pages_id','page_name');//->display(array('form'=>'autocomplete/Basic'));  
        
    }
}