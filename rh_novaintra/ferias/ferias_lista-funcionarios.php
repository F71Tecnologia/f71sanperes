<?php
/* 
 * PHP-DOC - ferias_lista-funcionarios.php
 * 
 * ??/??/????
 * 
 * Versão: 3.0.3375 - 22/10/2015 - Jacques - Carga errada de variável $data_aquisi_final = converteData($value['data_aquisicao_ini']) o correto
 *                                           deveria ser  $data_aquisi_final = converteData($value['data_aquisicao_fim']);
 * Versão: 3.0.3375 - 14/12/2015 - Jacques - Liberando link de lançamento de férias para Clt que perdeu o período aquisitivo.
 * Versão: 3.0.5447 - 11/01/2016 - Jacques - Desativado o link de férias antigas para processamento de férias
 * 
 * @desconhecido 
 * 
 */

if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../../conn.php');
include('../../funcoes.php');
include('../../classes/global.php');
//include("../../classes/CalculoFeriasClass.php");
include("../../classes/FeriasClass.php");
include('../../wfunction.php');

$usuario = carregaUsuario();
$id_regiao = $usuario['id_regiao'];

$regiao = $_REQUEST['regiao'];
$projeto = $_REQUEST['projeto'];
$data_ini_fmt = $_REQUEST['data_ini_fmt'];

$feriasObj = new Ferias();




$pesquisa = mysql_real_escape_string(trim($_REQUEST['pesquisa']));

$pesquisa .= (!empty($data_ini_fmt)) ? ";{$data_ini_fmt}" : ""; ?>



        <br>
        <h4 class="valign-middle">
            <i class="fa fa-chevron-right"></i> <?php echo utf8_encode($array_clt[$key]['dados']['nome']); ?> / CNPJ: <?php echo $array_clt[$key]['dados']['cnpj']; ?>
            <span class="pull-right"><a class="btn btn-success gerar-ferias-lote"><i class="fa fa-file-pdf-o"></i>Impress&atilde;o de F&eacute;rias em Lote</a></span>&nbsp;&nbsp;
            <span class="pull-right"><a class="btn btn-success" href="#" onclick="tableToExcel('tbRelatorio<?= $key ?>', 'Relatório')"><i class="fa fa-file-excel-o"></i> Exportar para Excel</a></span>
        </h4>
        
        <?php
        $qr_unidades = mysql_query("SELECT * FROM unidade WHERE id_regiao = {$regiao}");
        while($row_unidade = mysql_fetch_assoc($qr_unidades)) {
        	$clts = $feriasObj->listaFuncionariosFerias($regiao, $projeto, $pesquisa, false, $row_unidade['id_unidade']);
        	foreach($clts as $clt) {
        		if(count($clt['clt'])) {
        ?>
        
        <div class="panel panel-default">
		    <div class="panel-heading">
		    	<?php echo utf8_encode($row_unidade['unidade']); ?>
		    </div>
		    <div class="panel-body">
		    	<table class="table table-striped table-hover table-condensed table-bordered text-sm valign-middle" id="tbRelatorio<?= $key ?>">
				    <thead>
				        <tr class="bg-primary valign-middle">
				            <th class="text-center" style="width:5%;">COD</th>
				            <th style="width:40%;">NOME</th>
				            <th class="text-center" style="width:10%;">CURSO</th>
				            <th class="text-center" style="width:10%;">DT. ENTRADA</th>
				            <th class="text-center" style="width:10%;">AQUI. DE F&Eacute;RIAS</th>
				            <th class="text-center" style="width:10%;">VENC. F&Eacute;RIAS</th>
				            <th style="width:5%;">&emsp;</th>
				        </tr>
				    </thead>
				    <tbody>
				    
				        <?php
				        
				        foreach($clt['clt'] as $value) {
				            $class = ($value['status'] == '40') ? "info" : "";
				            $class = (in_array($value['status'],[60,61,62,81,63,101,64,65,66,200])) ? "danger" : $class;
				            
				            $link = encrypt("$id_regiao&2&{$value['id_clt']}");
				            $link2 = str_replace("+", "--", $link);
				            
				            $linkFerias = 1;
				            $licencaFerias = null;
				            /*$sqlLicenca = mysql_query("SELECT B.especifica FROM rh_clt as A 
				                                        LEFT JOIN rhstatus as B ON(A.status = B.codigo)
				                                        WHERE A.id_clt = '{$value['id_clt']}'");
				            
				            while($rowLicenca = mysql_fetch_assoc($sqlLicenca)){$linkFerias = 0;
				               if($value['status'] != '200'){ 
				                    $licencaFerias .= '<span class="text-sm text-success pull-right">('.utf8_encode($rowLicenca['especifica']).')</span>';
				                    $class = "success"; 
				                }
				            }*/ ?>
				            
				            <tr class="<?=$class?>">
				                <td data-last-id-ferias="<?= $value['last_id_ferias'] ?>" class=""><?= $value['id_clt'] ?></td>
				                <td><?= utf8_encode($value['nome']) ?>
				                    <?php
				                    if ($value['status'] == '200') {$linkFerias = 0;
				                        $licencaFerias .= '<span class="text-sm text-danger pull-right">(Aguardando Demiss&atilde;o)</span>';
				                    }
				                    elseif ($value['status'] == '40') {
				                        $licencaFerias .= '<span class="text-sm text-info pull-right">(Em F&eacute;rias)</span>';
				                        $linkFerias = 1;
				                    }elseif($value['status'] != '10'){
				                        $licencaFerias .= "<span class='text-sm text-success pull-right'>".utf8_encode($value['especifica'])."</span>";
				                        $linkFerias = 0;
				                    }
				                    
				                    echo $licencaFerias; ?>
				                </td>
				                <td class="text-center"><?= utf8_encode($value['curso_nome']) ?></td>
				                <td class="text-center"><?= $value['data_entrada_br'] ?></td>
				                <td class="text-center"><?= $value['data_aquisicao_ini'] ?></td>
				                <td class="text-center"><?= $value['data_aquisicao_fim'] ?></td>
				                <td class="text-center">
				                    <a href="javascript:void(0);" data-id-clt="<?= $value['id_clt'] ?>" class="historico-ferias" data-id-clt="<?= $value['id_clt'] ?>">
				                        <i data-type="visualizar" class="tooo btn btn-xs btn-primary fa fa-search <?php if($value['qtd_ferias'] == 0){ ?>disabled<?php } ?>" data-toggle="tooltip" data-placement="top" title="Ver Hist&oacute;rico"></i>
				                    </a>
                                                    <?php if($_COOKIE['logado'] != 395){ ?>
                                                        <a href="javascript:void(0);" data-id-clt="<?= $value['id_clt'] ?>" class="lancar-ferias">
                                                            <i data-type="visualizar" class="tooo btn btn-xs btn-warning fa fa-plane" data-toggle="tooltip" data-placement="top" title="Lan&ccedil;ar F&eacute;rias"></i>
                                                        </a>
                                                    <?php } ?>
				                </td>
				            </tr>
				        <?php } ?>
				    </tbody>
				</table>
		    </div>
		</div>
		
        <?php
        		}
            }
        }
    
/*if ($cont_clt > 0) { ?>
    <!--table class="table table-striped table-hover table-condensed table-bordered">
        <tfoot>
            <tr>
                <td style="width:45%;" class="text-right"><strong>TOTAL:</strong></td>
                <td style="width:10%;">R$ <span class="pull-right"><?php echo number_format($totalizador_ferias, 2, ',', '.'); ?></span></td>
                <td style="width:45%;"></td>
            </tr>
        </tfoot> 
    </table-->  
<?php } else { ?>                                                                                                                                                
                                                                                                                                                                                                            <!--<META HTTP-EQUIV=Refresh CONTENT="2; URL=/intranet/principalrh.php?regiao=<?= $regiao ?>&id=1"/>-->
    <div class="bs-callout bs-callout-info">
        <h4 class="text-info"><i class="fa fa-info-circle"></i> Aten&ccedil;&atilde;o!</h4>
        <p class="text-info">A Regi&atilde;o n&atilde;o possui CLTs ativos.</p>
    </div>

<?php } */ ?>
