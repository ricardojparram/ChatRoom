<?php
	if(isset($_SESSION['user'])){
		session_destroy();
		die('<script> window.location = "?url=login" </script>');
	}else{
		die('<script> window.location = "?url=login" </script>');
	}
