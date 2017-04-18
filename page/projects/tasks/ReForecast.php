<?php

Class Page_Projects_Tasks_ReForecast extends Page {

    function init() {
        parent::init();
        $this->api->stickyGet('project_id');
        $this->api->stickyGet('id');
        $f = $this->add('Form');
        $fct = $f->addField('DatePicker', 'Forecast')->set(date('d/m/Y', time()));
        $r_fc = $f->addField('checkBox', 'Re-Forecast');
        $f->addSubmit('Set Forecast');

        $fct->addHook('validate', function() use ($f, $fct) {

            // $config['act_backdate']
            if ($fct->get() == "") {
                $fct->displayFieldError('Date Required!');
            }

            $dt1 = new DateTime(date("Y-m-d", strtotime('now')));

            $dt2 = new DateTime(date("Y-m-d", strtotime($fct->get())));

            $int = $dt1->diff($dt2);
            if ($int->invert == 1) {
                if ((-1 * $int->d) < 0) {
                    $fct->displayFieldError('Cant Forecast in the past! ' . $int->format('%R%d days'));
                }
            }
        });
        if ($f->isSubmitted()) {
            $tl = $this->add('Model_TaskLog');
            $m_tasks = $this->add('Model_Tasks');
            $task_id = $_GET['id'];
            $m_tasks->load($task_id);
            $prev_fc = $m_tasks->get('forecast');
            $m_tasks->set('forecast', $f->get('Forecast'));
            $m_tasks->update();
            if($f->get('Re_Forecast')==1){
                $sel_option = 'selected';
            }else{
                $sel_option = 'not selected';
            }
            $tl->history_push($task_id,'Forecast',$prev_fc,$f->get('forecast'),'Forecast Changed - re-forecast '.$sel_option,$this->api->auth->get('id'));
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
                        $tl->history_push($ms['id'],'Forecast',$fc_prev,$f->get('Forecast'),'Automatic re-forecast after Forecast date changed',$this->api->auth->get('id'));                        
                    }
                }
            }

            $js = array();
            $js[] = $this->js()->univ()->successMessage('Processed');
            $js[] = $this->js()->_selector('.reloadable_grid')->trigger('reloadpage');
            $this->js(true, $js)->univ()->closeDialog()->execute();
        }
    }

}
