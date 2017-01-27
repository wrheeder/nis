<?php
namespace grid;
class Controller_Grid_Format_popup extends \AbstractController {
	public $label, $descr, $page;
	function initField($field, $description){
		$grid=$this->owner;
		$this->label=$this->descr=$description;
		$this->page='./'.$field;
		$grid->js(true)->_selector('#'.$grid->name.' .button_'.$field)
			->button();

		$grid->js('click')->_selector('#'.$grid->name.' .button_'.$field)
			->univ()
			->frameURL($description,array($this->api->url($this->page),
				$grid->model->id_field=>$grid->js()
					->_selectorThis()
					->closest('tr')
					->attr('data-id')));


		$grid->columns[$field]['thparam'].=' style="width: 40px; text-align: center"';
	}
	function formatField($field){
		$grid=$this->owner;
		$grid->current_row_html[$field]='<button type="button" class="button_'.$field.'">'.$this->label.'</button>';
	}
}
  