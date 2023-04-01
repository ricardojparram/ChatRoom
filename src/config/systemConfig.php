<?php

	namespace config;

	define('DB_NAME', 'chat');
	define('DB_USER', 'root');
	define('DB_PASS', '');
	define('URL_', 'http://localhost/practicaWebSocket/');
	define('LOCAL_', 'localhost');
	define('SOCKET_IP', '10.42.0.1');
	define('SOCKET_PORT', '1616');
	define('SOCKET_FRONT', '10.42.0.1:1616');

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
		public function _SOCKET_IP(){
			return SOCKET_IP;
		}
		public function _SOCKET_PORT(){
			return SOCKET_PORT;
		}

	}

?>