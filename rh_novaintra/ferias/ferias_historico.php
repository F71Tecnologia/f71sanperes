<?php
/**
 * Procedimento para listagem de férias gozadas por um determinado clt
 * 
 * @file      ferias_historico.php
 * @license   F71
 * @link      http://www.f71lagos.com/intranet/rh_novaintra/ferias/ferias_historico.php
 * @copyright 2016 F71
 * @author    
 * @package   
 * @access    public  
 * 
 * @version: 3.0.0000L - ??/??/???? - Não definido   - Versão Inicial 
 * @version: 3.0.5652L - 19/01/2016 - Jacques - Adicionado botão para impressão de aviso de férias
 * @version: 3.0.5652L - 20/06/2016 - Jacques - Adicionado botão para reprocessamento de férias
 * @version: 3.0.0000I - 01/12/2016 - Jacques - Adicionado dados ao elemento button classe reprocessar-ferias dados de período aquisitivo e nome do clt
 * @version: 3.0.0288I - 06/12/2016 - Jacques - Alterado a opção de visualizar no histórico de férias o PDF do recibo que era cherado em tempo real para o PDF armazenado no servidor
 * 
 */   

if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true"); 
    exit;
}

//ARRAY DE FUNCIONARIO DA F71
//$func_f71 = array('255', '258', '256', '259', '260', '158', '257', '179');

if(!include_once('../../conn.php')) die ('Não foi possível incluir ../../conn.php'); 
if(!include_once('../../classes/global.php')) die ('Não foi possível incluir ../../classes/global.php'); 
if(!include_once('../../classes/clt.php')) die ('Não foi possível incluir ../../classes/clt.php'); 
if(!include_once('../../classes/FeriasClass.php')) die ('Não foi possível incluir ../../classes/FeriasClass.php'); 
if(!include_once('../../wfunction.php')) die ('Não foi possível incluir ../../wfunction.php'); 
if(!include_once('../../classes_permissoes/acoes.class.php')) die ('Não foi possível incluir ../../classes_permissoes/acoes.class.php'); 

$objAcoes = new Acoes();

$usuario = carregaUsuario();
$id_regiao = $usuario['id_regiao'];
$id_clt = $_REQUEST['id_clt'];

$feriasObj = new Ferias();

$feriasObj->calcFerias->setIdClt($id_clt);

$listaFeria = $feriasObj->calcFerias->getFeriasPorClt();

//print_array($listaFeria);
?>
<table class="table table-hover table-striped">
    <thead>
        <tr>
            <th>Per&iacute;odo Aquisitivo</th>
            <th>Per&iacute;odo De F&eacute;rias</th>
            <th class="text-center">PDF</th>
            <!--th class="text-center">Excluir</th-->
        </tr>
    </thead>
    <tbody>
        <?php foreach ($listaFeria['registros'] as $row_ferias) { ?>
            <tr class="periodo_<?=$row_ferias['id_ferias']?>">
                <td><?= "{$row_ferias['data_aquisitivo_iniBR']} &agrave; {$row_ferias['data_aquisitivo_fimBR']}" ?></td>
                <td><?= "{$row_ferias['data_iniBR']} &agrave; {$row_ferias['data_fimBR']}" ?></td>
                <td class="text-center">
                    <a href="/intranet/?class=ferias/processar&method=telaAvisoFerias&id_ferias=<?=$row_ferias['id_ferias']?>" class="btn btn-default btn-xs" title="Ver Aviso de F&eacute;rias" target="_blank">
                        <img src="../../imagens/icons/att-generic.png" style="width: 1.5em; height: 1.5em;" alt="Ver Aviso de F&eacute;rias"
                        <i class="text-danger fa fa-file-pdf-o" alt="Ver PDF de F&eacute;rias Novas"></i>
                    </a>
                    <a href="/intranet/?class=ferias/processar&method=obterPdf&id_ferias=<?=$row_ferias['id_ferias']?>&value=pdf" class="btn btn-default btn-xs" title="Ver PDF do Recibo de F&eacute;rias" target="_blank">
                        <img src="../../imagens/icons/att-pdf.png" style="width: 1.5em; height: 1.5em;" alt="Ver PDF"
                        <i class="text-danger fa fa-file-pdf-o" alt="Ver PDF de Férias Novas"></i>
                    </a>
                </td>
                <?php if ($objAcoes->verifica_permissoes(87)) { ?>
                    <td class="text-center">
                        <button type="button" class="btn btn-danger btn-xs reprocessar-ferias" data-id-ferias="<?=$row_ferias['id_ferias']?>" data-nome="<?=$row_ferias['nome']?>" data-aquisitivo-ini="<?=$row_ferias['data_aquisitivo_iniBR']?>"  data-aquisitivo-fim="<?=$row_ferias['data_aquisitivo_fimBR']?>" data-toggle="tooltip" title="Reprocessar Férias"><i class="fa fa-refresh"></i></button>
                        <!--a class="btn btn-danger btn-xs reprocessar-ferias"  data-toggle="tooltip" title="Reprocessar Férias" target="_blank" data-id-ferias="<?=$row_ferias['id_ferias']?>"><i class="fa fa-refresh"></i></a-->
                    </td>
                    <td class="text-center">
                        <!--<button type="button" class="btn btn-danger btn-xs desprocessar_ferias" data-ferias="<?=$row_ferias['id_ferias']?>" data-ferias="<?=$row_ferias['id_clt']?>" data-toggle="tooltip" title="Desprocessar Férias"><i class="fa fa-trash-o"></i></button>-->
                        <a href="../../rh/ferias/rh_ferias_desprocessar.php?clt=<?php echo $row_ferias['id_clt']; ?>&ferias=<?php echo $row_ferias['id_ferias']; ?>&tela=1" class="btn btn-danger btn-xs" <?php if($row_ferias['ano'] <= 2016){ echo "disabled"; }else{ echo "enabled";}?> data-toggle="tooltip" title="Desprocessar Férias"><i class="fa fa-trash-o"></i></a>
                        <!--<a href="rh_ferias_desprocessar.php?clt=<?php echo $id_clt; ?>&ferias=<?php echo $id_ferias; ?>&tela=1" title="Desprocessar Férias"><img src="../imagensrh/deletar.gif" /></a>-->
                    </td>
					
                <?php } ?>
                
            </tr>
        <?php } ?>
    </tbody>
</table>

