<?php

namespace Application\Commands;

require_once "../src/Domain/Entities.php";

interface ICommand
{
    function handle($data);
}
