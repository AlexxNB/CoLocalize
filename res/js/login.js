$(document).ready(function() {
	$('#doSignIn').click(function(){
		doSignIn();
	});

	$('#doSignUp').click(function(){
		doSignUp();
	});
	
	bindEnterKey('#email,#password','#doSignIn');
	bindEnterKey('#rName,#rEmail,#rPassword,#rPassword2','#doSignUp');
});

function doSignUp(){
	var name = $('#rName').val();
	var email = $('#rEmail').val();
	var password = $('#rPassword').val();
	var password2 = $('#rPassword').val();

	startLoading('#doSignUp');
	disable('#doSignIn');
	clearInpError();

	getJSON('login','signup',{name:name, email:email, password:password, password2:password2},function(data) {
		if(data.status != 200){
			if(data.data == 'name') setInpError('#rName',data.error);
			if(data.data == 'email') setInpError('#rEmail',data.error);
			if(data.data == 'password') setInpError('#rPassword',data.error);
			if(data.data == 'password2') setInpError('#rPassword2',data.error);
			stopLoading('#doSignUp');
			enable('#doSignIn');
			return false;
		}
		document.location.href = '/';		
	});
}

function doLogin(){
	var user = $('#User').val();
	var password = $('#Password').val();
	var remember = ($('#Remember').prop("checked")) ? '1' : '0';
	
	startLoading('#DoLogin');
	clearInpError();
	
	if(!user.trim()) 
	{
		setInpError('#User','Введите имя пользователя');
		stopLoading('#DoLogin');
		return false;
	}
	
	if(!password.trim()) 
	{
		setInpError('#Password','Введите пароль');
		stopLoading('#DoLogin');
		return false;
	}
	
	getJSON('login','login',{user:user,password:password,remember:remember},function(data) {
		if(data.status != 200){
			if(data.data == 'user') setInpError('#User',data.error);
			if(data.data == 'password') setInpError('#Password',data.error);
			stopLoading('#DoLogin');
			return false;
		}
		document.location.href = '/';		
	});
}


