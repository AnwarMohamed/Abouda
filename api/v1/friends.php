<?php

require_once 'tokens_db.php';

class Friends
{
    static public function getBlocked($response, $token)
    {
        if (!TokensDB::check($token)) {
            return putError(
                'invalid token', 
                Users::ERROR_AUTH_INVALID, $response);            
        }

        $blocked = Database::getFriends($token[Users::ID_KEY], 'blocked', null);

        return putJsonBody(array(
            'error' => false,                
            'friends' => $blocked
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