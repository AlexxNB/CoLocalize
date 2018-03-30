$(document).ready(function() {
	$('#doCreateProject').click(function(){
		doCreateProject();
	});

	$('#doCopyLink').click(function(){
		doCopyLink();
	});
	
	$("#public").change(function() {
		togglePubLink();
	});
});


function togglePubLink(){
	var isPublic = ($('#public').prop("checked")) ? true : false;
	var pid = $('#public').data('pid');
	if(isPublic){
		getJSON('projects','getPublicLink',{pid:pid},function(resp) {
			var url = '';
			if(resp.status == 200){
				url = resp.data;
			}
			$('#public_link').val(url);
			$('#pl_container').slideDown(200);
			$('#public_descr').fadeIn(200);
		});
	}else{
		$('#pl_container').slideUp(200,function(){
			$('#public_link').val('');
		});
		$('#public_descr').fadeOut(200);
	}
}

function doCreateProject(){
	var title = $('#title').val();
	var descr = $('#descr').val();
	var public = ($('#public').prop("checked")) ? '1' : '0';

	startLoading('#doCreateProject');
	clearInpError();

	getJSON('projects','add',{name:name, email:email, password:password, password2:password2},function(resp) {
		if(resp.status != 200){
			if(resp.data == 'name') setInpError('#rName',data.error);
			if(resp.data == 'email') setInpError('#rEmail',data.error);
			if(resp.data == 'password') setInpError('#rPassword',data.error);
			if(resp.data == 'password2') setInpError('#rPassword2',data.error);
			stopLoading('#doSignUp');
			enable('#doSignIn');
			return false;
		}
		locate('/projects/');		
	});
}
