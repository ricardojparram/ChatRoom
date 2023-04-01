<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Log In</title>
	<link rel="icon" href="assets/img/chaticon.png">
	<link rel="stylesheet" href="assets/css/normalize.css">
	<link rel="stylesheet" href="assets/css/style.css">
	<link rel="stylesheet" href="assets/css/login_style.css">
</head>
<body>
	
	<main style="display: none;">
		
		<div class="container">
			
			<div class="loginBox container-box">
				<div class="title">
					<h1>Chat room</h1>
					<label id="moduleTitle">Log In</label>
				</div>
				<div class="form-container">

					<form id="login-form">

						<label for="user">User</label>
						<input type="text" id="user">

						<label for="password">Password</label>
						<input type="password" id="password">

						<div id="button-container">
							<button class="button" id="login">Log In</button>
						</div>
						<div class="form-footer">
							<div class="footer-div">
								<hr>
								<p>or</p>
								<hr>
							</div>
							<p class="change">Create a new Account</p>
						</div>	
					</form>

				</div>
			</div>	

			<div class="registerBox container-box" style="display:none;">
				<div class="title">
					<h1>Chat room</h1>
					<label id="moduleTitle">Sign In</label>
				</div>
				<div class="form-container">

					<form id="register-form">

						<label for="regis-user">User</label>
						<input type="text" id="regis-user">

						<label for="regis-pass">Password</label>
						<input type="password" id="regis-pass">

						<label for="regis-repass">Repeat Password</label>
						<input type="password" id="regis-repass">

						<div id="button-container">
							<button class="button" id="signin">Sign In</button>
						</div>
						<div class="form-footer">
							<div class="footer-div">
								<hr>
								<p> or </p>
								<hr>
							</div>
							<p class="change">Log In</p>
						</div>	
					</form>

				</div>
			</div>
			
		</div>

	</main>


	<script src="assets/js/jquery-3.6.0.js"></script>
	<script src="assets/js/sweetalert2@11.js"></script>
	<script src="assets/js/validations.js"></script>
	<script src="assets/js/signin.js"></script>

</body>
</html>