<?php
if (empty($_COOKIE['logado'])) {
    print "Efetue o Login<br><a href='login.php'>Logar</a> ";
    exit;
}

require("../conn.php");
require("../wfunction.php");

$usuario = carregaUsuario();

//SELECIONANDO OS DADOS DO RELATÓRIO
$qr = "SELECT *,
IF(data_final1 < CURDATE(), 1, 2) AS tipo,
DATEDIFF(data_final1,CURDATE()) AS dias1,
DATEDIFF(data_final2,CURDATE()) AS dias2
FROM (
SELECT A.id_projeto,A.nome,B.nome AS funcao,DATE_FORMAT(A.data_entrada, '%d/%m/%Y') AS data_br,
        DATE_ADD(A.data_entrada,INTERVAL 44 DAY) AS data_final1,
        DATE_ADD(A.data_entrada,INTERVAL 89 DAY) AS data_final2,
        DATE_FORMAT(DATE_ADD(A.data_entrada,INTERVAL 44 DAY), '%d/%m/%Y') AS data_final1_br,
        DATE_FORMAT(DATE_ADD(A.data_entrada,INTERVAL 89 DAY), '%d/%m/%Y') AS data_final2_br,
        C.nome AS projeto
        FROM rh_clt AS A
        LEFT JOIN curso AS B ON (A.id_curso=B.id_curso)
        LEFT JOIN projeto AS C ON (A.id_projeto=C.id_projeto)
        WHERE C.id_master = {$usuario['id_master']} AND A.status = 10
        HAVING 
        data_final1 BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 45 DAY) OR 
        data_final2 BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 45 DAY)) AS tab ORDER BY tipo,data_final1,nome";
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
                    <h2>Todos os Projetos</h2>
                    <h3>Relatório de Funcionários com matrícula</h3>
                </div>
                <div class="fright"> <?php include('../reportar_erro.php'); ?></div> 
            </div>
            <br class="clear"/>
            <br/>
            <table width="96%" align="center" cellpadding="0" cellspacing="0" border="0" class="grid">
                <thead>
                    <tr>
                        <th>Projeto</th>
                        <th>Nome</th>
                        <th>Função</th>
                        <th>Data Entrada</th>
                        <th>Término 45 dias</th>
                        <th>Término 90 dias</th>
                        <th>OBS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysql_fetch_assoc($result)) { 
                        $obs="";
                        if($row['tipo']==1){
                            if($row['dias2']==0)
                                $obs = "<span style='color:red;'>VENCE HOJE</span> os 90 dias de experiencia";
                            else
                                $obs = "falta(m) {$row['dias2']} dia(s) para vencer os 90 dias de experiencia";
                        }else{
                            if($row['dias1']==0)
                                $obs = "<span style='color:red;'>VENCE HOJE</span> o periodo de experiencia de 45 dias";
                            else
                                $obs = "falta(m) {$row['dias1']} dia(s) para vencer o periodo de experiencia de 45 dias";
                        }
                    ?>
                        <tr class="<?php echo $count++ % 2 ? "even":"odd"?> ">
                            <td><?php echo $row['projeto'] ?></td>
                            <td><?php echo $row['nome'] ?></td>
                            <td><?php echo $row['funcao'] ?></td>
                            <td><?php echo $row['data_br'] ?></td>
                            <td><?php echo $row['data_final1_br'] ?></td>
                            <td><?php echo $row['data_final2_br'] ?></td>
                            <td><?php echo $obs ?></td>
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