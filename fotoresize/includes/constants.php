<?php
/*---------------------------------------------------+
| PHP-FOTORESIZE 
| $! constants.php
+----------------------------------------------------+
| Copyright © 2008 - Luís Fred
| [url][/url]
+----------------------------------------------------+
| Released under the terms & conditions of v2 of the
| GNU General Public License. For details refer to
| the included gpl.txt file or visit http://gnu.org
+----------------------------------------------------*/

if ( !defined('IN_PHPPHOTORESIZE') )
{
	die("Inacessivel");
}

// Debug Level
//define('DEBUG', 1); // Debugging on
define('DEBUG', 1); // Debugging off

define('IMAGES', '/images/');
define('TEMP', 'temp/');
define('RESIZED', 'resized/');
define('REFLECT', 'reflect/');
define('CONFIG_TABLE', $table_prefix . 'config');
define('DIR_SEPARATOR', '/');
// SQL codes
define('BEGIN_TRANSACTION', 1);
define('END_TRANSACTION', 2);


// Error codes
define('GENERAL_MESSAGE', 200);
define('GENERAL_ERROR', 202);
define('CRITICAL_MESSAGE', 203);
define('CRITICAL_ERROR', 204);


?>