<?php
/**
 * Created by IntelliJ IDEA.
 * User: WRheeder
 * Date: 02/02/2017
 * Time: 9:01 AM
 */


class Page_BulkConfigUploader extends Page
{

    public $title = 'ConfigUploader';

    function init()
    {
        parent::init();

        $cols = $this->add('View_Columns');

        $left_col = $cols->addColumn('33%');
        $middle_col = $cols->addColumn('33%');
        $right_col = $cols->addColumn('33%');

        $fr_mid = $middle_col->add('Frame');
        $fr_lft = $left_col->add('Frame');
        $fr_rgt = $right_col->add('Frame');

        $f_2g = $fr_lft->add('Form');
        $f_3g = $fr_mid->add('Form');
        $f_lte = $fr_rgt->add('Form');

        $progressBar =  $left_col->add('Progressbar\View_Progressbar');

        $progressBar1 =  $middle_col->add('Progressbar\View_Progressbar');
        $progressBar2 =  $right_col->add('Progressbar\View_Progressbar');

        $f_lte->addSubmit('Process',5)->js('click',$progressBar2->js()->univ()->updateProgress($progressBar2,0));
        $f_3g->addSubmit('Process',5)->js('click',$progressBar1->js()->univ()->updateProgress($progressBar1,0));
        $f_2g->addSubmit('Process',5)->js('click',$progressBar->js()->univ()->updateProgress($progressBar,0));
     //   $f_lte->addSubmit('Process');


                        //echo '<script language="javascript">document.getElementById("'.$progressBar.'").innerHTML="<div style=\"width:'.$x.';background-color:#ddd;\">&nbsp;</div>";</script>';

        $c1=$f_2g->add('View_Console');


        if ($f_2g->isSubmitted()) {
            $c1->out('test');
            $js = array();
            $x=0;
            $files = glob("./2G/*.csv");
            $num_files = count($files);
            $file_order = array();
            $i=0;

            foreach ($files as $key => $val){
                $file_order[$val] = filemtime($val);
            }
            array_multisort($file_order, SORT_ASC, $files);

            // die(print_r($file_order));
            foreach($file_order as $file=>$dte) {
                $x=($i/$num_files)*100;
                $js[]=$this->js()->univ()->updateProgress($progressBar,$x);
                $i++;
                $f = fopen($file, "r");
                $input = array();
                while (!feof($f)) {
                    $result = fgetcsv($f);
                    if (array(null) !== $result) { // ignore blank lines
                        $input[] = $result;
                    }
                }

                fclose($f);

                $ul_date = date('Y/m/d h:i:s',$dte);

                $cgi2g = $this->add('Model_CGI2G');
                $cgi2g_history = $this->add('Model_CGI2GHistory');

                $cgi2g_array = $cgi2g->getRows();
                $cgi2g_array_new = array();
                $cgi_ids = array();
                foreach ($cgi2g_array as $key => $val) {
                    $cgi2g_array_new[$val['cgi']] = $val;
                    $cgi_ids[$val['cgi']] = $val['id'];
                }
                $cgi2g_array = $cgi2g_array_new;
                unset($cgi2g_array_new);
                $holding_array = array();
                $i=0;
                $header_array = array();
                $j=0;
                foreach ($input as $r) {

                    if($i==0){
                        foreach ($r as $key=>$heading) {
                            $header_array[$heading]=$j;
                            $j++;
                        }
                    }
                    if ($i > 0 && is_array($r)) {
                        $r[$header_array['CGI']] = str_replace('[', '', $r[$header_array['CGI']]);
                        $r[$header_array['CGI']] = str_replace(']', '', $r[$header_array['CGI']]);
                        $r[$header_array['CGI']] = str_replace('-7-', '-07-', $r[$header_array['CGI']]);
                        $r[$header_array['CountOfTRXs']] = str_replace('\N', '0', $r[$header_array['CountOfTRXs']]);  //TRX Count \N replaced
                        $cgi = $r[$header_array['CGI']];
                        $name = $r[$header_array['NAME']];
                        if (!isset($holding_array[$cgi])) {
                            $holding_array[$cgi]['cgi'] = $cgi;
                            $holding_array[$cgi]['vendor'] = $r[$header_array['VENDOR']];
                            $holding_array[$cgi]['region'] = $r[$header_array['REGION']];
                            $holding_array[$cgi]['bscname'] = $r[$header_array['BSCNAME']];
                            $holding_array[$cgi]['bscid'] = $r[$header_array['BSCID']];
                            $holding_array[$cgi]['siteid'] = $r[$header_array['SITEID']];
                            $holding_array[$cgi]['sitename'] = $r[$header_array['SITENAME']];
                            $holding_array[$cgi]['name'] = $r[$header_array['NAME']];
                            $holding_array[$cgi]['btsid'] = $r[$header_array['BTSID']];
                            $holding_array[$cgi]['ci'] = $r[$header_array['CI']];
                            $holding_array[$cgi]['lac'] = $r[$header_array['LAC']];
                            $holding_array[$cgi]['rac'] = $r[$header_array['RAC']];
                            $holding_array[$cgi]['bcch'] = $r[$header_array['BCCH']];
                            $holding_array[$cgi]['ncc'] = $r[$header_array['NCC']];
                            $holding_array[$cgi]['bcc'] = $r[$header_array['BCC']];
                            $holding_array[$cgi]['trx_cnt'] = $r[$header_array['CountOfTRXs']];
                        }

                        if (isset($cgi2g_array[$cgi])) {

                            //a - CGI found in previous CGI report//
                            if (!$cgi2g_array[$cgi]['removed']) {
                                foreach ($holding_array[$cgi] as $key => $value) {
                                    if ($cgi2g_array[$cgi][$key] != $holding_array[$cgi][$key] && $key != 'id') {
                                        ///params changed
                                        //
                                        $cgi2g_history->set('cgi_2g_id', $cgi2g_array[$cgi]['id']);
                                        $cgi2g_history->set('fld', $key);
                                        $cgi2g_history->set('previous', $cgi2g_array[$cgi][$key]);
                                        $cgi2g_history->set('current', $holding_array[$cgi][$key]);
                                        $cgi2g_history->set('changed_on', $ul_date);
                                        $cgi2g_history->set('cgi_filename', basename($file));
                                        $cgi2g_history->saveAndUnload();

                                        $cgi2g->tryLoadBy('cgi', $cgi);
                                        $cgi2g->set($key, $value);
                                        $cgi2g->saveAndUnload();

                                    } else {
//                                     echo 'same';
                                    }

                                }
                            }else{
                                $cgi2g_history->set('cgi_2g_id', $cgi2g_array[$cgi]['id']);
                                $cgi2g_history->set('fld', 'cgi - reactivated');
                                $cgi2g_history->set('previous', 'removed');
                                $cgi2g_history->set('current', $holding_array[$cgi]['cgi']);
                                $cgi2g_history->set('changed_on', $ul_date);
                                $cgi2g_history->set('cgi_filename', basename($file));
                                $cgi2g_history->saveAndUnload();
                                $cgi2g->tryLoadBy('cgi', $cgi);
                                $cgi2g->set('removed', 0);
                                $cgi2g->saveAndUnload();
                                foreach ($holding_array[$cgi] as $key => $value) {
                                    if ($cgi2g_array[$cgi][$key] != $holding_array[$cgi][$key] && $key != 'id') {
                                        ///params changed
                                        //
//                                $cgi2g_history->debug();
                                        $cgi2g_history->set('cgi_2g_id', $cgi2g_array[$cgi]['id']);
                                        $cgi2g_history->set('fld', $key);
                                        $cgi2g_history->set('previous', $cgi2g_array[$cgi][$key]);
                                        $cgi2g_history->set('current', $holding_array[$cgi][$key]);
                                        $cgi2g_history->set('changed_on', $ul_date);
                                        $cgi2g_history->set('cgi_filename', basename($file));
                                        $cgi2g_history->saveAndUnload();

                                        $cgi2g->tryLoadBy('cgi', $cgi);
                                        $cgi2g->set($key, $value);
                                        $cgi2g->saveAndUnload();

                                    } else {
//                                     echo 'same';
                                    }
                                }
                            }
                        } else {
                            //b - CGI not found in previous CGI report//
                            //
                            //// check for LAC change(Cutover)  + CI change(only possible to check on one cell sites)
                            $k = null;
                            //$k = array_column($cgi2g_array, 'name');
                            $k = array_search($name,array_column($cgi2g_array, 'name'));
                            //die(print_r(array_column($cgi2g_array, 'name')));
                            if($k !== false && $k !== null && $name != '' && $name != null && $name!='/N' && $holding_array[$cgi]['ci']==$cgi2g_array[array_keys($cgi2g_array)[$k]]['ci'])
                            {
                                $cutover_id = $cgi2g_array[array_keys($cgi2g_array)[$k]]['id'];

                                $cgi_ids[$cgi] = $cutover_id;
                                $cgi2g_history->set('cgi_2g_id', $cutover_id);
                                $cgi2g_history->set('fld', 'cgi - cutover');
                                $cgi2g_history->set('previous', $cgi2g_array[array_keys($cgi2g_array)[$k]]['cgi']);
                                $cgi2g_history->set('current', $holding_array[$cgi]['cgi']);
                                $cgi2g_history->set('changed_on', $ul_date);
                                $cgi2g_history->set('cgi_filename', basename($file));
                                $cgi2g_history->saveAndUnload();

                                if (!isset($holding_array[$cgi2g_array[array_keys($cgi2g_array)[$k]]['cgi']])) {
                                    $holding_array[$cgi2g_array[array_keys($cgi2g_array)[$k]]['cgi']]= $cgi2g_array[array_keys($cgi2g_array)[$k]];
                                }

                                foreach ($holding_array[$cgi] as $key => $value) {
                                    if ($cgi2g_array[array_keys($cgi2g_array)[$k]][$key] != $holding_array[$cgi][$key] && $key != 'id') {
                                        ///params changed
                                        //
//                                $cgi2g_history->debug();
                                        $cgi2g_history->set('cgi_2g_id', $cutover_id);
                                        $cgi2g_history->set('fld', $key);
                                        $cgi2g_history->set('previous', $cgi2g_array[array_keys($cgi2g_array)[$k]][$key]);
                                        $cgi2g_history->set('current', $holding_array[$cgi][$key]);
                                        $cgi2g_history->set('changed_on', $ul_date);
                                        $cgi2g_history->set('cgi_filename', basename($file));
                                        $cgi2g_history->saveAndUnload();

                                        $cgi2g->load($cutover_id);
                                        $cgi2g->set($key, $value);
                                        $cgi2g->saveAndUnload();
                                    } else {
//                                     echo 'same';
                                    }
                                }
                            }else {


                                // - search on site_name.  check if CI is in results, build new CGI and check if matches not found CGI.  ///cancelled for now to keep history and make it searchable for old CGIs
//                        $cgi2g->tryLoadBy('sitename', $holding_array[$cgi]['sitename']);
//                        if ($cgi2g->loaded()) {
//                            ///possible lac cutover, go through cells and check if cell exists with different lac
//                            foreach($cgi2g as $cell2G){
//
//                            }
//                        } else {
                                ///just normal new cell added to cgi
                                foreach ($holding_array[$cgi] as $key => $value) {
                                    //die(print_r($holding_array[$cgi]));
                                    if ($key != 'id') {
                                        $cgi2g->set($key, $value);
                                    }
                                }

                                $cgi2g->save();
                                $cgi2g_history->set('cgi_2g_id', $cgi2g->id);
                                $cgi_ids[$cgi] = $cgi2g->id;
                                $cgi2g_history->set('fld', 'cgi - added');
                                $cgi2g_history->set('previous', null);
                                $cgi2g_history->set('current', $holding_array[$cgi]['cgi']);
                                $cgi2g_history->set('changed_on', $ul_date);
                                $cgi2g_history->set('cgi_filename', basename($file));
                                $cgi2g_history->saveAndUnload();
                                $cgi2g->unload();
                            }

                        }
                    }
                    $i++;

                }

                $to_delete = array_diff_key($cgi_ids,$holding_array);
                //die(print_r($to_delete));
                foreach ($to_delete as $c=>$del_id) {
                    //die(print_r($to_delete));
                    $cgi2g->load($del_id);
                    if (!$cgi2g['removed']) {
                        $cgi2g->set('removed', 1);
                        $cgi2g->save();
                        $cgi2g_history->set('cgi_2g_id', $del_id);
                        $cgi2g_history->set('fld', 'cgi - removed');
                        $cgi2g_history->set('previous', $c);
                        $cgi2g_history->set('current', 'removed');
                        $cgi2g_history->set('changed_on', $ul_date);
                        $cgi2g_history->set('cgi_filename', basename($file));
                        $cgi2g_history->saveAndUnload();
                    }
                }
            }
            $this->js(true, $js)->univ()->successMessage('Files processed')->execute();
        }

        //$this->setTDParam($progressBar1, 'width', '50');
        if ($f_3g->isSubmitted()) {
            $js = array();
            $x=0;
            $files = glob("./3G/*.csv");
            $num_files = count($files);
            $file_order = array();
            $i=0;

            foreach ($files as $key => $val){
                $file_order[$val] = filemtime($val);
            }
            array_multisort($file_order, SORT_ASC, $files);

            // die(print_r($file_order));
            foreach($file_order as $file=>$dte) {
                $x=($i/$num_files)*100;
                $js[]=$this->js()->univ()->updateProgress($progressBar1,$x);
                $i++;
                $f = fopen($file, "r");
                $input = array();
                while (!feof($f)) {
                    $result = fgetcsv($f);
                    if (array(null) !== $result) { // ignore blank lines
                        $input[] = $result;
                    }
                }

                fclose($f);

                $ul_date = date('Y/m/d h:i:s',$dte);

                $cgi3g = $this->add('Model_CGI3G');
                $cgi3g_history = $this->add('Model_CGI3GHistory');

                $cgi3g_array = $cgi3g->getRows();
                $cgi3g_array_new = array();
                $cgi_ids = array();
                foreach ($cgi3g_array as $key => $val) {
                    $cgi3g_array_new[$val['cgi']] = $val;
                    $cgi_ids[$val['cgi']] = $val['id'];
                }
                $cgi3g_array = $cgi3g_array_new;
                unset($cgi3g_array_new);
                $holding_array = array();
                $i=0;
                $header_array = array();
                $j=0;
                foreach ($input as $r) {

                    if($i==0){
                        foreach ($r as $key=>$heading) {
                            $header_array[$heading]=$j;
                            $j++;
                        }
                    }
                    if ($i > 0 && is_array($r)) {
                        $r[$header_array['CGI']] = str_replace('[', '', $r[$header_array['CGI']]);
                        $r[$header_array['CGI']] = str_replace(']', '', $r[$header_array['CGI']]);
                        $r[$header_array['CGI']] = str_replace('-7-', '-07-', $r[$header_array['CGI']]);
                        $cgi = $r[$header_array['CGI']];
                        $name = $r[$header_array['NAME']];
                        if (!isset($holding_array[$cgi])) {
                            $holding_array[$cgi]['cgi'] = $cgi;
                            $holding_array[$cgi]['vendor'] = $r[$header_array['VENDOR']];
                            $holding_array[$cgi]['region'] = $r[$header_array['REGION']];
                            $holding_array[$cgi]['rncname'] = $r[$header_array['RNCNAME']];
                            $holding_array[$cgi]['rncid'] = $r[$header_array['RNCID']];
                            $holding_array[$cgi]['siteid'] = $r[$header_array['SITEID']];
                            $holding_array[$cgi]['sitename'] = $r[$header_array['SITENAME']];
                            $holding_array[$cgi]['name'] = $r[$header_array['NAME']];
                            $holding_array[$cgi]['btsid'] = $r[$header_array['BTSID']];
                            $holding_array[$cgi]['ci'] = $r[$header_array['CI']];
                            $holding_array[$cgi]['lac'] = $r[$header_array['LAC']];
                            $holding_array[$cgi]['rac'] = $r[$header_array['RAC']];
                            $holding_array[$cgi]['uarfcn'] = $r[$header_array['UARFCN']];
                            $holding_array[$cgi]['priscrcode'] = $r[$header_array['PRISCRCODE']];
                            $holding_array[$cgi]['cpichpower'] = $r[$header_array['CPICHPOWER']];
                            $holding_array[$cgi]['totalpower'] = $r[$header_array['TOTALPOWER']];
                        }

                        if (isset($cgi3g_array[$cgi])) {

                            //a - CGI found in previous CGI report//
                            if (!$cgi3g_array[$cgi]['removed']) {
                                foreach ($holding_array[$cgi] as $key => $value) {
                                    if ($cgi3g_array[$cgi][$key] != $holding_array[$cgi][$key] && $key != 'id') {
                                        ///params changed
                                        //
                                        $cgi3g_history->set('cgi_3g_id', $cgi3g_array[$cgi]['id']);
                                        $cgi3g_history->set('fld', $key);
                                        $cgi3g_history->set('previous', $cgi3g_array[$cgi][$key]);
                                        $cgi3g_history->set('current', $holding_array[$cgi][$key]);
                                        $cgi3g_history->set('changed_on', $ul_date);
                                        $cgi3g_history->set('cgi_filename', basename($file));
                                        $cgi3g_history->saveAndUnload();

                                        $cgi3g->tryLoadBy('cgi', $cgi);
                                        $cgi3g->set($key, $value);
                                        $cgi3g->saveAndUnload();

                                    } else {
//                                     echo 'same';
                                    }

                                }
                            }else{
                                $cgi3g_history->set('cgi_3g_id', $cgi3g_array[$cgi]['id']);
                                $cgi3g_history->set('fld', 'cgi - reactivated');
                                $cgi3g_history->set('previous', 'removed');
                                $cgi3g_history->set('current', $holding_array[$cgi]['cgi']);
                                $cgi3g_history->set('changed_on', $ul_date);
                                $cgi3g_history->set('cgi_filename', basename($file));
                                $cgi3g_history->saveAndUnload();
                                $cgi3g->tryLoadBy('cgi', $cgi);
                                $cgi3g->set('removed', 0);
                                $cgi3g->saveAndUnload();
                                foreach ($holding_array[$cgi] as $key => $value) {
                                    if ($cgi3g_array[$cgi][$key] != $holding_array[$cgi][$key] && $key != 'id') {
                                        ///params changed
                                        //
//                                $cgi3g_history->debug();
                                        $cgi3g_history->set('cgi_3g_id', $cgi3g_array[$cgi]['id']);
                                        $cgi3g_history->set('fld', $key);
                                        $cgi3g_history->set('previous', $cgi3g_array[$cgi][$key]);
                                        $cgi3g_history->set('current', $holding_array[$cgi][$key]);
                                        $cgi3g_history->set('changed_on', $ul_date);
                                        $cgi3g_history->set('cgi_filename', basename($file));
                                        $cgi3g_history->saveAndUnload();

                                        $cgi3g->tryLoadBy('cgi', $cgi);
                                        $cgi3g->set($key, $value);
                                        $cgi3g->saveAndUnload();

                                    } else {
//                                     echo 'same';
                                    }
                                }
                            }
                        } else {
                            //b - CGI not found in previous CGI report//
                            //
                            //// check for LAC change(Cutover)  + CI change(only possible to check on one cell sites)
                            $k = null;
                            //$k = array_column($cgi3g_array, 'name');
                            $k = array_search($name,array_column($cgi3g_array, 'name'));
                            //die(print_r(array_column($cgi3g_array, 'name')));
                            if($k !== false && $k !== null && $name != '' && $name != null && $name!='/N' && $holding_array[$cgi]['ci']==$cgi3g_array[array_keys($cgi3g_array)[$k]]['ci'])
                            {
                                $cutover_id = $cgi3g_array[array_keys($cgi3g_array)[$k]]['id'];

                                $cgi_ids[$cgi] = $cutover_id;
                                $cgi3g_history->set('cgi_3g_id', $cutover_id);
                                $cgi3g_history->set('fld', 'cgi - cutover');
                                $cgi3g_history->set('previous', $cgi3g_array[array_keys($cgi3g_array)[$k]]['cgi']);
                                $cgi3g_history->set('current', $holding_array[$cgi]['cgi']);
                                $cgi3g_history->set('changed_on', $ul_date);
                                $cgi3g_history->set('cgi_filename', basename($file));
                                $cgi3g_history->saveAndUnload();

                                if (!isset($holding_array[$cgi3g_array[array_keys($cgi3g_array)[$k]]['cgi']])) {
                                    $holding_array[$cgi3g_array[array_keys($cgi3g_array)[$k]]['cgi']]= $cgi3g_array[array_keys($cgi3g_array)[$k]];
                                }

                                foreach ($holding_array[$cgi] as $key => $value) {
                                    if ($cgi3g_array[array_keys($cgi3g_array)[$k]][$key] != $holding_array[$cgi][$key] && $key != 'id') {
                                        ///params changed
                                        //
//                                $cgi3g_history->debug();
                                        $cgi3g_history->set('cgi_3g_id', $cutover_id);
                                        $cgi3g_history->set('fld', $key);
                                        $cgi3g_history->set('previous', $cgi3g_array[array_keys($cgi3g_array)[$k]][$key]);
                                        $cgi3g_history->set('current', $holding_array[$cgi][$key]);
                                        $cgi3g_history->set('changed_on', $ul_date);
                                        $cgi3g_history->set('cgi_filename', basename($file));
                                        $cgi3g_history->saveAndUnload();

                                        $cgi3g->load($cutover_id);
                                        $cgi3g->set($key, $value);
                                        $cgi3g->saveAndUnload();
                                    } else {
//                                     echo 'same';
                                    }
                                }
                            }else {


                                // - search on site_name.  check if CI is in results, build new CGI and check if matches not found CGI.  ///cancelled for now to keep history and make it searchable for old CGIs
//                        $cgi3g->tryLoadBy('sitename', $holding_array[$cgi]['sitename']);
//                        if ($cgi3g->loaded()) {
//                            ///possible lac cutover, go through cells and check if cell exists with different lac
//                            foreach($cgi2g as $cell2G){
//
//                            }
//                        } else {
                                ///just normal new cell added to cgi
                                foreach ($holding_array[$cgi] as $key => $value) {
                                    //die(print_r($holding_array[$cgi]));
                                    if ($key != 'id') {
                                        $cgi3g->set($key, $value);
                                    }
                                }

                                $cgi3g->save();
                                $cgi3g_history->set('cgi_3g_id', $cgi3g->id);
                                $cgi_ids[$cgi] = $cgi3g->id;
                                $cgi3g_history->set('fld', 'cgi - added');
                                $cgi3g_history->set('previous', null);
                                $cgi3g_history->set('current', $holding_array[$cgi]['cgi']);
                                $cgi3g_history->set('changed_on', $ul_date);
                                $cgi3g_history->set('cgi_filename', basename($file));
                                $cgi3g_history->saveAndUnload();
                                $cgi3g->unload();
                            }

                        }
                    }
                    $i++;

                }

                $to_delete = array_diff_key($cgi_ids,$holding_array);
                //die(print_r($to_delete));
                foreach ($to_delete as $c=>$del_id) {
                    //die(print_r($to_delete));
                    $cgi3g->load($del_id);
                    if (!$cgi3g['removed']) {
                        $cgi3g->set('removed', 1);
                        $cgi3g->save();
                        $cgi3g_history->set('cgi_3g_id', $del_id);
                        $cgi3g_history->set('fld', 'cgi - removed');
                        $cgi3g_history->set('previous', $c);
                        $cgi3g_history->set('current', 'removed');
                        $cgi3g_history->set('changed_on', $ul_date);
                        $cgi3g_history->set('cgi_filename', basename($file));
                        $cgi3g_history->saveAndUnload();
                    }
                }
            }
            $this->js(true, $js)->univ()->successMessage('Files processed')->execute();
        }

        if ($f_lte->isSubmitted()) {
            $js = array();
            $x=0;
            $files = glob("./LTE/*.csv");
            $num_files = count($files);
            $file_order = array();
            $i=0;

            foreach ($files as $key => $val){
                $file_order[$val] = filemtime($val);
            }
            array_multisort($file_order, SORT_ASC, $files);

            // die(print_r($file_order));
            foreach($file_order as $file=>$dte) {
                $x=($i/$num_files)*100;
                $js[]=$this->js()->univ()->updateProgress($progressBar2,$x);
                $i++;
                $f = fopen($file, "r");
                $input = array();
                while (!feof($f)) {
                    $result = fgetcsv($f);
                    if (array(null) !== $result) { // ignore blank lines
                        $input[] = $result;
                    }
                }

                fclose($f);

                $ul_date = date('Y/m/d h:i:s',$dte);

                $cgilte = $this->add('Model_CGILTE');
                $cgilte_history = $this->add('Model_CGILTEHistory');

                $cgilte_array = $cgilte->getRows();
                $cgilte_array_new = array();
                $cgi_ids = array();
                foreach ($cgilte_array as $key => $val) {
                    $cgilte_array_new[$val['cgi']] = $val;
                    $cgi_ids[$val['cgi']] = $val['id'];
                }
                $cgilte_array = $cgilte_array_new;
                unset($cgilte_array_new);
                $holding_array = array();
                $i=0;
                $header_array = array();
                $j=0;
                foreach ($input as $r) {

                    if($i==0){
                        foreach ($r as $key=>$heading) {
                            $header_array[$heading]=$j;
                            $j++;
                        }
                    }
                    if ($i > 0 && is_array($r)) {

                        $cgi = '655-07-'.$r[$header_array['TAC']].'-'.$r[$header_array['ENODEBID']].$r[$header_array['CELLID']];
                        $name = $r[$header_array['NAME']];
                        if (!isset($holding_array[$cgi])) {
                            $holding_array[$cgi]['cgi'] = $cgi;
                            $holding_array[$cgi]['vendor'] = $r[$header_array['VENDOR']];
                            $holding_array[$cgi]['region'] = $r[$header_array['REGION']];
                            $holding_array[$cgi]['enodebid'] = $r[$header_array['ENODEBID']];
                            $holding_array[$cgi]['sitename'] = $r[$header_array['SITENAME']];
                            $holding_array[$cgi]['name'] = $r[$header_array['NAME']];
                            $holding_array[$cgi]['cellid'] = $r[$header_array['CELLID']];
                            $holding_array[$cgi]['phycellid'] = $r[$header_array['PHYCELLID']];
                            $holding_array[$cgi]['tac'] = $r[$header_array['TAC']];
                            $holding_array[$cgi]['dlearfcn'] = $r[$header_array['DLEARFCN']];
                        }

                        if (isset($cgilte_array[$cgi])) {

                            //a - CGI found in previous CGI report//
                            if (!$cgilte_array[$cgi]['removed']) {
                                foreach ($holding_array[$cgi] as $key => $value) {
                                    if ($cgilte_array[$cgi][$key] != $holding_array[$cgi][$key] && $key != 'id') {
                                        ///params changed
                                        //
                                        $cgilte_history->set('cgi_lte_id', $cgilte_array[$cgi]['id']);
                                        $cgilte_history->set('fld', $key);
                                        $cgilte_history->set('previous', $cgilte_array[$cgi][$key]);
                                        $cgilte_history->set('current', $holding_array[$cgi][$key]);
                                        $cgilte_history->set('changed_on', $ul_date);
                                        $cgilte_history->set('cgi_filename', basename($file));
                                        $cgilte_history->saveAndUnload();

                                        $cgilte->tryLoadBy('cgi', $cgi);
                                        $cgilte->set($key, $value);
                                        $cgilte->saveAndUnload();

                                    } else {
//                                     echo 'same';
                                    }

                                }
                            }else{
                                $cgilte_history->set('cgi_lte_id', $cgilte_array[$cgi]['id']);
                                $cgilte_history->set('fld', 'cgi - reactivated');
                                $cgilte_history->set('previous', 'removed');
                                $cgilte_history->set('current', $holding_array[$cgi]['cgi']);
                                $cgilte_history->set('changed_on', $ul_date);
                                $cgilte_history->set('cgi_filename', basename($file));
                                $cgilte_history->saveAndUnload();
                                $cgilte->tryLoadBy('cgi', $cgi);
                                $cgilte->set('removed', 0);
                                $cgilte->saveAndUnload();
                                foreach ($holding_array[$cgi] as $key => $value) {
                                    if ($cgilte_array[$cgi][$key] != $holding_array[$cgi][$key] && $key != 'id') {
                                        ///params changed
                                        //
//                                $cgilte_history->debug();
                                        $cgilte_history->set('cgi_lte_id', $cgilte_array[$cgi]['id']);
                                        $cgilte_history->set('fld', $key);
                                        $cgilte_history->set('previous', $cgilte_array[$cgi][$key]);
                                        $cgilte_history->set('current', $holding_array[$cgi][$key]);
                                        $cgilte_history->set('changed_on', $ul_date);
                                        $cgilte_history->set('cgi_filename', basename($file));
                                        $cgilte_history->saveAndUnload();

                                        $cgilte->set($key, $value);
                                        $cgilte->saveAndUnload();

                                    } else {
//                                     echo 'same';
                                    }
                                }
                            }
                        } else {
                            //b - CGI not found in previous CGI report//
                            //
                            //// check for LAC change(Cutover)  + CI change(only possible to check on one cell sites)
                            $k = null;
                            //$k = array_column($cgi3g_array, 'name');
                            $k = array_search($name,array_column($cgilte_array, 'name'));
                            //die(print_r(array_column($cgi3g_array, 'name')));
                            if($k !== false && $k !== null && $name != '' && $name != null && $name!='/N' && $holding_array[$cgi]['ci']==$cgilte_array[array_keys($cgilte_array)[$k]]['ci'])
                            {
                                $cutover_id = $cgilte_array[array_keys($cgilte_array)[$k]]['id'];

                                $cgi_ids[$cgi] = $cutover_id;
                                $cgilte_history->set('cgi_lte_id', $cutover_id);
                                $cgilte_history->set('fld', 'cgi - cutover');
                                $cgilte_history->set('previous', $cgilte_array[array_keys($cgilte_array)[$k]]['cgi']);
                                $cgilte_history->set('current', $holding_array[$cgi]['cgi']);
                                $cgilte_history->set('changed_on', $ul_date);
                                $cgilte_history->set('cgi_filename', basename($file));
                                $cgilte_history->saveAndUnload();

                                if (!isset($holding_array[$cgilte_array[array_keys($cgilte_array)[$k]]['cgi']])) {
                                    $holding_array[$cgilte_array[array_keys($cgilte_array)[$k]]['cgi']]= $cgilte_array[array_keys($cgilte_array)[$k]];
                                }

                                foreach ($holding_array[$cgi] as $key => $value) {
                                    if ($cgilte_array[array_keys($cgilte_array)[$k]][$key] != $holding_array[$cgi][$key] && $key != 'id') {
                                        ///params changed
                                        //
//                                $cgilte_history->debug();
                                        $cgilte_history->set('cgi_lte_id', $cutover_id);
                                        $cgilte_history->set('fld', $key);
                                        $cgilte_history->set('previous', $cgilte_array[array_keys($cgilte_array)[$k]][$key]);
                                        $cgilte_history->set('current', $holding_array[$cgi][$key]);
                                        $cgilte_history->set('changed_on', $ul_date);
                                        $cgilte_history->set('cgi_filename', basename($file));
                                        $cgilte_history->saveAndUnload();

                                        $cgilte->load($cutover_id);
                                        $cgilte->set($key, $value);
                                        $cgilte->saveAndUnload();
                                    } else {
//                                     echo 'same';
                                    }
                                }
                            }else {


                                // - search on site_name.  check if CI is in results, build new CGI and check if matches not found CGI.  ///cancelled for now to keep history and make it searchable for old CGIs
//                        $cgi3g->tryLoadBy('sitename', $holding_array[$cgi]['sitename']);
//                        if ($cgi3g->loaded()) {
//                            ///possible lac cutover, go through cells and check if cell exists with different lac
//                            foreach($cgi2g as $cell2G){
//
//                            }
//                        } else {
                                ///just normal new cell added to cgi
                                foreach ($holding_array[$cgi] as $key => $value) {
                                    //die(print_r($holding_array[$cgi]));
                                    if ($key != 'id') {
                                        $cgilte->set($key, $value);
                                    }
                                }

                                $cgilte->save();
                                $cgilte_history->set('cgi_lte_id', $cgilte->id);
                                $cgi_ids[$cgi] = $cgilte->id;
                                $cgilte_history->set('fld', 'cgi - added');
                                $cgilte_history->set('previous', null);
                                $cgilte_history->set('current', $holding_array[$cgi]['cgi']);
                                $cgilte_history->set('changed_on', $ul_date);
                                $cgilte_history->set('cgi_filename', basename($file));
                                $cgilte_history->saveAndUnload();
                                $cgilte->unload();
                            }

                        }
                    }
                    $i++;

                }

                $to_delete = array_diff_key($cgi_ids,$holding_array);
                //die(print_r($to_delete));
                foreach ($to_delete as $c=>$del_id) {
                    //die(print_r($to_delete));
                    $cgilte->load($del_id);
                    if (!$cgilte['removed']) {
                        $cgilte->set('removed', 1);
                        $cgilte->save();
                        $cgilte_history->set('cgi_lte_id', $del_id);
                        $cgilte_history->set('fld', 'cgi - removed');
                        $cgilte_history->set('previous', $c);
                        $cgilte_history->set('current', 'removed');
                        $cgilte_history->set('changed_on', $ul_date);
                        $cgilte_history->set('cgi_filename', basename($file));
                        $cgilte_history->saveAndUnload();
                    }
                }
            }
            $this->js(true, $js)->univ()->successMessage('Files processed')->execute();
        }
    }
}
