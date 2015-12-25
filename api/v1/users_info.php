<?php

require 'users_info_db.php';

class UsersInfo
{
    const FNAME_KEY = 'fname';
    const LNAME_KEY = 'lname';
    const MOBILE_KEY = 'mobile';
    const GENDER_KEY = 'gender';
    const BIRTHDATE_KEY = 'birthdate';
    const MARTIAL_KEY = 'martial';
    const ABOUT_KEY = 'about';


	static public function get($response, $token, $friend_id)
	{
        if (!TokensDB::check($token)) {
            return putError(
                'invalid token', 
                Users::ERROR_AUTH_INVALID, $response);            
        }

        $info = UsersInfoDB::get($token[Users::ID_KEY], $friend_id);

        if ($info === FALSE) {
            return putError(
                'database connection error', 
                DATABASE::ERROR_DATABASE_CONN, $response);             
        }

        return putJsonBody(array(
            'error' => false,                
            'info' => $info
        ), 200, $response);   
	}

	static public function update($response, $token, $info)
	{
        if (!TokensDB::check($token)) {
            return putError(
                'invalid token', 
                Users::ERROR_AUTH_INVALID, $response);            
        }

        if (count($info) < 4) {
            return putError(
                'invalid request parameters', 
                Users::ERROR_FORMAT, $response);
        }

        $info[Users::FNAME_KEY] = Validator::filterName($info, Users::FNAME_KEY);
        $info[Users::LNAME_KEY] = Validator::filterName($info, Users::LNAME_KEY);        
        $info[Users::GENDER_KEY] = Validator::filterGender($info, Users::GENDER_KEY);        
        $info[Users::BIRTHDATE_KEY] = Validator::filterDate($info, Users::BIRTHDATE_KEY);
        $info[Users::MOBILE_KEY] = Validator::filterMobile($info, Users::MOBILE_KEY);
        $info[Users::ABOUT_KEY] = Validator::filterAbout($info, Users::ABOUT_KEY);
        $info[Users::MARITAL_KEY] = Validator::filterMarital($info, Users::MARITAL_KEY);

        if (!$info[Users::FNAME_KEY]) {
            return putError(
                'invalid firstname parameter', 
                Users::ERROR_FNAME_FORMAT, $response);
        } 
        else if (!$info[Users::LNAME_KEY]) {
            return putError(
                'invalid lastname parameter', 
                Users::ERROR_LNAME_FORMAT, $response);
        }       
        else if (!$info[Users::GENDER_KEY]) {
            return putError(
                'invalid gender parameter', 
                Users::ERROR_GENDER_FORMAT, $response);
        }
        else if (!$info[Users::BIRTHDATE_KEY]) {
            return putError(
                'invalid birthdate parameter', 
                Users::ERROR_BIRTHDATE_FORMAT, $response);
        }   
        
        $info[Users::GENDER_KEY] = ($info[Users::GENDER_KEY] == "male");

        $info = UsersInfoDB::update($token[Users::ID_KEY], $info);

        if ($info === FALSE) {
            return putError(
                'database connection error', 
                DATABASE::ERROR_DATABASE_CONN, $response);             
        }

        return putJsonBody(array(
            'error' => false,                
            'info' => $info
        ), 200, $response);
	}
}

?>