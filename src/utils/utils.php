<?php

namespace utils;
use PDO;

function connect(): PDO {
    return new PDO("mysql:host=". $_ENV['mysql_host']. ";dbname=" . $_ENV['mysql_database'], $_ENV['mysql_username'] , $_ENV['mysql_password'], array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
}
function sanitize($data): string
{

    return htmlentities(
        strip_tags(
            stripslashes(
                trim($data)
            )
        )
    );
}