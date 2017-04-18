<?php
/**
 * Created by IntelliJ IDEA.
 * User: WRheeder
 * Date: 15/02/2017
 * Time: 8:48 AM
 */


class Page_upload3G extends Page
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
            $c->out('3G Loading Started');
            //sleep(50);
            $js = array();
            $x=0;
            $files = glob("./3G/*.csv");
            $num_files = count($files);
            $file_order = array();
            $i=0;
            $files_proc = 1;
            foreach ($files as $key => $val){
                $file_order[$val] = filemtime($val);
            }
            array_multisort($file_order, SORT_ASC, $files);

            // die(print_r($file_order));
            foreach($file_order as $file=>$dte) {
                $z=($files_proc/$num_files)*100;
                $c->jsEval($this->js()->univ()->updateProgress($pb,$z));
                $i++;
                $f = fopen($file, "r");
                $input = array();
                while (!feof($f)) {
                    $result = fgetcsv($f);
                    if (array(null) !== $result) { // ignore blank lines
                        $input[] = $result;
                    }
                }
                $files_proc++;
                fclose($f);
                $c->out('Processing File :' . $file);
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
                $c->jsEval($this->js()->univ()->updateProgress($pb1,0));
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
                foreach ($to_delete as $e=>$del_id) {
                    //die(print_r($to_delete));
                    $cgi3g->load($del_id);
                    if (!$cgi3g['removed']) {
                        $cgi3g->set('removed', 1);
                        $cgi3g->save();
                        $cgi3g_history->set('cgi_3g_id', $del_id);
                        $cgi3g_history->set('fld', 'cgi - removed');
                        $cgi3g_history->set('previous', $e);
                        $cgi3g_history->set('current', 'removed');
                        $cgi3g_history->set('changed_on', $ul_date);
                        $cgi3g_history->set('cgi_filename', basename($file));
                        $cgi3g_history->saveAndUnload();
                    }
                }
            }

            $c->jsEval($done->js(true)->show());
        });
    }
}