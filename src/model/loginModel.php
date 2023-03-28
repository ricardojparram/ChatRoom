<?php

	namespace model;
	use config\DBconnect;

	class loginModel extends DBconnect{

		private $user;
		private $password;

		public function __construct(){
			parent::__construct();
		}

		public function getLoginUser($user, $password){
			$this->user = $user;
			$this->password = $password;

			$this->loginUser();
		}

		private function loginUser(){

			try {
				
				$new = $this->con->prepare('SELECT username, password FROM user WHERE username = ?');
				$new->bindValue(1, $this->user);
				$new->execute();
				$data = $new->fetchAll(\PDO::FETCH_OBJ);
				
				if(!isset($data[0]->username)){
					die(json_encode(['error' => 'error', 'response' => 'Username doesnt exists']));
				}

				if(password_verify($this->password, $data[0]->password)){
					$response = ['success' => 'success', 'response' => 'User logged in correctly.'];
					$_SESSION['user'] = $this->user;
					die(json_encode($response));
				}else{
					$response = ['error' => 'error', 'response' => 'Incorrect password'];
					die(json_encode($response));
				}

			} catch (\PDOException $e) {
				die('Error: '.$e->getMessage().'</br>');
			}

		}

		public function getRegisUser($user, $password){
			$this->user = $user;
			$this->password = $password;

			$this->registerUser();
		}

		private function registerUser(){

			try {

				$new = $this->con->prepare('SELECT username FROM user WHERE username = ?');
				$new->bindValue(1, $this->user);
				$new->execute();
				$response = $new->fetchAll();

				if(isset($response[0]['username'])){
					die(json_encode(['error' => 'error','response' => 'User is already registered.']));
				}

				$this->password = password_hash($this->password, PASSWORD_BCRYPT);

				$new = $this->con->prepare('INSERT INTO user(username, password) VALUES(?,?)');
				$new->bindValue(1, $this->user);
				$new->bindValue(2, $this->password);
				$new->execute();
				$response = ['success' => 'success', 'response' => 'User has been registered.'];
				die(json_encode($response));

			} catch (\PDO $e) {
				die($e);
			}

		}

	}

?>