<?php
class Model_UserRegions extends Model_Table{
    
    public $entity_code = 'users_regions';
    function init() {
        parent::init();
        $this->api->stickyGet('users_id');
        //echo $_GET['users_id'];
        $this->hasOne('Users','users_id','email');
        $this->hasOne('Regions','regions_id','region');//->display(array('form'=>'autocomplete/Basic'));  
        
    }
}