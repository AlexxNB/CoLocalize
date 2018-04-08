var termsnum = 0;
var loading = false;

$(document).ready(function() {
    clearContainer(function(){loadTerms()});
});

function loadTerms(query){
    query = query || '';

    var cont = $('#term-container');
    var pid = cont.data('pid');

    var listpart = $('<div>');
    startLoading(listpart);
    cont.append(listpart);

    
    getJSON('terms','load',{query:query,num:termsnum,pid:pid},function(resp) {
        console.log(resp);
        stopLoading(listpart);
		if(resp.status != 200){
			showToast(resp.error,{type:'error'});
        }
        var list = resp.data;
        if(!$.isArray(list)) return false;
        if(list.length == 0 && termsnum == 0){
            showEmpty();
            return true;
        }

        var cont = $('#term-container');
        $.each(list,function(k,term){
            var tile = $('#tile-sample').clone();
            var input = tile.find(".term-input");
            tile.data('tid',term.ID);
            input.val(term.Name);
            cont.append(tile);
            tile.removeClass('hide');
        });
	});
}

function clearContainer(func){
    var cont = $('#term-container');
    cont.slideUp(function(){
        cont.html('');
        cont.show(1);
        func();
    });
}

function showEmpty(){
    var empty = $('#empty-terms').clone();
    var cont = $('#term-container');
    cont.append(empty);
    empty.slideDown();
}
