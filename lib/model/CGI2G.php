<?php
class Model_Cgi2G extends Model_Table{
    public $table = 'cgi_2g';
    
    function init() {
        parent::init();
        $this->addField('cgi')->required(true);
        $this->addField('vendor');
        $this->addField('region');
//        $this->addField('msc');
        $this->addField('bscname');
        $this->addField('bscid');
        $this->addField('sitename');
        $this->addField('siteid');
        $this->addField('btsid');
        $this->addField('ci');
        $this->addField('lac');
        $this->addField('rac');
        $this->addField('name');
        $this->addField('bcch');
        $this->addField('ncc');
        $this->addField('bcc');
        $this->addField('trx_cnt')->caption('trxs');
        $this->addField('removed')->type('boolean');
        
//        $ext_data = $this->join('CGI_2G_External_data.cgi_2g_id',null,'left outer');
//        $ext_data->addField('sector');
//        $this->add('dynamic_model/Controller_AutoCreator_MySQL');    
    }
}