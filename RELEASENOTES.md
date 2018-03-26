## Joomla and PHP Compatibility

We are developing, testing and using Akeeba Subscriptions using the latest version of Joomla! and a popular and actively maintained branch of PHP 7. At the time of this writing this is:
* Joomla! 3.8
* PHP 7.1

Akeeba Subscriptions should be compatible with:
* Joomla! 3.8
* PHP 7.0, 7.1, 7.2.

## Language files

Akeeba Subscriptions comes with English (Great Britain) language built-in. Installation packages for other languages are available [on our language download page](https://cdn.akeebabackup.com/language/akeebasubs/index.html).

## Changelog

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
