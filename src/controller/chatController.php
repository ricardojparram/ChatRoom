<?php 
	
	if(!isset($_SESSION['user'])){
		die('<script> window.location = "?url=login" </script>');
	}
	
	if(isset($_POST['username'])){
		die(json_encode($_SESSION['user']));
	}	

	require_once 'view/chatView.php';

?>