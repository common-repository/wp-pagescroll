=== Plugin Name ===
Contributors: deambulando
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business=neo22s%40gmail%2ecom&lc=EUR&item_name=wp-pagescroll&amount=5.00&currency_code=EUR&no_note=1&no_shipping=2&rm=1&weight_unit=lbs&bn=PP%2dBuyNowBF%3abtn_buynowCC_LG%2egif%3aNonHosted
Tags: navigation, wp-pagenavi, page, paginator, ajax, pagination, scrolling, scroll, endless, reading, infinite
Requires at least: 2.8.4
Tested up to: 2.9
Stable tag: 0.3

Infinite scroll for your site with “paginator” and an easy way to go to the top/botttom.

== Description ==

I tried [Infinite Scroll](http://wordpress.org/extend/plugins/infinite-scroll/) and I liked so much! I think is a really good idea, but has some "problems".

The user loses the perception of "In witch page am I" and also the people that is not using JavaScript can't browse your site properly. Also bots couldn't follow  your entire site.

[Demo](http://neo22s.com/) 

**Solution:**

We need a pagination that is updating meanwhile I'm scrolling down/up.

For pagination in WordPress we have the great work of Lester on [WP-PageNavi](http://wordpress.org/extend/plugins/wp-pagenavi/), what I did is modify it to have some HTML tags with ID. Then I updated the Infinite Scroll to do an update on that field every time the action is performed.  When that action is done I keep the position of the Y in the screen in an Array to use it later when you go  up/down. To control the scroll I wrote a simple JavaScript that control each movement on the scroll from you page an compares it with the Array of the Y, if there's a match we update WP-PageNavi.

As I said before I included [jsScroll](http://neo22s.com/jsscroll) for later making easier to the user going to the top or bottom :D


== Installation ==

To install it you can do as any other plugin of WP.

**NOTE:** This will install to you only one plugin but with 2 different pages of configuration.

* Upload (you can do it to from the control panel) it to your /wp-content/plugins/ folder and activate it!
* The you need to configure Infinite Scroll (be carefull doesn't work with all the themes)
* Change something in WP-PageNavi if you want to
* Check your homepage it will be in the corner.
*To changes styles please open wp-pagescroll.css



== Frequently Asked Questions ==

= Support? =

Support can be found in the [forum](http://forum.neo22s.com)

And in the sites of [Infinite Scroll](http://wordpress.org/extend/plugins/infinite-scroll/) and [WP-PageNavi](http://wordpress.org/extend/plugins/wp-pagenavi/)


== Screenshots ==

1. Screenshot of the navigation

== Changelog ==

= 0.3 =
* If you click in a page number scrolls to that page
= 0.2 =
* New style sheet from [Infectedfx.net](http://infectedfx.net/10-estilos-de-paginacion-gratis-para-usar-con-wp-pagenavi/2009/)
* Not need of edit footer.php
* Function wp-pagenave renamed to wp-pagescroll
= 0.1 =
* First version
