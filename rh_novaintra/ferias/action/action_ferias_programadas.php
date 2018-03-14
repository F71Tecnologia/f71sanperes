<?php
/**
 * Rotina para listar clts
 * 
 * @file      action_ferias_programadas.php
 * @license   
 * @link      
 * @copyright 2016 F71
 * @author    Não definido
 * @package   
 * @access    public  
 * @version: 3.00.L0000 - ??/??/???? - Não Definido - Versão Inicial 
 * @version: 3.00.L0000 - ??/??/???? - Não Definido - Bug com fechamento de } excedente no código
 * @version: 3.00.L0000 - 31/03/2017 - Removendo o disabled do checkbox para agendamento de férias
 * @version: 3.00.L0000 - 31/03/2017 - Removendo o AddClassExt para construção de classes no framework pois a versão atual não exige mais a carga das classes usadas
 * 
 * 
 * @todo 
 * @example:  
 * 
 * @author 
 * 
 * @copyright www.f71.com.br
 */


include("../../../conn.php");
require("../../../wfunction.php");
require("../../../funcoes.php");
require("../../../classes/LogClass.php");
require("../../../classes/FeriasProgramadasClass.php");
include('../../../classes_permissoes/acoes.class.php');
if(!include_once(ROOT_LIB.'fw.class.php')) die ('Não foi possível incluir '.ROOT_LIB.'fw.class.php, a partir de '.__FILE__);

$rh = new \hub\FwClass();

$log = new Log();

$charset = mysql_set_charset('utf8');

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : NULL;

$usuario = carregaUsuario();
$objAcoes = new Acoes();
$objFeriasProg = new FeriasProgramadasClass();
$objLog = new Log();
    

if($_COOKIE['debug']==666){
    print_array($_REQUEST);
}

switch ($action) {
    case 'ver_agendados' :
        
        $auxProjeto = ($_REQUEST['projeto'] > 0) ? " AND B.id_projeto = '{$_REQUEST['projeto']}' " : null;
        $arrayAgendamentosDia = $objFeriasProg->getVerAgendamentosDia($_REQUEST['data'], $usuario['id_regiao'], $auxProjeto); 
        ?>
    
        <table class='table table-condensed text-sm valign-middle'>
        <tr>
            <th>NOME</th>
            <th class='text-center'>INICIO</th>
            <th class='text-center'>FINAL</th>
            <th class='text-center'></th>
        </tr>
        <?php foreach ($arrayAgendamentosDia as $value) { ?>
            <tr>
                <td><?= $value['nome'] ?></td>
                <td class="text-center text-success"><?= $value['inicioBR'] ?></td>
                <td class="text-center text-danger"><?= $value['fimBR'] ?></td>
                <td class="text-center"><?php if($objAcoes->verifica_permissoes(101)){ ?><buttom class="btn btn-xs btn-danger deletarAgendamento" data-key="<?= $value['id_ferias_programadas'] ?>"><i class="fa fa-trash-o"></i></buttom><?php } ?></td>
            </tr>
        <?php } ?>
        </table>
    <?php
    break;
    
    case 'get_clt' :
//        $auxCurso = (!empty($_REQUEST['unidade'])) ? " AND id_unidade = {$_REQUEST['unidade']} " : null;
       $sql = "
            SELECT 
                id_clt, 
                nome,
                status 
            FROM rh_clt
            WHERE 
                id_projeto = {$_REQUEST['projeto']}
                AND (status < 60 || status = 200)
                $auxCurso
            ORDER BY nome";
        $qry = mysql_query($sql);
        $num = mysql_num_rows($qry);
        
        
        if($num > 0) { ?>

            <table class='table table-condensed text-sm valign-middle'>
            <tr>
            <?php 
            $count=0;
            while($row = mysql_fetch_assoc($qry)) { 
                

                $rh->Clt->setDefault()->setIdClt($row['id_clt'])->select()->getRow();
                $periodos_pendentes = $rh->Ferias->setDefault()->getPeriodoAquisitivoPendente();
                
                if(!empty($periodos_pendentes)){
                    
                    $ini = $periodos_pendentes[0]['data_aquisitivo_ini']->get('d/m/Y')->val();
                    $fim = $periodos_pendentes[0]['data_aquisitivo_fim']->get('d/m/Y')->val();
            ?>
                <td class="text-center">
                    <?php if($row['status'] == 20 or $row['status'] == 40 or $row['status'] == 50){ ?>
                        <input type="checkbox" name="id_clt[]" value="<?=$row['id_clt']?>" data-clt="<?=$ini?>"></td>
                    <?php }else{ ?>
                        <input type="checkbox" name="id_clt[]" value="<?= $row['id_clt'] ?>" data-clt="<?=$ini?>"></td>
                     <?php }?>
                <td><?php echo $row['nome']; 
                    
                    if($row['status'] != 10){
                        if($row['status'] == 20){
                            $text_label = "Lic. Medica";    
                            $type_label = "danger";
                        }elseif($row['status'] == 40){
                            $text_label = "Ferias";
                            $type_label = "info";
                        }elseif($row['status'] == 50){
                            $text_label = "Lic. Maternidade";
                            $type_label = "success";
                        }
                        
                        echo "&nbsp;&nbsp;<span class='label label-{$type_label}'>{$text_label}</span>";
                    }
                    
                    ?></td>
                <td><?=$periodos_pendentes[0]['data_aquisitivo_ini']->get('d/m/Y')->val()?> &agrave; <?=$periodos_pendentes[0]['data_aquisitivo_fim']->get('d/m/Y')->val()?></td>
                <?php if((++$count%2) == 0){ echo '</tr><tr>'; }
                }
            } ?>
            </tr>
            </table>

        <?php } else { ?>

            <div class="alert alert-warning">
                Nenhum participante ativo encontrado neste projeto!
            </div>

        <?php }
    
    break;
    
    case 'get_curso' :
        
        $sql = "SELECT id_unidade, unidade FROM unidade WHERE id_regiao = {$usuario['id_regiao']} ORDER BY unidade";
        $qry = mysql_query($sql) or die(mysql_error());
        //$arrayCurso[''] = 'Todas as Funções';
        echo '<option value="">Todas as Unidades</option>';
        while($row = mysql_fetch_assoc($qry)){
            //$arrayCurso[$row['id_curso']] = $row['nome'];
            $aux = ($row['id_unidade'] == $_REQUEST['unidade']) ? 'selected' : null;
            echo '<option value="'.$row['id_unidade'].'" '.$aux.' >'.$row['unidade'].'</option>';
        }
    
    break;
    
    case 'cadastrar' :
        
        $arrayClts = $_REQUEST['id_clt'];
        
        
        $dias_ferias = $_REQUEST['dias_ferias'];
        if(empty($dias_ferias)){
            $dias = 30;
        }else{
            $dias = $dias_ferias;
        }
        $objFeriasProg->setInicio(implode('-', array_reverse(explode('/', $_REQUEST['inicio']))));
                
        $objFeriasProg->SetIdFuncionario($usuario['id_funcionario']);
        
        foreach ($arrayClts as $key => $value) {
            
            
            
            try {
                
                $rh->setDefault();
                

                if(!$rh->Clt->setIdClt($value)->select()->getRow()->isOk()) $rh->Clt->error->set('Nenhum registro carregado para Clt! Verifique região e projeto',E_FRAMEWORK_ERROR);
                if(!$rh->Projeto->select()->getRow()->isOk()) $rh->Projeto->error->set('Nenhum registro carregado para Projeto deste Clt! Verifique região e projeto',E_FRAMEWORK_ERROR);
                if(!$rh->Empresa->select()->getRow()->isOk()) $rh->Empresa->error->set('Nenhum registro carregado para Empresa deste Clt! Verifique região e projeto',E_FRAMEWORK_ERROR);
                
                $rh->Bancos->select()->getRow()->isOk();               
                
                $periodos_pendentes = $rh->Ferias->getPeriodoAquisitivoPendente();
                
                $rh->Ferias->setDefault(); 
                $rh->Ferias->setDataIni($objFeriasProg->getInicio()); 
                $rh->Ferias->setQntDiasFerias($dias);
                $rh->Ferias->setDataAquisitivoIni($periodos_pendentes[0]['data_aquisitivo_ini']);
                $rh->Ferias->setDataAquisitivoFim($periodos_pendentes[0]['data_aquisitivo_fim']);
                
//                $rh->Ferias->setVendido($chk_abono_pecuniario);
//                $rh->Ferias->setIgnorarFaltas($chk_ignorar_faltas);
//                $rh->Ferias->setIgnorarFeriasDobradas($chk_ignorar_ferias_dobradas);
//                $rh->Ferias->setMetadeFerias($chk_metade_ferias);                

//                $dias = $rh->Ferias->getCalcLimiteDiasFeriasPorFalta();
                
                $rh->Ferias->setCalcFerias();
                
                //echo $rh->Ferias->error->getAllMsgCode();
                
                $data_fim = $rh->Ferias->getDataFim();
                
                $objFeriasProg->setDiasFerias($dias);
                $objFeriasProg->setFim($data_fim);
                $objFeriasProg->setIdClt($value);
                $objFeriasProg->setPeriodoInicio($periodos_pendentes[0]['data_aquisitivo_ini']);
                $objFeriasProg->setPeriodoFim($periodos_pendentes[0]['data_aquisitivo_fim']);
                $objFeriasProg->setAbonoPecuniario($_REQUEST['chk_abono_pecuniario']);
                $objFeriasProg->setIgnorarFaltas($_REQUEST['chk_ignorar_faltas']);
                $objFeriasProg->setIgnorarFeriasDobradas($_REQUEST['chk_ignorar_ferias_dobradas']);
                
                //print_array($objFeriasProg); 
                
                //exit();

                //echo $rh->getAllMsgCode();
                
                //echo 'antes';

                $objFeriasProg->insertFeriasProgramadas();
                $log->gravaLog('Férias', "Cadastro de Férias para o(s) Funcionário(s) ID".implode(', ',$arrayClts));

                //echo 'depois';
                
            } catch (Exception $ex) {
                
                echo $rh->Ferias->getAllMsgCode();
                
            }

            
        } 
        
        header("Location: ../../ferias");
        
    break;
    
    case 'deletar' :
        $objFeriasProg->setIdFeriasProgramadas($_REQUEST['id']);
        $objFeriasProg->deletarFeriasProgramadas();
        
        $log->gravaLog('Ferias', 'Exclusão de Ferias Programadas para o Funcionário ID'.$_REQUEST['id']);
        
    break;
//    
    default:
        echo 'action: ' . $action;
    break;
}
