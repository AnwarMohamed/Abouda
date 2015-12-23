<?php

require 'validator.php';
require 'database.php';

class Users
{
    const FNAME_KEY = 'fname';
    const LNAME_KEY = 'lname';
    const EMAIL_KEY = 'email';
    const PASSWORD_KEY = 'password';
    const GENDER_KEY = 'gender';
    const BIRTHDATE_KEY = 'birthdate';

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

    static public function newUser($response, $data)
    {
        if (count($data) != 6) {
            return putError(
                'invalid request parameters', 
                Users::ERROR_FORMAT, $response);
        }

        $data[Users::FNAME_KEY] = Validator::filterName($data, Users::FNAME_KEY);
        $data[Users::LNAME_KEY] = Validator::filterName($data, Users::LNAME_KEY);
        $data[Users::EMAIL_KEY] = Validator::filterEmail($data, Users::EMAIL_KEY);
        $data[Users::GENDER_KEY] = Validator::filterGender($data, Users::GENDER_KEY);
        $data[Users::PASSWORD_KEY] = Validator::filterPassword($data, Users::PASSWORD_KEY);        
        $data[Users::BIRTHDATE_KEY] = Validator::filterDate($data, Users::BIRTHDATE_KEY);
        $data[Users::REMOTE_ADDR_KEY] = $_SERVER['REMOTE_ADDR'];

        if (!$data[Users::FNAME_KEY]) {
            return putError(
                'invalid firstname parameter', 
                Users::ERROR_FNAME_FORMAT, $response);
        } 
        else if (!$data[Users::LNAME_KEY]) {
            return putError(
                'invalid lastname parameter', 
                Users::ERROR_LNAME_FORMAT, $response);
        }
        else if (!$data[Users::EMAIL_KEY]) {
            return putError(
                'invalid email parameter', 
                Users::ERROR_EMAIL_FORMAT, $response);
        }
        else if (!$data[Users::PASSWORD_KEY]) {
            return putError(
                'invalid password parameter', 
                Users::ERROR_PASSWORD_FORMAT, $response);
        }
        else if (!$data[Users::GENDER_KEY]) {
            return putError(
                'invalid gender parameter', 
                Users::ERROR_GENDER_FORMAT, $response);
        }
        else if (!$data[Users::BIRTHDATE_KEY]) {
            return putError(
                'invalid birthdate parameter', 
                Users::ERROR_BIRTHDATE_FORMAT, $response);
        }   
        
        $data[Users::GENDER_KEY] = ($data[Users::GENDER_KEY] == "male");

        return Database::newUser($response, $data);
    }

    static public function authenticate($response, $data) 
    {
        if (count($data) != 2) {
            return putError(
                'invalid request parameters', 
                Users::ERROR_FORMAT, $response);
        }

        $data[Users::EMAIL_KEY] = Validator::filterEmail($data, Users::EMAIL_KEY);        
        $data[Users::PASSWORD_KEY] = Validator::filterPassword($data, Users::PASSWORD_KEY);
        $data[Users::REMOTE_ADDR_KEY] = $_SERVER['REMOTE_ADDR'];

        if (!$data[Users::EMAIL_KEY]) {
            return putError(
                'invalid email parameter', 
                Users::ERROR_EMAIL_FORMAT, $response);
        }
        else if (!$data[Users::PASSWORD_KEY]) {
            return putError(
                'invalid password parameter', 
                Users::ERROR_PASSWORD_FORMAT, $response);
        }

        return Database::authenticate($response, $data);
    }

    static public function deleteMe($response, $token)
    {
        if (!$token || !Database::checkToken(null, $token)) {
            return putError(
                'invalid token', 
                Users::ERROR_AUTH_INVALID, $response);            
        }

        
    }

    static public function getMe($response)
    {

    }

    static public function updateMe($app, $data)
    {

    }
}

?>