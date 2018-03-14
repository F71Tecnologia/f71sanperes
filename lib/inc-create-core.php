<?php
/**
 * Código para incorporação e criação de Core
 * 
 * @file      inc-create-core.php
 * @license   http://www.gnu.org/licenses/gpl-3.0.txt GNU General Public License
 * @link      
 * @copyright 2016 F71
 * @author    Jacques <jacques@f71.com.br>
 * @package   createCoreClass
 * @access    public  
 * @version:  3.00.7788L - 03/05/2016 - Jacques - Versão Inicial
 * @version:  3.00.7788L - 07/05/2016 - Jacques - Adicionado validação de inclusão de arquivo
 * @version:  3.00.0170F - 11/07/2016 - Jacques - Adicionado opção de carga de arquivo de configuração com nome personalizado
 * @todo 
 * @example:  
 * 
 * 
 */

try {
    
    $this->setValue(0);

    $file['inc'] = ROOT_LIB.'inc-projeto.php'; 
    $file['config'] = ROOT_LIB.'ConfigClass.php'; 
    $file['error'] = ROOT_LIB.'ErrorClass.php'; 
    $file['mysql'] = ROOT_LIB.'MySqlClass.php'; 
    $file['date'] = ROOT_LIB.'DateClass.php'; 
    $file['file'] = ROOT_LIB.'FileClass.php'; 
    $file['cmbBox'] = ROOT_LIB.'CmbBoxClass.php'; 
    $file['log'] = ROOT_LIB.'LogClass.php'; 
    $file['lib'] = ROOT_LIB.'LibClass.php'; 
    
    foreach ($file as $key => $value) {

        if(!file_exists($file[$key])) die(_("# Não foi possível carregar o arquivo do core ").$file[$key]._(" da classe ").__CLASS__);
        
    }
    
    if(!isset($this->error)){

        if(!include_once($file['error'])) die(_("# Não foi possível incluir {$file['error']}"));         

        $this->error = new ErrorClass();     
        
        if(!is_object($this->error)) die(_("# Não foi possível instânciar a classe error"));
        
    }
    
    if(!isset($this->config)){

        if(!include_once($file['config'])) die(_("# Não foi possível incluir {$file['config']}"));    

        if(!include($file['inc'])) die(_("# Não foi possível incluir {$file['inc']}"));   
        
        $this->config = new ConfigClass($file_setup);        

        if(!is_object($this->config)) $this->error->set(_("# Não foi possível instânciar a classe config"),E_FRAMEWORK_ERROR);
        
    }

    if(!isset($this->db)){
        
        if(!include_once($file['mysql'])) die(_("# Não foi possível incluir ").$file['mysql']);         

        $this->db = new MySqlClass();

        if(!is_object($this->db)) $this->error->set(_("# Não foi possível instânciar a classe db"),E_FRAMEWORK_ERROR);
        
    }

    if(!isset($this->date)){

        if(!include_once($file['date'])) die(_("# Não foi possível incluir ").$file['date']);         

        $this->date = new DateClass();

        if(!is_object($this->date)) $this->error->set(_("# Não foi possível instânciar a classe date"),E_FRAMEWORK_ERROR);
        
    }

    if(!isset($this->file)){

        if(!include_once($file['file'])) die(_("# Não foi possível incluir ").$file['file']);         

        $this->file = new FileClass();

        if(!is_object($this->file)) $this->error->set(_("# Não foi possível instânciar a classe file"),E_FRAMEWORK_ERROR);
        
    }
    
    if(!isset($this->cmbBox)){
        
        if(!include_once($file['cmbBox'])) die(_("Não foi possível incluir ").$file['cmbBox']);         

        $this->cmbBox = new cmbBoxClass($this->db);
        
        if(!is_object($this->cmbBox)) $this->error->set(_("Não foi possível instânciar a classe cmbBox"),E_FRAMEWORK_ERROR);

    }
    
    if(!isset($this->log)){
        
        if(!include_once($file['log'])) die(_("Não foi possível incluir ").$file['log']);         

        $this->log = new LogClass();
        
        if(!is_object($this->log)) $this->error->set(_("Não foi possível instânciar a classe log"),E_FRAMEWORK_ERROR);

    }
    
    if(!isset($this->lib)){
        
        if(!include_once($file['lib'])) die(_("# Não foi possível incluir ").$file['lib']);         

        $this->lib = new LibClass();
        
        if(!is_object($this->lib)) $this->error->set(_("# Não foi possível instânciar a classe lib"),E_FRAMEWORK_ERROR);

    }
    
    $this->setValue(1);

} catch (Exception $ex) {

    $this->error->set(_("# Não foi possível aplicar o método createCoreClass"),E_FRAMEWORK_WARNING,$ex);

    die($this->error->getAllMsgCode());

}

