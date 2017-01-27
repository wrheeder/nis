<?php
class View_History extends View_Box {
    function init(){
        parent::init();
        $this->addClass('atk-effect-info');
        $this->addIcon('back-in-time');
    }
}
