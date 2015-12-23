<?php

require 'users_info_db.php';

class UsersInfo
{
	static public function get($response, $token, $friend_id)
	{
        if (!TokensDB::check($token)) {
            return putError(
                'invalid token', 
                Users::ERROR_AUTH_INVALID, $response);            
        }

        $info = UsersInfoDB::get($token[Users::ID_KEY], $friend_id);

        if ($info === FALSE) {
            return putError(
                'database connection error', 
                DATABASE::ERROR_DATABASE_CONN, $response);             
        }

        return putJsonBody(array(
            'error' => false,                
            'info' => $info
        ), 200, $response);   
	}

	static public function update($response, $token, $info)
	{
        if (!TokensDB::check($token)) {
            return putError(
                'invalid token', 
                Users::ERROR_AUTH_INVALID, $response);            
        }
	}
}

?>