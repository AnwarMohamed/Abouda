<?php

require 'search_db.php';

class Search 
{    
    static public function get($response, $token, $string, $flag)
    {
        if (!TokensDB::check($token)) {
            return putError(
                'invalid token', 
                Users::ERROR_AUTH_INVALID, $response);            
        }

        $search = SearchDB::get($token[Users::ID_KEY], $string, $flag);

        if ($search === FALSE) {
            return putError(
                'database connection error', 
                DATABASE::ERROR_DATABASE_CONN, $response);             
        }

        return putJsonBody(array(
            'error' => false,                
            'results' => $search
        ), 200, $response);   
    }

}
?>