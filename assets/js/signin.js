$(document).ready(function(){
	$('main').animate({opacity: 'toggle'}, 2000);

	$('#login-form .change').click(function(){

		$('.loginBox').animate({
			opacity : 'toggle',
		}, 500, function(){
			$('.registerBox').animate({
				opacity: 'toggle'
			}, 500);
			$('head title').html('Sign In')
		})

	})

	$('#register-form .change').click(function(){

		$('.registerBox').animate({
			opacity : 'toggle',
		}, 500, function(){
			$('.loginBox').animate({
				opacity: 'toggle'
			}, 500)
			$('head title').html('Log In')
		})

	})

	$('#login').click(function(e){
		e.preventDefault();	

		let user = $('#user').val();
		let password = $('#password').val();

		let vuser = validUser($('#user'));
		let vpassword = validPass($('#password'));

		if(vuser && vpassword){
			$.ajax({
				url: '',
				type: 'post',
				data: {user, password, login:'login'},
				dataType: 'json',
				success(response){
					if(response.error){
						alert.fire({
							icon: 'warning',
							title: 'Username or password incorrect.'
						})
					}
					if(response.success){
						$('main').animate({opacity: 'toggle'}, 2000);
						alert.fire({
							icon: 'success',
							title: 'Logged In correctly.',
						}).then(()=>{
							window.location = "?module=chat";
						})
					}
				},
				error(response){
					alert.fire({
						icon: 'error',
						title: 'An error has ocurred.'
					})
					console.error(response);
				}
			})
		}

	})

	$('#signin').click(function(e){
		e.preventDefault();

		let user = $('#regis-user').val();
		let password = $('#regis-pass').val();
		let repass = $('#regis-repass').val();

		let vuser = validUser($('#regis-user'));
		let vpass = validPass($('#regis-pass'));
		let vrepass = validRepass($('#regis-pass'), $('#regis-repass')); 

		if(vuser && vpass && vrepass){
			$.ajax({
				url: '',
				type: 'post',
				dataType: 'json',
				data: {user, password, register:'asd'},
				success(response){
					if(response.error){
						alert.fire({
							icon: 'warning',
							title: 'The username has been used alredy.',
						})
					}
					if(response.success){
						alert.fire({
							icon: 'success',
							title: 'Your account has been created correctly.',
						}).then(()=>{
							$('#register-form .change').click();
							$('#register-form input').val('');
						})
					}
				},
				error(err){
					alert.fire({
						icon: 'error',
						title: 'An error has ocurred.'
					})
					console.error(err)
				}
			})
		}
	})

})