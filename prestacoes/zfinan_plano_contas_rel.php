<?php
if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="login.php">Logar</a>';
    exit;
}

include '../conn.php';
include '../wfunction.php';

$usuario = carregaUsuario();
$master = $usuario['id_master'];

$qrAssoc = "SELECT A.id_entradasaida, B.cod, B.nome AS nomeES, A.id_plano_contas, C.classificador, C.nome AS nomePC
FROM entradaesaida_plano_contas_assoc AS A
LEFT JOIN entradaesaida AS B ON (A.id_entradasaida = B.id_entradasaida)
LEFT JOIN plano_de_contas AS C ON (A.id_plano_contas = C.id_plano_contas)
ORDER BY A.id_entradasaida ,A.id_plano_contas ASC;";

$assoc = mysql_query($qrAssoc);
?>
<html>
    <head>
        <title>:: Intranet :: FINANCEIRO - RECURSOS HUMANOS</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../favicon.ico" />
        <link href="../net1.css" rel="stylesheet" type="text/css" />
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../favicon.ico" rel="shortcut icon" />
        <script src="../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script>
            $(function() {
                $("#voltar").click(function() {
                    window.history.go(-1);
                });
            });
        </script>
    </head>
    <body class="novaintra">
        <div id="content">
            <form action="" method="post" name="form1" id="form1">
                <div id="head">
                    <img src="../imagens/logomaster<?php echo $master; ?>.gif" class="fleft" style="margin-right: 25px;" />
                    <div class="fleft">
                        <h2>Relatório do Relacionamento de Plano de Contas</h2>
                    </div>
                </div>
                <br class="clear"/>
                <div class="esq">
                    <table id="tabela" border="0" cellpadding="0" cellspacing="0" width="100%" class="grid">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Descrição</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            while ($rowAssoc = mysql_fetch_assoc($assoc)){ 
                                if ($codESant != $rowAssoc['cod']) { ?>
                                    <tr class="subtitulo">
                                        <td><?php echo $rowAssoc['cod'] ?></td>
                                        <td><?php echo strtoupper($rowAssoc['nomeES']); ?></td>
                                    </tr>
                                    <?php
                                    $codESant = $rowAssoc['cod'];
                                }
                                ?>
                                <tr>
                                    <td><?php echo $rowAssoc['classificador'] ?></td>
                                    <td><?php echo strtoupper($rowAssoc['nomePC']); ?></td>
                                </tr>
                       <?php } ?>
                        </tbody>
                    </table> 
                </div>
                <br class="clear"/>
                <p class="controls"> 
                    <input type="button" class="button" value="Voltar" name="voltar" id="voltar" />
                </p>
            </form>
        </div>  
    </body>
</html>