$(document).ready(function(){
  $('#contactname').focus(function(){
    clearErr('name_wrap', 'contactname');
  });
  $('#email-address').focus(function(){
    clearErr('email_wrap', 'email-address');
  });
  $('#enquiry').focus(function(){
    clearErr('message_wrap', 'enquiry');
  });
  $('#recaptcha_response_field').focus(function(){
    clearErr('captcha_wrap', 'captcha');
  });

});//end $(document).ready(function()

var err='';

function checkForm(){
  err='';
  if($('#contactname').val()==''){
    setError('name_wrap', 'contactname', 'Please enter your name');
  }
  if($('#email-address').val()==''){
    setError('email_wrap', 'email-address', 'Please enter a valid email address');
  }
  if($('#enquiry').val()==''){
    setError('message_wrap', 'enquiry', 'Please enter a message');
  }
  if($('#recaptcha_response_field').val()==''){
     setError('captcha_wrap', 'captcha', 'Please type the security code above');
  }
  if(err!=''){
    $('#err_msg').html('<b>We have found problems with your answers. Please do the following:</b><br/>' + err);
    $('#err_msg').css({'background-color': '#fddddd', 'border': '1px solid silver'});
   $('html,body').animate({scrollTop: $("#err_msg").offset().top},'slow');
    return
  }

  $("#fuzz").css("height", $(document).height());
  $("#fuzz").fadeIn();
  $('form[name="contact_us"]').submit();
}

function setError(theDiv, theLable, theMsg){
  $('#'+ theDiv).css({'background-color': '#fddddd', 'border': '1px solid silver'});
  $('[for=' + theLable +']').css({'color': 'red', 'font-weight': 'bold'});
  $('#'+theDiv).children('.err_icon').css({'display':'inherit'});
  if($("#" + theDiv).find("span").length < 2){
    $('#'+theDiv).append("<span id='emtt'>" + theMsg + "</span>");
  }
  if(err.search(theMsg)== -1){
    if(err!=''){
        err += '<br/>';
    }
    err +=  theMsg;
  }
}

function clearErr(theDiv, txtSelector){
  $('#'+theDiv).children('.err_icon').css({'display':'none'});
  $('#'+theDiv).css({'background-color': '', 'border':''});
  $('[for='+txtSelector+']').css({'color': '', 'font-weight': ''});
  //$('#'+theDiv+' span:last').remove();
  $('#'+theDiv).children('#emtt').remove();
}