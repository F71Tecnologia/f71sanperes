<?php
session_start();
if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../../conn.php');
include('../../classes/global.php');
include('../../wfunction.php');
include('../../classes/FuncoesClass.php');

$usuario = carregaUsuario();

if(isset($_REQUEST['id_departamento']) && !empty($_REQUEST['id_departamento']) && $_REQUEST['id_departamento'] != 0){
    $updateDepartamento = "UPDATE curso SET id_departamento = '{$_REQUEST['id_departamento']}' WHERE id_curso = '{$_REQUEST['id_curso']}' LIMIT 1;";
    $updateDepartamento = mysql_query($updateDepartamento);
    echo $_REQUEST['id_curso'].' - '.$_REQUEST['id_departamento'];
    exit;
}

$id_regiao = $_REQUEST['regiao'];
$id_projeto = $_REQUEST['projeto'];

$result = FuncoesClass::getCursos($id_regiao, $id_projeto);
$total_curso = mysql_num_rows($result);

$filtro = false;

if ((isset($_REQUEST['filtrar']) && !empty($_REQUEST['filtrar'])) || (isset($_SESSION['voltarCurso'])) || ($_REQUEST['atualizar'])) {
    $filtro = true;
    if(isset($_SESSION['voltarCurso'])){
        $_REQUEST['regiao'] = $_SESSION['voltarCurso']['id_regiao'];
        $_REQUEST['projeto'] = $_SESSION['voltarCurso']['id_projeto'];
        unset($_SESSION['voltarCurso']);
    }
    $result = FuncoesClass::getCursos($_REQUEST['regiao'], $_REQUEST['projeto']);
}

/* VARIAVEIS PARA MANTER OS CAMPOS DO FORMULÁRIO SELECIONADO */
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

/*$projetoR = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : null;
$regiaoR = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : null;*/

$sql_departamento = "SELECT * FROM departamentos ORDER BY nome";
$sql_departamento = mysql_query($sql_departamento);
$arrayDepartamentos[0] = 'Selecione';
while($row_departamento = mysql_fetch_assoc($sql_departamento)){
    $arrayDepartamentos[$row_departamento['id_departamento']] = $row_departamento['nome'];
}

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
                    var qtd = $(this).data("qtd");

                    if(action === "visualizar") {
                        $("#curso").val(key);
                        $("#form1").attr('action','detalhes_curso.php');
                        $("#form1").submit();
                    }else if(action === "editar"){
                        $("#curso").val(key);
                        $("#form1").attr('action','edit_curso.php');
                        $("#form1").submit();                        
                    }
                    else if(action === "excluir"){
                        
                        if(qtd != 0){
                            thickBoxAlert("Exclusão de Função", "Função não pode ser excluida, pois existe vínculo a mesma", 300, 130, null);
                        }else{
                            thickBoxConfirm("Exclusão de Função", "Você deseja realmente excluir esta função?", 300, 200, function(data){
                                if(data){                                       
                                    if(data == true){
                                        $("#"+key).remove();
                                        $.ajax({
                                            url:"del_curso.php?id="+key
                                        });
                                    }
                                }
                            });
                        }
                    }
                });
                
                $("#novoCurso").click(function(){
                    $("#form1").attr('action','form_curso.php');
                    $("#form1").submit();
                });
                
                var edit_projeto = $(hide_projeto).val();
                var edit_regiao = $(hide_regiao).val();                                                               
                
//                $("#projeto").each(function(){
//                    $("#filt").trigger("click");
//                });  

                $(".departamento").change(function(){
                    var text = $("option:selected", this).text();
                    var curso = $(this).data('curso');
                    var departamento = $(this).val();
                    
                    $.post("", {bugger:Math.random(), id_departamento:departamento, id_curso:curso}, function(resultado){
                        console.log(resultado);
                    });
                    
                    $(this).next().html(text);
                    $(this).hide();
                    $(this).next().show();
                });
                
                gridZebra('#tbRelatorio');
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
                        <h2>Administrativo - Funções</h2>
                        <p>Administração geral das Funções</p>
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
                    <input type="hidden" name="curso" id="curso" value="" />
                    <p><label class="first">Região:</label> <?php echo montaSelect(GlobalClass::carregaRegioes($usuario['id_master']), $regiaoR, "id='regiao' name='regiao' class='required[custom[select]]'") ?></p>
                    <p><label class="first">Projeto:</label> <?php echo montaSelect(array("-1"=>"« Selecione a Região »"), $projetoR, "id='projeto' name='projeto' class='required[custom[select]]'") ?></p>

                    <p class="controls"> <input type="submit" class="button" value="Filtrar" name="filtrar" id="filt" /> <?php if ($filtro) { ?><input type="submit" class="button" value="Nova Função" name="novo" id="novoCurso" /><?php } ?></p>
                </fieldset>

                <?php
                if ($filtro) {
                    if ($total_curso > 0) {
                        $count = 0;
                        ?>
                        <br/>
                        <p style="text-align: right; margin-top: 20px"><input type="button" onclick="tableToExcel('tbRelatorio', 'Relatório')" value="Exportar para Excel" class="exportarExcel"></p>
                        <table id="tbRelatorio" cellpadding="0" cellspacing="0" border="0" class="grid" width="100%">
                            <thead>
                                <tr>
                                    <th>Cód.</th>
                                    <th>Qtd. de Vínculos</th>
                                    <th>Função</th>
                                    <th>Departamento</th>
                                    <th>CBO</th>
                                    <th>Valor</th>
                                    <th>Qtd. Máxima</th>                                    
                                    <th colspan="3">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            $contratacao = "";
                            while ($row = mysql_fetch_assoc($result)) {
                                $departamento = '';
                                //trata qtd de vinculos
                                if($row['tipo'] == 2){
                                    $qtd_vinculos = FuncoesClass::getRhClt($row['id_curso']);
                                    if($row['id_departamento'] == 0){
                                        $departamento = montaSelect($arrayDepartamentos, $value, 'name="departamento" class="departamento" data-curso="'.$row['id_curso'].'"');
                                        $departamento .= '<span class="nomeDepartamento"></span>';
                                    } else {
                                        $departamento = $arrayDepartamentos[$row['id_departamento']];
                                    }
                                }elseif(($row['tipo'] == 1) || ($row['tipo'] == 3)){
                                    $qtd_vinculos = FuncoesClass::getAutonomo($row['id_curso']);
                                }
                                
                                if($contratacao != $row['tipo_contratacao_nome']){
                                    $contratacao = $row['tipo_contratacao_nome'];
                                    echo "<tr class='tr_contratacao'><td colspan='10' style='background: #F0F0F7'>".ucwords($row['tipo_contratacao_nome'])."</td><tr />";
                                }
                            ?>
                                    <tr style="margin: 0 0 50px 0;" id="<?php echo $row['id_curso']; ?>">
                                        <td><?php echo $row['id_curso']; ?></td>
                                        <td><?php echo $qtd_vinculos; ?></td>
                                        <td><?php echo $row['nome']; ?></td>
                                        <td><?php echo $departamento; ?></td>
                                        <td><?php echo $row['cod']; ?></td>
                                        <td><?php echo formataMoeda($row['salario']); ?></td>
                                        <td><?php echo $row['qnt_maxima']; ?></td>
                                        <td class="center"><img src="../../imagens/icones/icon-docview.gif" title="Visualizar" class="bt-image" data-type="visualizar" data-key="<?php echo $row['id_curso']; ?>" /></td>
                                        <td class="center"><img src="../../imagens/icones/icon-edit.gif" title="Editar" class="bt-image" data-type="editar" data-key="<?php echo $row['id_curso']; ?>" /></td>
                                        <td class="center"><img src="../../imagens/icones/icon-delete.gif" title="Desativar" class="bt-image" data-qtd="<?php echo $qtd_vinculos; ?>" data-type="excluir" data-key="<?php echo $row['id_curso']; ?>" /></td>
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