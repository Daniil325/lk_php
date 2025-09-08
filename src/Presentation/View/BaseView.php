<?php

namespace Presentation\View;

use Exception;

class BaseView
{
    private $templatePath = '../src/Presentation/Templates/';

    public function render($template, $data = [])
    {
        // Извлекаем данные в переменные для использования в шаблоне
        extract($data);

        // Проверяем, существует ли файл шаблона
        $templateFile = $this->templatePath . $template . '.tpl.php';
        if (file_exists($templateFile)) {
            require_once $templateFile;
        } else {
            throw new Exception("Template $template not found");
        }
    }
}
