<?php

class Database
{
    const ERROR_DATABASE_CONN = 1220;    

    static public function getConection() 
    {
        $mysqli = new mysqli("localhost", "root", "root", "abouda");
        return $mysqli->connect_errno ? false: $mysqli;
    }

    static public function checkEmail($email) 
    {
        if (!($mysqli = Database::getConection()))
            return false;

        $query_sql = "SELECT 
                            COUNT(user_email) 
                      FROM 
                            users 
                      WHERE 
                            user_email = ?";

        $query = $mysqli->prepare($query_sql);          
        $query->bind_param('s', $email);        
        $query->execute();
        $query-> bind_result($count);

        while ($query-> fetch()) { break;}

        $query->close(); 
        $mysqli->close();       

        return $count != 0;
    }

    static public function newUser($user) 
    {
        if (!($mysqli = Database::getConection()))
            return false;

        $mysqli->autocommit(FALSE);

        $query_sql = "INSERT INTO users 
                      VALUES (default,?,SHA1(?))";

        $query = $mysqli->prepare($query_sql);              
        $query->bind_param("ss", 
            $user[Users::EMAIL_KEY], 
            $user[Users::PASSWORD_KEY]);
        
        $query->execute();
        $query->close();

        $user[Users::ID_KEY] = strval($mysqli->insert_id);

        $query_sql = "INSERT INTO 
                            users_info 
                            (user_id, 
                                user_fname, 
                                user_lname, 
                                user_gender, 
                                user_birthdate) 
                      VALUES 
                            (?,?,?,?,?)";

        $query = $mysqli->prepare($query_sql);              
        $query->bind_param("sssss", 
            $user[Users::ID_KEY],
            $user[Users::FNAME_KEY],
            $user[Users::LNAME_KEY],
            $user[Users::GENDER_KEY],
            $user[Users::BIRTHDATE_KEY]);

        $query->close();

        $user = Database::newToken($mysqli, $user);

        $mysqli->commit();
        $mysqli->close();       

        return $user;        
    }

    static public function newToken($mysqli, $user) {
        $user[Users::TOKEN_KEY] = sha1(strval(time())
            .$user[Users::REMOTE_ADDR_KEY]
            .$user[Users::ID_KEY]);

        $query_sql = "INSERT INTO 
                            tokens
                      VALUES 
                            (?,?,NOW(),?)
                      ON 
                            DUPLICATE KEY
                      UPDATE 
                            user_token=?, 
                            token_timestamp=NOW(), 
                            token_address=?;";                

        $query = $mysqli->prepare($query_sql);      
        $query->bind_param("sssss", 
            $user[Users::ID_KEY],
            $user[Users::TOKEN_KEY],
            $user[Users::REMOTE_ADDR_KEY],
            $user[Users::TOKEN_KEY],
            $user[Users::REMOTE_ADDR_KEY]);

        $query->execute();
        $query->close();    

        return $user;   
    }

    static public function authUser($user)
    {
        if (!($mysqli = Database::getConection()))
            return false;                  

        $query_sql = "SELECT 
                            user_id 
                      FROM 
                            users 
                      WHERE 
                            user_email = ? AND 
                            user_password = SHA1(?)";

        $query = $mysqli->prepare($query_sql);              
        $query->bind_param("ss", 
            $user[Users::EMAIL_KEY], 
            $user[Users::PASSWORD_KEY]);
        
        $query->execute();
        $query->store_result();

        if ($query->num_rows == 1) {

            $query->bind_result($user_id);          
            $query->fetch();
            $query->close();            

            $user[Users::ID_KEY] = $user_id;
            $user = Database::newToken($mysqli, $user);
            $mysqli->close();

            return $user;           
        } 
            
        $query->free_result();
        $query->close();
        $mysqli->close();           
        
        return false;     
    }

    static public function checkToken($token) {
        if (!$token)
            return false;

        if (!($mysqli = Database::getConection()))
            return false;       

        $query_sql = "SELECT 
                            user_id
                      FROM 
                            tokens
                      WHERE 
                            user_id=? AND 
                            user_token=? AND 
                            token_address=?";

        $query = $mysqli->prepare($query_sql);
        $query->bind_param("sss", 
            $token[Users::ID_KEY],
            $token[Users::TOKEN_KEY],
            $token[Users::REMOTE_ADDR_KEY]);

        $query->execute();      
        $query->store_result();

        $row_count = $query->num_rows;      

        $query->free_result();
        $query->close();
        $mysqli->close();

        return $row_count == 1;
    }

    static public function deleteUser($user_id)
    {
        if (!($mysqli = Database::getConection()))
            return false;                  

        $query_sql = "DELETE 
                      FROM 
                            users 
                      WHERE 
                            user_id = ?";

        $query = $mysqli->prepare($query_sql);              
        $query->bind_param("s", $user_id);

        $query->execute();
        $query->close();
        $mysqli->close();

        return true;    
    }

    static public function getPost($user_id, $post_id) 
    {
        if (!($mysqli = Database::getConection()))
            return false;                 

        $query_sql = "SELECT 
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
                'id' => $post_id,
                'user_id' => $post_user_id,
                'public' => $post_privacy,
                'timestamp' => $post_timestamp,
                'text' => $post_text,
                'picture' => $post_picture
            );
        }
        
        $query->close();
        return $post;
    }

    static public function getPosts($mysqli, $user_id)
    {
        $query_sql = "SELECT 
                            post_id, 
                            post_privacy, 
                            post_timestamp, 
                            post_text, 
                            picture_path
                      FROM 
                            posts 
                      INNER JOIN 
                            pictures 
                      ON 
                            pictures.picture_id = post_picture 
                      WHERE 
                            user_id = ?";

        $query = $mysqli->prepare($query_sql);
        $query->bind_param("s", $user_id);
        $query->bind_result(
            $post_id, 
            $post_privacy, 
            $post_timestamp, 
            $post_text, 
            $post_picture);

        $query->execute();

        $posts = array();

        while($query->fetch()) {
            $posts[]  = array(
                'id' => $post_id,
                'public' => $post_privacy,
                'timestamp' => $post_timestamp,
                'text' => $post_text,
                'picture' => $post_picture
            );
        }
        
        $query->close();
        return $posts;
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