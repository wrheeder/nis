<?php

class Page_admin_changePasswordExt extends Page {

    function init() {
        parent::init();
        if (!$this->api->auth->get('user_must_change_pw')) {
            $this->api->redirect('index');
        }
        $id = $this->api->auth->get('id');
        $this->api->stickyGet('id');
        $f = $this->add('Form');
        $model = $this->add('Model_users');
        $model->load($id);
        $f->setModel($model, array('password', 'user_must_change_pw'));
        $f->add('Form_Field_Hidden', 'id')->set($id);
        $f->addField('password', 'password_confirm');
        $f->set('password', '');
        $f->getElement('user_must_change_pw')->js(true)->closest('.atk-form-row')->hide();
        $pw = $f->getElement('password');
        $pw->add('StrengthChecker', null, 'after_field');
        $f->addSubmit('Change PW');
        if ($f->isSubmitted()) {

            if ($f->get('password') != $f->get('password_confirm')) {
                $f->displayError('password', 'Passwords should match');
                $f->set('user_must_change_pw', true);
            }
            $f->set('user_must_change_pw', false);
            $f->set('password', $f->api->auth->encryptPassword($f->get('password')));
            $f->update();
            $p = $this->api->getDestinationURL('logout');
            $f->js()->univ()->location($p)->successMessage('Password Changed')->execute();
            $this->api->redirect($p);
        }
    }

}