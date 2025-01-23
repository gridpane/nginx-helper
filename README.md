# Nginx Helper #
[![Project Status: Active – The project has reached a stable, usable state and is being actively developed.](https://www.repostatus.org/badges/latest/active.svg)](https://www.repostatus.org/#active)

**Contributors:** jeffcleverley(GridPane), rtcamp, rahul286, saurabhshukla, manishsongirkar36, faishal, desaiuditd, darren-slatten, jk3us, daankortenbach, telofy, pjv, llonchj, jinnko, weskoop, bcole808, gungeekatx, rohanveer, chandrapatel, gagan0123, ravanh, michaelbeil, samedwards, niwreg, entr, nuvoPoint, iam404, rittesh.patel, vishalkakadiya, BhargavBhandari90, vincent-lu, murrayjbrown, bryant1410, 1gor, matt-h, pySilver, johan-chassaing, dotsam, sanketio, petenelson, nathanielks, rigagoogoo, dslatten, jinschoi, kelin1003, vaishuagola27, rahulsprajapati, Joel-James, utkarshpatel, gsayed786, shashwatmittal, sudhiryadav, thrijith, stayallive, jaredwsmith, abhijitrakas, umeshnevase, sid177, souptik, arafatkn, subscriptiongroup, akrocks,  gnif, jeffcleverley(GridPane)

**Tags:** nginx, cache-purge, fastcgi, permalinks, redis-cache

**Requires at least:** 3.0

**Tested up to:** 6.7

**Stable tag:** 9.9.10

**License:** GPLv2 or later (of-course)

**License URI:** http://www.gnu.org/licenses/gpl-2.0.html


Cleans nginx's fastcgi/proxy cache or redis-cache whenever a post is edited/published. Also does a few more things.

## Description ##

1. Removes `index.php` from permalinks when using WordPress with nginx.
1. Adds support for purging redis-cache when used as full-page cache created using [nginx-srcache-module](https://github.com/openresty/srcache-nginx-module#caching-with-redis)
1. Adds support for nginx fastcgi_cache_purge & proxy_cache_purge directive from [module](https://github.com/FRiCKLE/ngx_cache_purge "ngx_cache_purge module"). Provides settings so you can customize purging rules.
1. Adds support for nginx `map{..}` on a WordPress-multisite network installation. Using it, Nginx can serve PHP file uploads even if PHP/MySQL crashes. Please check the tutorial list below for related Nginx configurations.


## Installation ##

Automatic Installation

1. Log in to your WordPress admin panel, navigate to the Plugins menu and click Add New.
1. In the search field type “Nginx Helper” and click Search Plugins. From the search results, pick Nginx Helper and click Install Now. Wordpress will ask you to confirm to complete the installation.

Manual Installation

1. Extract the zip file.
1. Upload them to `/wp-content/plugins/` directory on your WordPress installation.
1. Then activate the Plugin from Plugins page.

## Frequently Asked Questions ##

### FAQ - Installation/Comptability ###

**Q. Will this work out of the box?**

No. You need to make some changes at the Nginx end.

**Q. Can I set the cache type using wp-config.php constants**

Nginx Helper only helps purge the cache, but it needs to know what type of Nginx page caching you are using.

You can set these in the nginx-helper admin settings, or control them using constants defined in the `wp-config.php` file:

```php
# Tell the plugin you are using fastcgi caching

define( ' RT_WP_NGINX_HELPER_CACHE_METHOD', 'enable_fastcgi' );

# Tell the plugin you are using redis caching

define( ' RT_WP_NGINX_HELPER_CACHE_METHOD', 'enable_redis' );
```

### FAQ - Nginx Fastcgi Cache Purge ###

**Q. There's a 'purge all' button? Does it purge the whole site?**

Well that depends on your server configuration. 

There are three options

1. Purge by GET request - using the FRiCKLE ngx_cache_purge module
2. Purge by GET request - using the Torden ngx_cache_purge module
3. Delete local server cache files

If your Nginx server has a the same webserver system user (likely www-data) as your sites directory/files owner then option 3 will purge all by physically emptying the cache directory. It is set by default to `/var/run/nginx-cache/`.
If your server has multiple sites, then this would be a security risk however, so it is likely the Nginx User and Site system user will differ... and purge all will not work.

If your cache directory is different, you can override this in your wp-config.php by adding:

```php
define( 'RT_WP_NGINX_HELPER_CACHE_PATH', '/path/to/your/nginx-cache/' );
```

Replace the path with your own.

If your Nginx server is compiled with the FRiCKLE ngx_cache_purge module, then Purge All is not available

If your Nginx server is compiled with the Torden ngx_cache_purge module, then Purge All will work.

**Q. Can I set the fastcgi purge type using constants**

You can set the purge type from the nginx-helper wp-admin settings page, or use a constants defined in the `wp-config.php:

```php
# Delete local server cache files with unlink_files

define( 'RT_WP_NGINX_HELPER_PURGE_METHOD', 'unlink_files' );

# Delete cache using GET requests when Nginx is compiled with FRiCKLE ngx_cache_purge module

define( 'RT_WP_NGINX_HELPER_PURGE_METHOD', 'get_request' );

# Delete cache using GET requests when Nginx is compiled with Tordent ngx_cache_purge module

define( 'RT_WP_NGINX_HELPER_PURGE_METHOD', 'get_request_torden' );
```

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

### FAQ - Nginx Redis Cache ###

**Q. What connection settings does Nginx Helper suppor for purging the cache?**

The plugin supports:
* Hostname + Port (will be ignored if Unix socket is set)
* Unix sockets (will override Hostname + Port)
* Redis Prefix 
* Redis Databases
* Redis ACLs (username and password)

You can set these parameters via the Nginx Helper wp-admin settings

**Q. Can I set the Redis Connection parameters using constants**

Yes you can, setting them via constant will override these settings in the wp-admin

```php
# If using hostname and port both must be set - either in config or via wp-admin

define( 'RT_WP_NGINX_HELPER_REDIS_HOSTNAME', '127.0.0.1' );
define( 'RT_WP_NGINX_HELPER_REDIS_PORT', '6379' );

# If Unix Socket is set then hostname and port will be ignored.

define( 'RT_WP_NGINX_HELPER_REDIS_UNIX_SOCKET', '/run/redis/redis.sock' );

# Prefix and database allow for some degree of cache isolation for performance

define( 'RT_WP_NGINX_HELPER_REDIS_PREFIX', 'nginx-cache:' );
define( 'RT_WP_NGINX_HELPER_REDIS_DATABASE', '0' );

# Username and password require Redis ACLs but allow for secure cache isolation

define( 'RT_WP_NGINX_HELPER_REDIS_USERNAME', 'user' );
define( 'RT_WP_NGINX_HELPER_REDIS_PASSWORD', 'password' );
```

**Q. Can I override the redis socket path, username, password?**

Yes, you can force override the redis socket path, username, password by defining constant in wp-config.php. For example:

```php
define( 'RT_WP_NGINX_HELPER_REDIS_UNIX_SOCKET', '/var/run/redis/redis.sock' );

define( 'RT_WP_NGINX_HELPER_REDIS_USERNAME', 'admin' );

define( 'RT_WP_NGINX_HELPER_REDIS_PASSWORD', 'admin' );
```

### FAQ - Nginx Map ###

**Q. My multisite already uses `WPMU_ACCEL_REDIRECT`. Do I still need Nginx Map?**

Definitely. `WPMU_ACCEL_REDIRECT` reduces the load on PHP, but it still ask WordPress i.e. PHP/MySQL to do some work for static files e.g. images in your post. Nginx map lets nginx handle files on its own bypassing wordpress which gives you much better performance without using a CDN.

**Q. I am using X plugin. Will it work on Nginx?**

Most likely yes. A wordpress plugin, if not using explicitly any Apache-only mod, should work on Nginx. Some plugin may need some extra work.


### FAQ - WP-CLI ###

**Q. How can I update the options using WP-CLI?**

```shell
wp option patch update rt_wp_nginx_helper_options <option_name> <option_value>
```


### Still need help! ###

Please post your problem in the github issues.

## Screenshots ##

### 1. Nginx plugin settings ###

### 2. Remaining settings ###

## Changelog ##

### 9.9.10 ###

Pulled in rtCamp fixes from their version - 2.3.0 and 2.3.1

* Update the contributors list and tags for the plugin. [#378](https://github.com/rtCamp/nginx-helper/issues/378) - by [Vedant Gandhi](https://github.com/Vedant-Gandhi)
* Disable the purge functionality when importing data. [#52](https://github.com/rtCamp/nginx-helper/pull/52) - by [Vedant Gandhi](https://github.com/Vedant-Gandhi)
* Added option to preload cache for all Post and Pages. [#47](https://github.com/rtCamp/nginx-helper/pull/47) - by [Vedant Gandhi](https://github.com/Vedant-Gandhi)
* Added the capability to purge Amp URL's. [#135](https://github.com/rtCamp/nginx-helper/pull/135) - by [Vedant Gandhi](https://github.com/Vedant-Gandhi)
* Add capability to allow modifying options using WP-CLI. [#307](https://github.com/rtCamp/nginx-helper/pull/307) - by [Vedant Gandhi](https://github.com/Vedant-Gandhi)
* Fix the plugin crash issue due to internationalization after upgrading to WordPress Version 6.7. [#364](https://github.com/rtCamp/nginx-helper/pull/364) - by [Vedant Gandhi](https://github.com/Vedant-Gandhi)


### 9.9.9 ###

* Add support for adding Username, Password support for Redis. It also includes the support for Unix Socket Path for Redis along with Database selection - by [Jeff Cleverley](https://github.com/gridpane)
* Added exclude filters for cache purging of home page - by [Jeff Cleverley](https://github.com/gridpane)
* Added Ability to configure all options by Constants - by [Jeff Cleverley](https://github.com/gridpane)
* Added Ability to purge fastcgi cache purge using the Torden Module - by [Jeff Cleverley](https://github.com/gridpane)
* All Previous rtCamp Nginx-Helper code up to 2.5.0