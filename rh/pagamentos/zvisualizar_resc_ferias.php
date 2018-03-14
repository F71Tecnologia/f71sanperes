<?php
include('../../conn.php');
include("../../wfunction.php");
include("../../funcoes.php");
include '../../classes_permissoes/acoes.class.php';


$mes = $_GET['mes'];
$ano = $_GET['ano'];
$clt = $_GET['id_clt'];
$tipo_guia = $_GET['tipo']; // 1 - FÉRIAS, 2 - RECISÂO, 3- MULTA FGTS, 4 - RESCISÃO COMPLEMENTAR, 5 - MULTA FGTS COMPLEMENTAR
$id_rescisao = $_GET['id_rescisao'];
$projeto = $_GET['projeto'];
$regiao = $_GET['regiao'];
$ferias = $_GET['ferias'];

$usuario = carregaUsuario();
$acoes = new Acoes();

$qr_clt = mysql_query("SELECT CONCAT(A.id_clt,' - ',A.nome) as clt, B.regiao as nome_regiao, C.nome as nome_projeto
                        FROM rh_clt as A
                       INNER JOIN regioes as B
                       ON B.id_regiao = A.id_regiao
                       INNER JOIN projeto as C
                       ON C.id_projeto = A.id_projeto
                      WHERE A.id_clt = '$clt'");

$row_clt = mysql_fetch_assoc($qr_clt);

// Montando o nome da saida
if ($tipo_guia == 1) {

    $tipo_id = "156";
    $tipo_nome = "FÉRIAS";
    $subgrupo = 1;
    $qr_saida = mysql_query("SELECT B.id_banco, B.id_saida, B.status, B.nome as descricao, B.especifica, B.valor,
                                DATE_FORMAT(B.data_proc, '%d/%m/%Y') as processado,
                                DATE_FORMAT(B.data_vencimento, '%d/%m/%Y') as data_vencimento,
                                C.nome as enviado_por,
                                D.nome as pago_por, F.nome AS nomeBanco, 
                                (SELECT tipo_saida_file FROM saida_files WHERE id_saida = B.id_saida LIMIT 1) as anexo,
                                (SELECT tipo_pg FROM saida_files_pg WHERE id_saida = B.id_saida LIMIT 1) as comprovante                                 
                                FROM pagamentos_especifico as A 
                                INNER JOIN saida as B
                                ON B.id_saida = A.id_saida
                                LEFT JOIN funcionario as C 
                                ON C.id_funcionario = B.id_user
                                LEFT JOIN funcionario as D 
                                ON D.id_funcionario = B.id_userpg
                                LEFT JOIN bancos AS F 
                                ON B.id_banco = F.id_banco
                                WHERE A.id_clt = '$clt' AND (B.tipo = 76 OR B.tipo = 156) AND A.ano = $ano;");
} elseif ($tipo_guia == 2) {

    $tipo_nome = "RESCISÃO";
    $qr_saida = mysql_query("SELECT B.id_banco, PG.id_saida,B.nome, C.id_banco as id_banco, B.estorno,B.estorno_obs,
                                    DATE_FORMAT(B.data_proc, '%d/%m/%Y')  as processado,
                                    D.nome as enviado_por,
                                    DATE_FORMAT(B.data_vencimento, '%d/%m/%Y')  as data_vencimento,
                                    IF(B.data_pg = NULL,'',DATE_FORMAT(B.data_pg, '%d/%m/%Y' )) as data_pg,
                                    B.valor,
                                    B.nome as descricao,
                                    B.especifica, F.nome AS nomeBanco, 
                                    (SELECT tipo_saida_file FROM saida_files WHERE id_saida = B.id_saida LIMIT 1) as anexo,
                                    (SELECT tipo_pg FROM saida_files_pg WHERE id_saida = B.id_saida LIMIT 1) as comprovante,
                                    E.nome as pago_por,
                                    C.nome as nome_banco, C.conta, C.agencia,B.status
                                    FROM pagamentos_especifico AS PG
                                    INNER JOIN saida as B 
                                    ON PG.id_saida = B.id_saida
                                    INNER JOIN bancos as C
                                    ON C.id_banco = B.id_banco
                                    LEFT JOIN funcionario as D
                                    ON(D.id_funcionario = B.id_user)    
                                    LEFT JOIN funcionario as E
                                    ON (E.id_funcionario  = B.id_userpg)
                                    LEFT JOIN bancos AS F 
                                    ON B.id_banco = F.id_banco
                                    WHERE B.status != '0' AND PG.mes = '$mes' AND PG.ano = '$ano' AND PG.id_clt = '$clt' AND (B.tipo = '51' OR B.tipo = '170') AND PG.id_rescisao = '$id_rescisao';
                                    ");
} elseif ($tipo_guia == 3) {
    $tipo_nome = "MULTA FGTS";
    $qr_saida = mysql_query("SELECT  B.id_banco, E.nome as nome_banco, E.id_banco as id_banco, DATE_FORMAT(B.data_proc, '%d/%m/%Y')  as processado,
                            DATE_FORMAT(B.data_vencimento, '%d/%m/%Y')  as data_vencimento,
                            B.valor,
                            B.nome as descricao,
                            B.especifica,B.id_saida, F.nome AS nomeBanco, 
                            (SELECT tipo_saida_file FROM saida_files WHERE id_saida = B.id_saida LIMIT 1) as anexo,
                            (SELECT tipo_pg FROM saida_files_pg WHERE id_saida = B.id_saida LIMIT 1) as comprovante,
                            C.nome as enviado_por,
                            D.nome as pago_por,
                            B.status
                            FROM saida_files as A
                            INNER JOIN saida as B
                            ON A.id_saida = B.id_saida
                            LEFT JOIN funcionario as C
                            ON C.id_funcionario = B.id_user
                            LEFT JOIN funcionario as D
                            ON D.id_funcionario = B.id_userpg
                            INNER JOIN bancos AS E 
                            ON B.id_banco = E.id_banco
                            LEFT JOIN bancos AS F 
                            ON B.id_banco = F.id_banco
                            WHERE B.id_clt = '$clt' AND B.tipo IN(167,170) AND A.multa_rescisao = 1");
    
} elseif ($tipo_guia == 4) {

    $tipo_nome = "RESCISÃO COMPLEMENTAR";

//        $teste = "SELECT  B.id_banco, E.nome as nome_banco, E.id_banco as id_banco, DATE_FORMAT(B.data_proc, '%d/%m/%Y')  as processado,
//                            DATE_FORMAT(B.data_vencimento, '%d/%m/%Y')  as data_vencimento,
//                            B.valor,
//                            B.nome as descricao,
//                            B.especifica,B.id_saida, F.nome AS nomeBanco, 
//                            (SELECT tipo_saida_file FROM saida_files WHERE id_saida = B.id_saida LIMIT 1) as anexo,
//                            (SELECT tipo_pg FROM saida_files_pg WHERE id_saida = B.id_saida LIMIT 1) as comprovante,
//                            C.nome as enviado_por,
//                            D.nome as pago_por,
//                            B.status
//                            FROM saida_files as A
//                            INNER JOIN saida as B
//                            ON A.id_saida = B.id_saida
//                            LEFT JOIN funcionario as C
//                            ON C.id_funcionario = B.id_user
//                            LEFT JOIN funcionario as D
//                            ON D.id_funcionario = B.id_userpg
//                            INNER JOIN bancos AS E 
//                            ON B.id_banco = E.id_banco
//                            LEFT JOIN bancos AS F 
//                            ON B.id_banco = F.id_banco
//                            WHERE B.id_clt = '$clt' AND B.tipo IN(167,170) AND A.rescisao_complementar = 1";
//        echo $teste;
//        exit;
    $qr_saida = mysql_query("SELECT  B.id_banco, E.nome as nome_banco, E.id_banco as id_banco, DATE_FORMAT(B.data_proc, '%d/%m/%Y')  as processado,
                            DATE_FORMAT(B.data_vencimento, '%d/%m/%Y')  as data_vencimento,
                            B.valor,
                            B.nome as descricao,
                            B.especifica,B.id_saida, F.nome AS nomeBanco, 
                            (SELECT tipo_saida_file FROM saida_files WHERE id_saida = B.id_saida LIMIT 1) as anexo,
                            (SELECT tipo_pg FROM saida_files_pg WHERE id_saida = B.id_saida LIMIT 1) as comprovante,
                            C.nome as enviado_por,
                            D.nome as pago_por,
                            B.status
                            FROM saida_files as A
                            INNER JOIN saida as B
                            ON A.id_saida = B.id_saida
                            LEFT JOIN funcionario as C
                            ON C.id_funcionario = B.id_user
                            LEFT JOIN funcionario as D
                            ON D.id_funcionario = B.id_userpg
                            INNER JOIN bancos AS E 
                            ON B.id_banco = E.id_banco
                            LEFT JOIN bancos AS F 
                            ON B.id_banco = F.id_banco
                            LEFT JOIN pagamentos_especifico AS G ON (B.id_saida = G.id_saida)
                            WHERE B.id_clt = '$clt' AND B.tipo IN(167,170) AND A.rescisao_complementar = 1 AND G.id_rescisao = '$id_rescisao' AND A.multa_rescisao <> 2");
    
}elseif ($tipo_guia == 5) {

    $tipo_nome = "MULTA FGTS COMPLEMENTAR";
    $qr_saida = mysql_query("SELECT  B.id_banco, E.nome as nome_banco, E.id_banco as id_banco, DATE_FORMAT(B.data_proc, '%d/%m/%Y')  as processado,
                            DATE_FORMAT(B.data_vencimento, '%d/%m/%Y')  as data_vencimento,
                            B.valor,
                            B.nome as descricao,
                            B.especifica,B.id_saida, F.nome AS nomeBanco, 
                            (SELECT tipo_saida_file FROM saida_files WHERE id_saida = B.id_saida LIMIT 1) as anexo,
                            (SELECT tipo_pg FROM saida_files_pg WHERE id_saida = B.id_saida LIMIT 1) as comprovante,
                            C.nome as enviado_por,
                            D.nome as pago_por,
                            B.status
                            FROM saida_files as A
                            INNER JOIN saida as B
                            ON A.id_saida = B.id_saida
                            LEFT JOIN funcionario as C
                            ON C.id_funcionario = B.id_user
                            LEFT JOIN funcionario as D
                            ON D.id_funcionario = B.id_userpg
                            INNER JOIN bancos AS E 
                            ON B.id_banco = E.id_banco
                            LEFT JOIN bancos AS F 
                            ON B.id_banco = F.id_banco
                            LEFT JOIN pagamentos_especifico AS G ON (B.id_saida = G.id_saida)
                            WHERE B.id_clt = '$clt' AND B.tipo IN(167,170) AND A.rescisao_complementar = 0 AND G.id_rescisao = '$id_rescisao' AND A.multa_rescisao = 2 GROUP BY A.id_saida");
    
}

if(isset($_REQUEST['excluir'])){
    $id_saida = $_REQUEST['id_saida'];
    $tp_guia = $_REQUEST['tipo_guia'];
    $id_clt = $_REQUEST['id_clt'];
    $mes_consulta = $_REQUEST['mes_consulta'];
    $ano_consulta = $_REQUEST['ano_consulta'];
    $id_usuario = $_REQUEST['id_usuario'];
    $qr_saida = "SELECT status FROM saida WHERE id_saida = $id_saida;";
    $result = mysql_query($qr_saida);
    $row_saida = mysql_fetch_assoc($result);
    if($row_saida['status']==1){
        switch ($tp_guia) {
            case 1: // FÉRIAS
                $qr_saida = mysql_query("UPDATE saida SET status = 0 WHERE id_saida = $id_saida AND status = 1 LIMIT 1;") or die ("Erro ao desprocessar a saida.");
                $qr_pgEspecifico = mysql_query("DELETE FROM pagamentos_especifico WHERE id_saida = $id_saida LIMIT 1;") or die ("Erro ao excluir. (pagamentos_específico, saida $id_saida.");
                $qr_saidaFiles = mysql_query("DELETE FROM saida_files WHERE id_saida = $id_saida LIMIT 1;") or die ("Erro ao excluir. saida_files, id_saida $id_saida.");
                break;
            case 2: // RESCISAO
                $qr_buscaRescisao = mysql_query("SELECT C.id_rescisao
                                    FROM saida AS A
                                    LEFT JOIN rh_recisao AS B ON (A.id_clt = B.id_clt)
                                    LEFT JOIN pagamentos_especifico AS C ON (B.id_recisao = C.id_rescisao)
                                    WHERE A.id_saida = $id_saida AND B.rescisao_complementar = 0;");
                $row_rescisao = mysql_fetch_assoc($qr_buscaRescisao);
                $qr_buscaResComplementar = mysql_query("SELECT B.id_saida
                                            FROM rh_recisao AS A
                                            LEFT JOIN pagamentos_especifico AS C ON (A.id_recisao = C.id_rescisao)
                                            LEFT JOIN saida AS B ON (C.id_saida = B.id_saida)
                                            WHERE A.vinculo_id_rescisao = {$row_rescisao['id_rescisao']} AND B.status != 0;");
                $qtdResComplementar = mysql_num_rows($qr_buscaResComplementar);
                if($qtdResComplementar == 0){
                    $qr_saida = mysql_query("UPDATE saida SET status = 0 WHERE id_saida = $id_saida AND status = 1 LIMIT 1;") or die ("Erro ao desprocessar a saida");
                    $qr_pgEspecifico = mysql_query("DELETE FROM pagamentos_especifico WHERE id_saida = $id_saida LIMIT 1;") or die ("Erro ao excluir. (pagamentos_específico, saida $id_saida.");
                    $qr_saidaFiles = mysql_query("DELETE FROM saida_files WHERE id_saida = $id_saida LIMIT 1;") or die ("Erro ao excluir. saida_files, id_saida $id_saida.");
                }else{
                    echo 'Não é possível excluir esta saída, pois a mesma possui uma rescisão complementar.';
                    exit;
                }
                break;
            case 3: // MULTA 
                $qr_saida = mysql_query("UPDATE saida SET status = 0 WHERE id_saida = $id_saida AND status = 1 LIMIT 1;") or die ("Erro ao desprocessar a saida");
                $qr_saidaFiles = mysql_query("DELETE FROM saida_files WHERE id_saida = $id_saida LIMIT 1;") or die ("Erro ao excluir. saida_files, id_saida $id_saida.");
                break;
            case 4: // RESCISAO COMPLEMENTAR
                $qr_saida = mysql_query("UPDATE saida SET status = 0 WHERE id_saida = $id_saida AND status = 1 LIMIT 1;") or die ("Erro ao desprocessar a saida");
                $qr_pgEspecifico = mysql_query("DELETE FROM pagamentos_especifico WHERE id_saida = $id_saida LIMIT 1;") or die ("Erro ao excluir. (pagamentos_específico, saida $id_saida.");
                $qr_saidaFiles = mysql_query("DELETE FROM saida_files WHERE id_saida = $id_saida LIMIT 1;") or die ("Erro ao excluir. saida_files, id_saida $id_saida.");
                break;
            case 5: // MULTA COMPLEMENTAR
                $qr_saida = mysql_query("UPDATE saida SET status = 0 WHERE id_saida = $id_saida AND status = 1 LIMIT 1;") or die ("Erro ao desprocessar a saida");
                $qr_pgEspecifico = mysql_query("DELETE FROM pagamentos_especifico WHERE id_saida = $id_saida LIMIT 1;") or die ("Erro ao excluir. (pagamentos_específico, saida $id_saida.");
                $qr_saidaFiles = mysql_query("DELETE FROM saida_files WHERE id_saida = $id_saida AND multa_rescisao = 2 LIMIT 2;") or die ("Erro ao excluir. saida_files, id_saida $id_saida.");
                break;
        }
    }else{
        echo 'Não é possível excluir esta saída, pois a mesma já foi paga.';
        exit;
    }
    $qr_log = mysql_query("INSERT INTO log_desprocessar_saida (id_saida, id_usuario) VALUES ('$id_saida', '$id_usuario');") or die("Erro ao gravar o log.");
    echo 'Saída desprocessada com sucesso...';
        echo "<script> 
                setTimeout(function(){
                window.parent.location.href = 'http://" . $_SERVER['HTTP_HOST'] . "/intranet/rh/pagamentos/index.php?id=1&regiao=$regiao&mes=$mes_consulta&ano=$ano_consulta&filtrar=1&tipo_pagamento=1&tipo_contrato=2';
                parent.eval('tb_remove()')
                },3000)    
        </script>";
    exit;
}
?>
<html>
    <head>
        <title>RH - Pagamentos</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

        <link href="../../net1.css" rel="stylesheet" type="text/css" />
        <script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>        
        <script src="../../jquery/priceFormat.js" type="text/javascript"></script>
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <style>
            .aviso { color: #f96a6a;
                     font-weight: bold;
            }
            h3{ color: #0bbfe7; }
            body{background-color: #FFF;}

        </style>
    </head>
    <body class="novaintra">
        <div id="content">   
            <form name="form1" id="form1" method="post">
            <h3><?php echo $row_clt['clt']; ?></h3>
            <p><strong><?php echo $tipo_nome; ?></strong></p>
            <p><strong>Região: </strong><?php echo $row_clt['nome_regiao']; ?></p>
            <p><strong>Projeto: </strong><?php echo $row_clt['nome_projeto']; ?></p>
            
            <table class="grid" cellpadding="0" cellspacing="0"  width="800">
                <thead> 
                    <tr class="titulo">
                        <th>Status</th>
                        <th>Enviado Em</th>
                        <th>Enviado Por</th>
                        <th>Saída</th>
                        <th>Banco</th>
                        <th>Descrição</th>
                        <th>Especificação</th>
                        <th>Valor</th>
                        <th>Vencimento Em</th>
                        <th>Pago Por</th>
                        <th colspan="2">Arquivos</th>
                        <?php if ($acoes->verifica_permissoes(89)) { ?>
                            <th>Desprocessar saída</th>
                        <?php } ?>
                    </tr>
                </thead>
                        <?php
                        while ($row_saida = mysql_fetch_assoc($qr_saida)) {
                            $msg_de_vencido = FALSE;

                            if ($row_saida['anexo'] != "") {
                                $link_encryptado = encrypt('ID=' . $row_saida['id_saida'] . '&tipo=0');
                                $anexo = "<a target=\"_blank\" title=\"Anexo\" href=\"../../novoFinanceiro/view/comprovantes.php?" . $link_encryptado . "\"><img src=\"../../financeiro/imagensfinanceiro/attach-32.png\"  /></a>";
                            }
                            
                            var_dump($row_saida['comprovante']);
                            if($row_saida['comprovante'] != ""){
                                echo 'entra';
                            }
                            if ($row_saida['comprovante'] != "") {
                                $link_encryptado_pg = encrypt('ID=' . $row_saida['id_saida'] . '&tipo=1');
                                $comp = "<a target=\"_blank\" title=\"Comprovante\" href=\"../../novoFinanceiro/view/comprovantes.php?" . $link_encryptado_pg . "\"><img src=\"../../financeiro/imagensfinanceiro/attach-32.png\"  /></a>";
                            }
                            ?>
                    <tr style="font-size: 10px;" class="<?php echo ($i++ % 2 == 0) ? 'even' : 'odd'; ?>">
                        <td>
                    <?php
                    if ($row_saida['estorno'] == 1) {
                        $reenvio = true;
                        echo "<span class='tx-orange' data-key='{$row['id_saida']}'>Estornada</span>";
                    } elseif ($row_saida['status'] == 1) {
                        $reenvio = true;
                        $msg_de_vencido = TRUE;
                        echo "<span class='tx-blue' data-key='{$row['id_saida']}'>Não pago</span>";
                    } elseif ($row_saida['status'] == 2) {
                        echo "<span class='tx-green' data-key='{$row['id_saida']}'>Pago</span>";
                    } else {
                        $reenvio = true;
                        echo "<span class='tx-red' data-key='{$row['id_saida']}'>Deletado</span>";
                    }
                    ?>
                        </td>

                        <td><?php echo $row_saida['processado']; ?></td>
                        <td><?php echo $row_saida['enviado_por']; ?></td>
                        <td><?php echo $row_saida['id_saida']; ?></td>
                        <td><?php echo $row_saida['id_banco'] . ' - ' . $row_saida['nomeBanco']; ?></td>
                        <td><?php echo $row_saida['descricao']; ?></td>
                        <td><?php echo $row_saida['especifica']; ?></td>
                        <td><?php echo $row_saida['valor']; ?></td>
    <?php
    $dias = diferencaDias(date('d/m/Y'), $row_saida['data_vencimento']);
    $msg_vencimento = '';
    if ($dias <= 0) {
        $msg_vencimento = 'VENCIDO';
    } else {
        $msg_vencimento = 'Faltam ' . $dias . ' dias.';
    }
//                    
    ?>
                        <?php if ($msg_de_vencido) { ?>                                
                            <td style="font-size: 16px; color: red;">
                                <strong>
                            <?php echo $row_saida['data_vencimento'];
                            echo ' <br>' . $msg_vencimento; ?>
                                </strong>
                            </td>
                        <?php } else { ?> 
                            <td style="font-size: 16px;">
                                <strong>
                                    <?= $row_saida['data_vencimento']; ?>
                                </strong>                            
                            </td>
                        <?php } ?>

                        <td><?php echo $row_saida['pago_por']; ?></td>
                        <td><?php echo $anexo; ?></td>
                        <td><?php echo $comp; ?></td>
                        <?php if ($acoes->verifica_permissoes(89)){
                            if ($row_saida['status'] == 1) { ?>
                            <td>
                                <input type="hidden" value="<?php echo $usuario['id_funcionario'];?>" name="id_usuario"/>
                                <input type="hidden" value="<?php echo $row_saida['id_saida'];?>" name="id_saida"/>
                                <input type="hidden" value="<?php echo $tipo_guia;?>" name="tipo_guia"/>
                                <input type="hidden" value="<?php echo $id_clt;?>" name="id_clt"/>
                                <input type="hidden" value="<?php echo $mes;?>" name="mes_consulta"/>
                                <input type="hidden" value="<?php echo $ano;?>" name="ano_consulta"/>
                                <center><input type="submit" value="Excuir" name="excluir"/></center>
                            </td>
                        <?php }else{?>
                            <td>
                            <center><input type="button" value="Excuir" name="excluir" disabled="disabled" /></center>
                            </td>
                        <?php }
                        }?>
                            
    <?php
    if ($row_saida['estorno'] == 1 and ! empty($row_saida['estorno_obs']) != '') {
        echo '<td>' . $row_saida['estorno_obs'] . '</td>';
    }

    if ($row_saida['estorno'] == 2 and ! empty($row_saida['valor_estorno_parcial'])) {
        echo '<td>' . $row_saida['valor_estorno_parcial'] . '</td>';
    }
    ?>
                    </tr> 


                    <?php 
unset($anexo,$comp);
    } ?>
            </table>
            </form>
        </div>
    </body>
</html>