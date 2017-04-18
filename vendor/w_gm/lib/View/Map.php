<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace w_gm;

class View_Map extends \View {

    public $height = 400;
    public $width = 400;
    public $sensor = 'false';
    public $lat = -29.518914;
    public $lng = 24.235833;
    public $zoom = 10;
    public $map_type = 'google.maps.MapTypeId.TERRAIN';
    public $options = array();

    function init() {
        parent::init();
        //$this->api_js_url = 'http://maps.googleapis.com/maps/api/js?sensor=' . $this->sensor;
        $this->set('Loading Google Map...');
    }

    function setCenter($lat, $lng) {
        $this->lat = $lat;
        $this->lng = $lng;
        return $this;
    }

    function setZoom($zoom) {
        $this->zoom = $zoom;
        return $this;
    }

    function showMap() {
        $this->js(true)->w_gm()->init_gm($this->lat, $this->lng, $this->zoom, $this->options, $this->map_type);
        return $this;
    }
    function codeAddress($address='South Africa'){
        $this->js(true)->w_gm()->codeAddress($address);
    }
    function setMarker($args = null, $trigger = true) {
        $this->js($trigger)->w_gm()->marker($args);
        return $this;
    }
    function drawSector($lat,$lon,$r,$azi,$steps,$fan_deg,$line_col,$fill_col,$trigger = true){
        $this->js($trigger)->w_gm()->drawSector($lat,$lon,$r,$azi,$steps,$fan_deg,$line_col,$fill_col);
        return $this;
    }
    private function setWidthHeight() {
        $this->addStyle(array('height' => $this->height . 'px'));
        return $this;
    }

    function render() {
        $this->setWidthHeight();
        $this->js(true)
                ->_load('w_gm')
        //		->_css('x_gm')
        ;
        parent::render();
    }

    function log() {
        $this->js(true)->w_gm()->log();
    }

    function defaultTemplate() {
        // add add-on locations to pathfinder
        $l = $this->api->locate('addons', 'w_gm', 'location');
        $this->api->pathfinder->addLocation(array(
            'js' => 'vendor/w_gm/templates/js/',
            'css' => 'templates/css',
            'template' => 'templates',
        ))->setParent($l);

        return parent::defaultTemplate();
    }

}
