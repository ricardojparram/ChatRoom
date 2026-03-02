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

	<!-- Dark mode toggle -->
	<button id="darkmode-btn" title="Toggle dark mode">◐</button>

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
						<div class="pass-wrap">
							<input type="password" id="password">
							<button type="button" class="pass-toggle" data-target="password"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="M2.062 12.348a1 1 0 0 1 0-.696a10.75 10.75 0 0 1 19.876 0a1 1 0 0 1 0 .696a10.75 10.75 0 0 1-19.876 0"/><circle cx="12" cy="12" r="3"/></g></svg></button>
						</div>

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
						<div class="pass-wrap">
							<input type="password" id="regis-pass">
							<button type="button" class="pass-toggle" data-target="regis-pass"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="M2.062 12.348a1 1 0 0 1 0-.696a10.75 10.75 0 0 1 19.876 0a1 1 0 0 1 0 .696a10.75 10.75 0 0 1-19.876 0"/><circle cx="12" cy="12" r="3"/></g></svg></button>
						</div>

						<label for="regis-repass">Repeat Password</label>
						<div class="pass-wrap">
							<input type="password" id="regis-repass">
							<button type="button" class="pass-toggle" data-target="regis-repass"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="M2.062 12.348a1 1 0 0 1 0-.696a10.75 10.75 0 0 1 19.876 0a1 1 0 0 1 0 .696a10.75 10.75 0 0 1-19.876 0"/><circle cx="12" cy="12" r="3"/></g></svg></button>
						</div>

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
	<script>
		// Dark mode toggle
		const dmBtn = document.getElementById('darkmode-btn');
		if (localStorage.getItem('darkmode') === '1') document.body.classList.add('dark');
		dmBtn.addEventListener('click', () => {
			document.body.classList.toggle('dark');
			localStorage.setItem('darkmode', document.body.classList.contains('dark') ? '1' : '0');
		});

		// Password toggle
		const svgEye     = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="M2.062 12.348a1 1 0 0 1 0-.696a10.75 10.75 0 0 1 19.876 0a1 1 0 0 1 0 .696a10.75 10.75 0 0 1-19.876 0"/><circle cx="12" cy="12" r="3"/></g></svg>';
		const svgEyeOff  = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="M10.733 5.076a10.744 10.744 0 0 1 11.205 6.575a1 1 0 0 1 0 .696a10.8 10.8 0 0 1-1.444 2.49m-6.41-.679a3 3 0 0 1-4.242-4.242"/><path d="M17.479 17.499a10.75 10.75 0 0 1-15.417-5.151a1 1 0 0 1 0-.696a10.75 10.75 0 0 1 4.446-5.143M2 2l20 20"/></g></svg>';
		document.querySelectorAll('.pass-toggle').forEach(btn => {
			btn.addEventListener('click', () => {
				const input = document.getElementById(btn.dataset.target);
				const show  = input.type === 'password';
				input.type    = show ? 'text' : 'password';
				btn.innerHTML = show ? svgEyeOff : svgEye;
			});
		});
	</script>

</body>

</html>