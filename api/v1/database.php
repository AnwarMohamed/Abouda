<?php

class Database
{
    const ERROR_DATABASE_CONN = 1220;    

    static public function getConection() 
    {
        $mysqli = new mysqli("localhost", "root", "root", "abouda");
        return $mysqli->connect_errno ? false: $mysqli;
    }

    static public function getInfos($user_id){
        $query = $mysqli->prepare("SELECT * FROM `users_info` WHERE user_id = ?");
        $query->bind_param("s", $user_id);
        while($row = $query->fetch_row()) {
            $rows[]=$row;
        }
        $query->execute();
        $query->close();

    }

    static public function getFriends($user_id, $friendship_type, $friend_id)
    {
        if (!($mysqli = Database::getConection()))
            return false;

       $query_sql = "SELECT 
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
                      INNER JOIN 
                            pictures
                      ON 
                            pictures.picture_id = user_thumbnail
                      WHERE 
                            friendships.user_id = ? AND friendship_type = ?";

        $query = $mysqli->prepare($query_sql);        
        $query->bind_param("ss", $user_id, $friendship_type);
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
}

?>