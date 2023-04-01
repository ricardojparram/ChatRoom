$(document).ready(function(){
	$('main').animate({opacity: 'toggle'}, 2000);

	let user;
	$.post('', {username : ''}, function(response){
		user = JSON.parse(response);		
	})

	let conex = new WebSocket(`ws://${socket_front}`);

	conex.onopen = function(e) {
		console.log("Connection established!");
	};

	conex.onmessage = function(e) {
		let response = JSON.parse(e.data);
		let msg = `<div class="msg"><p class="username">${response.username}:</p><p>${response.message}</p></div>`;
		$('.chat-box').append(msg);
		let msgBox = $('.chat-box')[0];
		msgBox.scrollTop = msgBox.scrollHeight;
	};

	$('#msg').on('keydown', function(e){
		if(e.key === "Enter"){
			e.preventDefault();
			$('#sendMessage').click();
		}
	})

	$('#sendMessage').click(function(e){
		e.preventDefault();
		let textarea = $('#msg');
		let msg = textarea.val();
		if(msg == null || msg == ''){
			throw new Error('Empty message.');
		}
		let data = {'username' : user, message : msg};

		let mymsg = `<div class="msg me"><p>${msg}</p></div>`;

		conex.send(JSON.stringify(data));
		$('.chat-box').append(mymsg);
		textarea.val('');
		let msgBox = $('.chat-box')[0];
		msgBox.scrollTop = msgBox.scrollHeight;
	})

	


	$('#logout').click(()=>{ window.location = "?module=logout" });

})