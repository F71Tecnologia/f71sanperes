<?php
session_start();

if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../../conn.php');
include('../../classes/global.php');
include('../../wfunction.php');
include('../../classes/UnidadeClass.php');

$usuario = carregaUsuario();

$id_regiao = $_REQUEST['regiao'];
$id_projeto = $_REQUEST['projeto'];

$result = getUnidade($id_regiao, $id_projeto);
$total_unidade = mysql_num_rows($result);

$filtro = false;

if ((isset($_REQUEST['filtrar']) && !empty($_REQUEST['filtrar'])) || (isset($_SESSION['voltarCurso']))) {
    $filtro = true;
    if(isset($_SESSION['voltarCurso'])){
        $_REQUEST['regiao'] = $_SESSION['voltarCurso']['id_regiao'];
        $_REQUEST['projeto'] = $_SESSION['voltarCurso']['id_projeto'];
        unset($_SESSION['voltarCurso']);
    }
    $result = getUnidade($_REQUEST['regiao'], $_REQUEST['projeto']);
}

/* VARIAVEIS PARA MANTER OS CAMPOS DO FORMUL�RIO SELECIONADO */
if(isset($_REQUEST['projeto']) && isset($_REQUEST['regiao'])){
    $projetoR = $_REQUEST['projeto'];
    $regiaoR = $_REQUEST['regiao'];
}elseif(isset($_SESSION['projeto']) && isset($_SESSION['regiao'])){
    $projetoR = $_SESSION['projeto'];
    $regiaoR = $_SESSION['regiao'];
}elseif (isset($_SESSION['projeto_select']) && isset($_SESSION['regiao_select'])) {
    $projetoR = $_SESSION['projeto_select'];
    $regiaoR = $_SESSION['regiao_select'];
}

?>
<html>
    <head>
        <title>:: Intranet :: Administra��o de Unidades</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../../favicon.ico" />
        <link href="../../net1.css" rel="stylesheet" type="text/css" />
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="unidades.css" rel="stylesheet" type="text/css" />
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
                    var clt = $(this).data("clt");                     
                    
                    if(action === "visualizar") {
                        $("#unidade").val(key);
                        $("#form1").attr('action','detalhes_unidade.php');
                        $("#form1").submit();
                    }else if(action === "editar"){
                        $("#unidade").val(key);
                        $("#form1").attr('action','form_unidade.php');
                        $("#form1").submit();                        
                    }else if(action === "excluir"){                      
                        
                        if(clt != 0){
                            thickBoxAlert("Exclus�o de Unidade", "Unidade n�o pode ser excluida, pois existe CLT vinculada a mesma", 300, 130, null);
                        }else{
                            thickBoxConfirm("Exclus�o de Unidade", "Voc� deseja realmente excluir esta unidade?", 300, 200, function(data){
                                if(data){   
                                    
                                    if(data == true){
                                        $("#"+key).remove();
                                        $.ajax({
                                            url:"del_unidade.php?id="+key
                                        });
                                    }
                                }
                            });
                        }
                    }
                });
                
                $("#novaUnidade").click(function(){
                    $("#form1").attr('action','form_unidade.php');
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
                    <img src="../../imagens/logomaster<?php echo $usuario['id_master']; ?>.gif" class="fleft" style="margin-right: 25px;">
                    <div class="fleft">
                        <h2>Administrativo - Unidades</h2>
                        <p>Administra��o geral das Unidades</p>
                    </div>
                </div>
                
                <!--resposta de algum metodo realizado-->
                <div id="message-box" class="<?php echo $_SESSION['MESSAGE_COLOR']; ?> alinha2">
                    <?php echo $_SESSION['MESSAGE'];
                    session_destroy(); ?>
                </div>
                
                <fieldset>
                    <legend>Filtro</legend>
                    <input type="hidden" name="hide_projeto" id="hide_projeto" value="<?php echo $projetoR ?>" />
                    <input type="hidden" name="hide_regiao" id="hide_regiao" value="<?php echo $regiaoR ?>" />
                    <input type="hidden" name="unidade" id="unidade" value="" />
                    <p><label class="first">Regi�o:</label> <?php echo montaSelect(GlobalClass::carregaRegioes($usuario['id_master']), $regiaoR, "id='regiao' name='regiao' class='required[custom[select]]'") ?></p>
                    <p><label class="first">Projeto:</label> <?php echo montaSelect(array("-1"=>"� Selecione a Regi�o �"), $projetoR, "id='projeto' name='projeto' class='required[custom[select]]'") ?></p>

                    <p class="controls"> 
                        <input type="submit" class="button" value="Filtrar" name="filtrar" /> 
                        <?php if ($filtro) { ?>
                        <input type="submit" class="button" value="Nova Unidade" name="novo" id="novaUnidade" />
                        <?php } ?>
                    </p>
                </fieldset>
                
                <?php
                if ($filtro) {
                    if ($total_unidade > 0) {
                        $count = 0;
                        ?>
                        <br/>
                        <p id="excel" style="text-align: right; margin-top: 20px"><input type="button" onclick="tableToExcel('tbRelatorio', 'Relat�rio')" value="Exportar para Excel" class="exportarExcel"></p>
                        <table id="tbRelatorio" cellpadding="0" cellspacing="0" border="0" class="grid" width="100%">
                            <thead>
                                <tr>
                                    <th>C�d.</th>
                                    <th>Qtd. de V�nculos</th>
                                    <th>Unidade</th>
                                    <th>Telefone</th>
                                    <th>Endere�o</th>
                                    <th>Respons�vel</th>
                                    <th colspan="3">A��es</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            $contratacao = "";
                            while ($row = mysql_fetch_assoc($result)) {
                                
                                $clt = getRhClt($row['id_unidade']);
                                
                                if($contratacao != $row['tipo_contratacao_nome']){
                                    $contratacao = $row['tipo_contratacao_nome'];
                                    echo "<tr class='tr_contratacao'><td colspan='9'>".ucwords($row['tipo_contratacao_nome'])."</td><tr />";
                                }                                   
                            ?>
                                    <tr style="margin: 0 0 50px 0;" id="<?php echo $row['id_unidade']; ?>">
                                        <td><?php echo $row['id_unidade']; ?></td>
                                        <td><?php echo $clt; ?></td>
                                        <td><?php echo strtoupper($row['unidade']); ?></td>
                                        <td><?php echo $row['tel']; ?></td>
                                        <td><?php echo strtoupper($row['endereco']); ?></td>
                                        <td><?php echo strtoupper($row['responsavel']); ?></td>                                                                                
                                        <td class="center"><img src="../../imagens/icones/icon-docview.gif" title="Visualizar" class="bt-image" data-type="visualizar" data-key="<?php echo $row['id_unidade']; ?>" /></td>
                                        <td class="center"><img src="../../imagens/icones/icon-edit.gif" title="Editar" class="bt-image" data-type="editar" data-key="<?php echo $row['id_unidade']; ?>" /></td>                                        
                                        <td class="center"><img src="../../imagens/icones/icon-delete.gif" title="Excluir" class="bt-image" data-clt="<?php echo $clt; ?>" data-type="excluir" data-key="<?php echo $row['id_unidade']; ?>" /></td>
                                        <!--<td class="center"><img src="../../imagens/icones/icon-copy.png" title="Duplicar" class="bt-image" data-type="duplicar" data-key="<?php //echo $row['id_prestador']; ?>" /></td>-->
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