<?php
/**
 * Created by IntelliJ IDEA.
 * User: WRheeder
 * Date: 15/02/2017
 * Time: 11:08 AM
 */

class Page_uploadLTE extends Page
{
    function init()
    {
        parent::init();
        $pb = $this->add('Progressbar\View_Progressbar');
        $pb1 = $this->add('Progressbar\View_Progressbar');
        $done = $this->add('Button')->set('Done');
        $done->js('click', $this->js()->univ()->closeDialog())->hide();
        $done->js(true)->hide();
        $c = $this->add('View_Console')->set(function($c) use ($done,$pb,$pb1){
            $c->out('LTE Loading Started');
            //sleep(50);
            $x=0;
            $files = glob("./LTE/*.csv");
            $num_files = count($files);
            $file_order = array();
            $files_proc = 1;
            $i=0;

            foreach ($files as $key => $val){
                $file_order[$val] = filemtime($val);
            }
            array_multisort($file_order, SORT_ASC, $files);

            // die(print_r($file_order));
            foreach($file_order as $file=>$dte) {
                $x=($files_proc/$num_files)*100;
                $c->jsEval($this->js()->univ()->updateProgress($pb,$x));
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
                $files_proc++;
                $c->out('Processing File :' . $file);
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
                    $y = ($i/count($input))*100;;
                    $c->jsEval($this->js()->univ()->updateProgress($pb1,$y));
                    if($i==0){
                        foreach ($r as $key=>$heading) {
                            $header_array[$heading]=$j;
                            $j++;
                        }
                    }
                    if ($i > 0 && is_array($r)) {

                        $cgi = '655-07-'.$r[$header_array['TAC']].'-'.$r[$header_array['ENODEBID']].$r[$header_array['CELLID']];
                        //$c->err($cgi);
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
                            $holding_array[$cgi]['dlearfcn'] = $r[$header_array['DLEARFCN']]!='\N'?$r[$header_array['DLEARFCN']]:null;
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
                            if($k !== false && $k != null && $name != '' && $name != null && $name!='/N' && $holding_array[$cgi]['enodebid']==$cgilte_array[array_keys($cgilte_array)[$k]]['enodebid'])
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
                                $holding_array[$cgi]['id']=$cgilte->id;
                                $cgilte_array[$cgi]=$holding_array[$cgi];
                                $cgilte->unload();
                            }

                        }
                    }
                    $i++;

                }

                $to_delete = array_diff_key($cgi_ids,$holding_array);
                //die(print_r($to_delete));
                foreach ($to_delete as $e=>$del_id) {
                    //die(print_r($to_delete));
                    $cgilte->load($del_id);
                    if (!$cgilte['removed']) {
                        $cgilte->set('removed', 1);
                        $cgilte->save();
                        $cgilte_history->set('cgi_lte_id', $del_id);
                        $cgilte_history->set('fld', 'cgi - removed');
                        $cgilte_history->set('previous', $e);
                        $cgilte_history->set('current', 'removed');
                        $cgilte_history->set('changed_on', $ul_date);
                        $cgilte_history->set('cgi_filename', basename($file));
                        $cgilte_history->saveAndUnload();
                    }
                }
            }


            $c->jsEval($done->js(true)->show());
        });
    }
}