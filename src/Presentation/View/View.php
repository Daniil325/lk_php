<?php

namespace Presentation;

interface IView {
    public function render($template, $data);
}