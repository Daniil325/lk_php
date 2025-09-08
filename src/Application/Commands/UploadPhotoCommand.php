<?php

namespace Application\Commands;

class UploadPhotoCommand
{
    private $imgRepo;

    function __construct($imgRepo)
    {
        $this->imgRepo = $imgRepo;
    }

    // return photo id
    function handle($data)
    {
        $imgData = file_get_contents($data["tmp_name"]);
        $imgName = $data["name"];

        $insertedId = $this->imgRepo->add(["name" => $imgName, "data" => $imgData]);
        return $insertedId;
    }
}
