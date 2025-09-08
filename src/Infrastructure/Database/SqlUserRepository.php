<?php

namespace Infrastructure\Database;

use Infrastructure\Database\BaseSqlRepository;
use Infrastructure\IUserRepository;

class SqlUserRepository extends BaseSqlRepository implements IUserRepository
{

    function __construct($session)
    {
        $this->session = $session->getConnection();
        $this->tableName = "user";
    }

    public function getByEmail(string $email): array | null
    {
        error_log("SqlUserRepository called");
        $stmt = $this->session->prepare("SELECT id, email, password password FROM user WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        $stmt->close();
        return $data;
    }
}
