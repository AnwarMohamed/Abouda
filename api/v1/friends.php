<?php

require_once 'tokens_db.php';
require 'friends_db.php';

class Friends
{
    static public function blocked($response, $token)
    {
        if (!TokensDB::check($token)) {
            return putError(
                'invalid token', 
                Users::ERROR_AUTH_INVALID, $response);            
        }

        $blocked = FriendsDB::blocked($token[Users::ID_KEY]);

        if ($blocked === FALSE) {
            return putError(
                'database connection error', 
                DATABASE::ERROR_DATABASE_CONN, $response);             
        }

        return putJsonBody(array(
            'error' => false,                
            'friends' => $blocked
        ), 200, $response); 
    }   

    static public function block($response, $token, $friend_id)
    {
        if (!TokensDB::check($token)) {
            return putError(
                'invalid token', 
                Users::ERROR_AUTH_INVALID, $response);            
        }

        $blocked = FriendsDB::block($token[Users::ID_KEY], $friend_id);

        if ($blocked === FALSE) {
            return putError(
                'database connection error', 
                DATABASE::ERROR_DATABASE_CONN, $response);             
        }

        return putJsonBody(array(
            'error' => false            
        ), 200, $response); 
    }

    static public function unblock($response, $token, $friend_id)
    {
        if (!TokensDB::check($token)) {
            return putError(
                'invalid token', 
                Users::ERROR_AUTH_INVALID, $response);            
        }

        $unblocked = FriendsDB::unblock($token[Users::ID_KEY], $friend_id);

        if ($unblocked === FALSE) {
            return putError(
                'database connection error', 
                DATABASE::ERROR_DATABASE_CONN, $response);             
        }

        return putJsonBody(array(
            'error' => false            
        ), 200, $response); 
    }

    static public function request($response, $token, $friend_id)
    {
        if (!TokensDB::check($token)) {
            return putError(
                'invalid token', 
                Users::ERROR_AUTH_INVALID, $response);            
        }

        $requested = FriendsDB::request($token[Users::ID_KEY], $friend_id);

        if ($requested === FALSE) {
            return putError(
                'database connection error', 
                DATABASE::ERROR_DATABASE_CONN, $response);             
        }

        return putJsonBody(array(
            'error' => false            
        ), 200, $response); 
    }

    static public function unrequest($response, $token, $friend_id)
    {
        if (!TokensDB::check($token)) {
            return putError(
                'invalid token', 
                Users::ERROR_AUTH_INVALID, $response);            
        }

        $unrequested = FriendsDB::unrequest($token[Users::ID_KEY], $friend_id);

        if ($unrequested === FALSE) {
            return putError(
                'database connection error', 
                DATABASE::ERROR_DATABASE_CONN, $response);             
        }

        return putJsonBody(array(
            'error' => false            
        ), 200, $response); 
    }

    static public function requests($response, $token)
    {
        if (!TokensDB::check($token)) {
            return putError(
                'invalid token', 
                Users::ERROR_AUTH_INVALID, $response);            
        }

        $requests = FriendsDB::requests($token[Users::ID_KEY]);

        if ($requests === FALSE) {
            return putError(
                'database connection error', 
                DATABASE::ERROR_DATABASE_CONN, $response);             
        }

        return putJsonBody(array(
            'error' => false,
            'requests' => $requests           
        ), 200, $response); 
    }

    static public function requested($response, $token)
    {
        if (!TokensDB::check($token)) {
            return putError(
                'invalid token', 
                Users::ERROR_AUTH_INVALID, $response);            
        }

        $requested = FriendsDB::requested($token[Users::ID_KEY]);

        if ($requested === FALSE) {
            return putError(
                'database connection error', 
                DATABASE::ERROR_DATABASE_CONN, $response);             
        }

        return putJsonBody(array(
            'error' => false,
            'friends' => $requested           
        ), 200, $response); 
    }

    static public function get($response, $token, $friend_id)
    {
        if (!TokensDB::check($token)) {
            return putError(
                'invalid token', 
                Users::ERROR_AUTH_INVALID, $response);            
        }

        $accepted = FriendsDB::get($token[Users::ID_KEY], 'accepted', $friend_id);

        return putJsonBody(array(
            'error' => false,                
            'friends' => $accepted
        ), 200, $response); 
    }   

    static public function getRequested($response, $token)
    {
        if (!TokensDB::check($token)) {
            return putError(
                'invalid token', 
                Users::ERROR_AUTH_INVALID, $response);            
        }

        $requested = Database::getFriends($token[Users::ID_KEY], 'requested', null);

        return putJsonBody(array(
            'error' => false,                
            'friends' => $requested
        ), 200, $response); 
    }

    static public function getWaiting($response, $token)
    {
        if (!TokensDB::check($token)) {
            return putError(
                'invalid token', 
                Users::ERROR_AUTH_INVALID, $response);            
        }

        $waiting = Database::getWaitingFriends($token[Users::ID_KEY], 'waiting', null);

        return putJsonBody(array(
            'error' => false,                
            'friends' => $waiting
        ), 200, $response); 
    }    

    static public function accept($response, $token, $friend_id)
    {
        if (!TokensDB::check($token)) {
            return putError(
                'invalid token', 
                Users::ERROR_AUTH_INVALID, $response);            
        }

        $accepted = FriendsDB::accept($token[Users::ID_KEY], $friend_id);

        if ($accepted === FALSE) {
            return putError(
                'database connection error', 
                DATABASE::ERROR_DATABASE_CONN, $response);             
        }

        return putJsonBody(array(
            'error' => false            
        ), 200, $response); 
    }    
}

?>