## Joomla and PHP Compatibility

We are developing, testing and using Akeeba Subscriptions using the latest version of Joomla! and a popular and actively maintained branch of PHP 7. At the time of this writing this is:
* Joomla! 3.7
* PHP 7.0

Akeeba Subscriptions should be compatible with:
* Joomla! 3.4, 3.5, 3.6, 3.7
* PHP 5.4, 5.5, 5.6, 7.0, 7.1.

## Language files

Akeeba Subscriptions comes with English (Great Britain) language built-in. Installation packages for other languages are available [on our language download page](https://cdn.akeebabackup.com/language/akeebasubs/index.html).

## Changelog

**Bug fixes**

* Fatal error in backend Coupons page for some users.
* Workaround for Joomla! Bug 16147 (https://github.com/joomla/joomla-cms/issues/16147) - Cannot access component after installation when cache is enabled.
* Workaround for Joomla! bug "Sometimes files are not copied on update".
* 2Checkout TLS 1.1 requirement, applicable since June 1st, 2017.
* PayMill button class should be "btn btn-success", not just "btn".
* The selected payment method is not remembered when coming back to the form after a validation error.
