<?php
if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../conn.php');
include('../classes/global.php');
include('../wfunction.php');
include('../classes/cooperativa.php');

$usuario = carregaUsuario();
$filtro = false;

if ((isset($_REQUEST['filtrar']) && !empty($_REQUEST['filtrar'])) || (isset($_SESSION['voltarCooperativa']))) {
    $filtro = true;
    if (isset($_SESSION['voltarCooperativa'])) {
        $_REQUEST['regiao'] = $_SESSION['voltarCooperativa']['id_regiao'];
        $_REQUEST['projeto'] = $_SESSION['voltarCooperativa']['id_projeto'];
        unset($_SESSION['voltarCooperativa']);
    }
    $rs = montaQuery("cooperativas", "id_coop,tipo,nome,tel,contato,taxa", "id_regiao = {$_REQUEST['regiao']} AND status_reg = 1", NULL, null, null, false);
    $num_rows = mysql_num_rows($rs);
}

/* VARIAVEIS PARA MANTER OS CAMPOS DO FORMUL�RIO SELECIONADO */
$regiaoR = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : null;
?>
<html>
    <head>
        <title>:: Intranet :: Cooperativas</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../../favicon.ico" />
        <link href="../net1.css" rel="stylesheet" type="text/css" />
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../favicon.ico" rel="shortcut icon" />
        <script src="../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine.js" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine-pt.js" type="text/javascript"></script>
        <script src="../jquery/mascara/jquery.maskedinput-1.2.2.js" type="text/javascript" ></script>
        <script src="../js/global.js" type="text/javascript"></script>

        <script>
            $(function() {
                $(".bt-image").on("click", function() {
                    var action = $(this).data("type");
                    var key = $(this).data("key");
                    var emp = $(this).parents("tr").find("td:first").next().html();
                    var botao = $(this);

                    //THICKBOX VISUALIZA DOCUMENTOS
                    if (action === "excluir") {
                        if (confirm("Tem certaza que deseja excluir esta cooperativa ou PJ?")) {
                            $.post('action.php', {id_coop: key, action: 'excluir'}, function(data) {
                                alert(data);
                                botao.closest("tr").remove();
                            });
                        }
                    } else if (action === "cooperativa") {
                        $("#cooperativa").val(key);
                        $("#form1").attr('action', 'ver_cooperativa.php');
                        $("#form1").submit();
                    } else if (action === "editar") {
                        $("#cooperativa").val(key);
                        $("#form1").attr('action', 'form_cooperativa.php');
                        $("#form1").submit();
                    }
                });

                $("#novoPrest").click(function() {
                    $("#form1").attr('action', 'form_cooperativa.php');
                    $("#form1").submit();
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
                        <h2>Administrativo - Cooperativas</h2>
                        <p>Administra��o de Cooperativas</p>
                    </div>
                </div>

                <fieldset>
                    <legend>Filtro</legend>
                    <input type="hidden" name="hide_projeto" id="hide_projeto" value="<?php echo $projetoR ?>" />
                    <input type="hidden" name="cooperativa" id="cooperativa" value="" />
                    <p><label class="first">Regi�o:</label> <?php echo montaSelect(GlobalClass::carregaRegioes($usuario['id_master']), $regiaoR, "id='regiao' name='regiao' class='required[custom[select]]'") ?></p>

                    <p class="controls"> <input type="submit" class="button" value="Filtrar" name="filtrar" /> <?php if ($filtro) { ?><input type="submit" class="button" value="Nova Cooperativa" name="novo" id="novoPrest" /><?php } ?></p>
                </fieldset>

                <?php
                if ($filtro) {
                    if ($num_rows > 0) {
                        $count = 0;
                        ?>
                        <br/>
                        <p style="text-align: right; margin-top: 20px"><input type="button" onclick="tableToExcel('tbRelatorio', 'Relat�rio')" value="Exportar para Excel" class="exportarExcel"></p>
                        <table id="tbRelatorio" cellpadding="0" cellspacing="0" border="0" class="grid" width="100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>TIPO</th>
                                    <th>NOME</th>
                                    <th>TEL</th>
                                    <th>CONTATO</th>
                                    <th>TAXA</th>
                                    <th colspan="3">A��ES</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysql_fetch_assoc($rs)) { ?>
                                    <tr class="<?php echo ($count++ % 2 == 0) ? "odd" : "even" ?>">
                                        <td class="center"><?php echo $row['id_coop']; ?></td>
                                        <td class="center"><?php echo ($row['tipo'] == 1) ? 'Cooperativa' : 'Pessoa Jur�dica'; ?></td>
                                        <td class="center"><?php echo $row['nome']; ?></td>
                                        <td class="center"><?php echo $row['tel']; ?></td>
                                        <td class="center"><?php echo $row['contato']; ?></td>
                                        <td class="center"><?php echo $row['taxa']; ?></td>
                                        <td class="center"><img src="../imagens/icones/icon-doc.gif" title="Ver Cooperativa" class="bt-image" data-type="cooperativa" data-key="<?php echo $row['id_coop']; ?>" /></td>
                                        <td class="center"><img src="../imagens/icones/icon-edit.gif" title="Editar" class="bt-image" data-type="editar" data-key="<?php echo $row['id_coop']; ?>" /></td>
                                        <td class="center"><img src="../imagens/icones/icon-trash.gif" title="Excluir" class="bt-image" data-type="excluir" data-key="<?php echo $row['id_coop']; ?>" /></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    <?php } else { ?>
                        <br/>
                        <div id='message-box' class='message-yellow'>
                            <p>Nenhum registro encontrado</p>
                        </div>
                    <?php }
                }
                ?>
            </form>
        </div>
    </body>
</html>