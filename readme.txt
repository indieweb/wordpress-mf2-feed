=== MF2 Feeds ===
Contributors: pfefferle, dshanske, indieweb
Donate link: https://opencollective.com/indieweb
Tags: microformats, mf2, jf2, rel-alternate, indieweb
Requires at least: 5.2
Tested up to: 6.2
Stable tag: 3.1.1
Requires PHP: 5.6
License: MIT
License URI: http://opensource.org/licenses/MIT

Add Microformats2 Feeds for WordPress

== Description ==

Provides a [Microformats2 JSON](http://microformats.org/wiki/microformats2-parsing) "Feed" for every WordPress URL, and helps to allow other sites to get pre-parsed [Microformats-JSON](https://indieweb.org/jf2) even if the theme
does not support Microformats2.

[Microformats2](https://indieweb.org/microformats) are a key [building-block](https://indieweb.org/Category:building-blocks) of the IndieWeb, but it is very hard (if not impossible) to get Microformats2 as a core feature for all WordPress themes. There are several themes that are supporting Microformats2, but everyone should choose his prefered theme and should not be limited to use one of the [few community themes](https://indieweb.org/WordPress/Themes). After [a lot of discussions](https://github.com/indieweb/wordpress-uf2/issues/30) and some different plugin approaches, we are trying to provide an alternate ([`rel=altenate`](https://indieweb.org/rel-alternate)) representation of the microformatted HTML.

The plugin is inspired by the URL design of [p3k](https://github.com/aaronpk/p3k) of [@aaronpk](https://github.com/aaronpk).

p3k Example:

* Original: <https://aaronparecki.com/2018/07/30/18/xray-updates>
* Microformats2 JSON: <https://aaronparecki.com/2018/07/30/18/xray-updates.json>
* JF2: <https://aaronparecki.com/2018/07/30/18/xray-updates.jf2>

WordPress Example:

* Original: <https://notiz.blog/2013/06/18/the-rise-of-the-indieweb/>
* Microformats2 JSON: <https://notiz.blog/2013/06/18/the-rise-of-the-indieweb/feed/mf2>
* JF2: <https://notiz.blog/2013/06/18/the-rise-of-the-indieweb/feed/jf2>

== FAQ ==

= What are Microformats 2? =

Microformats are a simple way to markup structured information in HTML using classes. WordPress incorporates some classic Microformats. Microformats 2 supersedes classic microformats.

== Installation ==

Follow the normal instructions for [installing WordPress plugins](https://codex.wordpress.org/Managing_Plugins#Installing_Plugins).

= Automatic Plugin Installation =

To add a WordPress Plugin using the [built-in plugin installer](https://codex.wordpress.org/Administration_Screens#Add_New_Plugins):

1. Go to [Plugins](https://codex.wordpress.org/Administration_Screens#Plugins) > [Add New](https://codex.wordpress.org/Plugins_Add_New_Screen).
1. Type "`mf2-feed`" into the **Search Plugins** box.
1. Find the WordPress Plugin you wish to install.
    1. Click **Details** for more information about the Plugin and instructions you may wish to print or save to help setup the Plugin.
    1. Click **Install Now** to install the WordPress Plugin.
1. The resulting installation screen will list the installation as successful or note any problems during the install.
1. If successful, click **Activate Plugin** to activate it, or **Return to Plugin Installer** for further actions.

= Manual Plugin Installation =

There are a few cases when manually installing a WordPress Plugin is appropriate.

* If you wish to control the placement and the process of installing a WordPress Plugin.
* If your server does not permit automatic installation of a WordPress Plugin.
* If you want to try the [latest development version](https://github.com/indieweb/wordpress-mf2-feed).

Installation of a WordPress Plugin manually requires FTP familiarity and the awareness that you may put your site at risk if you install a WordPress Plugin incompatible with the current version or from an unreliable source.

Backup your site completely before proceeding.

To install a WordPress Plugin manually:

* Download your WordPress Plugin to your desktop.
    * Download from [the WordPress directory](https://wordpress.org/plugins/mf2-feed/)
    * Download from [GitHub](https://github.com/indieweb/wordpress-mf2-feed/releases)
* If downloaded as a zip archive, extract the Plugin folder to your desktop.
* With your FTP program, upload the Plugin folder to the `wp-content/plugins` folder in your WordPress directory online.
* Go to [Plugins screen](https://codex.wordpress.org/Administration_Screens#Plugins) and find the newly uploaded Plugin in the list.
* Click **Activate** to activate it.

== Changelog ==

Project actively developed on Github at [indieweb/wordpress-mf2-feed](https://github.com/indieweb/wordpress-mf2-feed). Please file support issues there.

= 3.1.1 =

* Small tweaks and dependency updates

= 3.1.0 =

* Support Content Negotiation

= 3.0.0 =

* Refactored to match the configuration of feeds built into WordPress
* Bumped PHP Version requirement to PHP5.6 to match WordPress 5.3
* Bumped minimum WordPress version to 5.2 as this allows for the version of get_content that includes a $post parameter
* Fixed incorrect PHPCS configuration
* Enabled JSON Pretty Print by default as originally disabled due a PHP5.4 requirement
* Changed Post Item Generation Class to use WordPress functions instead of directly accessing the data where applicable
* Adjusted jf2 feed to comply with jf2feed spec (https://jf2.spec.indieweb.org/#jf2feed)

= 2.1.0 =

* Fixed JSON output
* Fixed "flush rewrite rules" again

= 2.0.1 =

* Fixed "flush rewrite rules"
* Added filter to extend the mf2/jf2 data

= 2.0.0 =

* Complete re-write to match the latest ideas of rel-alternate: https://github.com/indieweb/wordpress-uf2/issues/38

= 1.0.0 =

* Initial plugin
