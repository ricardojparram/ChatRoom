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
	<link href="https://fonts.googleapis.com/css2?family=Material+Icons"
	rel="stylesheet">
</head>
<body>
	
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
					<textarea id="msg" placeholder="Write a message..."></textarea>
					<button id="sendMessage"><span class="material-icons">send</span></button>
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
						<img id="profileImage" src="assets/img/profile_photo.jpg" width="100px" height="100px">
						<h3><?= $_SESSION['user']; ?></h3>
					</div>
					<button id="logout">Log out</button>
				</div>
			</div>
		</div>

	</main>


	<script src="assets/js/jquery-3.6.0.js"></script>
	<script src="assets/js/sweetalert2@11.js"></script>
	<script src="assets/js/validations.js"></script>
	<script src="assets/js/chat.js"></script>

</body>
</html>