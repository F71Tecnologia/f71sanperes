<?php
session_start();

if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../../conn.php');
include('../../classes/global.php');
include('../../wfunction.php');
include('../../classes/EmpresaClass.php');

$usuario = carregaUsuario();

$id_regiao = $_REQUEST['regiao'];
$id_projeto = $_REQUEST['projeto'];

$result = getEmpresa($id_regiao);
$total_empresa = mysql_num_rows($result);

$filtro = false;

if ((isset($_REQUEST['filtrar']) && !empty($_REQUEST['filtrar'])) || (isset($_SESSION['voltarCurso']))) {
    $filtro = true;
    if(isset($_SESSION['voltarCurso'])){
        $_REQUEST['regiao'] = $_SESSION['voltarCurso']['id_regiao'];
        $_REQUEST['projeto'] = $_SESSION['voltarCurso']['id_projeto'];
        unset($_SESSION['voltarCurso']);
    }
    $result = getEmpresa($_REQUEST['regiao']);
}

/* VARIAVEIS PARA MANTER OS CAMPOS DO FORMULÁRIO SELECIONADO */
if(isset($_REQUEST['regiao'])){    
    $regiaoR = $_REQUEST['regiao'];
}elseif(isset($_SESSION['regiao'])){    
    $regiaoR = $_SESSION['regiao'];
}elseif(isset($_SESSION['regiao_select'])) {    
    $regiaoR = $_SESSION['regiao_select'];
}

?>
<html>
    <head>
        <title>:: Intranet :: Administração de Empresas</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../../favicon.ico" />
        <link href="../../net1.css" rel="stylesheet" type="text/css" />
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="empresa.css" rel="stylesheet" type="text/css" />
        <link href="../../favicon.ico" rel="shortcut icon" />
        <script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine-pt.js" type="text/javascript"></script>
        <script src="../../jquery/mascara/jquery.maskedinput-1.2.2.js" type="text/javascript" ></script>
        <script src="../../js/global.js" type="text/javascript"></script>
        
        <script>
            $(function() {
                $(".bt-image").on("click", function() {
                    var action = $(this).data("type");
                    var key = $(this).data("key");
                    var emp = $(this).parents("tr").find("td:first").next().html();
                    var proj = $(this).data("proj");
                    
                    if(action === "visualizar") {
                        $("#empresa").val(key);
                        $("#form1").attr('action','detalhes_empresa.php');
                        $("#form1").submit();
                    }else if(action === "editar"){
                        $("#empresa").val(key);
                        $("#form1").attr('action','form_empresa.php');
                        $("#form1").submit();
                    }else if(action === "excluir"){
                        
                        if(proj != 0){
                            thickBoxAlert("Exclusão de Empresa", "Empresa não pode ser excluida, pois existe Projeto vinculado a mesma", 300, 130, null);
                        }else{
                            thickBoxConfirm("Exclusão de Empresa", "Você deseja realmente excluir esta empresa?", 300, 200, function(data){
                                if(data){
                                    
                                    if(data == true){
                                        $("#"+key).remove();
                                        $.ajax({
                                            url:"del_empresa.php?id="+key
                                        });
                                    }
                                }
                            });
                        }
                    }
                });
                
                $("#novaEmpresa").click(function(){
                    $("#form1").attr('action','form_empresa.php');
                    $("#form1").submit();
                });
                
                //acao de clique ao voltar
//                var reg = $("#volta").val();  
//                var pausa = $("#pausa").val();
//                
//                if((reg != '') && (pausa == '')){
//                    $("#filt").click();
//                }
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
                        <h2>Administrativo - Empresas</h2>
                        <p>Administração geral das Empresas</p>
                    </div>
                </div>
                
                <!--resposta de algum metodo realizado-->
                <div id="message-box" class="<?php echo $_SESSION['MESSAGE_COLOR']; ?> alinha2">
                    <?php echo $_SESSION['MESSAGE'];
                    session_destroy(); ?>
                </div>
                
                <fieldset>
                    <legend>Filtro</legend>
                    <input type="hidden" name="pausa" id="pausa" value="<?php echo $_SESSION['pausa']; ?>" />
                    <input type="hidden" name="volta" id="volta" value="<?php echo $_SESSION['regiao_select']; ?>" />
                    <input type="hidden" name="hide_projeto" id="hide_projeto" value="<?php echo $projetoR ?>" />
                    <input type="hidden" name="hide_regiao" id="hide_regiao" value="<?php echo $regiaoR ?>" />
                    <input type="hidden" name="empresa" id="empresa" value="" />
                    <p><label class="first">Região:</label> <?php echo montaSelect(GlobalClass::carregaRegioes($usuario['id_master']), $regiaoR, "id='regiao' name='regiao' class='required[custom[select]]'") ?></p>                    

                    <p class="controls"> 
                        <input type="submit" class="button" value="Filtrar" name="filtrar" id="filt" /> 
                        <?php if ($filtro) { ?>
                        <input type="submit" class="button" value="Nova Empresa" name="novo" id="novaEmpresa" />
                        <?php } ?>
                    </p>
                </fieldset>
                
                <?php
                if ($filtro) {
                    if ($total_empresa > 0) {
                        $count = 0;
                        ?>
                        <br/>
                        <table cellpadding="0" cellspacing="0" border="0" class="grid" width="100%">
                            <thead>
                                <tr>
                                    <th>Cód.</th>                                    
                                    <th>Empresa</th>
                                    <th>CNPJ</th>
                                    <th>Responsável</th>
                                    <th colspan="3">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php                            
                            while ($row = mysql_fetch_assoc($result)) {                                
                                $vinc_projeto = getRhProjeto($row['id_projeto']);                                                                                                  
                            ?>
                                    <tr style="margin: 0 0 50px 0;" id="<?php echo $row['id_empresa']; ?>" class="<?php echo ($count++ % 2 == 0) ? "odd" : "even" ?>">
                                        <td><?php echo $row['id_empresa']; ?></td>
                                        <td><?php echo acentoMaiusculo($row['nome']); ?></td>
                                        <td><?php echo $row['cnpj']; ?></td>
                                        <td><?php echo strtoupper($row['responsavel']); ?></td>
                                        <td class="center"><img src="../../imagens/icones/icon-docview.gif" title="Visualizar" class="bt-image" data-type="visualizar" data-key="<?php echo $row['id_empresa']; ?>" /></td>
                                        <td class="center"><img src="../../imagens/icones/icon-edit.gif" title="Editar" class="bt-image" data-type="editar" data-key="<?php echo $row['id_empresa']; ?>" /></td>                                        
                                        <td class="center"><img src="../../imagens/icones/icon-delete.gif" title="Excluir" class="bt-image" data-proj="<?php echo $vinc_projeto; ?>" data-type="excluir" data-key="<?php echo $row['id_empresa']; ?>" /></td>                                        
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