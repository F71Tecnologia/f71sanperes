<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../login.php?entre=true';</script>";
    exit;
}

include "../conn.php";
include "../classes/funcionario.php";
include '../classes_permissoes/regioes.class.php';
include('../funcoes.php');
include "../wfunction.php";
include "../classes_permissoes/acoes.class.php";

$usuario = carregaUsuario();
$optRegiao = getRegioes();
$ACOES = new Acoes();

if (isset($_REQUEST['gerar'])) {
     
    $arquivo_1 = $_FILES['arquivo1'];
    
    $diretorio_destino = '../rh/sefip/arquivos/';
    
    $id_regiao = $_REQUEST['regiao'];
    $id_projeto = $_REQUEST['projeto'];
    $ano = $_REQUEST['ano'];
    $mes = str_pad($_REQUEST['mes'], 2, "0", STR_PAD_LEFT);
    $mes_int = (int) $mes;

    switch ($_REQUEST['tipo-consulta']) {
        case 1: // 13º
            $sql = "";
            $sqlTpConsulta = "AND terceiro = 1 ";
            break;
        case 2: // normal
            $sql = "";
            $sqlTpConsulta = "AND terceiro = 2 ";
            break;
        default:
            break;
    }

    $qrTotalFolha = "SELECT A.id_folha, A.status, A.clts, A.base_inss, A.tipo_terceiro,(A.total_inss+A.inss_ferias+A.inss_rescisao+A.inss_dt) AS inss_empregado, (A.base_inss * 0.2) AS inss_empresa, (A.base_inss * 0.01) AS rat, (SUM(B.salfamilia) + SUM(B.a6005)) AS salFamiMater,
        (A.base_inss * 0.058) AS outrasEntidades
                    FROM rh_folha AS A
                    LEFT JOIN rh_folha_proc AS B ON (A.id_folha = B.id_folha)
                    WHERE A.regiao = $id_regiao AND A.projeto = $id_projeto AND A.ano = '$ano' AND A.mes = '$mes' AND A.sTATUS IN (2,3) AND B.status = 3 $sqlTpConsulta;";
    
    $result_folha = mysql_query($qrTotalFolha) or die("Erro na consulta do totalizador da folha");
    
    

    move_uploaded_file($arquivo_1['tmp_name'], $diretorio_destino . 'sefip.re');
}
$projeto = montaQueryFirst("projeto", "nome", array('id_projeto' => $id_projeto));

$regiaoSel = (isset($_REQUEST['reg'])) ? $_REQUEST['reg'] : $usuario['id_regiao'];
$projetoSel = (isset($_REQUEST['pro'])) ? $_REQUEST['pro'] : null;
$funcaoSel = (isset($_REQUEST['funcao'])) ? $_REQUEST['funcao'] : null;

$regiaoSel = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $regiaoSel;
$projetoSel = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : $projetoSel;


$anoSel = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : date('Y');
$mesSel = (isset($_REQUEST['mes'])) ? $_REQUEST['mes'] : date('m');
$tipoSel = (isset($_REQUEST['tipo-consulta'])) ? $_REQUEST['tipo-consulta'] : null;

/////////////////////////// array de anos //////////////////////////////////////
$arrayAnos[-1] = '« Selecione o Ano »';
for ($i = date('Y'); $i >= date('Y') - 10; $i--) {
    $arrayAnos[$i] = $i;
}

// Lê o arquivo SEFIP.re
// Abre um arquivo somente para leitura
$id_arquivo = fopen("../rh/sefip/arquivos/sefip.re", "r");
while (!feof($id_arquivo)) {
    // lê uma linha do arquivo
    $linha = fgets($id_arquivo);
    $ini = substr("$linha",0,2);
    $pis = trim(substr("$linha",31,12));
    if(strcmp($ini, '30')==0){ // clt
        $bInss =substr(substr("$linha", -195,15),0,13).'.'.substr(substr("$linha", -195,15),-2);
        $bInssRes =substr(substr("$linha", 232,14),0,12).'.'.substr(substr("$linha", 232,14),-2);
        $arraySefip[$pis][] = number_format($bInss,2, ',', '.');
        $arraySefip[$pis][] = number_format($bInssRes,2, ',', '.');        
    }
    unset($sem13,$com13);

    }
// fecha o arquivo e libera recursos da memória
fclose($id_arquivo);
?>
<html>
    <head>
        <title>:: Intranet :: Conferência de Sefip</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../favicon.ico" />
        <link href="../net1.css" rel="stylesheet" type="text/css" />
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css"/>

        <link href="../favicon.ico" rel="shortcut icon" />
        <script src="../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine-2.6.js" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine-pt.js" type="text/javascript"></script>
        <script src="../js/global.js" type="text/javascript"></script>

        <script>
            $(function() {
                $('#mes').change(function() {
                    if ($(this).val() < 11) {
                        $('#tipo-consulta').attr("disabled", "disabled");
                    } else {
                        $('#tipo-consulta').removeAttr("disabled");
                    }
                });

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

                var myArray = [];
                $(".ev").each(function() {
                    if ($.inArray($(this).data('id'), myArray) !== -1) {
                        $("." + $(this).data('id')).addClass("alert");
                    }
                    myArray.push($(this).data('id'));
                });
            });

            $(document).ready(function() {
                // instancia o validation engine no formulário
                $("#form1").validationEngine();
            });
            checkSelect = function(field) {
                var date = field.val();
                if (date <= 0 || date == null || date == '') {
                    return 'Selecione um Valor';
                }
            };
        </script>
    </head>
    <body class="novaintra" >        
        <div id="content">
            <form  name="form" action="" method="post" id="form" enctype="multipart/form-data">
                <div id="head">
                    <img src="../imagens/logomaster<?php echo $usuario['id_master']; ?>.gif" class="fleft" style="margin-right: 25px;"/>
                    <div class="fleft">
                        <h2>Relatório de Conferência de Sefip</h2>
                    </div>
                </div>
                <br class="clear">
                <br/>
                <fieldset class="noprint">
                    <legend>Relatório</legend>
                    <div class="fleft">
                        <input type="hidden" name="hide_projeto" id="hide_projeto" value="<?php echo $projetoSel ?>" />
                        <input type="hidden" name="hide_funcao" id="hide_funcao" value="<?php echo $funcaoSel ?>" />
                        <p><label class="first">Região:</label> <?php echo montaSelect($optRegiao, $regiaoSel, array('name' => "regiao", 'id' => 'regiao', 'class' => 'validate[required,funcCall[checkSelect]]')); ?> </p>                        
                        <p><label class="first">Projeto:</label> <?php echo montaSelect(array("-1" => "« Selecione a Região »"), $projetoSel, array('name' => "projeto", 'id' => 'projeto', 'class' => 'validate[required,funcCall[checkSelect]]')); ?> </p>
                        <p><label class="first">Período:</label> <?php echo montaSelect(mesesArray(), $mesSel, array('name' => "mes", 'id' => 'mes', 'class' => 'validate[required,funcCall[checkSelect]]')); ?> / <?php echo montaSelect($arrayAnos, $anoSel, array('name' => "ano", 'id' => 'ano', 'class' => 'validate[required,funcCall[checkSelect]]')); ?></p>
                        <p>
                            <label class="first">Competência:</label>
                            <select name="tipo-consulta" id="tipo-consulta" class="validate[required,funcCall[checkSelect]]">
                                <option value="0">-- Selecione --</option>
                                <option value="1" <?= ($tipoSel == 1) ? 'selected="selected"' : '' ?>>Folha de 13º</option>
                                <option value="2" <?= ($tipoSel == 2) ? 'selected="selected"' : '' ?>>Folha normal</option>
                            </select>
                        </p>
                        <p><label class="first">Importar arquivo:</label><input type="file" name="arquivo1" id="arquivo1" /></p>

                    </div>
                    <br class="clear"/>
                    <p class="controls" style="margin-top: 10px;">
                        <input type="submit" name="gerar" value="Gerar" id="gerar"/>
                    </p>
                </fieldset>
                <?php
                if (isset($_POST['gerar'])) {
                    
                    while ($row = mysql_fetch_assoc($result_folha)) {
                        $inss_empregado = $row['inss_empregado'];
                        $inss_empresa = $row['inss_empresa'];
                        $rat = $row['rat'];
                        $salFamiMater = $row['salFamiMater'];
                        $outrasEntidades = $row['outrasEntidades'];
                        $folha = $row['id_folha'];
                    }
                    
                    ?>
                    <table id="tbRelatorio" border="0" cellpadding="0" cellspacing="0" class="grid" width="100%" style="page-break-after:auto;">
                        <thead>
                            <tr>
                                <th colspan="7">Totalizador</th>
                            </tr>
                            <th>Empregados/ Avulsos (Segurado)</th>
                            <th>Empregados/ Avulsos (Empresa)</th>
                            <th>Rat</th>
                            <th>Sal. Família/ Sal. Maternidade</th>
                            <th>Outras Entidades</th>
                        </thead>
                        <tbody>
                            <tr>
                                <td align="center"> R$ <?php echo number_format($inss_empregado, 2, ',', '.'); ?></td>
                                <td align="center"> R$ <?php echo number_format($inss_empresa, 2, ',', '.'); ?></td>
                                <td align="center"> R$  <?php echo number_format($rat, 2, ',', '.'); ?></td>
                                <td align="center"> R$  <?php echo number_format($salFamiMater, 2, ',', '.'); ?></td>
                                <td align="center"> R$  <?php echo number_format($outrasEntidades, 2, ',', '.'); ?></td>
                            </tr>
                        </tbody>
                        
                    </table>
                    <br/>
                    <table id="tbRelatorio" border="0" cellpadding="0" cellspacing="0" class="grid" width="100%" style="page-break-after:auto;"> 
                        <thead>
                            <tr>
                                <th colspan="8"><?php echo $projeto['nome'] ?></th>
                            </tr>
                            <tr>
                                <th>ID</th>
                                <th>NOME</th>
                                <th>BASE INSS (Folha)</th>
                                <th>BASE INSS (Sefip)</th>
                                <th>BASE 13 RESCISÃO (Folha)</th>
                                <th>BASE 13 RESCISÃO (Sefip)</th>
                                <th>RESCISÃO</th>
                                <th>Erro?</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
//                            while ($row_folha = mysql_fetch_assoc($result_folha)) {
                                // Consulta dos Participantes da Folha
//                                $folha = $row_folha['id_folha'];
                                $qr_participante = mysql_query("SELECT A.id_clt, A.nome, A.base_inss, A.base_inss_13_rescisao, B.pis
                                                                FROM rh_folha_proc AS A
                                                                LEFT JOIN rh_clt AS B ON (A.id_clt = B.id_clt)
                                                                WHERE A.id_folha = $folha AND A.status = 3
                                                                ORDER BY B.pis ASC;");
                                while ($row_participante = mysql_fetch_assoc($qr_participante)) {
                                    $class = ($cont++ % 2 == 0) ? "even" : "odd";
                                    $bInssFolha = number_format($row_participante['base_inss'], 2, ',', '.');
                                    $bInssResFolha = number_format($row_participante['base_inss_13_rescisao'], 2, ',', '.');
                                    $sinalizadorDIF = false;
                                    ?>
                                    <tr class="<?php echo $class ?>">
                                        <td><?php echo $row_participante['id_clt'] ?></td>
                                        <td><?php echo $row_participante['nome'] ?></td>
                                        <td align="center">R$ <?php echo $bInssFolha; ?></td>    
                                        <?php 
                                                foreach ($arraySefip as $key => $value) {
                                                    if(strcmp($key, $row_participante['pis']) == 0 ){
                                                        if($_REQUEST['tipo-consulta']==1){
                                                            $valor = $value[1];
                                                        }else{
                                                            $valor = $value[0];
                                                        }
                                                        
                                                        if($valor != $bInssFolha){
                                                            $sinalizadorDIF = true;
                                                        }
                                                        
                                                        echo "<td align='center'>R$ $valor </td>"; // bInss
                                                        break;
                                                    }
                                                }
                                        ?>
                                        <td align="center">R$ <?php echo $bInssResFolha; ?></td>   
                                        <?php 
                                                foreach ($arraySefip as $key => $value) {
                                                    if(strcmp($key, $row_participante['pis']) == 0){
                                                        if($_REQUEST['tipo-consulta']==1){
                                                            $valor = $value[0];
                                                        }else{
                                                            $valor = $value[1];
                                                        }
                                                        
                                                        if($valor != $bInssResFolha){
                                                            $sinalizadorDIF = true;
                                                        }
                                                        
                                                        echo "<td align='center'>R$ $valor</td>"; // bInssRes
                                                        break;
                                                    }
                                                }
                                        ?>
                                                                                                                                       
                                        <td style="text-align:center;">
                                            <?php
                                            $id_clt = $row_participante['id_clt'];
                                            $qr_rescisao = mysql_query("SELECT id_recisao, rescisao_complementar FROM rh_recisao where id_clt = $id_clt AND status = 1;");
                                            $nRow = mysql_num_rows($qr_rescisao);
                                            if ($nRow != 0) {
                                                while ($row_rescisao = mysql_fetch_assoc($qr_rescisao)) {
                                                    $id_rescisao = $row_rescisao['id_recisao'];
                                                    $link = str_replace('+', '--', encrypt("$id_regiao&$id_clt&$id_rescisao"));
                                                    if (substr($row_rel['data_proc'], 0, 10) >= '2013-04-04') {
                                                        $link_nova_rescisao = "nova_rescisao_2.php?enc=$link";
                                                    } else {
                                                        $link_nova_rescisao = "nova_rescisao.php?enc=$link";
                                                    }
                                                    echo "<a href='../rh/recisao/$link_nova_rescisao;' class='link' target='_blank' title='Visualizar Rescisão'><img src='../imagens/pdf.gif' border='0'></a>";
                                                }
                                            } else {
                                                echo "---";
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php echo ($sinalizadorDIF) ? "ERRO" : ""; ?>
                                        </td>
                                        <?php } ?>
                                    </tr>
                                    
                                <?php } ?>

                            <?php // $total += $row_rel['total_liquido'];  ?>
                        </tbody>
                    </table>
                        <?php             
                   unset($inss_empresa,$inss_empresa,$rat,$salFamiMater);
                ?>
            </form>
        </div>
    </body>
</html>