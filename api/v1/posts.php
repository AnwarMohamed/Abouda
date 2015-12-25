<?php

require 'posts_db.php';

class Posts 
{
    const ID_KEY = 'id';
    const USER_ID_KEY = 'user_id';
    const USER_NAME_KEY = 'user_name';
    const PRIVACY_PUBLIC_KEY = 'public';
    const PRIVACY_KEY = 'privacy';
    const TIMESTAMP_KEY = 'timestamp';
    const TEXT_KEY = 'text';
    const PICTURE_KEY = 'picture';

    const ERROR_TEXT_FORMAT = 1230;
    const ERROR_PRIVACY_FORMAT = 1231;

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

    static public function create($response, $token, $post)
    {
        if (!TokensDB::check($token)) {
            return putError(
                'invalid token', 
                Users::ERROR_AUTH_INVALID, $response);            
        }

        if (count($post) != 2) {
            return putError(
                'invalid request parameters', 
                Users::ERROR_FORMAT, $response);
        }

        $post[Posts::TEXT_KEY] = Validator::filterText($post, Posts::TEXT_KEY);
        $post[Posts::PRIVACY_KEY] = Validator::filterPrivacy($post, Posts::PRIVACY_KEY);

        if (!$post[Posts::TEXT_KEY]) {
            return putError(
                'invalid text parameter', 
                Users::ERROR_TEXT_FORMAT, $response);
        }
        else if (!$post[Posts::PRIVACY_KEY]) {
            return putError(
                'invalid privacy parameter', 
                Users::ERROR_PRIVACY_FORMAT, $response);
        } 

        $post[Posts::PRIVACY_KEY] = ($post[Posts::PRIVACY_KEY] == 'private');

        $post = PostsDB::create($token[Users::ID_KEY], $post);

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