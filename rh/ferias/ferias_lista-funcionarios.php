<?php
if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../../conn.php');
include('../../classes/global.php');
//include("../../classes/CalculoFeriasClass.php");
include("../../classes/FeriasClass.php");
include('../../wfunction.php');

$usuario = carregaUsuario();
$id_regiao = $usuario['id_regiao'];

$regiao = $_REQUEST['regiao'];
$projeto = $_REQUEST['projeto'];

$feriasObj = new Ferias();

$pesquisa = mysql_real_escape_string(trim($_REQUEST['pesquisa']));

$array_clt = $feriasObj->listaFuncionariosFerias($regiao, $projeto, $pesquisa);

$cont_clt = 0; // calcula quantidade de CLTs

foreach ($array_clt as $key => $clt) {
    if (count($clt['clt']) > 0) {
        $cont_clt++;
        ?>
        <br>
        <h3>
            <i class="fa fa-chevron-right"></i> <?php echo utf8_encode($array_clt[$key]['dados']['nome']); ?> / CNPJ: <?php echo $array_clt[$key]['dados']['cnpj']; ?>
            <span class="pull-right"><a class="btn btn-success" href="#" onclick="tableToExcel('tbRelatorio<?= $key ?>', 'Relatório')"><i class="fa fa-file-excel-o"></i> Exportar para Excel</a></span>
        </h3>
        <table class="table table-striped table-hover" id="tbRelatorio<?= $key ?>">
            <thead>
                <tr>
                    <th style="width:5%;">COD</th>
                    <th style="width:34%;">NOME</th>
                    <th style="width:10%;">VALOR</th>
                    <th style="width:15%;">DATA DE ENTRADA</th>
                    <th style="width:15%;">AQUISI&Ccedil;&Atilde;O DE F&Eacute;RIAS</th>
                    <th style="width:15%;">VENC. DE F&Eacute;RIAS</th>
                    <th style="width:3%;">&emsp;</th>
                    <th style="width:3%;">&emsp;</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($clt['clt'] as $key2 => $value) {
                    $class = ($value['status'] == '40') ? "class=\"info\"" : "";
                    $class = ($value['status'] == '200') ? "class=\"danger\"" : $class;
                    
                    $linkFerias = 1;
                    $licencaFerias = null;
                    $sqlLicenca = mysql_query("SELECT * FROM rh_eventos WHERE data <= NOW() AND (data_retorno >= NOW() OR data_retorno = '0000-00-00') AND cod_status NOT IN(10,40) AND status = 1 AND id_clt = {$value['id_clt']}");
                    while($rowLicenca = mysql_fetch_assoc($sqlLicenca)){$linkFerias = 0;
                        $licencaFerias .= '<span class="text-success pull-right">('.utf8_encode($rowLicenca['nome_status']).')</span>';
                        $class = "class=\"success\"";
                    }
                    ?>
                    <tr <?= $class ?>>
                        <td><?= $value['id_clt'] ?></td>
                        <td><?= utf8_encode($value['nome']) ?>
                            <?php
                            if ($value['status'] == '200') {$linkFerias = 0;
                                $licencaFerias .= '<span class="text-danger pull-right">(Aguardando Demiss&atilde;o)</span>';
                            }
                            if ($value['status'] == '40') {
                                $licencaFerias .= '<span class="text-info pull-right">(Em F&eacute;rias)</span>';
                                $linkFerias = 1;
                            } 
                            echo $licencaFerias;
                            ?>
                        </td>
                        <td>R$ <span class="pull-right">
                                <?php
                                $total_ferias = $value['total_liquido'];
                                $totalizador_ferias += $total_ferias;
                                echo number_format($total_ferias, 2, ',', '.');
                                ?>
                            </span>
                        </td>
                        <td><?= $value['data_entrada_br'] ?></td>
                        <td><?= $value['data_aquisicao_ini'] ?></td>
                        <td><?= $value['data_aquisicao_fim'] ?></td>
                        <td class="text-center">
                            <a href="#" data-id-clt="<?= $value['id_clt'] ?>" class="historico-ferias" data-id-clt="<?= $value['id_clt'] ?>">
                                <img src="../../imagens/icones/icon-docview.gif" data-type="visualizar" class="tooo"
                                     data-toggle="tooltip" data-placement="top" title="Ver Hist&oacute;rico">
                            </a>
                        </td>
                        <td class="text-center">
                            <?php //if($linkFerias){ ?>
                            <a href="#" data-id-clt="<?= $value['id_clt'] ?>" class="lancar-ferias">
                                <img src="../../imagens/icones/icon-edit.gif" data-type="visualizar" class="tooo"
                                     data-toggle="tooltip" data-placement="top" title="Lan&ccedil;ar F&eacute;rias"><i class="fa fa-trash"></i>
                            </a>
                            <?php //} ?>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
        <?php
    }
}
if ($cont_clt > 0) {
    ?>
    <table class="table table-hover table-striped">
        <tfoot>
            <tr>
                <td style="width:5%;">&nbsp;</td>
                <td style="width:33%;" class="text-right"><strong>TOTAL:</strong></td>
                <td style="width:10%;">R$ <span class="pull-right"><?php echo number_format($totalizador_ferias, 2, ',', '.'); ?></span></td>
                <td style="width:15%;">&nbsp;</td> 
                <td style="width:15%;">&nbsp;</td>
                <td style="width:15%;">&nbsp;</td>
                <td style="width:7%;">&nbsp;</td>
            </tr>
        </tfoot> 
    </table>  
<?php } else { ?>                                                                                                                                                
                                                                                                                                                                                                            <!--<META HTTP-EQUIV=Refresh CONTENT="2; URL=/intranet/principalrh.php?regiao=<?= $regiao ?>&id=1"/>-->
    <div class="bs-callout bs-callout-info">
        <h4 class="text-info"><i class="fa fa-info-circle"></i> Aten&ccedil;&atilde;o!</h4>
        <p class="text-info">A Regi&atilde;o n&atilde;o possui CLTs ativos.</p>
    </div>

<?php } ?>
