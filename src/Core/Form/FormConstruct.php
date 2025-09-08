<?php

namespace Core;

use Exception;

interface IField
{
    public function render();
}

class TextField implements IField // text, textarea, number, password, etc
{
    public string $id;
    public string $label;
    public string $type;
    public string $value = "";
    public bool $required;
    public string $validate;
    public string $errorMessage;

    public function __construct($id, $label, $type, $value, $required, $validate, $errorMessage)
    {
        $this->id = $id;
        $this->label = $label;
        $this->type = $type;
        $this->value = $value;
        $this->required = $required;
        $this->validate = $validate;
        $this->errorMessage = $errorMessage;
    }

    public function render()
    {
        $templateFile = __DIR__ . '/Templates/FormTemplate.tpl.php';

        if (file_exists($templateFile)) {
            // Подключаем шаблон
            require $templateFile;
        } else {
            // Выводим ошибку, если шаблон не найден
            throw new Exception("Template not found");
        }
    }
}

class CheckOption
{
    public string $id;
    public string $value;
    public string $label;
    public string $checked;

    public function __construct($id, $value, $label, $checked)
    {
        $this->id = $id;
        $this->label = $label;
        $this->value = $value;
        $this->checked = $checked;
    }

    public function render($type, $name)
    {
        $templateFile = __DIR__ . '/Templates/Option.tpl.php';

        if (file_exists($templateFile)) {
            // Подключаем шаблон
            require $templateFile;
        } else {
            // Выводим ошибку, если шаблон не найден
            throw new Exception("Template not found");
        }
    }
}

class CheckField implements IField
{
    public string $id;
    public string $legend;
    public string $name;
    public string $type; // radio, checkbox
    /**  @var CheckOption[] */
    public array $options;

    public function __construct($id, $legend, $name, $type, $options)
    {
        $this->id = $id;
        $this->legend = $legend;
        $this->name = $name;
        $this->type = $type;
        $this->options = $options;
    }

    public function render()
    {
        $templateFile = __DIR__ . '/Templates/CheckField.tpl.php';

        if (file_exists($templateFile)) {
            // Подключаем шаблон
            require $templateFile;
        } else {
            // Выводим ошибку, если шаблон не найден
            throw new Exception("Template not found");
        }
    }
}


class FormConstruct
{
    public array $fields;
    public string $action;
    public string $title;
    public string $buttonText;

    function __construct($fields, $action, $title, $buttonText)
    {
        $this->action = $action;
        $this->fields = $fields;
        $this->title = $title;
        $this->buttonText = $buttonText;
    }


    public function renderForm()
    {
        // Начинаем буферизацию вывода
        $templateFile = __DIR__ . '/Templates/Form.tpl.php';

        if (file_exists($templateFile)) {
            // Подключаем шаблон
            require $templateFile;
        } else {
            // Выводим ошибку, если шаблон не найден
            throw new Exception("Template not found");
        }
    }
}
