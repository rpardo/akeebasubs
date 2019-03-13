## Joomla and PHP Compatibility

We are developing, testing and using Akeeba Subscriptions using the latest version of Joomla! and a popular and actively maintained branch of PHP 7. At the time of this writing this is:

* Joomla! 3.9
* PHP 7.3

Akeeba Subscriptions should be compatible with:

* Joomla! 3.8 and 3.9.
* PHP 7.2, 7.3

## Changelog

**Miscellaneous changes**

* Protection of all component and plugin folders against direct web access

**Bug fixes**

* [MEDIUM] Expiration Control plugin did not, in fact, auto-publish early renewals (despite supposedly fixing this issue in 6.1.4) 
