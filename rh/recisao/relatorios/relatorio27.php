<?php
if (empty($_COOKIE['logado'])) {
    print "Efetue o Login<br><a href='login.php'>Logar</a> ";
    exit;
}

require("../conn.php");
require("../wfunction.php");

$usuario = carregaUsuario();

$projeto = $_REQUEST['pro'];
//SELECIONA O PROJETO PARA SER EXIBIDO NO RELATÓRIO
$result_proj = mysql_query("SELECT nome FROM projeto WHERE id_projeto = '{$projeto}'");
$nome_projeto = mysql_result($result_proj,0,0);

//SELECIONANDO OS DADOS DO RELATÓRIO
$qr = "SELECT A.id_clt,A.nome,A.matricula,A.pis,A.data_entrada,A.cpf,
            B.nome AS funcao,B.salario,
			DATE_FORMAT(A.data_entrada, '%d/%m/%Y')as databr,
            C.horas_semanais
            FROM rh_clt AS A
            LEFT JOIN curso AS B ON (B.id_curso=A.id_curso)
            LEFT JOIN rh_horarios AS C ON (C.id_horario=A.rh_horario)
            WHERE A.id_projeto = {$projeto} ORDER BY matricula";
$result = mysql_query($qr);
echo "<!-- \r\n $qr \r\n-->";
$total = mysql_num_rows($result);
$count = 0;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>Relatório de Funcionários com matrícula</title>
        <link href="../net1.css" rel="stylesheet" type="text/css"/>
        <script type="text/javascript" src="../jquery/jquery-1.4.2.min.js"></script>

        <style>
            .grid thead tr th {font-size: 12px!important;}
            .bt-edit{cursor: pointer;}
        </style>
    </head>

    <body class="novaintra">
        <div id="content" style="width: 90%;">
            <div id="head">
                <img src="../imagens/logomaster<?php echo $usuario['id_master']; ?>.gif" class="fleft" style="margin-right: 25px;"/>
                <div class="fleft">
                    <h2><?php echo $nome_projeto; ?></h2>
                    <h3>Relatório de Funcionários com matrícula</h3>
                </div>
                <div class="fright"> <?php include('../reportar_erro.php'); ?></div> 
            </div>
            <br class="clear"/>
            <br/>
            <table width="90%" align="center" cellpadding="0" cellspacing="0" border="0" class="grid">
                <thead>
                    <tr>
                        <th>Matrícula</th>
                        <th>Nome</th>
                        <th>Função</th>
                        <th>PIS</th>
                        <th>Data de Admiss&atilde;o</th>
                        <th>CPF</th>
                        <th>Carga horária semanal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysql_fetch_assoc($result)) { ?>
                        <tr class="<?php echo $count++ % 2 ? "even":"odd"?> ">
                            <td><?php echo $row['matricula'] ?></td>
                            <td><?php echo $row['nome'] ?></td>
                            <td><?php echo $row['funcao'] ?></td>
                            <td class="txcenter"><?php echo $row['pis'] ?></td>
                            <td class="txcenter"><?php echo $row['databr'] ?></td>
                            <td class="txcenter"><?php echo $row['cpf'] ?></td>
                            <td><?php echo $row['horas_semanais'] ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="6" class="txright"><strong>Total de funcionários:</strong></td>
                        <td><?php echo $total ?></td>
                    </tr>
                </tfoot>
            </table>

        </div>
    </body>
</html>