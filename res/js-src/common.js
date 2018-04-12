function getJSON(group,command,data,func){
    $.getJSON('/api/'+group+'/'+command+'/',data)
        .done(func)
        .fail(function( jqxhr, textStatus, error ) {
            console.log( "Request Failed: " + error );
        });
}

function getURL(part){
    var url = window.location.pathname;
    if(part == undefined) return url;
    url = url.split('/');
    if(url[part] == undefined || url[part] == '') return false;
    return url[part];
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

function bindKey(obj,code,func){
    obj = validJQ(obj);
    obj.keydown(function(e){
        if(e.keyCode==code){
            func();
        }
    });
}

function exists(id){
    if($(id).length > 0)
        return true;
    else
        return false;
}

function validJQ(obj){
    if(isJQObject(obj)) return obj;
    return $(obj);
}

function isJQObject(obj){
    if(obj.jquery == undefined) return false;
    return true;
}

function startLoading(id){
    if(isJQObject(id))
        id.addClass('loading');
    else
        $(id).addClass('loading');
}

function stopLoading(id){
    if(isJQObject(id))
        id.removeClass('loading');
    else
        $(id).removeClass('loading');
}

function disable(id){
    $(id).prop('disabled',true);
}

function enable(id){
    $(id).prop('disabled',false);
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

function upload(group,command,file_data,data,success,progress){
        var form_data = new FormData();
        form_data.append('file', file_data);
        $.each(data,function(name,value){
            form_data.append(name, value);   
        });
        $.ajax({
            url: '/api/'+group+'/'+command+'/',
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,
            type: 'post',
            success: success,
            error: function( jqxhr, textStatus, error ) {
                console.log( "Request Failed: " + error );
            },
            xhr: function(){
                var xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener("progress", function(evt){
                    if (evt.lengthComputable) {
                        var percentComplete = evt.loaded / evt.total;
                        progress(percentComplete);
                    }
                }, false);
                return xhr;
            }
        });
}

function onScrollEnd(func){
    $(window).scroll(function(){
        if  ($(window).scrollTop() == $(document).height() - $(window).height()){
           func();
        }
}); 
}