<?php

namespace Presentation\View;

require_once __DIR__ . "/../../Core/Form/FormConstruct.php";

use Core\FormConstruct;
use Core\TextField;
use Core\CheckField;
use Core\CheckOption;

class RegistrationView
{
    public function render()
    {
        $options = array(
            new CheckOption("o1", "m", "Мужской", false),
            new CheckOption("o2", "f", "Женский", false),
        );

        $fields = array(
            new TextField("name", "Имя", "text", "", false, "", "error"),
            new TextField("surname", "Фамилия", "text", "", false, "", "error"),
            new TextField("age", "Возраст", "number", "", false, "", "error"),
            new CheckField("sex", "Пол", "sex", "radio", $options),
            new TextField("email", "Email", "email", "", false, "", "error"),
            new TextField("password", "Пароль", "password", "", false, "", "error"),
            new TextField("profilePhoto", "Фото", "file", "", false, "", "error"),
        );

        $form = new FormConstruct($fields, "/registration", "Регистрация", "Зарегистрироваться");

        echo $form->renderForm();
    }
}
