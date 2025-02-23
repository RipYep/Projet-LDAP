<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wordpress' );

/** Database username */
define( 'DB_USER', 'wp-admin' );

/** Database password */
define( 'DB_PASSWORD', 'wp-admin' );

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
define( 'AUTH_KEY',         ':3 v1P9.(<mJ+,]v:CqML[[RbA/I/wjRvRX9$s<{}k6H*fe%5<>Fu9g2}2pl/s[a' );
define( 'SECURE_AUTH_KEY',  'GzYk[m,u2U`!{+x6>rC0V;=)@5b1F>?9Tk-g+;!}2=o<<Y3g_-sM5k{DyU2;qZI+' );
define( 'LOGGED_IN_KEY',    ']*C7+0mK)|B~#{&QNr D<h:&eMC>p38PCAER<eWQFZ#D;cfk0N`jhS{Uv#*XCnjp' );
define( 'NONCE_KEY',        '/e)zR1ZsIA:&`A4a).uQnJRp8nnYS!gzaJVZdY;k> S!I(OhYacN[t#XD|JLymz|' );
define( 'AUTH_SALT',        'R/~CNe$PX=CO+IKt(cVzHi0PK3pcCUir6CI)@)#X86.[G{@CH@JKojX_U+&7(r2G' );
define( 'SECURE_AUTH_SALT', '|R:B,bD[f<)UjT4 32i8G|T0rWp9i}G+8VmVL@j[,/6u>)~Ph7nelRRR.v.Wg2(e' );
define( 'LOGGED_IN_SALT',   'P{}JgbQ+~0U)ucE]V uYyeW;g2e{~~mN0=pGS{/NIcF95jDPE{/6Fns=;<-5U.@$' );
define( 'NONCE_SALT',       '3HnPkK|<%y2ToB7Ke4~`6>R8b!a/b{//md;#7cBF/7uf_%/bX?/-`uX;>uh{jW 2' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
 */
$table_prefix = 'wp_';

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
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */
define('WP_TEMP_DIR', dirname(__FILE__) . '/wp-content/temp/');

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
