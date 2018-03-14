<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../login.php?entre=true';</script>";
    exit;
}
error_reporting(E_ALL);

include('../conn.php');
include("../wfunction.php");

$lista = false;
$usuario = carregaUsuario();
$id_master = $usuario['id_master'];
$id_usuario = $usuario['id_funcionario'];

//VERIFICA INFORMAÇÃO DE POST
if (validate($_REQUEST['filtrar'])) {
    $lista = true;
    //FALTANDO INFORMAÇÕES DE CADASTRO
    $result_faltacad = mysql_query("SELECT * FROM prestadorservico  WHERE id_projeto = '{$_REQUEST['projeto']}' AND prestador_tipo=0 ORDER BY c_razao");
    $total_faltacad = mysql_num_rows($result_faltacad);
    
    //EMPRESAS DO PROJETO
    $result = mysql_query("SELECT * FROM prestadorservico WHERE id_projeto = '{$_REQUEST['projeto']}' ORDER BY c_razao");
    $total_emp = mysql_num_rows($result);
}

//SELECIONA O PROJETO
$rs_projeto = projetosPermissao($id_usuario, $id_master);
$projetos = array("-1" => "« Selecione »");
while ($row_projeto = mysql_fetch_assoc($rs_projeto)) {
    $projetos[$row_projeto['id_projeto']] = $row_projeto['id_projeto'] . " - " . $row_projeto['nome'];
}

//SETANDO VARIAVIES DE RETORNO DOS SELECTS
$projetoR = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : "-1";
?>
<html>
    <head>
        <title>Administrativo - Gestão de Prestador de Serviço</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link href="../net1.css" rel="stylesheet" type="text/css" />
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../uploadfy/css/uploadify.css" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" href="../jquery/thickbox/thickbox.css" type="text/css" media="screen" />

        <script src="../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>        
        <script src="../jquery/priceFormat.js" type="text/javascript"></script>
        <script src="../uploadfy/scripts/jquery.uploadify.v2.1.0.js" type="text/javascript"></script>
        <script src="../uploadfy/scripts/swfobject.js" type="text/javascript"></script>
        <script src="../jquery/mascara/jquery.maskedinput-1.2.2.js" type="text/javascript"></script>
        <script type="text/javascript" src="../jquery/thickbox/thickbox.js"></script>
        <script src="../js/global.js" type="text/javascript"></script>

        <script>
            $(function() {

            });
        </script>
    </head>
    <body class="novaintra">
        <form action="" method="post" name="form1">
            <div id="content" style="width: 80%;">
                <div id="head">
                    <img src="../imagens/logomaster<?php echo $usuario['id_master']; ?>.gif" class="fleft" style="margin-right: 25px;"/>
                    <div class="fleft">
                        <h2>Administrativo - Gestão de Prestador de Serviço</h2>
                        <p>Controle dos Prestadores de serviço</p>
                    </div>
                </div>
                <br class="clear">

                <br/>
                <fieldset>
                    <legend>Filtro</legend>
                    <p><label class="first">Projeto:</label> <?php echo montaSelect($projetos, $projetoR, "id='projeto' name='projeto'") ?></p>
                    <p class="controls" style="margin-top: 10px;"><input type="submit" name="filtrar" value="Filtrar" id="filtrar"/></p>
                </fieldset>
                <br/><br/>
                <?php if ($lista) { ?>
                
                    <!-- LISTAGEM DE EMPRESAS COM PROBLEMA NO CADASTRO -->
                    <?php if($total_faltacad > 0){ ?>
                    <h3>Empresas com problemas no cadastro</h3>
                    <table border="0" cellpadding="0" cellspacing="0" class="grid">
                        <thead>
                            <tr>
                                <th width="10%">N.</th>
                                <th width="15%">PROCESSO</th>
                                <th width="50%">RAZÃO SOCIAL</th>
                                <th width="10%">VALOR LIMITE</th>
                                <th width="10%">VALOR PAGO</th>
                                <th width="5%">COMPLETAR CADASTRO</th>
                            </tr>
                        </thead>
                        <?php
                        while ($row_faltacad = mysql_fetch_assoc($result_faltacad)) { ?>
                            <tr>
                                <td align='center'><?php echo $row_faltacad['id_prestador'] ?></td>
                                <td align='center'><?php echo $row_faltacad['numero'] ?></td>
                                <td><?php echo $row_faltacad['c_razao'] ?></td>
                                <td align='center'>N/D</td>
                                <td align='center'><?php echo $row_faltacad['valor'] ?></td>
                                <td align="center"><a href=''>COMPLETAR CADASTRO</a></td>
                            </tr>   
                        <?php  } ?>
                    </table>
                    <?php }else{ ?>
                        <div id="message-box" class="message-yellow">
                            <p>Nenhuma empresa com problemas no cadastro para o projeto selecionado</p>
                        </div>
                    <?php } ?>
                    <!-- FIM LISTAGEM DE EMPRESAS COM PROBLEMA NO CADASTRO -->
                    
                    <!-- LISTAGEM DAS EMPRESAS -->
                    <?php if($total_emp > 0){ ?>
                    <h3>Empresas cadastradas</h3>
                    <table border="0" cellpadding="0" cellspacing="0" class="grid">
                        <thead>
                            <tr>
                                <th width="10%">N.</th>
                                <th width="15%">PROCESSO</th>
                                <th width="50%">RAZÃO SOCIAL</th>
                                <th width="10%">VALOR LIMITE</th>
                                <th width="10%">VALOR PAGO</th>
                                <th width="5%">COMPLETAR CADASTRO</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        while ($row = mysql_fetch_assoc($result)) { ?>
                            <tr>
                                <td align='center'><?php echo $row['id_prestador'] ?></td>
                                <td align='center'><?php echo $row['numero'] ?></td>
                                <td><?php echo $row['c_razao'] ?></td>
                                <td align='center'>N/D</td>
                                <td align='center'><?php echo $row['valor'] ?></td>
                                <td align="center"><a href="javascript:;" class='bt-image' data-tp='editar'> <img src='../imagens/icon-edit.gif' alt='Editar' title='Editar' /></td>
                            </tr>   
                        <?php  } ?>
                        </tbody>
                    </table>
                    <?php }else{ ?>
                        <div id="message-box" class="message-yellow">
                            <p>Nenhuma empresa cadastro para o projeto selecionado</p>
                        </div>
                    <?php } ?>
                    <!-- FIM LISTAGEM DAS EMPRESAS -->
                <?php } ?>
            </div>
        </form>
    </body>
    <di  style="background-color: #c8ebf9 ">

    </di>
</html>