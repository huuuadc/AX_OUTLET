=== SCSS Happy Compiler - Compile SCSS to CSS & Automatic Enqueue ===
Contributors: Happy Monkey
Donate link:
Tags: css, scss, sass
Requires at least: 4.0
Tested up to: 6.1
Stable tag: 1.3.10
Requires PHP: 7.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Compile your SCSS code to CSS files and enqueue them automatically. Choose when and how to compile.

== Description ==

= AUTOMATICALLY COMPILE YOUR SCSS FILES TO CSS FILES =

Compile easily your SCSS code to CSS automatically, directly on your server and without installing SASS. You choose when and how to compile.

SCSS is a CSS preprocessor language that adds many features like variables, mixins, imports, nesting, color manipulation, functions, and control directives. All theses features are supported in the plugin.

= HOW IT WORKS =

In the plugin settings pages, you can choose the folder which will contain all of your SCSS files, and the folder which will contain generated CSS files.

The plugin compile your SCSS files recursively, so that SCSS files in subfolders can be compiled too, creating the missing folders to recreate the same path in the CSS folder than the SCSS folder.

As everyone has not the same needs, you will be able to set some useful options.

= ENQUEUE CSS FILES AUTOMATICALLY =

This option let you ask the plugin to automatically include your generated CSS files in your header, thus you won't have to enqueue these files manually.

= COMPILATION OPTIONS =

Choose when you want the plugin to generate the CSS files:

* Always: if you are in development and be sure that all CSS files will be compiled everytime you load a page
* When SCSS has changed: if you just want to compile if you have edited one of your SCSS files ; this option takes into account inclusions and subfolders, so that an included scss file that has been changed will trigger the compilation of the file which includes it
* If logged in: if you want only logged in users to trigger compilation
* If admin logged in: if you want only logged in administrators to trigger compilation

Also, you can choose to generate source map files with your CSS files to better visualize your generated code while debugging.
Exemple: main.css will generate the source map main.css.map.

= MINIFICATION OPTIONS =

Your generated CSS code can be fully or partially minified according to your needs. Here are the minification modes available:
* Expanded
* Compressed
To let you see the difference between all these options, they are previewed live when you change it.

= ERRORS DISPLAYING OPTIONS =

Choose how to display compiling errors in front office:
* Always
* If the WP_DEBUG constant of your WordPress installation is true (in wp_options.php)
* If Logged In: if you want only logged in users to see compilation errors
* If Admin Logged In: if you want only logged in administrators to see compilation errors
* Never
And if you don't want to display errors even in BO, select the last option :
* Never, even in Back Office

= COMPILE CSS FROM EVERY FOLDERS IN YOUR WORDPRESS INSTALLATION in the 'Advanced Paths' tab =

Compile SCSS from any folder of your WordPress installation : themes, plugins, ...
In two textareas, you can specify one path per row: SCSS folder path on the left, CSS folder path on the right.
And you can add as many paths as you need!

= IMPORT / EXPORT SETTINGS in the 'Import / Export' tab =

Download immediately your settings in a .json file, either to use them on another site or to save them.
Upload this .json file on every websites with this plugin installed to overwrite settings.

== Installation ==

This section describes how to install the plugin and get it working.

1. Install Happy WP SCSS Compiler either via the WordPress.org plugin repository or by uploading the files to your server. (See instructions on [how to install a WordPress plugin](https://www.wpbeginner.com/beginners-guide/step-by-step-guide-to-install-a-wordpress-plugin-for-beginners/))
2. Activate Happy WP SCSS Compiler from Plugins page.
3. If it is not done yet, create two folders in your (child) active theme: one for your SCSS files, one for the generated CSS files.
3. Navigate to your administration sidebar in Settings > WP SCSS and set the path of the two folders.
4. Eventually set other options. And enjoy!

== Frequently Asked Questions ==


== Screenshots ==

1. Main compilation settings
2. Add as many paths as you need from Wordpress root
3. Import / Export settings

== Changelog ==

= 1.3.10 =
Upgrade SCSSPHP to 1.11.0, which includes:
- Add support for deep map manipulation in map-get, map-has-key and map-merge (@stof)
- Preserve the type of keys when iterating over maps (@stof)
- Fix the generation of source maps without an input path (@stof)
- Fix the handling of list separators in list functions (@stof)
- Add explicit return types to avoid warnings from the Symfony DebugClassLoader (@shyim)
- Fix the handling of rgb, rgba, hsl and hsla called with a trailing comma in arguments (@stof)
- Fix the handling of negative index in str-insert (@stof)
- Fix the tracking of the location of comments when using sourcemaps (@stof)
- Fix the leaking of an output buffer in case of error during the formatting of the output (@stof)
- Fix the handling of nested at-root in mixins (@stof)
- Remove false positive deprecation warnings when compiling Bootstrap 5.2.0 (@stof)
And even more changes!

= 1.3.9 =
Fix auto-enqueue custom CSS files (only last one was enqueued)
Fix auto-enqueue custom CSS files if same filenames
Remove useless plugin files

= 1.3.8 =
Upgrade SCSSPHP to 1.10.5
Fix auto-enqueue custom CSS files if option selected

= 1.3.7 =
Remove useless auto enqueued CSS & JS files

= 1.3.6 =
Add a new "Display Errors" option which let not to display errors even in back office
Fix a PHP issue about the extension detection in filenames

= 1.3.5 =
Fix warning if SCSS folder is empty

= 1.3.4 =
Upgrade SCSSPHP to 1.8.1
Remove deprecated minification options (Expanded and Compressed remain)
Remove unused Hightlight styles and languages

= 1.3.3 =
Fix compiling issue for normal paths

= 1.3.2 =
Fix compiling issue on "When SCSS has changed" option

= 1.3.1 =
Fix saving empty advanced paths

= 1.3.0 =
Add new tab: you can now specify as many SCSS / CSS paths as you want, from the entire WordPress root folder

= 1.2.1 =
Fix 3 'Notice‘ issues.

= 1.2.0 =
Add new tab: you can now import / export settings

= 1.1.4 =
Fix subdomain issue in @import paths
Fix JS error in some cases (old Divi version)

= 1.1.3 =
Fix some echos
Rename plugin name somewhere

= 1.1.2 =
Improve compilation 'if SCSS has changed' with inclusions and recursion

= 1.1.1 =
Fix empty scss folders error

= 1.1.0 =
Add recursive compilation

= 1.0.6 =
Add Minification options preview

= 1.0.5 =
Remove minification 'OutputBlock' option
Order minification options by compression level

= 1.0.4 =
Add new settings option : "Files starting with an _ won't generate CSS files"

= 1.0.3 =
Add Settings link in administration Plugins page

= 1.0.2 =
Fix empty paths
Fix empty cookie constants for safer pluggable.php include

= 1.0.1 =
Fix quotes compilation in PHP class

= 1.0.0 =
First stable version of the plugin.
