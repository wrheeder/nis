<?php
/**
 * Created by IntelliJ IDEA.
 * User: WRheeder
 * Date: 24/02/2017
 * Time: 10:29 AM
 */

class Model_AssetMapping  extends Model_Table{
    public $table = 'asset_mapping';

    function init() {
        parent::init();
        $this->addField('logcellpk')->required(true);
    }
}