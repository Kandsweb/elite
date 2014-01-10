<?php
/**
 * jscript_form_check
 *
 * @package page
 * @copyright Copyright 2003-2010 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: jscript_form_check.php 16186 2010-05-03 18:38:57Z drbyte $
 */
?>
<script language="javascript" type="text/javascript"><!--
var selected;

$(document).ready(function(){
    $(".user-type").change(function(){
        if($(".user-type").val()=='b'){
            $('.businessShow').toggle();
            //alert("S");
        }else{
            $('.businessShow').toggle();
            //alert("H");
        }
    })

    $(".trade-type").change(function(){
       if($(".trade-type").val()==99){
           //Show text box
           $(".tradeOther").show();
       }else{
           //Hide text box
           $(".tradeOther").hide();
       }
    });
});

function check_form_optional(form_name) {
  var form = form_name;
  if (!form.elements['firstname']) {
    return true;
  } else {
    var firstname = form.elements['firstname'].value;
    var lastname = form.elements['lastname'].value;
    var street_address = form.elements['street_address'].value;

    if (firstname == '' && lastname == '' && street_address == '') {
      return true;
    } else {
      return check_form(form_name);
    }
  }
}
var form = "";
var submitted = false;
var error = false;
var error_message = "";

function check_input(field_name, field_size, message, business_only) {
    business_only = typeof business_only !== 'undefined' ? business_only : false;

    if(business_only && !is_business())return;

  if (form.elements[field_name] && (form.elements[field_name].type != "hidden")) {
    if (field_size == 0) return;
    var field_value = form.elements[field_name].value;

    if (field_value == '' || field_value.length < field_size) {
      error_message = error_message + "* " + message + "\n";
      error = true;
      changeColor(field_name);
    }
  }
}

function is_business(){
  if (form.elements["userType"] && (form.elements["userType"].type != "hidden")) {
    var radio = form.elements["userType"];

    if (radio[1].checked == true) {
        return true;
    }
  }
}

function check_radio(field_name, message, business_only) {
    business_only = typeof business_only !== 'undefined' ? business_only : false;
    var isChecked = false;

    if(!business_only || !is_business())return;

  if (form.elements[field_name] && (form.elements[field_name].type != "hidden")) {
    var radio = form.elements[field_name];

    for (var i=0; i<radio.length; i++) {
      if (radio[i].checked == true) {
        isChecked = true;
        break;
      }
    }

    if (isChecked == false) {
      error_message = error_message + "* " + message + "\n";
      error = true;
      //changeColor(field_name);
    }
  }
}


function check_select(field_name, field_default, message) {
  if (form.elements[field_name] && (form.elements[field_name].type != "hidden")) {
    var field_value = form.elements[field_name].value;

    if (field_value == field_default) {
      error_message = error_message + "* " + message + "\n";
      error = true;
    }
  }
}

function check_password(field_name_1, field_name_2, field_size, message_1, message_2) {
  if (form.elements[field_name_1] && (form.elements[field_name_1].type != "hidden")) {
    var password = form.elements[field_name_1].value;
    var confirmation = form.elements[field_name_2].value;

    if (password == '' || password.length < field_size) {
      if(message_1.length > 0){
        error_message = error_message  + "* " +  message_1 + "\n";
      }
      error = true;
      changeColor(field_name_1);
      changeColor(field_name_2);
    } else if (password != confirmation) {
      error_message = error_message  + "* " +  message_2 + "\n";
      error = true;
      changeColor(field_name_2);
    }
  }}

function check_password_new(field_name_1, field_name_2, field_name_3, field_size, message_1, message_2, message_3) {
  if (form.elements[field_name_1] && (form.elements[field_name_1].type != "hidden")) {
    var password_current = form.elements[field_name_1].value;
    var password_new = form.elements[field_name_2].value;
    var password_confirmation = form.elements[field_name_3].value;

    if (password_current == '' ) {
      error_message = error_message + "* " + message_1 + "\n";
      error = true;
    } else if (password_new == '' || password_new.length < field_size) {
      error_message = error_message + "* " + message_2 + "\n";
      error = true;
    } else if (password_new != password_confirmation) {
      error_message = error_message + "* " + message_3 + "\n";
      error = true;
    }
  }
}

function check_state(min_length, min_message, select_message) {
  if (form.elements["state"] && form.elements["zone_id"]) {
    if (!form.state.disabled && form.zone_id.value == "") check_input("state", min_length, min_message);
  } else if (form.elements["state"] && form.elements["state"].type != "hidden" && form.state.disabled) {
    check_select("zone_id", "", select_message);
  }
}

function check_trade_type(){
    var sVal = $('#trade_type').val();
    if(sVal==0){
        error_message = error_message + "* You have not selected your business type\n";
        error = true;
        changeColor('trade_type');
    }else if(sVal==99){
          if (form.elements['trade-other'] && (form.elements['trade-other'].type != "hidden")) {
    var field_value = form.elements['trade-other'].value;

    if (field_value == '' || field_value.length < 2) {
      error_message = error_message + "* You have not entered your business type\n";
      error = true;
      changeColor('trade-other');
    }
  }

    }
}

function check_form(form_name) {
  if (submitted == true) {
    alert("<?php echo JS_ERROR_SUBMITTED; ?>");
    return false;
  }

  error = false;
  form = form_name;
  error_message = "<?php echo JS_ERROR; ?>";

  //check_input("email_address", "<?php echo (int)ENTRY_EMAIL_ADDRESS_MIN_LENGTH; ?>" , "<?php echo ENTRY_EMAIL_ADDRESS_ERROR; ?>");
  check_password("email_address", "email_addressConf", "<?php echo ENTRY_PASSWORD_MIN_LENGTH; ?>", "", "* The confirmation email must match your email");
  validateEmail('account_edit','email_address');
  check_password("password", "confirmation", "<?php echo (int)ENTRY_PASSWORD_MIN_LENGTH; ?>", "<?php echo ENTRY_PASSWORD_ERROR; ?>", "<?php echo ENTRY_PASSWORD_ERROR_NOT_MATCHING; ?>");
  //check_password_new("password_current", "password_new", "password_confirmation", "<?php echo (int)ENTRY_PASSWORD_MIN_LENGTH; ?>", "<?php echo ENTRY_PASSWORD_ERROR; ?>", "<?php echo ENTRY_PASSWORD_NEW_ERROR; ?>", "<?php echo ENTRY_PASSWORD_NEW_ERROR_NOT_MATCHING; ?>");

  //check_input("company",3,"Is your company name correct?. Our system requires a minimum of 3 characters", true);
  //check_radio("trading-type","Please indicate if you are a sole trader or a limited company", true);

  //check_trade_type();


<?php if (ACCOUNT_GENDER == 'true') echo '  check_radio("gender", "' . ENTRY_GENDER_ERROR . '");' . "\n"; ?>

<?php if ((int)ENTRY_FIRST_NAME_MIN_LENGTH > 0) { ?>
  check_input("firstname", "<?php echo (int)ENTRY_FIRST_NAME_MIN_LENGTH; ?>", "<?php echo ENTRY_FIRST_NAME_ERROR; ?>");
<?php } ?>
<?php if ((int)ENTRY_LAST_NAME_MIN_LENGTH > 0) { ?>
  check_input("lastname", "<?php echo (int)ENTRY_LAST_NAME_MIN_LENGTH; ?>", "<?php echo ENTRY_LAST_NAME_ERROR; ?>");
<?php } ?>

<?php if (ACCOUNT_DOB == 'true' && (int)ENTRY_DOB_MIN_LENGTH != 0) echo '  check_input("dob", ' . (int)ENTRY_DOB_MIN_LENGTH . ', "' . ENTRY_DATE_OF_BIRTH_ERROR . '");' . "\n"; ?>
<?php if (ACCOUNT_COMPANY == 'true' && (int)ENTRY_COMPANY_MIN_LENGTH != 0) echo '  check_input("company", ' . (int)ENTRY_COMPANY_MIN_LENGTH . ', "' . ENTRY_COMPANY_ERROR . '");' . "\n"; ?>

<?php //if ((int)ENTRY_EMAIL_ADDRESS_MIN_LENGTH > 0) { ?>
  //check_input("email_address", <?php //echo (int)ENTRY_EMAIL_ADDRESS_MIN_LENGTH; ?>, "<?php //echo ENTRY_EMAIL_ADDRESS_ERROR; ?>");
<?php //} ?>

<?php if ((int)ENTRY_TELEPHONE_MIN_LENGTH > 0) { ?>
  check_input("telephone", "<?php echo ENTRY_TELEPHONE_MIN_LENGTH; ?>", "<?php echo ENTRY_TELEPHONE_NUMBER_ERROR; ?>");
  check_input("mobile", "<?php echo ENTRY_TELEPHONE_MIN_LENGTH; ?>", "Your Mobile Number is invalid");
<?php } ?>



  if (error == true) {
    alert(error_message);
    return false;
  } else {
    submitted = true;
    return true;
  }
}

////////////////////////////////////////////////////////////////////////////
function validateEmail(form_id,email1) {
var address1 = document.forms[form_id].elements[email1].value;
var emailPat=/^(.+)@(.+)$/
var specialChars="\\(\\)<>@,;:\\\\\\\"\\.\\[\\]"
var validChars="\[^\\s" + specialChars + "\]"
var quotedUser="(\"[^\"]*\")"
var ipDomainPat=/^\[(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})\]$/
var atom=validChars + '+'
var word="(" + atom + "|" + quotedUser + ")"
var userPat=new RegExp("^" + word + "(\\." + word + ")*$")
var domainPat=new RegExp("^" + atom + "(\\." + atom +")*$")
var matchArray=address1.match(emailPat)
if (matchArray==null) {
   error_message = error_message + "* Your email address is incorrect (check @ and .'s)" + "\n";
   error = true;
   changeColor(email1);
   return;
}
var user=matchArray[1]
var domain=matchArray[2]
var errStr = "";
// See if "user" is valid
if (user.match(userPat)==null) {
    // user is not valid
    error_message = error_message + "* The part of your email address before the '@' is not valid." + "\n";
    error = true;
    changeColor(email1);
    return;
}
var IPArray=domain.match(ipDomainPat)
if (IPArray!=null) {
    // this is an IP address
    for (var i=1;i<=4;i++) {
      if (IPArray[i]>255) {
          error_message = error_message + "* Destination IP address is invalid!" + "\n";
          error = true;
          changeColor(email1);
          return;
      }
    }
    return;
}
var domainArray=domain.match(domainPat);
if (domainArray==null) {
  error_message = error_message + "* Part of your email address after the '@' is not valid" + "\n";
  error = true;
  changeColor(email1);
  return;
}
var atomPat=new RegExp(atom,"g");
var domArr=domain.match(atomPat);
var len=domArr.length;
if (domArr[domArr.length-1].length<2 ||
    domArr[domArr.length-1].length>6) {
   // the address must end in a two letter or other TLD including museum
   error_message = error_message +  "* Your email address must end in a top level domain (e.g. .com), or two letter country." + "\n";
   error = true;
   changeColor(email1);
   return;
}
if (len<2) {
   error_message = error_message + "* Your email address is missing a hostname!" + "\n";
   error = true;
   changeColor(email1);
   return;
}

// If we've got this far, everything's valid!
return;
}

function changeColor(field) {
  form.elements[field].style.background="#ff6666";
}

function getEvent(e){
  if(window.event != null) {
    return event;
  }
  return e;
}

function reSetBKColor(e){
 e = getEvent(e);
 var src =  e.srcElement || e.target;
 if(src != null) {
   src.style.backgroundColor = "#ffffff";
 }
}
//--></script>
