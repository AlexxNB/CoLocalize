$(document).ready(function() {
	$('#showAddLanguage').click(function(){
		showAddLanguage();
	});

	$('#doAddLanguage').click(function(){
		doAddLanguage();
	});

	$('.doDeleteLanguage').click(function(){
		doDeleteLanguage($(this).data('lid'));
	});
	
});

function showAddLanguage(){
	var modal = $('#modal-addlang');
	modal.addClass('active');
	loadLangList();
}

function doAddLanguage(){
	var button = $('#doAddLanguage');
	var pid = button.data('pid');
	var code = $('#lang').val();
	var modal = $('#modal-addlang');

	startLoading(button);
	getJSON('langs','add',{pid:pid,code:code},function(resp) {
		stopLoading(button);
		if(resp.status != 200){
			showToast(resp.error,{type:'error'});
			return false;
		}
		locate();
	});
}

function loadLangList(){
	var select = $('#lang');
	var pid = $('#doAddLanguage').data('pid');
	var loading = $('#listloading');
	loading.removeClass('hide');
	$('.langopt').remove();
	getJSON('langs','getUnusedList',{pid:pid},function(resp){
		loading.addClass('hide');
		if(resp.status == 200){
			$.each(resp.data,function(code,lang){
				select.append($('<option>', {
					value: code,
					class: 'langopt',
					text: lang.name + ' | ' + lang.native
				}));
			});
		}
	});
}

function doDeleteLanguage(lid){
	var button = $('#confirmDelete');
	var modal = $('#modal-delete');

	modal.addClass('active');
	
	button.off();
	button.click(function(){
		startLoading(button);
		getJSON('langs','delete',{lid:lid},function(resp) {
			if(resp.status == 200){
				removeFromList(lid);
				showToast(resp.data,{type:'success'});
			}else{
				showToast(resp.error,{type:'error'});
			}
			stopLoading(button);
			modal.removeClass('active');
		});
	});
}

function removeFromList(lid){
	var tile = $("#lid-"+lid);
	tile.slideUp(500,function(){
		tile.remove();
		if(!exists('.lang-tile')) locate();
	});
}
