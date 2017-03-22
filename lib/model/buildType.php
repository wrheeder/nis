<?php

class Model_buildType extends Model_Table {
    public $table = 'build_type';

    function init() {
        parent::init();
        $this->addField('build_type');
    }

}
