<?php

class Model_LoginHistory extends Model_Table {

    public $entity_code = 'login_history';

    function init() {
        parent::init();
        $this->hasOne('Users',null,'email');
        $this->addField('action');
        $this->addField('date')->type('datetime');
        $this->addField('ip');
    }

}