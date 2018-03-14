<?php
session_start();

if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

if(date('md') <= 1121){
    //echo '<script>alert("Atualizar os feriados no sistema!")</script>';
    echo '<script>alert("'.date('md').' <= 1121")</script>';
}

include('../../conn.php');
include('../../classes/global.php');
include('../../wfunction.php');
include('../../classes/FeriadoClass.php');

$usuario = carregaUsuario();
$id_regiao = $usuario['id_regiao'];

$meses = mesesArray();
$optReg[0] = '« Selecione a Região »';
$sqlReg = getRegiao($user, $master);
while($rowReg = mysql_fetch_assoc($sqlReg)){
    $optReg[$rowReg['id_regiao']] = $rowReg['regiao'];
}

$optProj[0] = '« Selecione o Projeto »';
$sqlProj = mysql_query("SELECT id_projeto, id_regiao, nome FROM projeto WHERE status_reg = 1 ORDER BY nome");
while($rowProj = mysql_fetch_assoc($sqlProj)){
    $optProj[$rowProj['id_regiao']] = $rowProj['nome'];
}

$result = getFeriadoFiltrado($usuario['id_master'],$_POST['id_regiao'],$_POST['mes']);
$total_feriado = mysql_num_rows($result);
?>
<html>
    <head>
        <title>:: Intranet :: Administração de Feriados</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../../favicon.ico" />
        <link href="../../net1.css" rel="stylesheet" type="text/css" />
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="feriado.css" rel="stylesheet" type="text/css" />
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
                    
                    if(action === "editar"){
                        $("#feriado").val(key);
                        $("#form1").attr('action','form_feriado.php');
                        $("#form1").submit();
                        
                    }else if(action === "excluir"){
                        thickBoxConfirm("Exclusão de Feriado", "Você deseja realmente excluir este feriado?", 300, 200, function(data){
                            if(data){
                                if(data == true){
                                    $("#"+key).remove();
                                    $.ajax({
                                        url:"del_feriado.php?id="+key
                                    });
                                }
                            }
                        });                        
                    }
                });
                
                $("#novoFeriado").click(function(){
                    $("#form1").attr('action','form_feriado.php');
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
                        <h2>Administrativo - Feriados</h2>
                        <p>Administração geral dos Feriados</p>
                    </div>
                </div>
                
                <!--resposta de algum metodo realizado-->
                <div id="message-box" class="<?php echo $_SESSION['MESSAGE_COLOR']; ?> alinha2">
                    <?php echo $_SESSION['MESSAGE'];
                    session_destroy(); ?>
                </div>
                
                <input type="hidden" id="feriado" name="feriado" value="" />
                <fieldset>
                    <legend>Filtro</legend>
                    <input type="hidden" name="filtro" value="1">
                    <p><label class="first">Regiao:</label> <?php echo montaSelect($optReg, $_REQUEST['id_regiao'], 'name="id_regiao" '); ?></p>
                    <!--p><label class="first">Projeto:</label> <?php echo montaSelect($optProj, $_REQUEST['id_regiao'], 'name="id_regiao" '); ?></p-->
                    <p><label class="first">Mês:</label> <?php echo montaSelect($meses, $_REQUEST['mes'], 'name="mes" '); ?></p>
                    <p class="controls">
                        <input type="submit" value="Consultar" class="button" name="consultar">
                        <input type="submit" class="button" value="Novo Feriado" name="novo" id="novoFeriado" />
                    </p>
                </fieldset>
                <?php                
                if ($total_feriado > 0) {
                    $count = 0;
                    ?>
                    <br/>
                    <table cellpadding="0" cellspacing="0" border="0" class="grid" width="100%">
                        <thead>
                            <tr>
                                <th>Cód.</th>
                                <th>Data</th>
                                <th>Nome</th>
                                <th>Tipo</th>
                                <th>Móvel</th>
                                <th>Região</th>
                                <th colspan="2">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php while ($row = mysql_fetch_assoc($result)) { ?>
                            <tr style="margin: 0 0 50px 0;" id="<?php echo $row['id_feriado']; ?>" class="<?php echo ($count++ % 2 == 0) ? "odd" : "even" ?>">
                                <td><?php echo $row['id_feriado']; ?></td>
                                <td><?php echo $row['data_m']; ?></td>
                                <td><?php echo acentoMaiusculo($row['nome']); ?></td>
                                <td><?php echo $row['tipo']; ?></td>
                                <td><?php echo ($row['movel'] == 0) ? $movel = 'Não' : $movel = 'Sim'; ?></td>
                                <td><?php echo ($row['nome_regiao'] != '') ? $regiao_f = $row['nome_regiao'] : $regiao_f = 'Federal'; ?></td>                                
                                <td class="center"><img src="../../imagens/icones/icon-edit.gif" title="Editar" class="bt-image" data-type="editar" data-key="<?php echo $row['id_feriado']; ?>" /></td>
                                <td class="center"><img src="../../imagens/icones/icon-delete.gif" title="Excluir" class="bt-image" data-type="excluir" data-key="<?php echo $row['id_feriado']; ?>" /></td>
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