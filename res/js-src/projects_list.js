$(document).ready(function() {
	$('.doDeleteProject').click(function(){
		doDeleteProject($(this).data("pid"));
	});
});

function doDeleteProject(pid){
	var butID = '#confirmDelete';
	var button = $(butID);
	var modal = $('#modal-delete');

	modal.addClass('active');
	
	button.off();
	button.click(function(){
		startLoading(butID);
		getJSON('projects','delete',{pid:pid},function(resp) {
			if(resp.status == 200){
				removeFromList(pid);
				showToast(button.data('ok'),{type:'success'});
			}else{
				showToast(button.data('err'),{type:'error'});
			}
			stopLoading(butID);
			modal.removeClass('active');
		});
	});
}

function removeFromList(pid){
	var tile = $("#pid-"+pid);
	tile.slideUp(500,function(){
		tile.remove();
		if(!exists('.project-tile')) locate('/projects/');
	});
}

