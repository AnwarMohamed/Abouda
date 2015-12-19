<?php

require 'validator.php';

class Users
{
    const FNAME_KEY = 'fname';
    const LNAME_KEY = 'lname';
    const EMAIL_KEY = 'email';
    const PASSWORD_KEY = 'password';
    const GENDER_KEY = 'gender';
    const BIRTHDATE_KEY = 'birthdate';
    
    const ERROR_FNAME_FORMAT = 1200;
    const ERROR_LNAME_FORMAT = 1201;
    const ERROR_EMAIL_FORMAT = 1202;
    const ERROR_PASSWORD_FORMAT = 1203;
    const ERROR_GENDER_FORMAT = 1204;
    const ERROR_BIRTHDATE_FORMAT = 1205;

    const ERROR_EMAIL_DUPLICATE = 1210;

    static public function newUser($app, $data)
    {

        $fname_filtered = Validator::filterName($data, Users::FNAME_KEY);
        $lname_filtered = Validator::filterName($data, Users::LNAME_KEY);
        $email_filtered = Validator::filterEmail($data, Users::EMAIL_KEY);
        $password_filtered = Validator::filterPassword($data, Users::PASSWORD_KEY);
        $gender_filtered = Validator::filterGender($data, Users::GENDER_KEY);
        $birthdate_filtered = Validator::filterDate($data, Users::BIRTHDATE_KEY);


        if (!$fname_filtered) {
            return putError(
                'invalid firstname parameter', 
                Users::ERROR_FNAME_FORMAT, $app);
        } 
        else if (!$lname_filtered) {
            return putError(
                'invalid lastname parameter', 
                Users::ERROR_LNAME_FORMAT, $app);
        }
        else if (!$email_filtered) {
            return putError(
                'invalid email parameter', 
                Users::ERROR_EMAIL_FORMAT, $app);
        }
        else if (!$password_filtered) {
            return putError(
                'invalid password parameter', 
                Users::ERROR_PASSWORD_FORMAT, $app);
        }
        else if (!$gender_filtered) {
            return putError(
                'invalid gender parameter', 
                Users::ERROR_GENDER_FORMAT, $app);
        }
        else if (!$birthdate_filtered) {
            return putError(
                'invalid birthdate parameter', 
                Users::ERROR_BIRTHDATE_FORMAT, $app);
        }   

        
        if($gender_filtered=="male"){
            $gender_filtered=TRUE;
        }
        else{
            $gender_filtered=FALSE;
        }

        echo $fname_filtered .':'. $lname_filtered .':'. $email_filtered .':'. $password_filtered;



    }

    static public function getMe($app)
    {

    }

    static public function deleteMe($app)
    {

    }

    static public function updateMe($app, $data)
    {

    }
}

?>