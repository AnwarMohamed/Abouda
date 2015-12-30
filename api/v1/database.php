<?php

class Database
{
    const ERROR_DATABASE_CONN = 1220;    

    static public function connect() 
    {
        $mysqli = new mysqli("localhost", "root", "root", "abouda");
        return $mysqli->connect_errno ? false: $mysqli;
    }

    static public function pusher() {
        $app_id = '163590';
        $app_key = '394054e78c53c11464f8';
        $app_secret = '0d3a532c2b59d5d3afe1';

        return new Pusher(
            $app_key,
            $app_secret,
            $app_id,
            array('encrypted' => true)
        );
    }
}

?>