<?php
class SearchDB extends Database 
{
    static public function get($user_id, $string, $flag)
    {
        if (!($mysqli = SearchDB::connect()))
            return false;        

        if($flag == "mobile") {

            $string = $string.'%';
            $query_sql="SELECT
                            concat(user_fname, ' ', user_lname), 
                            users_info.user_id,
                            picture_path,
                            user_mobile
                        FROM
                            users_info
                        INNER JOIN
                            friendships
                        ON
                            friendships.user_id = ?  
                        LEFT JOIN 
                            pictures
                        ON  
                            pictures.picture_id = user_picture                      
                        WHERE   
                            friendships.friendship_type = 'accepted'
                        AND
                            user_mobile
                        LIKE     
                            ?";

            $query = $mysqli->prepare($query_sql);             
            $query->bind_param("ss", 
                $string,
                $user_id);   

            $query->bind_result(
                $user_name,
                $user_id,
                $user_thumbnail,
                $user_mobile);

            $query->execute();
            $search = array();

            while($query->fetch()) {
                $search[]  = array(
                    'type' => $flag,
                    'mobile' =>$mobile,
                    Posts::USER_NAME_KEY => $user_name,
                    Posts::USER_ID_KEY => $user_id,
                    Posts::USER_THUMBNAIL_KEY => $user_thumbnail);
            }

            $query->close();
            $mysqli->close();

            return $search;
        }

        else if($flag == "email") {

            $string = $string.'%';
            $query_sql="    SELECT
                                concat(user_fname, ' ', user_lname), 
                                users.user_id,
                                picture_path,
                                user_email
                            FROM
                                users_info
                            INNER JOIN  
                                users
                            ON
                                users_info.user_id = users.user_id
                            LEFT JOIN 
                                pictures
                            ON  
                                pictures.picture_id = user_picture                                    
                            WHERE   
                                user_email
                            LIKE     
                                ?";

            $query = $mysqli->prepare($query_sql); 

            $query->bind_param("s", $string);            
            $query->bind_result(
                $user_name,
                $user_id,
                $user_thumbnail,
                $user_email);                

            $query->execute();
            $search = array();

            while($query->fetch()) {
                $search[]  = array(
                    'type' => $flag,
                    'email' => $user_email,
                    Posts::USER_NAME_KEY => $user_name,
                    Posts::USER_ID_KEY => $user_id,
                    Posts::USER_THUMBNAIL_KEY => $user_thumbnail);
            }

            $query->close();
            $mysqli->close();

            return $search;
        }

        else if ($flag == "fullname") {            

            $string = '%'.$string.'%';
            $query_sql = "  SELECT
                                concat(user_fname, ' ', user_lname), 
                                user_id,
                                picture_path
                            FROM
                                users_info
                            LEFT JOIN 
                                pictures
                            ON  
                                pictures.picture_id = user_picture                                  
                            WHERE   
                                concat(user_fname, ' ', user_lname)
                            LIKE     
                                ?";

            $query = $mysqli->prepare($query_sql); 

            $query->bind_param("s", $string);       
            $query->bind_result(
                $user_name,
                $user_id,
                $user_thumbnail);

            $query->execute();
            $search = array();

            while($query->fetch()) {
                $search[]  = array(
                    'type' => $flag,
                    Posts::USER_NAME_KEY => $user_name,
                    Posts::USER_ID_KEY => $user_id,
                    Posts::USER_THUMBNAIL_KEY => $user_thumbnail);
            }

            $query->close();
            $mysqli->close();

            return $search;
        }

        else if ($flag == "hometown") {

            $string = $string.'%';
            $query_sql="    SELECT
                                concat(user_fname, ' ', user_lname), 
                                user_id
                            FROM
                                users_info
                            WHERE   
                                user_hometown
                            LIKE     
                                ?";

            $query = $mysqli->prepare($query_sql); 
            $query->bind_param("s", $string);       
            $query->bind_result(
                $user_name,
                $user_id);

            $query->execute();
            $search = array();

            while($query->fetch()) {
                $search[]  = array(
                    'type' => $flag,
                    Posts::USER_NAME_KEY => $user_name,
                    Posts::USER_ID_KEY => $user_id,
                    Posts::USER_THUMBNAIL_KEY => $user_thumbnail);
            }

            $query->close();
            $mysqli->close();

            return $search;
        }

        else if ($flag == "post"){

            $string = '%'.$string.'%';
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
                            AND 
                                post_text 
                            LIKE
                                ?
                            ORDER BY
                                post_timestamp DESC";

            $query = $mysqli->prepare($query_sql);        
            $query->bind_param("sss", 
                $user_id, 
                $user_id,
                $string);


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
                $search = array();

            while($query->fetch()) {
                $search[]  = array(
                    'type' => $flag,                    
                    Posts::USER_NAME_KEY => $post_user_name,
                    Posts::USER_ID_KEY => $post_user_id,
                    Posts::TEXT_KEY => $post_text,
                    Posts::ID_KEY => $post_id,
                    Posts::USER_THUMBNAIL_KEY => $post_user_thumbnail
                );
            }

            $query->close();
            $mysqli->close();

            return $search;
        }
        
        return false;        
    } 
}
