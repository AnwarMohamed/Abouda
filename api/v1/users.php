<?php

require 'validator.php';
require 'users_db.php';

class Users
{
    const FNAME_KEY = 'fname';
    const LNAME_KEY = 'lname';
    const EMAIL_KEY = 'email';
    const PASSWORD_KEY = 'password';
    const GENDER_KEY = 'gender';
    const BIRTHDATE_KEY = 'birthdate';
    const MOBILE_KEY = 'mobile';
    const MARITAL_KEY = 'marital';
    const ABOUT_KEY = 'about';

    const ID_KEY = 'user_id';
    const TOKEN_KEY = 'token';
    const REMOTE_ADDR_KEY = 'remote_addr';

    const ERROR_FNAME_FORMAT = 1200;
    const ERROR_LNAME_FORMAT = 1201;
    const ERROR_EMAIL_FORMAT = 1202;
    const ERROR_PASSWORD_FORMAT = 1203;
    const ERROR_GENDER_FORMAT = 1204;
    const ERROR_BIRTHDATE_FORMAT = 1205;

    const ERROR_FORMAT = 1206;

    const ERROR_EMAIL_DUPLICATE = 1210;
    const ERROR_AUTH_INVALID = 1211;

    static public function create($response, $user)
    {
        if (count($user) != 6) {
            return putError(
                'invalid request parameters', 
                Users::ERROR_FORMAT, $response);
        }

        $user[Users::FNAME_KEY] = Validator::filterName($user, Users::FNAME_KEY);
        $user[Users::LNAME_KEY] = Validator::filterName($user, Users::LNAME_KEY);
        $user[Users::EMAIL_KEY] = Validator::filterEmail($user, Users::EMAIL_KEY);
        $user[Users::GENDER_KEY] = Validator::filterGender($user, Users::GENDER_KEY);
        $user[Users::PASSWORD_KEY] = Validator::filterPassword($user, Users::PASSWORD_KEY);        
        $user[Users::BIRTHDATE_KEY] = Validator::filterDate($user, Users::BIRTHDATE_KEY);
        $user[Users::REMOTE_ADDR_KEY] = $_SERVER['REMOTE_ADDR'];

        if (!$user[Users::FNAME_KEY]) {
            return putError(
                'invalid firstname parameter', 
                Users::ERROR_FNAME_FORMAT, $response);
        } 
        else if (!$user[Users::LNAME_KEY]) {
            return putError(
                'invalid lastname parameter', 
                Users::ERROR_LNAME_FORMAT, $response);
        }
        else if (!$user[Users::EMAIL_KEY]) {
            return putError(
                'invalid email parameter', 
                Users::ERROR_EMAIL_FORMAT, $response);
        }
        else if (!$user[Users::PASSWORD_KEY]) {
            return putError(
                'invalid password parameter', 
                Users::ERROR_PASSWORD_FORMAT, $response);
        }
        else if (!$user[Users::GENDER_KEY]) {
            return putError(
                'invalid gender parameter', 
                Users::ERROR_GENDER_FORMAT, $response);
        }
        else if (!$user[Users::BIRTHDATE_KEY]) {
            return putError(
                'invalid birthdate parameter', 
                Users::ERROR_BIRTHDATE_FORMAT, $response);
        }   
        
        $user[Users::GENDER_KEY] = ($user[Users::GENDER_KEY] == "male");

        $duplicate = UsersDB::duplicate($user[Users::EMAIL_KEY]);

        if ($duplicate) {
            return putError(
                'duplicate email parameter', 
                Users::ERROR_EMAIL_DUPLICATE, $response);
        }

        $user = UsersDB::create($user);

        if ($user === FALSE) {
            return putError(
                'database connection error', 
                DATABASE::ERROR_DATABASE_CONN, $response);   
        }

        return putJsonBody(array(
            'error' => false,
            'result' => array(
                'user_id' => $user[Users::ID_KEY],
                'token' => $user[Users::TOKEN_KEY],
            )
        ), 200, $response); 
    }

    static public function auth($response, $creds) 
    {
        if (count($creds) != 2) {
            return putError(
                'invalid request parameters', 
                Users::ERROR_FORMAT, $response);
        }

        $creds[Users::EMAIL_KEY] = Validator::filterEmail($creds, Users::EMAIL_KEY);        
        $creds[Users::PASSWORD_KEY] = Validator::filterPassword($creds, Users::PASSWORD_KEY);
        $creds[Users::REMOTE_ADDR_KEY] = $_SERVER['REMOTE_ADDR'];

        if (!$creds[Users::EMAIL_KEY]) {
            return putError(
                'invalid email parameter', 
                Users::ERROR_EMAIL_FORMAT, $response);
        }
        else if (!$creds[Users::PASSWORD_KEY]) {
            return putError(
                'invalid password parameter', 
                Users::ERROR_PASSWORD_FORMAT, $response);
        }

        $creds = UsersDB::auth($creds);

        if ($creds === FALSE) {
            return putError(
                'invalid user credentials', 
                Users::ERROR_AUTH_INVALID, $response);
        }

        return putJsonBody(array(
            'error' => false,
            'result' => array(
                'user_id' => $creds[Users::ID_KEY],
                'token' => $creds[Users::TOKEN_KEY],
            )
        ), 200, $response);  
    }

    static public function delete($response, $token)
    {
        if (!TokensDB::check($token)) {
            return putError(
                'invalid token', 
                Users::ERROR_AUTH_INVALID, $response);            
        }

        $delete = UsersDB::delete($token[Users::ID_KEY]);

        if ($delete === FALSE) {
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