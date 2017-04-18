<?php

class Page_AddProject extends Page {

    function init() {
        parent::init();
        $this->api->stickyGet('sel_node');
        $f = $this->add('Form');
        
        $include_projs = array();
        $include_projs_on_this_site = $this->api->db->dsql()->table('project_types')->where('multiple_allowed',true)->do_getAll();
        foreach ($include_projs_on_this_site as $inc_proj) {
            $include_projs[] = $inc_proj['id'];
        }
//        die(var_dump($include_projs_on_this_site));
        if(count($include_projs)>0)
            $existing_proj_on_this_site = $this->api->db->dsql()->table('project')->field('project_types_id')->where('site_id', str_replace('[Site]', '', $_GET['sel_node']))->where('project_types_id','not in',$include_projs)->do_getAll();
        else
            $existing_proj_on_this_site = $this->api->db->dsql()->table('project')->field('project_types_id')->where('site_id', str_replace('[Site]', '', $_GET['sel_node']))->do_getAll();
        $exclude_projs = array();
        foreach ($existing_proj_on_this_site as $used_proj) {
            $exclude_projs[] = $used_proj['project_types_id'];
        }


        $m_proj_type = $this->add('Model_ProjectTypes');
        if (count($existing_proj_on_this_site) != 0) {
            $m_proj_type->addCondition('id', 'not in', $exclude_projs);
        }
        if ($_GET['selectedProj']) {
            $m_proj_type->load($_GET['selectedProj']);
        }
        $existing_projects_cnt = $m_proj_type->count()->get()[0]['count(*)'];

        if ($existing_projects_cnt > 0) {
            $f->addSubmit('Add Project');
            $f->add('View_Info')->set('Please select a project from dropdown below');
            $dd = $f->addField('DropDown', 'Project');
            $f->addField('name');
            $m_proj_type->loadAny();
            $dd->setModel($m_proj_type);
            $sdate = $f->addField('DatePicker', 'Project_Start')->set(date("Y-m-d"));
            
            $dte = date('d-m-Y', strtotime(str_replace('/', '-', date('d/m/Y'))));
                //$date=strtotime('+7days',$date);
                //die($date);
            $edate = $f->addField('DatePicker', 'Project_End')->set(date('Y-m-d', strtotime($dte . ' + ' . $m_proj_type->get('duration') . ' days')));
            $edate->template->set('after_field', $m_proj_type->get('duration').' days');

            if ($_GET['selectedProj']) {
                $m_proj_type->load($_GET['selectedProj']);
                $dd->set($_GET['selectedProj']);
            }
            if ($_GET['sdate']) {
                $sdate->set($_GET['sdate']);
                $date = date('d-m-Y', strtotime(str_replace('/', '-', $_GET['sdate'])));
                //$date=strtotime('+7days',$date);
                //die($date);
                $edate->set(date('Y-m-d', strtotime($date . ' + ' . $m_proj_type->get('duration') . ' days')));
                $edate->template->set('after_field', $m_proj_type->get('duration') . ' days');
                $edate->js()->reload();
            }
            if ($_GET['edate']) {
                $edate->set(date('d-m-Y', strtotime(str_replace('/', '-', $_GET['edate']))));
                $edate->template->set('after_field',floor((strtotime($edate->get())-strtotime($sdate->get()))/(60*60*24)).' days');
            }
            $f->addField('date', 'Project_Created')->set(date("Y-m-d H:i:s")); //->disable(true)->set(date("Y-m-d H:i:s"));
            $f->addField('text', 'comments');
            
            $js = array();
            $js1 = array();
            $js[]=$f->js()->atk4_form('reloadField','Project_End',array($this->api->url(),'sel_node'=>$_GET['sel_node'],'edate'=>$edate->js()->val()));
            $js1[] = $f->js()->reload(array($this->api->url(),'sel_node'=>$_GET['sel_node'], 'selectedProj' => $dd->js()->val(), 'sdate' => $sdate->js()->val()));

            $dd->js('change', $js1);
            $sdate->js('change', $js1);
            $edate->js('change', $js);
        } else {
            $f->addSubmit('Close');
            $f->add('View_Error')->set('There are no free projects avaialble for execution on this Site');
        }
        if ($f->isSubmitted()) {
            if ($existing_projects_cnt > 0) {
                $projs = $this->add('Model_Project');
                //var_dump($f->get());
                $projs->set('name', $f->get('name'));
                $projs->set('project_start', $f->get('Project_Start'));
                $projs->set('project_end', $f->get('Project_End'));
                $projs->set('created_on', $f->get('Project_Created'));
                $projs->set('site_id', str_replace('[Site]', '', $_GET['sel_node']));
                $projs->set('created_by_id', $this->api->auth->get('id'));
                $projs->set('project_types_id', $f->get('Project'));
                $projs->set('comments', $f->get('comments'));
                $projs->update();
                $proj_mss=$this->add('Model_ProjectMilestones');
                $proj_mss->addCondition('project_types_id',$f->get('Project'));
                $task_tree = array();
                foreach($proj_mss as $ms){
                    $task=$this->add('Model_Tasks');
                    $task->set('project_id',$projs->id);
                    $task->set('project_milestones_id',$ms['id']);
                    if($ms['parent_id']==null){
                        $task->set('baseline',date('Y-m-d', strtotime($f->get('Project_Start') . ' + ' . $ms['duration'] . ' days')));
                        $task->set('forecast',date('Y-m-d', strtotime($f->get('Project_Start') . ' + ' . $ms['duration'] . ' days')));
                        $task_tree[$ms['id']]=date('Y-m-d', strtotime($f->get('Project_Start') . ' + ' . $ms['duration'] . ' days'));
                    }
                    else{
                        $task_tree[$ms['id']]=date('Y-m-d', strtotime($task_tree[$ms['parent_id']] . ' + ' . $ms['duration'] . ' days'));
                        $task->set('baseline',date('Y-m-d', strtotime($task_tree[$ms['parent_id']] . ' + ' . $ms['duration'] . ' days')));
                        $task->set('forecast',date('Y-m-d', strtotime($task_tree[$ms['parent_id']] . ' + ' . $ms['duration'] . ' days')));
                    }
                    $task->set('created_by',$this->api->auth->get('id'));
                    $task->set('created_on',  date('Y-m-d H:i:s'));
                    $task->update();
                }
                
                
            }
            $js2 = array();
            $js2[] =  $this->js()->_selector(".reloadable")->trigger("reloadpage");
            $js2[] = $f->js(true)->univ()->closeDialog();
            
            $this->js(true, $js2)->univ()->successMessage('Project added to Site'.$_GET['sel_node'])->execute();
        }
    }

}
