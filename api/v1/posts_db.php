<?php

class PostsDB extends Database
{
	const ID_KEY = 'id';
	const USER_ID_KEY = 'user_id';
	const PRIVACY_PUBLIC_KEY = 'public';
	const TIMESTAMP_KEY = 'timestamp';
	const TEXT_KEY = 'text';
	const PICTURE_KEY = 'picture';

    static public function get($user_id, $post_id) 
    {
        if (!($mysqli = Database::getConection()))
            return false;                 

        $query_sql = "	SELECT 
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
                            post_id = ? AND
                            (user_id = ? OR 
                                post_privacy = true OR 
                                post_id IN (
                                    SELECT 
                                        post_id
                                    FROM 
                                        posts
                                    INNER JOIN 
                                        friendships
                                    ON 
                                        posts.user_id = friendships.friend_id AND
                                        friendships.user_id = ?
                                )
                            )";

        $query = $mysqli->prepare($query_sql);
        $query->bind_param("sss", 
            $post_id, 
            $user_id, 
            $user_id);   

        $query->bind_result(
            $post_id,
            $post_user_id, 
            $post_privacy, 
            $post_timestamp, 
            $post_text, 
            $post_picture);

        $query->execute();

        $post = array();

        while($query->fetch()) {
            $post  = array(
                PostsDB::ID_KEY => $post_id,
                PostsDB::USER_ID_KEY => $post_user_id,
                PostsDB::PRIVACY_PUBLIC_KEY => $post_privacy,
                PostsDB::TIMESTAMP_KEY => $post_timestamp,
                PostsDB::TEXT_KEY => $post_text,
                PostsDB::PICTURE_KEY => $post_picture
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

        $query_sql = "	SELECT 
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
                            user_id = ? OR 
                            post_privacy = true OR 
                            post_id IN (
                                SELECT 
                                    post_id
                                FROM 
                                    posts
                                INNER JOIN 
                                    friendships
                                ON 
                                    posts.user_id = friendships.friend_id AND
                                    friendships.user_id = ?)";

        $query = $mysqli->prepare($query_sql);
        $query->bind_param("ss", $user_id, $user_id);
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
                PostsDB::ID_KEY => $post_id,
                PostsDB::PRIVACY_PUBLIC_KEY => $post_privacy,
                PostsDB::TIMESTAMP_KEY => $post_timestamp,
                PostsDB::TEXT_KEY => $post_text,
                PostsDB::PICTURE_KEY => $post_picture
            );
        }
        
        $query->close();
        $mysqli->close();

        return $posts;
    }    
}

?>