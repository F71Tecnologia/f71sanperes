<?php
/*---------------------------------------------------+
| PHP-PHOTORESIZE 
| $! db.php
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

include('mysql4.'.$phpEx);

// Make the database connection.
$db = new sql_db($dbhost, $dbuser, $dbpasswd, $dbname, false);
if(!$db->db_connect_id)
{
	die("Could not connect to the database");
}


?>