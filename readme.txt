=== Nginx Helper ===
Contributors:  jeffcleverley(GridPane), rtcamp, rahul286, saurabhshukla, manishsongirkar36, faishal, desaiuditd, darren-slatten, jk3us, daankortenbach, telofy, pjv, llonchj, jinnko, weskoop, bcole808, gungeekatx, rohanveer, chandrapatel, gagan0123, ravanh, michaelbeil, samedwards, niwreg, entr, nuvoPoint, iam404, rittesh.patel, vishalkakadiya, BhargavBhandari90, bryant1410, 1gor, matt-h, dotsam, nathanielks, rigagoogoo, dslatten, jinschoi, kelin1003, vaishuagola27, rahulsprajapati, utkarshpatel, gsayed786, shashwatmittal, sudhiryadav, thrijith, stayallive, jaredwsmith, abhijitrakas, umeshnevase, sid177, souptik, arafatkn, subscriptiongroup, akrocks, vedantgandhi28
Tags: nginx, cache-purge, fastcgi, permalinks, redis-cache
License: GPLv2 or later (of-course)
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Requires at least: 3.0
Tested up to: 6.7
Stable tag: 9.9.10

Cleans nginx's fastcgi/proxy cache or redis-cache whenever a post is edited/published. Also does a few more things.

== Description ==

1. Removes `index.php` from permalinks when using WordPress with nginx.
1. Adds support for purging redis-cache when used as full-page cache created using [nginx-srcache-module](https://github.com/openresty/srcache-nginx-module#caching-with-redis)
1. Adds support for nginx fastcgi_cache_purge & proxy_cache_purge directive from [module](https://github.com/FRiCKLE/ngx_cache_purge "ngx_cache_purge module"). Provides settings so you can customize purging rules.
1. Adds support for nginx `map{..}` on a WordPress-multisite network installation. Using it, Nginx can serve PHP file uploads even if PHP/MySQL crashes. Please check the tutorial list below for related Nginx configurations.

== Installation ==

Automatic Installation

1. Log in to your WordPress admin panel, navigate to the Plugins menu and click Add New.
1. In the search field type “Nginx Helper” and click Search Plugins. From the search results, pick Nginx Helper and click Install Now. Wordpress will ask you to confirm to complete the installation.

Manual Installation

1. Extract the zip file.
1. Upload them to `/wp-content/plugins/` directory on your WordPress installation.
1. Then activate the Plugin from Plugins page.

== Frequently Asked Questions ==

= FAQ - Installation/Comptability =

**Q. Will this work out of the box?**

No. You need to make some changes at the Nginx end. Documentation coming...

= FAQ - Nginx Fastcgi Cache Purge =

**Q. There's a 'purge all' button? Does it purge the whole site?**

Yes, it does.

If your web application and nginx server are both the same user (www-user) it can empty the cache directory. It is set by default to `/var/run/nginx-cache/`

If your cache directory is different, you can override this in your wp-config.php by adding
`define('RT_WP_NGINX_HELPER_CACHE_PATH','/var/run/nginx-cache/');`

If you are on a webserver where the application system user differs from the webserver user, you Nginx will need to be compiled with the Torden version of `ngx_cache_purge`

**Q. Does it work for custom posts and taxonomies?**

Yes. It handles all post-types the same way.

**Q. How do I know my Nginx config is correct for fastcgi purging?**

Manually purging any page from the cache, by following instructions in the previous answer.

Version 1.3.4 onwards, Nginx Helper adds a comment at the end of the HTML source ('view source' in your favourite browser):
`&lt;!--Cached using Nginx-Helper on 2012-10-08 07:01:45. It took 42 queries executed in 0.280 seconds.--&gt;`. This shows the time when the page was last cached. This date/time will be reset whenever this page is purged and refreshed in the cache. Just check this comment before and after a manual purge.

As long as you don't purge the page (or make changes that purge it from the cache), the timestamp will remain as is, even if you keep refreshing the page. This means the page was served from the cache and it's working!

The rest shows you the database queries and time saved on loading this page. (This would have been the additional resource load, if you weren't using fast-cgi-cache.)

**Q. I need to flush a cached page immediately! How do I do that?**

Nginx helper plugin handles usual scenarios, when a page in the cache will need purging. For example, when a post is edited or a comment is approved on a post.

To purge a page immediately, follow these instructions:

* Let's say we have a page at the following domain: http://yoursite.com/about.
* Between the domain name and the rest of the URL, insert '/purge/'.
* So, in the above example, the purge URL will be http://yoursite.com/purge/about.
* Just open this in a browser and the page will be purged instantly.
* Needless to say, this won't work, if you have a page or taxonomy called 'purge'.

= FAQ - Nginx Redis Cache =

**Q. Can I override the redis hostname, port and prefix?**

Yes, you can force override the redis hostname, port or prefix by defining constant in wp-config.php. For example:

```
define('RT_WP_NGINX_HELPER_REDIS_HOSTNAME', '10.0.0.1');

define('RT_WP_NGINX_HELPER_REDIS_PORT', '6000');

define('RT_WP_NGINX_HELPER_REDIS_PREFIX', 'page-cache:');
```

**Q. Can I override the redis socket path, username, password?**

Yes, you can force override the redis socket path, username, password by defining constant in wp-config.php. For example:

```php
define( 'RT_WP_NGINX_HELPER_REDIS_UNIX_SOCKET', '/var/run/redis/redis.sock' );

define( 'RT_WP_NGINX_HELPER_REDIS_USERNAME', 'admin' );

define( 'RT_WP_NGINX_HELPER_REDIS_PASSWORD', 'admin' );
```

= FAQ - Nginx Map =

**Q. My multisite already uses `WPMU_ACCEL_REDIRECT`. Do I still need Nginx Map?**

Definitely. `WPMU_ACCEL_REDIRECT` reduces the load on PHP, but it still ask WordPress i.e. PHP/MySQL to do some work for static files e.g. images in your post. Nginx map lets nginx handle files on its own bypassing wordpress which gives you much better performance without using a CDN.

**Q. I am using X plugin. Will it work on Nginx?**

Most likely yes. A wordpress plugin, if not using explicitly any Apache-only mod, should work on Nginx. Some plugin may need some extra work.


= FAQ - WP-CLI =

**Q. How can I update the options using WP-CLI?**

```shell
wp option patch update rt_wp_nginx_helper_options <option_name> <option_value>
```

= Still need help! =

Please post your problem in the github issues.

== Screenshots ==
1. Nginx plugin settings
2. Remaining settings

== Changelog ==

= 9.9.10 =

Pulled in rtCamp fixes from their version - 2.3.0 and 2.3.1

* Update the contributors list and tags for the plugin. [#378](https://github.com/rtCamp/nginx-helper/issues/378) - by [Vedant Gandhi](https://github.com/Vedant-Gandhi)
* Disable the purge functionality when importing data. [#52](https://github.com/rtCamp/nginx-helper/pull/52) - by [Vedant Gandhi](https://github.com/Vedant-Gandhi)
* Added option to preload cache for all Post and Pages. [#47](https://github.com/rtCamp/nginx-helper/pull/47) - by [Vedant Gandhi](https://github.com/Vedant-Gandhi)
* Added the capability to purge Amp URL's. [#135](https://github.com/rtCamp/nginx-helper/pull/135) - by [Vedant Gandhi](https://github.com/Vedant-Gandhi)
* Add capability to allow modifying options using WP-CLI. [#307](https://github.com/rtCamp/nginx-helper/pull/307) - by [Vedant Gandhi](https://github.com/Vedant-Gandhi)
* Fix the plugin crash issue due to internationalization after upgrading to WordPress Version 6.7. [#364](https://github.com/rtCamp/nginx-helper/pull/364) - by [Vedant Gandhi](https://github.com/Vedant-Gandhi)


= 9.9.9 =

* Add support for adding Username, Password support for Redis. It also includes the support for Unix Socket Path for Redis along with Database selection - by [Jeff Cleverley](https://github.com/gridpane)
* Added exclude filters for cache purging of home page - by [Jeff Cleverley](https://github.com/gridpane)
* Added Ability to configure all options by Constants - by [Jeff Cleverley](https://github.com/gridpane)
* Added Ability to purge fastcgi cache purge using the Torden Module - by [Jeff Cleverley](https://github.com/gridpane)
* All Previous rtCamp Nginx-Helper code up to 2.5.0