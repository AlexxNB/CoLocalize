var termsnum = 0;
var loading = false;
var endlist = false;
var search = '';

$(document).ready(function() {
    $('#doSearch').click(function(){
        search = $('#search').val();
        termsnum = 0;
        endlist = false;
        clearContainer(function(){loadTerms(search)});
    });
    bindEnterKey('#search','#doSearch');

    onScrollEnd(function(){
        loadTerms(search);
    });

    clearContainer(function(){loadTerms(search)});
});

function loadTerms(query){
    query = query || '';

    if(loading || endlist) return false;

    var cont = $('#term-container');
    var pid = cont.data('pid');

    var listpart = $('<div>');
    startLoading(listpart);
    cont.append(listpart);

    loading = true;
    getJSON('terms','load',{query:query,num:termsnum,pid:pid},function(resp) {
        stopLoading(listpart);
		if(resp.status != 200){
            if(termsnum == 0) showToast(resp.error,{type:'error'});
            endlist=true;
            loading=false;
            return false;
        }
        var list = resp.data;
        if(!$.isArray(list)) return false;
        if(list.length == 0 && termsnum == 0){
            showEmpty();
            return true;
        }

        var cont = $('#term-container');
        $.each(list,function(k,term){
            termsnum++;
            var tile = $('#tile-sample').clone();
            var input = tile.find(".term-input");
            var num = tile.find(".term-num");
            var delBut = tile.find(".doDeleteTerm");
            tile.prop('id','tid-'+term.id);
            num.text(termsnum);
            cont.append(tile);
            tile.removeClass('hide');

            input.val(term.name);
            input.prop('tabindex',termsnum);
            var inVal = term.name;
            input.focusin(function(){
                inVal = $(this).val();
            });
            input.focusout(function(){
                var outVal = $(this).val();
                if(inVal == outVal) return false;
                saveTerm(term.id,pid,outVal,inVal);
            });

            delBut.click(function(){
                doDeleteTerm(term.id,pid)
            });
        });
        loading = false;
	});
}

function saveTerm(tid,pid,newVal,oldVal){
    var cont = $('#term-container');
    var loading = $('#tid-'+tid).find('.loading');
   
    loading.removeClass('hide');
    getJSON('terms','save',{tid:tid,value:newVal,pid:pid},function(resp) {
        loading.addClass('hide');
        if(resp.status == 200){
            showToast(resp.data,{type:'success'});
        }else{
            showToast(resp.error,{type:'error'});
            $('#tid-'+tid).find('.term-input').val(oldVal);
        }
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

function doDeleteTerm(tid,pid){
	var button = $('#confirmDelete');
	var modal = $('#modal-delete');

	modal.addClass('active');
	
	button.off();
	button.click(function(){
		startLoading(button);
		getJSON('terms','delete',{pid:pid,tid:tid},function(resp) {
			if(resp.status == 200){
				removeFromList(tid);
				showToast(button.data('ok'),{type:'success'});
			}else{
				showToast(button.data('err'),{type:'error'});
			}
			stopLoading(button);
			modal.removeClass('active');
		});
	});
}

function removeFromList(tid){
	var tile = $("#tid-"+tid);
	tile.slideUp(500,function(){
        tile.remove();
        termsnum--;
        var i = 1;
        $('.term-num').each(function(){
            $(this).text(i);
            i++;
        });
    });
}
