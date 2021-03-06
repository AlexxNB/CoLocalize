$(document).ready(function() {
    repairDropdown();
    closeModals();
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

function closeModals(){
    $(".modal-close").click(function(){
        $(".modal").removeClass('active');
    });
}

function showToast(msg,config){
    config = config || {}

    config.duration = config.duration || 2000;
    config.type = config.type || 'normal';
    

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
    switch(config.type){
        case 'error':
            toast.addClass('toast-error');
            break; 
        case 'primary':
            toast.addClass('toast-primary');
            break; 
        case 'success':
            toast.addClass('toast-success');
            break; 
        case 'warning':
            toast.addClass('toast-warning');
            break; 
    }

    toast.html(msg);
    toast.append(close);
    toast.hide();

    close.click(function(){
        closeToast(toast);
    });

    setTimeout(closeToast,config.duration,toast);

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