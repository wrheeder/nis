<?php
class Model_Cgi3G extends Model_Table{
    public $table = 'cgi_3g';
    
    function init() {
        parent::init();
        $this->addField('cgi')->required(true);
        $this->addField('vendor');
        $this->addField('region');
//        $this->addField('msc');
        $this->addField('rncname');
        $this->addField('rncid');
        $this->addField('siteid');
        $this->addField('sitename');
        $this->addField('btsid');
        $this->addField('ci');
        $this->addField('lac');
        $this->addField('rac');
        $this->addField('name');
        $this->addField('uarfcn');
        $this->addField('priscrcode');
        $this->addField('cpichpower');
        $this->addField('totalpower');
        $this->addField('removed')->type('boolean');
        
    }
}