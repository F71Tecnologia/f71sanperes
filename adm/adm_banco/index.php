<?php
session_start();

if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../../conn.php');
include('../../classes/global.php');
include('../../wfunction.php');
include('../../classes/BancoClass.php');

$usuario = carregaUsuario();

$id_regiao = $_REQUEST['regiao'];

$result = getBanco($id_regiao);
$total_banco = mysql_num_rows($result);

$filtro = false;

if ((isset($_REQUEST['filtrar']) && !empty($_REQUEST['filtrar']))) {
    $filtro = true;    
    $result = getBanco($_REQUEST['regiao']);
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
        <title>:: Intranet :: Administração de Bancos</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../../favicon.ico" />
        <link href="../../net1.css" rel="stylesheet" type="text/css" />
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="bancos.css" rel="stylesheet" type="text/css" />
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
                    var clt = $(this).data("clt");
                    
                    if(action === "visualizar") {
                        $("#banco").val(key);
                        $("#form1").attr('action','detalhes_banco.php');
                        $("#form1").submit();
                    }else if(action === "editar"){
                        $("#banco").val(key);
                        $("#form1").attr('action','form_banco.php');
                        $("#form1").submit();
                    }
                });
                
                $("#novoBanco").click(function(){
                    $("#form1").attr('action','form_banco.php');
                    $("#form1").submit();
                });
                
                //acao de clique ao voltar
                var reg = $("#volta").val();  
                var pausa = $("#pausa").val();
                
                if((reg != '') && (pausa == '')){
                    $("#filt").click();
                }
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
            <form action="" method="post" name="form1" id="form1" enctype="multipart/form-data">
                <div id="head">
                    <img src="../../imagens/logomaster<?php echo $usuario['id_master']; ?>.gif" class="fleft" style="margin-right: 25px;">
                    <div class="fleft">
                        <h2>Administrativo - Bancos</h2>
                        <p>Administração geral dos Bancos</p>
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
                    <input type="hidden" name="hide_regiao" id="hide_regiao" value="<?php echo $regiaoR; ?>" />
                    <input type="hidden" name="banco" id="banco" value="" />
                    <p><label class="first">Região:</label> <?php echo montaSelect(GlobalClass::carregaRegioes($usuario['id_master']), $regiaoR, "id='regiao' name='regiao' class='required[custom[select]]'") ?></p>                    
                    
                    <p class="controls">
                        <input type="submit" class="button" id="filt" value="Filtrar" name="filtrar" />
                        <?php if ($filtro) { ?>
                        <input type="submit" class="button" value="Novo Banco" name="novo" id="novoBanco" />
                        <?php } ?>
                    </p>
                </fieldset>
                
                <?php
                if ($filtro) {
                    if ($total_banco > 0) {
                        $count = 0;
                        ?>
                        <br/>
                        <p style="text-align: right; margin-top: 20px"><input type="button" onclick="tableToExcel('tbRelatorio', 'Relatório')" value="Exportar para Excel" class="exportarExcel"></p>
                        <table id="tbRelatorio" cellpadding="0" cellspacing="0" border="0" class="grid" width="100%">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Cód.</th>
                                    <th>Banco</th>
                                    <th>Agência</th>
                                    <th>Conta</th>
                                    <th>Endereço</th>
                                    <th>Telefone</th>
                                    <th>Gerente</th>
                                    <th colspan="2">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                            
                            <?php    
                            $sel_projeto = getProjetosRegiao($id_regiao);
                            
                            while($row_proj = mysql_fetch_assoc($sel_projeto)){
                            
                            $result_proj = getBancoProj($id_regiao, $row_proj['id_projeto']);
                            
                            $projeto = "";
                            while ($row = mysql_fetch_assoc($result_proj)) {
                                
                                if($projeto != $row_proj['nome']){
                                    $projeto = $row_proj['nome'];
                                    echo "<tr class='tr_contratacao'><td style='background: #F0F0F7' colspan='10'>".ucwords($row_proj['nome'])."</td><tr />";
                                }
                            ?>
                                    <tr style="margin: 0 0 50px 0;" id="<?php echo $row['id_banco']; ?>">
                                        <td class="td_img"><img src='../../imagens/bancos/<?php echo $row['id_nacional']; ?>.jpg' width='25' height='25' align='absmiddle'></td>
                                        <td><?php echo acentoMaiusculo($row['id_banco']); ?></td>
                                        <td><?php echo acentoMaiusculo($row['nome']); ?></td>
                                        <td><?php echo $row['agencia']; ?></td>
                                        <td><?php echo $row['conta']; ?></td>
                                        <td><?php echo acentoMaiusculo($row['endereco']); ?></td>
                                        <td><?php echo $row['tel']; ?></td>
                                        <td><?php echo acentoMaiusculo($row['gerente']); ?></td>
                                        <td class="center"><img src="../../imagens/icones/icon-docview.gif" title="Visualizar" class="bt-image" data-type="visualizar" data-key="<?php echo $row['id_banco']; ?>" /></td>
                                        <td class="center"><img src="../../imagens/icones/icon-edit.gif" title="Editar" class="bt-image" data-type="editar" data-key="<?php echo $row['id_banco']; ?>" /></td>                                                                                
                                    </tr>
                            <?php }} ?>
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