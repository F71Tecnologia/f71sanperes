<?php
if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../../conn.php');
include('../../classes/global.php');
include('../../wfunction.php');
include('../../classes/FolhaClass.php');

$usuario = carregaUsuario();

$id_regiao = $_REQUEST['regiao'];
$id_projeto = $_REQUEST['projeto'];

$result = Folha::getCursos($id_regiao, $id_projeto);
$total_curso = mysql_num_rows($result);

$filtro = false;

if ((isset($_REQUEST['filtrar']) && !empty($_REQUEST['filtrar']))) {
    $filtro = true;    
}

/* VARIAVEIS PARA MANTER OS CAMPOS DO FORMULÁRIO SELECIONADO */
$projetoR = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : null;
$regiaoR = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : null;

?>
<html>
    <head>
        <title>:: Intranet :: Administração de Funções</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../../favicon.ico" />
        <link href="../../net1.css" rel="stylesheet" type="text/css" />
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="cursos.css" rel="stylesheet" type="text/css" />
        <link href="../../favicon.ico" rel="shortcut icon" />
        <script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine-pt.js" type="text/javascript"></script>
        <script src="../../jquery/mascara/jquery.maskedinput-1.2.2.js" type="text/javascript" ></script>
        <script src="../../js/global.js" type="text/javascript"></script>
        
        <script>
            $(function() {
                $("#regiao").ajaxGetJson("../../methods.php", {method: "carregaProjetos"}, null, "projeto");

                $(".bt-image").on("click", function() {
                    var action = $(this).data("type");
                    var key = $(this).data("key");
                    var emp = $(this).parents("tr").find("td:first").next().html();
                    
                    //THICKBOX VISUALIZA DOCUMENTOS
                    if (action === "visualizar") {    
                        thickBoxIframe(emp, "detalhes_curso.php", {prestador: key, method: "getDocs"}, "625-not", "500");
                    }else if (action === "duplicar") {
                        $("#prestador").val(key);
                        $("#form1").attr('action','duplicar_prestador.php');
                        $("#form1").submit();
                    }else if (action === "prestador") {
                        $("#prestador").val(key);
                        $("#form1").attr('action','ver_prestador.php');
                        $("#form1").submit();
                    }else if (action === "editar") {
                        $("#prestador").val(key);
                        $("#form1").attr('action','form_prestador.php');
                        $("#form1").submit();
                    }
                });
                
                $("#novoCurso").click(function(){
                    $("#form1").attr('action','form_curso.php');
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
                    <img src="../../imagens/logomaster<?php echo $usuario['id_master'] ?>.gif" class="fleft" style="margin-right: 25px;">
                    <div class="fleft">
                        <h2>Administrativo - Funções</h2>
                        <p>Administração geral das Funções</p>
                    </div>
                </div>

                <fieldset>
                    <legend>Filtro</legend>
                    <input type="hidden" name="hide_projeto" id="hide_projeto" value="<?php echo $projetoR ?>" />
                    <input type="hidden" name="prestador" id="prestador" value="" />
                    <p><label class="first">Região:</label> <?php echo montaSelect(GlobalClass::carregaRegioes($usuario['id_master']), $regiaoR, "id='regiao' name='regiao' class='required[custom[select]]'") ?></p>
                    <p><label class="first">Projeto:</label> <?php echo montaSelect(array("-1"=>"« Selecione a Região »"), $projetoR, "id='projeto' name='projeto' class='required[custom[select]]'") ?></p>

                    <p class="controls"> <input type="submit" class="button" value="Filtrar" name="filtrar" /> <?php if ($filtro) { ?><input type="submit" class="button" value="Nova Função" name="novo" id="novoCurso" /><?php } ?></p>
                </fieldset>

                <?php
                if ($filtro) {
                    if ($total_curso > 0) {
                        $count = 0;
                        ?>
                        <br/>
                        <table cellpadding="0" cellspacing="0" border="0" class="grid" width="100%">
                            <thead>
                                <tr>
                                    <th>Cód.</th>
                                    <th>Função</th>
                                    <th>CBO</th>
                                    <th>Valor</th>
                                    <th>Qtd. Máxima</th>                                    
                                    <th colspan="3">Açoes</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            $contratacao = "";
                            while ($row = mysql_fetch_assoc($result)) {
                                if($contratacao != $row['tipo_contratacao_nome']){
                                    $contratacao = $row['tipo_contratacao_nome'];
                                    echo "<tr class='tr_contratacao'><td colspan='9'>".ucwords($row['tipo_contratacao_nome'])."</td><tr />";
                                }
                                   // echo $row['nome']."<br />";
                            ?>
                                    <tr style="margin: 0 0 50px 0;">
                                        <td><?php echo $row['id_curso']; ?></td>
                                        <td><?php echo $row['nome']; ?></td>
                                        <td><?php echo $row['cod']; ?></td>
                                        <td><?php echo $row['salario']; ?></td>
                                        <td><?php echo $row['qnt_maxima']; ?></td>                                                                                
                                        <td class="center"><img src="../../imagens/icones/icon-docview.gif" title="Visualizar" class="bt-image" data-type="visualizar" data-key="<?php echo $row['id_curso']; ?>" /></td>
                                        <td class="center"><img src="../../imagens/icones/icon-edit.gif" title="Editar" class="bt-image" data-type="editar" data-key="<?php //echo $row['id_prestador']; ?>" /></td>
                                        <td class="center"><img src="../../imagens/icones/icon-copy.png" title="Duplicar" class="bt-image" data-type="duplicar" data-key="<?php //echo $row['id_prestador']; ?>" /></td>
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
                } ?>
            </form>
        </div>
    </body>
</html>