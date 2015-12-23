<?php

class Posts 
{
	static public function getPost($response, $token, $post_id)
	{
        if (!$token || !Database::checkToken(null, $token)) {
            return putError(
                'invalid token', 
                Users::ERROR_AUTH_INVALID, $response);            
        }

        $post = Database::getPost($token[Users::ID_KEY], $post_id);

        if (!$post) {
            return putError(
                'database connection error', 
                DATABASE::ERROR_DATABASE_CONN, $response);
        }

        return putJsonBody(array(
                'error' => false,                
                'post' => $post
            ), 200, $response);   
	}
}

?>