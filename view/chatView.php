<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Chat Room</title>
	<link rel="icon" href="assets/img/chaticon.png">
	<link rel="stylesheet" href="assets/css/normalize.css">
	<link rel="stylesheet" href="assets/css/style.css">
	<link rel="stylesheet" href="assets/css/chat_style.css">
	<link href="https://fonts.googleapis.com/css2?family=Material+Icons" rel="stylesheet">
</head>

<body>

	<!-- Dark mode toggle -->
	<button id="darkmode-btn" title="Toggle dark mode">◐</button>

	<main style="display: none">

		<div class="chat-container">

			<div class="title">
				<h1>Chat room</h1>
				<label id="moduleTitle">General room</label>
			</div>

			<div class="chat">
				<div class="chat-box">

				</div>
				<div class="chat-message">
					<div class="chat-message-row">
						<textarea id="msg" placeholder="Write a message..."></textarea>
						<button id="sendMessage"><span class="material-icons">send</span></button>
					</div>
				</div>
			</div>

		</div>


		<div style="" class="right-boxes">
			<div class="container-side">
				<div class="title">
					<h1>Profile</h1>
					<label id="moduleTitle">User data</label>
				</div>

				<div class="data-container">
					<div class="user-data">
						<img id="profileImage" src="assets/img/profile_photo.jpg" width="80px" height="80px">
						<h3><?= $_SESSION['user']; ?></h3>
					</div>
					<hr>
					<button id="logout">Log out</button>
				</div>
			</div>
		</div>

	</main>

	<script>const socket_front = "<?= SOCKET_FRONT ?>"</script>
	<script src="assets/js/jquery-3.6.0.js"></script>
	<script src="assets/js/sweetalert2@11.js"></script>
	<script src="assets/js/validations.js"></script>
	<script src="assets/js/chat.js"></script>
	<script>
		// Dark mode toggle
		const dmBtn = document.getElementById('darkmode-btn');
		if (localStorage.getItem('darkmode') === '1') document.body.classList.add('dark');
		dmBtn.addEventListener('click', () => {
			document.body.classList.toggle('dark');
			localStorage.setItem('darkmode', document.body.classList.contains('dark') ? '1' : '0');
		});
	</script>

</body>

</html>