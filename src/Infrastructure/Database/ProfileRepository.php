<?php

namespace Infrastructure\Database;

use Infrastructure\IProfileRepository;
use Infrastructure\Database\BaseSqlRepository;


class ProfileRepository extends BaseSqlRepository implements IProfileRepository
{
    function __construct($session)
    {
        $this->session = $session->getConnection();
        $this->tableName = "user";
    }

    public function getById(int $id): array | null
    {
        error_log("GET BY ID FROM $this->tableName");
        $stmt = $this->session->prepare(
            "SELECT
                user.id, user.email, user.name, user.surname, user.age, user.sex, photo.data AS photoData, photo.name AS photoName
                FROM $this->tableName
                JOIN photo ON user.profilePhoto = photo.id
                WHERE $this->tableName.id = ?"
        );
        $stmt->bind_param('s', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        $stmt->close();

        error_log("BY ID " . print_r($data, true));
        return $data;
    }
}
