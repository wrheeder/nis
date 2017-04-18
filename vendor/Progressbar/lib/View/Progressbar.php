<?php
/**
 * Created by IntelliJ IDEA.
 * User: WRheeder
 * Date: 02/02/2017
 * Time: 10:03 AM
 */
namespace Progressbar;

class View_Progressbar extends \View {

    function init() {
        $this->js(true)
            ->_load('progressbar');
        parent::init();
        $this->js(true)->univ()->pb($this);
    }


    function defaultTemplate() {
        // add add-on locations to pathfinder
        $l = $this->api->locate('addons', 'progressbar', 'location');
        $this->api->pathfinder->addLocation(array(
            'js' => 'vendor/progressbar/templates/js',
            'css' => 'templates/css',
            'template' => 'templates',
        ))->setParent($l);

        return parent::defaultTemplate();


    }

    public function updateProgress($pb,$x){
        $pb->js(true)->univ()->updateProgress($pb,$x);
    }



}