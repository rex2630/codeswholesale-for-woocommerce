=== CodesWholesale.com for WooCommerce ===
Contributors: Devteam CodesWholesale
Tags: woocommerce, restapi, codeswholesale, games, cd-keys
Requires at least: 3.0.1
Tested up to: 4.4.2
Stable tag: trunk
License: Apache License Version 2.0, January 2004
License URI: http://www.apache.org/licenses/




CodesWholesale.com integration plugin for WooCommerce.

== Description ==

This plugin allows to integrate a WooCommerce store with [CodesWholesale.com](http://codeswholesale.com) RestAPI allowing to make purchases on the platform automatic, also providing price and stock updates for the products.

== Installation ==


1. Upload and install the plugin through 'Plugins' tab.
1. Use the 'CodesWholesale' menu to configure your plugin


== Frequently Asked Questions ==

= What it does? =

* Adds a field to product in order to match with CodesWholesale's product in "General tab" 
* Buys and sends keys (text and images) when order is marked as complete
* Error reporting to admin's email
* [CodesWholesale's](http://codeswholesale.com) low balance notification
* Automatically complete order when payment is received

= How to generate client ID and Secret? =

1. Log in to [our platform](http://codeswholesale.com)
1. Go to *RestAPI* tab
1. Click on *Generate client credentials*
1. Please note the credentials somewhere - especially client secret as it will get pernamently hidden after you leave the page.

= What to do if I lost my client secret? =
1. Log in to [our platform](http://codeswholesale.com)
1. Go to *RestAPI* tab
1. Click on *Delete client*
1. Click on *Yes, I'm sure - delete it* (don't worry, it will not delete your account)
1. Click on *Generate client credentials*
1. You will receive a new set of client ID and secret - please note it somewhere and update your API settings

== Screenshots ==

1. Plugin settings.
2. Product settings.

== Changelog ==

= 2.0 =
* Added currency conversion option.

== Upgrade Notice ==

= 2.0 =
Fixed connection issues - please update.