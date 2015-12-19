<?php

class AboudaDB
{
	static public getConection() {
		$mysqli = new mysqli("localhost", "root", "", "Abouda");
		return $mysqli->connect_errno ? false: $mysqli;
	}

	static public checkEmail($email) {

		$query = $mysqli->prepare("SELECT COUNT(email) FROM users WHERE email = ?");
		$query->bind_param("s", $email);
		$query->execute();

		$result = $query->get_result();
		$query->close();
		$mysqli->close();
		return $result->fetch_row()[0] != 0;
	}

	static public newUser($app, $data) {

		if (AboudaDB::checkEmail($data[Users::EMAIL_KEY])) {
			return putError(
				'duplicate email parameter', 
				Users::ERROR_EMAIL_DUPLICATE, $app);
		}

		$query = $mysqli->prepare("INSERT INTO `users` (`user_id`,`user_email,`password`) VALUES (default,?,?)");
		$query->bind_param("ss", $data[Users::EMAIL_KEY], $data[Users::PASSWORD_KEY]);
		
		$query->execute();
		if ($conn->query($sql) === TRUE) {
			$last_id = $conn->insert_id;
		}
		if($data[Users::GENDER_KEY] == "male"){
			$data[Users::GENDER_KEY] = TRUE;
		}
		else{
			$data[Users::GENDER_KEY] = FALSE;
		}	

		$query = $mysqli->prepare("INSERT INTO `users_info` (`user_id`,`user_fname,`user_lname`,`user_gender`,`user_birthdate`) VALUES (?,?,?,?,?)");
		$query->bind_param("sssss", $last_id, $data[Users::FNAME_KEY], $data[Users::LNAME_KEY], $data[Users::GENDER_KEY], $data[Users::BIRTHDATE_KEY]);
		$query->execute();
		$query->close();

	}

	static public getPosts($user_id){
		$query = $mysqli->prepare("SELECT * FROM `posts` WHERE user_id = ?");
		$query->bind_param("s", $user_id);
		while($row = $query->fetch_row()) {
			$rows[]=$row;
		}
		$query->execute();
		$query->close();

	}

	static public getInfos($user_id){
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