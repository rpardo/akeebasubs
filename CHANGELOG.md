# 7.0.1

**Bug fixes**

* Cannot apply a coupon code for seven days since I last closed the payment dialog for the same subscription level
* Wrong language string for canceled subscriptions
* Wrong display in My Subscriptions page when you have not subscribed yet

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