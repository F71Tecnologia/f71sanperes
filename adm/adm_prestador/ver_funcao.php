<?php

if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../../conn.php');
include('../../classes/global.php');
include('../../wfunction.php');
include('../../classes/FuncoesClass.php');

$usuario = carregaUsuario();
$funcao = FuncoesClass::getFuncao($id_funcao);
#$projeto = montaQueryFirst("projeto", "*" , "id_projeto={$prestador['id_projeto']}");
#$regiao = montaQueryFirst("regioes", "*" , "id_regiao={$prestador['id_regiao']}");

?>

<html>
    <head>
        <title>:: Intranet :: Prestador de Serviço</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../../favicon.ico" />
        <link href="../../net1.css" rel="stylesheet" type="text/css" />
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="prestador.css" rel="stylesheet" type="text/css" />
        <link href='http://fonts.googleapis.com/css?family=Exo+2' rel='stylesheet' type='text/css'>
        <link href="../../favicon.ico" rel="shortcut icon" />
        <script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../../js/global.js" type="text/javascript"></script>
        

        <script>
            $(function() {

            });
        </script>
        <style>
            .colEsq{
                float: left;
                width: 50%;
                margin-top: -10px;
            }
            fieldset{
                margin-top: 10px;
            }
            fieldset legend{
                font-family: 'Exo 2', sans-serif;
                font-size: 16px!important;
                font-weight: bold;
            }
        </style>
    </head>
    <body class="novaintra">
        <div id="content" style="width:1000px">
            <form action="" method="post" name="form1" id="form1" enctype="multipart/form-data" >
                <div id="head">
                    <img src="../../imagens/logomaster<?php echo $usuario['id_master'] ?>.gif" class="fleft" style="margin-right: 25px;">
                    <div class="fleft">
                        <h2>Funcão</h2>
                    </div>
                </div>
                
                <fieldset>
                    <legend>Dados do Funcao</legend>
                    <p><label class='first'>Nome da Atividade:</label> <?php echo $funcao['nome']?></p>
                    <p><label class='first'>Nome do Curso:</label> <?php echo $funcao['nome']?></p>
                    <p><label class='first'>Área:</label> <?php echo $funcao['area']?></p>
                    <p><label class='first'>Local:</label> <?php echo $funcao['local']?></p>
                    <p><label class='first'>Quantidade máxima para contratação:</label> <?php echo $funcao['qnt_maxima']?></p>
                    <p><label class='first'>Valor:</label> <?php echo 'R$ '.format_number($funcao['salario'], 2, ',', '.') ?></p>
                    <p><label class='first'>Parcelas:</label> <?php echo $funcao['parcelas']?></p>
                    <p><label class='first'>Horas mensais:</label> <?php echo $funcao['hora_mes']?></p>
                    <p><label class='first'>Horas de Folgas:</label> <?php echo $funcao['hora_folga']?></p>
                    <p><label class='first'>Horas semanais:</label> <?php echo $funcao['hora_semana']?></p>
                    <p><label class='first'>Descrição:</label> <?php echo $funcao['descricao']?></p>
                </fieldset>
                 <p class="controls"> <input type="button" name="voltar" id="voltar" value="Voltar" onclick="window.location = 'index.php';" /> </p>
            </form>
        </div>
    </body>

