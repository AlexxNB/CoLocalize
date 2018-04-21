$(document).ready(function() {
	$('#showAddLanguage').click(function(){
		showAddLanguage();
	});

	$('#doAddLanguage').click(function(){
		doAddLanguage();
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
		modal.removeClass('active');
		showToast(resp.data,{type:'success'});
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

