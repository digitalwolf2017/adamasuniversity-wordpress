<?php
define( 'WP_CACHE', true );

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
define( 'DB_NAME', 'u202799095_tps' );

/** Database username */
define( 'DB_USER', 'u202799095_tps' );

/** Database password */
define( 'DB_PASSWORD', 'Xz0F66n&Xs$' );

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
define( 'AUTH_KEY',         'CM2&TGL%)_0DYziQ@|cz8Obq 7fjvDT6s#v*7<xh_<JfQGp?v@j4Ut&,pu;!]:M)' );
define( 'SECURE_AUTH_KEY',  '5 }<O25ZD)<h7d!G/<j+}+,4L _e7F*(F4)P}DH2S wQ-78bcsY3)ySMvCe`V`M ' );
define( 'LOGGED_IN_KEY',    ',gr^*L!n{HY e.qr!TXj`0mT|gAIsE>6kCoSH%tTA1/ummQ#Xg|<4l0tZK`0%#p#' );
define( 'NONCE_KEY',        ')]ZxV7:I+6lD-E-(j}-VEE<}= 6yM@(FeVm0<C<?1 yl<F$WcwekrO$>iKCGPs9v' );
define( 'AUTH_SALT',        '4cMCWV!*PX]L6;%sPy2v`3vZm}<XDLXZoU.-x<+aCUt_v#Ex5^_TB~=H#qFB?n*4' );
define( 'SECURE_AUTH_SALT', '2Kc+eRq84x#K;LPG{03/afN8F`wp`kz2`g5Z`L=]706f(T@t?wOH1Pn}?:sc4dle' );
define( 'LOGGED_IN_SALT',   'NH{mYAc7=F6RlVqq,f.C/}QLeP>}/&pGFL@E5dmk(_l@MT=O.2<t1v#m|EI5Ah4>' );
define( 'NONCE_SALT',       '$EWm s?9$pLdaxi.b8G|E.0k,#VqbNBxyJMy`_?OA&n1w?RH:V.Izb3*{LZDh9!O' );

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

/* Security Hardening */
define( 'DISALLOW_FILE_EDIT', true );
define( 'WP_DEBUG_DISPLAY', false );
define( 'WP_DEBUG_LOG', false );
@ini_set( 'display_errors', '0' );

define( 'WP_AUTO_UPDATE_CORE', true );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
