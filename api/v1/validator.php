<?php

class Validator
{
    static public function filterName($data, $key) 
    {   
        if (!isset($data[$key]))
            return false;

        if (!preg_match("/^[a-zA-Z'-]+$/", trim($data[$key])))
            return false;

        return filter_var(ucfirst(trim($data[$key])), FILTER_SANITIZE_STRING);
    }

    static public function filterMobile($data, $key) 
    {   
        if (isset($data[$key]))            
            return preg_match("/[^0-9]/", trim($data[$key]));

        return null;
    }

    static public function filterEmail($data, $key) 
    {
        if (!isset($data[$key]))
            return false;

        if (!filter_var(trim($data[$key]), FILTER_VALIDATE_EMAIL))
            return false;

        return filter_var(trim($data[$key]), FILTER_SANITIZE_EMAIL);
    }

    static public function filterPassword($data, $key)
    {
        if (!isset($data[$key]))
            return false;

        if (strlen(trim($data[$key])) < 8)
            return false;

        return trim($data[$key]);
    }

    static public function filterGender($data, $key) 
    {
        if (!isset($data[$key]))
            return false;

        if (strcmp(trim($data[$key]), "male") &&
            strcmp(trim($data[$key]), "female"))
            return false;

        return trim($data[$key]);
    }

    static public function filterDate($data, $key, $format = 'Y-m-d') 
    {
        if (!isset($data[$key]))
            return false;
        
        $d = DateTime::createFromFormat($format, trim($data[$key]));
        
        if (!$d || $d->format($format) != trim($data[$key]))
            return false;

        return trim($data[$key]);
    }

    static public function filterText($data, $key) 
    {   
        if (!isset($data[$key]))
            return false;

        if (!strlen(trim($data[$key])))
            return false;

        return filter_var(trim($data[$key]), FILTER_SANITIZE_STRING);
    }    

    static public function filterAbout($data, $key) 
    {
        if (isset($data[$key]) && strlen(trim($data[$key])))
            return filter_var(trim($data[$key]), FILTER_SANITIZE_STRING);

        return null;
    }

    static public function filterPrivacy($data, $key) 
    {
        if (!isset($data[$key]))
            return false;

        if (strcmp(trim($data[$key]), "public") &&
            strcmp(trim($data[$key]), "private"))
            return false;

        return trim($data[$key]);
    } 

    static public function filterMarital($data, $key) 
    {
        if (isset($data[$key]))
            return trim($data[$key]);

        return null;
    }        
}

?>