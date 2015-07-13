=== Shopp Minimum Order ===
Contributors: crunnells
Donate link: http://www.chrisrunnells.com/shopp/minimum-order/
Tags: shopp, minimum order, minimum, order, e-commerce
Requires at least: 3.0
Tested up to: 4.2.2
Stable tag: 1.3.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin allows you to set a minimum order amount in the Shopp e-commerce checkout process.

== Description ==

Do you use the [Shopp plugin](http://shopplugin.net) for e-commerce? Do you want to make sure your customers order a minimum amount of items, or spend a minimum amount of money? Then the Shopp Minimum Order plugin is what you need! It will prevent customers from checking out unless they meet the minimum order requirements set by the shop owner.

*This plugin requires you to be running at least Shopp 1.3*

Once you’ve installed and activated the plugin, there will be a link in the Shopp > Setup menu where you configure the minimum order values. Set the minimum type you want to use, enter a number, then hit Save and you’re done!

The plugin will now generate an error message during the Checkout process if the order does not meet the requirements you set. We highly recommend that you notify your customers (via a message on the Shopping Cart page for example) that you have a minimum order requirement, so they’re not surprised when they try to check out.

*New Feature:* Version 1.3 supports per-product minimums! Set a minimum quantity for each product independent of the store minimums.

I’ve got some ideas for improving this plugin down the road, so I’ll be working on it as I have time.

== Installation ==

Installation is pretty straightforward.

1. Upload the `shopp-minimum-order` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Set your minimum order amounts on the Setup > Minimum Order page.
1. Alternatively, in the product editor there’s a box in the right sidebar which allows you to set a minimum quantity.

== Frequently Asked Questions ==

= Will this plugin work in earlier versions of Shopp? =

Nope! This plugin uses the Shopp API from version 1.3 (and later). It *may* work in earlier versions, but it hasn't been tested.

== Screenshots ==

1. Once you’ve installed and activated the plugin, there will be a link in the Shopp > Setup menu where you configure the minimum order values. Set the minimum type you want to use, enter a number, then hit Save and you’re done!
2. The plugin will now generate an error message during the Checkout process if the order does not meet the requirements you set. We highly recommend that you notify your customers (via a message on the Shopping Cart page for example) that you have a minimum order requirement, so they’re not surprised when they try to check out.

== Changelog ==

= 1.3.4 =
* Squashed a couple of sneaky (but dumb) bugs. One was hiding the quantity select box on product pages, and the other was just silly.

= 1.3.3 =
* Squashed a bug preventing minimums from being saved in 1.3.x (at the expense of 1.2.x compatibility). Thanks Thomas Bisshop

= 1.3.2 =
* Added a link to the Settings page from the main Plugins page.

= 1.3.1 =
* Fixed an obvious bug where the order amount minimum was never being satisfied.

= 1.3 =
* Updated to work with Shopp 1.3.x
* Added support for individual product minimums

= 1.2.1 =
* Started adding i18l support.
* Check if there are items in the cart before displaying the "minimum" error message to the customer.

= 1.2 =
* Fixed a bug where the minimum error would occur for all non-USD currencies.

= 1.1 =
* Added the ability to choose between minimum order amount or minimum quantity

= 1.0 =
* First release, huzzah!

== Upgrade Notice ==

= 1.1 =
This new version supports minimum order quantity.
