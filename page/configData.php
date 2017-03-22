<?php
/**
 * Created by IntelliJ IDEA.
 * User: WRheeder
 * Date: 31/01/2017
 * Time: 2:40 PM
 */

class Page_ConfigData extends Page {

    public $title = 'ConfigDB';

    function init() {
        parent::init();

        $this->api->mm->destroy();

        $cols = $this->add('View_Columns');

        $left_col = $cols->addColumn('33%');
        $middle_col = $cols->addColumn('33%');
        $right_col = $cols->addColumn('33%');

        $fr_mid = $middle_col->add('Frame');
        $fr_lft = $left_col->add('Frame');
        $fr_rgt = $right_col->add('Frame');

        $f_3g = $fr_lft->add('Form');
        $f_2g = $fr_mid->add('Form');
        $f_lte = $fr_rgt->add('Form');

        $fr_mid_1 = $fr_mid->add('Frame');
        $fr_lft_1 = $fr_lft->add('Frame');
        $fr_rgt_1 = $fr_rgt->add('Frame');

        $ul_3g = $f_3g->addField('Upload', 'file_id', '3GCGI')->validateNotNull('Please upload file first');
        $fs_3g = $ul_3g->setModel('filestore/File');
        $ul_2g = $f_2g->addField('Upload', 'file_id', '2GCGI')->validateNotNull('Please upload file first');
        $fs_2g = $ul_2g->setModel('filestore/File');
        $ul_lte = $f_lte->addField('Upload', 'file_id', 'LTECGI')->validateNotNull('Please upload file first');
        $fs_lte = $ul_lte->setModel('filestore/File');


        $cgi2g = $this->add('Model_CGI2G');
        $cgi2g_holding = $this->add('Model_CGI2GHolding');
        $cgi2g_history = $this->add('Model_CGI2GHistory');

        $cgi3g = $this->add('Model_CGI3G');
        $cgi3g_holding = $this->add('Model_CGI3Gholding');
        $cgi3g_history = $this->add('Model_CGI3GHistory');

        $cgilte = $this->add('Model_CgiLTE');
        $cgilte_holding = $this->add('Model_CGILTEholding');
        $cgilte_history = $this->add('Model_CGILTEhistory');

        $cgi3g_tbl_grid = $fr_lft_1->add('grid');
        $cgi2g_tbl_grid = $fr_mid_1->add('grid');
        $cgilte_tbl_grid = $fr_rgt_1->add('grid');

        $cgi3g_tbl_grid->addPaginator(10);
        $cgi2g_tbl_grid->addPaginator(10);
        $cgilte_tbl_grid->addPaginator(10);

        $cgi3g_tbl_grid->setModel($cgi3g);
        $cgi2g_tbl_grid->setModel($cgi2g);
        $cgilte_tbl_grid->setModel($cgilte);

        $cgi3g_tbl_grid->addColumn('expander', 'History3G');
        $cgi2g_tbl_grid->addColumn('expander', 'History2G');
        $cgilte_tbl_grid->addColumn('expander', 'HistoryLTE');

        $cgi3g_tbl_grid->controller->importField('cgi');
        $cgi2g_tbl_grid->controller->importField('cgi');
        $cgilte_tbl_grid->controller->importField('cgi');

        $cgi3g_tbl_grid->addOrder()->move('cgi', 'first')->now();
        $cgi2g_tbl_grid->addOrder()->move('cgi', 'first')->now();
        $cgilte_tbl_grid->addOrder()->move('cgi', 'first')->now();
//        $cgi3g_tbl_grid->addExtendedSearch(array('cgi', 'name', 'lac'));
//        $cgi2g_tbl_grid->addExtendedSearch(array('cgi', 'name', 'lac'));
        $cgi3g_tbl_grid->addQuickSearch(array('cgi', 'name', 'lac','sitename'));
        $cgi2g_tbl_grid->addQuickSearch(array('cgi', 'name', 'lac','sitename'));
        $cgilte_tbl_grid->addQuickSearch(array('cgi', 'name', 'tac','sitename'));

        $fr_mid_2 = $fr_mid->add('Frame');
        $fr_lft_2 = $fr_lft->add('Frame');
        $fr_rgt_2 = $fr_rgt->add('Frame');

        $f_3g_holding = $fr_lft_2->add('Form');
        $sel_3g = $f_3g_holding->addField('sel_3g');
        $s3g = array();
        foreach($cgi3g_holding as $r){
            $s3g[] = $r['id'];
        }
        $sel_3g->set(json_encode($s3g));
        $sel_3g->js(true)->closest('.atk-form-row')->hide();
        $holding_tbl_grid = $fr_lft_2->add('grid');
        $holding_tbl_grid->setModel($cgi3g_holding);
        $holding_tbl_grid->controller->importField('cgi');
        $holding_tbl_grid->addOrder()->move('cgi', 'first')->now();
        $holding_tbl_grid->addPaginator(10);
        $holding_tbl_grid->addQuickSearch(array('cgi', 'name', 'lac'));
        $holding_tbl_grid->addSelectable($sel_3g);
        $holding_tbl_grid->addColumn('button', 'Delete3G');
//        $sel_all_3g = $f_3g_holding->addButton('Select All')->js('click',$holding_tbl_grid->js()->gridext_checkboxes('select_star'));;


        $f_2g_holding = $fr_mid_2->add('Form');
        $sel_2g = $f_2g_holding->addField('sel_2g');
        $s2g = array();
        foreach($cgi2g_holding as $r){
            $s2g[] = $r['id'];
        }
        $sel_2g->set(json_encode($s2g));
        $sel_2g->js(true)->closest('.atk-form-row')->hide();
        $holding_tbl_grid_2g = $fr_mid_2->add('grid');
        $holding_tbl_grid_2g->setModel($cgi2g_holding);
        $holding_tbl_grid_2g->controller->importField('cgi');
        $holding_tbl_grid_2g->addOrder()->move('cgi', 'first')->now();
        $holding_tbl_grid_2g->addPaginator(10);
        $holding_tbl_grid_2g->addQuickSearch(array('cgi', 'name', 'lac'));
        $holding_tbl_grid_2g->addSelectable($sel_2g);
        $holding_tbl_grid_2g->addColumn('button', 'Delete2G');

        $f_lte_holding = $fr_rgt_2->add('Form');
        $sel_lte = $f_lte_holding->addField('sel_lte');
        $slte = array();
        foreach($cgilte_holding as $r){
            $slte[] = $r['id'];
        }
        $sel_lte->set(json_encode($slte));
        $sel_lte->js(true)->closest('.atk-form-row')->hide();
        $holding_tbl_grid_lte = $fr_rgt_2->add('grid');
        $holding_tbl_grid_lte->setModel($cgilte_holding);
        $holding_tbl_grid_lte->controller->importField('cgi');
        $holding_tbl_grid_lte->addOrder()->move('cgi', 'first')->now();
        $holding_tbl_grid_lte->addPaginator(10);
        $holding_tbl_grid_lte->addQuickSearch(array('cgi', 'name', 'tac'));
        $holding_tbl_grid_lte->addSelectable($sel_lte);
        $holding_tbl_grid_lte->addColumn('button', 'DeleteLTE');


        $f_3g->addSubmit('Process');
        $f_2g->addSubmit('Process');
        $f_lte->addSubmit('Process');
        $f_3g_holding->addSubmit('Delete');
        $f_2g_holding->addSubmit('Delete');
        $f_lte_holding->addSubmit('Delete');




        if ($f_3g->isSubmitted()) {
            $cgi3g_holding->deleteAll();
            $cgi3g_array = $cgi3g->getRows();
            $cgi3g_array_new = array();
            foreach ($cgi3g_array as $key => $val) {
                $cgi3g_array_new[$val['cgi']] = $val;
            }

            $cgi3g_array = $cgi3g_array_new;
            unset($cgi3g_array_new);
            $holding_array = array();
            $ul_date = date('Y/m/d h:i:s', time());

            $fs_3g->tryLoad($ul_3g->form->data);
            $furl = './' . $fs_3g['dirname'] . '/' . $fs_3g['filename'];
            $file = fopen($furl, "r");
            $input = array();

            while (!feof($file)) {
                $result = fgetcsv($file);
                if (array(null) !== $result) { // ignore blank lines
                    $input[] = $result;
                }
            }
            fclose($file);

            $i = 0;


            foreach ($input as $r) {
                if ($i > 0 && is_array($r)) {
                    $r[15] = str_replace('[', '', $r[15]);
                    $r[15] = str_replace(']', '', $r[15]);
                    $r[15] = str_replace('-7-', '-07-', $r[15]);
                    $cgi = $r[15];

                    if (!isset($holding_array[$cgi])) {
                        $holding_array[$cgi]['cgi'] = $r[15];
                        $holding_array[$cgi]['vendor'] = $r[0];
                        $holding_array[$cgi]['region'] = $r[1];
                        // $cgi3g_holding->set('msc', $r[2]);
                        $holding_array[$cgi]['rncname'] = $r[2];
                        $holding_array[$cgi]['rncid'] = $r[3];
                        $holding_array[$cgi]['siteid'] = $r[4];
                        $holding_array[$cgi]['sitename'] = $r[5];
                        $holding_array[$cgi]['btsid'] = $r[6];
                        $holding_array[$cgi]['ci'] = $r[7];
                        $holding_array[$cgi]['lac'] = $r[8];
                        $holding_array[$cgi]['rac'] = $r[9];
                        $holding_array[$cgi]['name'] = $r[10];
                        $holding_array[$cgi]['uarfcn'] = $r[11];
                        $holding_array[$cgi]['priscrcode'] = $r[12];
                        $holding_array[$cgi]['cpichpower'] = $r[13];
                        $holding_array[$cgi]['totalpower'] = $r[14];
                    }
                    //$cgi3g->tryLoadBy('cgi', $cgi);
                    if (isset($cgi3g_array[$cgi])) {
                        //a - CGI found in previous CGI report//
                        if (!$cgi3g_array[$cgi]['removed']) {

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
                                    $cgi3g_history->set('cgi_filename', $fs_3g['original_filename']);
                                    $cgi3g_history->saveAndUnload();

                                    $cgi3g->tryLoadBy('cgi', $cgi);
                                    $cgi3g->set($key, $value);
                                    $cgi3g->saveAndUnload();
                                } else {
//                                     echo 'same';
                                }

                            }
                        } else {
                            $cgi3g_history->set('cgi_3g_id', $cgi3g_array[$cgi]['id']);
                            $cgi3g_history->set('fld', 'cgi - reactivated');
                            $cgi3g_history->set('previous', 'removed');
                            $cgi3g_history->set('current', $holding_array[$cgi]['cgi']);
                            $cgi3g_history->set('changed_on', $ul_date);
                            $cgi3g_history->set('cgi_filename', $fs_3g['original_filename']);
                            $cgi3g_history->saveAndUnload();


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
                                    $cgi3g_history->set('cgi_filename', $fs_3g['original_filename']);
                                    $cgi3g_history->saveAndUnload();

                                    $cgi3g->tryLoadBy('cgi', $cgi);
                                    $cgi3g->set($key, $value);
                                    $cgi3g->set('removed', 0);
                                    $cgi3g->saveAndUnload();
                                } else {
//                                     echo 'same';
                                }
                            }

                        }
                        //$cgi3g->saveAndUnload();
                    } else {
                        //b - CGI not found in previous CGI report//
                        //
                        //
                        foreach ($holding_array[$cgi] as $key => $value) {
                            if ($key != 'id') {
                                $cgi3g->set($key, $value);
                            }
                        }

                        $cgi3g->save();
                        $cgi3g_history->set('cgi_3g_id', $cgi3g->id);
                        $cgi3g_history->set('fld', 'cgi - added');
                        $cgi3g_history->set('previous', null);
                        $cgi3g_history->set('current', $holding_array[$cgi]['cgi']);
                        $cgi3g_history->set('changed_on', $ul_date);
                        $cgi3g_history->set('cgi_filename', $fs_3g['original_filename']);
                        $cgi3g_history->saveAndUnload();
                        $cgi3g->unload();
                    }
                }
                $i++;
            }

            $cgi3g->unload();
            $cgi3g_all = $cgi3g->getRows();
            //die(var_dump($cgi3g_all));
            foreach ($cgi3g_all as $cgi) {
                $curr_cgi = $cgi['cgi'];
                if (isset($holding_array[$curr_cgi])) {
                    unset($holding_array[$curr_cgi]);
                } else {
                    if ($cgi['removed'] == false) {
                        $holding_array[$curr_cgi]['cgi'] = $cgi['cgi'];
                        $holding_array[$curr_cgi]['vendor'] = $cgi['vendor'];
                        $holding_array[$curr_cgi]['cgiregion'] = $cgi['region'];
                        $holding_array[$curr_cgi]['msc'] = $cgi['msc'];
                        $holding_array[$curr_cgi]['rncname'] = $cgi['rncname'];
                        $holding_array[$curr_cgi]['rncid'] = $cgi['rncid'];
                        $holding_array[$curr_cgi]['siteid'] = $cgi['siteid'];
                        $holding_array[$curr_cgi]['sitename'] = $cgi['sitename'];
                        $holding_array[$curr_cgi]['btsid'] = $cgi['btsid'];
                        $holding_array[$curr_cgi]['ci'] = $cgi['ci'];
                        $holding_array[$curr_cgi]['lac'] = $cgi['lac'];
                        $holding_array[$curr_cgi]['rac'] = $cgi['rac'];
                        $holding_array[$curr_cgi]['name'] = $cgi['name'];
                        $holding_array[$curr_cgi]['uarfcn'] = $cgi['uarfcn'];
                        $holding_array[$curr_cgi]['priscrcode'] = $cgi['priscrcode'];
                        $holding_array[$curr_cgi]['cpichpower'] = $cgi['cpichpower'];
                        $holding_array[$curr_cgi]['totalpower'] = $cgi['totalpower'];
                    }
                }
            }
            foreach ($holding_array as $key => $val) {
                $cgi3g_holding->set($val);
                $cgi3g_holding->saveAndUnload();
            }

            $js = array();
            $js[] = $holding_tbl_grid->js()->reload();
            $this->js(true, $js)->univ()->successMessage('File processed')->execute();
        }
        //// 2G Submit
        if ($f_2g->isSubmitted()) {
            $cgi2g_holding->deleteAll();
            $cgi2g_array = array();
            $cgi2g_array = $cgi2g->getRows();
            $cgi2g_array_new = array();
            foreach ($cgi2g_array as $key => $val) {
                $cgi2g_array_new[$val['cgi']] = $val;
            }
            $cgi2g_array = $cgi2g_array_new;
            unset($cgi2g_array_new);
            $holding_array = array();
            $ul_date = date('Y/m/d h:i:s', time());

            $fs_2g->tryLoad($ul_2g->form->data);
            $furl = './' . $fs_2g['dirname'] . '/' . $fs_2g['filename'];
            $file = fopen($furl, "r");
            $input = array();

            while (!feof($file)) {
                $result = fgetcsv($file);
                if (array(null) !== $result) { // ignore blank lines
                    $input[] = $result;
                }
            }
            fclose($file);

            $i = 0;


            foreach ($input as $r) {
                if ($i > 0 && is_array($r)) {
                    $r[15] = str_replace('[', '', $r[15]);
                    $r[15] = str_replace(']', '', $r[15]);
                    $r[15] = str_replace('-7-', '-07-', $r[15]);
                    $r[14] = str_replace('\N', '0', $r[14]);  //TRX Count \N replaced
                    $cgi = $r[15];

                    if (!isset($holding_array[$cgi])) {
                        $holding_array[$cgi]['cgi'] = $r[15];
                        $holding_array[$cgi]['vendor'] = $r[0];
                        $holding_array[$cgi]['region'] = $r[1];
                        // $cgi2g_holding->set('msc', $r[2]);
                        $holding_array[$cgi]['bscname'] = $r[2];
                        $holding_array[$cgi]['bscid'] = $r[3];
                        $holding_array[$cgi]['siteid'] = $r[4];
                        $holding_array[$cgi]['sitename'] = $r[5];
                        $holding_array[$cgi]['name'] = $r[10];
                        $holding_array[$cgi]['btsid'] = $r[6];
                        $holding_array[$cgi]['ci'] = $r[7];
                        $holding_array[$cgi]['lac'] = $r[8];
                        $holding_array[$cgi]['rac'] = $r[9];
                        $holding_array[$cgi]['bcch'] = $r[11];
                        $holding_array[$cgi]['ncc'] = $r[12];
                        $holding_array[$cgi]['bcc'] = $r[13];
                        $holding_array[$cgi]['trx_cnt'] = $r[14];
                    }
                    //$cgi2g->tryLoadBy('cgi', $cgi);
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
                                    $cgi2g_history->set('cgi_filename', $fs_2g['original_filename']);
                                    $cgi2g_history->saveAndUnload();

                                    $cgi2g->tryLoadBy('cgi', $cgi);
                                    $cgi2g->set($key, $value);
                                    $cgi2g->saveAndUnload();

                                } else {
//                                     echo 'same';
                                }

                            }
                        } else {
                            $cgi2g_history->set('cgi_2g_id', $cgi2g_array[$cgi]['id']);
                            $cgi2g_history->set('fld', 'cgi - reactivated');
                            $cgi2g_history->set('previous', 'removed');
                            $cgi2g_history->set('current', $holding_array[$cgi]['cgi']);
                            $cgi2g_history->set('changed_on', $ul_date);
                            $cgi2g_history->set('cgi_filename', $fs_2g['original_filename']);
                            $cgi2g_history->saveAndUnload();

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
                                    $cgi2g_history->set('cgi_filename', $fs_2g['original_filename']);
                                    $cgi2g_history->saveAndUnload();

                                    $cgi2g->tryLoadBy('cgi', $cgi);
                                    $cgi2g->set($key, $value);
                                    $cgi2g->set('removed', 0);
                                    $cgi2g->saveAndUnload();
                                } else {
//                                     echo 'same';
                                }
                            }

                        }
                        //$cgi2g->saveAndUnload();
                    } else {
                        //b - CGI not found in previous CGI report//
                        //
                        //// check for LAC change(Cutover)  + CI change(only possible to check on one cell sites)
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
                            if ($key != 'id') {
                                $cgi2g->set($key, $value);
                            }
                        }

                        $cgi2g->save();
                        $cgi2g_history->set('cgi_2g_id', $cgi2g->id);
                        $cgi2g_history->set('fld', 'cgi - added');
                        $cgi2g_history->set('previous', null);
                        $cgi2g_history->set('current', $holding_array[$cgi]['cgi']);
                        $cgi2g_history->set('changed_on', $ul_date);
                        $cgi2g_history->set('cgi_filename', $fs_2g['original_filename']);
                        $cgi2g_history->saveAndUnload();
                        $cgi2g->unload();

//                        }
                    }
                }
                $i++;
            }

            $cgi2g->unload();
            $cgi2g_all = $cgi2g->getRows();
            //die(var_dump($cgi3g_all));
            foreach ($cgi2g_all as $cgi) {
                $curr_cgi = $cgi['cgi'];
                if (isset($holding_array[$curr_cgi])) {
                    unset($holding_array[$curr_cgi]);
                } else {
                    if ($cgi['removed'] == false) {
                        $holding_array[$curr_cgi]['cgi'] = $cgi['cgi'];
                        $holding_array[$curr_cgi]['vendor'] = $cgi['vendor'];
                        $holding_array[$curr_cgi]['region'] = $cgi['region'];
                        $holding_array[$curr_cgi]['bscname'] = $cgi['bscname'];
                        $holding_array[$curr_cgi]['bscid'] = $cgi['bscid'];
                        $holding_array[$curr_cgi]['siteid'] = $cgi['siteid'];
                        $holding_array[$curr_cgi]['sitename'] = $cgi['sitename'];
                        $holding_array[$curr_cgi]['name'] = $cgi['name'];
                        $holding_array[$curr_cgi]['btsid'] = $cgi['btsid'];
                        $holding_array[$curr_cgi]['ci'] = $cgi['ci'];
                        $holding_array[$curr_cgi]['lac'] = $cgi['lac'];
                        $holding_array[$curr_cgi]['rac'] = $cgi['rac'];
                        $holding_array[$curr_cgi]['bcch'] = $cgi['bcch'];
                        $holding_array[$curr_cgi]['ncc'] = $cgi['ncc'];
                        $holding_array[$curr_cgi]['bcc'] = $cgi['bcc'];
                        $holding_array[$curr_cgi]['trx_cnt'] = $cgi['trx_cnt'];
                    }
                }
            }
            foreach ($holding_array as $key => $val) {
                $cgi2g_holding->set($val);
                $cgi2g_holding->saveAndUnload();
            }

            $js = array();
            $js[] = $holding_tbl_grid_2g->js()->reload();
            $this->js(true, $js)->univ()->successMessage('File processed')->execute();
        }


        //// LTE Submit
        if ($f_lte->isSubmitted()) {
            $cgilte_holding->deleteAll();
            $cgilte_array = array();
            $cgilte_array = $cgilte->getRows();
            $cgilte_array_new = array();
            foreach ($cgilte_array as $key => $val) {
                $cgilte_array_new[$val['cgi']] = $val;
            }
            $cgilte_array = $cgilte_array_new;
            //var_dump($cgilte_array);
            unset($cgilte_array_new);
            $holding_array = array();
            $ul_date = date('Y/m/d h:i:s', time());

            $fs_lte->tryLoad($ul_lte->form->data);
            $furl = './' . $fs_lte['dirname'] . '/' . $fs_lte['filename'];
            $file = fopen($furl, "r");
            $input = array();

            while (!feof($file)) {
                $result = fgetcsv($file);
                if (array(null) !== $result) { // ignore blank lines
                    $input[] = $result;
                }
            }
            fclose($file);

            $i = 0;


            foreach ($input as $r) {
                if ($i > 0 && is_array($r)) {
                    /*$r[15] = str_replace('[', '', $r[15]);
                    $r[15] = str_replace(']', '', $r[15]);
                    $r[15] = str_replace('-7-', '-07-', $r[15]);
                     $r[14] = str_replace('\N', '0', $r[14]);  //TRX Count \N replaced
                    $r[14] = str_replace('\N', '0', $r[14]);  //TRX Count \N replaced
                    */
                    $r[8] = str_replace('\N', '0', $r[8]);  //DLearrfn Count \N replaced
                    $cgi = '655-07-'.$r[7].'-'.$r[2].$r[5];
                    if (!isset($holding_array[$cgi])) {
                        $holding_array[$cgi]['cgi'] = $cgi;
                        $holding_array[$cgi]['vendor'] = $r[0];
                        $holding_array[$cgi]['region'] = $r[1];
                        $holding_array[$cgi]['enodebid'] = $r[2];
                        $holding_array[$cgi]['sitename'] = $r[3];
                        $holding_array[$cgi]['name'] = $r[4];
                        $holding_array[$cgi]['cellid'] = $r[5];
                        $holding_array[$cgi]['phycellid'] = $r[6];
                        $holding_array[$cgi]['tac'] = $r[7];
                        $holding_array[$cgi]['dlearfcn'] = $r[8];
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
                                    $cgilte_history->set('cgi_filename', $fs_lte['original_filename']);
                                    $cgilte_history->saveAndUnload();

                                    $cgilte->tryLoadBy('cgi', $cgi);
                                    $cgilte->set($key, $value);
                                    $cgilte->saveAndUnload();

                                } else {
//                                     echo 'same';
                                }

                            }
                        } else {
                            $cgilte_history->set('cgi_lte_id', $cgilte_array[$cgi]['id']);
                            $cgilte_history->set('fld', 'cgi - reactivated');
                            $cgilte_history->set('previous', 'removed');
                            $cgilte_history->set('current', $holding_array[$cgi]['cgi']);
                            $cgilte_history->set('changed_on', $ul_date);
                            $cgilte_history->set('cgi_filename', $fs_lte['original_filename']);
                            $cgilte_history->saveAndUnload();

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
                                    $cgilte_history->set('cgi_filename', $fs_lte['original_filename']);
                                    $cgilte_history->saveAndUnload();

                                    $cgilte->tryLoadBy('cgi', $cgi);
                                    $cgilte->set($key, $value);
                                    $cgilte->set('removed', 0);
                                    $cgilte->saveAndUnload();
                                } else {
//                                     echo 'same';
                                }
                            }

                        }
                    } else {
                        //b - CGI not found in previous CGI report//
                        //todo
                        //// check for LAC change(Cutover)  + CI change(only possible to check on one cell sites)
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
                            if ($key != 'id') {
                                $cgilte->set($key, $value);
                            }
                        }
                        $cgilte->save();

                        //die(var_dump($cgilte->id));
                        $cgilte_history->set('cgi_lte_id', $cgilte->id);
                        $cgilte_history->set('fld', 'cgi - added');
                        $cgilte_history->set('previous', null);
                        $cgilte_history->set('current', $holding_array[$cgi]['cgi']);
                        $cgilte_history->set('changed_on', $ul_date);
                        $cgilte_history->set('cgi_filename', $fs_lte['original_filename']);
                        $cgilte_history->saveAndUnload();
                        $cgilte->unload();

//                        }
                    }
                }
                $i++;
            }

            $cgilte->unload();
            $cgilte_all = $cgilte->getRows();
            //die(var_dump($cgi3g_all));
            foreach ($cgilte_all as $cgi) {
                $curr_cgi = $cgi['cgi'];
                if (isset($holding_array[$curr_cgi])) {
                    unset($holding_array[$curr_cgi]);
                } else {
                    if ($cgi['removed'] == false) {

                        $holding_array[$curr_cgi]['cgi'] = $cgi['cgi'];
                        $holding_array[$curr_cgi]['vendor'] = $cgi['vendor'];
                        $holding_array[$curr_cgi]['region'] = $cgi['region'];
                        $holding_array[$curr_cgi]['enodebid'] = $cgi['enodebid'];
                        $holding_array[$curr_cgi]['sitename'] = $cgi['sitename'];
                        $holding_array[$curr_cgi]['name'] = $cgi['name'];
                        $holding_array[$curr_cgi]['cellid'] = $cgi['cellid'];
                        $holding_array[$curr_cgi]['phycellid'] = $cgi['phycellid'];
                        $holding_array[$curr_cgi]['tac'] = $cgi['tac'];
                        $holding_array[$curr_cgi]['dlearfcn'] = $cgi['dlearfcn'];
                    }
                }
            }
            foreach ($holding_array as $key => $val) {
                $cgilte_holding->set($val);
                $cgilte_holding->saveAndUnload();
            }

            $js = array();
            $js[] = $holding_tbl_grid_lte->js()->reload();
            $this->js(true, $js)->univ()->successMessage('File processed')->execute();
        }


        ////end LTE submit

        if ($_GET['Delete3G']) {
            $del_id = $_GET['Delete3G'];
            $cgi3g_holding->load($del_id);
            $del_cgi = $cgi3g_holding->get('cgi');
            $cgi3g->loadBy('cgi', $del_cgi);
            $cgi_id = $cgi3g->id;
            $cgi3g_holding->delete($del_id);
            $cgi3g->set('removed', true);
            $cgi3g->save();
            $cgi3g_history->set('cgi_3g_id', $cgi_id);
            $cgi3g_history->set('fld', 'cgi - removed');
            $cgi3g_history->set('previous', $del_cgi);
            $cgi3g_history->set('current', 'removed');
            $cgi3g_history->set('changed_on', date('Y-m-d H:i:s'));
            $cgi3g_history->set('cgi_filename', $fs_3g['original_filename']);
            $cgi3g_history->saveAndUnload();
            $js = array();
            $js[] = $this->js()->reload();
            $this->js(true, $js)->univ()->successMessage('Cell Deleted')->execute();
        }
        if ($_GET['Delete2G']) {
            $del_id = $_GET['Delete2G'];
            $cgi2g_holding->load($del_id);
            $del_cgi = $cgi2g_holding->get('cgi');
            $cgi2g->loadBy('cgi', $del_cgi);
            $cgi_id = $cgi2g->id;
            $cgi2g_holding->delete($del_id);
            $cgi2g->set('removed', true);
            $cgi2g->save();
            $cgi2g_history->set('cgi_2g_id', $cgi_id);
            $cgi2g_history->set('fld', 'cgi - removed');
            $cgi2g_history->set('previous', $del_cgi);
            $cgi2g_history->set('current', 'removed');
            $cgi2g_history->set('changed_on', date('Y-m-d H:i:s'));
            $cgi2g_history->set('cgi_filename', $fs_2g['original_filename']);
            $cgi2g_history->saveAndUnload();
            $js = array();
            $js[] = $this->js()->reload();
            $this->js(true, $js)->univ()->successMessage('Cell Deleted')->execute();
        }

        if ($_GET['DeleteLTE']) {
            $del_id = $_GET['DeleteLTE'];
            $cgilte_holding->load($del_id);
            $del_cgi = $cgilte_holding->get('cgi');
            $cgilte->loadBy('cgi', $del_cgi);
            $cgi_id = $cgi2g->id;
            $cgilte_holding->delete($del_id);
            $cgilte->set('removed', true);
            $cgilte->save();
            $cgilte_history->set('cgi_lte_id', $cgi_id);
            $cgilte_history->set('fld', 'cgi - removed');
            $cgilte_history->set('previous', $del_cgi);
            $cgilte_history->set('current', 'removed');
            $cgilte_history->set('changed_on', date('Y-m-d H:i:s'));
            $cgilte_history->set('cgi_filename', $fs_lte['original_filename']);
            $cgilte_history->saveAndUnload();
            $js = array();
            $js[] = $this->js()->reload();
            $this->js(true, $js)->univ()->successMessage('Cell Deleted')->execute();
        }

        if ($f_3g_holding->isSubmitted()) {
//            die(var_dump(json_decode($sel_3g->get())));
            $sel_nodes = json_decode($sel_3g->get());
            if (is_array($sel_nodes)) {
                foreach ($sel_nodes as $del_id) {
                    $cgi3g_holding->load($del_id);
                    $del_cgi = $cgi3g_holding->get('cgi');
                    $cgi3g->loadBy('cgi', $del_cgi);
                    $cgi_id = $cgi3g->id;
                    $cgi3g_holding->delete($del_id);
                    $cgi3g->set('removed', true);
                    $cgi3g->save();
                    $cgi3g_history->set('cgi_3g_id', $cgi_id);
                    $cgi3g_history->set('fld', 'cgi - removed');
                    $cgi3g_history->set('previous', $del_cgi);
                    $cgi3g_history->set('current', 'removed');
                    $cgi3g_history->set('changed_on', date('Y-m-d H:i:s'));
                    $cgi3g_history->set('cgi_filename', $fs_3g['original_filename']);
                    $cgi3g_history->saveAndUnload();
                }
                $js = array();
                $js[] = $this->js()->reload();
                $this->js(true, $js)->univ()->successMessage('Cells Deleted')->execute();
            } else {
                $js = array();
                $js[] = $this->js()->reload();
                $this->js(true, $js)->univ()->errorMessage('No Cells Selected')->execute();
            }
        }
        if ($f_2g_holding->isSubmitted()) {
//            die(var_dump(json_decode($sel_2g->get())));
            $sel_nodes = json_decode($sel_2g->get());
            if (is_array($sel_nodes)) {
                foreach ($sel_nodes as $del_id) {
                    $cgi2g_holding->load($del_id);
                    $del_cgi = $cgi2g_holding->get('cgi');
                    $cgi2g->loadBy('cgi', $del_cgi);
                    $cgi_id = $cgi2g->id;
                    $cgi2g_holding->delete($del_id);
                    $cgi2g->set('removed', true);
                    $cgi2g->save();
                    $cgi2g_history->set('cgi_2g_id', $cgi_id);
                    $cgi2g_history->set('fld', 'cgi - removed');
                    $cgi2g_history->set('previous', $del_cgi);
                    $cgi2g_history->set('current', 'removed');
                    $cgi2g_history->set('changed_on', date('Y-m-d H:i:s'));
                    $cgi2g_history->set('cgi_filename', $fs_2g['original_filename']);
                    $cgi2g_history->saveAndUnload();
                }
                $js = array();
                $js[] = $this->js()->reload();
                $this->js(true, $js)->univ()->successMessage('Cells Deleted')->execute();
            } else {
                $js = array();
                $js[] = $this->js()->reload();
                $this->js(true, $js)->univ()->errorMessage('No Cells Selected')->execute();
            }
        }
        if ($f_lte_holding->isSubmitted()) {
//            die(var_dump(json_decode($sel_2g->get())));
            $sel_nodes = json_decode($sel_lte->get());
            if (is_array($sel_nodes)) {
                foreach ($sel_nodes as $del_id) {
                    $cgilte_holding->load($del_id);
                    $del_cgi = $cgilte_holding->get('cgi');
                    $cgilte->loadBy('cgi', $del_cgi);
                    $cgi_id = $cgilte->id;
                    $cgilte_holding->delete($del_id);
                    $cgilte->set('removed', true);
                    $cgilte->save();
                    $cgilte_history->set('cgi_lte_id', $cgi_id);
                    $cgilte_history->set('fld', 'cgi - removed');
                    $cgilte_history->set('previous', $del_cgi);
                    $cgilte_history->set('current', 'removed');
                    $cgilte_history->set('changed_on', date('Y-m-d H:i:s'));
                    $cgilte_history->set('cgi_filename', $fs_lte['original_filename']);
                    $cgilte_history->saveAndUnload();
                }
                $js = array();
                $js[] = $this->js()->reload();
                $this->js(true, $js)->univ()->successMessage('Cells Deleted')->execute();
            } else {
                $js = array();
                $js[] = $this->js()->reload();
                $this->js(true, $js)->univ()->errorMessage('No Cells Selected')->execute();
            }
        }
    }

}
