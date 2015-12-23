<?php

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

class Database
{
	const ERROR_DATABASE_CONN = 1220;
	const ERROR_DATABASE_LOGIC = 1221;

	static public function getConection() 
	{
		$mysqli = new mysqli("localhost", "root", "root", "abouda");
		return $mysqli->connect_errno ? false: $mysqli;
	}

	static public function checkEmail($mysqli, $email) 
	{
		$query_sql = "SELECT COUNT(user_email) 
					  FROM users 
					  WHERE user_email = ?";

		$query = $mysqli->prepare($query_sql);			
		$query->bind_param('s', $email);		
		$query->execute();
		$query-> bind_result($count);

		while ($query-> fetch()) { break;}

		$query->close();		

		return $count != 0;
	}

	static public function newUser($response, $user) 
	{
		if (!($mysqli = Database::getConection())) {
			return putError(
				'database connection error', 
				DATABASE::ERROR_DATABASE_CONN, $response);			
		}		

		if (Database::checkEmail($mysqli , $user[Users::EMAIL_KEY])) {
			return putError(
				'duplicate email parameter', 
				Users::ERROR_EMAIL_DUPLICATE, $response);
		}

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

		$query_sql = "INSERT INTO users_info (user_id, user_fname, user_lname, user_gender, user_birthdate) 
					  VALUES (?,?,?,?,?)";

		$query = $mysqli->prepare($query_sql);				
		$query->bind_param("sssss", 
			$user[Users::ID_KEY],
			$user[Users::FNAME_KEY],
			$user[Users::LNAME_KEY],
			$user[Users::GENDER_KEY],
			$user[Users::BIRTHDATE_KEY]);

		$query->close();

		$user = Database::generateToken($mysqli, $user);

		$mysqli->commit();
		$mysqli->close();		

	    return putJsonBody(array(
	        'error' => false,
	        'error_code' => 0,
	        'msg'   => 'new user created',
	        'result' => array(
	        	'user_id' => $user[Users::ID_KEY],
	        	'token' => $user[Users::TOKEN_KEY],
	        )
	    ), 200, $response);  		
	}

	static public function generateToken($mysqli, $user) {
		$user[Users::TOKEN_KEY] = sha1(strval(time())
			.$user[Users::REMOTE_ADDR_KEY]
			.$user[Users::ID_KEY]);

		$query_sql = "INSERT INTO tokens
					  VALUES (?,?,NOW(),?)
					  ON DUPLICATE KEY
					  UPDATE user_token=?, token_timestamp=NOW(), token_address=?;";				  

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

	static public function authenticate($response, $user)
	{
		if (!($mysqli = Database::getConection())) {
			return putError(
				'database connection error', 
				DATABASE::ERROR_DATABASE_CONN, $response);			
		}

		$query_sql = "SELECT user_id from users 
					  WHERE user_email = ? AND user_password = SHA1(?)";

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
			$user = Database::generateToken($mysqli, $user);
			$mysqli->close();

		    return putJsonBody(array(
		        'error' => false,
		        'error_code' => 0,
		        'msg'   => 'user authenticated',
		        'result' => array(
		        	'user_id' => $user[Users::ID_KEY],
		        	'token' => $user[Users::TOKEN_KEY],
		        )
		    ), 200, $response); 			

		} else if ($query->num_rows == 0) {

			$query->free_result();
			$query->close();
			$mysqli->close();

			return putError(
				'user unauthenticated', 
				Users::ERROR_AUTH_INVALID, $response);	

		} else {

			$query->free_result();
			$query->close();
			$mysqli->close();

			return putError(
				'database logic error', 
				DATABASE::ERROR_DATABASE_LOGIC, $response);				
		}		
	}

	static public function checkToken($mysqli, $token) {
		if (!($mysqli = Database::getConection()))
			return false;		

		$query_sql = "SELECT user_id
					  FROM tokens
					  WHERE user_id=? AND user_token=? AND token_address=?";

		$query = $mysqli->prepare($query_sql);
		$query->bind_param("sss", 
			$token[Users::ID_KEY],
			$token[Users::TOKEN_KEY],
			$token[Users::REMOTE_ADDR_KEY]);

		$query->store_result();

		$row_count = $query->num_rows;

		$query->free_result();
		$query->close();
		$mysqli->close();

		return $row_count == 1;
	}

	static public function getPosts($mysqli, $user_id){
		$query_sql = "SELECT post_id, post_privacy, post_timestamp, post_text, picture_path
					  FROM posts 
					  INNER JOIN pictures ON pictures.picture_id = post_picture 
					  WHERE user_id = ?";

		$query = $mysqli->prepare($query_sql);
		$query->bind_param("s", $user_id);
		$query->bind_result($post_id, $post_privacy, $post_timestamp, $post_text, $post_picture);
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
}

?>