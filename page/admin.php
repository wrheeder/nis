<?php

class page_admin extends Page{

    function init() {
        parent::init();
        if ($this->api->auth->isAdmin()) {
            $tabs = $this->add('Tabs');
            $user = $tabs->addTab('Users')->add("CRUD");
            $regions = $tabs->addTab('Regions')->add("CRUD");
            $project_types = $tabs->addTab('Project Types')->add("CRUD");
            $application_pages = $tabs->addTab('ApplicationPages');
            $ap_crud = $application_pages->add("CRUD");
            $site_owners = $tabs->addTab('Site_Owner')->add("CRUD");
            $colo_owners = $tabs->addTab('Colo-Owners')->add("CRUD");
            $site_types = $tabs->addTab('Site_Types')->add("CRUD");
            $build_types = $tabs->addTab('Build_Types')->add("CRUD");
            $m_usr = $user->setModel('Users', array('email', 'name', 'surname','can_change_region','can_add_site','can_update_baseline','can_update_forecast','can_update_actual', 'isAdmin','user_must_change_pw','rollout_menu','config_data_menu','can_upload_sites'));
            $m_regs = $regions->setModel('Regions');
            $m_project_types = $project_types->setModel('ProjectTypes');
            $m_app_pages = $ap_crud->setModel('ApplicationPages');
            $site_owners->setModel('siteOwner');
            $colo_owners->setModel('coloOwner');
            $site_types->setModel('sitetype');
            $build_types->setModel('buildType');
            //$ap_crud->grid->addButton('Expander','Sections');
                    
            $this->api->stickyGet('id');
            $this->api->stickyGet('project_types_id');
            
            if($ap_crud->grid){
                $ap_crud->grid->addClass("zebra bordered");
                $ap_crud->grid->addPaginator(10);
                $ap_crud->grid->addColumn('expander', 'Sections');    
            }
            
            if ($user->grid) {
                $user->grid->addQuickSearch(array('email','name','surname','user_must_change_pw'));
                $user->grid->getColumn('email')->makeSortable();
                $user->grid->getColumn('name')->makeSortable();
                $user->grid->getColumn('surname')->makeSortable();
//                $user->grid->dq->order('email asc');
                $user->grid->addClass("zebra bordered");
                $user->grid->addPaginator(10);

                $user->grid->addColumn('button', 'changePassword');
                $user->grid->addColumn('expander', 'UserRegions');
                 $user->grid->addColumn('expander', 'UserAppPages');
                
                if ($_GET['changePassword']) {

                    // Get the name of currently selected member
                    $name = $user->grid->model->load($_GET['changePassword'])->get('username');

                    // Open frame with member's name in the title. Load content through AJAX from subpage
                    $this->js()->univ()->frameURL('Change Password for ' . $name, $this->api->url('admin/changePassword', array('id' => $_GET['changePassword'])))
                            ->execute();
                }
            }

            if ($user->form) {
                //$user->form->addField('password','password');
                //$user->form->getElement('email')->validateField('filter_var($this->get(), FILTER_VALIDATE_EMAIL)');
                if ($user->form->isSubmitted()) {
                    $m = $user->form->getModel();
                    if ($m->get('Password') == null || $m->get('Password') == '')
                        $m->set('password', $this->api->auth->encryptPassword('tempPW1234'));
                    $m->save();
                }
            }
            
            if($project_types->grid){
                $project_types->grid->addColumn('expander', 'ProjectMilestones');
            }
        }
        
    }

}