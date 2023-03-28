const regExp = {
	nombre: /^[a-zA-ZÀ-ÿ]{0,30}$/,
	correo: /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/,
	direccion: /^[a-zA-ZÀ-ÿ]+([a-zA-ZÀ-ÿ0-9\s#/,.-]){7,50}$/,
	fecha: /^([0-9]{4}\-[0-9]{2}\-[0-9]{2})$/,
	string: /^[a-zA-ZÀ-ÿ]+([a-zA-ZÀ-ÿ0-9\s,.-]){3,50}$/
}

function validUser(input){
	let val = input.val();

	if(val == null || val == ""){
		input.css({'border' : '2px solid #e7181b'});
		return false
	}else if(val.length < 4 || val.length > 25){
		input.css({'border' : '2px solid #e7181b'});
		return false
	}else{
		input.css({'border' : 'none'})
		return true		
	}
}

function validPass(input){
	let val = input.val();

	if(val == null || val == ""){
		input.css({'border' : '2px solid #e7181b'});
		return false
	}else if(val.length < 4 || val.length > 25){
		input.css({'border' : '2px solid #e7181b'});
		return false
	}else{
		input.css({'border' : 'none'})
		return true		
	}
}

function validRepass(input, input2){
	let pass = input.val();
	let repass = input2.val();

	if(repass == null || repass == ""){
		input2.css({'border' : '2px solid #e7181b'})
		return false
	}else if(pass != repass){
		input2.css({'border' : '2px solid #e7181b'})
		return false
	}else{
		input2.css({'border' : 'none'})
		return true
	}
}

const alert = Swal.mixin({
				toast: true,
				position: 'top-end',
				showConfirmButton: false,
				timer: 2000,
			})