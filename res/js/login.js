$(document).ready(function() {
	
	$('#DoLogin').click(function(){
		doLogin();
	});
	
	bindEnterKey('#User,#Password','#DoLogin');

});


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


