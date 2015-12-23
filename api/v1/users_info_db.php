<?php

class UsersInfoDB extends Database
{
    static public function get($user_id, $friend_id)
    {
        if (!($mysqli = UsersInfoDB::getConection()))
            return false;

        if (!$friend_id) {
            $query_sql = "  SELECT
                                user_fname,
                                user_lname,
                                user_mobile,
                                user_gender,
                                user_birthdate,
                                user_marital,
                                user_about
                            FROM
                                user_info
                            WHERE
                                user_id = ?";
        } else {
            $query_sql = "  SELECT
                                user_fname,
                                user_lname,
                                user_mobile,
                                user_gender,
                                user_birthdate,
                                user_marital,
                                user_about
                            FROM
                                
                            WHERE
                                user_id = ?";
        }
    }
}

?>