<?php

class PostsDB extends Database
{
    static public function get($user_id, $post_id) 
    {
        if (!($mysqli = PostsDB::connect()))
            return false;                 

        $query_sql = "  SELECT 
                            posts.post_id, 
                            posts.user_id,
                            concat(user_fname, ' ', user_lname),  
                            up.picture_path,                          
                            post_privacy, 
                            post_timestamp, 
                            post_text, 
                            pp.picture_path
                        FROM 
                            posts 
                        LEFT JOIN 
                            pictures as pp
                        ON 
                            pp.picture_id = post_picture 
                        LEFT JOIN
                            users_info
                        ON
                            posts.user_id = users_info.user_id                            
                        LEFT JOIN 
                            pictures as up
                        ON 
                            up.picture_id = users_info.user_thumbnail                    

                        WHERE
                            posts.post_id
                        IN  
                        (                            
                            SELECT 
                                post_id
                            FROM
                                posts
                            WHERE
                                post_privacy=1 
                            OR 
                                user_id = ?
                                         
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

                        ) 
                        AND 
                            post_id = ?";

        $query = $mysqli->prepare($query_sql);        
        $query->bind_param("sss",            
            $user_id,
            $user_id,
            $post_id); 

        $query->bind_result(
            $post_id, 
            $post_user_id,
            $post_user_name,
            $post_user_thumbnail,
            $post_privacy, 
            $post_timestamp, 
            $post_text, 
            $post_picture);

        $query->execute();

        $posts = array();

        while($query->fetch()) {
            $posts  = array(
                Posts::ID_KEY => $post_id,
                Posts::USER_ID_KEY => $post_user_id,
                Posts::USER_NAME_KEY => $post_user_name,
                Posts::USER_THUMBNAIL_KEY => $post_user_thumbnail,
                Posts::PRIVACY_PUBLIC_KEY => $post_privacy,
                Posts::TIMESTAMP_KEY => $post_timestamp,
                Posts::TEXT_KEY => $post_text,
                Posts::PICTURE_KEY => $post_picture
            );
        }
        
        $query->close();

        foreach ($posts as &$post) {
            $query_sql = "  SELECT 
                                count(comment_id)
                            FROM
                                comments
                            WHERE
                                post_id = ?";

            $query = $mysqli->prepare($query_sql);        
            $query->bind_param("s", $post[Posts::ID_KEY]);
            $query->bind_result($post_comments_count);

            $query->execute();
            
            while($query->fetch()) {
                $post[Posts::COMMENTS_COUNT_KEY] = $post_comments_count;
            }
            
            $query->close();

            $query_sql = "  SELECT 
                                count(user_id)
                            FROM
                                likes
                            WHERE
                                post_id = ?";

            $query = $mysqli->prepare($query_sql);        
            $query->bind_param("s", $post[Posts::ID_KEY]);
            $query->bind_result($post_likes_count);

            $query->execute();
            
            while($query->fetch()) {
                $post[Posts::LIKES_COUNT_KEY] = $post_likes_count;
            }
            
            $query->close(); 

            $query_sql = "  SELECT 
                                count(user_id)
                            FROM
                                likes
                            WHERE
                                post_id = ?
                            AND
                                user_id = ?";

            $query = $mysqli->prepare($query_sql);        
            $query->bind_param("ss", 
                $post[Posts::ID_KEY],
                $user_id);

            $query->bind_result($post_liked);

            $query->execute();
            
            while($query->fetch()) {
                $post[Posts::LIKED_KEY] = $post_liked;
            }
            
            $query->close();                         
        }

        unset($post);

        $mysqli->close();

        return $posts;
    }

    static public function home($user_id)
    {
        if (!($mysqli = PostsDB::connect()))
            return false; 

        $query_sql = "  SELECT 
                            posts.post_id, 
                            posts.user_id,
                            concat(user_fname, ' ', user_lname), 
                            up.picture_path,
                            post_privacy, 
                            post_timestamp, 
                            post_text, 
                            pp.picture_path                       
                        FROM 
                            posts 
                        LEFT JOIN 
                            pictures as pp
                        ON 
                            pp.picture_id = post_picture 
                        LEFT JOIN
                            users_info
                        ON
                            posts.user_id = users_info.user_id                            
                        LEFT JOIN 
                            pictures as up
                        ON 
                            up.picture_id = users_info.user_thumbnail 

                        WHERE
                            posts.post_id
                        IN  
                        (                            
                            SELECT 
                                post_id
                            FROM
                                posts
                            WHERE
                                post_privacy = 1
                            OR
                                user_id = ?
                            
                            UNION 
                            
                            SELECT 
                                post_id
                            FROM
                                friendships
                            INNER JOIN
                                posts
                            ON
                                friendships.friend_id = posts.user_id 
                            AND
                                friendships.friendship_type = 'accepted'
                            AND                                    
                                friendships.user_id = ?                                                                                    
                        )
                        ORDER BY
                            post_timestamp DESC";

        $query = $mysqli->prepare($query_sql);        
        $query->bind_param("ss", $user_id, $user_id);

        $query->bind_result(
            $post_id, 
            $post_user_id,
            $post_user_name,
            $post_user_thumbnail,
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
                Posts::PICTURE_KEY => $post_picture,
                Posts::USER_ID_KEY => $post_user_id,
                Posts::USER_NAME_KEY => $post_user_name,
                Posts::USER_THUMBNAIL_KEY => $post_user_thumbnail
            );
        }
        
        $query->close();

        foreach ($posts as &$post) {
            $query_sql = "  SELECT 
                                count(comment_id)
                            FROM
                                comments
                            WHERE
                                post_id = ?";

            $query = $mysqli->prepare($query_sql);        
            $query->bind_param("s", $post[Posts::ID_KEY]);
            $query->bind_result($post_comments_count);

            $query->execute();
            
            while($query->fetch()) {
                $post[Posts::COMMENTS_COUNT_KEY] = $post_comments_count;
            }
            
            $query->close();

            $query_sql = "  SELECT 
                                count(user_id)
                            FROM
                                likes
                            WHERE
                                post_id = ?";

            $query = $mysqli->prepare($query_sql);        
            $query->bind_param("s", $post[Posts::ID_KEY]);
            $query->bind_result($post_likes_count);

            $query->execute();
            
            while($query->fetch()) {
                $post[Posts::LIKES_COUNT_KEY] = $post_likes_count;
            }
            
            $query->close();     

            $query_sql = "  SELECT 
                                count(user_id)
                            FROM
                                likes
                            WHERE
                                post_id = ?
                            AND
                                user_id = ?";

            $query = $mysqli->prepare($query_sql);        
            $query->bind_param("ss", 
                $post[Posts::ID_KEY],
                $user_id);

            $query->bind_result($post_liked);

            $query->execute();
            
            while($query->fetch()) {
                $post[Posts::LIKED_KEY] = $post_liked;
            }
            
            $query->close();                     
        }

        unset($post);

        $mysqli->close();

        return $posts;
    }    

    static public function all($user_id, $friend_id)
    {
        if (!($mysqli = PostsDB::connect()))
            return false; 

        if ($friend_id == null) {
            $friend_id = $user_id;
        }

        $query_sql = "  SELECT 
                            posts.post_id, 
                            posts.user_id,
                            concat(user_fname, ' ', user_lname), 
                            up.picture_path,
                            post_privacy, 
                            post_timestamp, 
                            post_text, 
                            pp.picture_path                       
                        FROM 
                            posts 
                        LEFT JOIN 
                            pictures as pp
                        ON 
                            pp.picture_id = post_picture 
                        LEFT JOIN
                            users_info
                        ON
                            posts.user_id = users_info.user_id                            
                        LEFT JOIN 
                            pictures as up
                        ON 
                            up.picture_id = users_info.user_thumbnail 

                        WHERE
                            posts.user_id = ?
                        AND                                            
                            posts.post_id
                        IN  
                        (                            
                            SELECT 
                                post_id
                            FROM
                                posts
                            WHERE
                                post_privacy = 1
                            OR
                                user_id = ?
                            
                            UNION 
                            
                            SELECT 
                                post_id
                            FROM
                                friendships
                            INNER JOIN
                                posts
                            ON
                                friendships.friend_id = posts.user_id 
                            AND
                                friendships.friendship_type = 'accepted'
                            AND                                    
                                friendships.user_id = ?                                                                                    
                        )
                        ORDER BY
                            post_timestamp DESC";

        $query = $mysqli->prepare($query_sql);        
        $query->bind_param("sss", 
            $friend_id, 
            $user_id, 
            $user_id);

        $query->bind_result(
            $post_id, 
            $post_user_id,
            $post_user_name,
            $post_user_thumbnail,
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
                Posts::PICTURE_KEY => $post_picture,
                Posts::USER_ID_KEY => $post_user_id,
                Posts::USER_NAME_KEY => $post_user_name,
                Posts::USER_THUMBNAIL_KEY => $post_user_thumbnail
            );
        }
        
        $query->close();

        foreach ($posts as &$post) {
            $query_sql = "  SELECT 
                                count(comment_id)
                            FROM
                                comments
                            WHERE
                                post_id = ?";

            $query = $mysqli->prepare($query_sql);        
            $query->bind_param("s", $post[Posts::ID_KEY]);
            $query->bind_result($post_comments_count);

            $query->execute();
            
            while($query->fetch()) {
                $post[Posts::COMMENTS_COUNT_KEY] = $post_comments_count;
            }
            
            $query->close();

            $query_sql = "  SELECT 
                                count(user_id)
                            FROM
                                likes
                            WHERE
                                post_id = ?";

            $query = $mysqli->prepare($query_sql);        
            $query->bind_param("s", $post[Posts::ID_KEY]);
            $query->bind_result($post_likes_count);

            $query->execute();
            
            while($query->fetch()) {
                $post[Posts::LIKES_COUNT_KEY] = $post_likes_count;
            }
            
            $query->close();     

            $query_sql = "  SELECT 
                                count(user_id)
                            FROM
                                likes
                            WHERE
                                post_id = ?
                            AND
                                user_id = ?";

            $query = $mysqli->prepare($query_sql);        
            $query->bind_param("ss", 
                $post[Posts::ID_KEY],
                $user_id);

            $query->bind_result($post_liked);

            $query->execute();
            
            while($query->fetch()) {
                $post[Posts::LIKED_KEY] = $post_liked;
            }
            
            $query->close();                     
        }

        unset($post);

        $mysqli->close();

        return $posts;
    }

    static public function create($user_id, $post) 
    {
        if (!($mysqli = PostsDB::connect()))
            return false;                 

        $mysqli->autocommit(FALSE);        

        if (isset($post[Posts::PICTURE_KEY]['name'])) {                    

            $picture_path = sha1(
                $post[Posts::PICTURE_KEY]['name'].
                $post[Posts::PICTURE_KEY]['size']);

            $picture_data = $post[Posts::PICTURE_KEY]['data'];
            $picture_data =  preg_replace('#^data:image/[^;]+;base64,#', '', $picture_data);            
            $picture_data = str_replace(' ', '+', $picture_data);

            $picture_data = base64_decode($picture_data);

            $picture_full_path = '../../uploads/' . $picture_path;
            
            if (file_put_contents($picture_full_path, $picture_data)) {
                $query_sql = "  INSERT INTO 
                                    pictures
                                VALUES 
                                    (default,?)";

                $query = $mysqli->prepare($query_sql);
                $query->bind_param("s", $picture_path);   

                $query->execute();        
                $query->close();

                $picture_id = strval($mysqli->insert_id);

                $query_sql = "  INSERT INTO 
                                    posts
                                VALUES 
                                    (default,?,?,NOW(),?,?)";

                $query = $mysqli->prepare($query_sql);
                $query->bind_param("ssss", 
                    $user_id, 
                    $post[Posts::PRIVACY_KEY],            
                    $post[Posts::TEXT_KEY],
                    $picture_id);  

            } else {
                return false;
            }


        } else {

            $query_sql = "  INSERT INTO 
                                posts
                            VALUES 
                                (default,?,?,NOW(),?,default)";

            $query = $mysqli->prepare($query_sql);
            $query->bind_param("sss", 
                $user_id, 
                $post[Posts::PRIVACY_KEY],            
                $post[Posts::TEXT_KEY]);              
        }        

        $query->execute();        
        $query->close();

        $post[Posts::ID_KEY] = strval($mysqli->insert_id);

        $mysqli->commit();
        $mysqli->close(); 

        return $post;
    }  

    static public function delete($user_id, $post_id) 
    {
        if (!($mysqli = PostsDB::connect()))
            return false;                                 

        $query_sql = "  DELETE FROM 
                            posts
                        WHERE
                            post_id = ?
                        AND
                            user_id = ?";                             

        $query = $mysqli->prepare($query_sql);        
        $query->bind_param("ss", 
            $post_id,
            $user_id);                     

        $query->execute();        
        $query->close();

        $mysqli->close(); 

        return true;
    }      

    static public function likes($post_id)
    {
        if (!($mysqli = PostsDB::connect()))
            return false;

        $query_sql = "  SELECT
                            CONCAT(user_fname,' ',user_lname), 
                            users_info.user_id,
                            picture_path
                        FROM
                            likes
                        INNER JOIN
                            users_info
                        ON
                           likes.user_id = users_info.user_id
                        LEFT JOIN 
                            pictures
                        ON 
                            picture_id = users_info.user_thumbnail                            
                        WHERE
                            post_id=?";

        $query = $mysqli->prepare($query_sql);    
        $query->bind_param("s", $post_id);
        $query->bind_result(
            $user_name,
            $user_id,
            $user_thumbnail);        

        $query->execute();

        $likes = array();

        while($query->fetch()) {
            $likes[]  = array(
                Posts::USER_NAME_KEY => $user_name,
                Posts::USER_ID_KEY => $user_id,
                Posts::USER_THUMBNAIL_KEY => $user_thumbnail
            );            
        }

        $query->close();
        $mysqli->close();

        return $likes;                   
    }

    static public function like($user_id, $post_id)
    {
        if (!($mysqli = PostsDB::connect()))
            return false;
     
        $query_sql = "  INSERT INTO 
                            likes
                        VALUES 
                            (?,?,NOW())
                        ON 
                            DUPLICATE KEY 
                        UPDATE
                            likes.like_timestamp = NOW()";

        $query = $mysqli->prepare($query_sql);
        $query->bind_param("ss", 
            $post_id, 
            $user_id);

        $query->execute();
        $query->close();


        $query_sql = "  SELECT
                            user_id,
                            (
                                SELECT 
                                    concat(user_fname, ' ', user_lname)
                                FROM
                                    users_info
                                WHERE
                                    user_id = ?
                            )
                        FROM 
                            posts
                        WHERE
                            post_id = ?";

        $query = $mysqli->prepare($query_sql);
        $query->bind_param("ss", $user_id, $post_id);
        $query->bind_result($post_user_id, $like_user_name);

        $query->execute();
        while($query->fetch()) { }
        $query->close();

        if ($post_user_id != $user_id) {
            $query_sql = "  INSERT INTO 
                                notifications
                            VALUES 
                                (?,default,?,NOW(),'0')";

            $notification = json_encode(array(
                'msg' => $like_user_name.' liked your post.',
                'type' => 'like',
                'post_id' => $post_id
            )); 

            $query = $mysqli->prepare($query_sql);
            $query->bind_param("ss", 
                $post_user_id, 
                $notification);

            $query->execute();
            $query->close();        

            PostsDB::pusher()->trigger(
                $post_user_id, 
                'like', 
                $notification);
        }

        $mysqli->close(); 

        return true;
    }

    static public function dislike($user_id, $post_id) 
    {
        if (!($mysqli = PostsDB::connect()))
            return false;       

        $query_sql = "  DELETE FROM 
                            likes
                        WHERE
                            post_id = ?
                        AND
                            user_id = ?";    

        $query = $mysqli->prepare($query_sql);        
        $query->bind_param("ss", 
            $post_id,
            $user_id); 

        $query->execute();        

        $query->close();
        $mysqli->close(); 

        return true;
    }

}

?>