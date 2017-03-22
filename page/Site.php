<?php

class Page_Site extends Page {

    public $title = 'Site';

    function init() {
        parent::init();

        $this->api->stickyGet('selected_tab');

        $this->js(true)->_selector('#NRT_layout_fluid_index_form_sel_tab')->val($_GET['selected_tab']);

        $regions = array('[Region]1' => 'Free State', '[Region]2' => 'Eastern Cape', '[Region]3' => 'Johannesburg', '[Region]4' => 'Gauteng', '[Region]5' => 'Kwazulu Natal', '[Region]6' => 'Western Cape');
        $this->api->stickyGet('sel_node');
        $m_site = $this->add('Model_Site');
        $site_id = 'New Site';
        $region_id = '';
        if (strpos($_GET['sel_node'], '[Site]') !== false)
            ;
        $site_id = str_replace('[Site]', '', $_GET['sel_node']);
        if (strpos($_GET['sel_node'], '[TX]') !== false)
            $site_id = str_replace('[TX]', '', $_GET['sel_node']);
        if (strpos($_GET['sel_node'], '[RF]') !== false)
            $site_id = str_replace('[RF]', '', $_GET['sel_node']);
        if (strpos($_GET['sel_node'], '[LEASE]') !== false)
            $site_id = str_replace('[LEASE]', '', $_GET['sel_node']);
        if (strpos($_GET['sel_node'], '[Region]') !== false)
            $region_id = str_replace('[Region]', '', $_GET['sel_node']);

        $v = $this->add('View_Columns');
        $input = $v->addColumn(6);
        $map_disp = $v->addColumn(6);

//        echo $site_id.' - '.$region_id;
//        var_dump(strpos($_GET['sel_node'], '[Site]'));
        $f = $input->add('Form');
        $this->api->addLocation(array('js' => 'atk4-addons/misc/templates/js'))->setParent($this->api->pathfinder->base_location);
        //$this->js()->_load('univ.google.map');

        $m = $map_disp->add('w_gm\View_Map'); //,array('libraries'=>array('drawing'),));
        $m->height = 700;
        $m->showMap();


        $fld_ro = false;
        if (strpos($_GET['sel_node'], '[Site]') !== false || strpos($_GET['sel_node'], '[RX]') !== false || strpos($_GET['sel_node'], '[TX]') !== false || strpos($_GET['sel_node'], '[LEASE]') !== false) {
            $info_disp = $f->add('View_Info');
            $info_disp->add('H3')->set('Site Info');
            $m_site->load($site_id);
            $m->setCenter($m_site->get('latitude'), $m_site->get('longitude'));
            $m->setZoom(15);
            $m->showMap();
            $m->setMarker(array('lat' => $m_site->get('latitude'), 'lng' => $m_site->get('longitude'), 'name' => $m_site->getMarkerHtml(), 'thumb' => '../vendor/jsTree/js/themes/default/red-broadcast-tower-icon_sml.png'));
            $fld_ro = true;
            $m->drawSector($m_site->get('latitude'), $m_site->get('longitude'),1500,90,36,45,'#000000','#FF0000',true);
        } else {
            $fld_ro = false;
            $site_id = 'New Site';
            $m_site->set('regions_id', $region_id);
            $m->setZoom(9);
            //$m->showMap();
            $m->codeAddress($regions[$_GET['sel_node']]);
            $ve = $f->add('View_Error')->set('Creating New Site in selected Region');
            $ve->template->tryDel('label');
            $ve->add('H3')->set('Creating New Site');
        }

        $field_arr = array('site_code', 'candidate_letter', 'site_name', 'candidate_status', 'latitude', 'longitude', 'lease_id', 'owner_id', 'colo_owner_id', 'site_type_id','build_type_id', 'site_name_used','on_air_status');
        if ($this->api->auth->get('can_change_region') && $site_id != 'New Site')
            $field_arr = array('site_code', 'candidate_letter', 'site_name', 'candidate_status', 'latitude', 'longitude', 'regions_id', 'lease_id', 'owner_id', 'colo_owner_id', 'site_type_id','build_type_id', 'site_name_used','on_air_status');
        $f->setModel($m_site, $field_arr);
        if ($fld_ro) {
            $f->getElement('site_code')->setAttr('readonly', true);
        }
        $f->getElement('latitude')->setMask('-d9.99?9999');
        $f->getElement('longitude')->setMask('99.99?9999');
       // $f->getElement('corr_lat')->setMask('-d9.99?9999');
       // $f->getElement('corr_lon')->setMask('99.99?9999');
        $f->addSubmit('Update');

        $h = $this->add('View_History')->set('History');
        $h->add('H3')->set('History');
        $h_icn_dwn = $h->add('Icon')->set('angle-down');
        $h_icn_dwn->js(true)->hide();
        $h_icn_up = $h->add('Icon')->set('angle-up');

        $m_hist = $this->add('Model_SiteLog');
        $m_hist->addCondition('site_id', $site_id);

        $hist_g = $h->add('Grid');
        $hist_g->setModel($m_hist);
        $hist_g->RemoveColumn('site');
        $hist_g->addPaginator(5);
        $js_hist = array();
        $js_hist_up[] = $h_icn_dwn->js()->show();
        $js_hist_up[] = $h_icn_up->js()->hide();
        $js_hist_up[] = $hist_g->js()->toggle();
        $js_hist_dwn[] = $h_icn_dwn->js()->hide();
        $js_hist_dwn[] = $h_icn_up->js()->show();
        $js_hist_dwn[] = $hist_g->js()->toggle();
        $h_icn_up->js('click', $js_hist_up);
        $h_icn_dwn->js('click', $js_hist_dwn);

        // die(var_dump($curr_class));
        if ($f->isSubmitted()) {
//            $f->getElement('site_code')->disable(false);
            $js = array();
            $js[] = $m->js()->reload();
            $hist = $this->add('Model_SiteLog');
            if ($m_site->loaded()) {
                if ($f->model->get('site_code') != $f->get('site_code')) {
                    $hist->history_push($f->model->get('id'), 'site_code', $f->model->get('site_code'), $f->get('site_code'), 'Site Code Updated', $this->api->auth->get('id'));
                }
                if ($f->model->get('candidate_letter') != $f->get('candidate_letter')) {
                    $hist->history_push($f->model->get('id'), 'candidate_letter', $f->model->get('candidate_letter'), $f->get('candidate_letter'), 'Candidate Letter Updated', $this->api->auth->get('id'));
                }
                if ($f->model->get('site_name') != $f->get('site_name')) {
                    $hist->history_push($f->model->get('id'), 'site_name', $f->model->get('site_name'), $f->get('site_name'), 'Site Name Updated', $this->api->auth->get('id'));
                }
                if ($f->model->get('latitude') != $f->get('latitude')) {
                    $hist->history_push($f->model->get('id'), 'latitude', $f->model->get('latitude'), $f->get('latitude'), 'Latitude Updated', $this->api->auth->get('id'));
                }
                if ($f->model->get('longitude') != $f->get('longitude')) {
                    $hist->history_push($f->model->get('id'), 'longitude', $f->model->get('longitude'), $f->get('longitude'), 'Longitude Updated', $this->api->auth->get('id'));
                }
                if ($f->model->get('corr_lon') != $f->get('corr_lon')) {
                    $hist->history_push($f->model->get('id'), 'coll_lon', $f->model->get('corr_lon'), $f->get('corr_lon'), 'Corrected Longitude Updated', $this->api->auth->get('id'));
                }
                if ($f->model->get('corr_lat') != $f->get('corr_lat')) {
                    $hist->history_push($f->model->get('id'), 'coll_lat', $f->model->get('corr_lat'), $f->get('corr_lat'), 'Corrected Latitude Updated', $this->api->auth->get('id'));
                }
                if ($f->model->get('regions_id') != $f->get('regions_id')) {
                    $reg = $this->api->db->dsql()->table('regions')->field('region')->where('id', $f->get('regions_id'))->do_getOne();
                    $hist->history_push($f->model->get('id'), 'region', $f->model->get('regions'), $reg, 'Region Updated', $this->api->auth->get('id'));
                }
                if ($f->model->get('owner_id') != $f->get('owner_id')) {
                    $owner = $this->api->db->dsql()->table('owner')->field('site_owner')->where('id', $f->get('owner_id'))->do_getOne();
                    $hist->history_push($f->model->get('id'), 'owner', $f->model->get('owner'), $owner, 'Owner Updated', $this->api->auth->get('id'));
                }
                if ($f->model->get('colo_owner_id') != $f->get('colo_owner_id')) {
                    $colo_owner = $this->api->db->dsql()->table('colo_owner')->field('colo_owner')->where('id', $f->get('colo_owner_id'))->do_getOne();
                    $hist->history_push($f->model->get('id'), 'colo_owner', $f->model->get('colo_owner'), $colo_owner, 'Colo-owner Updated', $this->api->auth->get('id'));
                }
                if ($f->model->get('site_type_id') != $f->get('site_type_id')) {
                    $site_type = $this->api->db->dsql()->table('site_type')->field('site_type')->where('id', $f->get('site_type_id'))->do_getOne();
                    $hist->history_push($f->model->get('id'), 'site_type', $f->model->get('site_type'), $site_type, 'Site Type Updated', $this->api->auth->get('id'));
                }
                if ($f->model->get('site_name_used') != $f->get('site_name_used')) {
                    $hist->history_push($f->model->get('id'), 'site_name_used', $f->model->get('site_name_used'), $f->get('site_name_used'), 'Site Name Used Updated', $this->api->auth->get('id'));
                }
                if ($f->model->get('build_type_id') != $f->get('build_type_id')) {
                    $build_type = $this->api->db->dsql()->table('build_type')->field('build_type')->where('id', $f->get('build_type_id'))->do_getOne();
                    $hist->history_push($f->model->get('id'), 'build_type', $f->model->get('build_type'), $build_type, 'Build Type Updated', $this->api->auth->get('id'));
                }
//                die(var_dump($f->get()));
//                if($f['site_code']=='1234') return $f->error('site_code','site exists');
                $site_check = $this->api->db->dsql()->table('site')->field('id')->where('site_code', $f->get('site_code'))->where('candidate_letter', $f->get('candidate_letter'))->do_getAll();
//                die(var_dump($site_check));
                if (count($site_check) === 0 || $m_site->id == $site_check[0]['id']) {
                    $f->update();
                    $js[] = $hist_g->js()->reload();
//                    $js[] = $this->js()->_selector('#NRT_layout_fluid_menu_form_2_form_submit')->click();
                    $this->js(true, $js)->univ()->successMessage('Updated Site!')->execute();
                } else {
                    return $f->error('site_code', 'Candidate for this site_code exists, change candidate letter');
                }
            } else {
                $site_check = $this->api->db->dsql()->table('site')->field('site_code')->where('site_code', $f->get('site_code'))->where('candidate_letter', $f->get('candidate_letter'))->do_getAll();
                if (count($site_check) === 0) {
                    $f->update();
                    $hist->history_push($f->model->get('id'), 'Site Created', null, null, 'New Site Created', $this->api->auth->get('id'));
                    $js[] = $this->js()->_selector('#NRT_layout_fluid_menu_form_2_form_submit')->click();
                    $this->js(true,$js)->univ()->successMessage('Site created!')->execute();
                } else {
                    return $f->error('site_code', 'Candidate for this site_code exists');
                }
            }
        }
    }

}
