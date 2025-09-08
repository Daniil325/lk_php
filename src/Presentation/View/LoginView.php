<?php

namespace Presentation\View;

require_once __DIR__ . "/../../Core/Form/FormConstruct.php";

use Core\FormConstruct;
use Core\TextField;

class LoginView
{
    public function render()
    {
        $fields = array(
            new TextField("email", "Email", "email", "", false, "", "error"),
            new TextField("password", "Пароль", "password", "", false, "", "error")
        );

        $form = new FormConstruct($fields, "/", "Вход", 'Войти');

        echo $form->renderForm();
    }
}
