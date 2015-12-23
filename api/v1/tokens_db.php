<?php

class TokensDB extends Database
{
    static public function create($mysqli, $user) {
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

    static public function check($token) {
        if (!$token)
            return false;

        if (!($mysqli = TokensDB::getConection()))
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
}

?>