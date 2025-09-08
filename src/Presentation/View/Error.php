<?php

namespace Presentation;

use Exception;

class ErrorView
{
    private $templatePath = '../src/Presentation/Templates/';

    public function render($template, $data = [])
    {
        extract($data);
        $templateFile = $this->templatePath . $template . '.tpl.php';
        if (file_exists($templateFile)) {
            require_once $templateFile;
        } else {
            throw new Exception("Template $template not found");
        }
    }
}
