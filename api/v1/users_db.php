<?php

class UsersDB extends Database
{
    static public function create($user) 
    {
        if (!($mysqli = UsersDB::connect()))
            return false;

        $mysqli->autocommit(FALSE);

        $query_sql = "  INSERT INTO 
                            users 
                        VALUES 
                            (default,?,SHA1(?))";

        $query = $mysqli->prepare($query_sql);              
        $query->bind_param("ss", 
            $user[Users::EMAIL_KEY], 
            $user[Users::PASSWORD_KEY]);
        
        $query->execute();
        $query->close();

        //var_dump($user);

        $user[Users::ID_KEY] = strval($mysqli->insert_id);

        $query_sql = "  INSERT INTO 
                            users_info 
                            (
                                user_id, 
                                user_fname, 
                                user_lname, 
                                user_gender, 
                                user_birthdate
                            ) 
                        VALUES 
                            (?,?,?,?,?)";

        $query = $mysqli->prepare($query_sql);              
        $query->bind_param("sssss", 
            $user[Users::ID_KEY],
            $user[Users::FNAME_KEY],
            $user[Users::LNAME_KEY],
            $user[Users::GENDER_KEY],
            $user[Users::BIRTHDATE_KEY]);

        $query->execute();
        $query->close();

        //var_dump($user);

        $user = TokensDB::create($mysqli, $user);

        $mysqli->commit();
        $mysqli->close();       

        

        return $user;        
    }   

    static public function auth($user)
    {
        if (!($mysqli = UsersDB::connect()))
            return false;                  

        $query_sql = "  SELECT 
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
            $user = TokensDB::create($mysqli, $user);
            $mysqli->close();

            return $user;           
        } 
            
        $query->free_result();
        $query->close();
        $mysqli->close();           
        
        return false;     
    }

    static public function delete($user_id)
    {
        if (!($mysqli = UsersDB::connect()))
            return false;                  

        $query_sql = "  DELETE 
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

    static public function duplicate($email) 
    {
        if (!($mysqli = Database::connect()))
            return false;

        $query_sql = "  SELECT 
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
}

?>