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
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'local' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'root' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

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
define( 'AUTH_KEY',          '[=j93m]zC7oqEBKHQ* ?%^N/mT]#tGzf66.[BeyV!zaqV_-lS]b{vLw4t+@` @~D' );
define( 'SECURE_AUTH_KEY',   '-oTiu%xBO5hD+&)ldf#DOPaOA7|G!]xLUvws~Nr?mIqX}Fa$(xMrY5P}ZDyB6NCZ' );
define( 'LOGGED_IN_KEY',     'Uyoy%Pt3DmgF?|~q-TCkoZ@6ti&hI67FlO(0%5^t,!S33`$t.lX._H[&S&#V2`$r' );
define( 'NONCE_KEY',         '6FNNCoTKn,sW(sr7Q{a6%CnvX}%7TVi1)CTh7!!er^KZ7AfkN:CmHG:8eqcR$ayx' );
define( 'AUTH_SALT',         'kWmgRrv[g5Q$C0nb()fNnjKU+)qo*^>>}U4OHYJf{Lq=q*9-,02FBD/iVj-Q&Ue/' );
define( 'SECURE_AUTH_SALT',  '?5fwqJAQI(IHOEPoSBWNX-5ic&1Ab6raD:3)~>(^<bLqltQrN{<72#m8:58,%3?t' );
define( 'LOGGED_IN_SALT',    'k!9G?7*zQ)JLu@v,4r%(<DH|M(J!sh=<#72O${l4:S^93#!(<yVp].8H}z=HW,@~' );
define( 'NONCE_SALT',        '{vQKm5e5Lib[Aa{t&*[8a.itmG7QJay9.Iaom2Y0TW:nVs1?%IXLGDHR.6Bg|;|B' );
define( 'WP_CACHE_KEY_SALT', '>C*_Zj *2tw+XtLbrd33JF7NRN3[2S*{11i:oXkQV-=w?4WplrVc#xe5aIXto%M<' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


/* Add any custom values between this line and the "stop editing" line. */



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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', true ); // Enables WP debugging
define( 'WP_DEBUG_LOG', true ); // Saves errors to wp-content/debug.log
define( 'WP_DEBUG_DISPLAY', false ); // Prevents errors from showing on the frontend
@ini_set( 'display_errors', 0 );
}
define('DISALLOW_FILE_EDIT', true);

define( 'WP_ENVIRONMENT_TYPE', 'local' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
