<?php

	namespace config;

	define('DB_NAME', 'chat');
	define('DB_USER', 'root');
	define('DB_PASS', '');
	define('URL_', 'http://localhost/practicaWebSocket/');
	define('LOCAL_', 'localhost');

	class systemConfig{

		public function validFront(){
			if(!file_exists('src/controller/frontController.php')){
				die('frontController doesnt exists');
			}
		}

		public function _URL_(){
			return URL_;
		}
		public function _BD_(){
			return DB_NAME;
		}
		public function _PASS_(){
			return DB_PASS;
		}
		public function _USER_(){
			return DB_USER;
		}
		public function _LOCAL_(){
			return LOCAL_;
		}

	}

?>