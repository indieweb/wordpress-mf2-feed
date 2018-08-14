=== MF2 Feeds ===
Contributors: pfefferle, indieweb
Donate link: https://notiz.blog/donate/
Tags: microformats, mf2, jf2, rel-alternate, indieweb
Requires at least: 4.7
Tested up to: 4.9.8
Stable tag: 2.0.0
Requires PHP: 5.3
License: MIT
License URI: http://opensource.org/licenses/MIT

Microformats2 Feeds for WordPress

== Description ==

[Microformats2](https://indieweb.org/microformats) are a key [building-block](https://indieweb.org/Category:building-blocks) of the IndieWeb, but it is very hard (if not impossible) to get Microformats2 as a core feature for all WordPress themes. There are several themes that are supporting Microformats2, but everyone should choose his prefered theme and should not be limited to use one of the [few community themes](https://indieweb.org/WordPress/Themes). After [a lot of discussions](https://github.com/indieweb/wordpress-uf2/issues/30) and some different plugin approaches, we are trying to provide an alternate ([`rel=altenate`](https://indieweb.org/rel-alternate)) representation of the microformatted HTML.

The `mf2-feed` plugin provides a [Microformats2 JSON](http://microformats.org/wiki/microformats2-parsing) "Feed" for every WordPress URL, and helps to get a pre-parsed [Microformats-JSON](https://indieweb.org/jf2) even if the theme does not support Microformats2.

The plugin is inspired by the URL design of [p3k](https://github.com/aaronpk/p3k) of [@aaronpk](https://github.com/aaronpk).

p3k Example:

* <http://aaronparecki.com/articles/2015/01/22/1/why-not-json>
* <http://aaronparecki.com/articles/2015/01/22/1/why-not-json.json>

WordPress Example:

* Original: <http://notizblog.org/2013/06/18/the-rise-of-the-indieweb/>
* Microformats2 JSON: <http://notizblog.org/2013/06/18/the-rise-of-the-indieweb/feed/mf2>
* JF2: <http://notizblog.org/2013/06/18/the-rise-of-the-indieweb/feed/jf2>

== FAQ ==

= What are Microformats 2? =

Microformats are a simple way to markup structured information in HTML. WordPress incorporates some classic Microformats. Microformats 2 supersedes class microformats.

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

= 2.0.0 =

* Complete re-write to match the latest ideas of rel-alternate: https://github.com/indieweb/wordpress-uf2/issues/38

= 1.0.0 =

* Initial plugin
