<?php
class Model_CgiLTE extends Model_Table{
    public $table = 'cgi_LTE';
    
    function init() {
        parent::init();
        $this->addField('cgi')->required(true);
        $this->addField('vendor');
        $this->addField('region');
        $this->addField('enodebid');
        $this->addField('sitename');
        $this->addField('name');
        $this->addField('cellid');
        $this->addField('phycellid');
        $this->addField('tac');
        $this->addField('dlearfcn');
        $this->addField('removed')->type('boolean');
        
    }
}