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
    $.getJSON('/api/'+group+'/'+command+'/',data)
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

function exists(id){
    if($(id).length > 0)
        return true;
    else
        return false;
}

function startLoading(id){
    $(id).addClass('loading');
}

function stopLoading(id){
    $(id).removeClass('loading');
}

function disable(id){
    $(id).attr('disabled','disabled');
}

function enable(id){
    $(id).removeAttr('disabled');
}

function locate(url){
    document.location.href = url;	
}

function copyToClipboard(id) {
  var elem = document.getElementById(id);
  var targetId = "_hiddenCopyText_";
  var isInput = elem.tagName === "INPUT" || elem.tagName === "TEXTAREA";
  var origSelectionStart, origSelectionEnd;
  if (isInput) {
      target = elem;
      origSelectionStart = elem.selectionStart;
      origSelectionEnd = elem.selectionEnd;
  } else {
      target = document.getElementById(targetId);
      if (!target) {
          var target = document.createElement("textarea");
          target.style.position = "absolute";
          target.style.left = "-9999px";
          target.style.top = "0";
          target.id = targetId;
          document.body.appendChild(target);
      }
      target.textContent = elem.textContent;
  }
  var currentFocus = document.activeElement;
  target.focus();
  target.setSelectionRange(0, target.value.length);
  
  var succeed;
  try {
        succeed = document.execCommand("copy");
  } catch(e) {
      succeed = false;
  }
  if (currentFocus && typeof currentFocus.focus === "function") {
      currentFocus.focus();
  }
  
  if (isInput) {
      elem.setSelectionRange(origSelectionStart, origSelectionEnd);
  } else {
      target.textContent = "";
  }
  return succeed;
}
