/*
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
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

var akeebasubsMessageUrl = window.location;

var isoCountries = {
    'AF' : 'Afghanistan',
    'AX' : 'Aland Islands',
    'AL' : 'Albania',
    'DZ' : 'Algeria',
    'AS' : 'American Samoa',
    'AD' : 'Andorra',
    'AO' : 'Angola',
    'AI' : 'Anguilla',
    'AQ' : 'Antarctica',
    'AG' : 'Antigua And Barbuda',
    'AR' : 'Argentina',
    'AM' : 'Armenia',
    'AW' : 'Aruba',
    'AU' : 'Australia',
    'AT' : 'Austria',
    'AZ' : 'Azerbaijan',
    'BS' : 'Bahamas',
    'BH' : 'Bahrain',
    'BD' : 'Bangladesh',
    'BB' : 'Barbados',
    'BY' : 'Belarus',
    'BE' : 'Belgium',
    'BZ' : 'Belize',
    'BJ' : 'Benin',
    'BM' : 'Bermuda',
    'BT' : 'Bhutan',
    'BO' : 'Bolivia',
    'BA' : 'Bosnia And Herzegovina',
    'BW' : 'Botswana',
    'BV' : 'Bouvet Island',
    'BR' : 'Brazil',
    'IO' : 'British Indian Ocean Territory',
    'BN' : 'Brunei Darussalam',
    'BG' : 'Bulgaria',
    'BF' : 'Burkina Faso',
    'BI' : 'Burundi',
    'KH' : 'Cambodia',
    'CM' : 'Cameroon',
    'CA' : 'Canada',
    'CV' : 'Cape Verde',
    'KY' : 'Cayman Islands',
    'CF' : 'Central African Republic',
    'TD' : 'Chad',
    'CL' : 'Chile',
    'CN' : 'China',
    'CX' : 'Christmas Island',
    'CC' : 'Cocos (Keeling) Islands',
    'CO' : 'Colombia',
    'KM' : 'Comoros',
    'CG' : 'Congo',
    'CD' : 'Congo, Democratic Republic',
    'CK' : 'Cook Islands',
    'CR' : 'Costa Rica',
    'CI' : 'Cote D\'Ivoire',
    'HR' : 'Croatia',
    'CU' : 'Cuba',
    'CY' : 'Cyprus',
    'CZ' : 'Czech Republic',
    'DK' : 'Denmark',
    'DJ' : 'Djibouti',
    'DM' : 'Dominica',
    'DO' : 'Dominican Republic',
    'EC' : 'Ecuador',
    'EG' : 'Egypt',
    'SV' : 'El Salvador',
    'GQ' : 'Equatorial Guinea',
    'ER' : 'Eritrea',
    'EE' : 'Estonia',
    'ET' : 'Ethiopia',
    'FK' : 'Falkland Islands (Malvinas)',
    'FO' : 'Faroe Islands',
    'FJ' : 'Fiji',
    'FI' : 'Finland',
    'FR' : 'France',
    'GF' : 'French Guiana',
    'PF' : 'French Polynesia',
    'TF' : 'French Southern Territories',
    'GA' : 'Gabon',
    'GM' : 'Gambia',
    'GE' : 'Georgia',
    'DE' : 'Germany',
    'GH' : 'Ghana',
    'GI' : 'Gibraltar',
    'GR' : 'Greece',
    'GL' : 'Greenland',
    'GD' : 'Grenada',
    'GP' : 'Guadeloupe',
    'GU' : 'Guam',
    'GT' : 'Guatemala',
    'GG' : 'Guernsey',
    'GN' : 'Guinea',
    'GW' : 'Guinea-Bissau',
    'GY' : 'Guyana',
    'HT' : 'Haiti',
    'HM' : 'Heard Island & Mcdonald Islands',
    'VA' : 'Holy See (Vatican City State)',
    'HN' : 'Honduras',
    'HK' : 'Hong Kong',
    'HU' : 'Hungary',
    'IS' : 'Iceland',
    'IN' : 'India',
    'ID' : 'Indonesia',
    'IR' : 'Iran, Islamic Republic Of',
    'IQ' : 'Iraq',
    'IE' : 'Ireland',
    'IM' : 'Isle Of Man',
    'IL' : 'Israel',
    'IT' : 'Italy',
    'JM' : 'Jamaica',
    'JP' : 'Japan',
    'JE' : 'Jersey',
    'JO' : 'Jordan',
    'KZ' : 'Kazakhstan',
    'KE' : 'Kenya',
    'KI' : 'Kiribati',
    'KR' : 'Korea',
    'KW' : 'Kuwait',
    'KG' : 'Kyrgyzstan',
    'LA' : 'Lao People\'s Democratic Republic',
    'LV' : 'Latvia',
    'LB' : 'Lebanon',
    'LS' : 'Lesotho',
    'LR' : 'Liberia',
    'LY' : 'Libyan Arab Jamahiriya',
    'LI' : 'Liechtenstein',
    'LT' : 'Lithuania',
    'LU' : 'Luxembourg',
    'MO' : 'Macao',
    'MK' : 'Macedonia',
    'MG' : 'Madagascar',
    'MW' : 'Malawi',
    'MY' : 'Malaysia',
    'MV' : 'Maldives',
    'ML' : 'Mali',
    'MT' : 'Malta',
    'MH' : 'Marshall Islands',
    'MQ' : 'Martinique',
    'MR' : 'Mauritania',
    'MU' : 'Mauritius',
    'YT' : 'Mayotte',
    'MX' : 'Mexico',
    'FM' : 'Micronesia, Federated States Of',
    'MD' : 'Moldova',
    'MC' : 'Monaco',
    'MN' : 'Mongolia',
    'ME' : 'Montenegro',
    'MS' : 'Montserrat',
    'MA' : 'Morocco',
    'MZ' : 'Mozambique',
    'MM' : 'Myanmar',
    'NA' : 'Namibia',
    'NR' : 'Nauru',
    'NP' : 'Nepal',
    'NL' : 'Netherlands',
    'AN' : 'Netherlands Antilles',
    'NC' : 'New Caledonia',
    'NZ' : 'New Zealand',
    'NI' : 'Nicaragua',
    'NE' : 'Niger',
    'NG' : 'Nigeria',
    'NU' : 'Niue',
    'NF' : 'Norfolk Island',
    'MP' : 'Northern Mariana Islands',
    'NO' : 'Norway',
    'OM' : 'Oman',
    'PK' : 'Pakistan',
    'PW' : 'Palau',
    'PS' : 'Palestinian Territory, Occupied',
    'PA' : 'Panama',
    'PG' : 'Papua New Guinea',
    'PY' : 'Paraguay',
    'PE' : 'Peru',
    'PH' : 'Philippines',
    'PN' : 'Pitcairn',
    'PL' : 'Poland',
    'PT' : 'Portugal',
    'PR' : 'Puerto Rico',
    'QA' : 'Qatar',
    'RE' : 'Reunion',
    'RO' : 'Romania',
    'RU' : 'Russian Federation',
    'RW' : 'Rwanda',
    'BL' : 'Saint Barthelemy',
    'SH' : 'Saint Helena',
    'KN' : 'Saint Kitts And Nevis',
    'LC' : 'Saint Lucia',
    'MF' : 'Saint Martin',
    'PM' : 'Saint Pierre And Miquelon',
    'VC' : 'Saint Vincent And Grenadines',
    'WS' : 'Samoa',
    'SM' : 'San Marino',
    'ST' : 'Sao Tome And Principe',
    'SA' : 'Saudi Arabia',
    'SN' : 'Senegal',
    'RS' : 'Serbia',
    'SC' : 'Seychelles',
    'SL' : 'Sierra Leone',
    'SG' : 'Singapore',
    'SK' : 'Slovakia',
    'SI' : 'Slovenia',
    'SB' : 'Solomon Islands',
    'SO' : 'Somalia',
    'ZA' : 'South Africa',
    'GS' : 'South Georgia And Sandwich Isl.',
    'ES' : 'Spain',
    'LK' : 'Sri Lanka',
    'SD' : 'Sudan',
    'SR' : 'Suriname',
    'SJ' : 'Svalbard And Jan Mayen',
    'SZ' : 'Swaziland',
    'SE' : 'Sweden',
    'CH' : 'Switzerland',
    'SY' : 'Syrian Arab Republic',
    'TW' : 'Taiwan',
    'TJ' : 'Tajikistan',
    'TZ' : 'Tanzania',
    'TH' : 'Thailand',
    'TL' : 'Timor-Leste',
    'TG' : 'Togo',
    'TK' : 'Tokelau',
    'TO' : 'Tonga',
    'TT' : 'Trinidad And Tobago',
    'TN' : 'Tunisia',
    'TR' : 'Turkey',
    'TM' : 'Turkmenistan',
    'TC' : 'Turks And Caicos Islands',
    'TV' : 'Tuvalu',
    'UG' : 'Uganda',
    'UA' : 'Ukraine',
    'AE' : 'United Arab Emirates',
    'GB' : 'United Kingdom',
    'US' : 'United States',
    'UM' : 'United States Outlying Islands',
    'UY' : 'Uruguay',
    'UZ' : 'Uzbekistan',
    'VU' : 'Vanuatu',
    'VE' : 'Venezuela',
    'VN' : 'Viet Nam',
    'VG' : 'Virgin Islands, British',
    'VI' : 'Virgin Islands, U.S.',
    'WF' : 'Wallis And Futuna',
    'EH' : 'Western Sahara',
    'YE' : 'Yemen',
    'ZM' : 'Zambia',
    'ZW' : 'Zimbabwe'
};

function getCountryName (countryCode) {
    if (isoCountries.hasOwnProperty(countryCode)) {
        return isoCountries[countryCode];
    } else {
        return countryCode;
    }
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
        var elUseRecurring = $("#use_recurring");
        var useRecurring   = false;

        if (elUseRecurring)
        {
            useRecurring = elUseRecurring.is(":checked") ? 1 : 0;
        }

        var formData = {
            coupon:        $("#coupon").val(),
            accept_terms:  $("#accept_terms").is(":checked") ? 1 : 0,
            use_recurring: useRecurring
        };

        if ($("#name"))
        {
            formData["name"]      = $("#name").val();
            formData["username"]  = $("#username").val();
            formData["password"]  = $("#password").val();
            formData["password2"] = $("#password2").val();
            formData["email"]     = $("#email").val();
            formData["email2"]    = $("#email2").val();
        }

        $.ajax({
            url:      $("#signupForm").attr("action"),
            data:     formData,
            type:     "POST",
            dataType: "json"
        }).done(function (ret) {
            console.log(ret);

            if (ret.method === "redirect")
            {
                if (ret.url === null)
                {
                    window.location.reload();

                    return;
                }

                window.location = ret.url;

                return;
            }

            akeebasubsMessageUrl = ret.messageUrl;

            Paddle.Checkout.open({
                override:        ret.url,
                successCallback: "akeebasubsCheckoutComplete",
                closeCallback:   "akeebasubsCheckoutClosed",
                eventCallback:   "akeebasubsCheckoutEvent"
            });
        }).fail(function (jqXHR, textStatus, errorThrown) {
            window.location.reload();
        });
    })(akeeba.jQuery);

    return false;
}

/**
 * Fired when the payment is successful
 *
 * @param {Object} data
 */
function akeebasubsCheckoutComplete(data)
{
    console.log('Got checkout complete');
    console.log(data);

    window.setTimeout(function() {
        window.location = akeebasubsMessageUrl;
    }, 1000);
}

/**
 * Fired when the checkout modal closes without a successful payment
 *
 * @param {Object} data
 */
function akeebasubsCheckoutClosed(data)
{
    console.log('Got checkout closed');
    console.log(data);

    window.setTimeout(function() {
        window.location = akeebasubsMessageUrl;
    }, 1000);
}

/**
 * Executes when Paddle fires an event. Used for Google Analytics e-commerce tracking.
 *
 * @param {Object} data
 */
function akeebasubsCheckoutEvent(data)
{
    if (data.event === 'Checkout.PaymentComplete')
    {
        console.log("AkeebaSubs GACommerce: Submitting e-commerce information using ga.js");
        ga('require', 'ecommerce');
        ga('ecommerce:addTransaction', {
            'id': data.checkout.passthrough,
            'revenue': data.checkout.prices.vendor.total,
            'currency': data.checkout.prices.vendor.currency
        });
        ga('ecommerce:addItem', {
            'id': data.checkout.passthrough,
            'name': data.product.name,
            'sku': data.product.id,
            'price': data.checkout.prices.vendor.total,
            'currency': data.checkout.prices.vendor.currency
        });
        ga('ecommerce:send');
    }
}

/**
 * Localises a price using the Paddle API. Only for single products, not recurring subscriptions.
 *
 * @param   {Number}   product            Paddle product ID
 * @param   {Boolean}  allowTaxInclusive  Should I display tax inclusive pricing in grossTarget?
 * @param   {String}   grossTarget        ID of the element to display the gross price
 * @param   {String}   taxTarget          ID of the element to display the tax price
 * @param   {String}   netTarget          ID of the element to display the net price
 * @param   {String}   taxContainer       ID of an element to hide if Paddle returns non-tax-inclusive price
 * @param   {String}   countryTarget      ID of the element to display the country code
 *
 * @see https://paddle.com/docs/paddlejs-localized-prices/
 */
function akeebasubsLocalisePrice(product, allowTaxInclusive, grossTarget, taxTarget, netTarget, taxContainer, countryTarget)
{
    Paddle.Product.Prices(product, 1, function(prices) {

        if (grossTarget)
        {
            var elGross = document.getElementById(grossTarget);

            if (elGross !== null)
            {
                elGross.innerText = allowTaxInclusive ? prices.price.gross : prices.price.net;
            }
        }

        if (taxTarget)
        {
            var elTax = document.getElementById(taxTarget);

            if (elTax !== null)
            {
                elTax.innerText = prices.price.tax;
            }
        }

        if (netTarget)
        {
            var elNet = document.getElementById(netTarget);

            if (elNet !== null)
            {
                elNet.innerText = prices.price.net;
            }
        }

        if (taxContainer)
        {
            var elTaxContainer = document.getElementById(taxContainer);

            if (elTaxContainer !== null)
            {
                elTaxContainer.style.display = prices.price.tax_included ? 'block' : 'none'
            }
        }

        if (countryTarget)
        {
            var elCountry = document.getElementById(countryTarget);

            if (elCountry !== null)
            {
                if ('value' in elCountry)
                {
                    elCountry.value = prices.country;
                }
                else
                {
                    elCountry.innerText = getCountryName(prices.country);
                }
            }
        }
    });
}

/**
 * Localises the price of a recurring product which can be bought instead on a (discounted) upgrade.
 *
 * @param   {Number}  product               Subscription plan ID
 * @param   {Boolean} includeTax            Include tax in the price?
 * @param   {String}  priceContainerId      HTML ID of the price container
 * @param   {String}  frequencyContainerId  HTML ID of the recurring charge frequency container
 */
function akeebasubsLocaliseRecurringPriceOnly(product, includeTax, priceContainerId, frequencyContainerId)
{
    if (!product)
    {
        return;
    }

    if (includeTax === null)
    {
        includeTax = true;
    }

    Paddle.Product.Prices(product, 1, function(prices) {
        console.log(prices);
        var elPrice     = document.getElementById(priceContainerId);
        var elFrequency = document.getElementById(frequencyContainerId);

        if (!prices.hasOwnProperty('recurring'))
        {
            return;
        }

        if (elPrice !== null)
        {
            elPrice.innerText = includeTax ? prices.recurring.price.gross : prices.recurring.price.net;
        }

        if (elFrequency !== null)
        {
            var type   = prices.recurring.subscription.type;
            var length = prices.recurring.subscription.length;
            var frequency = length + ' ' + Joomla.Text._('COM_AKEEBASUBS_LEVEL_LBL_OPTIN_RECURRING_PERIOD_' + type);

            if (length === 1)
            {
                frequency = Joomla.Text._('COM_AKEEBASUBS_LEVEL_LBL_OPTIN_RECURRING_PERIOD_ONE_' + type);
            }

            elFrequency.innerText = frequency;
        }
    });
}

/**
 * Localises the price of a recurring product which can be bought instead on a (discounted) upgrade.
 *
 * @param   {Number}  product     Subscription plan ID
 * @param   {Boolean} includeTax  Include tax in the price?
 */
function akeebasubsLocaliseRecurring(product, includeTax)
{
    if (!product)
    {
        return;
    }

    if (includeTax === null)
    {
        includeTax = true;
    }

    Paddle.Product.Prices(product, 1, function(prices) {
        console.log(prices);

        var elContainer = document.getElementById('akeebasubs-optin-recurring-container');
        var elInfoBox   = document.getElementById('akeebasubs-optin-recurring-info');
        var elPrice     = document.getElementById('akeebasubs-optin-recurring-price');
        var elFrequency = document.getElementById('akeebasubs-optin-recurring-frequency');

        if (!prices.hasOwnProperty('recurring'))
        {
            return;
        }

        if (elContainer !== null)
        {
            elContainer.style.display = 'block';
        }

        if (elPrice !== null)
        {
            elPrice.innerText = includeTax ? prices.recurring.price.gross : prices.recurring.price.net;
        }

        if (elFrequency !== null)
        {
            var type   = prices.recurring.subscription.type;
            var length = prices.recurring.subscription.length;
            var frequency = length + ' ' + Joomla.Text._('COM_AKEEBASUBS_LEVEL_LBL_OPTIN_RECURRING_PERIOD_' + type);

            if (length === 1)
            {
                frequency = Joomla.Text._('COM_AKEEBASUBS_LEVEL_LBL_OPTIN_RECURRING_PERIOD_ONE_' + type);
            }

            elFrequency.innerText = frequency;
        }

        if (elInfoBox !== null)
        {
            elInfoBox.style.display = 'block';
        }
    });
}

(function ($) {
    $(document).ready(function () {
        // Disable form submit when ENTER is hit in the coupon field
        $("input#coupon")
            .keypress(function (e) {
                if (e.which === 13)
                {
                    validateForm();
                    return false;
                }
            });
    });
})(akeeba.jQuery);
