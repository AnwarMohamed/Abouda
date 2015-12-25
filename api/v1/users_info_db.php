<?php

class UsersInfoDB extends Database
{
    static public function get($user_id, $friend_id)
    {
        if (!($mysqli = UsersInfoDB::getConection()))
            return false;

        if (!$friend_id || $user_id == $friend_id) {
            $query_sql = "  SELECT
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
                                user_id = ?";

            $query = $mysqli->prepare($query_sql);
            $query->bind_param("s", $user_id);  

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
                                    user_id = ? 
                                AND 
                                    user_id
                                IN 
                                (
                                    SELECT
                                        friend_id
                                    FROM
                                        friendships
                                    WHERE
                                        user_id=? 
                                    AND 
                                        friendship_type = 'accepted'
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
                                    users_info
                                WHERE 
                                    user_id = ? 
                                AND 
                                    user_id
                                NOT IN
                                (
                                    SELECT
                                        friend_id
                                    FROM
                                        friendships
                                    WHERE
                                        user_id=?   
                                    AND 
                                        friendship_type = 'accepted'                                             
                                )
                            ) AS x";

            $query = $mysqli->prepare($query_sql);
            var_dump($mysqli->error);
            $query->bind_param("ssss",                 
                $friend_id, 
                $user_id,                 
                $friend_id,
                $user_id);                                                 
        }

        $query->bind_result(
            $user_fname,
            $user_lname,
            $user_mobile,
            $user_gender,
            $user_birthdate,
            $user_marital,
            $user_about);

        $query->execute();

        $info  = array();

        while($query->fetch()) {
            $info  = array(
                Users::FNAME_KEY => $user_fname,
                Users::LNAME_KEY => $user_lname,
                Users::MOBILE_KEY => $user_mobile,                
                Users::GENDER_KEY => $user_gender,
                Users::BIRTHDATE_KEY => $user_birthdate,
                Users::MARITAL_KEY => $user_martial,
                Users::ABOUT_KEY => $user_about
            );
        }

        $query->close();
        $mysqli->close();

        return $info;
    }

    static public function update($user_id, $info)
    {
        if (!($mysqli = UsersInfoDB::getConection()))
            return false;

        $query_sql = "  UPDATE
                            users_info
                        SET                            
                            user_fname = ?,
                            user_lname = ?,
                            user_mobile = ?,
                            user_gender = ?,
                            user_birthdate = ?,
                            user_marital = ?,
                            user_about = ?
                        WHERE
                            user_id = ?";

        $query = $mysqli->prepare($query_sql);
        $query->bind_param("ssssssss",             
            $info[Users::FNAME_KEY],
            $info[Users::LNAME_KEY],
            $info[Users::MOBILE_KEY],
            $info[Users::GENDER_KEY],
            $info[Users::BIRTHDATE_KEY],
            $info[Users::MARITAL_KEY],
            $info[Users::ABOUT_KEY],
            $user_id); 

        $query->execute();
        $query->close();
        $mysqli->close();
                
        return $info;
    }    
}

?>