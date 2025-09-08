<?php

namespace Infrastructure\Database;

use Infrastructure\Database\BaseSqlRepository;

class ImageRepository extends BaseSqlRepository
{
    function __construct($session)
    {
        $this->session = $session->getConnection();
        $this->tableName = "photo";
    }
}
