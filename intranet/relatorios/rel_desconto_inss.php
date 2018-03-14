<?php
if (empty($_COOKIE['logado'])) {
    print "Efetue o Login<br><a href='login.php'>Logar</a> ";
    exit;
}

include "../conn.php";

$projeto = $_REQUEST['pro'];

// PEGA O ID DO FUNCIONÁRIO LOGADO E SELECIONA OS DADOS DELE NA BASE DE DADOS
$id_user = $_COOKIE['logado'];
$result_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($result_user);

//FAZENDO UM SELECT NA TABELA MASTAR PARA PEGAR AS INFORMAÇÕES DA EMPRESA
$result_master = mysql_query("SELECT * FROM master WHERE id_master = '{$row_user['id_master']}'");
$row_master = mysql_fetch_array($result_master);

//SELECIONA O PROJETO PARA SER EXIBIDO NO RELATÓRIO
$result_proj = mysql_query("SELECT nome FROM projeto WHERE id_projeto = '{$projeto}'");
$nome_projeto = mysql_result($result_proj,0,0);

//SELECIONANDO OS DADOS DO RELATÓRIO
$result = mysql_query("SELECT 
                        A.id_clt,A.nome,A.desconto_inss,A.tipo_desconto_inss,A.valor_desconto_inss,
                        B.nome AS funcao,B.salario
                        FROM rh_clt AS A
                        LEFT JOIN curso AS B ON (B.id_curso=A.id_curso)
                        WHERE A.desconto_inss = '1' AND A.id_projeto = {$projeto}");

$total = mysql_num_rows($result);
$count = 0;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>Relatório de Funcionários com desconto de INSS</title>
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
                <img src="../imagens/logomaster<?php echo $row_master['id_master']; ?>.gif" class="fleft" style="margin-right: 25px;"/>
                <div class="fleft">
                    <h2><?php echo $nome_projeto; ?></h2>
                    <h3>Relatório de Funcionários com desconto de INSS</h3>
                </div>
                <div class="fright"> <?php include('../reportar_erro.php'); ?></div> 
            </div>
            <br class="clear"/>
            <br/>
            <table width="80%" align="center" cellpadding="0" cellspacing="0" border="0" class="grid">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Função</th>
                        <th>Salário</th>
                        <th>Tipo de Desconto</th>
                        <th>Quantidade</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysql_fetch_assoc($result)) { ?>
                        <tr class="<?php echo $count++ % 2 ? "even":"odd"?> ">
                            <td><?php echo $row['nome'] ?></td>
                            <td><?php echo $row['funcao'] ?></td>
                            <td class="txright">R$ <?php echo number_format($row['salario'],2,",",".") ?></td>
                            <td><?php echo $row['tipo_desconto_inss'] ?></td>
                            <td class="txright">R$ <?php echo number_format($row['valor_desconto_inss'],2,",",".") ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="txright"><strong>Total de funcionários:</strong></td>
                        <td><?php echo $total ?></td>
                    </tr>
                </tfoot>
            </table>

        </div>
    </body>
</html>