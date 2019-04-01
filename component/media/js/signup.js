/*
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

/**
 * Setup (required for Joomla! 3)
 */
if (typeof(akeeba) === "undefined")
{
	var akeeba = {};
}

if (typeof(akeeba.jQuery) === "undefined")
{
	akeeba.jQuery = window.jQuery.noConflict();
}

var akeebasubs_eu_configuration = {
	"BE": ["Belgium", "BE", 21],
	"BG": ["Bulgaria", "BG", 20],
	"CZ": ["Czech Rebulic", "CZ", 21],
	"DK": ["Denmark", "DK", 25],
	"DE": ["Germany", "DE", 19],
	"EE": ["Estonia", "EE", 20],
	"GR": ["Greece", "EL", 24],
	"ES": ["Spain", "ES", 21],
	"FR": ["France", "FR", 20],
	"HR": ["Croatia", "HR", 25],
	"IE": ["Ireland", "IE", 23],
	"IT": ["Italy", "IT", 22],
	"CY": ["Cyprus", "CY", 19],
	"LV": ["Latvia", "LV", 21],
	"LT": ["Lithuania", "LT", 21],
	"LU": ["Luxembourg", "LU", 17],
	"HU": ["Hungary", "HU", 27],
	"MT": ["Malta", "MT", 18],
	"NL": ["Netherlands", "NL", 21],
	"AT": ["Austria", "AT", 20],
	"PL": ["Poland", "PL", 23],
	"PT": ["Portugal", "PT", 23],
	"RO": ["Romania", "RO", 19],
	"SI": ["Slovenia", "SI", 22],
	"SK": ["Slovakia", "SK", 20],
	"FI": ["Finland", "FI", 24],
	"SE": ["Sweden", "SE", 25],
	"GB": ["United Kingdom", "GB", 20],
	"MC": ["Monaco", "FR", 20],
	"IM": ["Isle of Man", "GB", 20]
};

var akeebasubs_business_state               = "";
var akeebasubs_isbusiness                   = false;
var akeebasubs_blocked_gui                  = false;
var akeebasubs_run_validation_after_unblock = false;
var akeebasubs_cached_response              = false;
var akeebasubs_valid_form                   = true;
var akeebasubs_validation_fetch_queue       = [];
var akeebasubs_validation_queue             = [];
var akeebasubs_sub_validation_fetch_queue   = [];
var akeebasubs_sub_validation_queue         = [];
var akeebasubs_level_id                     = 0;
var akeebasubs_submit_after_validation      = false;
var akeebasubs_noneuvat                     = false;
var akeebasubs_apply_validation             = false;
var akeebasubs_form_specifier               = "signupForm";

(function ($)
{
	$.fn.removePartial = function (baseClass, removeThis)
	{
		var myElement = this;

        if (this.length !== undefined)
        {
            if (this.length === 0)
            {
                return;
            }

            myElement = $(this[0]);
        }

		var myClass   = myElement.attr('class');
        var classList = [];

		if (typeof myClass !== "undefined")
		{
			classList = myClass.split(/\s+/);
		}

		$.each(classList, function (index, wholeClass)
		{
			// Wrong base class or already bare?
			if (!wholeClass.startsWith(baseClass + "--"))
			{
				return;
			}

			// Degenerate case "baseClass--removeThis", we just need to return baseClass
			if (wholeClass === (baseClass + "--" + removeThis))
			{
				myElement.removeClass(wholeClass).addClass(baseClass);

				return;
			}

			var partials = wholeClass.split("--");
			partials.splice(0, 1);

			var newClass = baseClass;
			myElement.removeClass(wholeClass);

			for (var i = 0; i < partials.length; i++)
			{
				if (partials[i] === removeThis)
				{
					continue;
				}

				newClass += "--" + partials[i];
			}

			myElement.addClass(newClass);
		});

		return this;
	};

	$.fn.addPartial = function (baseClass, addThis)
	{
		var myElement = this;

		if (this.length !== undefined)
        {
            if (this.length === 0)
            {
                return;
            }

            myElement = $(this[0]);
        }

		var classList = myElement.attr("class").split(/\s+/);

        $.each(classList, function (index, wholeClass)
		{
			// Wrong base class?
			if (!wholeClass.startsWith(baseClass))
			{
				return;
			}

			// Class already "baseClass--addThis"?
			if (wholeClass === (baseClass + "--" + addThis))
			{
				return;
			}

			// Class already bare? Just add the partial.
			if (wholeClass === baseClass)
			{
				myElement.removeClass(wholeClass);
				myElement.addClass(baseClass + "--" + addThis);
				return;
			}

			// Double check we are not matching the wrong class, e.g. whileClass = 'foobar' and baseClass='foo'
			if (!wholeClass.startsWith(baseClass + "--"))
			{
				return;
			}

			// Already includes '--' + addThis? Return.
			if (wholeClass.indexOf("--" + addThis) !== -1)
			{
				return;
			}

			// Add the partial
			myElement.removeClass(wholeClass);
			myElement.addClass(wholeClass + "--" + addThis);
		});

		return this;
	};

})(akeeba.jQuery);


function cacheSubmitAction (e)
{
	(function ($)
	{
		e.preventDefault();
		akeebasubs_submit_after_validation = true;
		$("#subscribenow").attr("disabled", "disabled");
	})(akeeba.jQuery);
}

function blockInterface ()
{
	(function ($)
	{
		var btnSubscribeNow = $("#subscribenow");
		btnSubscribeNow.click(cacheSubmitAction);
		btnSubscribeNow.attr("disabled", "disabled");
		akeebasubs_blocked_gui = true;
	})(akeeba.jQuery);
}

function enableInterface ()
{
	(function ($)
	{
		var btnSubscribeNow = $("#subscribenow");

		btnSubscribeNow.unbind("click");
		btnSubscribeNow.removeAttr("disabled");

		akeebasubs_blocked_gui = false;

		if (akeebasubs_run_validation_after_unblock)
		{
			akeebasubs_run_validation_after_unblock = false;

			validateBusiness();

			return;
		}

		if (akeebasubs_submit_after_validation)
		{
			akeebasubs_submit_after_validation = false;
			setTimeout("(function($) {$('#subscribenow').click()})(akeeba.jQuery);", 100);
		}
	})(akeeba.jQuery);
}

/**
 * Runs a form validation with the server and returns validation and, optionally,
 * price analysis information. If a callback_function is specified, it will be called
 * if the form is valid.
 * @param callback_function
 * @return
 */
function validateForm (callback_function)
{
	if (akeebasubs_blocked_gui)
	{
		akeebasubs_run_validation_after_unblock = true;
		return;
	}

	(function ($)
	{
		var paymentMethod = null;

		paymentMethod = $("input[name=paymentmethod]:checked").val();

		if (paymentMethod == null)
		{
			paymentMethod = $("select[name=paymentmethod]").val();
		}

		var $couponField = $("#coupon");
		var couponValue  = ($couponField.length > 0) ? $couponField.val() : "";

		var data = {
			"action"       : "read",
			"id"           : akeebasubs_level_id,
			"username"     : $("#username").val(),
			"name"         : $("#name").val(),
			"email"        : $("#email").val(),
			"email2"       : $("#email2").val(),
			"address1"     : $("#address1").val(),
			"address2"     : $("#address2").val(),
			"country"      : $("#" + akeebasubs_form_specifier + " select[name$=\"country\"]").val(),
			"city"         : $("#city").val(),
			"zip"          : $("#zip").val(),
			"isbusiness"   : $("#isbusiness").val(),
			"businessname" : $("#businessname").val(),
			"occupation"   : $("#occupation").val(),
			"vatnumber"    : $("#vatnumber").val(),
			"coupon"       : couponValue,
			"paymentmethod": paymentMethod,
			"custom"       : {},
			"subcustom"    : {}
		};

		var elPassword = $("#password");

		if (elPassword)
		{
			data.password  = elPassword.val();
			data.password2 = $("#password2").val();
		}

		// Fetch the custom fields
		$.each(akeebasubs_validation_fetch_queue, function (index, function_name)
		{
			var result = function_name();

			if ((result !== null) && (typeof result === "object"))
			{
				// Merge the result with the data object
				$.extend(data.custom, result);
			}
		});

		// Fetch the per-subscription custom fields
		$.each(akeebasubs_sub_validation_fetch_queue, function (index, function_name)
		{
			var result = function_name();
			if ((result !== null) && (typeof result === "object"))
			{
				// Merge the result with the data object
				$.extend(data.subcustom, result);
			}
		});

		blockInterface();

		$.ajax({
				   type    : "POST",
				   url     : akeebasubs_validate_url + "?option=com_akeebasubs&view=Validate&format=json",
				   data    : data,
				   dataType: "json",
				   success : function (msg, textStatus, xhr)
				   {
					   if (msg.validation)
					   {
						   msg.validation.custom_validation    = msg.custom_validation;
						   msg.validation.custom_valid         = msg.custom_valid;
						   msg.validation.subcustom_validation = msg.subscription_custom_validation;
						   msg.validation.subcustom_valid      = msg.subscription_custom_valid;
						   applyValidation(msg.validation, callback_function);
					   }
					   if (msg.price)
					   {
						   applyPrice(msg.price);
					   }
					   enableInterface();
				   },
				   error   : function (jqXHR, textStatus, errorThrown)
				   {
					   enableInterface();
				   }
			   });

		// Fetch list of payment methods
		if (akeebasubs_form_specifier == "signupForm")
		{
			$.ajax({
					   type    : "POST",
					   url     : akeebasubs_validate_url + "?option=com_akeebasubs&view=Validate&task=getpayment&format=json",
					   data    : data,
					   dataType: "text",
					   success : function (result)
					   {
						   var html = /###(\{.*?\})###/.exec(result);

						   if (html && html[1] !== "undefined" && html[1].html !== "undefined")
						   {
							   // Before building the new payment list, let's save the select method, so I can select
							   // it again
							   var cur_method = $("input[name=\"paymentmethod\"]:checked").val();
							   $("#paymentlist-container").html(JSON.parse(html[1]).html);
							   $("input[name=\"paymentmethod\"][value=\"" + cur_method + "\"]").prop("checked", true);
						   }

						   enableInterface();
					   },
					   error   : function ()
					   {
						   enableInterface();
					   }
				   });
		}

	})(akeeba.jQuery);
}

/**
 * Validates the password fields
 * @return
 */
function validatePassword ()
{
	(function ($)
	{
		var elPassword  = $("#password");
		var elPassword2 = $("#password2");

		if (!elPassword)
		{
			return;
		}
		var password  = elPassword.val();
		var password2 = elPassword2.val();

		var elPasswordInvalid = $("#password_invalid");
		var elPasswordValid   = $("#password2_invalid");

		elPasswordInvalid.hide();
		elPasswordValid.hide();

		if (!akeebasubs_apply_validation)
		{
			if ((password === "") && (password2 === ""))
			{
				$("#password").parents("div[class*=akeeba-form-group]").removePartial("akeeba-form-group", "error");
				$("#password2").parents("div[class*=akeeba-form-group]").removePartial("akeeba-form-group", "error");

				return;
			}
		}

		if (password === "")
		{
			$("#password").parents("div[class*=akeeba-form-group]").addPartial("akeeba-form-group", "error");
			$("#password2").parents("div[class*=akeeba-form-group]").removePartial("akeeba-form-group", "error");
			elPasswordInvalid.show();
			akeebasubs_valid_form = false;
		}
		else
		{
			$("#password").parents("div[class*=akeeba-form-group]").removePartial("akeeba-form-group", "error");

			if (password2 !== password)
			{
				$("#password2").parents("div[class*=akeeba-form-group]").addPartial("akeeba-form-group", "error");
				elPasswordValid.show();
				akeebasubs_valid_form = false;
			}
			else
			{
				$("#password2").parents("div[class*=akeeba-form-group]").removePartial("akeeba-form-group", "error");
			}
		}
	})(akeeba.jQuery);
}

/**
 * Validates the (real) name
 * @return
 */
function validateName ()
{
	(function ($)
	{
		var elNameEmpty = $("#name_empty");
		var elName      = $("#name");
		var name        = elName.val();

		elNameEmpty.hide();
		elName.parents("div[class*=akeeba-form-group]").removePartial("akeeba-form-group", "error");

		if (!akeebasubs_apply_validation)
		{
			return;
		}

		var invalidName = false;

		if (name === "")
		{
			invalidName = true;
		}
		/**
		 else {
			name = ltrim(rtrim(name, " "), " ");
			var nameParts = name.split(' ');
			if(nameParts.length < 2) invalidName = true;
		}
		 **/

		if (invalidName)
		{
			elName.parents("div[class*=akeeba-form-group]").addPartial("akeeba-form-group", "error");
			elNameEmpty.show();
			akeebasubs_valid_form = false;

			return;
		}

		elName.parents("div[class*=akeeba-form-group]").removePartial("akeeba-form-group", "error");
	})(akeeba.jQuery);
}

/**
 * DHTML email validation script. Courtesy of SmartWebby.com (http://www.smartwebby.com/dhtml/)
 */

function echeck (str)
{
	var at   = "@";
	var dot  = ".";
	var lat  = str.indexOf(at);
	var lstr = str.length;
	var ldot = str.indexOf(dot);
	if (str.indexOf(at) == -1)
	{
		return false;
	}

	if (str.indexOf(at) == -1 || str.indexOf(at) == 0 || str.indexOf(at) == lstr)
	{
		return false;
	}

	if (str.indexOf(dot) == -1 || str.indexOf(dot) == 0 || str.indexOf(dot) == lstr)
	{
		return false;
	}

	if (str.indexOf(at, (lat + 1)) != -1)
	{
		return false;
	}

	if (str.substring(lat - 1, lat) == dot || str.substring(lat + 1, lat + 2) == dot)
	{
		return false;
	}

	if (str.indexOf(dot, (lat + 2)) == -1)
	{
		return false;
	}

	if (str.indexOf(" ") != -1)
	{
		return false;
	}

	return true;
}

/**
 * Validates the email address
 * @return
 */
function validateEmail ()
{
	(function ($)
	{
		var elEmailEmpty    = $("#email_empty");
		var elEmailInvalid  = $("#email_invalid");
		var elEmail2Invalid = $("#email2_invalid");
		var elEmail         = $("#email");
		var elEmail2        = $("#email2");

		elEmailEmpty.hide();
		elEmailInvalid.hide();
		elEmail2Invalid.hide();

		elEmail.parents("div[class*=akeeba-form-group]").removePartial("akeeba-form-group", "error");
		elEmail2.parents("div[class*=akeeba-form-group]").removePartial("akeeba-form-group", "error");

		var email  = elEmail.val();
		var email2 = elEmail2.val();

		if (!akeebasubs_apply_validation)
		{
			return;
		}

		if ((email === "") && (email2 === ""))
		{
			elEmail.parents("div[class*=akeeba-form-group]").removePartial("akeeba-form-group", "error");
			elEmail2.parents("div[class*=akeeba-form-group]").removePartial("akeeba-form-group", "error");

			elEmailEmpty.hide();
			elEmailInvalid.hide();
			elEmail2Invalid.hide();

			return;
		}

		if (email === "")
		{
			elEmail.parents("div[class*=akeeba-form-group]").addPartial("akeeba-form-group", "error");
			elEmailEmpty.show();
			akeebasubs_valid_form = false;

			return;
		}

		if (!echeck(email))
		{
			elEmail.parents("div[class*=akeeba-form-group]").addPartial("akeeba-form-group", "error");
			elEmailInvalid.show();
			akeebasubs_valid_form = false;

			return;
		}

		validateForm();
	})(akeeba.jQuery);
}

function validateAddress ()
{
	(function ($)
	{
		var elAddress1       = $("#address1");
		var elCity           = $("#city");
		var elZip            = $("#zip");
		var elCountry        = $("#country");
		var elAdddress1Empty = $("#address1_empty");
		var elCountryEmpty   = $("#country_empty");
		var elCityEmpty      = $("#city_empty");
		var elZipEmpty       = $("#zip_empty");

		var address = elAddress1.val();
		var country = elCountry.val();
		var city    = elCity.val();
		var zip     = elZip.val();

		var hasErrors = false;

		if (!akeebasubs_apply_validation)
		{
			elAddress1.parents("div[class*=akeeba-form-group]").removePartial("akeeba-form-group", "error");
			elCountry.parents("div[class*=akeeba-form-group]").removePartial("akeeba-form-group", "error");
			elCity.parents("div[class*=akeeba-form-group]").removePartial("akeeba-form-group", "error");
			elZip.parents("div[class*=akeeba-form-group]").removePartial("akeeba-form-group", "error");

			elAdddress1Empty.hide();
			elCountryEmpty.hide();
			elCityEmpty.hide();
			elZipEmpty.hide();

			return;
		}


		elAddress1.parents("div[class*=akeeba-form-group]").removePartial("akeeba-form-group", "error");
		elAdddress1Empty.hide();

		if (address === "")
		{
			elAddress1.parents("div[class*=akeeba-form-group]").addPartial("akeeba-form-group", "error");
			elAdddress1Empty.show();
			hasErrors = true;
		}

		elCountry.parents("div[class*=akeeba-form-group]").removePartial("akeeba-form-group", "error");

		if (country === "")
		{
			elCountry.parents("div[class*=akeeba-form-group]").addPartial("akeeba-form-group", "error");
			elCountryEmpty.show();
			hasErrors = true;
		}
		else
		{
			elCountryEmpty.hide();

			// If that's an EU country, show and update the VAT field
			var elVatFields  = $("#vatfields");
			var elVatCountry = $("#vatcountry");

			if (elVatFields)
			{
				elVatFields.hide();

				if (akeebasubs_noneuvat)
				{
					elVatFields.show();
					elVatCountry.text("");
				}

				Object.keys(akeebasubs_eu_configuration)
					  .forEach(function (key)
							   {
								   if (key === country)
								   {
									   $("#vatfields").show();

									   var ccode = akeebasubs_eu_configuration[key][1];
									   $("#vatcountry").text(ccode);

								   }
							   });
			}
		}

		elCity.parents("div[class*=akeeba-form-group]").removePartial("akeeba-form-group", "error");
		elCityEmpty.hide();

		if (city === "")
		{
			elCity.parents("div[class*=akeeba-form-group]").addPartial("akeeba-form-group", "error");
			elCityEmpty.show();
			hasErrors = true;
		}

		elZip.parents("div[class*=akeeba-form-group]").removePartial("akeeba-form-group", "error");
		elZipEmpty.hide();

		if (zip === "")
		{
			elZip.parents("div[class*=akeeba-form-group]").addPartial("akeeba-form-group", "error");
			elZipEmpty.show();
			hasErrors = true;
		}

		if (hasErrors)
		{
			akeebasubs_valid_form = false;

			return;
		}

	})(akeeba.jQuery);
}

/**
 * Validates the business registration information and runs a price fetch
 * @return
 */
function validateBusiness ()
{
	(function ($)
	{
		// Do I have to show the business fields?
		var elIsBusiness = $("#isbusiness");

		if (elIsBusiness.val() === "1")
		{
			$("#businessfields").show();
		}
		else
		{
			$("#businessfields").hide();
			// If it's not a business validation, chain an address validation
			if (akeebasubs_blocked_gui)
			{
				akeebasubs_run_validation_after_unblock = true;

				return;
			}

			akeebasubs_valid_form = true;
			validateForm();
			return;
		}

		// Do I have to show VAT fields?
		var elCountry   = $("#" + akeebasubs_form_specifier + " select[name$=\"country\"]");
		var elVatFields = $("#vatfields");
		var country     = elCountry.val();

		elVatFields.hide();

		if (akeebasubs_noneuvat)
		{
			elVatFields.css("display", "grid");
			$("#vatcountry").text("");
		}

		Object.keys(akeebasubs_eu_configuration)
			  .forEach(function (key)
					   {
						   if (key === country)
						   {
							   $("#vatfields").css("display", "grid");

							   var ccode = akeebasubs_eu_configuration[key][1];
							   $("#vatcountry").text(ccode);

						   }
					   });

		// Make sure we don't do business validation / price check unless something's changed
		var elVatNumber = $("#vatnumber");
		var vatnumber   = "";

		if (elVatNumber)
		{
			vatnumber = elVatNumber.val();
		}

		var elCoupon = $("#coupon");

		var data = {
			country     : elCountry.val(),
			city        : $("#city").val(),
			zip         : $("#zip").val(),
			isbusiness  : elIsBusiness.val(),
			businessname: $("#businessname").val(),
			occupation  : $("#occupation").val(),
			vatnumber   : vatnumber,
			coupon      : (elCoupon.length > 0) ? elCoupon.val() : ""
		};

		var hash = "";
		for (key in data)
		{
			hash += "|" + key + "|" + data[key];
		}
		hash += "|";

		if (akeebasubs_business_state === hash)
		{
			if (akeebasubs_isbusiness)
			{
				return;
			}

			akeebasubs_isbusiness = true;
		}

		akeebasubs_business_state = hash;

		validateForm();
	})(akeeba.jQuery);
}

function validateIsNotBusiness (e)
{
	(function ($)
	{
		$("#businessfields").hide();

		akeebasubs_cached_response.businessname = true;
		akeebasubs_cached_response.novatrequired = true;

		applyValidation(akeebasubs_cached_response);
		akeebasubs_isbusiness = false;
	})(akeeba.jQuery);
}

function onIsBusinessClick (e)
{
	(function ($)
	{
		var isBusiness = $("#isbusiness").val() == 1;

		if (isBusiness)
		{
			validateBusiness();

			return;
		}

		validateIsNotBusiness();
	})(akeeba.jQuery);
}

function applyValidation (response, callback)
{
	akeebasubs_cached_response = response;

	(function ($)
	{
		akeebasubs_valid_form = true;
		var elBusinessName    = $("#businessname");
		var elOccupation      = $("#occupation");
		if (akeebasubs_apply_validation)
		{
			var elUsername        = $("#username");
			var elUsernameInvalid = $("#username_invalid");

			elUsername.parents("div[class*=akeeba-form-group]").removePartial("akeeba-form-group", "error");

			if (elUsernameInvalid)
			{
				if (response.username)
				{
					elUsernameInvalid.hide();
				}
				else
				{
					if (!elUsername.attr("disabled") || (elUsername.attr("disabled") !== "disabled"))
					{
						akeebasubs_valid_form = false;
					}

					elUsernameInvalid.hide();

					if (true || (elUsername.val() !== ""))
					{
						elUsername.parents("div[class*=akeeba-form-group]").addPartial("akeeba-form-group", "error");
						elUsernameInvalid.show();
					}
				}
			}

			var elName = $("#name");
			elName.parents("div[class*=akeeba-form-group]").removePartial("akeeba-form-group", "error");
			if (response.name)
			{
				$("#name_empty").hide();
			}
			else
			{
				elName.parents("div[class*=akeeba-form-group]").addPartial("akeeba-form-group", "error");
				akeebasubs_valid_form = false;
				$("#name_empty").show();
			}

			var elEmail = $("#email");
			elEmail.parents("div[class*=akeeba-form-group]").removePartial("akeeba-form-group", "error");
			if (response.email)
			{
				$("#email_invalid").hide();
			}
			else
			{
				elEmail.parents("div[class*=akeeba-form-group]").addPartial("akeeba-form-group", "error");
				akeebasubs_valid_form = false;
				$("#email_invalid").show();
			}

			var elEmail2 = $("#email2");
			elEmail2.parents("div[class*=akeeba-form-group]").removePartial("akeeba-form-group", "error");
			if (response.email2)
			{
				$("#email2_invalid").hide();
			}
			else
			{
				elEmail2.parents("div[class*=akeeba-form-group]").addPartial("akeeba-form-group", "error");
				akeebasubs_valid_form = false;
				$("#email2_invalid").show();
			}

			var elAddress1 = $("#address1");
			elAddress1.parents("div[class*=akeeba-form-group]").removePartial("akeeba-form-group", "error");
			if (response.address1)
			{
				$("#address1_empty").hide();
			}
			else
			{
				elAddress1.parents("div[class*=akeeba-form-group]").addPartial("akeeba-form-group", "error");
				akeebasubs_valid_form = false;
				$("#address1_empty").show();
			}

			var elCountry = $("#country");
			elCountry.parents("div[class*=akeeba-form-group]").removePartial("akeeba-form-group", "error");

			if (response.country)
			{
				$("#country_empty").hide();
			}
			else
			{
				akeebasubs_valid_form = false;
				elCountry.parents("div[class*=akeeba-form-group]").addPartial("akeeba-form-group", "error");
				$("#country_empty").show();
			}

			var elCity = $("#city");
			elCity.parents("div[class*=akeeba-form-group]").removePartial("akeeba-form-group", "error");

			if (response.city)
			{
				$("#city_empty").hide();
			}
			else
			{
				elCity.parents("div[class*=akeeba-form-group]").addPartial("akeeba-form-group", "error");
				akeebasubs_valid_form = false;
				$("#city_empty").show();
			}

			var elZip = $("#zip");
			elZip.parents("div[class*=akeeba-form-group]").removePartial("akeeba-form-group", "error");

			if (response.zip)
			{
				$("#zip_empty").hide();
			}
			else
			{
				elZip.parents("div[class*=akeeba-form-group]").addPartial("akeeba-form-group", "error");
				akeebasubs_valid_form = false;
				$("#zip_empty").show();
			}

			elBusinessName.parents("div[class*=akeeba-form-group]").removePartial("akeeba-form-group", "error");
			var elIsBusiness = $("#isbusiness");
			if (response.businessname)
			{
				$("#businessname_empty").hide();
			}
			else
			{
				elBusinessName.parents("div[class*=akeeba-form-group]").addPartial("akeeba-form-group", "error");
				if (elIsBusiness.val() == 1)
				{
					akeebasubs_valid_form = false;
				}
				$("#businessname_empty").show();
			}

			elOccupation.parents("div[class*=akeeba-form-group]").removePartial("akeeba-form-group", "error");
			if (elOccupation.val())
			{
				$("#occupation_empty").hide();
			}
			else
			{
				elOccupation.parents("div[class*=akeeba-form-group]").addPartial("akeeba-form-group", "error");

				if (elIsBusiness.val() == 1)
				{
					akeebasubs_valid_form = false;
				}

				$("#occupation_empty").show();
			}
		}
		else
		{
			// Apply validation is false
			elBusinessName.parents("div[class*=akeeba-form-group]").removePartial("akeeba-form-group", "error");
			elOccupation.parents("div[class*=akeeba-form-group]").removePartial("akeeba-form-group", "error");
			$("#businessname_empty").hide();
			$("#occupation_empty").hide();
		}

		var elVatNumber = $("#vatnumber");
		elVatNumber.parents("div[class*=akeeba-form-group]").removePartial("akeeba-form-group", "warning");
		if (response.vatnumber)
		{
			$("#vat-status-invalid").hide();
		}
		else
		{
			elVatNumber.parents("div[class*=akeeba-form-group]").addPartial("akeeba-form-group", "warning");
			$("#vat-status-invalid").show();
		}

		if (response.novatrequired)
		{
			elVatNumber.parents("div[class*=akeeba-form-group]").removePartial("akeeba-form-group", "warning");
			$("#vat-status-invalid").hide();
		}

		// Finally, apply the custom validation
		$.each(akeebasubs_validation_queue, function (index, function_name)
		{
			var isValid           = function_name(response);
			akeebasubs_valid_form = akeebasubs_valid_form & isValid;
		});
		$.each(akeebasubs_sub_validation_queue, function (index, function_name)
		{
			var isValid           = function_name(response);
			akeebasubs_valid_form = akeebasubs_valid_form & isValid;
		});

		if (!akeebasubs_apply_validation)
		{
			akeebasubs_valid_form = true;
		}

		if (akeebasubs_apply_validation)
		{
			if (akeebasubs_valid_form)
			{
				$("#subscribenow")
				.addPartial("akeeba-btn", "success")
				.removePartial("akeeba-btn", "warning")
				.removePartial("akeeba-btn", "teal");
			}
			else
			{
				$("#subscribenow")
				.addPartial("akeeba-btn", "warning")
				.removePartial("akeeba-btn", "success")
				.removePartial("akeeba-btn", "teal");
			}
		}

	})(akeeba.jQuery);
}

function applyPrice (response)
{
	(function ($)
	{
		var $sumTotalField = $("#akeebasubs-sum-total");

		if ($sumTotalField.length > 0)
		{
			var vatContainer = $("#akeebasubs-vat-container");
			vatContainer.hide();

			$sumTotalField.text(response.gross);
			$("#akeebasubs-sum-vat-percent").html(response.taxrate);

			if (response.taxrate > 0)
			{
				vatContainer.show();
			}

			var $discountFieldContainer = $("#akeebasubs-sum-discount-container");
			var $originalFieldContainer = $("#akeebasubs-sum-original-container");

			if ($discountFieldContainer.length)
			{
				$discountFieldContainer.hide();

				if ($originalFieldContainer.length)
				{
					$originalFieldContainer.hide();
				}

				var $discountField = $("#akeebasubs-sum-discount");
				var $originalField = $("#akeebasubs-sum-original");

				if ((response.discount > 0) && $discountField.length)
				{
					$discountFieldContainer.show();

					if ($originalFieldContainer.length)
					{
						$originalFieldContainer.show();
					}

					var discountWithVAT = response.discount * (1.00 + (response.taxrate / 100.00));
					$discountField.html(discountWithVAT.toFixed(2));

					if ($originalField.length)
					{
						var originalWithVAT = response.net * (1.00 + (response.taxrate / 100.00));
						$originalField.html(originalWithVAT.toFixed(2));
					}
				}
			}

			if (response.gross * 1 <= 0)
			{
				$("#paymentmethod-container").css("display", "none");
			}
			else
			{
				$("#paymentmethod-container").css("display", "inherit");
			}
		}

		var $couponField = $("#coupon");

		if ($couponField.length)
		{
			var couponValue = ($couponField.length > 0) ? $couponField.val() : "";
			$couponField.removeClass("coupon-valid coupon-invalid");

			if (couponValue)
			{
				var couponClass = (response.couponid > 0) ? "coupon-valid" : "coupon-invalid";
				$couponField.addClass(couponClass);
			}
		}

	})(akeeba.jQuery);
}

/**
 * Adds a function to the validation fetch queue
 */
function addToValidationFetchQueue (myfunction)
{
	if (typeof myfunction != "function")
	{
		return false;
	}
	akeebasubs_validation_fetch_queue.push(myfunction);
}

/**
 * Adds a function to the validation queue
 */
function addToValidationQueue (myfunction)
{
	if (typeof myfunction != "function")
	{
		return false;
	}
	akeebasubs_validation_queue.push(myfunction);
}

/**
 * Adds a function to the per-subscription validation fetch queue
 */
function addToSubValidationFetchQueue (myfunction)
{
	if (typeof myfunction != "function")
	{
		return false;
	}
	akeebasubs_sub_validation_fetch_queue.push(myfunction);
}

/**
 * Adds a function to the per-subscription validation queue
 */
function addToSubValidationQueue (myfunction)
{
	if (typeof myfunction != "function")
	{
		return false;
	}
	akeebasubs_sub_validation_queue.push(myfunction);
}


(function ($)
{
	$(document).ready(function ()
					  {
						  if (jQuery("#userinfoForm").length)
						  {
							  akeebasubs_form_specifier = "userinfoForm";
						  }

						  $("#username").blur(validateForm);
						  if ($("#password"))
						  {
							  $("#password").blur(validatePassword);
							  $("#password2").blur(validatePassword);
						  }
						  $("#name").blur(validateName);
						  $("#email").blur(validateEmail);
						  $("#email2").blur(validateEmail);
						  $("#address1").blur(validateAddress);
						  $("#city").blur(validateBusiness);
						  $("#zip").blur(validateBusiness);
						  $("#businessname").blur(validateBusiness);
						  $("#vatnumber").blur(validateBusiness);

						  $("#" + akeebasubs_form_specifier + " select[name$=\"country\"]").change(validateBusiness);
						  $("#" + akeebasubs_form_specifier + " select[name$=\"isbusiness\"]").change(
							  onIsBusinessClick);

						  if ($("#coupon").length > 0)
						  {
							  $("#coupon").blur(validateBusiness);
						  }
						  // Attach onBlur events to custom fields
						  var elFormElements = $("#" + akeebasubs_form_specifier + " *[name]");
						  elFormElements
						  .filter(function (index)
								  {
									  if ($(this).is("input"))
									  {
										  return $(this)
										  .attr("name")
										  .substr(0, 7) === "custom[";
									  }
									  return false;
								  }).blur(validateForm);
						  elFormElements
						  .filter(function (index)
								  {
									  if ($(this).is("input"))
									  {
										  return $(this)
										  .attr("name")
										  .substr(0, 10) === "subcustom[";
									  }
									  return false;
								  }).blur(validateForm);
						  // Attach onChange events to custom checkboxes
						  elFormElements
						  .filter(function (index)
								  {
									  if ($(this).attr("type") === "checkbox")
									  {
										  return true;
									  }
									  if ($(this).attr("type") === "radio")
									  {
										  return true;
									  }
									  if ($(this).is("select"))
									  {
										  return true;
									  }
									  return false;
								  }).change(validateForm);

						  setTimeout("onIsBusinessClick();", 1500);

						  // Disable form submit when ENTER is hit in the coupon field
						  $("input#coupon")
						  .keypress(function (e)
									{
										if (e.which == 13)
										{
											validateForm();
											return false;
										}
									});

						  validateEmail();
						  validateForm();
					  });
})(akeeba.jQuery);

/**
 (function($) {
})(akeeba.jQuery);
 /**/

function rtrim (str, charlist)
{
	charlist = !charlist ? " \\s\u00A0" : (charlist + "").replace(/([\[\]\(\)\.\?\/\*\{\}\+\$\^\:])/g, "\\$1");
	var re   = new RegExp("[" + charlist + "]+$", "g");
	return (str + "").replace(re, "");
}

function ltrim (str, charlist)
{
	charlist = !charlist ? " \\s\u00A0" : (charlist + "").replace(/([\[\]\(\)\.\?\/\*\{\}\+\$\^\:])/g, "$1");
	var re   = new RegExp("^[" + charlist + "]+", "g");
	return (str + "").replace(re, "");
}
