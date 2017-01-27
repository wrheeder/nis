<?php
/**
 * Created by IntelliJ IDEA.
 * User: WRheeder
 * Date: 26/01/2017
 * Time: 4:01 PM
 */

class Page_ApplicationPage extends Page{

    public $dec=5;
    function init(){
        parent::init();
        if($this->api->auth->get('user_must_change_pw'))
        {
            $name = $this->api->auth->get('username');
            $this->api->redirect('/admin/changePasswordExt');
            $url = $this->page;
        }

        if($this->api->auth->isLoggedIn())
        {
            $this->api->template->set('Welcome','Logged In as '.$this->api->auth->get('email'));
        }
        $this->api->template->set('run_time','Page Rendered in ...'.substr(microtime(true) - $_SERVER['REQUEST_TIME'],0,$this->dec).' seconds');
    }
//    function render(){
//        parent::render();
//
//    }
    function setLimit($dec = 5){
        $this->dec=$dec;
    }
}

