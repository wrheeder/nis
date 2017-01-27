<?php
class Form_Field_NoLabelLine extends Form_Field_Line{
    function init() {
        parent::init();
        $this->js(true)->remove('label');
        $this->addIcon('plus');
    }
}