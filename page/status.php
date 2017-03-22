<?php
/**
 * Created by IntelliJ IDEA.
 * User: WRheeder
 * Date: 02/02/2017
 * Time: 12:36 PM
 */
class Page_Status extends Page
{

    public $title = 'ConfigUploader';

    function init()
    {
        parent::init();

        $f=$this->add("form");
        $f->addSubmit('3GUpload');
        $f2=$this->add("form");
        $f2->addSubmit('2GUpload');
        $f3=$this->add("form");
        $f3->addSubmit('LTEUpload');

        if($f->isSubmitted()){
            //$c->err('test');
            $js = array();
            $js[] = $this->js()->univ()->frameURL('3G Uploader',$this->api->url('upload3G',array('test'=>'123')));
            $this->js(true,$js)->univ()->execute();
        }
        if($f2->isSubmitted()){
            //$c->err('test');
            $js = array();
            $js[] = $this->js()->univ()->frameURL('2G Uploader',$this->api->url('upload2G',array('test'=>'123')));
            $this->js(true,$js)->univ()->execute();
        }
        if($f3->isSubmitted()){
            //$c->err('test');
            $js = array();
            $js[] = $this->js()->univ()->frameURL('LTE Uploader',$this->api->url('uploadLTE',array('test'=>'123')));
            $this->js(true,$js)->univ()->execute();
        }

    }
}
