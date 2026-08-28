# MF2 Feeds
- Contributors: pfefferle, dshanske, indieweb
- Donate link: https://opencollective.com/indieweb
- Tags: microformats, mf2, jf2, rel-alternate, indieweb
- Requires at least: 5.3
- Tested up to: 7.1
- Stable tag: 3.2.0
- Requires PHP: 7.4
- License: MIT
- License URI: http://opensource.org/licenses/MIT

Add Microformats2 Feeds for WordPress

## Description

MF2 Feed adds a machine readable version of your posts, in the same way WordPress already offers RSS feeds. Other sites and services (readers, Webmention endpoints, IndieWeb tools) can read your content as structured data, no matter which theme you use.

Just install and activate the plugin. There are no settings.

After activation every URL of your site gets two additional feeds:

* **Microformats2 JSON** (`/feed/mf2/`): the raw [Microformats2](https://indieweb.org/microformats) data, the same format a Microformats parser would produce.
* **JF2** (`/feed/jf2/`): a simplified version of the same data, easier to read and to work with. See the [JF2 spec](https://jf2.spec.indieweb.org/).

### Examples

* Your site: `https://example.com/` → `https://example.com/feed/mf2/` and `https://example.com/feed/jf2/`
* A single post: `https://example.com/2024/01/my-post/` → `https://example.com/2024/01/my-post/feed/mf2/` and `https://example.com/2024/01/my-post/feed/jf2/` (the comments of the post are included)

A live example: <https://notiz.blog/2013/06/18/the-rise-of-the-indieweb/> and the matching [mf2](https://notiz.blog/2013/06/18/the-rise-of-the-indieweb/feed/mf2) and [jf2](https://notiz.blog/2013/06/18/the-rise-of-the-indieweb/feed/jf2) feeds.

The plugin also adds `rel="alternate"` links to the `<head>` of every page, so tools can discover the feeds on their own. And it supports content negotiation: a client that requests a post with the `Accept: application/mf2+json` (or `application/jf2+json`) header gets the JSON directly instead of the HTML.

### Why?

[Microformats2](https://indieweb.org/microformats) are a key [building block](https://indieweb.org/Category:building-blocks) of the IndieWeb. But it is very hard (if not impossible) to get them into all WordPress themes. There are a [few community themes](https://indieweb.org/WordPress/Themes) that support Microformats2, but you should be able to use the theme you like. After [a lot of discussions](https://github.com/indieweb/wordpress-uf2/issues/30) and some different plugin approaches, this plugin provides an alternate ([`rel="alternate"`](https://indieweb.org/rel-alternate)) representation instead of changing the HTML of your theme.

The URL design is inspired by [p3k](https://github.com/aaronpk/p3k) of [@aaronpk](https://github.com/aaronpk).

## FAQ

### What are Microformats2?

Microformats are a simple way to mark up structured information in HTML using classes. WordPress uses some classic Microformats out of the box. Microformats2 supersedes the classic ones. You can read more on [microformats.org](http://microformats.org/wiki/microformats2).

### What is JF2?

[JF2](https://jf2.spec.indieweb.org/) is a simpler JSON format based on Microformats2. It is easier to consume in code, so most tools prefer it.

### Do I need a special theme?

No. The feeds are generated from the post data, not from the HTML of your theme. That is the whole point of the plugin.

### Are there any settings?

No. Install, activate, done.

### The feed URLs return a 404

The plugin registers new rewrite rules on activation. If the URLs do not work, go to *Settings > Permalinks* and click *Save Changes* once. This flushes the rewrite rules.

### Can I change the feed output?

Yes, for developers there are a few filters: `mf2_feed_array` and `jf2_feed_array` for the whole feed, `mf2_entry_array` and `jf2_entry_array` for a single entry, and `mf2_feed_content_type` / `jf2_feed_content_type` for the content type.

### Where can I get help?

The plugin is developed on GitHub at [indieweb/wordpress-mf2-feed](https://github.com/indieweb/wordpress-mf2-feed). Please file support issues there.

## Installation

### From the plugin directory

1. Go to *Plugins > Add New* in your WordPress admin.
1. Search for "`mf2-feed`".
1. Click **Install Now** and then **Activate**.

### Manual installation

1. Download the plugin from [the WordPress directory](https://wordpress.org/plugins/mf2-feed/) or from [GitHub](https://github.com/indieweb/wordpress-mf2-feed/releases).
1. Upload the `mf2-feed` folder to `wp-content/plugins/` on your server.
1. Go to *Plugins* and click **Activate**.

## Changelog

Project actively developed on Github at [indieweb/wordpress-mf2-feed](https://github.com/indieweb/wordpress-mf2-feed). Please file support issues there.

### 3.2.0

* Moved the main class to `includes/` and renamed it to `Mf2_Feed` (`Mf2Feed` still works as alias)
* Added `MF2_FEED_*` constants
* Fixed the `mf2_feed_options`/`jf2_feed_options` filters, they never fired
* Fixed post names being replaced by their IDs
* Fixed missing site icon in the JF2 feed
* Removed the `get_self_link()` shim, requires WordPress 5.3 now
* Fixed the feed links on a static front page, they pointed to the page entry instead of the site feed (#19)
* Feed links are only added on the home page, the front page and single entries, not on archives (#16)
* Added `Mf2_Feed_Entry::from_post()`, entries are only built for valid posts (#10)
* Added the `mf2_*` post meta (Micropub, Post Kinds) to the entries, with a `mf2_feed_meta_properties` filter (#3)
* Updated the dev dependencies and added a `composer lint` script

### 3.1.1

* Small tweaks and dependency updates

### 3.1.0

* Support Content Negotiation

### 3.0.0

* Refactored to match the configuration of feeds built into WordPress
* Bumped PHP Version requirement to PHP5.6 to match WordPress 5.3
* Bumped minimum WordPress version to 5.2 as this allows for the version of get_content that includes a $post parameter
* Fixed incorrect PHPCS configuration
* Enabled JSON Pretty Print by default as originally disabled due a PHP5.4 requirement
* Changed Post Item Generation Class to use WordPress functions instead of directly accessing the data where applicable
* Adjusted jf2 feed to comply with jf2feed spec (https://jf2.spec.indieweb.org/#jf2feed)

### 2.1.0

* Fixed JSON output
* Fixed "flush rewrite rules" again

### 2.0.1

* Fixed "flush rewrite rules"
* Added filter to extend the mf2/jf2 data

### 2.0.0

* Complete re-write to match the latest ideas of rel-alternate: https://github.com/indieweb/wordpress-uf2/issues/38

### 1.0.0

* Initial plugin
