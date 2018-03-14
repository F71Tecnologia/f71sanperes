<?php
session_start();

if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../../conn.php');
include('../../classes/global.php');
include('../../wfunction.php');
include('../../classes/SindicatoClass.php');

$usuario = carregaUsuario();
$id_regiao = $usuario['id_regiao'];

$result = getSindicato($id_regiao);
$total_sindicato = mysql_num_rows($result);

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
        <title>:: Intranet :: Administração de Sindicatos</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../../favicon.ico" />
        <link href="../../net1.css" rel="stylesheet" type="text/css" />
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="sindicato.css" rel="stylesheet" type="text/css" />
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
                    var qtd = $(this).data("qtd");
                    
                    if(action === "visualizar") {
                        $("#sindicato").val(key);
                        $("#form1").attr('action','detalhes_sindicato.php');
                        $("#form1").submit();
                        
                    }else if(action === "editar"){
                        $("#sindicato").val(key);
                        $("#form1").attr('action','form_sindicato.php');
                        $("#form1").submit();
                        
                    }else if(action === "excluir"){
                        
                        if(qtd != 0){
                            thickBoxAlert("Exclusão de Sindicato", "Sindicato não pode ser excluido, pois existe(m) vínculo(s)", 300, 130, null);
                        }else{
                            thickBoxConfirm("Exclusão de Função", "Você deseja realmente excluir este sindicato?", 300, 200, function(data){                                
                                if(data == true){                                        
                                    $.ajax({
                                        url:"del_sindicato.php",
                                        type:"POST",
                                        dataType:"json",
                                        data:{
                                            id:key,
                                            method:"excluir_sindicato"
                                        },
                                        success:function(data){
                                            $("#"+key).remove();
                                        }
                                    });
                                }                                
                            });
                        }
                    }
                });
                
                $("#novoSindicato").click(function(){
                    $("#form1").attr('action','form_sindicato.php');
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
                        <h2>Administrativo - Sindicatos</h2>
                        <p>Administração geral dos Sindicatos</p>
                    </div>
                </div>
                
                <!--resposta de algum metodo realizado-->
                <div id="message-box" class="<?php echo $_SESSION['MESSAGE_COLOR']; ?> alinha2">
                    <?php echo $_SESSION['MESSAGE'];
                    session_destroy(); ?>
                </div>
                
                <input type="hidden" id="sindicato" name="sindicato" value="" />
                
                <p class="controls">
                    <input type="submit" class="button" value="Novo Sindicato" name="novo" id="novoSindicato" />
                </p>
                
                <?php                
                if ($total_sindicato > 0) {
                    $count = 0;
                    ?>
                    <br/>
                    <table cellpadding="0" cellspacing="0" border="0" class="grid" width="100%">
                        <thead>
                            <tr>
                                <th>Cód.</th>
                                <th>Qtd. de Vínculos</th>
                                <th>Nome</th>
                                <th>Mês de desconto</th>
                                <th>Mês de dissídio</th>
                                <!--th>Telefone</th>
                                <th>Contato</th-->
                                <th colspan="3">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        while ($row = mysql_fetch_assoc($result)) {
                            $qtd_vinculos = getRhClt($row['id_sindicato']);
                        ?>
                            <tr style="margin: 0 0 50px 0;" id="<?php echo $row['id_sindicato']; ?>" class="<?php echo ($count++ % 2 == 0) ? "odd" : "even" ?>">
                                <td><?php echo $row['id_sindicato']; ?></td>
                                <td><?php echo $qtd_vinculos; ?></td>
                                <td><?php echo acentoMaiusculo($row['nome']); ?></td>
                                <td><?php echo mesesArray($row['mes_desconto']); ?></td>
                                <td><?php echo mesesArray($row['mes_dissidio']); ?></td>
                                <!--td><?php echo mascara_stringTel($row['tel']); ?></td>
                                <td><?php echo $row['contato']; ?></td-->
                                <td class="center"><img src="../../imagens/icones/icon-docview.gif" title="Visualizar" class="bt-image" data-type="visualizar" data-key="<?php echo $row['id_sindicato']; ?>" /></td>
                                <td class="center"><img src="../../imagens/icones/icon-edit.gif" title="Editar" class="bt-image" data-type="editar" data-key="<?php echo $row['id_sindicato']; ?>" /></td>
                                <td class="center"><img src="../../imagens/icones/icon-delete.gif" title="Excluir" class="bt-image" data-qtd="<?php echo $qtd_vinculos; ?>" data-type="excluir" data-key="<?php echo $row['id_sindicato']; ?>" /></td>
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
        </div>
    </body>
</html>