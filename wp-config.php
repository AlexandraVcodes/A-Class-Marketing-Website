<?php
/**
 * Grundeinstellungen für WordPress
 *
 * Diese Datei wird zur Erstellung der wp-config.php verwendet.
 * Du musst aber dafür nicht das Installationsskript verwenden.
 * Stattdessen kannst du auch diese Datei als „wp-config.php“ mit
 * deinen Zugangsdaten für die Datenbank abspeichern.
 *
 * Diese Datei beinhaltet diese Einstellungen:
 *
 * * Datenbank-Zugangsdaten,
 * * Tabellenpräfix,
 * * Sicherheitsschlüssel
 * * und ABSPATH.
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Datenbank-Einstellungen - Diese Zugangsdaten bekommst du von deinem Webhoster. ** //
/**
 * Ersetze datenbankname_hier_einfuegen
 * mit dem Namen der Datenbank, die du verwenden möchtest.
 */
define( 'DB_NAME', "c17_feinkorn" );


/**
 * Ersetze benutzername_hier_einfuegen
 * mit deinem Datenbank-Benutzernamen.
 */
define( 'DB_USER', "c17_feinkorn" );


/**
 * Ersetze passwort_hier_einfuegen mit deinem Datenbank-Passwort.
 */
define( 'DB_PASSWORD', "eH!He2cD" );


/**
 * Ersetze localhost mit der Datenbank-Serveradresse.
 */
define( 'DB_HOST', "localhost" );


/**
 * Der Datenbankzeichensatz, der beim Erstellen der
 * Datenbanktabellen verwendet werden soll
 */
define( 'DB_CHARSET', 'utf8mb4' );


/**
 * Der Collate-Type sollte nicht geändert werden.
 */
define( 'DB_COLLATE', '' );

/**#@+
 * Sicherheitsschlüssel
 *
 * Ändere jeden untenstehenden Platzhaltertext in eine beliebige,
 * möglichst einmalig genutzte Zeichenkette.
 * Auf der Seite {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * kannst du dir alle Schlüssel generieren lassen.
 *
 * Du kannst die Schlüssel jederzeit wieder ändern, alle angemeldeten
 * Benutzer müssen sich danach erneut anmelden.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'wa#(}>51KK:k=<HK*?DArdLNDqEiu%f*KOvHDvs#lk,7c(MT*| kWfz{!j[U~At6' );

define( 'SECURE_AUTH_KEY',  'WTRC,R@5mz8)}C4eCR2+(CClINvuDKqWQX:iHO EV+A;qOUTgux)s,oWsI1=fu~o' );

define( 'LOGGED_IN_KEY',    'DptuWKJ)kh}9G>$M?[&H];,$kQ4HilVA/4?!<M_l88(U7)Bbg+At3?y<i##KJKb|' );

define( 'NONCE_KEY',        'R5h}OswYIj[d=^?;vx%9Q3Grkw81]fF_Z: CiqqO|J>!6M%(<%W#Dz,{?Y}T(e=X' );

define( 'AUTH_SALT',        'yrP2n~5o;SMO,<(]/H?AsZEtML`bSo97jVv&%@U0>uq3v2@xozP0Jp3 Ql#@r6II' );

define( 'SECURE_AUTH_SALT', 'crUEPt=+SMy~rRT{<<|kR2Xl4neL,Ijl.KZ.dL >iUlGs+MVpXLI?!Pr>e7>?R+R' );

define( 'LOGGED_IN_SALT',   'l+Z|/OX7s1(:12W-UY6#:yYNVFQF5y0sZ[b=nPjc^M;DUq>u$qhSG8:,{Qq#9`@&' );

define( 'NONCE_SALT',       'wv$.j/4oGsTLJ-SzEBZT~  9+4TmIxPTP#C+1.Kkoya~}+f/8,ruEHDPChGadZgv' );


/**#@-*/

/**
 * WordPress Datenbanktabellen-Präfix
 *
 * Wenn du verschiedene Präfixe benutzt, kannst du innerhalb einer Datenbank
 * verschiedene WordPress-Installationen betreiben.
 * Bitte verwende nur Zahlen, Buchstaben und Unterstriche!
 */
$table_prefix = 'wp_';


/**
 * Für Entwickler: Der WordPress-Debug-Modus.
 *
 * Setze den Wert auf „true“, um bei der Entwicklung Warnungen und Fehler-Meldungen angezeigt zu bekommen.
 * Plugin- und Theme-Entwicklern wird nachdrücklich empfohlen, WP_DEBUG
 * in ihrer Entwicklungsumgebung zu verwenden.
 *
 * Besuche den Codex, um mehr Informationen über andere Konstanten zu finden,
 * die zum Debuggen genutzt werden können.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Füge individuelle Werte zwischen dieser Zeile und der „Schluss mit dem Bearbeiten“ Zeile ein. */



/* Das war’s, Schluss mit dem Bearbeiten! Viel Spaß. */
define( 'DUPLICATOR_AUTH_KEY', 'Lr6<NNr~F%TUaCH-Z}o<D8y6xprH%^80Oh%b1D:J4EuxS&GLtdtOKxQk#K`RCl3V' );
/* That's all, stop editing! Happy publishing. */

/** Der absolute Pfad zum WordPress-Verzeichnis. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname(__FILE__) . '/' );
}

// adjust Redis host and port if necessary 
define( 'WP_REDIS_HOST', '127.0.0.1' );
define( 'WP_REDIS_PORT', 6379 );

// change the prefix and database for each site to avoid cache data collisions
define( 'WP_REDIS_PREFIX', 'feinkorn.at' );
define( 'WP_REDIS_DATABASE', 0 ); // 0-15

// reasonable connection and read+write timeouts
define( 'WP_REDIS_TIMEOUT', 1 );
define( 'WP_REDIS_READ_TIMEOUT', 1 );

/** Definiert WordPress-Variablen und fügt Dateien ein.  */
require_once ABSPATH . 'wp-settings.php';
