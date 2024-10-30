=== Blrt WP Embed ===
Contributors: Blrt
Tags: Blrt,Embed,oembed
Requires at least: 2.9
Stable tag: 1.6.9
Tested up to: 4.7.5
License: GPL2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Enable embedding Blrts in your pages and posts by simply pasting in the URL of a public or private Blrt - just like YouTube videos.

== Description ==
<ul>
<li>Enable embedding Blrts in your WordPress pages and posts by simply pasting in the URL of a public or private Blrt - just like YouTube videos are embedded utilising oEmbed.
<li>Create Blrt Galleries with multiple Blrts and embed them in any web page.
<li>Provide fallback videos hosted on YouTube or Vimeo for users without Google Chrome
<li>Easily access Blrt Web from within WordPress
</ul>

All things Blrt
<ul>
<li>Learn all about Blrt at https://www.blrt.com
<li>For support visit http://help.blrt.com
<li>Use Blrt on the Web at https://web.blrt.com/
<li>Download Blrt for iOS at https://www.blrt.com/ios
<li>Download Blrt for Android at https://www.blrt.com/android
</ul>

== Frequently Asked Questions ==
Visit http://help.blrt.com for FAQs and support

== Changelog ==

= 1.6.9
* Fix inconsistencies with asset locations

= 1.6.8
* Automatically load local assets if dev mode is enabled
* Register Open Sans since WordPress has now removed it
* Fix TinyMCE bug which was affecting other textareas

= 1.6.5
* Better placeholder layout

= 1.6.4
* Fix placeholder images
* Fix readme typo
* Add support copy to admin footer

= 1.6.1
* Fix CDN issue when loaded on non-https site

= 1.6
* Improve display of various admin pages
* Use SVG for Blrt icons
* Add Blrt promos to admin pages
* Overhaul TinyMCE editor display and functionality
* Add proper shortcode for single Blrts
* Reduce requests for Blrt shortcode
* Optimize a lot of CSS, JS and PHP

= 1.4.11
* Remove some hidden errors on edit/add gallery page
* Ask about unsaved changes on edit/add gallery page
* Improve display of modified date
* Add graphical preview to shortcode preview
* Confine certain admin assets and actions to Blrt admin pages

= 1.4.8
* Fix wrong database table version

= 1.4.7
* Update database, remove unused columns
* Allow renaming galleries
* Highlight correct submenu item when editing gallery
* Update some backend copy
* Fix include error for TinyMCE view on older PHP versions
* Enable Blrt web toolbar icon by default
* Improve Blrt URL validation

= 1.3.9
* Fix PHP error on WordPress =< 4.3.0 related to `style_loader_tag` parameter amount
* Add seperate version number for assets to prevent unnecessary asset versions

= 1.3.7
* Improve mobile video responsive styles

= 1.3.6
* Use CDNs with fallback for CSS files as well
* Update database structure
* Allow switching mobile view between 'snippet' and 'video' in shortcode

= 1.3.3
* Allow multiple galleries to be loaded on a single page
* Added shortcode builder to 'Add New Gallery' screen
* Use different directories on assets CDN for different plugin versions

= 1.3
* Added settings page
* Fix menu icon hover issues and quality
* Improved fallback video management
* Added ability to change order of gallery Blrts by dragging
* Added Blrt Web toolbar icon option
* Only show message for deleted galleries to editors
* Made plugin available to users with editor role and above
* Improve asset loading and fallback code

= 1.2.2 =
* Fixed some incorrectly named menu items
* Added Blrt Web embed page

= 1.2 =
* Changed mobile view to keep player so that fallback videos can be watched on mobile devices
* Made fallback videos autoplay when selected
* Load external libraries from CDN to improve speed, with local fallbacks
* Load assets once, in footer, to improve loading speed

= 1.1.1 =
* Fix snippets view displaying empty page