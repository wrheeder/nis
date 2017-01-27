<?php
namespace maskedinput;
class Form_Field_maskedinput extends \Form_Field_Line {

    public $options = array();

    function init() {
        parent::init();
        $l = $this->api->locate('addons', 'maskedinput', 'location');
        $this->api->pathfinder->addLocation(array(
            'js' => 'vendor/maskedinput/js/',
            'template' => 'templates'
        ))->setParent($l);
        $this->js(true)->_load('jquery.maskedinput');
    }

    function getInput($attr = array()) {
        return parent::getInput();
    }
    function setMask($mask){
        $this->js(true,$this->js()->mask($mask));
    }
    function get(){
        return parent::get();
    }
}