<?php

	if(!file_exists('vendor/autoload.php')){
		die("Autoload doesn't exists");
	}else{
		require 'vendor/autoload.php';
	}

	session_start();

	use config\systemConfig;

	$systemConfig = new systemConfig();
	$systemConfig->validFront();
	
	use controller\frontController;	

	new frontController($_REQUEST);

?>