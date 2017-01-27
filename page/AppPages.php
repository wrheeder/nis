<?php

class Page_AppPages extends Page {

    function init() {
        parent::init();
        $fields_arr = array();
        $this->api->stickyGet('app_page_tbl');
        $this->api->stickyGet('sel_node');
        $this->api->stickyGet('sel_tab');
        $this->api->stickyGet('app_page');
        $this->api->stickyGet('app_page_id');
        $this->api->stickyGet('search_fld');
        $this->api->stickyGet('selected_tab');
        $this->js(true)->_selector('#NRT_layout_fluid_index_form_sel_tab')->val($_GET['selected_tab']);

        $this->api->jui->addStaticInclude('myJSFuncs');

        $m_sections = $this->add('Model_ApplicationPagesSections');
        $m_sections->addCondition('application_pages_id', $_GET['app_page_id']);
        $m_form_model = $this->add('Model_ApplicationPageGenericModel');
//        $m_form_model->debug();
//        die(var_dump(str_replace("[Site]","",$_GET['sel_node'])));

        $sections = $m_sections->getRows();
        $rd = rand(1, 1000);

        $v = $this->add("View", 'v_' . $_GET['app_page_id']);

        $f = $v->add('Form');

        $order = $f->add('Order');
        $f->setModel($m_form_model, null);
        $f->model->tryLoad(str_replace("[Site]", "", $_GET['sel_node']));



        $sections_array = array();
        foreach ($sections as $section) {
            $sections_array[$section['id']] = $f->addField('FieldSet', $section['section_name']);
            $m_fields = $this->add('Model_ApplicationPagesDataFields');
            $m_fields->addCondition('application_pages_sections_id', $section['id']);
            $fields = $m_fields->getRows();
            $sep = $f->addSeparator();
            foreach ($fields as $field) {
                $fld = $f->getElement($field['field_name']);
                $fname = $field['field_name'];
                $fld->js('change', $f->js()->addClass('form_changed'));
                $order->move($fld, 'before', $sep)->now();

                ${'pop_$fname'} = $this->add('View_Popover');
                $url = $this->api->url('AppPageHistory', array('field' => $fname, 'section' => $field['application_pages_sections_id'], 'app_page_id' => $_GET['app_page_id']));
                ${'pop_$fname'}->setURL($url);

                $fld_i = $fld->addButton('', array('options' => array('text' => false)))
                                ->setHtml('')
                                ->setIcon('back-in-time')->js('click', ${'pop_$fname'}->showJS());




//                $fld_i // ${'pop_$field'}->showJS()
                $fields_arr[] = $field;
            }
        }

        $f->addSubmit('Update', 'app_page_' . $_GET['app_page_id']);

        if ($f->isSubmitted()) {
            $hist = $this->add('Model_AppPageLog');
            $js = array();

            $js[] = $f->js()->reload();
//            $f->removeClass('form_changed');
            if ($f->model->loaded()) {
                
            } else {
                $f->model->set('site_id', str_replace("[Site]", "", $_GET['sel_node']));
            }
//            die(var_dump($fields_arr));
            foreach ($fields_arr as $field) {
                $prev = $f->model->get($field['field_name']);
                $curr = $f->get($field['field_name']);
                if ($f->model->getField($field['field_name'])->type() == 'datetime' || $f->model->getField($field['field_name'])->type() == 'date') {
                    $fld_dte = DateTime::createFromFormat('Y-m-d', $curr);
                    $prev_fld_dte = DateTime::createFromFormat('Y-m-d H:i:s', $prev);
                    $prev_fld_dte = $prev_fld_dte != false ? $prev_fld_dte->format('Y-m-d') : false;
                    $fld_dte = $fld_dte != false ? $fld_dte->format('Y-m-d') : false;
//                    die(var_dump($fld_dte));
                    if ($prev_fld_dte != $fld_dte) {
                        $hist->history_push($f->model->get('site_id'), $field['field_name'], $prev_fld_dte!=false?$prev_fld_dte:null, $fld_dte!=false?$fld_dte:null, $field['field_name'] . ' Updated', $this->api->auth->get('id'), $_GET['app_page_id'], $field['application_pages_sections_id']);
                    }
                } else if ($f->model->getField($field['field_name'])->type() == 'boolean') {
                    if ($f->model->get($field['field_name']) != $f->get($field['field_name'])) {
                        if ($prev == false)
                            $prev = "No";
                        if ($prev === '1')
                            $prev = "Yes";
                        if ($curr == false)
                            $curr = "No";
                        if ($curr === true)
                            $curr = "Yes";
                        $hist->history_push($f->model->get('site_id'), $field['field_name'], $prev, $curr, $field['field_name'] . ' Updated', $this->api->auth->get('id'), $_GET['app_page_id'], $field['application_pages_sections_id']);
                    }
                }else {
                    if ($f->model->get($field['field_name']) != $f->get($field['field_name'])) {

                        $hist->history_push($f->model->get('site_id'), $field['field_name'], $prev, $curr, $field['field_name'] . ' Updated', $this->api->auth->get('id'), $_GET['app_page_id'], $field['application_pages_sections_id']);
                    }
                }
            }

            $f->save()->js(null, $js)->univ()->successMessage($_GET['app_page'] . ' Updated!')->execute();
        }
    }

}
