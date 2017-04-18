<?php

Class Page_Projects_Tasks_ClaimActuals extends Page {

    function init() {
        parent::init();
        $this->api->stickyGet('project_id');
        $this->api->stickyGet('id');
        $f = $this->add('Form');
        $act = $f->addField('DatePicker', 'Actual')->set(date('d/m/Y', time()));
        $r_fc = $f->addField('checkBox', 'Re-Forecast');
        $f->addSubmit('Claim Actual');
        //validation
        $act->addHook('validate', function() use ($f, $act) {

            // $config['act_backdate']
            if ($act->get() == "") {
                $act->displayFieldError('Date Required!');
            }

            $dt1 = new DateTime(date("Y-m-d", strtotime('now')));

            $dt2 = new DateTime(date("Y-m-d", strtotime($act->get())));

            $int = $dt1->diff($dt2);
            if ($int->invert == 1) {
                if ((-1 * $int->d) < $this->api->getConfig('system/act_backdate', -360)) {
                    $act->displayFieldError('Actual Date cant be more than ' . $this->api->getConfig('system/act_backdate', -360) . ' days in the past! ' . $int->format('%R%d days'));
                }
            } else {
                if ($int->d != 0) {
                    $act->displayFieldError('Cant Claim Actual in the future!');
                }
            }
        });

        if ($f->isSubmitted()) {
            $tl = $this->add('Model_TaskLog');
            $this->api->stickyGet('project_id');
            $m_tasks = $this->add('Model_Tasks');
            $task_id = $_GET['id'];
            $m_tasks->load($task_id);
            $m_tasks->set('actual', $f->get('Actual'));
            $m_tasks->saveAndUnload();
            $tl->history_push($task_id,'Actual',null,$f->get('actual'),'Actual Claimed',$this->api->auth->get('id'));
            if ($r_fc->get()) {
                $m_tasks->loadBy('project_id', $_GET['project_id']);
                $ms_tasks = $m_tasks->getRows();
                $pid = $this->api->db->dsql()->table('project')->field('project_types_id')->where('id', $_GET['project_id'])->do_getOne();
                //echo $pid;       
                $proj_mss = $this->add('Model_ProjectMilestones');
                $proj_mss->addCondition('project_types_id', $pid);
                $task_tree = array();
                foreach ($ms_tasks as $ms) {
                    $task_tree[$ms['project_milestones_id']]['id'] = $ms['id'];
                    $task_tree[$ms['project_milestones_id']]['forecast'] = $ms['forecast'];
                    $task_tree[$ms['project_milestones_id']]['actual'] = $ms['actual'];
                }

                foreach ($proj_mss as $ms) {
                    $task_tree[$ms['id']]['parent'] = $ms['parent_id'];
                    $task_tree[$ms['id']]['duration'] = $ms['duration'];
                }
                $reforecast_start = false;
                foreach ($task_tree as $k => $ms) {
                    if ($ms['id'] == $_GET['id'])
                        $reforecast_start = true;
                    if ($reforecast_start) {
                        if ($ms['id'] == $_GET['id']) {
                            //$task_tree[$k]['forecast'] = $task_tree[$k]['actual'];  //decision to be made whether to re-forecast ms that is being claimed or not
                        } else {
                            if ($ms['parent'] != null) {
                                if ($task_tree[$ms['parent']]['actual'] != null) {  //use parent actual for re-forecast
                                    $task_tree[$k]['forecast'] = date('Y-m-d', strtotime($task_tree[$ms['parent']]['actual'] . ' + ' . $task_tree[$k]['duration'] . ' days'));
                                } else {   //use parent forecast for re-forecast
                                    $task_tree[$k]['forecast'] = date('Y-m-d', strtotime($task_tree[$ms['parent']]['forecast'] . ' + ' . $task_tree[$k]['duration'] . ' days'));
                                }
                            }
                        }
                        $m_tasks->load($ms['id']);
                        $fc_prev = $m_tasks->get('forecast');
                        $m_tasks->set('forecast', $task_tree[$k]['forecast']);
                        $m_tasks->save();
                        $tl->history_push($ms['id'],'Forecast',$fc_prev,$f->get('Forecast'),'Automatic re-forecast after Actual date claimed',$this->api->auth->get('id'));                        
                    }
                }
            }
//            echo var_dump($task_tree);
            $js = array();
            $js[] = $this->js()->univ()->successMessage('Processed');
            $js[] = $this->js()->_selector('.reloadable_grid')->trigger('reloadpage');
            $this->js(true, $js)->univ()->closeDialog()->execute();
        }
    }

}
