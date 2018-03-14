<?php
if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../../conn.php');
include('../../classes/global.php');
include('../../wfunction.php');
include('../../classes/FuncoesClass.php');

$usuario = carregaUsuario();
$filtro = false;

if (isset($_REQUEST['filtrar']) && !empty($_REQUEST['filtrar'])) {
    $filtro = true;
    //traz os registros das funcoes de acordo com o id do projeto selecionado 
    $funcoes = FuncoesClass::listaFuncoesClt($_REQUEST['projeto']);
    #print_r($funcoes);
    //conta o numero de linhas
    $num_rows = count($funcoes);
}

$projetoR = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : null;
$upa = (isset($_REQUEST['upa'])) ? $_REQUEST['upa'] : null;
?>
<html>
    <head>
        <title>:: Intranet :: </title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../../favicon.ico" />
        <link href="../../net1.css" rel="stylesheet" type="text/css" />
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../../favicon.ico" rel="shortcut icon" />
        <script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine-pt.js" type="text/javascript"></script>
        <script src="../../jquery/mascara/jquery.maskedinput-1.2.2.js" type="text/javascript" ></script>
        <script src="../../js/global.js" type="text/javascript"></script>
        
       <script>
            $(function()) {
                //$("#projeto").ajaxGetJson("../../methods.php", {method: "carregaProjetos"}, null, "projeto");
                
                    $(".bt-image").on("click", function() {
                    var action = $(this).data("type");
                    var key = $(this).data("key");
                    var emp = $(this).parents("tr").find("td:first").next().html();
                    
                    if (action === "funcao") {
                        $("#prestador").val(key);
                        $("#form1").attr('action','ver_funcao.php');
                        $("#form1").submit();
                    }
            }
        </script>
    </head>
    <body class="novaintra">
        <div id="content">
            <form action="" method="post" name="form1" id="form1" enctype="multipart/form-data">
                <div id="head">
                    <img src="../../imagens/logomaster<?php echo $usuario['id_master'] ?>.gif" class="fleft" style="margin-right: 25px;">
                    <div class="fleftt">
                        <h3>Visualizar fun&ccedil&otildees<br>(UPAS)</h3>
                    </div>
                </div>
                <fieldset>
                    <legend>Filtro</legend>
                    <p><label class="first">Upa:</label> <?php echo montaSelect(GlobalClass::carregaUpas($usuario['id_master']), $projetoR, "id='projeto' name='projeto' class='required[custom[select]]'") ?></p>
                    <p class="controls"> <input type="submit" class="button" value="Filtrar" name="filtrar" /> <?php if ($filtro) { ?><!--<input type="submit" class="button" value="Novo Prestador" name="novo" id="novoPrest" />--><?php } ?></p>
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
                                    <th>CÓD</th>
                                    <th>FUNÇÃO</th>
                                    <th>CBO</th>
                                    <th>VALOR</th>
                                    <th>QUANTIDADE MÁXIMA</th>
                                    <th colspan="3">Açoes</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    foreach($funcoes as $row){
                                    ?>

                                        <tr class="<?php echo ($count++ % 2 == 0) ? "odd" : "even" ?>">
                                            <td><?php echo $row['id_curso']; ?></td>
                                            <td><?php echo $row['nome']; ?></td>
                                            <td><?php echo $row['cod']; ?></td>
                                            <td><?php echo 'R$ '.number_format($row['salario'],2, ',','.'); ?></td>
                                            <td><?php echo $row['qnt_maxima']; ?></td>
                                            <td class="center"><img src="../../imagens/icones/icon-doc.gif" title="Ver Funcao" class="bt-image" data-type="funcao" data-key="<?php echo $row['id_curso']; ?>" /></td>
                                            <td class="center"><img src="../../imagens/icones/icon-edit.gif" title="Editar" class="bt-image" data-type="editar" data-key="<?php echo $row['id_curso']; ?>" /></td>
                                            <td class="center"><img src="../../imagens/icones/icon-copy.png" title="Duplicar" class="bt-image" data-type="duplicar" data-key="<?php echo $row['id_curso']; ?>" /></td>
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
