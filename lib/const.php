<?php
/**
 * Constantes para uso do framework.
 * 
 * Esse arquivo deve ser incluído em todos códigos do framework
 * 
 * @code
 * require_once 'PATH_TO/Boo.php';
 * 
 * @endcode
 * 
 * @file
 * @license		
 * @link		http://consello.com.br/svn/lagos /framework/lib/const.php
 * @copyright           2016 F71
 * @author		Jacques <jacques@f71.com.br>
 * @version		1.0.0
 * 
 * @todo		
 */
if(!defined('LOCALES_GETTEXT_DEFAULT')) DEFINE("LOCALES_GETTEXT_DEFAULT", 'pt_BR');
if(!defined('LOCALES_GETTEXT')) DEFINE("LOCALES_GETTEXT", 'pt_BR,en_US');

if(!defined('DOMAIN')) DEFINE("DOMAIN", $_SERVER['HTTP_HOST']);
//if(!defined('LOCALE')) DEFINE("LOCALE", $locale);

if(!defined('ROOT_VENDOR')) DEFINE("ROOT_VENDOR", ROOT_DIR.'vendor/');
if(!defined('ROOT_APP')) DEFINE("ROOT_APP", ROOT_DIR.'app/');
if(!defined('ROOT_APP_CONTROLLER')) DEFINE("ROOT_APP_CONTROLLER", ROOT_DIR.'app/controller/');
if(!defined('ROOT_APP_TEMPLATE')) DEFINE("ROOT_APP_TEMPLATE", ROOT_DIR.'app/template/');
if(!defined('ROOT_LOCALE')) DEFINE("ROOT_LOCALE", ROOT_DIR.'locale/');
if(!defined('ROOT_LIB')) DEFINE("ROOT_LIB", ROOT_DIR.'lib/');
if(!defined('ROOT_TEMPLATE')) DEFINE("ROOT_TEMPLATE", ROOT_DIR.'template/');
if(!defined('ROOT_CLASS')) DEFINE("ROOT_CLASS", ROOT_DIR.'class/');
if(!defined('ROOT_WWW')) DEFINE("ROOT_WWW", ROOT_DIR.'www/');
if(!defined('ROOT_WWW_ARQ_FERIAS')) DEFINE("ROOT_WWW_ARQ_FERIAS", ROOT_OLD_DIR.'intranet/rh_novaintra/ferias/arquivos/');

if(!defined('ROOT_OLD_INTRANET')) DEFINE("ROOT_OLD_INTRANET", ROOT_OLD_DIR.'intranet/');
if(!defined('ROOT_OLD_CLASS')) DEFINE("ROOT_OLD_CLASS", ROOT_OLD_DIR.'intranet/classes/');
if(!defined('ROOT_OLD_CLASS_GRANT')) DEFINE("ROOT_OLD_CLASS_GRANT", ROOT_OLD_DIR.'intranet/classes_permissoes/');
if(!defined('ROOT_OLD_TEMPLATE')) DEFINE("ROOT_OLD_TEMPLATE", ROOT_OLD_DIR.'intranet/template/');

if(!defined('PATH_ARQ_FERIAS')) DEFINE("PATH_ARQ_FERIAS", 'intranet/rh_novaintra/ferias/arquivos/');
if(!defined('PATH_ARQ_CNAB240')) DEFINE("PATH_ARQ_CNAB240", 'intranet/novoFinanceiro/cnab240/');
if(!defined('PATH_CLASS')) DEFINE("PATH_CLASS", "../../framework/lib/");


if(!defined('DIA')) DEFINE("DIA",0);
if(!defined('MES')) DEFINE("MES",1);
if(!defined('ANO')) DEFINE("ANO",2);

if(!defined('DATA_CONTRATACAO')) DEFINE("DATA_CONTRATACAO",0);
if(!defined('DATA_CONTRATACAO_FMT')) DEFINE("DATA_CONTRATACAO_FMT",1);
if(!defined('DATA_CONTRATACAO_DIA')) DEFINE("DATA_CONTRATACAO_DIA",2);
if(!defined('DATA_CONTRATACAO_MES')) DEFINE("DATA_CONTRATACAO_MES",3);
if(!defined('DATA_CONTRATACAO_ANO')) DEFINE("DATA_CONTRATACAO_ANO",4);
if(!defined('DATA_CONTRATACAO_STATUS')) DEFINE("DATA_CONTRATACAO_STATUS",5);

if(!defined('VENCIDOS')) DEFINE("VENCIDOS",0);
if(!defined('NA_DATA')) DEFINE("NA_DATA",1);
if(!defined('A_VENCER')) DEFINE("A_VENCER",2);

/**
 * Constantes para a classe MySqlClass
 */
if(!defined('QUERY')) DEFINE("QUERY",0);
if(!defined('SELECT')) DEFINE("SELECT",1);
if(!defined('FROM')) DEFINE("FROM",2);
if(!defined('UPDATE')) DEFINE("UPDATE",3);
if(!defined('INSERT')) DEFINE("INSERT",4);
if(!defined('WHERE')) DEFINE("WHERE",5);
if(!defined('SEARCH')) DEFINE("SEARCH",6);
if(!defined('GROUP')) DEFINE("GROUP",7);
if(!defined('HAVING')) DEFINE("HAVING",8);
if(!defined('ORDER')) DEFINE("ORDER",9);
if(!defined('LIMIT')) DEFINE("LIMIT",10);
if(!defined('CALL')) DEFINE("CALL",11);

if(!defined('ADD')) DEFINE("ADD",1);

if(!defined('PARCIAL')) DEFINE("PARCIAL",1);

/**
 * Constantes para definição na forma de retorno de uma função
 */
if(!defined('RETURN_FILE')) DEFINE("RETURN_FILE",1);
if(!defined('RETURN_JSON')) DEFINE("RETURN_JSON",2);
if(!defined('RETURN_HTML')) DEFINE("RETURN_HTML",3);


/**
 * Constantes para a classe ErrorClass
 * 
 * A constante E_FRAMEWORK_LOG não é uma constante de erro e sim uma flag de definição de registro em log
 */
if(!defined('E_FRAMEWORK_LOG')) DEFINE("E_FRAMEWORK_LOG",0);
if(!defined('E_FRAMEWORK_ERROR')) DEFINE("E_FRAMEWORK_ERROR",3);
if(!defined('E_FRAMEWORK_WARNING')) DEFINE("E_FRAMEWORK_WARNING",5);
if(!defined('E_FRAMEWORK_NOTICE')) DEFINE("E_FRAMEWORK_NOTICE",6);

//include('translation.php');

