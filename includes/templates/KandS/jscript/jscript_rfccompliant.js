<!-- Original author:  Sandeep V. Tamhankar (stamhankar@hotmail.com) -->
<!-- old Source on http://www.jsmadeeasy.com/javascripts/Forms/Email%20Address%20Validation/template.htm -->
<!-- The above address bounces and no current valid address  -->
<!-- can be found. This version has changes by Craig Cockburn -->
<!-- to accommodate top level domains .museum and .name      -->
<!-- plus various other minor corrections and changes -->
/* Script revision history
1.1.4. October 2010 Javascript minor modification by Codex-m at http://www.php-developer.org for original and confirm address client side verification.
Source of revision: http://www.siliconglen.com/software/e-mail-validation.html

THIS SOFTWARE IS PROVIDED BY THE AUTHORS "AS IS" AND
ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR
ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

1.1.3: Amended error messages and allowed script to deal with new TLDs
1.1.2: Fixed a bug where trailing . in e-mail address was passing
            (the bug is actually in the weak regexp engine of the browser; I
            simplified the regexps to make it work).
1.1.1: Removed restriction that countries must be preceded by a domain,
            so abc@host.uk is now legal.
1.1: Rewrote most of the function to conform more closely to RFC 822.
1.0: Original  */
<!-- Begin
function emailCheck(form_id,email1,email2) {
var address1 = document.forms[form_id].elements[email1].value;
var address2 = document.forms[form_id].elements[email2].value;
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
	alert("Email address seems incorrect (check @ and .'s)")
	return false
}
var user=matchArray[1]
var domain=matchArray[2]
// See if "user" is valid
if (user.match(userPat)==null) {
    // user is not valid
    alert("The part of your email address before the '@' doesn't seem to be valid.")
    return false
}
var IPArray=domain.match(ipDomainPat)
if (IPArray!=null) {
    // this is an IP address
	  for (var i=1;i<=4;i++) {
	    if (IPArray[i]>255) {
	        alert("Destination IP address is invalid!")
		return false
	    }
    }
    return true
}
var domainArray=domain.match(domainPat)
if (domainArray==null) {
	alert("Part of your email address after the '@' doesn't seem to be valid")
    return false
}
var atomPat=new RegExp(atom,"g")
var domArr=domain.match(atomPat)
var len=domArr.length
if (domArr[domArr.length-1].length<2 ||
    domArr[domArr.length-1].length>6) {
   // the address must end in a two letter or other TLD including museum
   alert("The address must end in a top level domain (e.g. .com), or two letter country.")
   return false
}
if (len<2) {
   var errStr="This address is missing a hostname!"
   alert(errStr)
   return false
}
if (address1 != address2) {
	alert('The email addresses entered for both text boxes are not identical');
	return false;
}
// If we've got this far, everything's valid!
return true;
}
//  End -->