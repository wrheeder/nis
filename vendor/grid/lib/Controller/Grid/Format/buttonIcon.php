<?php
namespace grid;
class Controller_Grid_Format_buttonIcon extends \AbstractController {
    public $label, $descr, $page;
    function initField($field, $description){
        $this->label=$this->descr=$description;
    }
    function formatField($field){
        $grid=$this->owner;
        $grid->current_row_html[$field]='<button type="button" class="button_'.$field.' ui-state-default  ui-button ui-widget ui-button-text-icon-primary ui-corner-left ui-corner-right"><span class="ui-button-text"><i class="atk-icon atk-icons-red atk-icon-office-pencil"></i>'.$this->label.'</span></button>';
    }
}