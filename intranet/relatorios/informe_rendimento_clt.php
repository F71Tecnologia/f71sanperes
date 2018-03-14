<?php

header("Location: ../rendimento/index2.php");

if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../login.php?entre=true';</script>";
    exit;
}

include "../conn.php";
include "../classes/funcionario.php";
include '../classes_permissoes/regioes.class.php';
include "../wfunction.php";
include "../classes_permissoes/acoes.class.php";

$usuario = carregaUsuario();
$optRegiao = getRegioes();
$ACOES = new Acoes();


$regiaoSel = (isset($_REQUEST['reg'])) ? $_REQUEST['reg'] : $usuario['id_regiao'];
$projetoSel = (isset($_REQUEST['pro'])) ? $_REQUEST['pro'] : null;

$regiaoSel = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $regiaoSel;
$projetoSel = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : $projetoSel;
$unidadeSel = (isset($_REQUEST['unidade'])) ? $_REQUEST['unidade'] : null;
$anoSel = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : null;
$mesSel = (isset($_REQUEST['mes'])) ? $_REQUEST['mes'] : null;

////////////////////////////////////////////////////////////////////////////////
/////////////////////////// array de anos //////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
$arrayAnos[-1] = '« Selecione o Ano »';
for ($i = date('Y'); $i >= date('Y') - 10; $i--) {
    $arrayAnos[$i] = $i;
}

/* CARREGA AS FUNÇÕES E UNIDADES VIA AJAX, RETORNA UM JSON */
if (isset($_REQUEST['method']) && !empty($_REQUEST['method']) && $_REQUEST['method'] == "carregafuncao") {
    //UNIDADE
    $qrUnidade = mysql_query("SELECT id_unidade,unidade FROM unidade WHERE campo1 = '{$_REQUEST['projeto']}' ORDER BY unidade");
    $num_rowsU = mysql_num_rows($qrUnidade);
    $unidades = array();
    if ($num_rowsU > 0) {
        $return['stunid'] = 1;
        while ($row = mysql_fetch_assoc($qrUnidade)) {
            $unidades[utf8_encode($row['id_unidade'])] = utf8_encode($row['id_unidade'].' - '.$row['unidade']);
        }
    } else {
        $return['stunid'] = 0;
        $unidades["-1"] = "nenhum curso encontrado";
    }

    $return['unidade'] = $unidades;

    echo json_encode($return);
    exit;
}
?>
<html>
    <head>
        <title>:: Intranet ::</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../favicon.ico" />
        <link href="../net1.css" rel="stylesheet" type="text/css" />

        <link href="../favicon.ico" rel="shortcut icon" />
        <script src="../js/jquery-1.8.3.min.js" type="text/javascript"></script>
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
            });
            
        </script>
    </head>
    <body class="novaintra" >        
        <div id="content">
            <form  name="form" action="../rendimento/informe_projeto_clt.php" id="form1" method="post" id="form">
                <div id="head">
                    <img src="../imagens/logomaster<?php echo $usuario['id_master']; ?>.gif" class="fleft" style="margin-right: 25px;"/>
                    <div class="fleft">
                        <h2>Informe de Rendimento por Projeto (CLT)</h2>
                    </div>
                </div>
                <br class="clear">
                <br/>


                <fieldset class="noprint">
                    <legend>Relatório</legend>
                    <div class="fleft">
                        <p><label class="first">Região:</label> <?php echo montaSelect($optRegiao, $regiaoSel, array('name' => "regiao", 'id' => 'regiao')); ?> </p>                        
                        <p><label class="first">Projeto:</label> <?php echo montaSelect(array("-1" => "« Selecione a Região »"), $projetoSel, array('name' => "projeto", 'id' => 'projeto')); ?> </p>
                    </div>

                    <br class="clear"/>

                    <p class="controls" style="margin-top: 10px;">
                        <input type="submit" name="gerar" value="Gerar" id="gerar"/>
                    </p>
                </fieldset>
            </form>
        </div>
    </body>
</html>