<?php

class Page_admin_changePassword extends Page_ApplicationPage {

    function init() {
        parent::init();
        $id = $_GET['id']?$_GET['id']:die(var_dump($this));//$this->getElement('eclipse_admin_changePassword_form_id');
        $this->api->stickyGet('id');
        $f = $this->add('Form');
        $model = $this->add('Model_users');
        $model->load($id);
        $f->setModel($model,array('password'));
        $f->add('Form_Field_Hidden','id')->set($id);
        $f->addField('password','password_confirm');
        $f->set('password','');
        $pw=$f->getElement('password');
		$pw->add('StrengthChecker',null,'after_field');
        $f->addSubmit('Change PW');
        if($f->isSubmitted()){
            if($f->get('password') != $f->get('password_confirm')){
				$f->displayError('password','Passwords should match');
			}

			$f->set('password',$f->api->auth->encryptPassword($f->get('password')));
			$f->update();
                        $js[] = $f->js(true)->univ()->closeDialog();
                        $js[] = $this->js(true)->closest('.reloadable')->trigger('myreload');
			$f->js(true,$js)->univ()->successMessage('Changed PW')->execute();
        }
    }

}