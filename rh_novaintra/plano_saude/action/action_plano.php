<?php

include("../../../conn.php");
require("../../../wfunction.php");
require("../../../funcoes.php");
require("../../../classes/LogClass.php");
require("../../../classes/PlanoSaudeClass.php");

$charset = mysql_set_charset('utf8');

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : NULL;

$usuario = carregaUsuario();
$objPlanoSaude = new PlanoSaudeClass();
$objLog = new Log();

switch ($action) {
    case 'form_plano' : 
        if(!empty($_REQUEST['id_plano_saude'])) {
            $objPlanoSaude->setIdPlanoSaude($_REQUEST['id_plano_saude']);
            $objPlanoSaude->getPlanoSaude();
            $objPlanoSaude->getRowPlanoSaude();
        } ?>

        <form action="action/action_plano.php" method="post" id="form1">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="form-group">
                        <label class="control-label">Raz&atilde;o:</label>
                        <div><input type="text" class="form-control validate[required]" name="razao" id="razao" placeholder="Raz&atilde;o do Plano de Sa&uacute;de" value="<?=$objPlanoSaude->getRazao()?>"></div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Cnpj:</label>
                        <div><input type="text" class="form-control validate[required]" name="cnpj" id="cnpj" maxlength="18" value="<?=$objPlanoSaude->getCnpj()?>"></div>
                        <input type="hidden" name="action" value="<?=$_REQUEST['tipo']?>">
                        <?=(!empty($_REQUEST['id_plano_saude'])) ? '<input type="hidden" name="id_plano_saude" value="'.$_REQUEST['id_plano_saude'].'">' : null?>
                    </div>
                </div>
            </div>
        </form>
        <script>
        $(function(){
            $('#cnpj').mask('99.999.999/9999-99');
        });
        </script>
    <?php break;
    
    case 'verificar_cnpj' :
        $objPlanoSaude->setCnpj($_REQUEST['cnpj']);
        $objPlanoSaude->getPlanoSaude();
        echo $objPlanoSaude->getNumRowPlanoSaude();
    break;
    
    case 'Cadastrar' :
        $objPlanoSaude->setRazao($_REQUEST['razao']);
        $objPlanoSaude->setCnpj($_REQUEST['cnpj']);
        $objPlanoSaude->setIdFuncionarioCad($usuario['id_funcionario']);
        $objPlanoSaude->setDataCad(date('Y-m-d H:i:s'));
        $id_plano_saude = $objPlanoSaude->insertPlanoSaude();
        
        $objLog->log('2', "Cadastro de Plano de Saúde: ID{$id_plano_saude}",'plano_saude');
        header("Location: ../../plano_saude");
    break;
    
    case 'Editar' :
        $objPlanoSaude->setRazao($_REQUEST['razao']);
        $objPlanoSaude->setCnpj($_REQUEST['cnpj']);
        $objPlanoSaude->setIdPlanoSaude($_REQUEST['id_plano_saude']);
        $objPlanoSaude->setIdFuncionarioCad($usuario['id_funcionario']);
        $antigo = $objLog->getLinha('plano_saude',$_REQUEST['id_plano_saude']);
        $objPlanoSaude->updatePlanoSaude();
        $novo = $objLog->getLinha('plano_saude',$_REQUEST['id_plano_saude']);
        $objLog->log('2', "Edição de Plano de Saúde: ID{$_REQUEST['id_plano_saude']}",'plano_saude',$antigo,$novo);
        
        header("Location: ../../plano_saude");
    break;
    
    case 'Deletar' :
        $objPlanoSaude->setIdPlanoSaude($_REQUEST['id_plano_saude']);
        $antigo = $objLog->getLinha('plano_saude',$_REQUEST['id_plano_saude']);
        $objPlanoSaude->deletarPlanoSaude();
        $novo = $objLog->getLinha('plano_saude',$_REQUEST['id_plano_saude']);
        $objLog->log('2', "Exclusão de Plano de Saúde: ID{$_REQUEST['id_plano_saude']}",'plano_saude',$antigo,$novo);
        //header("Location: ../../plano_saude");
    break;
    
    default:
        echo 'action: ' . $action;
    break;
}