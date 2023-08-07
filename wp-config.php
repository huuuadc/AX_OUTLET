<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'rexar628_ax_ecom' );

/** Database username */
define( 'DB_USER', 'rexar628_ax_ecom' );

/** Database password */
define( 'DB_PASSWORD', ';QTh?v4=q],X' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         ';xiYIBG,ELn>`jiG.Gu`}cg{Czk)t@iaKa:(!s.4K;SYkM9VthI24<LuqE40G/y0' );
define( 'SECURE_AUTH_KEY',  'ubs`?|!vF[<bKUN(CO:1/Un80/[n5%~+oUktiMtO?iLHq=t1}r*rm&m:*$r}.I4n' );
define( 'LOGGED_IN_KEY',    '{0)Tbo%7rf1Bky%(x),vBG5OtIVxqf4.at:,{lM{-<~8##euX]M,c%TC?yg{MPx ' );
define( 'NONCE_KEY',        '/=E1F#?n}GCa-Inl^1x{z~xj^R/6D?tI+@;+WN<k/]j 4toiYxWpaG<KMUESEFY?' );
define( 'AUTH_SALT',        'R-#^#1DL3`|h)|;+UGI,}ivU^=_CQ*(D:c01Z06QbYr>$XnvI@?&WHzb2T&4jyG-' );
define( 'SECURE_AUTH_SALT', 'k]rG8&n0^<M),!m;8v*5@$Ya}3Pz7/yAXxtrN3Xx$BbAg<ykTcajvNcV=iahOb`g' );
define( 'LOGGED_IN_SALT',   'tm^O%@#3y=#cIbbGwH67tzjVcX.SqH[,Du{0i_;UG/pMBH*px7 S(*=[@UtSNSIx' );
define( 'NONCE_SALT',       '@Q@sZF -xaSlt1Q!E1KfktEx8! <5Yc0R8`a<]@e3*2PzW;H#q[/9d@>]iS8} ;%' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'ecom_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/**
* Disable edit function and add plugin in admin page
*/
define( 'DISALLOW_FILE_EDIT',true);
define( 'DISALLOW_FILE_MODS', true);

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
