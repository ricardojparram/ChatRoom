<?php

namespace config;

define('DB_NAME', getenv('DB_NAME') ?: 'chat');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('URL_', getenv('APP_URL') ?: 'http://localhost/practicaWebSocket/');
define('LOCAL_', getenv('DB_HOST') ?: 'localhost');
define('SOCKET_IP', getenv('SOCKET_IP') ?: '0.0.0.0');
define('SOCKET_PORT', getenv('SOCKET_PORT') ?: '1616');
define('SOCKET_FRONT', getenv('SOCKET_FRONT') ?: 'localhost:1616');

class systemConfig
{

	public function validFront()
	{
		if (!file_exists('src/controller/frontController.php')) {
			die('frontController doesnt exists');
		}
	}

	public function _URL_()
	{
		return URL_;
	}
	public function _BD_()
	{
		return DB_NAME;
	}
	public function _PASS_()
	{
		return DB_PASS;
	}
	public function _USER_()
	{
		return DB_USER;
	}
	public function _LOCAL_()
	{
		return LOCAL_;
	}
	public function _SOCKET_IP()
	{
		return SOCKET_IP;
	}
	public function _SOCKET_PORT()
	{
		return SOCKET_PORT;
	}

}

?>