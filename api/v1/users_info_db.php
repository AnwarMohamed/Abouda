<?php

class UsersInfoDB extends Database
{
    static public function uploadPicture($user_id, $data) 
    {
        if (!($mysqli = UsersInfoDB::connect()))
            return false;     

        if (isset($data['name'])) 
        {                    
            $picture_path = sha1(
                $data['name'].
                $data['size']);

            $picture_data = $data['data'];
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

                $query_sql = "  UPDATE
                                    users_info
                                SET
                                    user_picture = ?,
                                    user_thumbnail = ?                                     
                                WHERE
                                    user_id = ?";                                    

                $query = $mysqli->prepare($query_sql);
                $query->bind_param("sss", 
                    $picture_id,
                    $picture_id,
                    $user_id);  

                $query->execute();        
                $query->close();

                $query_sql = "  INSERT INTO 
                                    posts
                                VALUES 
                                    (default,?,?,NOW(),?,?)";

                $post_privacy = 1;
                $post_text = "Changed my Profile Picture";

                $query = $mysqli->prepare($query_sql);
                $query->bind_param("ssss", 
                    $user_id, 
                    $post_privacy,
                    $post_text,
                    $picture_id);  


                $query->execute();        
                $query->close();

                $mysqli->close();
                return true;
            }        
        }

        return false;   
    }

    static public function get($user_id, $friend_id)
    {
        if (!($mysqli = UsersInfoDB::connect()))
            return false;        

        if (!$friend_id || $user_id == $friend_id) {
            $query_sql = "  SELECT
                                users.user_id,
                                user_fname,
                                user_lname,
                                user_mobile,
                                user_gender,
                                user_birthdate,
                                user_marital,
                                user_about,
                                'me',
                                picture_path,
                                user_email                               
                            FROM
                                users_info
                            LEFT JOIN
                                pictures
                            ON 
                                picture_id = user_picture
                            INNER JOIN
                                users
                            ON 
                                users_info.user_id = users.user_id
                            WHERE
                                users.user_id = ?";

            $query = $mysqli->prepare($query_sql);
            $query->bind_param("s", $user_id);  

        } else {
            $query_sql = "  SELECT
                                x.user_id,
                                x.user_fname,
                                x.user_lname,
                                x.user_mobile,
                                x.user_gender,
                                x.user_birthdate,
                                x.user_marital,
                                x.user_about,
                                (
                                    SELECT
                                        friendship_type
                                    FROM
                                        friendships
                                    WHERE
                                        user_id = ?
                                    AND
                                        friend_id = ?                                 
                                ),
                                picture_path,
                                user_email
                            FROM
                            (
                                SELECT
                                    user_id,
                                    user_fname,
                                    user_lname,
                                    user_mobile,
                                    user_gender,
                                    user_birthdate,
                                    user_marital,
                                    user_about,
                                    user_picture
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
                                        user_id = ?
                                    AND 
                                        friendship_type = 'accepted'
                                )
                                
                                UNION
                                
                                SELECT
                                    user_id,
                                    user_fname,
                                    user_lname,
                                    user_mobile,
                                    user_gender,
                                    NULL as user_birthdate,
                                    user_marital,
                                    NULL as user_about,
                                    user_picture
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
                                        user_id= ?
                                    AND 
                                        friendship_type = 'accepted'                                             
                                )
                            ) 
                            AS  
                                x
                            LEFT JOIN
                                pictures
                            ON 
                                picture_id = x.user_picture
                            INNER JOIN
                                users
                            ON 
                                x.user_id = users.user_id";

            $query = $mysqli->prepare($query_sql);                       
            $query->bind_param("ssssss",  
                $user_id,                 
                $friend_id,
                $friend_id, 
                $user_id,                 
                $friend_id,
                $user_id);                                                 
        }

        $query->bind_result(
            $user_fid,
            $user_fname,
            $user_lname,
            $user_mobile,
            $user_gender,
            $user_birthdate,
            $user_marital,
            $user_about,
            $user_friendship,
            $user_thumbnail,
            $user_email);

        $query->execute();

        $info  = array();

        while($query->fetch()) {            

            if (!$user_friendship) {
                $user_friendship = 'none';
            }

            $info  = array(
                Users::ID_KEY => $user_fid,
                Users::FNAME_KEY => $user_fname,
                Users::LNAME_KEY => $user_lname,
                Users::MOBILE_KEY => $user_mobile,                
                Users::GENDER_KEY => $user_gender,
                Users::BIRTHDATE_KEY => $user_birthdate,
                Users::MARITAL_KEY => $user_marital,
                Users::ABOUT_KEY => $user_about,
                Users::FRIENDSHIP_KEY => $user_friendship,
                Users::THUMBNAIL_KEY => $user_thumbnail,
                Users::EMAIL_KEY => $user_email
            );
        }

        $query->close();
        $mysqli->close();

        return $info;
    }

    static public function update($user_id, $info)
    {
        if (!($mysqli = UsersInfoDB::connect()))
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