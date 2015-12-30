<?php

require 'posts_db.php';


class Posts 
{
    const ID_KEY = 'id';
    const USER_ID_KEY = 'user_id';
    const USER_NAME_KEY = 'user_name';
    const USER_THUMBNAIL_KEY = 'user_thumbnail';
    const PRIVACY_PUBLIC_KEY = 'public';
    const PRIVACY_KEY = 'privacy';
    const TIMESTAMP_KEY = 'timestamp';
    const TEXT_KEY = 'text';
    const PICTURE_KEY = 'picture';

    const COMMENTS_COUNT_KEY = 'comments_count';
    const LIKES_COUNT_KEY = 'likes_count';
    const LIKED_KEY = 'liked';

    const ERROR_TEXT_FORMAT = 1230;
    const ERROR_PRIVACY_FORMAT = 1231;
    const ERROR_PICTURE_FORMAT = 1232;

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

    static public function delete($response, $token, $post_id)
    {
        if (!TokensDB::check($token)) {
            return putError(
                'invalid token', 
                Users::ERROR_AUTH_INVALID, $response);            
        }
        
        $post = PostsDB::delete($token[Users::ID_KEY], $post_id);

        if ($post === FALSE) {
            return putError(
                'database connection error', 
                DATABASE::ERROR_DATABASE_CONN, $response);             
        }

        return putJsonBody(array(
            'error' => false,
        ), 200, $response);   
    }

    static public function create($response, $token, $post)
    {
        if (!TokensDB::check($token)) {
            return putError(
                'invalid token', 
                Users::ERROR_AUTH_INVALID, $response);            
        }

        if (count($post) != 3) {
            return putError(
                'invalid request parameters', 
                Users::ERROR_FORMAT, $response);
        }

        $post[Posts::TEXT_KEY] = Validator::filterText($post, Posts::TEXT_KEY);
        $post[Posts::PRIVACY_KEY] = Validator::filterPrivacy($post, Posts::PRIVACY_KEY);
        //$post[Posts::PICTURE_KEY] = Validator::filterPicture($post, Posts::PICTURE_KEY);

        if (!$post[Posts::TEXT_KEY]) {
            return putError(
                'invalid text parameter', 
                Posts::ERROR_TEXT_FORMAT, $response);
        }
        else if (!$post[Posts::PRIVACY_KEY]) {
            return putError(
                'invalid privacy parameter', 
                Posts::ERROR_PRIVACY_FORMAT, $response);
        } 
        else if (!isset($post[Posts::PICTURE_KEY])) {
            return putError(
                'invalid picture parameter', 
                Posts::ERROR_PICTURE_FORMAT, $response);
        } 

        $post[Posts::PRIVACY_KEY] = ($post[Posts::PRIVACY_KEY] == 'public');

        $post = PostsDB::create($token[Users::ID_KEY], $post);

        if ($post === FALSE) {
            return putError(
                'database connection error', 
                DATABASE::ERROR_DATABASE_CONN, $response);             
        }

        return putJsonBody(array(
            'error' => false,            
        ), 200, $response);   
    }

    static public function all($response, $token, $friend_id)
    {
        if (!TokensDB::check($token)) {
            return putError(
                'invalid token', 
                Users::ERROR_AUTH_INVALID, $response);            
        }

        $posts = PostsDB::all($token[Users::ID_KEY], $friend_id);

        if ($posts === FALSE) {
            return putError(
                'database connection error', 
                DATABASE::ERROR_DATABASE_CONN, $response);             
        }

        return putJsonBody(array(
            'error' => false,                
            'posts' => $posts
        ), 200, $response);         
    }

    static public function home($response, $token) 
    {
        if (!TokensDB::check($token)) {
            return putError(
                'invalid token', 
                Users::ERROR_AUTH_INVALID, $response);            
        }   
        
        $posts = PostsDB::home($token[Users::ID_KEY]);

        if ($posts === FALSE) {
            return putError(
                'database connection error', 
                DATABASE::ERROR_DATABASE_CONN, $response);             
        }

        return putJsonBody(array(
            'error' => false,                
            'posts' => $posts
        ), 200, $response);              
    }

    static public function like($response, $token, $post_id) 
    {
        if (!TokensDB::check($token)) {
            return putError(
                'invalid token', 
                Users::ERROR_AUTH_INVALID, $response);            
        }   
        
        $result = PostsDB::like($token[Users::ID_KEY], $post_id);

        if ($result === FALSE) {
            return putError(
                'database connection error', 
                DATABASE::ERROR_DATABASE_CONN, $response);             
        }

        return putJsonBody(array(
            'error' => false,                            
        ), 200, $response);   
    }

    static public function dislike($response, $token, $post_id) 
    {
        if (!TokensDB::check($token)) {
            return putError(
                'invalid token', 
                Users::ERROR_AUTH_INVALID, $response);            
        }   
        
        $result = PostsDB::dislike($token[Users::ID_KEY], $post_id);

        if ($result === FALSE) {
            return putError(
                'database connection error', 
                DATABASE::ERROR_DATABASE_CONN, $response);             
        }

        return putJsonBody(array(
            'error' => false,                            
        ), 200, $response);   
    }    
}

?>