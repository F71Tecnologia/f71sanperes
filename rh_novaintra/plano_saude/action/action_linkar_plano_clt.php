<?php

include("../../../conn.php");
require("../../../wfunction.php");
require("../../../funcoes.php");
require("../../../classes/LogClass.php");
require("../../../classes/SetorClass.php");

$charset = mysql_set_charset('utf8');

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : NULL;

$usuario = carregaUsuario();
$objSetor = new SetorClass();
$objLog = new Log();

switch ($action) {
    case 'get_clt' :
        $auxCurso = (!empty($_REQUEST['curso'])) ? " AND id_curso = {$_REQUEST['curso']} " : null;
        $sql = "
            SELECT id_clt, nome FROM rh_clt WHERE id_projeto = {$_REQUEST['projeto']} AND medica = 1
                AND (status < 60 || status = 200) AND (id_plano_saude = 0 || id_plano_saude IS NULL) $auxCurso
            ORDER BY nome";
        $qry = mysql_query($sql);
        $num = mysql_num_rows($qry);
        
        if($num > 0) { ?>

            <table class='table table-condensed text-sm valign-middle'>
                <tr class="active">
                <td class="text-center"><input type="checkbox" class="checkAll" data-name="id_clt"></td>
                <td colspan="3" class="text-bold">Selecionar Todos</td>
            </tr>
            <tr>
            <?php 
            $count=0;
            while($row = mysql_fetch_assoc($qry)) { ?>
                <td class="text-center"><input type="checkbox" name="id_clt[]" value="<?= $row['id_clt'] ?>"></td>
                <td><?= $row['nome'] ?></td>
                <?php if((++$count%2) == 0){ echo '</tr><tr>'; }
            } ?>
            </tr>
            </table>

        <?php } else { ?>

            <div class="alert alert-warning">Nenhum participante encontrado!</div>

        <?php }
    
    break;
    
    case 'get_curso' :
        
        $sql = "SELECT id_curso, nome FROM curso WHERE status = 1 AND id_regiao = {$usuario['id_regiao']} AND campo3 = {$_REQUEST['projeto']} AND tipo = 2 ORDER BY nome";
        $qry = mysql_query($sql) or die(mysql_error());
        //$arrayCurso[''] = 'Todas as Funções';
        echo '<option value="">Todas as Fun&ccedil;&otilde;es</option>';
        while($row = mysql_fetch_assoc($qry)){
            //$arrayCurso[$row['id_curso']] = $row['nome'];
            $aux = ($row['id_curso'] == $_REQUEST['curso']) ? 'selected' : null;
            echo '<option value="'.$row['id_curso'].'" '.$aux.' >'.$row['nome'].'</option>';
        }
    
    break;
    
    case 'linkar' :
        
        $arrayClts = $_REQUEST['id_clt'];
        if(count($arrayClts) > 0){
            
            foreach ($arrayClts as $value) {
                $antigo = $objLog->getLinha('rh_clt',$value);
                $sql = "UPDATE rh_clt SET id_plano_saude = {$_REQUEST['id_plano_saude']} WHERE id_clt = $value";
                $qry = mysql_query($sql) or die(mysql_error());
                $novo = $objLog->getLinha('rh_clt',$value);
                
                $objLog->log('2', "Plano $id_plano_saude associado ao CLT $value");
                $objLog->log('2', "CLT ID $value associado ao plano ID $id_plano_saude");
            }
            
//            $sql = "UPDATE rh_clt SET id_plano_saude = {$_REQUEST['id_plano_saude']} WHERE id_clt IN(".implode(', ',$arrayClts).")";
//            $qry = mysql_query($sql) or die(mysql_error());
//            $objLog->gravaLog('Setor', "Setor $id_plano_saude Cadastrado");
            header("Location: ../../plano_saude");
        } else {
            header("Location: ../linkar_plano_clt.php?erro");
        }
    break;
    
    default:
        echo 'action: ' . $action;
    break;
}