<?php

class Model_Lease extends Model_Table {
    
    public $entity_code = 'lease';
    
    function init() {
        parent::init();
//        $this->debug();
//        $site  = $this->hasOne('Site','site_id')->system(true)->visible(false);
        $this->addField('LeaseName')->required('Please enter LeaseName');
        $this->addField('ThirdPartyRef')->Caption('3rd Party Reference');
        $this->addField('LeaseStatus')->enum(array('Lease','Application','Dont Cancel'));
        $this->addField('LeaseStart')->type('datetime');
        $this->addField('LeaseExpiry')->type('datetime');
        $this->addField('Escalation_Rate')->type('float');
        $this->addField('Escalation_Date')->type('datetime');
        $this->addField('Option_Periods');
        $this->addField('Option_Exercised')->type('datetime');
        $this->addField('Renewal_start')->type('datetime');
        $this->addField('Renewal_expiry')->type('datetime');
        $this->add('Controller_Validator')->is(['LeaseName|unique?Lease Name must be Unique|required?Enter an Unique Lease Name'])->on('beforeInsert');
    }

}
