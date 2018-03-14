<?php
if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../conn.php');
include('../classes/global.php');
include('../wfunction.php');

$usuario = carregaUsuario();
$filtro = false;

if ((isset($_REQUEST['filtrar']) && !empty($_REQUEST['filtrar']))) {
    $filtro = true;

    $result_projeto = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$projeto'");
    $row_projeto = mysql_fetch_array($result_projeto);
    
    $filtroData =''; // define a variável filtroData como vazia
    if (isset($_REQUEST['from']) && !empty($_REQUEST['from'])) {
        $data_ini_periodo = converteData($_REQUEST['from']); 
        $filtroData = " and data_ini >= '{$data_ini_periodo}'"; // altera filtroData para tadas maiores q data inicio
    }
    if (isset($_REQUEST['to']) && !empty($_REQUEST['to'])) {
        $data_fim_periodo = converteData($_REQUEST['to']);
        $filtroData = " and  data_fim <= '$data_fim_periodo'"; // altera filtro data para datas menores q data fim
    }

    if(isset($_REQUEST['from']) && isset($_REQUEST['to']) && !empty($_REQUEST['from']) && !empty($_REQUEST['to'])){
        $filtroData = " and (data_ini between '{$data_ini_periodo}' and '{$data_fim_periodo}' or data_fim between '{$data_ini_periodo}' and '$data_fim_periodo')"; // verifica datas entre o periodo do filtro
    }

    $result = mysql_query("SELECT nome, DATE_FORMAT(data_ini,'%d/%m/%Y')  as data_ini, DATE_FORMAT(data_fim,'%d/%m/%Y') as data_fim FROM rh_ferias WHERE projeto = '$_REQUEST[projeto]' AND regiao = '$_REQUEST[regiao]' AND status = 1 AND nome != '' {$filtroData} ORDER BY nome") or die(mysql_error());
    $num_rows = mysql_num_rows($result);
}

/* VARIAVEIS PARA MANTER OS CAMPOS DO FORMULÁRIO SELECIONADO */
$projetoR = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : null;
$regiaoR = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : null;
?>
<html>
    <head>
        <title>:: Intranet :: Prestador de Serviço</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../favicon.ico" />
        <link href="../net1.css" rel="stylesheet" type="text/css" />
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="prestador.css" rel="stylesheet" type="text/css" />
        <link href="../favicon.ico" rel="shortcut icon" />
        <script src="../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine.js" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine-pt.js" type="text/javascript"></script>
        <script src="../jquery/mascara/jquery.maskedinput-1.2.2.js" type="text/javascript" ></script>
        <script src="../js/global.js" type="text/javascript"></script>

        <script>
            $(function() {
                $("#regiao").ajaxGetJson("../methods.php", {method: "carregaProjetos"}, null, "projeto");

                $(".bt-image").on("click", function() {
                    var action = $(this).data("type");
                    var key = $(this).data("key");
                    var emp = $(this).parents("tr").find("td:first").next().html();

                    //THICKBOX VISUALIZA DOCUMENTOS
                    if (action === "docs") {
                        thickBoxIframe(emp, "actions.php", {prestador: key, method: "getDocs"}, "625-not", "500");
                    } else if (action === "duplicar") {
                        $("#prestador").val(key);
                        $("#form1").attr('action', 'duplicar_prestador.php');
                        $("#form1").submit();
                    } else if (action === "prestador") {
                        $("#prestador").val(key);
                        $("#form1").attr('action', 'ver_prestador.php');
                        $("#form1").submit();
                    } else if (action === "editar") {
                        $("#prestador").val(key);
                        $("#form1").attr('action', 'form_prestador.php');
                        $("#form1").submit();
                    }
                });

                $("#novoPrest").click(function() {
                    $("#form1").attr('action', 'form_prestador.php');
                    $("#form1").submit();
                });

                $(function() {
                    $("#from").datepicker({
                        defaultDate: "+1w",
                        changeMonth: true,
                        changeYear: true,
                        onClose: function(selectedDate) {
                            $("#to").datepicker("option", "minDate", selectedDate);
                        }
                    });
                    $("#to").datepicker({
                        defaultDate: "+1w",
                        changeMonth: true,
                        changeYear: true,
                        onClose: function(selectedDate) {
                            $("#from").datepicker("option", "maxDate", selectedDate);
                        }
                    });
                });
            });
        </script>
        <style>
            .bt-image{
                width: 18px;
                cursor: pointer;
            }
        </style>
    </head>
    <body class="novaintra">
        <div id="content">
            <form action="" method="post" name="form1" id="form1" enctype="multipart/form-data" >
                <div id="head">
                    <img src="../imagens/logomaster<?php echo $usuario['id_master'] ?>.gif" class="fleft" style="margin-right: 25px;">
                    <div class="fleft">
                        <h2>Administrativo - Prestador de Serviço</h2>
                        <p>Administração geral das Empresas Prestadoras de Serviço</p>
                    </div>
                </div>

                <fieldset>
                    <legend>Filtro</legend>
                    <input type="hidden" name="hide_projeto" id="hide_projeto" value="<?php echo $projetoR ?>" />
                    <input type="hidden" name="prestador" id="prestador" value="" />
                    <p><label class="first">Região:</label> <?php echo montaSelect(GlobalClass::carregaRegioes($usuario['id_master']), $regiaoR, "id='regiao' name='regiao' class='required[custom[select]]'") ?></p>
                    <p><label class="first">Projeto:</label> <?php echo montaSelect(array("-1" => "« Selecione a Região »"), $projetoR, "id='projeto' name='projeto' class='required[custom[select]]'") ?></p>


                    <p><label for="from" class="first">Início do Período:</label>
                        <input type="text" id="from" name="from"></p>
                    <p><label for="to" class="first">Fim do Período:</label>
                        <input type="text" id="to" name="to"></p>

                    <p class="controls"> <input type="submit" class="button" value="Filtrar" name="filtrar" />
                </fieldset>

                <?php
                if ($filtro) {
                    if ($num_rows > 0) {
                        $count = 0;
                        ?>
                        <br/>
                        <table cellpadding="0" cellspacing="0" border="0" class="grid" width="100%">
                            <thead>
                                <tr>

                                    <th>Nome</th>
                                    <th>Início</th>
                                    <th>Fim</th>

                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysql_fetch_array($result)) { ?>
                                    <tr class="<?php echo ($count++ % 2 == 0) ? "odd" : "even"; ?>">
                                        <td><?php echo $row['nome']; ?></td>
                                        <td><?php echo $row['data_ini']; ?></td>
                                        <td><?php echo $row['data_fim']; ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    <?php } else { ?>
                        <br/>
                        <div id='message-box' class='message-yellow'>
                            <p>Nenhum registro encontrado</p>
                        </div>
                        <?php
                    }
                }
                ?>
            </form>
        </div>
    </body>
</html>