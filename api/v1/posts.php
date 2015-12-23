<?php

class Posts 
{
	static public function get($response, $token, $post_id)
	{
        if (!TokensDB::check($token)) {
            return putError(
                'invalid token', 
                Users::ERROR_AUTH_INVALID, $response);            
        }

        $post = PostsDB::get($token[Users::ID_KEY], $post_id);

        if ($post === FALSE) {
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