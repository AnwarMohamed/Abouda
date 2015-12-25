<?php

class FriendsDB extends Database
{
    static public function block($user_id, $friend_id)
    {
        if (!($mysqli = FriendsDB::connect()))
            return false;

        if ($user_id != $friend_id) {
            $mysqli->autocommit(FALSE);

            $query_sql = "  INSERT INTO
                                friendships
                            VALUES
                                (?,?,'blocked',NOW())
                            ON 
                                DUPLICATE KEY 
                            UPDATE
                                friendship_timestamp = NOW(),
                                friendship_type = 'blocked'";

            $query = $mysqli->prepare($query_sql);                 
            $query->bind_param("s", $user_id); 
            $query->execute();  

            $query_sql = "  DELETE FROM
                                friendships
                            WHERE
                                user_id = ?
                            AND
                                friend_id = ?";

            $query = $mysqli->prepare($query_sql);                  
            $query->bind_param("ss", 
                $friend_id, 
                $user_id); 

            $query->execute();                         
            $query->close();

            $mysqli->commit();            
        }

        $mysqli->close();

        return true;
    }

    static public function blocked($user_id)
    {
        if (!($mysqli = Database::connect()))
            return false;

       $query_sql = "   SELECT 
                            friend_id, 
                            CONCAT(user_fname, ' ', user_lname), 
                            friendship_timestamp,
                            picture_path
                        FROM 
                            friendships
                        INNER JOIN
                            users_info
                        ON
                            users_info.user_id = friend_id                            
                        LEFT JOIN 
                            pictures
                        ON 
                            pictures.picture_id = user_thumbnail
                        WHERE 
                            friendships.user_id = ? 
                        AND 
                            friendship_type = 'blocked'";

        $query = $mysqli->prepare($query_sql);        
        $query->bind_param("s", $user_id);
        $query->bind_result(
            $friend_id, 
            $friend_name, 
            $friendship_timestamp, 
            $friend_thumbnail);

        $query->execute();

        $friends = array();

        while($query->fetch()) {
            $friends[]  = array(
                'id' => $friend_id,
                'name' => $friend_name,
                'timestamp' => $friendship_timestamp,
                'thumbnail' => $friend_thumbnail                
            );
        }
        
        $query->close();
        $mysqli->close();

        return $friends;        
    }

    static public function unblock($user_id, $friend_id)
    {
        if (!($mysqli = FriendsDB::connect()))
            return false;

        if ($user_id != $friend_id) {            
            $query_sql = "  DELETE FROM
                                friendships
                            WHERE
                                user_id = ?
                            AND
                                friend_id = ?
                            AND
                                friendship_type = 'blocked'";

            $query = $mysqli->prepare($query_sql);                  
            $query->bind_param("ss",                 
                $user_id,
                $friend_id); 

            $query->execute();                         
            $query->close();            
        }

        $mysqli->close();

        return true;
    } 

    static public function unrequest($user_id, $friend_id)
    {
        if (!($mysqli = FriendsDB::connect()))
            return false;

        if ($user_id != $friend_id) {            
            $query_sql = "  DELETE FROM
                                friendships
                            WHERE
                                user_id = ?
                            AND
                                friend_id = ?
                            AND
                                friendship_type = 'requested'";

            $query = $mysqli->prepare($query_sql);                  
            $query->bind_param("ss",                 
                $user_id,
                $friend_id); 

            $query->execute();                         
            $query->close();
        }

        $mysqli->close();

        return true;
    }

    static public function request($user_id, $friend_id)
    {
        if (!($mysqli = FriendsDB::connect()))
            return false;

        if ($user_id != $friend_id) {            
            $query_sql = "  INSERT INTO
                                friendships
                            SELECT
                                ?,?,'requested',NOW()
                            FROM
                                friendships
                            WHERE
                                ?
                            NOT IN 
                            (
                                SELECT 
                                    friend_id
                                FROM
                                    friendships
                                WHERE
                                    user_id = ?
                                AND
                                    friend_id = ?
                            )";                            

            $query = $mysqli->prepare($query_sql); 
            var_dump($mysqli->error);                 
            $query->bind_param("sss",
                $user_id,
                $friend_id,
                $friend_id,
                $user_id,
                $friend_id); 

            $query->execute();                         
            $query->close();
        }

        $mysqli->close();

        return true;
    }     
}

?>