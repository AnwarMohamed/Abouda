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
                            (
                                SELECT
                                    user_fname,
                                    user_lname,
                                    user_mobile,
                                    user_gender,
                                    user_birthdate,
                                    user_marital,
                                    user_about
                                FROM
                                    users_info
                                WHERE 
                                    ?
                                IN (
                                    SELECT
                                        friend_id
                                    FROM
                                        friendships
                                    WHERE
                                        user_id=?        
                                    )
                            
                                UNION
                                
                                SELECT
                                    user_fname,
                                    user_lname,
                                    user_mobile,
                                    user_gender,
                                    NULL as user_birthdate,
                                    user_marital,
                                    NULL as user_about
                                FROM
                                    user_info
                                WHERE 
                                    ?
                                NOT IN(
                                    SELECT
                                        friend_id
                                    FROM
                                        friendships
                                    WHERE
                                        user_id=?        
                                    )
                            )";                        
        }

        $query = $mysqli->prepare($query_sql);
        $query->bind_param("ssss", 
            $friend_id, 
            $user_id, 
            $friend_id,
            $user_id;)

        $query->bind_result(
            $user_fname,
            $user_lname,
            $user_mobile,
            $user_gender,
            $user_birthdate,
            $user_marital,
            $user_about;)

        $query->execute();

        while($query->fetch()) {
            $info  = array(
                UsersInfoDB::FNAME_KEY => $usersinfo_user_fname,
                UsersInfoDB::LNAME_KEY => $usersinfo_user_lname,
                UsersInfoDB::MOBILE_KEY => $usersinfo_user_mobile,                
                UsersInfoDB::GENDER_KEY => $usersinfo_user_gender,
                UsersInfoDB::BIRTHDATE_KEY => $usersinfo_user_birthdate,
                UsersInfoDB::MARTIAL_KEY => $usersinfo_user_martial,
                UsersInfoDB::ABOUT_KEY => $usersinfo_user_about
            );
        }

        $query->close();
        $mysqli->close();

        return $info;

    }
}

?>