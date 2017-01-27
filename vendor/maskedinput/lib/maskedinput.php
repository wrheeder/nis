<?php

namespace maskedinput;

class maskedinput extends \View {
    function init() {
        parent::init();
        $l = $this->api->locate('addons', 'maskedinput', 'location');
        $this->api->pathfinder->addLocation(array(
            'js' => 'vendor/maskedinput/js/',
            'template' => 'templates'
        ))->setParent($l);
        $this->js(true)->_load('jquery.maskedinput');
    }
}
