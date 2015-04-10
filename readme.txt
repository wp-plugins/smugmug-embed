=== SmugMug Embed ===
Contributors: twicklund
Donate link: http://www.wicklundphotography.com/smugmugembed-wordpress-plugin
Tags: SmugMug, Smug Mug, images, embed, integration
Requires at least: 3.2
Tested up to: 4.1.1
Stable tag: 2.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Allows users to search and embed images into posts or pages directly from their SmugMug accounts.

== Description ==

Allows users to search and embed images into posts or pages directly from their SmugMug accounts.

This plugin adds a tab into the media manager which gives authors access to a linked Smug Mug account.

The plugin first authorizes the user to a smug mug account.  In the settings screen, the administrator decides which galleries will be available to the author, the sizes available, and several other options.
 

== Installation ==

1. Unzip the file content into the /wp-content/plugings/smugmugembed directory.
2. Activate the SmugMug Embed plugin in the Plugins menu. 
3. After the plugin is activated, you will find SmugMug Embed in the Plugins menu of the Admin Panel.

== Frequently Asked Questions ==

= How does an admin decide which galleries to display?
On the settings page of SmugMug Embed, administrators choose which galleries they want to display on the embed form by placing a check mark in the box next to the gallery name.

= No galleries are being displayed on the settings page...What is happening?
The plugin must first authenticate with smug mug.  If this has not been done, please click the "Start Activation With SmugMug" button.
If the plugin has been authenticated in the past and something is still wrong, click the "Delete SmugMug Authentication" button on the setting page for SmugMug Embed.

= What is the process to authenticate with SmugMug?
1.  Click the "Start Authentication With SmugMug" button.
2.  Click the "Click here to log into SmugMug to approve access".  This will open a new window on SmugMug.com which will ask the user to verify credentials.
3.  Enter the username and password when prompted (if not already logged into SmugMug).  Once complete, the screen will display "SmugMugEmbed was added to your Authorized Applications."
4.  Close the SmugMug window.
5.  Back on the SmugMug Embed settings page, click the "Authorization Complete...Let's finalize this" button.
6.  The window will refresh in 5 seconds.  If not, click the "Got the key" button.

= Why can locked galleries only display thumbnails?
A locked gallery requires a password to view the sized images on SmugMug.  No mechanism exists to pass the gallery password to SmugMug, so only thumbnails can be displayed.

= Why does my gallery name have "thumbnails only" next to it in the drop down list of available galleries?
The gallery is locked.



== Screenshots ==



== Changelog ==

= 2.0 =
* Added support for XL, X2Large, and X3Large
* Added functionality to reload images and galleries on demand.  
  These are cached once hit and the cache is held hourly to increase 
  performance on large galleries.  Users have requested the ability 
  to reload this cache on demand
* Added functionality to uncheck images once inserted into a post.  
  Before, the same images just inserted would still be checked 
  if a user selected to add more images.  However, the settings will still remain.
* Added the ability to set the default image alignment in the settings tab 
* Fixed an issue which occurred when a user selected a gallery from the 
  drop down in the media browser, but then chose "Select Gallery".  
  The system would throw an error before
* Added ability to select all images from the media chooser SmugMug Embed form
* If a gallery had a hidden image, then the plugin would not embed any 
  image after the hidden image.  This is resolved
* SME now respects the SmugMug hidden flag on photos
* SME now has the ablitity to create shortcode that will use Wordpress's out of the box galleries style
* Added a progress indicator on several events in the Embed from SmugMug form

= 1.0 =
* Added ability to link to Cart in SmugMug.  The image is automatically added to the cart!
* Added ability to open the link in a new window
* Fixed several labels in both the admin panel and the embed form to be more user friendly

= 0.93 =
* Fixed an issue causing the white screen of death at plugin start (thanks Lord Laughter)
* Fixed an issue with pathing which threw errors on WP installs not at the root directory

= 0.92 =
* adding uninstall.php to clean up options

= 0.9 =
* SmugMug Embed initial release


== More Information ==
For more information, please visit http://www.wicklundphotography.com/servicesweb-designdevelopmentsmugmugembed-wordpress-plugin/

Consider donating to the development and maintenance of this plugin.

Thanks.
