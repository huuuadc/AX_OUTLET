=== FiboSearch - Ajax Search for WooCommerce  ===
Contributors: damian-gora, matczar
Tags: woocommerce search, ajax search, search by sku, product search, woocommerce
Requires at least: 5.0
Tested up to: 6.3
Requires PHP: 7.0
Stable tag: 1.25.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

The most popular WooCommerce product search plugin. Gives your users a well-designed advanced AJAX search bar with live search suggestions.

== Description ==

The most popular **WooCommerce product search plugin**. It gives your users a well-designed advanced AJAX search bar with live search suggestions.

By default, WooCommerce provides a very simple search solution, without live product search or even SKU search. FiboSearch (formerly Ajax Search for WooCommerce) provides advanced search with live suggestions.

Who doesn’t love instant, as-you-type suggestions? In 2023, customers expect smart product search. Baymard Institute’s latest UX research reveals that search autocomplete, auto-suggest, or an instant search feature **is now offered on 96% of major e-commerce sites**. It's a must-have feature for every online business that can’t afford to lose customers. Why? FiboSearch helps users save time and makes shopping easier. As a result, Fibo really boosts sales.

= Features =
&#9989; **Search by product title, long and short description**
&#9989; **Search by SKU**
&#9989; Show **product image** in live search results
&#9989; Show **product price** in live search results
&#9989; Show **product description** in live search results
&#9989; Show **SKU** in live search results
&#9989; **Mobile first** – special mobile search mode for better UX
&#9989; **Details panels** with extended information – **“add to cart” button** with a **quantity field** and **extended product** data displayed on hovering over the live suggestion
&#9989; **Easy implementation** in your theme - embed the plugin using a **shortcode**, as a **menu item** or as a **widget**
&#9989; **Terms search** – search for product categories and tags
&#9989; **Search history** – the current search history is presented when the user clicked/taped on the search bar, but hasn't yet typed the query.
&#9989; **Limit** displayed suggestions – the number is customizable
&#9989; **The minimum number of characters** required to display suggestions – the number is customizable
&#9989; **Better ordering** – a smart algorithm ensures that the displayed results are as accurate as possible
&#9989; **Support for WooCommerce search results page** - after typing enter, users get the same results as in FiboSearch bar
&#9989; **Grouping instant search results by type** – displaying e.g. first matching categories, then matching products
&#9989; **Google Analytics** support
&#9989; Multilingual support including **WPML**, **Polylang** and **qTranslate-XT**
&#9989; **Personalization** of search bar and autocomplete suggestions - labels, colors, preloader, image and more

= Try the PRO version =
FiboSearch also comes in a Pro version, with a modern, inverted index-based search engine. FiboSearch Pro works up to **10× faster** than the Free version or other popular search solutions for WooCommerce.

[Upgrade to PRO and boost your sales!](https://fibosearch.com/pricing/?utm_source=readme&utm_medium=referral&utm_content=pricing&utm_campaign=asfw)

= PRO features =

&#9989; **Ultra-fast search engine** based on the inverted index – works very fast, even with 100,000+ products
&#9989; **Fuzzy search** – works even with minor typos
&#9989; **Search in custom fields** with dedicated support for ACF
&#9989; **Search in attributes**
&#9989; **Search in categories**. Supports category thumbnails.
&#9989; **Search in tags**
&#9989; **Search in brands** (We support WooCommerce Brands, Perfect Brands for WooCommerce, Brands for WooCommerce, YITH WooCommerce Brands). Supports brand thumbnails.
&#9989; **Search by variation product SKU** – also shows variable products in live search after typing in the exact matching SKU
&#9989; **Search for posts** – also shows matching posts in live search
&#9989; **Search for pages** – also shows matching posts in live search
&#9989; **Synonyms**
&#9989; **Conditional exclusion of products**
&#9989; **TranslatePress** compatible
&#9989; Professional and fast **help with embedding** or replacing the search bar in your theme
&#9989; and more...
&#9989; SEE ALL PRO [FEATURES](https://fibosearch.com/pro-vs-free/?utm_source=readme&utm_medium=referral&utm_content=features&utm_campaign=asfw)!

= Showcase =
See how it works for others: [Showcase](https://fibosearch.com/showcase/?utm_source=readme&utm_medium=referral&utm_campaign=asfw&utm_content=showcase&utm_gen=utmdc).

= Feedback =
Any suggestions or comments are welcome. Feel free to contact us via the [contact form](https://fibosearch.com/contact/?utm_source=readme&utm_medium=referral&utm_campaign=asfw&utm_content=contact&utm_gen=utmdc).

== Installation ==

1. Install the plugin from within the Dashboard or upload the directory `ajax-search-for-woocommerce` and all its contents to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Go to `WooCommerce → FiboSearch` and set your preferences.
4. Embed the search bar in your theme.

== Frequently Asked Questions ==

= How do I embed the search bar in my theme? =
There are many easy ways to display the FiboSearch bar in your theme:

– **Replacing the existing search bar with one click** - it is possible for dozens of popular themes
– **As a menu item** - in your WordPress admin panel, go to `Appearance → Menu` and add `FiboSearch bar` as a menu item
– **Using a shortcode**

`[fibosearch]`

– **As a widget** - in your WordPress admin panel, go to `Appearance → Widgets` and choose `FiboSearch`
– **As a block** - [learn how to use blocks](https://fibosearch.com/documentation/get-started/how-to-add-fibosearch-to-your-website/#add-fibosearch-with-the-dedicated-fibosearch-block) and FiboSearch together
– **Using PHP**

`<?php echo do_shortcode('[fibosearch]'); ?>`

– **We will do it for you!** - we offer free search bar implementation for Pro users. Become one now!

Or insert this function inside a PHP file (often, it is used to insert a form inside page template files):

= How do I replace the existing search bar in my theme with FiboSearch? =
We have prepared a one-click replacement of the search bar for the following themes:

*  Storefront
*  Divi
*  Flatsome
*  OceanWP
*  Astra
*  Avada
*  Sailent
*  and 43 more... See a complete list of integrated themes on [our documentation](https://fibosearch.com/documentation/themes-integrations/?utm_source=readme&utm_medium=referral&utm_campaign=asfw&utm_content=theme-integrations).


If you want to replace your search bar in another theme, please [contact our support team](https://fibosearch.com/contact/?utm_source=readme&utm_medium=referral&utm_campaign=asfw&utm_content=contact&utm_gen=utmdc).
We will assist with replacing the search bar in your theme for free after you upgrade to the Pro version.

= Can I add the search bar as a WordPress menu item? =
**Yes, you can!** Go to `Appearance → Menu`. You will see a new menu item called “FiboSearch”. Select it and click “Add to menu”. Done!

= How can I ask a question? =
You can submit a ticket on the plugin [website](https://fibosearch.com/contact/?utm_source=readme&utm_medium=referral&utm_campaign=asfw&utm_content=contact&utm_gen=utmdc) and the support team will get in touch with you shortly. We also answer questions on the [WordPress Support Forum](https://wordpress.org/support/plugin/ajax-search-for-woocommerce/).

= Do you offer customization support? =
Depending on the theme you use, sometimes the search bar requires minor improvements in appearance. We guarantee fast CSS corrections for all Pro plugin users, but we also help Free plugin users.

= Where can I find plugin settings? =
In your WordPress dashboard, go to `WooCommerce → FiboSearch`. The FiboSearch settings page is a submenu of the WooCommerce menu.

= Who is the Pro plugin version for? =
The Pro plugin version is for all online sellers looking to **increase sales** by providing an ultra-fast smart search engine to their clients.

The main difference between the Pro and Free versions is search speed and search scope. The Pro version has a new fast smart search engine. For some online stores that offer a lot of products for sale, search speed can be increased **up to 10×**, providing a whole new experience to end users.

All in all, the Pro version is dedicated to all WooCommerce shops where autocomplete suggestions work too slowly.

You can read more and compare Pro and Free features here: [Full comparison - Pro vs Free](https://fibosearch.com/pro-vs-free/).

== Screenshots ==

1. Search suggestions with a details panel
2. Search suggestions
3. Search suggestions with a details panel
4. Settings page
5. Settings page

== Changelog ==

= 1.25.0, July 06, 2023 =
* ADDED: Possibility to search for taxonomy terms regardless of accents in a phrase or term name
* ADDED: Added some new filters to change URLs of results in autocomplete and details panel
* ADDED: Compatibility with 5.x of the “WooCommerce Visibility” plugin
* ADDED: Shortcode to display search results for post types
* ADDED: Indexing images for other post types than product
* ADDED: Integration with “WooCommerceB2B” by Addify
* ADDED: Integration with “WooCommerce B2B plugin” by Code4Life
* ADDED: Stopping the search after detecting an illegal phrase
* ADDED: Troubleshooting - warning against limiting the length of database queries in the “WP Engine” environment
* FIXED: Warnings due to `open_basedir` restrictions
* FIXED: Integration with the Impreza theme - broken AJAX pagination for Grid element
* FIXED: Integration with the TheGem theme - missing search results when the “Layout Type” option is set to “Products Grid”
* FIXED: Integration with the Divi theme - mobile overlay not showing up
* FIXED: Stronger sanitization of the details panel output
* FIXED: Indexer error due to too invalid cookie
* FIXED: Elementor - no image height alignment in the “FiboSearch Posts Search Results” widget
* FIXED: Support for dynamic prices for the plugin “YITH Multi Currency Switcher for WooCommerce”
* TWEAK: New filter to manipulate a term output
* TWEAK: Improved recognition if the “B2BKing plugin” is active (under SHORTINIT mode)
* TWEAK: `DGWT_WCAS()->searchPosts()` returns empty results if index was invalid
* UPDATED: Freemius SDK v2.5.10
* UPDATED: Polish translation

= 1.24.0, May 25, 2023 =
* ADDED: Integration with the “Minimog” theme
* ADDED: Posts, pages, and taxonomy terms are included in the FiboSearch Analytics module
* ADDED: Taking into account a new feature of the dark theme in the Nave theme
* ADDED: Possibility to change the color of a search bar underlay. Only for the Pirx style
* ADDED: New search widget and extended search results for Elementor
* ADDED: TheGem theme - “Header Builder” support
* ADDED: Searching interface usable from within PHP
* FIXED: Wrong position of search icons in the history search module
* FIXED: Broken suggestions layout and detailed panel visibility when the “Minimum characters” option is set to less than 1
* FIXED: Compatibility with PHP 8.1
* FIXED: Hide unnecessary modules when constant `DGWT_WCAS_ANALYTICS_ONLY_CRITICAL` is set to true in the FiboSearch Analytics module
* FIXED: Incorrect display of information about constants on the debug page
* FIXED: Other minor bugs in the FiboSearch Analytics module
* FIXED: Integration with the Astra theme - support for version 4.1.0 of the Astra Addon
* FIXED: Integration with the Minimog theme - wrong position of the search history wrapper
* FIXED: Integration with the Enfold theme - the search engine icon disappears when the page finishes loading
* FIXED: A HTML tag `<br>` was unnecessarily stripped in the description in the details panel
* FIXED: The voice search feature - overlapping icons and disabling functionality on Safari
* FIXED: Broken integrations with filter plugins when “home_url” was empty
* FIXED: Incorrect log file in failed index build reports
* FIXED: Index build failed because of a missing wp_cache_flush_group() function
* FIXED: Indexer error “[Searchable index] Database error “Duplicate entry '16777215'...”
* FIXED: Indexer error because a column was too short in the database
* FIXED: Uncode theme - incorrect results on the search results page
* FIXED: Slow index building when the TranslatePress plugin is active
* FIXED: PHP exceptions were thrown from the Redis Object Cache plugin and it stopped the indexer-building process
* TWEAK: New filter to manipulate taxonomy term data that are indexed
* UPDATED: French translation
* UPDATED: Freemius SDK v2.5.8
* TESTS: Two integration tests that check saving phrases in a database table
* TESTS: Fix assertion in “Analytics/Critical searches without result”
* REFACTOR: Change order if set settings defaults. Now the defaults are set after calling the `dgwt/wcas/settings` filter
* SECURITY: Added escaping for a “Search input placeholder” option

= 1.23.0, April 05, 2023 =
* ADDED: Integration with the “Blocksy” theme
* ADDED: Integration with the “Qwery” theme
* ADDED: Integration with the “StoreBiz” theme
* ADDED: Allows the `Shop manager` role to manage the plugin settings by adding a constant to the `wp.config.php` file
* ADDED: Allows creating HTML templates instead of displaying simple “No results” message
* ADDED: Ability to search using “Greeklish” for Greek
* ADDED: Fuzzy search, synonyms, and other pro features available in searching for taxonomy terms.
* IMPROVED: Blocks calculating score if the phrase contains a single character
* FIXED: More accurate calculation of the order of products in search results. The extra score for an exact match of a sequence of words
* FIXED: Storefront theme - not working focus event while using a mobile overlay for iPhone devices
* FIXED: Mobile overlay on iPhone devices - didn't hide search results on a scroll event or after clicking the “done” button
* FIXED: iPhone devices - annoying auto zoom in search input on focus
* FIXED: Search icon mode and search history - a search bar was needlessly concealed on clicking the “Clear” button
* FIXED: Freemius SDK - added submenu slug
* FIXED: Flatsome theme - detecting incompatible settings and disappearing search form on hover
* FIXED: Layout option - hidden triangle icon when a layout is “icon” and style is “Pirx”
* FIXED: Unnecessary AJAX query on the settings page
* FIXED: Disabling the search cache using a PHP constant didn't completely disable it. Now it disables it completely
* FIXED: `Table doesn't exist` error during index building in some rare cases
* FIXED: Incorrect product links in search results with the “Permalink Manager for WooCommerce” plugin active
* FIXED: TranslatePress and qTranslate-XT - untranslated breadcrumbs in category results
* TWEAK: Replacing empty href tag with `#` in Storefront integration because of SEO
* TWEAK: Trivial CSS changes
* TWEAK: Ability to disable the mutex mechanism during indexing
* TWEAK: Multilingual - supporting language codes up to 10 characters
* UPDATED: Freemius SDK to v2.5.6
* UPDATED: Polish translation
* REFACTOR: Forcing mobile overlay breakpoint and in layout breakpoint in theme integrations
* REFACTOR: Variables names in the method `Helpers::calcScore()`

= 1.22.3, January 30, 2023 =
* FIXED: Some prices were not aligned properly

= 1.22.0, January 30, 2023 =
* ADDED: New feature - Search history. The current search history is presented when the user clicked/taped on the search bar, but hasn't yet typed the query.
* ADDED: FiboSearch Analytics - New widget in WordPress Dashboard with critical searches without result
* ADDED: Integration with Essentials theme
* ADDED: Make UI_FIXER object as global object
* ADDED: Ability to search for vendors by description and city
* ADDED: Ability to exclude critical phrases in the Analytics module
* ADDED: Custom JavaScript events during the search process
* ADDED: Ability to export search analytics data as CSV files
* FIXED: Integration with Flatsome theme - focus event didn't work with a search bar
* FIXED: Integration with WooCommerce Product Filter by WooBeWoo - “Undefined array key 'query'” notice
* FIXED: Integration with Jet Smart Menu - repair duplicated search bars IDs
* FIXED: Integration with Astra theme - support for version 4.0.0
* FIXED: Integration with Astra theme - cannot change the number of products on the cart page
* FIXED: Integration with XStore theme - support for search icon in mobile panel
* FIXED: Compatibility with PHP 8.1
* FIXED: RWD for FiboSearch Settings views including Analytics views
* FIXED: Search bar CSS, especially when Pirx style and Voice Search work together
* FIXED: A user with permission to edit plugin settings cannot see search analytics
* FIXED: Pages can not be searched when the database prefix is empty
* FIXED: Improved compatibility with other plugins on the search results page
* FIXED: Broken index when there are many variants. Speed up getting variants when building an index
* CHANGE: Updated French translation
* CHANGE: Hide the Voice Search icon when a user starts typing
* CHANGE: Updated Freemius SDK to v2.5.3
* CHANGE: Remove information that Analytics is a beta feature
* CHANGE: Remove information that Darkened Background is a beta feature
* CHANGE: Set "Pirx" as a default search bar style


= 1.21.1, November 24, 2022 =
* FIXED: Multisite: search does not work when the plugin is activated on a network-wide level
* FIXED: Rare database error “Duplicate entry...” error when building search index

= 1.21.0, November 21, 2022 =
* ADDED: Integration with Product GTIN (EAN, UPC, ISBN) for WooCommerce plugin
* ADDED: Integration with EAN for WooCommerce plugin
* ADDED: Troubleshooting - checks if products thumbnails need to be regenerated
* ADDED: Optional consent to submit bug reports when installing the plugin
* FIXED: Missing translation domain in some texts
* FIXED: Support variants of &lt;br&gt; tag in product names in autocomplete
* FIXED: Unable to embed search bar as a widget
* FIXED: Disable voice search for Chrome on iPhone or iPad
* FIXED: Integration with the Astra theme - unclosed  &lt;div&gt; tag
* FIXED: Exclude save phrases to analyze when the phrase is 'fibotests' or the user has a specific role.
* FIXED: UI_FIXER: check if event listeners were correctly added to search inputs. If no, reinitiate the search instance
* FIXED: UI_FIXER: rebuild all search bars without correct JS events
* FIXED: Redundant DB queries related to the existence of plugin tables
* FIXED: Database error while updating or deleting product category
* FIXED: “Warning: strpos(): Empty needle” when there is a no “s” parameter (free version)
* FIXED: Can't get terms in the "Exclude/include products" option
* FIXED: Unnecessary secondary connection with a database in the indexer
* FIXED: MySQL deadlock errors during index building
* FIXED: Integration with Booster Plus for WooCommerce - error during index building
* FIXED: The endpoint of the search engine responds when the plugin is disabled
* FIXED: Not indexing custom fields - this fixes an issue that occurred after integrating FiboSearch with some EAN-related plugins
* FIXED: Double results in the autocomplete
* FIXED: The index cannot be rebuilt with an empty DB prefix
* FIXED: Invalid index status even though the index was built correctly
* FIXED: Push the index-building process further after updating the plugin. The index wasn't started in many cases
* FIXED: Integration with WC Marketplace stopped working after it was rebranded to MultiVendorX
* CHANGE: Updated Freemius SDK to v2.5.2

= 1.20.2, September 18, 2022 =
* FIXED: Resignation from the WP Cache in favor of a new own object cache solution that uses non-persistent mode. Some WP Cache based solutions caused complications with indexing or searching

= 1.20.1, September 15, 2022 =
* FIXED: Indexer issues: WordPress Object Cache issues
* FIXED: Indexer issues: “Index column size too large. The maximum column size is 767 bytes.”
* FIXED: Indexer issues: “[Searchable index] Database error "Duplicate entry...”
* FIXED: Warning related to Zend OPcache API when using plugin shortcode

= 1.20.0, September 13, 2022 =
* ADDED: Integration with Woostify theme
* ADDED: Integration with Neve theme
* ADDED: Integration with WP Rocket
* ADDED: Include block sources in the plugin package
* ADDED: Possibility to reset search statistics from the settings page
* ADDED: Support for &lt;sub&gt; element in autocomplete suggestions
* ADDED: Stopwords feature
* ADDED: Improved logging of indexer errors related to the database
* ADDED: Improved detection of non-emergency errors while building the index
* ADDED: Integration with “Product Specifications for Woocommerce” plugin
* ADDED: Polish translation
* FIXED: Incorrect display of styles with personalization of the search
* FIXED: Wrong settings index in Impreza and Enfold theme
* FIXED: Removed of unnecessary language files
* FIXED: Always set cursor at the end of the input
* FIXED: Incorrect verification if the browser supports speech recognition
* FIXED: FiboSearch Analytics - not working “check” buttons of the latest loading list
* FIXED: Unnecessary options and transients after uninstalling plugin
* FIXED: Indexer error when the variation has a very long SKU
* FIXED: Unable to save fields with capital letters in the "Search in custom fields" option
* FIXED: Not indexing ACF fields with “-” in the name
* FIXED: Constant rebuilding of the index in certain circumstances
* FIXED: Error trying to index products whose languages were not active
* FIXED: Using a second DB connection to a database outside of the searchable indexer context
* FIXED: Incorrectly handled transaction to the database during index build
* FIXED: Index building stops for unspecified reasons at 80% or 100%
* FIXED: Not including filtered data when indexing a variation of product
* FIXED: Indexer error when multilingualism is disabled by constant and the TranslatePress or qTranslate-XT plugin is active
* FIXED: Unable do add new indexer logs
* FIXED: No terms in “Exclude/include products” option
* FIXED: Duplicated SQL queries during page load
* FIXED: WOOF – Products Filter for WooCommerce: display search-related code above filters
* FIXED: No search results when a category is excluded with an active Polylang plugin
* FIXED: Notice in indexer logs with active Polylang plugin and indexing of post/pages
* REFACTOR: Removing references to dgwt_wcas_si_info table which is no longer used

= 1.19.0, July 27, 2022 =
* ADDED: New feature - New search layout called “Pirx”
* ADDED: New feature - FiboSearch Analytics
* ADDED: New feature - Layout type: Icon on desktop, search bar on mobile
* ADDED: New feature - Voice search
* ADDED: New feature - FiboSearch blocks in the block editor
* ADDED: Separated option “mobile_overlay_breakpoint” to handle overlay on mobile breakpoint
* ADDED: Add "mobile_overlay_breakpoint" as a shortcode param to add the opportunity to set this value independently from global settings
* ADDED: New search bars fixer: try to regenerate search bars when they were added by AJAX callbacks
* ADDED: Support for header builder in integration with Astra theme
* ADDED: Another question marks for FiboSearch settings that cover our documentation
* ADDED: Settings preview - smooth scrolling
* ADDED: Settings preview - animate typing on a search preview for “Search bar” tab
* ADDED: Support all types of layout in widget and embedding via Menu
* ADDED: Ability to reset plugin settings to default values
* ADDED: New shortcode params: “submit_btn” and “submit_text”
* ADDED: New feature - Parallel indexing
* ADDED: New feature - Building and updating index via WP-CLI
* ADDED: New filter to change score of custom post results
* ADDED: Exclude common WooCommerce pages from search results
* ADDED: Ability to build and update a search index using actions
* ADDED: New hook when index build aborts due to an error
* FIXED: WOOF – Products Filter for WooCommerce integration: broken counters on the search results page
* FIXED: Interdependent settings in Settings -> Search bar -> Style -> Design
* FIXED: Improved darkened background positioning (support for sticky elements as well)
* FIXED: Improved search suggestions and the details panel positioning (support for sticky elements as well)
* FIXED: Settings page - wrong position of a questions mark (Safari)
* FIXED: JavaScript errors in the settings page when the GeoTargetingWP plugin is active
* FIXED: Try to add “dgwt-wcas-active” class again if it has not been added by other events
* FIXED: Incorrect elements position after load “iconMode”
* FIXED: Incorrect adding CSS class as shortcode parameter
* FIXED: English grammar typos
* FIXED: Building the index fails because of too long phrases
* FIXED: Remove redundant caching of settings in MainQuery
* FIXED: Multisite - building an index on the main site, breaks the indexes on other sites
* FIXED: TranslatePress integration: slow index building when there are products with many variations
* FIXED: Unnecessary initialization of fuzzy search when $config['fuzz'] was filled
* FIXED: Invalid CSS classes for post and page search results
* FIXED: Clear taxonomies cache when the settings are updated. It clears the cache only for settings related to taxonomies
* FIXED: Improved handling of database errors when building the index
* FIXED: Troubleshooting - test if “HUSKY - Advanced searching by Text” extension from WOOF - WooCommerce Products Filter plugin is active
* FIXED: Indexer - rounding build progress formatting
* FIXED: Indexer - hide error icon from menu after successfully building index
* FIXED: Indexer - building an index gets stuck at 100% in some rare cases
* FIXED: Indexer - resuming the index build as scheduled when it got stuck the previous time
* CHANGE: Updated Freemius SDK to v2.4.4
* REFACTOR: Indexer - Replacing “PDO” with WPDB”
* REFACTOR: Settings page - rebuild the settings section Search Bar -> Appearance to improve UX
* REFACTOR: Search bars fixer

= 1.18.1, May 23, 2022 =
* FIXED: Exceeding the memory limit on the search results page

= 1.18.0, May 12, 2022 =
* ADDED: New feature - FiboSearch Analytics. This feature will be available to everyone in FiboSearch v1.19.0. To enable it in v1.18.0 declare constant `define( 'DGWT_WCAS_ANALYTICS_ENABLE', true );` in `wp-config.php`
* ADDED: Open selected suggestion in new tab by shortcut Cmd+Enter/Ctrl+Enter
* ADDED: Show score in search results on the Debug page
* ADDED: Link darkened background and fuzzy search feature to the documentation
* ADDED: Allow filtering no-results response
* ADDED: Integration with WooCommerce Protected Categories plugin
* ADDED: Possibility not to translate search index fields by TranslatePress
* ADDED: An alternative search engine endpoint can be enabled with a constant
* ADDED: New filter to manipulate search results score for taxonomy terms
* FIXED: Improving ESC key functionality: If there are suggestions, ESC hides them. If there are not suggestions and mobile icon mode is enabled, ESC disables mobile icon mode. If there are not suggestions and darkened overlay is enabled, ESC disables darkened overlay
* FIXED: Allow recognizing CMD key
* FIXED: Remove interaction on the TAB key
* FIXED: Elementor popups - reinit search bars after loading Elementor's popup
* FIXED: Cannot open the first result with Ctrl + Enter
* FIXED: Prevent displaying search results, if the search icon mode is closed
* FIXED: Unnecessary closing mobile icon mode and darkened overlay mode after selecting text in the search bar. It used to happen often when users selected text from the search bar to remove it and write something new but clicked outside the search bar (JS mouseup event was outside the bar)
* FIXED: Better sanitization of the plugin settings
* FIXED: Unable to search taxonomies terms with ampersands
* FIXED: Site Health warning about active session when some plugin integrations are active
* FIXED: “No results” suggestion if all results have been removed in earlier filters
* FIXED: Excluded product variations in the “Out of stock” fixer in the Troubleshooting module
* FACTOR: Retrieving results on the search page without additional HTTP request

= 1.17.0, February 28, 2022 =
* ADDED: New beta feature - “Darkened background”
* ADDED: Integration with Kadence theme
* ADDED: Integration with TheGem (Elementor) and renamed TheGem (WPBakery)
* ADDED: Comments in template files for the Details Panel
* ADDED: Refreshing the content on the checkout page when a product is added to the cart from the search Details Panel
* ADDED: Tooltip with information about overriding when an option is overridden by theme integration
* FIXED: Conflict between Salient theme and Shipmondo plugin
* FIXED: Unexpected hiding from the search bar right after the “focus” event. Bug occurred only on mobiles
* FIXED: Hide the Storefront handheld footer bar when the search results are open. Otherwise, handheld footer bar covers the autocomplete dropdown
* FIXED: Prevent toggle mobile overlay if the search bar doesn't have this mode
* FIXED: Non-existing table during the database repair process
* FIXED: Minor security issues
* FIXED: Fatal errors in PHP 8 when the dashboard language is set to “ru_RU”
* FIXED: Add artificial overlay to cover the “Close Button” because SVG elements don't provide information about parents elements in "event.target"
* CHANGE: General tooltip style on the plugin settings page - more padding, bigger font, right position of the tooltip, auto cursor, wider
* ADDED: Ability to replace part of the phrase before searching

= 1.16.0, February 03, 2022 =
* ADDED: Integration with a XStore theme
* ADDED: Allow customization of the Details Panel with actions and filters
* ADDED: Templating system to override details panel templates via child-theme
* ADDED: Troubleshooting - test if product translations are enabled in the Polylang settings
* ADDED: Add extra CSS classes when search bar is focused
* ADDED: Integration with MemberPress plugin
* FIXED: Compatibility with PHP 8.1
* FIXED: Integration with Astra theme - the “Save Changes” button disappeared after turning on the integration
* FIXED: JavaScript errors on the plugin activation page
* FIXED: Bug with enabling and disabling “overlay on mobile” feature during window resizing and reaching a breakpoint
* FIXED: Missing "Troubleshooting" tab icon with the number of issues
* FIXED: MySQL deadlocks during index building
* FIXED: Polylang - the search engine stopped working when one of the two languages was deactivated
* FIXED: Requiring file class-http.php emits deprecated warning in WordPress 5.9

= 1.15.1, December 20, 2021 =
* FIXED: Dynamic pricing is not displaying correctly


= 1.15.0, December 16, 2021 =
* ADDED: Integration with a Uncode theme
* ADDED: Integration with Uncode theme - support for dark menu skin
* ADDED: Possibility to submit the search event to Google Analytics in your own way
* ADDED: Basic support for AMP
* ADDED: Allow getting search results programmatically
* ADDED: New path for WordPress load:  wp-cms
* ADDED: Ability to load the search endpoint from custom locations
* ADDED: Allow switch to the alternative search endpoint if the default is blocked
* ADDED: The loader icon in the “Indexer” tab while building the index
* FIXED: Integration with the Goya theme has stopped working
* FIXED: Divi theme integration - overlay on mobile was fixed. Support for new Divi ID #et_top_search_mob
* FIXED: Divi theme integration - search form did not disappear after exiting mobile overlay
* FIXED: Search suggestions were invisible because of a bug in the old version of jQuery UI. The method outerHeight() returned an object instead of a number
* FIXED: Simplifying integration with Polylang
* FIXED: PHP 8 compatibility
* FIXED: Identifying an index build error due to a low "max_allowed_packet" value in the MySQL server
* FIXED: Prevent cache indexer queue data
* FIXED: Remove unused directories
* FIXED: TranslatePress integration - wrong translation in suggestions URLs
* FIXED: Troubleshooting tab - the button “Fix out of stock relationships” button doesn't work
* FIXED: Not using search cache when its tables do not exist
* CHANGE: Remove info about rebranding


= 1.14.1, October 25, 2021 =
* FIXED: Unable to search in other languages than the default one (TranslatePress, qTranslate-XT, sometimes WPML)


= 1.14.0, October 19, 2021 =
* ADDED: Integration with “GeneratePress” theme
* ADDED: Possibility to set a delay for initialization of mobile overlay
* ADDED: New filter to manipulate the results score
* ADDED: Details Panel - support for responsive images including retina images (2x), sizes, and srcset
* ADDED: Possibility to insert custom HTML in 5 places in the search suggestion
* ADDED: New filter and action hooks
* ADDED: New debugging tools for an indexer
* ADDED: Searching in custom taxonomies
* ADDED: Allow to easier change the order of the search results by simple code snippets
* FIXED: Prevent hiding search results on click an Enter key when submit is disabled via a filter
* FIXED: No results on the search page when WPML is active with “Language name added as a parameter” option
* FIXED: Support for version v1.3.1 of Open Shop theme
* FIXED: Integrating with Divi theme - delay in starting JS scripts
* FIXED: Integrating with Divi theme - force search overlay for mobile devices
* FIXED: Unnecessary HTML tags in the search input after selecting a suggestion
* FIXED: Hide mobile overlay after submitting a form or clicking a result. Fixes screen after clicking iPhone back arrow
* FIXED: Troubleshooting module. Fixed false negative in “OutOfStockRelationships test”. An order of arrays was taken into account for the diff function. It was replaced by full diff
* FIXED: Unclosed tag &lt;a/&gt;
* FIXED: Typo on Troubleshooting tab
* FIXED: Clear “alt” attribute in the product thumbnail
* FIXED: Unnecessary hiding all products in Woocommerce Memberships plugin (version < 1.22)
* FIXED: Prevent a PDO error with the message “There is no active transaction”
* FIXED: Prevent a fatal error with the message “Call to undefined function admin_url()” during the search
* FIXED: Unable to search products with very long SKU (more than 100 chars)
* FIXED: No search results if B2BKing is active and hiding products is disabled
* FIXED: Prevent errors while searching phrases that contain only spaces
* FIXED: Disabling search cache when its tables do not exist
* REFACTOR: Direct passing items set to searchable indexer instead of using a database
* REFACTOR: Escape search terms the way WordPress core does
* REFACTOR: Replacing image with thumbnails in DgoraWcas\Post class to keep a consistent style compared with DgoraWcas\Product


= 1.13.0, July 27, 2021 =
* ADDED: Integration with “eStore” theme
* ADDED: Allow to open search result in new tab with Ctrl+left mouse key
* ADDED: Integration with “Custom Product Tabs for WooCommerce” plugin
* ADDED: Ability to search in the content generated by the specified shortcodes
* ADDED: Integration with WooCommerce Memberships plugin
* ADDED: New filter to manipulating products score
* ADDED: Integration with “Premmerce Brands for WooCommerce” plugin
* ADDED: New test for the troubleshooting module - add test if MySQL server has support to InnoDB engine
* FIXED: Disappearing suggestions and details panel on click when there were more search bars.
* FIXED: Improved integration with “Avada” theme
* FIXED: Improved mobile search in new version of “Rehub” theme
* FIXED: Unable to use context menu and middle mouse button on search results
* FIXED: “Eletro” theme - Support cases when the search overlay is disabled
* FIXED: Identifying error “MySQL server has gone away”
* FIXED: Brand related filters can now be used in a theme
* FIXED: Change empty DB_HOST for IPv4 address
* FIXED: PHP Warning in the indexer logs (rare case): array_values() expects parameter 1 to be array, null given
* FIXED: TranslatePress - randomly untranslated URLs of objects as they were added to the index
* REFACTOR: Clean up composer files

= 1.12.0, June 22, 2021 =
* ADDED: Integration with Electro theme
* ADDED: New test for the troubleshooting module - test language codes
* ADDED: New test for the troubleshooting module - check if the Elementor Pro has defined correct template for search results
* ADDED: Support for “ACF” fields in the “Search in custom fields” list
* ADDED: Support for “ACF Table” field in the “Search in custom fields” list
* ADDED: New test for the troubleshooting module - test if the index was built by the current plugin version
* ADDED: New test for the troubleshooting module - test "Out of stock" relationships
* ADDED: Compatibility with “Permalink Manager for WooCommerce” plugin
* ADDED: Support for qTranslate-XT
* FIXED: “WOOF – Products Filter for WooCommerce” - disappearing filters if “Dynamic recount” and “Hide empty terms” was enabled and other issues
* FIXED: Remove unnecessary AJAX request on select “See all products ... (X)”
* FIXED: The search form is now generated without random ID, to be compatible with the LiteSpeed Cache plugin
* FIXED: TranslatePress v1.9.8 and higher - results on the search page were different from the results in the autocomplete
* FIXED: Order terms by total_products if there is the same score
* FIXED: Better index rebuild enforcement when the plugin version changes
* FIXED: PHP WARNING json_decode() expects parameter 1 to be string, array given
* FIXED: Not using the getmypid() function when it has been disabled
* FIXED: Do not index term if isn't associated with any object
* FIXED: The "Search in description" option now also applies to variations of products
* FIXED: Reindex related products after edit/delete term. Applies to category, tag, brand and attribute
* FIXED: Add another alternative location of wp-load.php file: wordpress/wp-load.php
* REFACTOR: Change .dgwt-wcas-suggestion element from &lt;div&gt; to &lt;a&gt; to allow open a suggestion in a new tab

= 1.11.0, May 24, 2021 =
* ADDED: Integration with Goya theme
* ADDED: Integration with Top and Top Store Pro theme
* ADDED: Keep the state of a details panel in memory instead of replacing it every time using jQuery.html() method. Doesn't clear quantity and "add to cart" states.
* ADDED: Prevent submit empty form 
* ADDED: Possibility to index only products belonging to the indicated categories, tags or groups of attributes
* ADDED: Show category thumbnails in autocomplete suggestions
* ADDED: Show brand thumbnails in autocomplete suggestions
* FIXED: W3 validator warning: The type attribute for the style element is not needed and should be omitted.
* FIXED: Search terms with apostrophes
* FIXED: Synchronization with the native WooCommerce option "Out of stock visibility" 
* FIXED: Hiding an unnecessary line in the product details when there is no description
* FIXED: Adding polyfill for supporting “includes” in Internet Explorer 11
* FIXED: Better elements positioning on the "Starting" tab on the plugin settings page
* FIXED: Support for custom Google Analytics object name
* FIXED: Better handling “plus” and “minus” buttons for a quantity field
* FIXED: Uncaught Error: Call to a member function get_review_count() on null
* FIXED: Displaying the search box off screen on mobile devices
* FIXED: Correct way for rebuilding autocomplete feature on an input by manually recalling dgwtWcasAutocomplete(). Remove more events on dispose method
* FIXED: Highlight single chars in autocomplete results
* FIXED: Add trim on query value
* FIXED: Clear search title and phrase from escape characters
* FIXED: Indexer errors when only one language is active in WPML
* FIXED: Rebuilding index when WPML active languages change
* FIXED: Composer - allow to use PHP 7.0.x
* FIXED: Variations are also excluded when the "Exclude 'out of stock' products" option is active
* FIXED: AJAX endpoint search.php now send a proper empty response if language is invalid
* FIXED: Identifying an indexer error related to a database connection limit
* FIXED: Indexing variations when updating a single product
* FIXED: Logging indexer timeout warning and show solution for it when indexer stuck
* FIXED: Exclude terms in search result matched by "Exclude/include products" option
* FIXED: “WOOF – Products Filter for WooCommerce” - correct results after using filter or pagination and counters next to the filters
* FIXED: InnoDB engine forcing in index tables
* REFACTOR: Standardize filters name and define indexer set items count in one place
* CHANGE: Decrease searchable indexer set items count from 100 to 50

= 1.10.0, April 22, 2021 =
* ADDED: Possibility to disable select event on suggestions (click and hit the Enter key)
* ADDED: Possibility to disable submit a search form via a filter
* ADDED: New tool for debugging a indexer
* ADDED: Notifying user (via icon) of an indexer error or index build delay
* ADDED: Improving a search index build in the background
* ADDED: New light method for building a search index - synchronously mode
* FIXED: Not working click event on suggestions after using “back arrow” on a Safari browser
* FIXED: Allow to recognize Chinese lang codes such as zh-hant and zh-hans
* FIXED: Error on PHP 8. Wrong format for printf function
* FIXED: When searching for something and then clicking “back arrow”, the “✕” for closing remained
* FIXED: Wrong path in restoration theme
* FIXED: Better checking of nonces
* FIXED: Handle efficiently long queries by limiting characters and tokens when searching
* FIXED: Correct handling of terms in which characters can be written differently eg. “läßt” and “lässt”
* FIXED: Sort search results by popularity - “desc” instead “asc”
* FIXED: Better detection when the indexer is stuck
* FIXED: Skip collecting irrelevant MySQL errors by Query Monitor
* FIXED: Troubleshooting - improvement of checking database connection by PDO
* FIXED: Duplicate attributes in the variant title
* FIXED: Indexer errors when only one language is active in TranslatePress
* FIXED: Integration with WooCommerce Product Filter by WooBeWoo - no products with AJAX filtering
* FIXED: Unnecessary sorting when fetching products with search results
* FIXED: Allow to delete product from an index after making order
* FIXED: Limit the search scope for multiple keywords as single letters
* REFACTOR: Troubleshooting - improved detection of problems with WP-Cron
* REFACTOR: Better indexer error handling
* REFACTOR: Build an index for variants in a separate background process
* REFACTOR: Build an index for taxonomies in a separate background process
* REMOVE: Excessive scheduler actions fixer was removed


= 1.9.0, March 15, 2021 =
* ADDED: Support for WooCommerce Private Store plugin
* ADDED: TranslatePress integration
* ADDED: New test for the troubleshooting module - test if PDO can connect to DB
* ADDED: Support for search products, post and pages with no translation in WPML (WPML settings: "Translatable - use translation if available or fallback to default language")
* CHANGE: Plugin rebranding -  Replace the plugin name AJAX Search for WooCommerce with new name FiboSearch
* CHANGE: Plugin rebranding -  Replace the old domain ajaxsearch.pro with new fibosearch.com
* CHANGE: Plugin rebranding -  Update visual assets 
* CHANGE: Updated Freemius SDK to v2.4.2
* CHANGE: New alternate shortcode [fibosearch] instead of [wcas-search-form]
* CHANGE: Min supported version of PHP is 7.0
* FIXED: Fixed Chrome lighthouse insights
* FIXED: Missing of dgwt-wcas-active class when the search was focused too early
* FIXED: Grammar and spelling errors in texts
* FIXED: Not firing jQuery onLoad event for some browsers
* FIXED: Fixed PHP Warning: json_decode() expects parameter 1 to be string, array given
* FIXED: Speed up placeholder search engine when 
* REMOVE: Removed useless dgwt-wcas-search-submit name attribute
* REMOVE: Removed unused search forms from a Avada theme
* REFACTOR: Move product related logic from Updater.php to Product.php

= 1.8.2, February 06, 2021 =
* ADDED: Support for Astra theme
* ADDED: Support for Avada theme - replacing a fusion search form
* ADDED: Support for Open Shop theme
* ADDED: Support for Divi - menu in custom header and hiding search results when opening a search overlay
* ADDED: Support for CiyaShop theme
* ADDED: Support for BigCart theme
* FIXED: Increase the clickable area of the 'back button' in the overlay mobile mode
* FIXED: Disappearing search bar especially on Firefox
* FIXED: Hide new aggressive admin notices added by other plugins
* FIXED: Hide shortcodes in the Details Panel
* FIXED: Divi theme integration - Prevent to focus input if it isn't empty. Fix case with more search bars in #main-header selector
* FIXED: Adaptation to the new class name convention of WooCommerce Product Table plugin
* FIXED: Fixed display of category names and tags in the Details Panel when the name contains an apostrophe
* ADDED: New option to exclude products belonging to the indicated categories, tags or groups of attributes
* ADDED: New test for the troubleshooting module - valid search results
* ADDED: New test for the troubleshooting module - check if WooCommerce Multilingual is active when site uses WPML
* ADDED: New test for the troubleshooting module - check if conflicted plugin YITH WooCommerce Ajax Search is installed
* ADDED: Integration with WooCommerce Catalog Visibility Options plugin
* ADDED: Integration with B2BKing plugin
* ADDED: Integration with Brands for WooCommerce plugin by BeRocket
* ADDED: Integration with brands solution delivered by WP Bingo themes
* ADDED: Limit the number of fields to be searched using the native engine when the search index is not ready
* FIXED: Support for other language code like xx-xx
* FIXED: Add attributes names of variations for search suggestions
* FIXED: Easiest way of removing the search index from the database
* FIXED: Bug related to run the search index process in loop for timezone GMT+11 and similar
* FIXED: Incorrect language for post/pages
* FIXED: Avoiding infinite index rebuilding when there are no products
* FIXED: Add alternate WP loading path for Themosis Framework

= 1.8.1, December 04, 2020 =
* ADDED: Support for Rehub theme
* ADDED: Support for Supro theme
* FIXED: Troubleshooting module improvements
* FIXED: Blinking suggestions
* FIXED: Bug in icon colors
* FIXED: Flatsome theme - quantity buttons issue
* FIXED: Divi theme - hide extra search bar in footer
* FIXED: Mobile overlay improvements for iPhones
* FIXED: Better suggestion order for non latin letters
* FIXED: Action URL in search form when Polylang is active
* REMOVE: Mobile Detect library 
* FIXED: Performance - prevent queuing redundant actions in Action Scheduler and remove unnecessary
* FIXED: Undefined function admin_url() during some search request

= 1.8.0, October 23, 2020 =
* ADDED: Support for Sober theme
* ADDED: Support for Divi theme
* ADDED: Support for Block Shop theme
* ADDED: Support for Enfold theme
* ADDED: Support for Restoration theme
* ADDED: Support for Salient theme
* ADDED: Support for Konte theme
* ADDED: New filter and action hooks
* ADDED: &lt;br&gt; to HTML whitelist for search suggestions
* ADDED: Allow to add search icon as menu item
* ADDED: Allow to change colors of search icon
* CHANGE: Updated Freemius SDK to v2.4.1
* CHANGE: Replace old close "x" icon with Material Design icons
* FIXED: Empty search results on search results page 
* FIXED: Support Touchpad click for some devices
* FIXED: Mixed Content on the plugin settings page in some cases
* FIXED: Integration with Flatsome theme
* FIXED: Broken translations via WPML String Translation
* ADDED: Troubleshooting module on the plugin settings page
* ADDED: Support for Hyyan WooCommerce Polylang Integration plugin
* ADDED: Integration with WooCommerce Wholesale Prices plugin - visibility of products and prices
* FIXED: Wrong time in Scheduling Indexing feature
* FIXED: Bug in synonyms regex
* FIXED: Add primary keys to few DB tables to improve compatibility with DigitalOcean hosting
* FIXED: Automatic select variation suggestion after submit the form
* FIXED: Support for WooCommerce Products Visibility - show products if there are no rules yet
* FIXED: Improved integration with JetSmartFilters and JetWooBuilder plugins
* FIXED: Support for adoptive options feature in Product Filter for WooCommerce (by XforWooCommerce) plugin
* FIXED: Issues related to high CPU usage
* FIXED: Improved checking if Polylang plugin is active
* FIXED: Bug with DB collation in specific cases


= 1.7.2, July 12, 2020 =
* ADDED: Integration with FacetWP plugin
* ADDED: Support for Shopkeeper theme
* ADDED: Support for The7 theme
* ADDED: Support for Avada theme
* ADDED: Support for Shop Isle theme
* ADDED: Support for Shopical theme
* ADDED: Support for Ekommart theme
* ADDED: Support for Savoy theme
* ADDED: Support for Sober theme
* ADDED: Support for Bridge theme
* ADDED: Possibility to change search icon color
* ADDED: New filter hook for a search form value
* ADDED: Support for Site Search module in Google Analytics
* FIXED: Add CSS border-box for each elements in search bar, suggestions and details panel
* FIXED: Sending events to Google Tag Manager
* FIXED: Remove &lt;b&gt; from product title
* FIXED: Search in categories and tags for Russian terms
* FIXED: Duplicated category in breadcrumb
* FIXED: No results when searching for phrase included apostrophe or double quote
* FIXED: Details panel - Remove HTML from titles attribute
* FIXED: Fixed for integration with Woo Product Filter plugin by WooBeWoo
* FIXED: Fixed for integration with XforWooCommerce plugin
* FIXED: Error: Undefined index: is_taxonomy in some cases
* ADDED: Support fro brand served via Martfury Addons plugin, the part of the Martfury theme
* ADDED: Integration with WooCommerce Products Visibility plugin
* FIXED: Better relevancy for exact match product title or SKU
* FIXED: Displaying product variation in the Details Panel

= 1.7.1, May 17, 2020 =
* FIXED: Selecting suggestions issue

= 1.7.0, May 17, 2020 =
* ADDED: Icon search instead of search bar (beta)
* ADDED: Improvements on search results pages
* ADDED: Integration with native WooCommerce filters
* ADDED: Integration with Advanced AJAX Product Filters plugin by BeRocket
* ADDED: Integration with WOOF – Products Filter for WooCommerce plugin by realmag777
* ADDED: Integration with Product Filters for WooCommerce plugin by Automattic developed by Nexter
* ADDED: Integration with Woo Product Filter plugin by WooBeWoo
* ADDED: Integration with WooCommerce Product Table plugin by Barn2 media
* ADDED: Support for TheGem theme
* ADDED: Support for Impreza theme
* ADDED: Support for Medicor theme
* ADDED: Support for WoodMart theme
* ADDED: Support for Polylang
* ADDED: New filter and action hooks
* ADDED: Dynamically loaded prices for WPML Multi-currency feature
* FIXED: Mobile search - don't hide suggestions on blur
* FIXED: Bug related to highlight keywords. For some cases it displayed &lt;strong&gt; tag.
* FIXED: Delay on mouse hover effect
* FIXED: Minor CSS improvements
* FIXED: Broken mobile view on cart page in some cases
* ADDED: Update search index for parent product on variation change
* ADDED: New filter hook to search products by category description
* ADDED: New filter hook to disable updater. Useful when importing large dataset.
* FIXED: AJAX endpoint - support for WP directory structure of Flywheel hosting.
* FIXED: Synonyms feature - better replacement special chars
* FIXED: Synonyms feature - support for Cyrillic
* FIXED: Synonyms feature - more matching synonyms to single text
* FIXED: Press ENTER key on "See all products..."
* FIXED: Loading unexpected PorterStemmer
* FIXED: Better search order - extra score for exact match
* FIXED: Admin settings - better manage custom fields (Selectize)
* FIXED: Redirect to product variation with preselected attributes instead of open search results page (on submit)
* FIXED: Wrong search expression when special chars exist
* FIXED: Displaying no results when search engine found nothing
* FIXED: Not working action scheduler
* CHANGE: Remove outdated backward compatibility system
* CHANGE: Move wp_register_style under the wp_enqueue_scripts hook

= 1.6.3, March 11, 2020 =
* ADDED: Details panel - display stock quantity
* FIXED: Better support for the Elementor including popups and sticky menu
* FIXED: Better support for page builders. Late initialization.
* FIXED: Disabling automatic regenerate thumbnails. Conditionally images regeneration.
* FIXED: HTTP 500 on getResultDetails for some cases
* FIXED: Too long description in live suggestions
* FIXED: Add non-breaking space for prices
* FIXED: JS errors Failed to execute 'getComputedStyle' on 'Window' (for some cases)
* CHANGE: Rename jQuery object from Autocomplete to DgwtWcasAutocompleteSearch because of namespaces conflicts
* ADDED: Support for YITH WooCommerce Brands Add-on (free version)
* ADDED: Support for Perfect WooCommerce Brands support
* ADDED: Better search for phrases include special chars
* ADDED: Update a product info in search index after make changes via WooCommerce REST API
* FIXED: Better sync on search results page
* FIXED: Support for Advanced AJAX Product Filters by BeRocket
* FIXED: Synonyms in Vietnamese
* FIXED: Issues related to multi currencies in WPML
* FIXED: Issues related to "out of stock" status
* FIXED: Non-indexing products in WPML (for some cases)
* FIXED: Search issues when fuzzy search feature is disabled

= 1.6.2, February 18, 2020 =
* ADDED: Details Panel - new layout for product overview and other UX improvements
* ADDED: Automatically regenerates images after first plugin activation
* ADDED: Synonyms
* ADDED: Search in tags
* ADDED: Filter for all labels for easier overwriting
* ADDED: Action hook on indexer completed
* FIXED: Minor issues in search preview in the settings
* FIXED: Details panel - hide "add to cart" button after adding - fix for some themes
* FIXED: Details panel - display overview for post and page suggestion type
* FIXED: Details panel - display overview for brand suggestion type
* FIXED: Better product short descriptions for suggestion and details panel
* FIXED: Repair the index rebuild after changing settings
* FIXED: Highlighted no results suggestion
* FIXED: Better security

= 1.6.1, January 26, 2020 =
* ADDED: Scheduling Indexing via the plugin settings
* ADDED: Builds the index asynchronously after save or delete products. Does not block the product edit logic
* ADDED: Support for Bedrock default ABSPATH
* ADDED: Search debugger
* ADDED: Update a search index after call create/update and delete methods via WooCommerce REST API
* ADDED: Details Panel - grouped load, faster load
* ADDED: New way to embed search box - embedding by menu
* ADDED: Details panel - show "more products..." link for taxonomy type suggestion
* ADDED: Add &lt;form&gt; to quantity elements in a details panel
* ADDED: New filters and actions hook
* FIXED: Always submit form on press ENTER key
* FIXED: Switch to Native WP search engine when the index isn't completed
* FIXED: Exclude from cache update_option in the indexer
* FIXED: Stemmer - Backward compatibility
* FIXED: Product variations exact match
* FIXED: Better order by score for post and pages
* FIXED: Permalink for variable products
* FIXED: Indexer progress bar percent overload in some cases
* FIXED: Table dgwt_wcas_tax_index - SQL error for CREATE INDEX Statement
* FIXED: Minor indexer errors
* FIXED: Variable products SKU exact match - show variant only if it has own SKU
* FIXED: Search in meta fields - better meta key validation
* FIXED: WPML - Support for hidden languages
* FIXED: Automatic index update after changing settings: show pages, show posts
* FIXED: Better rules for checking "out of stock" products
* FIXED: MySQL connector - fix bug related to empty charset
* FIXED: Issue related to colors in plugin settings
* FIXED: Suggestions groups - improved limits
* FIXED: Pricing for taxonomy term in a details panel
* FIXED: Show a details panel on keys UP and DOWN
* FIXED: Mobile search overlay - block scroll of &lt;html&gt; tag (issue on iPhones)
* FIXED: Better data-wcas-context ID, bypasses opcache
* FIXED: W3C - Accessibility errors
* FIXED: Storefront mobile search - more time for input autofocus
* FIXED: Disable quantity for Astra Pro theme - there were broken buttons
* FIXED: Minor CSS improvements
* CHANGE: Increase limit for GROUP_CONCAT (group_concat_max_len) to 100000
* CHANGE: Disable search in title for post/pages by default
* CHANGE:  Decrease debounce time for better speed effect
* CHANGE: Updated Freemius SDK v2.3.2

= 1.6.0, December 08, 2019 =
* ADDED: Search in variation products description
* ADDED: WPML support
* ADDED: Support for booster.io - Visibility by Country
* ADDED: Support for booster.io - Prices by Country
* ADDED: New translations
* ADDED: Non-product search - posts
* ADDED: Non-product search - pages
* ADDED: Possibility to change collation
* ADDED: Wptexturize support
* FIXED: Custom attributes support
* FIXED: Indexer improvements
* FIXED: Post IDs bigger than 2147483647
* FIXED: Support for unexpected and bad output from methods of WC_Product class
* CHANGE: Removing all custom DB tables and options after removing the plugin
* CHANGE: Removing all custom DB tables before build new search index
* CHANGE: Rename class Buildier to Builder
* REMOVE: Search engine based on SQLite
* ADDED: Suggestions groups
* ADDED: Hide advanced settings
* ADDED: Better grouping of settings
* ADDED: Support for Google Analytics events
* ADDED: Search bar preview in settings
* ADDED: New action and filters hooks
* FIXED: Flatsome theme support for [search] shortcode
* FIXED: Images in details panel
* CHANGE: Updated Freemius SDK
* REMOVE: Remove ontouch event from mobile detect

= 1.5.1, September 21, 2019 =
* FIXED: Infinite loop after plugin activation (for some users)
* FIXED: Updating brands in the search index after saving product
* FIXED: Updating custom fields in the search index after saving product

= 1.5.0, September 16, 2019 =
* ADDED  Search in custom fields
* ADDED  Search in brands (support for WooCommerce Brands and YITH WooCommerce Brands Add-on Premium)
* ADDED  Show also matching brands in the autocomplete suggestions similar to category and tags
* FIXED: Indexer issues with shared hosting and PHP FPM limitations
* FIXED: Empty plugin logo in the optin form
* FIXED: Bug with post ID larger that 8388607
* ADDED: Integration with the Flatsome theme. It is possible to replace the Flatsome search form via one checbox in the plugin settings page.
* FIXED: Overload servers. Optimalization for chain AJAX requests. Creates a debounced function that delays invoking func until after wait milliseconds have elapsed since the last time the debounced function was invoked
* FIXED: Better support for HTML entities in products title and description
* FIXED: Issues with mobile search version on Storefront theme for iPhones
* CHANGE: Update/sync fork of devbridge/jQuery-Autocomplete to the latest version
* CHANGE: Settings design

= 1.4.1, August 05, 2019 =
* ADDED: Filter for search results output
* FIXED: Improvements in MySQL connections
* FIXED: Support for SSL MySQL connections
* FIXED: Increase term column size to 127 chars
* FIXED: Add DEFAULT value for lang table in DB
* FIXED: Update index after changes in terms
* FIXED: Show terms even there are no relevant products
* CHANGE: Improving the logger
* CHANGE: Reduction of supported operators for the boolean search feature to only '&' and '|'
* CHANGE: More relevant results for SKUs
* ADDED: French translations
* FIXED: Better support for fixed menu
* FIXED: Add box-sizing to the search input to better implementation for some themes
* FIXED: Duplicated class Mobile_Detect in some cases
* FIXED: Submit button position in some cases
* FIXED: Zoom in iPhones on focused input
* FIXED: Size of images for categories and tags in the Details panel
* CHANGE: Updated Freemius SDK

= 1.4.0, May 04, 2019 =
* FIXED: Problem with duplicated SKU
* FIXED: Error with empty attributes
* FIXED: Minor search improvements
* FIXED: Support for &lt;sup&gt; tag
* FIXED: Support or HTML entities in categories and terms names
* CHANGE: Migration from SQLite to MySQL
* CHANGE: Remove unimportant files from vendor
* ADDED: New modern mobile search UX (beta, disabled by default, enabled only for Storefront theme)
* ADDED: Italian translations
* ADDED: Spain translations
* FIXED: Error with WP Search WooCommerce Integration
* FIXED: Conflict with the Divi theme for some cases
* CHANGE: Implementing flexbox grid (CSS)

= 1.3.3, March 02, 2019 =
* CHANGE: Longer search time limit for searchable index building proccess
* FIXED: Indexer improvements
* FIXED: Support for tikenizer after updating a product
* FIXED: Multiple SKU variations
* FIXED: Deactivate browser native "X" icon for search input
* FIXED: Products images for tags and categories in Details panel
* FIXED: Security fix
* ADDED: New logos
* CHANGE: Updated Freemius SDK

= 1.3.2.1, February 17, 2019 =
* FIXED: Order by price on the search results page

= 1.3.2, February 16, 2019 =
* ADDED: Add the tokenizer. Better support for "-" and "." in the search terms
* ADDED: Emergency mode for the indexer
* ADDED: Better errors logging (only for PHP 7)
* ADDED: Filters support in the SHORTINIT mode
* ADDED: The WP Background Processing package hosted independently
* FIXED: Infinite loop in the indexer in some cases
* FIXED: Better highlighting results
* FIXED: Wrong scoring by SKU
* FIXED: PHP error with $_SERVER variable
* FIXED: The errors with indexing products with the status "Out of the stock"
* FIXED: The error with artificially duplicated search forms occurred in the Mega Menu plugin and some themes.
* FIXED: The error with missing Apache "headers" extension
* CHANGE: Add the "booleanSearch" as a default search mode
* ADDED: The text "No results" and "See all results..." can be customized in the plugin settings
* ADDED: New filters and hooks
* FIXED: Hide the "Account" link in the free plugin versions
* FIXED: The error with the appearance of the tags suggestion
* FIXED: Problem with artificially duplicated search forms occurred in the Mega Menu plugin and some themes.
* CHANGE: Enforcing use "box-sizing: border-box" within the search form
* CHANGE: Updated Freemius SDK

= 1.3.1, January 06, 2019 =
* FIXED: PHP error with widget

= 1.3.0, January 06, 2019 =
* ADDED: Speed up search by new search engine based on inverted index
* ADDED: Fuzzy search
* ADDED: Search in variation products SKUs
* ADDED: Search in product attributes
* ADDED: If there are more results than limit, the "See all results..." link will appear
* ADDED: Information about the PRO features
* ADDED: Breadcrumbs for nested product categories
* FIXED: Better synchronization between the ajax search results and the search page
* FIXED: Improvements in the scoring system
* FIXED: Image placeholder for products without image
* FIXED: Add SKU label translatable in the suggestions
* CHANGE: Updated Freemius SDK

= 1.2.1, October 26, 2018 =
* ADDED: Storefront support as a option. Allows to replace the native Storefront search form
* FIXED: Improving the relevance of search results by adding score system
* FIXED: Problem with too big images is some cases
* FIXED: Support for HTML entities in the search results
* FIXED: Bugs with the blur event on mobile devices

= 1.2.0, August 24, 2018 =
* ADDED: Backward compatibility system
* ADDED: Support of image size improvements in Woocommerce 3.3
* ADDED: Dynamic width of the search form
* ADDED: Option to set max width of the search form
* ADDED: DISABLE_NAG_NOTICES support for admin notices
* ADDED: More hooks for developers
* ADDED: Minified version of CSS and JS
* ADDED: Label for taxonomy suggestions
* ADDED: Quantity input for a add to cart button in the Details panel
* FIXED: Problem with covering suggestions by other HTML elements of themes.
* FIXED: Details panel in RTL
* FIXED: Improvements for the IE browser
* CHANGE: Code refactor for better future development. Composer and PSR-4 support (in part).
* CHANGE: Better settings organization
* CHANGE: Updated Freemius SDK

= 1.1.7, April 22, 2018 =
* FIXED: Removed duplicate IDs
* CHANGE: PHP requires tag set to PHP 5.5
* CHANGE: Woocommerce requires tags
* CHANGE: Updated Freemius SDK
* REMOVE: Removed uninstall.php

= 1.1.6, October 01, 2017 =
* FIXED: Disappearing some categories and tags in suggestions
* FIXED: Hidden products were shown in search

= 1.1.5, September 05, 2017 =
* ADDED: Requires PHP tag in readme.txt
* FIXED: PHP Fatal error for PHP &lt; 5.3

= 1.1.4, September 03, 2017 =
* ADDED: Admin notice if there is no WooCommerce installed
* ADDED: Admin notice for better feedback from users
* FIXED: Deleting the 'dgwt-wcas-open' class after hiding the suggestion
* FIXED: Allows displaying HTML entities in suggestions title and description
* FIXED: Better synchronizing suggestions and resutls on a search page
* CHANGE: Move menu item to WooCommerce submenu

= 1.1.3, July 12, 2017 =
* ADDED: New WordPress filters
* FIXED: Repetitive search results
* FIXED: Extra details when there are no results

= 1.1.2, June 7, 2017 =
* FIXED: Replace deprecated methods and functions in WC 3.0.x

= 1.1.1, June 6, 2017 =
* ADDED: Added Portable Object Template file
* ADDED: Added partial polish translation
* FIXED: WooCommerce 3.0.x compatible
* FIXED: Menu items repeated in a search page
* FIXED: Other minor bugs

= 1.1.0, October 5, 2016 =
* NEW: Add WPML compatibility
* FIXED: Repeating search results for products in a admin dashboard
* FIXED: Overwrite default input element rounding for Safari browser

= 1.0.3.1, July 24, 2016 =
* FIXED: Disappearing widgets
* FIXED: Trivial things in CSS

= 1.0.3, July 22, 2016 =
* FIXED: Synchronization WP Query on a search page and ajax search query
* CHANGE: Disable auto select the first suggestion
* CHANGE: Change textdomain to ajax-search-for-woocommerce

= 1.0.2, June 30, 2016 =
* FIXED: PHP syntax error with PHP version &lt; 5.3

= 1.0.1, June 30, 2016 =
* FIXED: Excess AJAX requests in a detail mode
* FIXED: Optimization JS mouseover event in a detail mode
* FIXED: Trivial things in CSS

= 1.0.0, June 24, 2016 =
* ADDED: [Option] Exclude out of stock products from suggestions
* ADDED: [Option] Overwrite a suggestion container width
* ADDED: [Option] Show/hide SKU in suggestions
* ADDED: Add no results note
* FIXED: Search in products SKU
* FIXED: Trivial things in CSS and JS files

= 0.9.1, June 5, 2016 =
* ADDED: Javascript and CSS dynamic compression
* FIXED: Incorrect dimensions of the custom preloader

= 0.9.0, May 17, 2016 =
* ADDED: First public release
