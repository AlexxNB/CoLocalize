$(document).ready(function() {
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
});

function getJSON(group,command,data,func){
    $.getJSON('/ajax/'+group+'/'+command+'/',data)
        .done(func)
        .fail(function( jqxhr, textStatus, error ) {
            console.log( "Request Failed: " + err );
        });
}


function setInpError(e,text){
    $(e).addClass('is-error').after('<p class="form-input-hint inpError">'+text+'</p>');
}

function clearInpError(){
    $('.inpError').remove();
	$('.is-error').removeClass('is-error');
}

function bindEnterKey($keypressed,$to_click){
    $($keypressed).keypress(function(e){
        if(e.keyCode==13){
            $($to_click).click();
        }
    });
}

function exists($id){
    if($($id).length > 0)
        return true;
    else
        return false;
}

function startLoading($id){
    $($id).addClass('loading');
}

function stopLoading($id){
    $($id).removeClass('loading');
}

function disable($id){
    $($id).attr('disabled','disabled');
}

function enable($id){
    $($id).removeAttr('disabled');
}

var sock = null;
function startWS(onOpen,onMessage){
    var wsuri = "ws://185.125.218.27:8080";
    sock = new WebSocket(wsuri);
    sock.onopen = function() {onOpen()}
    sock.onclose = function(e) {}
    sock.onmessage = function(e) {
        var json = JSON.parse(e.data);
        onMessage(json.command,json.data);
    }   
}

function wsSendCommand(command,data) {
    if (sock == null) return false;
    var cmd = {command:command,data:data}
    sock.send(JSON.stringify(cmd));
}