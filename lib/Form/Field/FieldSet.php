<?php

class Form_Field_FieldSet extends Form_Field {

    public $left = 'Min';
    public $right = 'Max';
    public $class = 'FokMy';
    
    function init() {
        parent::init();
    }

    function getInput($attr = array()) {
       $output=$this->getTag('fieldset',array_merge(array(
                        'name'=>$this->name,
                        'data-shortname'=>$this->short_name,
                        'id'=>$this->name,
                        'class'=>$this->class
                        ),
                    $attr,
                    $this->attr)
                );
       return $output;
    }

}
