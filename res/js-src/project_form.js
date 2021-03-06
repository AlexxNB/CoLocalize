$(document).ready(function() {
	$('#doCreateProject').click(function(){
		doProject('add');
	});

	$('#doSaveProject').click(function(){
		doProject('save');
	});

	$('#doCopyLink').click(function(){
		doCopyLink();
	});
	
	$("#public").change(function() {
		togglePubLink();
	});

	togglePubLink();
});


function togglePubLink(){
	var isPublic = ($('#public').prop("checked")) ? true : false;
	var pid = $('#public').data('pid');
	if(isPublic){
		getJSON('projects','getPublicLink',{pid:pid},function(resp) {
			var url = '';
			var code = '';
			if(resp.status == 200){
				url = resp.data.url;
				code = resp.data.code;
			}
			$('#public_link').val(url);
			$('#public_link').data('code',code);
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

function doCopyLink(){
	enable('#public_link');
	$('#public_link').select();
	disable('#public_link');
	if(copyToClipboard('public_link')) {
		showToast($('#doCopyLink').data('ok'));
	}else{
		showToast($('#doCopyLink').data('err'),{type:'error',duration:3000});
	}
}

function doProject(action){
	if(action != 'save' && action != 'add') return false;

	var ButID = '#doCreateProject';
	if(action == 'save'){
		ButID =  '#doSaveProject';
	}

	var title = $('#title').val();
	var descr = $('#descr').val();
	var isPublic = ($('#public').prop("checked")) ? '1' : '0';
	var pubLinkCode = $('#public_link').data('code');
	var pid = $('#public').data('pid');

	startLoading(ButID);
	clearInpError();

	getJSON('projects',action,{title:title, descr:descr, isPublic:isPublic, pubLinkCode:pubLinkCode,pid:pid},function(resp) {
		if(resp.status != 200){
			if(resp.data == 'title') setInpError('#title',resp.error);
			if(resp.data == 'descr') setInpError('#descr',resp.error);
			if(resp.data == 'pubLink') setInpError('#public',resp.error);
			stopLoading(ButID);
			return false;
		}

		if(action == 'add'){
			locate('/langauages/'+resp.data+'/');
		}else{
			locate('/projects/');
		}		
	});
}
