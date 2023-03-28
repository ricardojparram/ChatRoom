<?php
	
	use model\loginModel;

	$model = new loginModel();

	if(isset($_POST['login'], $_POST['password'], $_POST['user'])){
		$model->getLoginUser($_POST['user'], $_POST['password']);
	}
	if(isset($_POST['register'], $_POST['user'], $_POST['password'])){
		$model->getRegisUser($_POST['user'], $_POST['password']);
	}

	if(file_exists('view/loginView.php')){
		require_once('view/loginView.php');		
	}else{
		die("Login's view doesn't exists.");
	}

?>