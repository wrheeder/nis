<?php

class Model_Users extends Model_Table {

    public $table = 'users';
    
    function init() {
        parent::init();
        $this->addField('email')->mandatory('Email required');
        $this->addField('name')->mandatory('Name required');
        $this->addField('surname')->mandatory('Surname required');
        $this->addField('password')->display(array('form' => 'password'))->mandatory('Type your password');
        $this->addField('isAdmin')->type('boolean');
        $this->addField('user_must_change_pw')->type('boolean');
        $this->addField('can_add_site')->type('boolean');
        $this->addField('can_change_region')->type('boolean');
        $this->addField('can_update_baseline')->type('boolean');
        $this->addField('can_update_forecast')->type('boolean');
        $this->addField('can_update_actual')->type('boolean');
        $this->addField('rollout_menu')->type('boolean');
        $this->addField('config_data_menu')->type('boolean');
        $this->addField('can_upload_sites')->type('boolean');
    }
    

}