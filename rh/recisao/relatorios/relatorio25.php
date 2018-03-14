<?php
if (empty($_COOKIE['logado'])) {
    print "Efetue o Login<br><a href='login.php'>Logar</a> ";
    exit;
}

include "../conn.php";

// PEGA O ID DO FUNCIONÁRIO LOGADO E SELECIONA OS DADOS DELE NA BASE DE DADOS
$id_user = $_COOKIE['logado'];
$result_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($result_user);

//FAZENDO UM SELECT NA TABELA MASTAR PARA PEGAR AS INFORMAÇÕES DA EMPRESA
$result_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_user[id_master]'");
$row_master = mysql_fetch_array($result_master);

//SELECIONANDO OS DADOS DO PROJETO
$result = mysql_query("SELECT 
                        A.id_clt,A.nome,C.nome as funcao,C.salario,A.`status`,B.nome as lotacao,
                        DATE_FORMAT(A.data_entrada, '%d/%m/%Y') as admissao, IF(A.data_saida!='0000-00-00',DATE_FORMAT(A.data_saida, '%d/%m/%Y'),'-') as demissao,
                        D.especifica
                        FROM rh_clt AS A 
                        LEFT JOIN projeto AS B ON (A.id_projeto=B.id_projeto)
                        LEFT JOIN curso AS C ON (A.id_curso=C.id_curso)
                        LEFT JOIN rhstatus AS D ON (A.`status`=D.codigo)
                        WHERE B.id_master = {$row_user['id_master']}
                        ORDER BY A.nome");

$total = mysql_num_rows($result);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>Relatório Geral de Funcionários</title>
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
                    <h2>Relatório Geral de Funcionários</h2>
                </div>
                <div class="fright"> <?php include('../reportar_erro.php'); ?></div> 
            </div>
            <br class="clear"/>
            <br/>
            <table cellpadding="0" cellspacing="0" border="0" class="grid">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Função</th>
                        <th>Salário</th>
                        <th>Admissão</th>
                        <th>Rescisão</th>
                        <th>Status</th>
                        <th>Lotação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysql_fetch_assoc($result)) { ?>
                        <tr>
                            <td><?php echo $row['nome'] ?></td>
                            <td><?php echo $row['funcao'] ?></td>
                            <td><?php echo number_format($row['salario'],2,",",".") ?></td>
                            <td><?php echo $row['admissao'] ?></td>
                            <td><?php echo $row['demissao'] ?></td>
                            <td><?php echo $row['especifica'] ?></td>
                            <td><?php echo $row['lotacao'] ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="6" class="txright"><strong>Total:</strong></td>
                        <td><?php echo $total ?></td>
                    </tr>
                </tfoot>
            </table>

        </div>
    </body>
</html>