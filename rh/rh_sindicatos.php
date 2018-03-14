<?php
if (empty($_COOKIE['logado'])) {
    print "Efetue o Login<br><a href='../login.php'>Logar</a> ";
    exit;
}
include('../conn.php');
include('../wfunction.php');

$usuario = carregaUsuario();

$id_user = $usuario['id_funcionario'];
$regiao = $usuario['id_regiao'];

$mes = date('m');

$meses = array('Erro', 'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro');

$query_master = mysql_query("SELECT master.id_master, master.razao FROM regioes 
                            INNER JOIN master 
                            ON regioes.id_master = master.id_master
                            WHERE regioes.id_regiao = '$regiao'") or die(mysql_error());

$row_master = mysql_fetch_assoc($query_master);
?>
<html>
    <head>
        <title>:: Intranet :: Prestador de Serviço</title>
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
                $(".bt-view").click(function() {
                    var id = $(this).attr('data-key');
                    $("#acao").val(3);
                    $("#sindicato").val(id);
                    $("#form1").submit();
                    return false;
                });
            });
        </script>
        <style>
            .data{width: 80px;}
            .colEsq{
                float: left;
                width: 55%;
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
            .first{
                vertical-align: 0!important;
            }
            .first-2{
                vertical-align: 0!important;
            }
        </style>
    </head>
    <body class="novaintra">
        <div id="content">
            <form action="rh_sindicatos_form.php" method="post" name="form1" id="form1">
                <div id="head">
                    <img src="../imagens/logomaster<?php echo $row_master['id_master'] ?>.gif" class="fleft" style="margin-right: 25px;">
                    <div class="fleft">
                        <h2>Sindicatos</h2>
                    </div>
                </div>

                <input type="hidden" name="acao" id="acao" value="1" />
                <input type="hidden" name="regiao" id="regiao" value="<?php echo $regiao ?>" />
                <input type="hidden" name="sindicato" id="sindicato" value="" />
                <p class="controls"> 
                    <input type="submit" name="cadastrar" id="cadastrar" value="Cadastrar Novo Sindicato">
                </p>
                <p style="text-align: left; margin-top: 20px"><input type="button" onclick="tableToExcel('tbRelatorio', 'Relatório')" value="Exportar para Excel" class="exportarExcel"></p>
                <table id="tbRelatorio" cellpadding="0" cellspacing="0" border="0" class="grid" style="width: 98%; margin: 10px;">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Mês de desconto</th>
                            <th>Mês de dissídio</th>
                            <th>Telefone</th>
                            <th>Contato</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $result = mysql_query("SELECT * FROM rhsindicato WHERE id_regiao = '$regiao' AND status = '1'");
                        while ($row = mysql_fetch_array($result)) {
                            if(strlen($row['nome']) > 40) {
                                $nome = substr($row['nome'],0,37).'...';
                            } else {
                                $nome = $row['nome'];
                            }
                            $mes_desconto = $meses[$row['mes_desconto']];
                            $mes_dissidio = $meses[$row['mes_dissidio']];
                            $class = ($linha++ % 2 == 0) ? "even" : "odd";
                            ?>
                            <tr class="<?php echo $class ?>">
                                <td><a class="bt-view" title='<?php echo $row['nome'] ?>' href='javasctipt:;' data-key="<?php echo $row['id_sindicato'] ?>"><?php echo $nome ?></a></td>
                                <td><?php echo $mes_desconto ?></td>
                                <td><?php echo $mes_dissidio ?></td>
                                <td><?php echo $row['tel'] ?></td>
                                <td><?php echo $row['contato'] ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </form>
        </div>
    </body>
</html>