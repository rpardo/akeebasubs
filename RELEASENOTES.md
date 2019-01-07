## Joomla and PHP Compatibility

We are developing, testing and using Akeeba Subscriptions using the latest version of Joomla! and a popular and actively maintained branch of PHP 7. At the time of this writing this is:

* Joomla! 3.9
* PHP 7.3

Akeeba Subscriptions should be compatible with:

* Joomla! 3.8 and 3.9.
* PHP 7.2, 7.3

## Changelog

**Miscellaneous changes**

* PHP versions supported: 7.2.x, 7.3.x
* Do not cache VIES validation failure in the session when the VIES service does not return a definitive result e.g. a member state's service is non-responsive

**Bug fixes**

* [HIGH] Cannot set the subscription level's Level Group (page reloads) gh-325 
* [HIGH] Modified subscription information wouldn't be remembered if the validation failed 
* [MEDIUM] The "Yes, do not check again" option in the user's Is VIES Registered field had no effect.
