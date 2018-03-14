<?php
/*---------------------------------------------------+
| PHP-FOTORESIZE 
| $id install.php
+----------------------------------------------------+
| Copyright © 2008 - Luís Fred
| [url][/url]
+----------------------------------------------------+
| Released under the terms & conditions of v2 of the
| GNU General Public License. For details refer to
| the included gpl.txt file or visit http://gnu.org
+----------------------------------------------------*/


error_reporting  (E_ERROR | E_WARNING | E_PARSE); // This will NOT report uninitialized variables
set_magic_quotes_runtime(0); // Disable magic_quotes_runtime

define('IN_PHPPHOTORESIZE', true);
$fotoresize_root_path = (defined('PHPPHOTORESIZE_ROOT_PATH')) ? PHPPHOTORESIZE_ROOT_PATH : '../';
$phpEx = substr(strrchr(__FILE__, '.'), 1);

define("PR_SELF", basename($_SERVER['PHP_SELF']));

// Initialise some basic arrays
//$lang = array();
$error = false;


// Define schema info
$available_dbms = array(
	'mysql4' => array(
		'SCHEMA'		=> 'mysql', 
		'DELIM'			=> ';', 
		'DELIM_BASIC'	=> ';',
		'COMMENTS'		=> 'remove_remarks'
	)
);



$dbhost = (!empty($_POST['dbhost'])) ? $_POST['dbhost'] : 'localhost';
$dbuser = (!empty($_POST['dbuser'])) ? $_POST['dbuser'] : '';
$dbpasswd = (!empty($_POST['dbpasswd'])) ? $_POST['dbpasswd'] : '';
$dbname = (!empty($_POST['dbname'])) ? $_POST['dbname'] : '';

$table_prefix = (!empty($_POST['prefix'])) ? $_POST['prefix'] : '';

$admin_name = (!empty($_POST['admin_name'])) ? $_POST['admin_name'] : '';
$admin_pass1 = (!empty($_POST['admin_pass1'])) ? $_POST['admin_pass1'] : '';
$admin_pass2 = (!empty($_POST['admin_pass2'])) ? $_POST['admin_pass2'] : '';
$script_path = (!empty($_POST['script_path'])) ? $_POST['script_path'] : str_replace('install', '', dirname($_SERVER['PHP_SELF']));

if (!empty($_POST['server_name']))
{
	$server_name = $_POST['server_name'];
}
else
{
	
	if (!empty($_SERVER['SERVER_NAME']) || !empty($_ENV['SERVER_NAME']))
	{
		$server_name = (!empty($_SERVER['SERVER_NAME'])) ? $_SERVER['SERVER_NAME'] : $_ENV['SERVER_NAME'];
	}
	else if (!empty($_SERVER['HTTP_HOST']) || !empty($_ENV['HTTP_HOST']))
	{
		$server_name = (!empty($_SERVER['HTTP_HOST'])) ? $_SERVER['HTTP_HOST'] : $_ENV['HTTP_HOST'];
	}
	else
	{
		$server_name = '';
	}
}


	if(isset($_POST['submit'])){
		
	include($fotoresize_root_path.'includes/db.'.$phpEx);

	$dbms_schema = 'schemas/' . $available_dbms['mysql4']['SCHEMA'] . '_schema.sql';
	$dbms_basic = 'schemas/' . $available_dbms['mysql4']['SCHEMA'] . '_basic.sql';

	$remove_remarks = $available_dbms['mysql4']['COMMENTS'];;
	$delimiter = $available_dbms['mysql4']['DELIM']; 
	$delimiter_basic = $available_dbms['mysql4']['DELIM_BASIC']; 
		
				include($fotoresize_root_path.'includes/sql_parse.'.$phpEx);

				$sql_query = @fread(@fopen($dbms_schema, 'r'), @filesize($dbms_schema));
				$sql_query = preg_replace('/phpphotoresize_/', $table_prefix, $sql_query);

				$sql_query = remove_remarks($sql_query);
				$sql_query = split_sql_file($sql_query, $delimiter);

				for ($i = 0; $i < sizeof($sql_query); $i++)
				{
					if (trim($sql_query[$i]) != '')
					{
						if (!($result = $db->sql_query($sql_query[$i])))
						{
							$error = $db->sql_error();
			
					echo('<script>alert("Install_db_error <br />"'.$error['message'].'"");</script>');
							exit;
						}
					}
				}
					
				$sql_query = @fread(@fopen($dbms_basic, 'r'), @filesize($dbms_basic));
				$sql_query = preg_replace('/phpphotoresize_/', $table_prefix, $sql_query);

				$sql_query = remove_remarks($sql_query);
				$sql_query = split_sql_file($sql_query, $delimiter_basic);

				for($i = 0; $i < sizeof($sql_query); $i++)
				{
					if (trim($sql_query[$i]) != '')
					{
						if (!($result = $db->sql_query($sql_query[$i])))
						{
							$error = $db->sql_error();

					echo('<script>alert("Install_db_error <br />"'.$error['message'].'"");</script>');
							//echo('Installer_Error => Install_db_error <br />' . $error['message']);
							exit;
						}
					}
				}
			

			// Ok at this point they have entered their admin password, let's go 
			// ahead and create the admin account with some basic default information
			// that they can customize later, and write out the config file.  After
			// this we are going to pass them over to the admin_forum.php script
			// to set up their forum defaults.
			$error = '';

			// Update the default admin user with their information.
			$sql = "INSERT INTO " . $table_prefix . "config (config_name, config_value) 
				VALUES ('board_startdate', " . time() . ")";
			if (!$db->sql_query($sql))
			{
				$error .= "Could not insert board_startdate :: " . $sql . " :: " . __LINE__ . " :: " . __FILE__ . "<br /><br />";
			}

		

			$update_config = array(
				'script_path'	=> $script_path,
				'server_name'	=> $server_name,
			);

			while (list($config_name, $config_value) = each($update_config))
			{
				$sql = "UPDATE " . $table_prefix . "config 
					SET config_value = '$config_value' 
					WHERE config_name = '$config_name'";
				if (!$db->sql_query($sql))
				{
					$error .= "Cerro no banco de dados :: " . $sql . " :: " . __LINE__ . " :: " . __FILE__ . "<br /><br />";
				}
			}

		

			if ($error != '')
			{
				
				echo('Installer_Error => Install_db_error <br /><br />' . $error);
				exit;
			}
		

			// Write out the config file.
			$config_data = '<?php'."\n\n";
			$config_data .= "\n// arquivo de configuração do phpPhotoResize v0.2 gerado dinamicamente.\n//Qualquer mudança neste arquivo ira comprometer o correto funcionamento do aplicativo!\n\n";
			$config_data .= '$dbhost = \'' . $dbhost . '\';' . "\n";
			$config_data .= '$dbname = \'' . $dbname . '\';' . "\n";
			$config_data .= '$dbuser = \'' . $dbuser . '\';' . "\n";
			$config_data .= '$dbpasswd = \'' . $dbpasswd . '\';' . "\n\n";
			$config_data .= '$table_prefix = \'' . $table_prefix . '\';' . "\n\n";
			$config_data .= 'define(\'PHPPHOTORESIZE_INSTALLED\', true);'."\n\n";	
			$config_data .= '?' . '>'; // Done this to prevent highlighting editors getting confused!

			@umask(0111);
			$no_open = FALSE;

			// Unable to open the file writeable do something here as an attempt
			// to get around that...
			if (!($fp = @fopen($fotoresize_root_path . 'config.'.$phpEx, 'w')))
			{
				echo('<script>alert("impossivel abrir/escrever no arquivo config.php");</script>');
			}

			$result = @fputs($fp, $config_data, strlen($config_data));
			@fclose($fp);
						

			
	}
	
		// Open config.php ... if it exists
		if (@file_exists($fotoresize_root_path . 'config.'.$phpEx))
		{
		include($fotoresize_root_path .'config.'.$phpEx);
		}
		if (defined("PHPPHOTORESIZE_INSTALLED"))
		{
		Header('Location: ' . $fotoresize_root_path. 'index.' . $phpEx);
		}


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>

<head>
	<meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
	<meta http-equiv="content-style-type" content="text/css" />
	<meta http-equiv="content-language" content="" />
	<meta http-equiv="imagetoolbar" content="no" />
	<meta name="resource-type" content="document" />
	<meta name="distribution" content="global" />
	<meta name="copyright" content="Foto Resizer" />
	<meta name="keywords" content="" />
	<meta name="description" content="" />
	<link rel="shortcut icon" href="images/icon.gif" type="image/ico" />
	<title>::Instalador do phpPhotoResize::</title>
	<style type="text/css">
	<!--

/* General markup styles
---------------------------------------- */
* {
	/* Reset browsers default margin, padding and font sizes */
	margin: 0;
	padding: 0;
	font-size: 100%;
}

body, div, p, th, td, li, dd {
	font-size: x-small;
	voice-family: "\"}\"";
	voice-family: inherit;
	font-size: small
}

html>body, html>div, html>p, html>th, html>td, html>li, html>dd {
	font-size: small
}

html {
	color: #536482;
	background: #DBD7D1;
	/* Always show a scrollbar for short pages - stops the jump when the scrollbar appears. non-ie browsers */
	height: 100%;
	margin-bottom: 1px;
}

body {
	/* Text-Sizing with ems: http://www.clagnut.com/blog/348/ */
	font-family: "Lucida Grande", Verdana, Helvetica, Arial, sans-serif;
	color: #536482;
	background: #DBD7D1;
	font-size: 62.5%;	/* This sets the default font size to be equivalent to 10px */
	margin: 10px 15px;
}	
	
/* Main blocks
---------------------------------------- */
#wrap {
	padding: 0 0 15px 0;
	min-width: 615px;
}

#page-header {
	clear: both;
	text-align: right;
	background: url("images/logo.gif") top left no-repeat;
	height: 49px;
	font-size: 0.85em;
	margin-bottom: 10px;
}

.rtl #page-header {
	text-align: left;
	background: url("images/logo.gif") top right no-repeat;
}

#page-header h1 {
	color: #767676;
	font-family: "Trebuchet MS",Helvetica,sans-serif;
	font-size: 1.70em;
	padding-top: 10px;
}

#page-header p {
	font-size: 1.00em;
}

#page-header p#skip {
	display: none;
}

#page-body {
	clear: both;
	min-width: 700px;
}

#page-footer {
	clear: both;
	font-size: 0.75em;
	text-align: center;
}

#content {
	padding: 30px 10px 10px;
	position: relative;
}

#content h1 {
	color: #115098;
	line-height: 1.2em;
	margin-bottom: 0;
}

#main {
	float: left;
	width: 76%;
	margin: 0 0 0 3%;
	min-height: 350px;
}

.rtl #main {
	float: right;
	margin: 0 3% 0 0;
}

* html #main { 
	height: 350px; 
}

#page-body.simple-page-body {
	padding: 0;
	padding-right: 10px;
	min-width: 0;
}


/* Main Panel
---------------------------------------- */
#acp {
	margin: 4px 0;
	padding: 3px 1px;
	min-width: 550px;
	background-color: #FFFFFF;
	border: 1px #999999 solid;
}

.panel {
	background: #F3F3F3 url("images/innerbox_bg.gif") repeat-x top;
	padding: 0;
}



/* General links  */
a:link, a:visited {
	color: #105289;
	text-decoration: none;
}

a:hover {
	color: #BC2A4D;
	text-decoration: underline;
}

a:active {
	color: #368AD2;
	text-decoration: none;
}


fieldset {
	margin: 15px 0;
	padding: 10px;
	border-top: 1px solid #D7D7D7;
	border-right: 1px solid #CCCCCC;
	border-bottom: 1px solid #CCCCCC;
	border-left: 1px solid #D7D7D7;
	background-color: #FFFFFF;
	position: relative;
}

.rtl fieldset {
	border-top: 1px solid #D7D7D7;
	border-right: 1px solid #D7D7D7;
	border-bottom: 1px solid #CCCCCC;
	border-left: 1px solid #CCCCCC;
}

* html fieldset {
	padding: 0 10px 5px 10px;
}

fieldset p {
	font-size: 0.85em;
}

legend {
	padding: 1px 0;
	font-family: Tahoma,arial,Verdana,Sans-serif;
	font-size: .9em;
	font-weight: bold;
	color: #115098;
	margin-top: -.4em;
	position: relative;
	text-transform: none;
	line-height: 1.2em;
	top: 0;
	vertical-align: middle;
}

/* Hide from macIE \*/
legend { top: -1.2em; }
/* end */

* html legend {
	margin: 0 0 -10px -7px;
	line-height: 1em;
	font-size: .85em;
}

/* Holly hack, .rtl comes after html */
* html .rtl legend {
	margin: 0;
	margin-right: -7px;
}

input, textarea {
	font-family: Verdana, Helvetica, Arial, sans-serif;
	font-size: 0.90em;
	font-weight: normal;
	cursor: text;
	vertical-align: middle;
	padding: 2px;
	color: #111111;
	border-left: 1px solid #AFAEAA;
	border-top: 1px solid #AFAEAA;
	border-right: 1px solid #D5D5C8;
	border-bottom: 1px solid #D5D5C8;
	background-color: #E3DFD8;
}

.rtl input, .rtl textarea {
	border-left: 1px solid #D5D5C8;
	border-top: 1px solid #AFAEAA;
	border-right: 1px solid #AFAEAA;
	border-bottom: 1px solid #D5D5C8;
}

input:hover, textarea:hover {
	border-left: 1px solid #AFAEAA;
	border-top: 1px solid #AFAEAA;
	border-right: 1px solid #AFAEAA;
	border-bottom: 1px solid #AFAEAA;
	background-color: #E9E9E2;
}

input.langvalue, textarea.langvalue {
	width: 90%;
}



label {
	cursor: pointer;
	font-size: 0.85em;
	padding: 0 5px 0 0;
}

.rtl label {
	padding: 0 0 0 5px;
}

label input {
	font-size: 1.00em;
	vertical-align: middle;
}

dl {
	font-family: Verdana, Helvetica, Arial, sans-serif;
	font-size: 1.00em;
}

dt {
	float: left;
	width: auto;
}

.rtl dt {
	float: right;
}

dd { color: #666666;}
dd + dd { padding-top: 5px;}
dt span { padding: 0 5px 0 0;}
.rtl dt span { padding: 0 0 0 5px;}

dt .explain { font-style: italic;}

dt label {
	font-size: 1.00em;
	text-align: left;
	font-weight: bold;
	color: #4A5A73;
}

.rtl dt label {
	text-align: right;
}

dd label {
	font-size: 1.00em;
	white-space: nowrap;
	margin: 0 10px 0 0;
	color: #4A5A73;
}

.rtl dd label {
	margin: 0 0 0 10px;
}

html>body dd label input { vertical-align: text-bottom;}	/* Tweak for Moz to align checkboxes/radio buttons nicely */

dd input {
	font-size: 1.00em;
	max-width: 100%;
}


fieldset dl {
	margin-bottom: 10px;
	font-size: 0.85em;
}

fieldset dt {
	width: 45%;
	text-align: left;
	border: none;
	border-right: 1px solid #CCCCCC;
	padding-top: 3px;
}



fieldset dd {
	margin: 0 0 0 45%;
	padding: 0 0 0 5px;
	border: none;
	border-left: 1px solid #CCCCCC;
	vertical-align: top;
	font-size: 1.00em;
}


/* Hover highlights for form rows */
fieldset dl:hover dt, fieldset dl:hover dd {
	border-color: #666666;
}

fieldset dl:hover dt label {
	color: #000000;
}

fieldset dl dd label:hover {
	color: #BC2A4D;
}

input:focus, textarea:focus {
	border: 1px solid #BC2A4D;
	background-color: #E9E9E2;
	color: #BC2A4D;
}
fieldset.submit-buttons {
	text-align: center;
	border: none;
	background-color: transparent;
	margin: 0;
	padding: 4px;
	margin-top: -1px;
}

p.submit-buttons {
	text-align: center;
	margin: 0;
	padding: 4px;
	margin-top: 10px;
}

fieldset.submit-buttons input, p.submit-buttons input {
	padding: 3px 2px;
}

fieldset.submit-buttons legend {
	display: none;
}
a.button1, input.button1, input.button3,
a.button2, input.button2 {
	width: auto !important;
	padding: 1px 3px 0 3px;
	font-family: "Lucida Grande", Verdana, Helvetica, Arial, sans-serif;
	color: #000;
	font-size: 0.85em;
	background: #EFEFEF;
	cursor: pointer;
}

a.button1, input.button1 {
	font-weight: bold;
	border: 1px solid #666666;
}

/* Hover states */
a.button1:hover, input.button1:hover,
a.button2:hover, input.button2:hover {
	border: 1px solid #BC2A4D;
	background: #EFEFEF url("images/bg_button.gif") repeat bottom;
	color: #BC2A4D;
}

input.disabled {
	font-weight: normal;
	color: #666666;
}
	-->
</style>
</head>
<body>
<div id="wrap">
	<div id="page-header">
		<h1>Instalação</h1><br>
		
							</div>
	
	<div id="page-body">


		<div id="acp">
		<div class="panel">
			<span class="corners-top"><span></span></span>
				<div id="content">
				<form action="<?php PR_SELF ?>" method="post">
						
			<fieldset>
			<legend>Configurações importantes</legend>
	<dl>
			<dt>
<label for="size">Servidor Banco de dados:</label><br />
<span class="explain">
Nome do seu servidor de banco de dados
</span>
</dt>
<dd>
<input id="dbhost" type="text" size="20" maxlength="20" name="dbhost" value="<?php echo ($dbhost != '') ? $dbhost : ''; ?>" />
</dd>
</dl>
<br>
<dl>
<dt>
<label for="size">Nome da base de dados:</label><br />
<span class="explain">
Nome da base de dados que ira armazenar os dados deste aplicativo.
</span>
</dt>
<dd>
<input id="dbname" type="text" size="20" maxlength="20" name="dbname" value="<?php echo ($dbname != '') ? $dbname : ''; ?>"  />
</dd>
<br>
</dl>
<br><br>
<dl>
<dt>
<label for="size">Username(Banco de dados):</label><br />
<span class="explain">
Seu login de acesso a esta base de dados.
</span>
</dt>
<dd>
<input id="dbuser" type="text" size="20" maxlength="20" name="dbuser" value="<?php echo ($dbuser != '') ? $dbuser : ''; ?>"  />
</dd>
<br>
</dl>
<br><br>
<dl>
<dt>
<label for="size">Senha(Banco de dados):</label><br />
<span class="explain">
sua senha de acesso a esta base de dados.
</span>
</dt>
<dd>
<input id="dbpasswd" type="text" size="20" maxlength="20" name="dbpasswd" value="<?php echo ($dbpasswd != '') ? $dbpasswd : ''; ?>"  />
</dd>
<br>
</dl>
<br><br>
<dl>
<dt>
<label for="size">Prefixo para as tabelas do banco de dados:</label><br />
<span class="explain">
Digite um prefixo para as tabelas que serao criadas.
Ex: fotoresize_table, algo_table, etc.
</span>
</dt>
<dd>
<input id="prefix" type="text" size="20" maxlength="20" name="prefix" value="<?php echo (!empty($table_prefix)) ? $table_prefix : "phpphotoresize_"; ?>"  />
</dd>
<br>
</dl>
<br><br>
<dl>
<dt>
<label for="size">Nome do seu dominio:</label><br />
<span class="explain">
Digite o nome do seu servidor.
Ex: localhost, www.dominio.com.br, etc.
</span>
</dt>
<dd>
<input id="server_name" type="text" size="20" maxlength="20" name="server_name" value="<?php echo $server_name; ?>"  />
</dd>
<br>
</dl>
<br><br>
<dl>
<dt>
<label for="size">Script Path:</label><br />
<span class="explain">
Caminho para o aplicativo. 
</span>
</dt>
<dd>
<input id="script_path" type="text" size="20" maxlength="20" name="script_path" value="<?php echo $script_path; ?>"  />
</dd>
<br>
</dl>
<br><br>
	
			</fieldset>						
			
	<fieldset class="submit-buttons">
		<input type="submit" name="submit" value="Instalar" class="button1"  />
					</fieldset>

</form>
							</div>
					</div>
		</div>
	</div>
	
	<div id="page-footer">
	Desenvolvido e mantido por Luís Fred
	</div>
</div>

</body>
</html>











