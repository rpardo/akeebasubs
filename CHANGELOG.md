# 7.0.0

**IMPORTANT** This changes radically the way Akeeba Subscriptions works. It no longer supports regular payment methods, it no longer includes integrated invoicing or tax calculations. It will only work with Merchant of Record services (only integration with Paddle is planned). If you want the classic Akeeba Subscriptions experience you should use version 6. **DO NOT INSTALL UNLESS YOU UNDERSTAND WHAT THIS MEANS**.

**Remove features**

* Removed “Do Not Track” warning (gh-328)
* Removed Sign-up Fee feature (gh-329)
* Removed “Payment Methods” option in subscription level setup page (gh-330)
* Removed “Level Groups” feature. Use subscription level relations instead. (gh-331)
* Removed “States” feature. This feature is no longer used since we removed tax calculations. (gh-333)
* Removed obsolete “Reseller” plugin (gh-337)
* Removed obsolete “automatic invoicing” plugin (gh-336)
* Removed obsolete “Akeeba Subscriptions - Country selection for tax display” module (gh-339)
* Removed post-installation wizard (gh-335)

# 6.2.3

**Miscellaneous changes**

* Remove frontend caching (all pages are user-specific and / or their content is modifiable by the session state)

**Bug fixes**

* [HIGH] Cannot select "Last percent" when setting up a coupon
* [HIGH] Cannot advance the Configuration Wizard when caching is enabled
* [LOW] Wrong icons and documentation URL in configuration wizard

# 6.2.2

**Bug fixes**

* Cannot install 6.2.1

# 6.2.1

**Miscellaneous changes**

* Protection of all component and plugin folders against direct web access

**Bug fixes**

* [MEDIUM] Expiration Control plugin did not, in fact, auto-publish early renewals (despite supposedly fixing this issue in 6.1.4) 

# 6.2.0

**Miscellaneous changes**

* PHP versions supported: 7.2.x, 7.3.x
* Do not cache VIES validation failure in the session when the VIES service does not return a definitive result e.g. a member state's service is non-responsive

**Bug fixes**

* [HIGH] Cannot set the subscription level's Level Group (page reloads) gh-325 
* [HIGH] Modified subscription information wouldn't be remembered if the validation failed 
* [MEDIUM] The "Yes, do not check again" option in the user's Is VIES Registered field had no effect.

# 6.1.4

**Miscellaneous changes**

* Improved use of timezones throughout the component (gh-295)

**Bug fixes**

* [HIGH] My Subscriptions shows timestamps in GMT despite a different timezone suffix printed on the page
* [HIGH] Subscription Expiration Control plugin did not auto-publish early renewals
* [MEDIUM] Subscription Relations edit page, the target level was incorrectly populated with the source level
* [LOW] Cannot select multiple subscriptions when creating a coupon
* [LOW] Subscription editor: could not view or modify the publish up/down time, only the date part was viewable and editable 

# 6.1.3

**Miscellaneous changes**

* Select the default payment method if no or an invalid method was selected (instead of simply failing) 
* Joomla! 3.9 backend Components menu item compatibility

# 6.1.2

**New**

* Improved VivaWallet (formerly VivaPayments) plugin

# 6.1.1

**New**

* Allow the use of custom layouts in the Level view using the 'layout' query string parameter
* Allow EU fields to be disabled per subscription level. Useful, for example, for free subscriptions where confirming the refund policy makes no sense.
* New “Personal Information” privilege for accessing views with personally identifiable information

**Miscellaneous changes**

* Support more Amazon S3 regions

**Bug fixes**

* [HIGH] New format for Irish VAT numbers was not taken into account
* [MEDIUM] Clicking on a user from the Subscriptions page resulted in an error instead of opening their profile

# 6.1.0

**New**

* Support for precompiled Blade templates, for those few servers which lack token_get_all support. IMPORTANT: On those servers you should do template overrides of the _precompiled_ .php template, not the Blade template. Otherwise you'll get a PHP Fatal error.
* Downloading invoices and credit notes is decoupled from storing them on disk
* Automatically encrypt invoices stored in the database

**Bug fixes**

* [CRITICAL] Editing existing records in the backend results in creation of new records instead
* [HIGH] PayPal and 2Checkout collation plugins do not trigger the fixSubscriptionDates code
* [HIGH] JavaScript error in the subscription page prevents users from using discount coupon codes (gh-311) 
* [HIGH] Payment callbacks may not be triggered (gh-310) 
* [HIGH] Credit Notes page was off by one column and did not display the invoice number 
* [LOW] Subscription form is not prefilled when logging in after visiting the subscription page as guest in the same session (gh-309) 
* [LOW] Update information appeared unstyled 

# 6.0.0

**New**

* Rewritten interface using our Akeeba Frontend Framework (FEF).
* Warn the user if either FOF or FEF is not installed.
* Warn the user about incompatible versions of PHP, use of eAccelerator on PHP 5.4 and use of HHVM.

**Removed features**

* The following payment plugins have not been maintained since 2013 and have been removed: Skrill, Stripe, PayPal Payment Pro, PayPal Express.
* The following payment plugins have been replaced by new versions and have been removed: 2Checkout (replaced by 2conew), PayMill (replaced by paymilldss3). Old versions of the plugins don't even work anymore so why ship them...? 

**Bug fixes**

* Inconsistent use of user-supplied data and data from the database in the subscription form in some cases.
* Fatal error if we cannot retrieve the exchange rate information from the ECB

# 5.2.6

**Added features**

* Log failed subscription for submissions (gh-298).
* Display timezone in the My Subscriptions page (ongoing gh-295)
* More EU checkboxes since we now have to also deal with GDPR.
* Google Analytics for e-commerce integration.

**Miscellaneous changes**

* Clicking on "Reload update information" will fix Joomla! erroneously reporting an update is available when you have the latest version installed.
* Removed redirection from the "System - Akeeba Subscriptions Logout user" plugin. Redirection caused the Thank You page to never appear until you logged in and only if you did not use the login module on the same page.
* PayPal Collation plugin will use POST instead of GET on June 2018, per PayPal's docs.

# 5.2.5

**Miscellaneous changes**

* Address Joomla! 3.7 visual bug with .span6 margins being too wide

**Bug fixes**

* "Do you already have an account" leads to the wrong URL
* Username validation for logged in users can make subscription / renewal impossible
* Reseller plugin caused a save button to display when displaying a subscription's details

# 5.2.4

**Bug fixes**

* Fatal error in backend Coupons page for some users
* Workaround for Joomla! Bug 16147 (https://github.com/joomla/joomla-cms/issues/16147) - Cannot access component after installation when cache is enabled
* Workaround for Joomla! bug "Sometimes files are not copied on update"
* Blind change: 2Checkout TLS 1.1 requirement since June 1st, 2017 (blind because I can't test it until AFTER they switch their production systems FFS)
* PayMill button class should be "btn btn-success", not just "btn"
* The selected payment method is not remembered when coming back to the form after a validation error

# 5.2.3

**Added features**

* More price formatting options in the asprice content plugin

**Bug fixes**

* gh-281 Paypal IPN issues
* Joomla! 3.7 added a fixed width to specific button classes in the toolbar, breaking the page layout
* Joomla! 3.7.0 broke the JDate package, effectively ignoring timezones, causing grave errors in date / time calculations and display
* Joomla! 3.7.0 has a broken System - Page Cache plugin leading to white pages and wrong redirections

# 5.2.2

**Removed features**

* Removed translations

**Added features**

* akeeba/internal#6 Added "Missing Invoice" report
* gh-276 Added method getFieldValue() to UserInfo\Html
* New update server

**Miscellaneous changes**

* Added local debugging option for the PayPal plugin
* Updated VAT rates

**Bug fixes**

* gh-275 VAT rate does not show on subscribe page
* Recurring subscriptions would result in a PHP exception
* Wrong VAT calculation in recurring subscriptions
* No invoice generated when a subscription is upgraded using a subscription relation rule
* gh-280 Expiration notification and expiration control plugins don't respect the scheduling option

# 5.2.1

(Ignore that version)

# 5.2.0

**Important changes**

* NO SUPPORT – INTERNAL PROJECT ONLY
* Invoices could be downloaded by anyone who knows the invoice ID without being logged in

**Removed features**

* Removed options: use email as username (no longer allowed)
* Removed options: show business fields, show regular fields, show discount field, show coupon field, show state (all fields are shown, use template overrides to hide them)
* Removed options: show/hide countries from the subscription page (all countries are displayed)
* Removed options: allow login (not logged in users always see the login area, use view template overrides to hide that)
* Removed JS comments working around third party Javascript bugs. If you get a JS bug in the front-end FIX YOUR SITE.
* Removed admin module showing latest subscriptions
* Removed AcyMailing integration plugin
* Removed Akeeba Ticket System 1.x credits integration plugin
* Removed automatic country and city fill plugin
* Removed Google Analytics for Commerce integration plugin
* Removed custom fields feature
* Removed intellectual property integration plugin
* Removed Joomla user profile integration plugin
* Removed Kunena integration plugin
* Removed reCaptcha integration plugin
* Removed Slave Subscriptions feature
* Removed custom SQL plugin
* Removed debug subscriptions email plugin
* Removed obsolete languages
* Removing CLI scripts, they can't work reliably
* No more PostgreSQL support

**Miscellaneous changes**

* Show a validation error if a user doesn't enter a username when submitting the form
* The subscription form is now implemented with a Blade template and ONLY supports Bootstrap 3 markup
* Updated PayPal payments plugin with forced TLSv1.2. You MUST use a compatible server!
* Working around Joomla! 3.5 and later mail sending backwards incompatible behaviour change
* Much better solution to remembering form information: Fields will contain saved information from your last subscription when you access the Level page *UNLESS* you have EXPLICITLY ended up there by submitting an invalid form.
* Updated VAT rates
* Adjustment for Joomla! 3.6 log dir change
* Update TCPDF to the newest available v6 release
* Do not delete New / Cancelled subscriptions, lets the collation plugins do their job correctly

**Added features**

* Edit the pre-discount amount in the back-end subscription edit form
* My Subscriptions module
* Credit notes (inverse of invoices), created against already issued invoices
* Invoicing information shown in the backend subscription edit page
* You can disable the Do Not Track warning
* PayPal collation plugin

**Bug fixes**

* Recurring subscriptions did not issue invoices
* ATS credits and the reseller plugin were referencing the obsolete FOF 2 instead of the correct FOF 3 classes
* The converted currency price didn't include VAT and signup fees even when the uncoverted price did
* Do not remove akeebabackup.com from the emails. It's breaking the emails we're sending from our own site...
* If someone blanked out their VAT number after using a VIES-registered VAT number no VAT was charged
* After submitting an invalid form the fields were filled with the saved user state parameters instead of the submitted user's data
* Cannot download invoice from the front-end
* "Run Integrations" didn't work
* An empty username is NOT acceptable
* Do not use recurring amount for the first initial payment
* Spooky error with the logout plugin and Joomla! 3.5+ session management
* Unhandled unsuspended reccuring payment
* Content plugins would fail to execute because they were using the wrong container
* downloadid was missing on the invoice due to changed paths & namespaces in ARS
* When an outdated plugin was used the wrong path was reported. Thanks @Radek-Suski
