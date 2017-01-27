<?php

class Model_Site extends Model_Table {

    public $entity_code = 'site';

    function init() {
        parent::init();
//        $this->debug();
        $this->addField('site_code')->mandatory(true)->caption('Property ID');
        $this->addField('candidate_letter')->mandatory(true)->defaultValue('A');
        $this->addField('site_name')->mandatory(true);
        $this->addField('candidate_status')->enum(array('Preferred', 'Decommissioned', 'Failed'));
        $this->hasOne('Regions', null, 'region')->caption('Region');
        $this->addField('latitude')->display(array('form' => 'maskedinput/maskedinput'))->mandatory(true);
        $this->addField('longitude')->display(array('form' => 'maskedinput/maskedinput'))->mandatory(true);
        $this->addField('corr_lat')->display(array('form' => 'maskedinput/maskedinput'))->caption('Corrected Latitude');
        $this->addField('corr_lon')->display(array('form' => 'maskedinput/maskedinput'))->caption('Corrected Longitude');
        $this->hasOne('siteOwner', null, 'site_owner')->mandatory(true);
        $this->hasOne('coloOwner', null, 'colo_owner');
        $this->hasOne('siteType', null, 'site_type')->mandatory(true);
        $this->addField('site_name_used')->enum(array('mLog', 'Lease', 'Assumed'));
        $this->hasMany('Lease');
        $this->addField('on_air_status')->enum(array('2G', '3G', '2G/3G','LTE'));
        
//        $this->addHook('beforeInsert', $this);
//        $this->addHook('beforeSave', $this);

//        $this->add('dynamic_model/Controller_AutoCreator_MySQL');
    }

    function BeforeInsert($m, $t) {
        foreach ($this->getActualFields() as $f) {
            $field = $this->getField($f);
            break;
        }
            throw $this->exception("Sitecode_Candidate exists!", "ValidityCheck")->setField($field);
            
        
        return $this;
    }

    function BeforeSave() {
        $field = $this->getField('site_code');
        throw $this->exception("Sitecode_Candidate exists!", "ValidityCheck")->setField($field);
        return $this;
    }

    function getMarkerHtml() {
        return '<H3>' . $this['site_code'] . ' - ' . $this['site_name'] . '</H3><hr><p><i>LATTITUDE:</i>' . $this['latitude'] . '</br><i>LONGITUDE:</i>' . $this['longitude'] . '</p></br>';
    }

}
