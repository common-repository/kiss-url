=== KISS URL ===
Contributors: victorjohnson
Dontae link:
Tags: url shortner, short url, shortlinks
Requires at least: 3.0
Tested up to: 3.2-beta1
Stable tag: 1.3

Automatically generates <a href="http://bit.ly">bit.ly</a> shortlink for all pages/posts.

== Description ==

Automatically generates <a href="http://bit.ly">bit.ly</a> shortlink for every posts, pages, category, archives etc. To get started, you'll need a free bit.ly user account and apiKey. Signup at: http://bit.ly/a/sign_up. 

After activating the plugin go to Settings->KISS URL enter your bit.ly username and apiKey and save.

Plugin by http://www.revood.com

== Installation == 

1. Upload the plugin to /wp-content/plugins folder
2. Activate the plugin from Appearance->Plugins menu.
3. Go to Settings->KISS URL and enter your bit.ly username and apiKey

== Changelog ==

= 1.3 =
* Fix another fatal error in 3.1

= 1.2 =
* Fixed the Fatal error: Call to undefined function submit_button() in /home/zeaks/public_html/test/wp-content/plugins/kiss-url/kiss_url.php on line 62 for WordPress 3.0.* 

= 1.1 =
* Added shortcode support. Example usage [kiss-url p=10]test[/kiss-url], where 10 is page/post ID and "test" the text to display.

= 1.0 =
* Initial release
