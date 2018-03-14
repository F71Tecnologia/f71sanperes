<?php

error_reporting(E_NOTICE);

if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
    exit;
}

include "../conn.php";
include "../classes/funcionario.php";
include '../classes_permissoes/regioes.class.php';
include '../classes/FolhaClass.php';
include '../classes/RescisaoClass.php';
include '../classes/FeriasClass.php';
include("../wfunction.php");

$optRegiao = getRegioes();
$regiaoSel = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : "";
$projetoSel = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : "";

function formata_numero($num) {
    if (strstr($num, '.') and !empty($num)) {
        return number_format($num, 2, ',', '.');
    } else {
        return $num;
    }
}

//OBJETO
$folha = new Folha();
$rescisao = new Rescisao();
$ferias = new Ferias();

//CARREGA DADOS DO USUÁRIO (NESSE CASO, LOGO DA EMPRESA VINCULADA AO USUÁRIO)
$usuario = carregaUsuario();


//GERARA
if (isset($_REQUEST['gerar'])) {
    
    //RECUPERA AS ULTIMAS FOLHAS
    $ultimas_folhas = $folha->getUltimasFolhas($regiaoSel, $projetoSel, 3);
    
    //SELECIONA AS FOLHAS EM QUESTÃO
    $f = $folha->getFolhaById($ultimas_folhas["folha"], array("id_folha","mes","ano"));
    $folhas = array();
    while($folha_dados = mysql_fetch_assoc($f)){
        $folhas[$folha_dados['id_folha']][$folha_dados['ano']] = $folha_dados['mes'];
    }
    
    //SELECIONA OS MOVIMENTOS ESPECÍFICOS NAS FOLHAS EM QUESTÃO
    $dados = $folha->getFolhaComparada($ultimas_folhas["folha"], array(50227, 50249, 5012, 5061, 50242));
    
    //RESCISÃO PELO FATOR (EMPREGADO, EMPREGADOR)
    $dados_rescisao = $rescisao->getTotalRescisaoByFolha($projetoSel, $ultimas_folhas["mes"], $ultimas_folhas["ano"]);
    
    //TOTAL DE FÉRIAS
    $dados_ferias = $ferias->getTotalFeriasByFolha($ultimas_folhas["folha"]);   
    
}
?>
<html>
    <head>
        <title>Comparativo Entre Folhas</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <link href="../net1.css" rel="stylesheet" type="text/css">
        <script src="../jquery/jquery-1.4.2.min.js" type="text/javascript"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../js/global.js" type="text/javascript"></script>
        <script>
            $(function() {
                var id_destination = "projeto";
                $('#regiao').ajaxGetJson("../methods.php", {method: "carregaProjetos"}, function(data) {
                    removeLoading();
                    $("#" + id_destination).html(data);
                    var selected = $("input[name=hide_" + id_destination + "]").val();
                    if (selected !== undefined) {
                        $("#" + id_destination).val(selected);
                    }
                    $('#projeto').trigger('change');
                }, "projeto");

                $('#projeto').ajaxGetJson("../methods.php", {method: "carregaFuncoes", default: "2"}, null, "funcao");

            });
        </script>
        <style type="text/css">
            table{
                border: 1px solid #E2E2E2;
            }

            #result th{
                background: #EEEEEE; 
                color: #000;
                border: 1px solid #E2E2E2;
                font-weight: bold;
                padding: 3px;
            }
            #result tr{
                padding: 5px; 
                font-size: 11px; 
                height: 25px; 
                padding: 1px;
                border: 1px solid #EEEEEE;
                text-align: right;
            }
            #result td{
                padding: 5px;
                box-sizing: border-box;
                border: 1px solid #EEEEEE;
                text-align: right;
            }
            .destaque{
                background: #e1e1e1;
                font-weight: bold;
                text-align: right;
            }

        </style>
        <style media="print">
            fieldset{ display: none;}
            body{ background-color: #FFF;}
            tr{ padding: 20px; background: #333;}
        </style>
    </head>
    <body class="novaintra">       
        <div id="content" style="display: table; width: 100%;">
            <div id="head">
                <img src="../imagens/logomaster<?php echo $usuario['id_master']; ?>.gif" class="fleft" style="height: 83; width: 163px; " width="140" height="100"/>
                <div class="fleft" style="width:86.3%">
                    <h2 style="margin-left: 80px;">COMPARATIVO ENTRE ULTIMAS FOLHAS</h2>
                </div>
            </div>
            <form  name="form" action="" method="post" id="form">
                <fieldset>
                    <legend>ULTIMAS FOLHAS</legend>
                    <input type="hidden" name="projeto_selecionado" id="projeto_selecionado" value="<?php echo $projetoSel; ?>" />
                    <div class="fleft">
                        <input type="hidden" name="hide_projeto" id="hide_projeto" value="<?php echo $projetoSel ?>" />
                        <input type="hidden" name="hide_funcao" id="hide_funcao" value="<?php echo $funcaoSel ?>" />
                        <p><label class="first">Região:</label> <?php echo montaSelect($optRegiao, $regiaoSel, array('name' => "regiao", 'id' => 'regiao')); ?> </p>                        
                        <p><label class="first">Projeto:</label> <?php echo montaSelect(array("-1" => "« Selecione a Região »"), $projetoSel, array('name' => "projeto", 'id' => 'projeto')); ?> </p>
                    </div>
                    <br class="clear"/>
                    <p class="controls" style="margin-top: 10px;">
                        <input type="submit" name="gerar" value="Gerar" id="gerar" style=" padding: 7px 35px; border: 1px solid #ccc;"/>
                    </p>
                </fieldset>
            </form>
            <div class="conteudo">
                <?php if (isset($_REQUEST['gerar'])) { ?>
                    <?php foreach ($folhas as $folha => $ano) { ?>
                        <?php foreach ($ano as $anos => $meses) { ?>
                            <table class="grid" border="1" cellspacing="0" cellpadding="0" style="float: left; width: 550px; margin: 15px 10px;" >
                                <thead> 
                                    <tr>
                                        <th colspan="3"><?php echo $meses . "/" . $anos; ?></th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <!--RESCISÃO --->

                                    <?php $rescisao = ""; $total_rescisao = 0; ?>
                                    <?php foreach ($dados_rescisao as $rescisao_key => $rescisao_valor){ ?>   
                                        <?php if($rescisao_key == $meses){ ?>
                                            <?php $total_rescisao = 0; ?>
                                            <?php if($rescisao != $rescisao_valor[$anos]["nome"]){ ?>
                                                <?php $rescisao = $rescisao_valor[$anos]["nome"]; ?>
                                                <tr>
                                                    <td colspan="2" style="background: #f1f1f1; font-weight: bold;"><?php echo $rescisao_valor[key($ano)]["nome"]; ?></td>
                                                </tr>
                                            <?php } ?>
                                            <?php $fator = "";  ?>
                                            <?php foreach ($rescisao_valor[$anos]["cargo"] as $fatores => $salario_rescisao){ ?>    
                                                <?php if($fator != $fatores){ ?>
                                                    <?php $fator = $fatores ?>
                                                    <tr>
                                                        <td colspan="2" style="padding-left: 30px; text-transform: uppercase; background: #f2f2f2"> » <?php echo $fatores; ?></td>
                                                    </tr>  
                                                <?php } ?>
                                                <?php foreach ($salario_rescisao as $cargo_rescisao => $salario_rescisao){ ?>
                                                    <tr>
                                                        <td style="padding-left: 60px;"> » <?php echo $cargo_rescisao; ?></td>
                                                        <td><?php echo $salario_rescisao; $total_rescisao += $salario_rescisao; ?></td>
                                                    </tr>  
                                                <?php } ?>
                                            <?php } ?>
                                                <tr>
                                                    <td style="text-align: right">TOTAL</td>
                                                    <td><?php echo "<b>" . number_format($total_rescisao, 2, ",", ".") . "</b>"; ?></td>
                                                </tr>
                                            <?php break; ?>
                                        <?php } ?>
                                    <?php } ?>

                                    <!--FÉRIAS--->           
                                    <tr>
                                        <td colspan="2" style="background: #f1f1f1; font-weight: bold;">FÉRIAS</td>
                                    </tr>
                                    <tr>
                                        <td style="padding-left: 30px;">» TOTAL FÉRIAS</td>
                                        <td><?php echo number_format($dados_ferias[$anos][(int)$meses],'2',',','.'); ?></td>
                                    </tr>

                                   <!--MOVIMENTOS--->         

                                    <?php foreach ($dados as $key => $meses_mov) { ?>
                                        <?php $mes_atual = ""; ?>
                                        <?php $total_salario = 0; ?>
                                        <?php foreach ($meses_mov[$anos][(int)$meses] as $keys => $cargo) { ?>
                                            <?php $movimento = ""; ?>
                                            <?php if($movimento != $keys){ ?>
                                                <?php $movimento = $keys; ?>
                                                <tr>
                                                    <td colspan="2" style="background: #f1f1f1; font-weight: bold;"><?php echo $cargo['nome']; ?></td>
                                                </tr>
                                            <?php } ?>
                                            <?php foreach ($cargo['cargo'] as $cargos => $salarios) { ?>
                                                <tr>
                                                    <td style="padding-left: 30px;"> » <?php echo $cargos; ?></td>
                                                    <td><?php echo number_format($salarios, 2, ",", ".");
                                                    $total_salario += $salarios; ?></td>
                                                </tr>
                                            <?php } ?>
                                            <?php if ($total_salario != 0) { ?>    
                                                <tr>
                                                    <td style="text-align: right">TOTAL</td>
                                                    <td><?php echo "<b>" . number_format($total_salario, 2, ",", ".") . "</b>"; ?></td>
                                                </tr>    
                                            <?php } ?>
                                        <?php } ?>

                                    <?php } ?>
                                </tbody>
                                <tfoot>
                                </tfoot>
                            </table>
                        <?php } ?>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>
    </body>
</html>