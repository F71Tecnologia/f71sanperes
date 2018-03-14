<?php

include('include/restricoes.php');
include('../conn.php');
include('../funcoes.php');
include('../wfunction.php');
include('include/criptografia.php');
include('../classes/formato_data.php');
include("../classes_permissoes/regioes.class.php");

$obj_regiao = new Regioes();

//----- CARREGA OS PROJETOS VIA AJAX, RETORNA UM JSON 
if (isset($_REQUEST['method']) && !empty($_REQUEST['method']) && $_REQUEST['method'] == "loadprojeto") {

    $return['status'] = 1;
    $qr_bancos = mysql_query("SELECT * FROM projeto WHERE id_regiao = '{$_REQUEST['regiao']}' AND status_reg=1");
    $num_rows = mysql_num_rows($qr_bancos);
    $bancos = array();
    if ($num_rows > 0) {
        while ($row = mysql_fetch_assoc($qr_bancos)) {
            $bancos[$row['id_projeto']] = $row['id_projeto'] . " - " . utf8_encode($row['nome']);
        }
    } else {
        $bancos["-1"] = "Projeto não encontrado";
    }
    $return['options'] = $bancos;
    echo json_encode($return);
    exit;
}
?>

<html>
    <head>
        <title>:: Intranet :: PRESTAÇÃO DE CONTAS</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link href="../favicon.ico" rel="shortcut icon" />
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="css/estrutura.css" rel="stylesheet" type="text/css">
        <script src="../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>

        <script src="../js/global.js" type="text/javascript"></script>

        <script>
            $(function() {
                $("#regiao").change(function() {
                    var $this = $(this);
                    if ($this.val() !== "-1") {
                        showLoading($this, "../");
                        $.post('prestador.php', {regiao: $this.val(), method: "loadprojeto"}, function(data) {
                            removeLoading();
                            if (data.status === 1) {
                                var opcao = "";
                                for (var i in data.options) {
                                    opcao += "<option value='" + i + "' >" + data.options[i] + "</option>";
                                }
                                $("#selprojeto").html(opcao);
                            }
                        }, 'json');
                    }
                }).trigger("change");
            });
        </script>
        <style type="text/css">
            #prestador{ width: 890px; border: 0px solid #ddd; margin: 0 auto; padding: 20px;}
            .label{ display: inline-block; width: 63px; text-align: right; }
            #selprojeto{ margin: 0px; padding: 5px; width: 306px; margin-top: 3px; font-size: 10px; color: #808080; }
        </style>
    </head>
    <body>
        <div id="corpo">
            <div id="menu" class="prestador">
                <?php //include "include/menu_prestador.php"; ?>
            </div>
            <div id="prestador">
                <form name="form1" id="form1" action="../processo/prestadorservico.php" method="POST">  
                    <fieldset>
                        <legend>Dados</legend>
                        <p>
                            <label for="regiao" class="first label">Região: </label>
                            <select name="regiao" id="regiao" style="font-size: 10px; color: #808080; padding: 5px;">        
                                <?php $obj_regiao->Preenhe_select_por_master($Master); ?>       
                            </select>
                        </p>
                        <p><label class="first label">Projeto:</label> <?php echo montaSelect(array("-1" => "« Todos »"), null, "id='selprojeto' name='selprojeto'") ?></p>
                        <p class="controls">
                            <input name="enviar" id="enviar" type="submit" value="OK" style="float: right; padding: 5px; margin-top: -2px;"/> </td>
                        </p>
                    </fieldset>
                </form>   
            </div>    
            <div id="pagina" style="text-align:center;">     
            </div>
            <div id="rodape">
                <?php //include('include/rodape.php'); ?>
            </div>

        </div>        
    </body>
</html>
