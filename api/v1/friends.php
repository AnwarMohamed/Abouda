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


    static public function getAccepted($response, $token, $friend_id)
    {
        if (!TokensDB::check($token)) {
            return putError(
                'invalid token', 
                Users::ERROR_AUTH_INVALID, $response);            
        }

        $accepted = Database::getFriends($token[Users::ID_KEY], 'accepted', $friend_id);

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
}

?>