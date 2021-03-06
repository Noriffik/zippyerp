<?php

namespace ZippyERP\System\Pages;

use Zippy\Binding\PropertyBinding as Bind;
use Zippy\Html\Form\TextInput;
use Zippy\Html\Form\CheckBox;
use Zippy\Html\Form\Form;
use Zippy\Html\Label;
use ZippyERP\System\System;
use \Zippy\Html\DataList\DataView;

class UserProfile extends \ZippyERP\System\Pages\Base
{

    public $user;

    public function __construct() {
        parent::__construct();

        $this->user = System::getUser();

        if ($this->user->user_id == 0) {
            App::Redirect("\\ZippyERP\\System\\Pages\\Userlogin");
        }
        //форма   профиля
        $form = new  Form('profileform');
        $form->add(new Label('userlogin', $this->user->userlogin));
        $form->add(new TextInput('email', $this->user->email));
        $form->onSubmit($this, 'onsubmitprof');
        $this->add($form);

        //форма   пароля
        $form = new  Form('passwordform');
        $form->add(new TextInput('userpassword'));
        $form->add(new TextInput('confirmpassword'));
        $form->onSubmit($this, 'onsubmitpass');
        $this->add($form);
        
 
    }

    //записать  пароль
    public function onsubmitpass($sender) {
        $this->setError('');
        $pass = $sender->userpassword->getText();
        $confirm = $sender->confirmpassword->getText();

        if ($pass == '') {
            $this->setError('Введіть пароль');
        } else
        if ($confirm == '') {
            $this->setError('Підтвердити пароль');
        } else
        if ($confirm != $pass) {
            $this->setError('Невірне підтвердження');
        }


        if (!$this->isError()) {
            $this->user->userpass = (\password_hash($pass, PASSWORD_DEFAULT));
            $this->user->save();
            $this->setSuccess('Пароль збережений');
        }
        
        $sender->userpassword->setText('');
        $sender->confirmpassword->setText('');
    }
  
    //запись  профиля
    
    public function onsubmitprof($sender) {
        $this->user->email = $sender->email->getText();
        if (!$this->isError()) {
            $this->user->save();
            System::setUser($user);
            $this->setSuccess('Профіль збережений');
        }
    }

 }
