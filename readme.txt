=== Shopp Minimum Order ===
Contributors: crunnells
Donate link: http://www.chrisrunnells.com/shopp/minimum-order/
Tags: shopp, minimum order, minimum, order, e-commerce
Requires at least: 3.0
Tested up to: 3.4.1
Stable tag: 4.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin allows you to set a minimum order amount in the Shopp e-commerce checkout process.

== Description ==

Do you use Shopp? Do you want to make sure your customers order a minimum amount of items, or spend a minimum amount of money? Then the Shopp Minimum Order plugin is what you need! It will prevent customers from checking out unless they meet the minimum order requirements set by the shop owner.

*This plugin requires you to be running at least Shopp 1.2.*

Once you’ve installed and activated the plugin, there will be a link in the Shopp > Setup menu where you configure the minimum order values. Set the minimum type you want to use, enter a number, then hit Save and you’re done!

The plugin will now generate an error message during the Checkout process if the order does not meet the requirements you set. We highly recommend that you notify your customers (via a message on the Shopping Cart page for example) that you have a minimum order requirement, so they’re not surprised when they try to check out.

I’ve got some ideas for improving this plugin down the road, so I’ll be working on it as I have time.

== Installation ==

Installation is pretty straightforward. 

1. Upload the `shopp-minimum-order` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Set your minimum order amounts on the Setup > Minimum Order page.

== Frequently Asked Questions ==

= Will this plugin work in earlier versions of Shopp? =

Nope! This plugin uses the Shopp API from version 1.2 (and later). It *may* work in earlier versions, but it hasn't been tested. 

== Screenshots ==

1. This screen shot description corresponds to screenshot-1.(png|jpg|jpeg|gif). Note that the screenshot is taken from
the directory of the stable readme.txt, so in this case, `/tags/4.3/screenshot-1.png` (or jpg, jpeg, gif)
2. This is the second screen shot

== Changelog ==

= 1.1 =
* Added the ability to choose between minimum order amount or minimum quantity

= 1.0 =
* First release, huzzah!

== Upgrade Notice ==

= 1.1 =
This new version supports minimum order quantity.
