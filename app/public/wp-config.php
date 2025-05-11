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
define( 'AUTH_KEY',          'Yz;Yo)#[h+q`8QDJ2D@I@;bc?51f}cuPg!r5;K.&c!b1`b(1^SR;^`[Fg5mIS7fI' );
define( 'SECURE_AUTH_KEY',   '>(O4809$}qO}cpAm9s1Hk*;W8N&)y[fmJe/!7{!u+%C ARK~IdHc}G6W`<ng!/R,' );
define( 'LOGGED_IN_KEY',     'IwC0w-kCzCN*7g@UB,MfUT=qSiX&d7Z)?E>Ly:v@f%%~lA&3RB>=4U>c:2j,HsL(' );
define( 'NONCE_KEY',         'b]40 7;XV`l@hs_YulndE#N)KRatj<k@Z){%2Lbv0K;$+RK57n~>&++mx<=Hl=dx' );
define( 'AUTH_SALT',         '~8xBt]z!u_5RlneGq#k&4%Uj+%#b6[jTs,>4fq/S|>1{*pd0D;^@M*f^p^>v2]m&' );
define( 'SECURE_AUTH_SALT',  'jt:C]a*xedRk~(/pYk?0YBe:JF$O::Ck1&OYR|%)pZ8@z*<BE6N1yNkGD%lvSJ^*' );
define( 'LOGGED_IN_SALT',    '#VQZTK>)%_6}-0!jaz]y?fdj;VqeuX``C+YpDR>79dsQ<g*0VGj!*v?WD~A;c^n|' );
define( 'NONCE_SALT',        '@p_`*|.Po.L-4OpQ],TIG<hTQn[V?D:U D/b2j$eUg6X%*.@T}N;b:3,0Jh/E[vo' );
define( 'WP_CACHE_KEY_SALT', '`F(sN z3]%b.RAq%S93):WT-ex1slRi{Vp{`}F$$CRn|HYEy6jCV|Rlk:e%!m|]N' );


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
	define( 'WP_DEBUG', false );
}

define( 'WP_ENVIRONMENT_TYPE', 'local' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
