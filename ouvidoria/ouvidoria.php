<?php
include("../conn.php");
include("../wfunction.php");

$usuario = carregaUsuario();
$master = $usuario['id_master'];

?>
<!DOCTYPE html>
<html>
    <head>
        <title>:: Intranet :: RH - Transferência de Unidade</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../favicon.ico" />
        <link href="../net1.css" rel="stylesheet" type="text/css" />
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../favicon.ico" rel="shortcut icon" />
        <script src="../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine.js" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine-pt.js" type="text/javascript"></script>

        <script src="../js/global.js" type="text/javascript"></script>
    </head>
    <body>
        <div id="content">
            <form action="" method="post" name="form1" id="form1">
                <div id="head">
                    <img src="../imagens/logomaster<?php echo $master; ?>.gif" class="fleft" style="margin-right: 25px;"/>
                    <div class="fleft">
                        <h2>RH - Transferência de funcionário</h2>
                    </div>
                    <div class="fright"> <?php include('../reportar_erro.php'); ?></div> 
                </div>
                <br/>
                
                <fieldset class="border-red">
                    <legend>Informações Atuais do Funcionário</legend>
                    <p><label class="first">Nome:</label> <?php echo $clt['nome'] ?></p>
                    <p><label class="first">CPF:</label> <?php echo $clt['cpf'] ?></p>
                    <p><label class="first">Região:</label> <?php echo $clt['regiao'] ?></p>
                    <p><label class="first">Projeto:</label> <?php echo $clt['projeto'] ?></p>
                    <p><label class="first">Função:</label> <?php echo $clt['id_curso'] ." - ". $clt['funcao'] . " - R$ " . number_format($clt['salario'], 2, ",", ".") ?></p>
                    <p><label class="first">Horário:</label> <?php echo $clt['id_horario']." - ".$clt['horario'] . " ({$row['entrada1']} - {$row['saida_1']} - {$row['entrada_2']} - {$row['saida_2']})" ?></p>
                    <p><label class="first">Unidade:</label> <?php echo $clt['locacao'] ?></p>
                </fieldset>
    </body>
</html>
