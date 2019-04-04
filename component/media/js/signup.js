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

function validateForm()
{
    (function ($) {
        var signupForm    = $("#signupForm");
        signupForm.attr('action', window.location);
        signupForm.submit();
    })(akeeba.jQuery);
}

function akeebaSubscriptionsStartPayment()
{
    (function ($) {
        var formData = {
            coupon: $('#coupon').val(),
            accept_terms: $('#accept_terms').is(':checked') ? 1 : 0
        };

        if ($('#name'))
        {
            formData['name'] = $('#name').val();
            formData['username'] = $('#username').val();
            formData['password'] = $('#password').val();
            formData['password2'] = $('#password2').val();
            formData['email'] = $('#email').val();
            formData['email2'] = $('#email2').val();
        }

        $.ajax({
            url: $('#signupForm').attr('action'),
            data: formData,
            type: "POST",
            dataType: "json"
        }).done(function(ret) {
            console.log(ret);

            if (ret.method === 'redirect')
            {
                if (ret.url === null)
                {
                    window.location.reload();

                    return;
                }

                window.location = ret.url;

                return;
            }

            Paddle.Checkout.open({
                override: ret.url
            });
        }).fail(function(jqXHR, textStatus, errorThrown){
            window.location.reload();
        });
    })(akeeba.jQuery);

    return false;
}

(function ($) {
    $(document).ready(function () {
        // Disable form submit when ENTER is hit in the coupon field
        $("input#coupon")
            .keypress(function (e) {
                if (e.which == 13)
                {
                    validateForm();
                    return false;
                }
            });
    });
})(akeeba.jQuery);