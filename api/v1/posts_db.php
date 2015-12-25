<?php

class PostsDB extends Database
{
    static public function get($user_id, $post_id) 
    {
        if (!($mysqli = Database::getConection()))
            return false;                 

        $query_sql = "  SELECT 
                            post_id, 
                            posts.user_id,
                            concat(user_fname, ' ', user_lname),                            
                            post_privacy, 
                            post_timestamp, 
                            post_text, 
                            picture_path
                        FROM 
                            posts 
                        LEFT JOIN 
                            pictures 
                        ON 
                            picture_id = post_picture
                        LEFT JOIN
                            users_info
                        ON
                            posts.user_id = users_info.user_id
                        WHERE
                            post_id
                        IN  
                        (                            
                            SELECT 
                                post_id
                            FROM
                                posts
                            WHERE
                                post_privacy=0 OR user_id = ?
                                         
                            UNION 
                            
                            SELECT 
                                post_id
                            FROM
                                friendships
                            INNER JOIN
                                posts
                            ON
                                friendships.friend_id = posts.user_id AND
                                friendships.user_id = ?

                        ) AND post_id = ?";

        $query = $mysqli->prepare($query_sql);
        var_dump($mysqli->error);
        $query->bind_param("sss",            
            $user_id,
            $user_id,
            $post_id); 

        $query->bind_result(
            $post_id,
            $post_user_id, 
            $post_user_name,
            $post_privacy, 
            $post_timestamp, 
            $post_text, 
            $post_picture);

        $query->execute();

        $post = array();

        while($query->fetch()) {
            $post  = array(
                Posts::ID_KEY => $post_id,
                Posts::USER_ID_KEY => $post_user_id,
                Posts::USER_NAME_KEY => $post_user_name,
                Posts::PRIVACY_PUBLIC_KEY => $post_privacy,
                Posts::TIMESTAMP_KEY => $post_timestamp,
                Posts::TEXT_KEY => $post_text,
                Posts::PICTURE_KEY => $post_picture
            );
        }
        
        $query->close();
        $mysqli->close();

        return $post;
    }

    static public function getAll($user_id)
    {
        if (!($mysqli = Database::getConection()))
            return false; 

        $query_sql = "  SELECT 
                            post_id, 
                            user_id,
                            post_privacy, 
                            post_timestamp, 
                            post_text, 
                            picture_path
                        FROM 
                            posts 
                        INNER JOIN 
                            pictures 
                        ON 
                            picture_id = post_picture 
                        WHERE
                            post_id
                        IN  
                        (
                            (
                                SELECT 
                                    post_id
                                FROM
                                    posts
                                WHERE
                                    post_privacy=0
                            ) 
                            UNION 
                            (
                                SELECT 
                                    post_id
                                FROM
                                    friendships
                                INNER JOIN
                                    posts
                                ON
                                    friendships.friend_id = posts.user_id AND
                                    friendships.user_id = ?                             
                            )
                            
                        ) AND post_id = ?";

        $query = $mysqli->prepare($query_sql);
        $query->bind_param("ss",            
            $user_id,           
            $post_id);

        $query->bind_result(
            $post_id, 
            $post_user_id,
            $post_privacy, 
            $post_timestamp, 
            $post_text, 
            $post_picture);

        $query->execute();

        $posts = array();

        while($query->fetch()) {
            $posts[]  = array(
                Posts::ID_KEY => $post_id,
                Posts::PRIVACY_PUBLIC_KEY => $post_privacy,
                Posts::TIMESTAMP_KEY => $post_timestamp,
                Posts::TEXT_KEY => $post_text,
                Posts::PICTURE_KEY => $post_picture
            );
        }
        
        $query->close();
        $mysqli->close();

        return $posts;
    }    

    static public function create($user_id, $post) 
    {
        if (!($mysqli = Database::getConection()))
            return false;                 

        $mysqli->autocommit(FALSE);

        $query_sql = "  INSERT INTO 
                            posts
                        VALUES 
                            (default,?,?,NOW(),?,default)";

        $query = $mysqli->prepare($query_sql);
        $query->bind_param("sss", 
            $user_id, 
            $post[Posts::PRIVACY_KEY],            
            $post[Posts::TEXT_KEY]);   

        $query->execute();        
        $query->close();

        $post[Posts::ID_KEY] = strval($mysqli->insert_id);

        $mysqli->commit();
        $mysqli->close(); 

        return $post;
    }    
}

?>