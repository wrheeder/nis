<?php
class Model_LeaseSite extends Model_Table{
    
    public $entity_code = 'lease_site';
    function init() {
        parent::init();
        $this->hasOne('Lease','lease_id','LeaseName');
        $this->hasOne('Site','site_id','site_code');//->display(array('form'=>'autocomplete/Basic'));  
        
    }
}