<?php
/**
 * Created by IntelliJ IDEA.
 * User: WRheeder
 * Date: 24/02/2017
 * Time: 12:30 PM
 */

class Model_AssetMappingHistory extends Model_Table{
    public $table = 'asset_mapping_history';

    function init() {
        parent::init();
        $this->hasOne('asset_mapping','asset_mapping_id');
        $this->addField('fld');
        $this->addField('previous');
        $this->addField('current');
        $this->addField('changed_on')->type('datetime');
    }
}