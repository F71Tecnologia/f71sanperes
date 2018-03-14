<?php
if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../conn.php');
include('../classes/global.php');
include('../wfunction.php');

$usuario = carregaUsuario();

$result = mysql_query("Select id_funcionario, nome, funcao, nome1 from funcionario where status_reg = '1' ORDER BY nome");
$num_rows = mysql_num_rows($result);

//SELECIONA TODOS OS FUNCIONARIOS ATIVOS
$arrayFunc = montaQuery("funcionario", "id_funcionario, nome","status_reg = 1", "nome ASC");
//MONTA ARRAY
$arrayFuncionario = array(" " => "« Selecione »");
foreach ($arrayFunc as $key => $value) {
    $arrayFuncionario[$value['id_funcionario']] = $value['id_funcionario'] . " - " . $value['nome'];  
}

if (isset($_REQUEST['copiar'])) {
    $funcionarioDe = $_REQUEST['funcionarioDe'];
    $funcionarioPara = $_REQUEST['funcionarioPara'];
    
    //CONSULTA TODAS AS PERMISSOES DO FUNCIONARIO DE:
    $func_reg_assocDe = montaQuery("funcionario_regiao_assoc", "id_regiao, id_master", "id_funcionario = $funcionarioDe");
    $btn_assocDe = montaQuery("botoes_assoc", "botoes_id", "id_funcionario = $funcionarioDe");
    $acoes_assocDe = montaQuery("funcionario_acoes_assoc", "acoes_id, id_regiao, botoes_id", "id_funcionario = $funcionarioDe");


    //CONSULTA TODAS AS PERMISSOES DO FUNCIONARIO PARA:
    $func_reg_assocPara = montaQuery("funcionario_regiao_assoc", "id_regiao, id_master", "id_funcionario = $funcionarioPara");
    $btn_assocPara = montaQuery("botoes_assoc", "botoes_id", "id_funcionario = $funcionarioPara");
    $acoes_assocPara = montaQuery("funcionario_acoes_assoc", "acoes_id", "id_funcionario = $funcionarioPara");
    
    //REMOVE TODAS AS PERMISSOES DO FUNCIONARIO PARA:
    if(!empty($func_reg_assocPara)){
        mysql_query("DELETE FROM funcionario_regiao_assoc WHERE id_funcionario = $funcionarioPara;"); 
    }

    if(!empty($btn_assocPara)){
       mysql_query("DELETE FROM botoes_assoc WHERE id_funcionario = $funcionarioPara;"); 
    }
    
    if(!empty($acoes_assocPara)){
        mysql_query("DELETE FROM funcionario_acoes_assoc WHERE id_funcionario = $funcionarioPara;"); 
    }
    
    //INSERE AS PERMISSOES DO FUNCIONARIO DE P/ O FUNCIONARIO PARA
    foreach ($func_reg_assocDe as $regioes) {
        mysql_query("INSERT INTO funcionario_regiao_assoc (id_funcionario, id_regiao, id_master) VALUES ( '$funcionarioPara', '{$regioes['id_regiao']}','{$regioes['id_master']}');");     
    }
    
    foreach ($btn_assocDe as $idBotao) {
        mysql_query("INSERT INTO botoes_assoc (botoes_id, id_funcionario) VALUES ('{$idBotao['botoes_id']}', '$funcionarioPara');");
    }
    
    foreach ($acoes_assocDe as $acoes) {
        mysql_query("INSERT INTO funcionario_acoes_assoc (id_funcionario, acoes_id, id_regiao, botoes_id ) VALUES('$funcionarioPara', '{$acoes['acoes_id']}','{$acoes['id_regiao']}','{$acoes['botoes_id']}');");
    }
    
}


?>
<html>
    <head>
        <title>:: Intranet :: Gestor de Funcionários</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../../favicon.ico" />
        <link href="../net1.css" rel="stylesheet" type="text/css" />
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../favicon.ico" rel="shortcut icon" />
        <script src="../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../js/global.js" type="text/javascript"></script>
        
        <script>
            $(function() {
                $("#regiao").ajaxGetJson("../../methods.php", {method: "carregaProjetos"}, null, "projeto");

                $(".bt-image").on("click", function() {
                    var action = $(this).data("type");
                    var key = $(this).data("key");
                    var emp = $(this).parents("tr").find("td:first").next().html();
                    
                    //THICKBOX VISUALIZA DOCUMENTOS
                    if (action === "alterarSenha") {
                        var confirma=confirm("Deseja alterar a senha?");
                        if (confirma===true) {
                            thickBoxIframe(emp, "alterar_senha.php", {funcionario: key},"300-not", "180");
                        } else {
                          return false;
                        }
                    }else if (action === "logs") {
                        $("#funcionario").val(key);
                        $("#form1").attr('action','ver_logs.php');
                        $("#form1").submit();        
                    }else if (action === "desativarUsuario") {
                        var confirma=confirm("Deseja desativar este usuário?");
                        if (confirma===true) {
                            thickBoxIframe(emp, "desativar_usuario.php", {funcionario: key},"300-not", "180");
                            $('#'+key).remove();
                        } else {
                          return false;
                        }
                    }else if (action === "permissoes") {
                        $("#funcionario").val(key);
                        $("#form1").attr('action','permissoes.php');
                        $("#form1").submit();    
                    }else if(action === "editarUsuario"){
                        $("#funcionario").val(key);
                        $("#form1").attr('action','form_usuario.php');
                        $("#form1").submit(); 
                    }else if (action === "copiarPermissoes") {
                        var funcionarioPara = key;
                        $("#funcionarioPara").val(funcionarioPara);  
                        thickBoxModal(emp, "#janela","200", "400"); // exibe o html da div oculta
                    }
                });
               
                $("#novoUsuario").click(function(){
                    $("#form1").attr('action','form_usuario.php');
                    $("#form1").submit();
                });
                
                 $("#listaUsuarioInativo").click(function(){
                   $("#form1").attr('action','listar_usuario_inativo.php');
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
        <div id="content" style="margin: auto;">
            <form action="" method="post" name="form1" id="form1" enctype="multipart/form-data" >
                <div id="head">
                    <img src="../imagens/logomaster<?php echo $usuario['id_master'] ?>.gif" class="fleft" style="margin-right: 25px;">
                    <div class="fleft">
                        <h2>Sistema - Gestor de Funcionários</h2>
                        <p>Controle Avançado de funcionários</p>
                    </div>
                </div>
                <p class="controls"><input type="button" class="button" value="Usuário Inativos" name="listaUsuarioInativo" id="listaUsuarioInativo"/><input type="submit" class="button" value="Novo Usuário" name="novo" id="novoUsuario" /></p>
                <p><input type="hidden" name="funcionario" id="funcionario" value="" /></p>

                <?php
                if ($num_rows > 0) {
                    $count = 0;
                    ?>
                    <br/>
                    <table cellpadding="0" cellspacing="0" border="0" class="grid" width="100%">
                        <thead>
                            <tr>
                                <th>COD.</th>
                                <th>NOME</th>
                                <th>FUNÇÃO</th>
                                <th>NOME NO SISTEMA</th>
                                <th>LOGS</th>
                                <th colspan="5">AÇÕES</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php while ($row = mysql_fetch_assoc($result)) { ?>
                                <tr class="<?php echo ($count++ % 2 == 0) ? "odd" : "even" ?>" id="<?php echo $row['id_funcionario']; ?>">
                                    <td><?php echo str_pad($row['id_funcionario'],3,"0",STR_PAD_LEFT); ?></td>
                                    <td><?php echo acentoMaiusculo($row['nome']); ?></td>
                                    <td><?php echo acentoMaiusculo($row['funcao']); ?></td>
                                    <td><?php echo acentoMaiusculo($row['nome1']); ?></td>
                                    <td class="center"><img src="../imagens/icones/icon-docview.gif" title="Ver Logs" class="bt-image" data-type="logs" data-key="<?php echo $row['id_funcionario']; ?>" /></td>
                                    <td class="center"><img src="../imagens/icones/icon-edit.gif" title="Editar Usuário" class="bt-image" data-type="editarUsuario" data-key="<?php echo $row['id_funcionario']; ?>" /></td>
                                    <td class="center"><img src="../imagens/icones/icon-relationship.gif" title="Permissões" class="bt-image" data-type="permissoes" data-key="<?php echo $row['id_funcionario']; ?>" /></td>
                                    <td class="center"><img src="../imagens/icones/icon-copy.png" title="Copiar Permissões" class="bt-image" data-type="copiarPermissoes" data-key="<?php echo $row['id_funcionario']; ?>" /></td>
                                    <td class="center"><img src="../imagens/icones/icon-unlock.gif" title="Alterar Senha" class="bt-image" data-type="alterarSenha" data-key="<?php echo $row['id_funcionario']; ?>" /></td>
                                    <td class="center"><img src="../imagens/icones/icon-trash.gif" title="Desativar Usuário" class="bt-image" data-type="desativarUsuario" data-key="<?php echo $row['id_funcionario']; ?>" /></td>
                                </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                    <?php } else { ?>
                    <br/>
                    <div id='message-box' class='message-yellow'>
                        <p>Nenhum registro encontrado</p>
                    </div>
                <?php } ?>
            </form>
            <div id="janela" style="display: none">
                <form action="" method="post" name="form2" id="form2" enctype="multipart/form-data" >
                    <h4>Copiar as Permissões</h4>
                    <p>
                        <label>DE:</label> <?php echo montaSelect($arrayFuncionario, null, "name='funcionarioDe' id='funcionarioDe' class='validate[required]' style='width: 340px;'");?> 
                        <input type="hidden" id="funcionarioPara" name="funcionarioPara" value=""/>
                    </p>
                    <p class="controls"> 
                        <input type="submit" name="copiar" value="Copiar" />
                    </p>
                </form>
            </div>
        </div>
    </body>
</html>