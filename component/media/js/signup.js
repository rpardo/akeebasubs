/*
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

/**
 * Setup (required for Joomla! 3)
 */
if (typeof (akeeba) === "undefined")
{
    var akeeba = {};
}

if (typeof (akeeba.jQuery) === "undefined")
{
    akeeba.jQuery = window.jQuery.noConflict();
}

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
var akeebasubs_apply_validation             = false;
var akeebasubs_form_specifier               = "signupForm";

(function ($) {
    $.fn.removePartial = function (baseClass, removeThis) {
        var myElement = this;

        if (this.length !== undefined)
        {
            if (this.length === 0)
            {
                return;
            }

            myElement = $(this[0]);
        }

        var myClass   = myElement.attr("class");
        var classList = [];

        if (typeof myClass !== "undefined")
        {
            classList = myClass.split(/\s+/);
        }

        $.each(classList, function (index, wholeClass) {
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

    $.fn.addPartial = function (baseClass, addThis) {
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

        $.each(classList, function (index, wholeClass) {
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

function akeebasubsLevelToggleDetails()
{
    (function ($) {
        var elDetails = $("#akeebasubs-column-product-description");

        if (!elDetails)
        {
            return;
        }

        if (elDetails.css("display") == "none")
        {
            elDetails.show("slow");

            return;
        }

        elDetails.hide("slow");
    })(akeeba.jQuery);
}

function akeebasubsLevelToggleCoupon(e)
{
    (function ($) {
        var elContainer = $("#akeebasubs-coupon-code-container");

        if (!elContainer)
        {
            return;
        }

        if (elContainer.css("display") == "none")
        {
            e          = e || window.event;
            var target = e.target || e.srcElement;
            elContainer.show("fast");
            $(target).hide("fast");

            return;
        }

        elContainer.hide("fast");
    })(akeeba.jQuery);
}

function cacheSubmitAction(e)
{
    (function ($) {
        e.preventDefault();
        akeebasubs_submit_after_validation = true;
        $("#subscribenow").attr("disabled", "disabled");
    })(akeeba.jQuery);
}

function blockInterface()
{
    (function ($) {
        var btnSubscribeNow = $("#subscribenow");
        btnSubscribeNow.click(cacheSubmitAction);
        btnSubscribeNow.attr("disabled", "disabled");
        akeebasubs_blocked_gui = true;
    })(akeeba.jQuery);
}

function enableInterface()
{
    (function ($) {
        var btnSubscribeNow = $("#subscribenow");

        btnSubscribeNow.unbind("click");
        btnSubscribeNow.removeAttr("disabled");

        akeebasubs_blocked_gui = false;

        if (akeebasubs_run_validation_after_unblock)
        {
            akeebasubs_run_validation_after_unblock = false;

            validateForm();

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
function validateForm(callback_function)
{
    if (akeebasubs_blocked_gui)
    {
        akeebasubs_run_validation_after_unblock = true;
        return;
    }

    (function ($) {
        var paymentMethod = $("input[name=paymentmethod]:checked").val();

        if (paymentMethod == null)
        {
            paymentMethod = $("select[name=paymentmethod]").val();
        }

        var $couponField = $("#coupon");
        var couponValue  = ($couponField.length > 0) ? $couponField.val() : "";

        var data = {
            "action":        "read",
            "id":            akeebasubs_level_id,
            "username":      $("#username").val(),
            "name":          $("#name").val(),
            "email":         $("#email").val(),
            "email2":        $("#email2").val(),
            "coupon":        couponValue,
            "paymentmethod": paymentMethod,
            "custom":        {},
            "subcustom":     {}
        };

        var elPassword = $("#password");

        if (elPassword)
        {
            data.password  = elPassword.val();
            data.password2 = $("#password2").val();
        }

        // Fetch the custom fields
        $.each(akeebasubs_validation_fetch_queue, function (index, function_name) {
            var result = function_name();

            if ((result !== null) && (typeof result === "object"))
            {
                // Merge the result with the data object
                $.extend(data.custom, result);
            }
        });

        // Fetch the per-subscription custom fields
        $.each(akeebasubs_sub_validation_fetch_queue, function (index, function_name) {
            var result = function_name();
            if ((result !== null) && (typeof result === "object"))
            {
                // Merge the result with the data object
                $.extend(data.subcustom, result);
            }
        });

        blockInterface();

        $.ajax({
            type:     "POST",
            url:      akeebasubs_validate_url + "?option=com_akeebasubs&view=Validate&format=json",
            data:     data,
            dataType: "json",
            success:  function (msg, textStatus, xhr) {
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
            error:    function (jqXHR, textStatus, errorThrown) {
                enableInterface();
            }
        });

    })(akeeba.jQuery);
}

/**
 * Validates the password fields
 * @return
 */
function validatePassword()
{
    (function ($) {
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
function validateName()
{
    (function ($) {
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

function echeck(str)
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
function validateEmail()
{
    (function ($) {
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

function applyValidation(response, callback)
{
    akeebasubs_cached_response = response;

    (function ($) {
        akeebasubs_valid_form = true;

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
        }

        // Finally, apply the custom validation
        $.each(akeebasubs_validation_queue, function (index, function_name) {
            var isValid           = function_name(response);
            akeebasubs_valid_form = akeebasubs_valid_form & isValid;
        });
        $.each(akeebasubs_sub_validation_queue, function (index, function_name) {
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

function applyPrice(response)
{
    (function ($) {
        var $sumTotalField = $("#akeebasubs-sum-total");

        if ($sumTotalField.length > 0)
        {
            $sumTotalField.text(response.gross);

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
                    response.discount = response.discount * 1.00;
                    response.net      = response.net * 1.00;

                    $discountFieldContainer.show();

                    if ($originalFieldContainer.length)
                    {
                        $originalFieldContainer.show();
                    }

                    $discountField.html(response.discount.toFixed(2));

                    if ($originalField.length)
                    {
                        $originalField.html(response.net.toFixed(2));
                    }
                }
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

(function ($) {
    $(document).ready(function () {
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

        if ($("#coupon").length > 0)
        {
            $("#coupon").blur(validateForm);
        }

        // Disable form submit when ENTER is hit in the coupon field
        $("input#coupon")
            .keypress(function (e) {
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
