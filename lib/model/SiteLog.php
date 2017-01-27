<?php

class Model_SiteLog extends Model_Table {

    public $table = "site_log";

    function init() {
        parent::init();
        $this->hasOne('site','site_id');
        $this->addField('fld');
        $this->addField('prev');
        $this->addField('curr');
        $this->hasOne('Users','changed_by','email');
        $this->addField('changed_on')->type('datetime');
        $this->addField('system_comment')->type('text');
                
    }
    function history_push($site_id,$field_changed,$prev,$curr,$comment,$changed_by){
        if($field_changed=='owner_id'){
            $m_owner = $this->add('Model_siteOwner');
            $m_owner->load($prev);
            $prev = $m_owner->get('site_owner');
            $m_owner->load($curr);
            $curr = $m_owner->get('site_owner');
            $field_changed = 'owner';
        }
        if($field_changed=='colo_owner_id'){
            $m_colo_owner = $this->add('Model_coloOwner');
            $m_colo_owner->load($prev);
            $prev = $m_colo_owner->get('colo_owner');
            $m_colo_owner->load($curr);
            $curr = $m_colo_owner->get('colo_owner');
            $field_changed = 'colo_owner';
        }
        if($field_changed=='regions_id'){
            $m_regions = $this->add('Model_Regions');
            $m_regions->load($prev);
            $prev = $m_regions->get('region');
            $m_regions->load($curr);
            $curr = $m_regions->get('region');
            $field_changed = 'region';
        }
        if($field_changed=='site_type_id'){
            $m_site_type = $this->add('Model_siteType');
            $m_site_type->load($prev);
            $prev = $m_site_type->get('site_type');
            $m_site_type->load($curr);
            $curr = $m_site_type->get('site_type');
            $field_changed = 'site_type';
        }
        
        $this->set('site_id',$site_id);
        $this->set('fld',$field_changed);
        $this->set('prev',$prev);
        $this->set('curr',$curr);
        $this->set('changed_by',$changed_by);
        $this->set('changed_on',date('Y-m-d H:i:s'));
        $this->set('system_comment',$comment);     
        $this->saveAndUnload();
    }

}
