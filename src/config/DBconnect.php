<?php

	namespace config;
	use config\systemConfig;
	use \PDO;

	class DBconnect extends systemConfig{

		private $dbname;
		private $user;
		private $pass;
		private $local;
		protected $con;

		public function __construct(){
			$this->dbname = parent::_BD_();
			$this->user = parent::_USER_();
			$this->pass = parent::_PASS_();
			$this->local = parent::_LOCAL_();

			$this->connectDB();
		}

		protected function connectDB(){

			try {
				$this->con = new \PDO("mysql:host={$this->local};dbname={$this->dbname}", $this->user, $this->pass);

			}catch(\PDOException $error){
				die('Error: '.$error->getMessage().'</br>');
			}

		}
	}

?>