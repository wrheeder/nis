<?php
class Model_ColoOwner extends Model_Table{
    
    public $entity_code = 'colo_owner';

    function init() {
        parent::init();
        $this->addField('colo_owner');
//        $this->add('dynamic_model/Controller_AutoCreator_MySQL');  
    }
}