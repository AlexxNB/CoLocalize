$(document).ready(function() {
    repairDropdown();
});

function repairDropdown(){
    var ddTimer;
    $('.overdown').mouseenter(function(){
        clearTimeout(ddTimer);
        $(this).addClass('active');
    });	

    $('.overdown').mouseleave(function(){
        ddTimer = setTimeout(function(e){
            e.removeClass('active');
        },200,$(this));
    });
}

function showToast(msg,duration){

    duration = duration || 2000;

    var container = null;
    if(!exists('#toast-container')){
        container = $('<div></div>');
        container.attr('id','toast-container');
        container.addClass('toast-container');
        $('body').append(container);
    }else{
        container = $('#toast-container');
    }

    var toast = $('<div></div>');
    var close = $('<button></button>');

    toast.addClass('toast');
    close.addClass('btn btn-clear float-right');

    toast.html(msg);
    toast.append(close);
    toast.hide();

    close.click(function(){
        closeToast(toast);
    });

    setTimeout(closeToast,duration,toast);

    container.append(toast); 
    toast.fadeIn(500);
}

function closeToast(toast){
    var container = toast.parent(0);
    toast.css({ opacity: 0, transition: 'opacity 0.3s' });
    toast.slideUp(300,function(){
        toast.remove()
        if(container.children().length == 0){
            container.remove();
        }
    });
}