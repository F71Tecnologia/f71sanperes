<?php
/*
 * PHP-DOC - ferias_lista-solicitacoes.php
 *  
 * @Sinesio Luiz
 * 
 */

//header("Content-Type: text/html; charset=ISO-8859-1",true);

if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../../conn.php');
include('../../funcoes.php');
include('../../classes/global.php');
include("../../classes/FeriasClass.php");
include('../../wfunction.php');

$usuario = carregaUsuario();
$id_regiao = $usuario['id_regiao'];

//$projeto   = $_REQUEST['projeto'];
//$mes       = str_pad($_REQUEST['mes'],2,0,STR_PAD_LEFT);
//$ano       = $_REQUEST['ano'];



$sql = "SELECT A.* , A.status as status_solicitacao, B.nome,C.nome AS user_ca,
                DATE_FORMAT(A.aquisitivo_ini, '%d/%m/%Y') AS aquisitivo_iniBR,
                DATE_FORMAT(A.aquisitivo_fim, '%d/%m/%Y') AS aquisitivo_fimBR,
                DATE_FORMAT(A.ferias_ini, '%d/%m/%Y') AS ferias_iniBR,
                DATE_FORMAT(A.ferias_fim, '%d/%m/%Y') AS ferias_fimBR,
                DATE_FORMAT(A.ferias_ini_alt, '%d/%m/%Y') AS ferias_ini_altBR,
                DATE_FORMAT(A.ferias_fim_alt, '%d/%m/%Y') AS ferias_fim_altBR,
                B.id_projeto, MONTH(A.ferias_ini) AS mes, YEAR(A.ferias_ini) AS ano, B.id_unidade, U.unidade AS nome_unidade
            FROM rh_ferias_solicitacao AS A
            LEFT JOIN rh_clt AS B ON (A.id_clt = B.id_clt)
            LEFT JOIN rh_clt AS C ON (A.user_cad = C.id_clt)
            LEFT JOIN unidade As U ON B.id_unidade = U.id_unidade
            WHERE A.status IN(3, 4)
            ORDER BY A.status,B.nome";
//            WHERE '{$ano}-{$mes}' BETWEEN DATE_FORMAT(A.ferias_ini, '%Y-%m') AND DATE_FORMAT(A.ferias_fim, '%Y-%m')
//            AND B.id_projeto = {$projeto} {$criteria}";


echo "<!-- $sql -->";

$reClts = mysql_query($sql) or die($sql . mysql_error());


while ($row = mysql_fetch_assoc($reClts)) {
    $unidades[$row['id_unidade']]['nome_unidade'] = $row['nome_unidade'];
    $unidades[$row['id_unidade']]['clts'][] = $row;
}

//$feriasObj = new Ferias();
//$reClts = $feriasObj->listaSolicitacoesFerias($projeto, $mes, $ano,NULL, 4);
?>



<?php if ($unidades > 0) { ?>
    <?php foreach ($unidades as $key => $row_unidade) { ?>

        <div class="text-right" style="margin-bottom: 15px">
            <span><a class="btn btn-success" href="#" onclick="tableToExcel('tbRelatorio<?= $key ?>', 'Relatório')"><i class="fa fa-file-excel-o"></i> Exportar para Excel</a></span>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <?php echo utf8_encode($row_unidade['nome_unidade']); ?>
            </div>
            <div class="panel-body">
                <table class="table table-striped table-hover table-condensed table-bordered text-sm valign-middle" id="tbRelatorio<?= $key ?>">
                    <thead>
                        <tr class="bg-primary valign-middle">
                            <th class="text-center" style="width:5%;">COD</th>
                            <th style="width:30%;">NOME</th>
                            <th class="text-center" style="width:10%;">AQUI. INI</th>
                            <th class="text-center" style="width:10%;">AQUI. FIM</th>
                            <th class="text-center" style="width:10%;">GOZO INI</th>
                            <th class="text-center" style="width:10%;">GOZO FIM</th>
                            <th class="text-center" style="width:10%;">IMPRESSAO</th>
                            <th class="text-center" style="width:10%;">UPLOAD</th>
                            <th style="width:5%;">&emsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($row_unidade['clts'] as $row_clt) { ?>
                            <?php //while ($row_clt = mysql_fetch_assoc($reClts)){ ?>
                            <?php
                            $classFormat = "";
                            $legendaStatus = "";
                            if ($row_clt['status_solicitacao'] == 4) {
                                $classFormat = "style='background: rgba(202, 66, 66, 0.48)'";
                                $legendaStatus = ' <br><span style="font-weight: bold; font-size: 10px; color: red; ">(SOLICITAÇÃO DE TROCA DE DATA DE INÍCIO DE FÉRIAS)</span>';
                            }
                            ?>
                            <tr data-key="<?php echo $row_clt['id_solicitacao']; ?>" data-status="<?php echo $row_clt['status_solicitacao']; ?>">
                                <td <?php echo $classFormat; ?> ><?php echo $row_clt['id_clt']; ?></td>
                                <td <?php echo $classFormat; ?> ><?php echo utf8_encode($row_clt['nome'] . " " . $legendaStatus); ?></td>
                                <td <?php echo $classFormat; ?>  class="text-center"><?php echo $row_clt['aquisitivo_iniBR']; ?></td>
                                <td <?php echo $classFormat; ?>  class="text-center"><?php echo $row_clt['aquisitivo_fimBR']; ?></td>
                                <td <?php echo $classFormat; ?>  class="text-center"><?php echo $row_clt['ferias_iniBR']; ?></td>
                                <td <?php echo $classFormat; ?>  class="text-center"><?php echo $row_clt['ferias_fimBR']; ?></td>
                                <td <?php echo $classFormat; ?>  class="text-center"><?php
                                    if ($row_clt['impressao'] == 1) {
                                        echo "<i style='padding: 3px 9px;' class='fa fa-check btn-success'>";
                                    }
                                    ?></td>
                                <td <?php echo $classFormat; ?>  class="text-center"><?php
                                    if ($row_clt['upload'] == 1) {
                                        echo "<a href='../../" . $row_clt['nome_arq_upload'] . "' target='blank'><i style='padding: 3px 9px; cursor: pointer' class='fa fa-file-pdf-o  btn-danger'></a>";
                                    }
                                    ?></i></td>
                                <td <?php echo $classFormat; ?>  class="text-center">
                                    <?php if($row_clt['status_solicitacao'] == 4) { ?>
                                    <a href="javascript:void(0);" data-id-clt="<?php echo $row_clt['id_clt']; ?>" data-projeto="<?php echo $row_clt['id_projeto']; ?>" data-mes="<?php echo $row_clt['mes']; ?>" data-ano="<?php echo $row_clt['ano']; ?>" class="ver_detalhes" data-id-clt="<?php echo $row_clt['id_clt']; ?>">
                                        <i data-type="visualizar" class="tooo btn btn-xs btn-primary fa fa-search" data-toggle="tooltip" data-placement="top" title="Ver Detalhes"></i>
                                    </a>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

    <?php } ?>
<?php } else { ?>
    <div class="alert alert-info">
        <p><i class="fa fa-info-circle"></i> <?= utf8_encode('Não há Solicitação de alteração de féras no momento.') ?></p>
    </div>
<?php } ?>