<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'u440311185_house');

/** MySQL database username */
define('DB_USER', 'u440311185_house');

/** MySQL database password */
define('DB_PASSWORD', 'u440311185_house');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '&9?|v8M}-#a4p|j]R~&O{Yhm@vwxIGi/NtWX;v$BK?Lr9${K5tXSHoL|HGr+P>4d');
define('SECURE_AUTH_KEY',  'I1t<0}fr@~k((!|IvV${/pd!J30[L@@r.*]Q<o+Zq.zfPojTj;.--gw_,8! `p*w');
define('LOGGED_IN_KEY',    'Re0F;|V{`#5}=betf+ZlGC_bK?v=m{KjuEG#6|yYECh~LGyJU?eS-<iut4qAT@!n');
define('NONCE_KEY',        'uk~9v@,XJvje+l.g4UV gx/Fxf<pX6dD_Iu}DTkMEupV>/;9d$ew=Q>{^wmxlzPK');
define('AUTH_SALT',        'w<J%*pBFoFWX*MTrZhBq1Z$CbS6$C!&!t5(,3>=5?*wBrdc8R!=^;{/y-:bwJ-3j');
define('SECURE_AUTH_SALT', '&;.@l)y]uF|yBGsF0AZH b;f}8N1rZ MS9<sWxwU.^`am Y<}%hV4p7?~URHF/)a');
define('LOGGED_IN_SALT',   '%-%@F9D0U#>8W=|%+@{vG1 KNt.!//fN?+fM1PEz_9Braz,W~4+wtniY#k2{(y? ');
define('NONCE_SALT',       '.WKJ<i4Q#}59+eJLwbg<B5IYMXFl^6cLu+KnEI1wRRu;!m@cjD]*m{2kqVG9/)=P');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
