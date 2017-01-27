<?php

namespace jsTree;

class jsTree extends \View {
    function init() {
        parent::init();
        $l = $this->api->locate('addons', 'jsTree', 'location');
        $this->api->pathfinder->addLocation(array(
            'js' => 'vendor/jstree/js/', 
            'template' => 'templates',
            'css' => 'vendor/jstree/js/'
        ))->setParent($l);
        
        $this->api->jui->addStaticStylesheet('themes/default/style', '.css', 'css');
        $this->js(true)->_load('jstreePlugin');
        $this->api->jui->addStaticInclude('jquery.jstree');
        $this->api->jui->addStaticInclude('myJSFuncs');
    }
}
