# 7.1.2

**New features**

* Frontend subscription statistics JSON API

**Bug fixes**

* _userInfo in frontend JSON subscriptions did not include the email address of the user

# 7.1.1

**New features**

* Sales off-line switch
* Script to fix contact flags of early renewals
* Defer loading of the Paddle JS

**Miscellaneous changes**

* Database optimization: cache Levels queries in content plugins
* Joomla 4 beta 1 compatibility
* Remove hardcoded null date 0000-00-00 00:00:00 (required for J4 compatibility)
* Make all JS and CSS load minified files first

**Bug fixes**

* Obsolete EmailTemplates link in the toolbar
* Emails not sent
* Administrator Dark Mode CSS would also partially apply in Light Mode as well if the setting was Auto instead of Always
* Concurrent handling of subscription_created and subscription_payment_succeeded callbacks leads to inactive subscriptions 
* Content plugin aslink incompatible with Joomla 4
* Some system plugins could fail depending on execution order
* Joomla 4 throws an exception when mail is disabled and you try to send an email
* Unhandled exception page was incompatible with Joomla 4
* Show correct recurring status in backend
* Recurring subscriptions with failed payments appeared as pending in the frontend instead of expired or canceled

# 7.1.0

**Removed features**

* Removed subscription country determined from IP address
* Removed installation and update prompts for the Akeeba GeoIP plugin

**New features**

* Common PHP version warning scripts
* Dark Mode
* CLI script to fix recurring subscriptions with missing update/cancel URLs
* Collation script to identify problem transactions and send a report email
* Using Blade templates for email messages 
* Do not show ugly errors in the subscription form for first time subscribers

**Bug fixes**

* Early recurring renewal would still charge you for the trial period 
* Recurring payments fail if the original transaction is not marked as such

# 7.0.1

**New features**

* Subscription emails and expiration notification plugins log all the emails they send in a common log and format.

**Miscellaneous changes**

* Bump minimum PHP version to 7.3.0.
* Joomla 4 compatibility (preliminary work based on Joomla 4.0.0-beta10)
* Give an option to disable emails (gh-388)
* Replace graphs with Charts.js (gh-386)
* Implement Joomla! 4 routing (gh-301) 
* Unpaid subscription page: update messages, provide support when people accidentally closed the payment popup
* My Subscriptions: show all transactions by default

**Remove features**

* Removed Joomla extension update support

**Bug fixes**

* Cannot apply a coupon code for seven days since I last closed the payment dialog for the same subscription level
* Wrong language string for canceled subscriptions
* Wrong display in My Subscriptions page when you have not subscribed yet
* Wrong dates in My Subscriptions page for older subscription records
* Users were linking to a view that no longer exists. Now they point to their J! user profile.
* The asprice content plugin did not respect the "Localise prices" component option.
* Sales Graph would show looped trend lines because the data wasn't sorted properly.
* Subscription expiration emails were sent for recurring subscriptions
* Hard failure (will no longer retry recurring payment) should cancel the subscription on our site, not just mark it expired.
* You couldn't renew if there was a pending transaction (unpaid subscription) ever, even ten years ago. Corrected to one fortnight only.
* Recurring access coupon codes would give you the first installment free of charge if you did not have an active subscription on the same level.

# 7.0.0

**IMPORTANT** This changes radically the way Akeeba Subscriptions works. It no longer supports regular payment methods, it no longer includes integrated invoicing or tax calculations. It will only work with Merchant of Record services (only integration with Paddle is planned). If you want the classic Akeeba Subscriptions experience you should use version 6. **DO NOT INSTALL UNLESS YOU UNDERSTAND WHAT THIS MEANS**.

**New features**

* Social Login buttons in the subscription page (gh-358)

**Miscellaneous changes**

* Invoices and Credit Notes are now read-only (gh-351)
* Redesigned the subscription page (gh-332)
* Migrate country data from `#__akeebasubs_users` on extension upgrade (gh-360)

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
* Removed the Import feature (gh-340)
* Removed the API Coupons feature (gh-352)
* Removed the Invoice Templates feature (gh-345)
* Removed the Credit Notes Templates feature (gh-347)
* Removed all tax-related features (gh-334)
* Removed all currency exchange rate features (gh-356)
* Removed all VAT number validation login (gh-357)
* Removed all former payment plugins (gh-341)
* Removed now obsolete as2cocollation and aspaypalcollation plugins (gh-338)
* Removed the Hide Lone Payment Option setting
* Removed the setting to show payment options as text
* Removed the Required Valid Coupon feature
* Removed invoicing fields from the Subscription page (gh-332)
* Removed backend user information management page (gh-359)
* Removed frontend user information management page (gh-359)
* Removed the plugins managing automatic user log out upon subscription purchase (gh-359)
* Removed unused update options
* Removed the payment plugins infrastructure (gh-342)