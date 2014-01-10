// *************************************************************************
// *                                                                       *
// * (c) 2008-2012 Wolf Software Limited <support@wolf-software.com>       *
// * All Rights Reserved.                                                  *
// *                                                                       *
// * This program is free software: you can redistribute it and/or modify  *
// * it under the terms of the GNU General Public License as published by  *
// * the Free Software Foundation, either version 3 of the License, or     *
// * (at your option) any later version.                                   *
// *                                                                       *
// * This program is distributed in the hope that it will be useful,       *
// * but WITHOUT ANY WARRANTY; without even the implied warranty of        *
// * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         *
// * GNU General Public License for more details.                          *
// *                                                                       *
// * You should have received a copy of the GNU General Public License     *
// * along with this program.  If not, see <http://www.gnu.org/licenses/>. *
// *                                                                       *
// *************************************************************************

(function($) {

	var cookiePage = "index.php?main_page=cookie_usage";


	if (typeof $.ws === 'undefined') {
		$.ws = function() {};
	}

	if (typeof $.ws.core == 'undefined') {
		//console.log("$.ws.core is available as a seperate include at https://cdn.wolf-secure.com/jquery/core/");
		$.ws.core = function() {};
	}

	if (typeof $.ws.core.debugAlert === 'undefined') {
		$.ws.core.debugAlert = function(template, level, message) {
			var output = template;
			output = output.replace('{MESSAGE}', message).replace('{LEVEL}', level);
			alert(output);
		};
	}

	if (typeof $.ws.core.cookies === 'undefined') {
		$.ws.core.cookies = function() {};
	}
	if (typeof $.ws.core.cookies.read === 'undefined') {
		$.ws.core.cookies.read = function(name) {
			try {
				var nameEq = name + '=';
				var ca = document.cookie.split(';');
				for (var i = 0; i < ca.length; i++) {
					var c = ca[i];
					while (c.charAt(0) == ' ') {
						c = c.substring(1, c.length);
					}
					if (c.indexOf(nameEq) == 0) {
						return c.substring(nameEq.length, c.length);
					}
				}
			} catch (err) {
				//Ignore any errors, we'll just assume the cookie hasn't been set..
			}
			return null
		};
	}
	if (typeof $.ws.core.cookies.write === 'undefined') {
		$.ws.core.cookies.write = function(name, value, days) {
			if (days)
                {
					var date = new Date();
					date.setTime(date.getTime()+(days * 24 * 60 * 60 * 1000));
					var expires = ";expires=" + date.toGMTString();
				}
            else
				var expires = "";
			document.cookie = name+"="+value+expires+"; path=/";
		};
	}

	if (typeof $.ws.core.json === 'undefined') {
		$.ws.core.json = function() {};
	}
	if (typeof $.ws.core.json.encodeArray === 'undefined') {
		$.ws.core.json.encodeArray = function(arr) {
			var parts = [];
			var is_list = (Object.prototype.toString.apply(arr) === '[object Array]');
			for(var key in arr) {
				var value = arr[key];
				if(typeof value == "object") {
					if(is_list) parts.push($.ws.core.json.encodeArray(value));
					else parts[key] = $.ws.core.json.encodeArray(value);
				} else {
					var str = "";
					if(!is_list) str = '"' + key + '":';
					//Custom handling for multiple data types
					if(typeof value == "number") str += value;
					else if(value === false) str += 'false';
					else if(value === true) str += 'true';
					else str += '"' + value + '"';
					// :TODO: Is there any more datatype we should be in the lookout for? (Functions?)
					parts.push(str);
				}
			}
			var json = parts.join(",");
			if(is_list) return '[' + json + ']';
			return '{' + json + '}';
		}
	}

	if (typeof $.ws.core.keepAlive === 'undefined') {
		$.ws.core.keepAlive = new function() {};
	}
	if (typeof $.ws.core.keepAlive.interval === 'undefined') {
		$.ws.core.keepAlive.interval = null;
	}
	if (typeof $.ws.core.keepAlive.URLs === 'undefined') {
		$.ws.core.keepAlive.URLs = new Array();
	}
	if (typeof $.ws.core.keepAlive.started === 'undefined') {
		$.ws.core.keepAlive.started = false;
	}
	if (typeof $.ws.core.keepAlive.start === 'undefined') {
		$.ws.core.keepAlive.start = function(interval) {
			if (typeof interval === 'undefined' || interval == null) {
				interval = 60000;
			}
			$.ws.core.keepAlive.interval = setInterval('$.ws.core.keepAlive.run();', interval);
		};
	}
	if (typeof $.ws.core.keepAlive.stop === 'undefined') {
		$.ws.core.keepAlive.stop = function() {
			clearInterval($.ws.core.keepAlive.interval);
		};
	}
	if (typeof $.ws.core.keepAlive.run === 'undefined') {
		$.ws.core.keepAlive.run = function() {
			for (url in $.ws.core.keepAlive.URLs) {
				$.ajax({
					type :		"GET",
					url :		$.ws.core.keepAlive.URLs[url],
					cache : 	false,
					error :	function(jqXHR, textStatus, errorThrown) {
						console.log('ERROR: ' + errorThrown);
					}
				});
			}
		};
	}
	if (typeof $.ws.core.keepAlive.push === 'undefined') {
		$.ws.core.keepAlive.push = function(url, autoStart) {
			if (typeof autoStart === 'undefined' || autoStart == null) {
				autoStart = true;
			}
			$.ws.core.keepAlive.URLs.push(url);
			if (autoStart && !$.ws.core.keepAlive.started) {
				$.ws.core.keepAlive.start();
			}
		};
	}

	if (typeof $.ws.core.reload === 'undefined') {
		$.ws.core.reload = function() {
			location.reload(true);
		};
	}

	// **************************
	// * JPECRGA PLUGIN			*
	// **************************

	// Deprecated Factory
	if (typeof $.jpecr === 'undefined') {
		$.jpecr = function(options) {
			console.log("$.jpecr will be deprecated at the next minor version, please use $.ws.jpecr instead");
			return $.ws.jpecr(options);
		}
	}

	// Factory
	if (typeof $.ws.jpecr === 'undefined') {
		$.ws.jpecr = function(options) {
			var docElement = $(document);
			if (docElement.data('jpecr')) return docElement.data('jpecr');
			var jpecr = new $.ws.jpecr.create(options);
			docElement.data('jpecr', jpecr);
		};
	}

	//Initialisation
	if (typeof $.ws.jpecr.create === 'undefined') {
		$.ws.jpecr.create = function(options) {

			this.settings = $.extend({debug : false}, $.ws.jpecr.defaults, options);
			var settings = this.settings;
			if (settings.debug) { $.ws.core.debugAlert($.ws.jpecr.alertTemplate, "INFO", "Running $.ws.jpecr.create"); }

			var jpecr = this;

			if (typeof jpecr.blackout === 'undefined') {
				jpecr.blackout = $('<div id="wsjpecrBlackout" />').css({position: 'fixed', top: 0, left: 0, bottom: 0, right: 0, background: '#000'}).hide();
				$('body').append(jpecr.blackout);
			}

			if (typeof jpecr.container === 'undefined') {
				jpecr.container = $('<div id="wsjpecr" />').addClass('jpecr' + settings.skin).addClass(settings.intrusion).addClass(settings.growlerType).addClass(settings.popupType);
				$('body').append(jpecr.container);
			}


			if (typeof jpecr.growlerDiv === 'undefined') {
				jpecr.growlerDiv = $('<div />').addClass('jpecrGrowler');
				jpecr.container.append(jpecr.growlerDiv);
				jpecr.growlerDiv.fadeOut(0);
			}
			if (typeof jpecr.growlerIcon === 'undefined') {
				jpecr.growlerIcon = $('<img src="' + settings.growlerIcon + '" alt="" />').addClass('jpecrGrowlerIcon');
				jpecr.growlerDiv.append(jpecr.growlerIcon);
			}
			if (typeof jpecr.growlerButtonDiv === 'undefined') {
				jpecr.growlerButtonDiv = $('<div />').addClass('jpecrGrowlerButtons');
				jpecr.growlerDiv.append(jpecr.growlerButtonDiv);
				var viewButton = $('<a class="button" href="javascript:void(0);">View Settings</a>');
				var hideButton = $('<a class="button" href="javascript:void(1);">Ask Me Later</a>');
				jpecr.growlerButtonDiv.append(viewButton);
				jpecr.growlerButtonDiv.append(hideButton);
				viewButton.click(function() {
					$.ws.jpecr.hideGrowler(jpecr, true, function() {
						$.ws.jpecr.display(jpecr, false);
					});
				});
				hideButton.click(function() {
					$.ws.jpecr.hideGrowler(jpecr);
					$.ws.jpecr.hidePopup(jpecr);
					if (settings.growlerRepeat) {
						$.ws.jpecr.growlTimer = setTimeout('$.ws.jpecr.growl($.ws.jpecr());', settings.growlerDelay);
					}
				});
			}
			if (typeof jpecr.growlerMessage === 'undefined') {
				jpecr.growlerMessage = $('<div />').addClass('jpecrGrowlerMessage');
				jpecr.growlerMessage.html(settings.growlerMessage);
				jpecr.growlerDiv.append(jpecr.growlerMessage);
			}

			if (typeof jpecr.popup === 'undefined') {
				jpecr.popup = $('<div />').addClass('jpecrPopup');
				jpecr.container.append(jpecr.popup);
				jpecr.popup.fadeOut(0);
				var maxHeight = $(window).height();
				maxHeight -= 115;
				jpecr.popup.css('max-height', maxHeight);
				var contentHeight = 0;
				jpecr.popup.children().each(function() {
					contentHeight += $(this).outerHeight();
				});
				contentHeight += (jpecr.popup.outerHeight() - jpecr.popup.innerHeight());
				if (contentHeight > (maxHeight - 59)) {
					jpecr.popup.css('overflow-y', 'scroll');
				} else {
					jpecr.popup.css('overflow-y', 'visible');
				}
				$(window).resize(function() {
					var maxHeight = $(window).height();
					maxHeight -= 115;
					jpecr.popup.css('max-height', maxHeight);
					var contentHeight = 0;
					jpecr.popup.children().each(function() {
						contentHeight += $(this).outerHeight();
					});
					contentHeight += (jpecr.popup.outerHeight() - jpecr.popup.innerHeight());
					if (contentHeight > (maxHeight - 59)) {
						jpecr.popup.css('overflow-y', 'scroll');
					} else {
						jpecr.popup.css('overflow-y', 'visible');
					}
				});
			}

			if (settings.keepAlive != null) {
				$.ws.core.keepAlive.push(settings.keepAlive, false);
			}

			jpecr.data = null;

			$.ws.jpecr.getData(jpecr);

			if (settings.displayButtonSelector != null) {
				$(settings.displayButtonSelector).each(function() {
					var button = $(this);
					button.attr('href', 'javascript:void(5);');
					button.click(function() { $.ws.jpecr.display(jpecr, true); });
				});
			}

			setTimeout('$.ws.jpecr.run($.ws.jpecr());', settings.delayTime);

		};
	}

	if (typeof $.ws.jpecr.growlTimer === 'undefined') {
		$.ws.jpecr.growlTimer = null;
	}

	if (typeof $.ws.jpecr.run === 'undefined') {
		$.ws.jpecr.run = function(jpecr) {

			var settings = jpecr.settings;
			if (settings.debug) { $.ws.core.debugAlert($.ws.jpecr.alertTemplate, "INFO", "Running $.ws.jpecr.run"); }

			if (jpecr.data == null) {
				console.log('we dont have any cookie data :(');
				return;
			}

			if (settings.keepAlive != null) {
				$.ws.core.keepAlive.start();
			}

			var alreadyAnswered = true;
			$.each(jpecr.data, function(){
				if (this.consent == null) {
					alreadyAnswered = false;
				}
			});

			if (!alreadyAnswered) {
				switch (settings.intrusion) {
					case 'growl':
						$.ws.jpecr.growl(jpecr);
						break;
					case 'popup':
						$.ws.jpecr.display(jpecr);
						break;
					case 'none':
						break;
				}
			}

		}
	}

	if (typeof $.ws.jpecr.growl === 'undefined') {
		$.ws.jpecr.growl = function(jpecr) {
			var settings = jpecr.settings;
			if (settings.debug) { $.ws.core.debugAlert($.ws.jpecr.alertTemplate, "INFO", "Running $.ws.jpecr.growl"); }
			jpecr.growlerButtonDiv.show();
			jpecr.growlerDiv.stop(true).fadeIn(settings.fadeSpeed);
		};
	}

	if (typeof $.ws.jpecr.hideGrowler === 'undefined') {
		$.ws.jpecr.hideGrowler = function(jpecr, halfTime, callback) {
			var settings = jpecr.settings;
			if (settings.debug) { $.ws.core.debugAlert($.ws.jpecr.alertTemplate, "INFO", "Running $.ws.jpecr.hideGrowler"); }
			var speed = settings.fadeSpeed;
			if (halfTime == true) {
				speed = (speed / 2);
			}
			jpecr.growlerDiv.stop(true).fadeOut(speed, function() {
				if (typeof callback !== 'undefined') {
					callback();
				}
			});
		}
	}

	if (typeof $.ws.jpecr.hidePopup === 'undefined') {
		$.ws.jpecr.hidePopup = function(jpecr) {
			var settings = jpecr.settings;
			if (settings.debug) { $.ws.core.debugAlert($.ws.jpecr.alertTemplate, "INFO", "Running $.ws.jpecr.hidePopup"); }
			jpecr.blackout.stop(true).fadeOut(settings.fadeSpeed, function() { jpecr.blackout.hide(); })
			jpecr.popup.stop(true).fadeOut(settings.fadeSpeed);
		}
	}

	if (typeof $.ws.jpecr.display === 'undefined') {
		$.ws.jpecr.display = function(jpecr, showCancel, halfTime) {
			var settings = jpecr.settings;
			if (settings.debug) { $.ws.core.debugAlert($.ws.jpecr.alertTemplate, "INFO", "Running $.ws.jpecr.display"); }
			clearTimeout($.ws.jpecr.growlTimer);
			if (typeof showCancel === 'undefined') {
				showCancel = false;
			}
			jpecr.popup.html(settings.popupMessage);
			var table = $('<table cellpadding="0" cellspacing="0" width="100%" />');
			table.append($('<thead>').append($('<tr>').append($('<th>Cookie</th>')).append($('<th>Description</th>')).append($('<th colspan="2" class="nowrap">Consent to Cookie?</th>'))));
			var tbody = $('<tbody />');
			table.append(tbody);
			var allConsentYes = ' checked';
			var allConsentNo =  ' checked';
			var allPermanent = ' checked';
			$.each(jpecr.data, function() {
				var tr = $('<tr />');
				tr.html('<td><input type="hidden" id="cookieName" value="' + this.name + '" class="cookieName" />' + this.title + '</td><td>' + this.description + '</td>');
				var consentYes = '';
				var consentNo = '';
				if (this.consent == true) {
					consentYes = ' checked';
					allConsentNo = '';
				} else if (this.consent == false) {
					consentNo = ' checked';
					allConsentYes = '';
				} else {
					allConsentNo = '';
					allConsentYes = '';
				}
				var permanent = '';
				if (this.permanent) {
					permanent = ' checked';
				} else {
					allPermanent = '';
				}
				var consenttd = $('<td class="nowrap" />').html(
					'<input type="radio" id="consent_' + this.name + '_yes" name="consent_' + this.name + '" class="consentYes"' + consentYes + ' />' +
					'<label for="consent_' + this.name + '_yes">Yes</label>' +
					'<input type="radio" id="consent_' + this.name + '_no" name="consent_' + this.name + '" class="consentNo"' + consentNo + ' />' +
					'<label for="consent_' + this.name + '_no">No</label>'
				);
				var permanenttd = $('<td class="nowrap" />').html(
					'<input type="checkbox" id="permanent_' + this.name + '" name="permanent_' + this.name + '" class="permanent"' + permanent + ' />' +
					'<label for="permanent_' + this.name + '">Store my choice permanently.</label>'
				);
				tr.append(consenttd).append(permanenttd);
				tbody.append(tr);
			});
			var tfoot = $('<tfoot />');
			var tfoottr = $('<tr />');
			tfoottr.html('<td colspan="2" style="text-align: right; font-weight: bold;">All Cookies</td>');
			var allconsenttd = $('<td class="nowrap" />').html(
				'<input type="radio" id="consent_all_yes" name="consent_all" class="consentYes"' + allConsentYes + ' />' +
				'<label for="consent_all_yes">Yes</label>' +
				'<input type="radio" id="consent_all_no" name="consent_all" class="consentNo"' + allConsentNo + ' />' +
				'<label for="consent_all_no">No</label>'
			);
			tfoottr.append(allconsenttd);
			var allpermanenttd = $('<td class="nowrap" />').html(
				'<input type="checkbox" id="permanent_all" name="permanent_all" class="permanent"' + allPermanent + ' />' +
				'<label for="permanent_all">Store my choice permanently.</label>'
			);
			tfoottr.append(allpermanenttd);
			tfoot.append(tfoottr);
			table.append(tfoot);
			table.wrap('<div />');
			jpecr.popup.append(table);
			table.find('tfoot input').bind('change', function () {
				var theInput = $(this);
				var cssClass = theInput.attr('class');
				var checked = theInput.is(':checked');
				var otherInputs = table.find('tbody input.' + cssClass)
				if (checked) {
					otherInputs.attr('checked', true);
				} else {
					otherInputs.removeAttr('checked');
				}
			});
			table.find('tbody input').bind('change', function () {
				var cssClass = $(this).attr('class');
				var theInputs = table.find('tbody input.' + cssClass);
				var allChecked = true;
				theInputs.each(function() {
					var checked = $(this).is(':checked');
					if (!checked) {
						allChecked = false;
					}
				});
				if (allChecked) {
					table.find('tfoot input.' + cssClass).attr('checked', true);
				} else {
					table.find('tfoot input.' + cssClass).attr('checked', false);
				}
			});
			var buttonDiv = $('<div class="jpecrPopupButtons" />');
			var errorDiv = $('<div id="jpecrError" />');
			errorDiv.hide();
			buttonDiv.append(errorDiv);
			buttonDiv.append($('<div />').html(settings.brand).css({position: 'absolute', top: 0, left: 0}));
			var saveButton = $('<a class="button" href="javascript:void(2);">Save Settings</a>');
			buttonDiv.append(saveButton);
			saveButton.click(function() {
				$.ws.jpecr.save(jpecr, $.ws.jpecr.hide);
			});
			var cancelButton;
			if (showCancel) {
				cancelButton = $('<a class="button" href="javascript:void(3);">Cancel</a>');
			} else {
				cancelButton = $('<a class="button" href="javascript:void(3);">Ask Me Later</a>');
			}
			cancelButton.click(function() {
				$.ws.jpecr.hideGrowler(jpecr);
				$.ws.jpecr.hidePopup(jpecr);
				if (settings.intrusion == 'growl' && settings.growlerRepeat) {
					$.ws.jpecr.growlTimer = setTimeout('$.ws.jpecr.growl($.ws.jpecr());', settings.growlerDelay);
				}
			});
			buttonDiv.append(cancelButton);
			jpecr.popup.append(buttonDiv);
			var speed = settings.fadeSpeed;
			if (halfTime == true) {
				speed = (speed / 2);
			}
			jpecr.blackout.show().fadeOut(0).fadeTo(speed, settings.popupBlackout, function() {
				jpecr.popup.fadeIn(speed);
			});
			var maxHeight = $(window).height();
			maxHeight -= 115;
			var contentHeight = 0;
			jpecr.popup.children().each(function() {
				contentHeight += $(this).outerHeight();
			});
			contentHeight += (jpecr.popup.outerHeight() - jpecr.popup.innerHeight());
			if (contentHeight > (maxHeight - 59)) {
				jpecr.popup.css('overflow-y', 'scroll');
			} else {
				jpecr.popup.css('overflow-y', 'visible');
			}
		};
	}

	if (typeof $.ws.jpecr.getData === 'undefined') {
		$.ws.jpecr.getData = function(jpecr) {
			var settings = jpecr.settings;
			if (settings.debug) { $.ws.core.debugAlert($.ws.jpecr.alertTemplate, "INFO", "Running $.ws.jpecr.outsideEU"); }

			$.ajax({
				type :		settings.requestType,
				url :		settings.requestPath,
				cache : 	false,
				dataType:	"json",
				success : 	function(data) {
								if (settings.debug) { $.ws.core.debugAlert($.ws.jpecr.alertTemplate, "INFO", "DATA RECIEVED:\n" + data); }
								jpecr.data = data;
							},
				error :		function(jqXHR, textStatus, errorThrown) {
								if (settings.debug) { $.ws.core.debugAlert($.ws.jpecr.alertTemplate, "ERROR" + errorThrown); }
								console.log($.ws.jpecr.alertTemplate, "ERROR" + errorThrown);
							}
			});
		};
	}

	if (typeof $.ws.jpecr.save === 'undefined') {
		$.ws.jpecr.save = function(jpecr) {
			var settings = jpecr.settings;
			if (settings.debug) { $.ws.core.debugAlert($.ws.jpecr.alertTemplate, "INFO", "Running $.ws.jpecr.display"); }
			var incomplete = 0;
			var dataOut = new Array();
			jpecr.popup.find('tbody tr').each(function() {
				var tr = $(this);
				var cookieName = tr.find('.cookieName').val();
				var consentYes = tr.find('.consentYes').is(':checked');
				var consentNo = tr.find('.consentNo').is(':checked');
				var permanent = tr.find('.permanent').is(':checked');
				if (!consentYes && !consentNo) {
					incomplete++;
				} else {
					var dataRow = {name: cookieName, consent: consentYes, permanent: permanent};
					dataOut.push(dataRow);
				}
			});
			if (incomplete > 0) {
				$('#jpecrError').html("You must select either 'Yes' or 'No' for each cookie.").fadeIn(settings.fadeSpeed);
			} else {
				$.ajax({
					type :		settings.responseType,
					url :		settings.responsePath,
					cache : 	false,
					data :		"cookiedata=" + $.ws.core.json.encodeArray(dataOut),
					success : 	function(data) {
									$.ws.core.reload();
								},
					error :		function(jqXHR, textStatus, errorThrown) {
									if (settings.debug) { $.ws.core.debugAlert($.ws.jpecr.alertTemplate, "ERROR" + errorThrown); }
								}
				});
				$.ws.jpecr.hidePopup(jpecr);
			}
		};
	}

	if (typeof $.ws.jpecr.version === 'undefined') {
		$.ws.jpecr.version = '2.0.0';
	}

	if (typeof $.ws.jpecr.defaults === 'undefined') {
		$.ws.jpecr.defaults = {
			skin:					'default',
			type:					'bar',
			intrusion:				'growl', 							// none, growl, popup
			delayTime:				1500,
			fadeSpeed:				400,
			autoReload:				true,
			growlerIcon:			'includes/cookie_law/exclamation.png',
			growlerMessage:			'This website uses cookies for certain features, please specify your cookie settings.',
			growlerRepeat: 			true,
			growlerDelay: 			60000,								// every minute
			growlerType:			'bar',								// bar, box, custom
			keepAlive:				null,
			popupIcon:				'includes/cookie_law/exclamation.png',
			popupMessage:			'<h1>Cookies</h1><p>Cookies are used across most websites on the internet for a variety of purposes; from remembering who you are, to tracking which pages are visited by users. They are very restricted in both their content and their use. If cookies are disabled, you may find that features of this website will not work.</p><p>For more information about cookies, <a href="'+ 'index.php?main_page=cookie_usage&a' + '">click here</a>.</p><p>If you consent to a cookie we will use a session cookie to remember your preference for this browsing session. We will use a persistent cookie to remember your preference permanently if you choose to do so.</p><p><strong>Please make your selections below to tell us which cookies we are allowed to place on your computer.</strong></p>',
			popupType:				'dropdown', 						// dropdown, modal
			popupBlackout:			0.5,								// number between 0 and 1
			displayButtonSelector:	null,
			requestPath:			'includes/cookie_law/php/pecr.php',
			requestType:			'GET',
			responsePath:			'includes/cookie_law/php/pecr.php',
			responseType:			'POST',
			brand:					'When you save your settings, the page will reload.'
		};
	}

	if (typeof $.ws.jpecr.alertTemplate === 'undefined') {
		$.ws.jpecr.alertTemplate = "Wolf Software JPECR Alert: {LEVEL}\n\n{MESSAGE}\n\nTo turn off debugging, either set to false or remove the debug option from your settings.";
	}


})(jQuery);

function readCookie(name) {
	var nameEQ = name + "=";
	var ca = document.cookie.split(';');
	for(var i=0;i < ca.length;i++) {
		var c = ca[i];
		while (c.charAt(0)==' ') c = c.substring(1,c.length);
		if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
	}
	return null;
}