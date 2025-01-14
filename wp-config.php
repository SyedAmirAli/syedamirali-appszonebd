<?php
require_once 'dotenv.php';

 // Added by AirLift



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
define('DB_NAME', PRODUCTION ? 'u472312318_trainyit' : 'trainyit');

/** Database username */
define('DB_USER', PRODUCTION ? 'u472312318_trainyit' : 'root');

/** Database password */
define('DB_PASSWORD', PRODUCTION ? 'Trainyit@123' : '');

/** Database hostname */
define('DB_HOST', PRODUCTION ? '127.0.0.1' : 'localhost');

/** Database charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The database collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

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
define('AUTH_KEY',          'Xa]pP&VIR [6TNC03j0Q(V+5%wDxwm_a;v46@F:x8ZF<UTb02QIn<*SepXEg],V.');
define('SECURE_AUTH_KEY',   'mjP*:DQ-3,GNw/J>me @:wF;Qn6OHhX_dqDLxa>O;/|-HygFFe-Z87`9MU6SaoZf');
define('LOGGED_IN_KEY',     '=/tyd JY ~29%;)..42R167>t_fd#~Rfw5ZQR8j)68BwGBwa~,_2 {sv`Cu*-[0D');
define('NONCE_KEY',         'JzBW,6,I&nYZ,l8^~(HXI$SN0b&3^+iJZe!)!03nEGp6MJMX#sM2.YR90$o83J]E');
define('AUTH_SALT',         'ZksIf*HM2*)8RZoaXW7_%N.dC{#R0O2GeQHXefF-6hn4TNhLd0ZJREMuOKq$i.sO');
define('SECURE_AUTH_SALT',  'D78jU#5~&Z7ME/ki,GoXC*1hP :&4/S,%0oQm+v/R~(W.>toE6qD4^8I_5{OKt?<');
define('LOGGED_IN_SALT',    '[UD_Ny6SFy/NZiPa,?#p8c9Jpinm<*do1eZdt_Wqasm#BsWr@cPB{O|4a7;TX/5|');
define('NONCE_SALT',        '-#-17mWX49W~XoDR(1G<N.q;T7)NSKJE2Dpkvko-vU~iD=%<x2S3U_^?16.%^rpA');
define('WP_CACHE_KEY_SALT', 'QQGEZAVxY6Z0Q8IR~>V|YKf?bI/R;rvNE]d3aM4(5}- lc)}{{_AzaKrV8|f@A{S');


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
if (! defined('WP_DEBUG') ) {
	define('WP_DEBUG', false );
}

define('FS_METHOD', 'direct');
define('WP_AUTO_UPDATE_CORE', false );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if (! defined('ABSPATH') ) {
	define('ABSPATH', __DIR__ . '/');
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
