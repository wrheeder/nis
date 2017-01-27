<?php

class Model_siteType extends Model_Table {
    public $table = 'site_type';

    function init() {
        parent::init();
        $this->addField('site_type');
    }

}
