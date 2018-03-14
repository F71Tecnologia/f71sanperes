<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../login.php?entre=true';</script>";
    exit;
}

include "../../conn.php";
include "../../classes/funcionario.php";
include '../../classes_permissoes/regioes.class.php';
include "../../wfunction.php";
include "../../classes_permissoes/acoes.class.php";

$usuario = carregaUsuario();
$optRegiao = getRegioes();
$ACOES = new Acoes();

$meses = mesesArray(null,'');
$anos = anosArray(null, null, array("" => "« Selecione o ano »"));

$mesR = (isset($_REQUEST['mes'])) ? $_REQUEST['mes'] : "";

$id_regiao = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $id_regiao;
$id_projeto = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : $id_projeto;
$mesRI = (isset($_REQUEST['mesIni'])) ? $_REQUEST['mesIni'] : '';
$anoRI = (isset($_REQUEST['anoIni'])) ? $_REQUEST['anoIni'] : '';
$mesRF = (isset($_REQUEST['mesFim'])) ? $_REQUEST['mesFim'] : '';
$anoRF = (isset($_REQUEST['anoFim'])) ? $_REQUEST['anoFim'] : '';
$funcaoR = $_REQUEST['funcao'];

if(isset($_REQUEST['projeto'])){
    $sqlFuncao = mysql_query("SELECT id_curso, nome FROM curso WHERE campo3 = {$_REQUEST['projeto']} AND tipo = '2' AND status = '1' AND status_reg = '1'  ORDER BY nome");
    $optFuncao = "<option value=''>Selecione</option>";
    while($rowFuncao = mysql_fetch_assoc($sqlFuncao)){
        if($_POST['ok'] == 1){$nomeFuncao = utf8_encode($rowFuncao['nome']);}else{$nomeFuncao = $rowFuncao['nome'];}
        if($funcaoR == $rowFuncao['id_curso']){$optFuncao .= "<option value='{$rowFuncao['id_curso']}' SELECTED >{$nomeFuncao}</option>";}
        else{$optFuncao .= "<option value='{$rowFuncao['id_curso']}'>{$nomeFuncao}</option>";}
    }
    if($_POST['ok'] == 1){die($optFuncao);}
}else{$optFuncao = "<option value=''>Selecione</option>";}

?>
<html>
    <head>
        <title>:: Intranet ::</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../favicon.ico" />
        <link href="../../net1.css" rel="stylesheet" type="text/css" />

        <link href="../../favicon.ico" rel="shortcut icon" />
        <script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../../js/global.js" type="text/javascript"></script>
        <style>
            table, thead, tbody, tr, th, td{
                border-collapse: collapse; border: 1px solid #000; padding: 5px;
            }
        </style>
        <script>
            $(function() {
                var id_destination = "projeto";
                $('#regiao').ajaxGetJson("../../methods.php", {method: "carregaProjetos"}, function(data) {
                    removeLoading();
                    $("#" + id_destination).html(data);
                    var selected = $("input[name=hide_" + id_destination + "]").val();
                    if (selected !== undefined) {
                        $("#" + id_destination).val(selected);
                    }
                    $('#projeto').trigger('change');
                }, "projeto");
                
                $('#projeto').change(function(){
                    $.post("relatorio_ferias.php", {bugger:Math.random(), projeto:$("#projeto").val(), ok:1}, function(resultado){
                        //console.log($("#regiao").val());
                        $("#funcao").html(resultado);
                    });
                });
            });
        </script>
    </head>
    <body class="novaintra" >     
        <div id="content">
            <form  name="form" action="" id="form1" method="post" id="form">
                <div id="head">
                    <img src="../../imagens/logomaster<?php echo $usuario['id_master']; ?>.gif" class="fleft" style="margin-right: 25px;"/>
                    <div class="fleft">
                        <h2>Relatorio de Férias</h2>
                    </div>
                </div>
                <br class="clear">
                <br/>
                <fieldset class="noprint">
                    <div>
                        <p><label class="first">Região:</label> <?php echo montaSelect($optRegiao, $id_regiao, array('name' => "regiao", 'id' => 'regiao')); ?> </p>
                        <p><label class="first">Projeto:</label> <select id='projeto' name="projeto"></select> </p><input type="hidden" name="hide_projeto" value="<?php echo $id_projeto; ?>">
                        <p>
                            <label class="first">Inicio: </label> 
                            <?php echo montaSelect($meses, $mesRI, "id='mesIni' name='mesIni' class='validate[custom[select]]'") ?>
                            <?php echo montaSelect($anos, $anoRI, "id='anoIni' name='anoIni' class='validate[custom[select]]'") ?>
                        </p>
                        <p>
                            <label class="first">Fim: </label> 
                            <?php echo montaSelect($meses, $mesRF, "id='mesFim' name='mesFim' class='validate[custom[select]]'") ?>
                            <?php echo montaSelect($anos, $anoRF, "id='anoFim' name='anoFim' class='validate[custom[select]]'") ?>
                        </p>
                        <p>
                            <label class="first">Função: </label> 
                            <select name="funcao" id="funcao"><?php echo $optFuncao; ?></select>
                        </p>
                    </div>
                    <br class="clear"/>
                    <p class="controls" style="margin-top: 10px;">
                        <input type="submit" name="gerar" value="Gerar" id="gerar"/><?php if(isset($_REQUEST['regiao'])){ ?><input style="margin-left: 20px;" type="button" onclick="tableToExcel('tbRelatorio', 'Relatório')" value="Exportar para Excel" class="exportarExcel"><?php } ?>
                    </p>
                </fieldset>
            </form>
            <?php if(isset($_REQUEST['regiao'])){ ?>
            <table id="tbRelatorio" style="width: 100%; margin: 5% 0% 0% 0%;">
            <tr class="novo_tr">
                <th align="center">Projeto</th>
                <th align="center">Nome</th>
                <th align="center">Função</th>
                <th align="center">Salário</th>
                <th align="center">Dt. Adimissão</th>
                <th align="center">Períodos Gozados</th>
                <th align="center">Períodos Vencidos</th>
                <th align="center">Períodos Não Gozados</th>
            </tr>
            <?php
                if(!empty($_REQUEST['mes'])){
                    $auxMes = " AND MONTH(A.data_entrada) = {$_REQUEST['mes']} ";
                }
                if(!empty($_REQUEST['funcao'])){
                    $auxFuncao = " AND A.id_curso = {$_REQUEST['funcao']} ";
                }
                if(!empty($_REQUEST['projeto']) AND $_REQUEST['projeto'] > 0){
                    $auxProjeto = " AND A.id_projeto = {$_REQUEST['projeto']} ";
                }
                $sqlClts = "
                SELECT A.nome, id_clt, data_entrada, id_projeto, date_format(A.data_entrada, '%d/%m/%Y') data_entrada2, 
                    ADDDATE(ADDDATE(A.data_entrada, INTERVAL + 1 YEAR), INTERVAL -1 DAY) pAno,
                    FLOOR(DATEDIFF(NOW(),A.data_entrada) / 365) tempoEmpresa,
                    B.nome funcao, B.valor salario
                FROM rh_clt A LEFT JOIN curso B ON (A.id_curso = B.id_curso)
                WHERE A.id_regiao = '{$_REQUEST['regiao']}' AND (A.status < '60' OR A.status = '200') $auxMes $auxFuncao $auxProjeto
                ORDER BY A.nome ASC";
                
                $rsClts = mysql_query($sqlClts);
                $numero_clts = mysql_num_rows($rsClts);
                $count = 0;
                if($numero_clts > 0){
                    while ($row_clt10 = mysql_fetch_array($rsClts)) {
                        $arrayPeriodos = $periodoGozado = $periodoVencido = $periodoNaoGozado = '';
                        $qr_ferias = mysql_query("
                        SELECT 
                            MIN(data_aquisitivo_ini) periodoGozadoIni, 
                            MAX(data_aquisitivo_fim) periodoGozadoFim
                        FROM rh_ferias
                        WHERE id_clt = {$row_clt10[id_clt]} AND status = '1' ORDER BY data_fim DESC");
                        $numFerias = mysql_num_rows($qr_ferias);
                        $ferias = mysql_fetch_assoc($qr_ferias);

                        for($i=0; $i<=$row_clt10['tempoEmpresa'];$i++){
                            $dataEntrada = explode('-',$row_clt10['data_entrada']);
                            $dataEntrada = $dataEntrada[0]+$i.'-'.$dataEntrada[1].'-'.$dataEntrada[2];

                            $pAno = explode('-',$row_clt10['pAno']);
                            $pAno = $pAno[0]+$i.'-'.$pAno[1].'-'.$pAno[2];
                            
                            if($dataEntrada >= $ferias['periodoGozadoIni'] AND $pAno <= $ferias['periodoGozadoFim'] AND !empty($ferias['periodoGozadoIni'])){
                                $periodoGozadoIni = explode('-', $dataEntrada);
                                $periodoGozadoIni = $periodoGozadoIni[2].'/'.$periodoGozadoIni[1].'/'.$periodoGozadoIni[0];

                                $periodoGozadoFim = explode('-', $pAno);
                                $periodoGozadoFim = $periodoGozadoFim[2].'/'.$periodoGozadoFim[1].'/'.$periodoGozadoFim[0];

                                $periodoGozado .= "$periodoGozadoIni a $periodoGozadoFim, ";
                            } else if($pAno < (date('Y')+1).'-01-01'){
                            //} else {
                                if($dataEntrada <= date("Y-m-d") AND $pAno >= date("Y-m-d") AND $row_clt10['tempoEmpresa'] > 0){
                                    $periodoNaoGozadoIni = explode('-', $dataEntrada);
                                    $periodoNaoGozadoIni = $periodoNaoGozadoIni[2].'/'.$periodoNaoGozadoIni[1].'/'.$periodoNaoGozadoIni[0];

                                    $periodoNaoGozadoFim = explode('-', $pAno);
                                    $periodoNaoGozadoFim = $periodoNaoGozadoFim[2].'/'.$periodoNaoGozadoFim[1].'/'.$periodoNaoGozadoFim[0];

                                    $periodoNaoGozado .= "$periodoNaoGozadoIni a $periodoNaoGozadoFim, ";
                                }
                                if($pAno < date("Y-m-d")){
                                    $arrayPeriodos[] = $pAno;
                                    $periodoVencidoIni = explode('-', $dataEntrada);
                                    $periodoVencidoIni = $periodoVencidoIni[2].'/'.$periodoVencidoIni[1].'/'.$periodoVencidoIni[0];

                                    $periodoVencidoFim = explode('-', $pAno);
                                    $periodoVencidoFim = $periodoVencidoFim[2].'/'.$periodoVencidoFim[1].'/'.$periodoVencidoFim[0];

                                    $periodoVencido .= "$periodoVencidoIni a $periodoVencidoFim, ";
                                }
                            }
                        }

                        if(empty($periodoGozado)){$periodoGozado = '---';} else {$periodoGozado = substr($periodoGozado, 0, -2);}
                        if(empty($periodoVencido)){$periodoVencido = '---';} else {$periodoVencido = substr($periodoVencido, 0, -2);}
                        if(empty($periodoNaoGozado)){$periodoNaoGozado = '---';} else {$periodoNaoGozado = substr($periodoNaoGozado, 0, -2);}

                        $result_pro = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$row_clt10[id_projeto]' AND status_reg = '1' ");
                        $row_pro = mysql_fetch_array($result_pro);

                        if(empty($_REQUEST['mesIni'])){$count++;
                        ?>
                            <tr style="background-color:<?php if($alternateColor++ % 2 != 0){echo "#F0F0F0";}else{echo "#FDFDFD";}?>">
                                <td><?= $row_pro['nome'] ?></td>
                                <td><?= $row_clt10['nome'];
                                    /*if ($row_clt10['status'] == '40') {
                                        echo '<span style="color:#069; font-weight:bold;">(Em Férias)</span>';
                                    } elseif ($row_clt10['status'] == '200') {
                                        echo '<span style="color:red; font-weight:bold;">(Aguardando Demissão)</span>';
                                    }*/?>
                                </td>
                                <td align="center"><?= $row_clt10['funcao'] ?></td>
                                <td align="center"><?= number_format($row_clt10['salario'],2,',','.') ?></td>
                                <td align="center"><?= $row_clt10['data_entrada2'] ?></td>
                                <td align="center"><?= $periodoGozado ?></td>
                                <td align="center"><?= $periodoVencido ?></td>
                                <td align="center"><?= $periodoNaoGozado ?></td>
                            </tr>
                    <?php 
                        } else { 
                            $exibe = 0;
                            if(is_array($arrayPeriodos)){
                                foreach ($arrayPeriodos as $final) {
                                    if($_REQUEST['anoIni'].sprintf("%02d",$_REQUEST['mesIni'])."01" <= str_replace('-', '', $final) AND $_REQUEST['anoFim'].sprintf("%02d",$_REQUEST['mesFim'])."31" >= str_replace('-', '', $final)){
                                        $exibe = 1;break;
                                    }
                                }
                            }
                            if($exibe == 1){ $count++;?>
                                <tr style="background-color:<?php if ($alternateColor++ % 2 != 0) { echo "#F0F0F0"; } else { echo "#FDFDFD"; } ?>">
                                    <td><?= $row_pro['nome'] ?></td>
                                    <td><?= $row_clt10['nome'] ?></td>
                                    <td align="center"><?= $row_clt10['funcao'] ?></td>
                                    <td align="center"><?= number_format($row_clt10['salario'],2,',','.') ?></td>
                                    <td align="center"><?= $row_clt10['data_entrada2'] ?></td>
                                    <td align="center"><?= $periodoGozado ?></td>
                                    <td align="center"><?= $periodoVencido ?></td>
                                    <td align="center"><?= $periodoNaoGozado ?></td>
                                </tr> <?php
                            }
                        }
                    }
                }
                if($count == 0){?>
                    <tr>
                        <td colspan="8">Nenhum Clt encontrado!</td>
                    </tr> <?php
                }
            } ?>
            </table>
        </div>
    </body>
</html>