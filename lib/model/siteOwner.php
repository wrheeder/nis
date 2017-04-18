<?php
class Model_SiteOwner extends Model_Table{
    
    public $entity_code = 'owner';

    function init() {
        parent::init();
        $this->addField('site_owner');
//        $this->add('dynamic_model/Controller_AutoCreator_MySQL');  
    }
}