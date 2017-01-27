<?php
/**
 * Created by IntelliJ IDEA.
 * User: WRheeder
 * Date: 25/01/2017
 * Time: 11:06 AM
 */

class page_index extends Page {

    public $title = 'Rollout';

    function init() {
        parent::init();

        if ($this->api->auth->get('rollout_menu')) {
            $this->api->stickyGet('sel_tab');
            $this->api->jui->addStaticInclude('myJSFuncs');
            $this->api->jui->addStaticInclude('http://maps.googleapis.com/maps/api/js?key=AIzaSyAcr9nQ9BZaNdhwA7btnCpSAlVP1cncmbk&sensor=true&libraries=geometry');
            $tabs = $this->add('Tabs');
            $form = $this->add('Form');
            $form->js(true)->hide();
            $sel_node = $form->addField('line', 'sel_node');
            $sel_tab = $form->addField('line', 'sel_tab');
            $subm = $form->addSubmit('Submit');
            $search_fld_frm = $this->api->mm->add('Form', null, null, ['form/stacked']);
            $tree = $search_fld_frm->add('jsTree/jsTree');
            $tree->addClass('do-reload');
            $refresh_tree_frm = $this->api->mm->add('Form');
            $refresh_tree_frm->addStyle('width', '265px');

            $sf = 'None';
            if ($_GET['search_fld'] && $_GET['search_fld'] != "None") {
                $sf = $_GET['search_fld'];
            }
            $search_fld = $search_fld_frm->addField('NoLabelLine', 'search_fld')->set($sf);
            $search_fld->setCaption('Site Code Search');
            $search_fld->js('focus')->val('');


            $opt = array('core' => array('data' => array('url' => $this->api->url('treeJSON', array('filter' => $search_fld->get())))),'plugins' =>array('search'));
            $tree->js(true)->univ()->jstreePlugin($tree, $sel_node, $opt);
            $tree->addStyle('overflow-y', 'auto');
            $tree->addStyle('height', '500px');

            $filter_subm = $search_fld_frm->addSubmit('Filter Sites');
            $refresh_subm = $refresh_tree_frm->addSubmit('Refresh Tree');
            $refresh_subm->js(true)->hide();
            if ($form->isSubmitted()) {
                $js = array();
                $js[] = $this->js()->reload(array('sel_node' => $sel_node->get(), 'search_fld' => $search_fld->get(), 'selected_tab' => $sel_tab->get()));
                $this->js(true, $js)->univ()->successMessage('Submitted')->execute();
            }
            if ($search_fld_frm->isSubmitted()) {
                $js = array();
                $js[] = $tree->js()->reload(array('sel_node' => $sel_node->get(), 'search_fld' => $search_fld->get()));
//                $js[] = $tree->js()->univ()->jstreeSelNode($tree,$sel_node->get());
                $search_fld_frm->js(true, $js)->univ()->execute();
            }
            if ($refresh_tree_frm->isSubmitted()){
                $tree->js()->univ()->jstreeRefresh($tree)->execute();
            }
            if ($_GET['selected_tab']) {
                $sel_tab->set($_GET['selected_tab']);
            }
            if ($_GET['sel_node']) {
                $sel_node->set($_GET['sel_node']);
                if ($sel_node->get() == '[Root]0000' || $sel_node->get() == "") {
                    //$tabs->js(true)->hide();
                    $sel_tab->set(0);
                    $_GET['selected_tab'] = 0;
                } elseif (!(strpos($_GET['sel_node'], '[Region]') === false )) {
                    if (!isset($r) && $this->api->auth->get('can_add_site') && !(strpos($_GET['sel_node'], '[Region]') === false)) {
                        $r = $tabs->addTabURL($this->api->url('Site', array('sel_node' => $_GET['sel_node'], 'selected_tab' => 0)), 'Site');
                        $sel_tab->set(0);
                        $_GET['selected_tab'] = 0;
                    }
                } else {
                    if (!isset($l) && !(strpos($_GET['sel_node'], '[LEASE]') === false)) {
                        $l = $tabs->addTabURL($this->api->url('Lease', array('sel_node' => $_GET['sel_node'], 'selected_tab' => 0)), 'Lease');
                        $sel_tab->set(0);
                        $_GET['selected_tab'] = 0;
                    } elseif (!isset($tx) && !(strpos($_GET['sel_node'], '[TX]') === false)) {
                        $tx = $tabs->addTabURL($this->api->url('Transmission', array('sel_node' => $_GET['sel_node'], 'selected_tab' => 0)), 'Transmission');
                        $sel_tab->set(0);
                        $_GET['selected_tab'] = 0;
                    } elseif (!isset($rf) && !(strpos($_GET['sel_node'], '[RF]') === false)) {
                        $rf = $tabs->addTabURL($this->api->url('RadioPlanning', array('sel_node' => $_GET['sel_node'], 'selected_tab' => 0)), 'RadioPlanning');
                        $sel_tab->set(0);
                        $_GET['selected_tab'] = 0;
                    }elseif (!isset($l_id) && !(strpos($_GET['sel_node'], '[LEASE_ID]') === false)) {
                        $l_id = $tabs->addTabURL($this->api->url('LeaseDetails', array('sel_node' => $_GET['sel_node'], 'selected_tab' => 0)), 'Lease Info');
                        $sel_tab->set(0);
                        $_GET['selected_tab'] = 0;
                    } else{
                        if (!isset($t)) {
                            $t = $tabs->addTabURL($this->api->url('Site', array('sel_node' => $_GET['sel_node'], 'selected_tab' => 0)), 'Site');
                            $_GET['selected_tab'] = 0;
                        }
                        if (!isset($p)) {
                            $p = $tabs->addTabURL($this->api->url('Projects', array('sel_node' => $_GET['sel_node'], 'selected_tab' => 1)), 'Projects');
                            $_GET['selected_tab'] = 1;
                        }
                        //$tabs->js(true)->show();
                        $m_user_app_pages = $this->add('Model_UserAppPages');
                        $m_user_app_pages->addCondition('users_id',$this->api->auth->get('id'));
                        $m_app_pages=$m_user_app_pages->join('application_pages');
                        $m_app_pages->addField('id');
                        $m_app_pages->addField('page_name');
                        $m_app_pages->addField('table_name');
//                    $m_app_pages->addCondition('id','in',$m_user_app_pages->getRows());
                        $app_pages = $m_user_app_pages->getRows();
//                    die(var_dump($app_pages));
                        $i = 1;
                        foreach ($app_pages as $app_page) {
                            if (!isset(${'a' . $app_page['page_name']}))
                                ${'a' . $app_page['page_name']} = $tabs->addTabURL($this->api->url('AppPages', array('sel_node' => $_GET['sel_node'], 'app_page' => $app_page['page_name'], 'app_page_id' => $app_page['id'], 'app_page_tbl' => $app_page['table_name'], 'selected_tab' => $i + 1)), $app_page['page_name']);
                            $i++;
                        }
                        $tabs->setOption('active', $sel_tab->get());
                    }
                }
            }
        }else {
            $this->add('p')->set('Home - you dont have Rollout Access');
        }
    }

}
