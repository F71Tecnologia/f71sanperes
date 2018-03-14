<?php
header("Content-type: text/html; charset=iso-8859-1");
include("../../../conn.php");
require("../../../wfunction.php");
require("../../../funcoes.php");
require("../../../classes/LogClass.php");
require("../../../classes/SetorClass.php");

$charset = mysql_set_charset('iso-8859-1');

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : NULL;

$usuario = carregaUsuario();
$objSetor = new SetorClass();
$objLog = new Log();
$projetos = getProjetos($usuario['id_regiao']);
$unidades = getUnidades($usuario['id_regiao']);

// FEITO NA CORRERIA, DEPOIS COLOCAR DE ACORDO COM ESSE FRAMEWORK
//$gerencia = montaQuery("setor_gerencia", "*", null, null, null, null, false);
//$arrayGerencia = array("" => "--Selecione a gerencia--");
//foreach ($gerencia as $gerenciaL) {
//    $arrayGerencia[$gerenciaL['id_gerencia']] = $gerenciaL['nome'];
//}

switch ($action) {
    case 'form_setor' :                   
        
        if(!empty($_REQUEST['id_setor'])) {
            $objSetor->setIdSetor($_REQUEST['id_setor']);
            $objSetor->getSetor();
            $objSetor->getRowSetor();
        } ?>
        
        <form action="action/action_setor.php" method="post" id="form1">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="form-group">
                        <label class="control-label">Nome:</label>
                        <div><input type="text" class="form-control validate[required]" name="nome" id="nome" placeholder="Nome do Setor" value="<?=$objSetor->getNome()?>"></div>
                        <input type="hidden" name="action" value="<?=$_REQUEST['tipo']?>">
                        <?=(!empty($_REQUEST['id_setor'])) ? '<input type="hidden" name="id_setor" value="'.$_REQUEST['id_setor'].'">' : null?>
                    </div>
<!--                    <div class="form-group">
                        <label class="control-label">Projeto:</label>
                        <div><?= montaSelect($projetos, $objSetor->getProjeto(),"class='form-control validate[required,custom[select]]' id='id_projeto' name='id_projeto'"); ?></div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Unidade:</label>
                        <div><?= montaSelect($unidades, $objSetor->getUnidade(),"class='form-control validate[required,custom[select]]' id='id_unidade' name='id_unidade'"); ?></div>
                    </div>-->
<!--                    <div class="form-group">
                        <label class="control-label">Gerencia:</label>
                        <div>
                            <select name="id_gerencia" id="id_gerencia" class="form-control validate[required]">
                                <option value="">--Selecione--</option>
                                <?php while($resG = mysql_fetch_assoc($gerencia)){ ?>
                                <option value="<?php echo $resG['id_gerencia']; ?>" <?php echo selected($resG['id_gerencia'], $objSetor->getGerencia()); ?>><?php echo $resG['nome']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <input type="hidden" name="action" value="<?=$_REQUEST['tipo']?>">
                        <?=(!empty($_REQUEST['id_setor'])) ? '<input type="hidden" name="id_gerencia" value="'.$_REQUEST['id_gerencia'].'">' : null?>
                    </div>-->
                </div>
            </div>
        </form>
    
    <?php break;
    
    case 'Cadastrar' :
        $objSetor->setNome($_REQUEST['nome']);
        $objSetor->setProjeto($_REQUEST['id_projeto']);
        $objSetor->setUnidade($_REQUEST['id_unidade']);
//        $objSetor->setGerencia($_REQUEST['id_gerencia']);
        $objSetor->setIdFuncionarioCad($usuario['id_funcionario']);
        $objSetor->setDataCad(date('Y-m-d H:i:s'));
        $id_setor = $objSetor->insertSetor();
        $objLog->log('2', "Cadastro de Setor: ID{$id_setor}",'setor');
        header("Location: ../../setor");
    break;
    
    case 'Editar' :
        $objSetor->setNome($_REQUEST['nome']);
        $objSetor->setProjeto($_REQUEST['id_projeto']);
        $objSetor->setUnidade($_REQUEST['id_unidade']);
//        $objSetor->setGerencia($_REQUEST['id_gerencia']);
        $objSetor->setIdFuncionarioCad($usuario['id_funcionario']);
        $objSetor->setDataCad(date('Y-m-d H:i:s'));
        $objSetor->setIdSetor($_REQUEST['id_setor']);
        $antigo = $objLog->getLinha('setor',$_REQUEST['id_setor']);
        $objSetor->updateSetor();
        $novo = $objLog->getLinha('setor',$_REQUEST['id_setor']);
        $objLog->log('2', "Edição do Setor: ID{$_REQUEST['id_setor']}",'setor',$antigo,$novo);
        header("Location: ../../setor");
    break;
    
    case 'Deletar' :
        $objSetor->setIdSetor($_REQUEST['id_setor']);
        $antigo = $objLog->getLinha('setor',$_REQUEST['id_setor']);
        $objSetor->deletarSetor();
         $novo = $objLog->getLinha('setor',$_REQUEST['id_setor']);
         $objLog->log('2', "Exclusão de Setor: ID{$_REQUEST['id_setor']}",'setor',$antigo,$novo);
        //header("Location: ../../setor");
    break;
    
    default:
        echo 'action: ' . $action;
    break;
}