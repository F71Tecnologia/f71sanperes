<?php
include('../../conn.php');
include("../../wfunction.php");
include("../../funcoes.php");
include ("../../classes_permissoes/acoes.class.php");
include "../../classes/LogClass.php";
$log = new Log();

$id_rpa = $_REQUEST['id_rpa'];
$tipo_guia = $_REQUEST['tipo_guia']; /// TIPO: 2- GPS, 3 - IR
$id_autonomo = $_REQUEST['id_autonomo'];
$mes_consulta = $_REQUEST['mes_consulta'];
$ano_consulta = $_REQUEST['ano_consulta'];

$acoes = new Acoes();
$usuario = carregaUsuario();

$qr_rpa = mysql_query("SELECT *
                      FROM  rpa_autonomo AS A
                      INNER JOIN autonomo AS B ON (A.id_autonomo = B.id_autonomo)
                      WHERE A.id_rpa = $id_rpa;");

$row_rpa = mysql_fetch_assoc($qr_rpa);


$regiao = $row_rpa['id_regiao'];
$projeto = $row_rpa['id_projeto'];

$mes = $row_rpa['mes_competencia'];
$ano = $row_rpa['ano_competencia'];
$nomeTipo = array("2" => "GPS", "3" => "IR");

$nome_completo = "RPA - " . htmlentities($row_rpa['nome']) . " - $texto " . mesesArray($mes) . '/' . $ano;
$qr_saida = mysql_query("SELECT *, B.nome as descricao,C.nome as enviado_por, D.nome as pago_por,B.estorno,B.id_saida,
                         DATE_FORMAT(B.data_proc, '%d/%m/%Y') as processado,
                         DATE_FORMAT(B.data_vencimento, '%d/%m/%Y') as data_vencimento,
                         (SELECT tipo_saida_file FROM saida_files WHERE id_saida = A.id_saida LIMIT 1) as anexo,
                         (SELECT tipo_pg FROM saida_files_pg WHERE id_saida = A.id_saida LIMIT 1) as comprovante, E.nome as nome_banco
                         FROM  rpa_saida_assoc as  A
                         INNER JOIN saida as B
                         ON A.id_saida = B.id_saida
                         LEFT JOIN funcionario AS C ON (B.id_user=C.id_funcionario)
                         LEFT JOIN funcionario AS D ON (B.id_userpg=D.id_funcionario)
                         LEFT JOIN bancos AS E ON B.id_banco = E.id_banco
                         WHERE A.id_rpa = $id_rpa AND tipo_vinculo = $tipo_guia");

$verifica_estorno = mysql_query("SELECT IF(B.estorno != 0 ,'estorno', B.status) as verifica_saida
                                 FROM rpa_saida_assoc AS A
                                 INNER JOIN saida AS B ON (A.id_saida = B.id_saida)
                                 WHERE id_rpa = '$id_rpa' AND tipo_vinculo = $tipo_guia  ORDER BY data_proc DESC LIMIT 1");
$row_verifica = mysql_fetch_assoc($verifica_estorno);

if(isset($_REQUEST['excluir'])){
    $id_saida = $_REQUEST['id_saida'];
    $mes_consulta = $_REQUEST['mes_consulta'];
    $ano_consulta = $_REQUEST['ano_consulta'];
    $regiao = $_REQUEST['regiao'];
    $id_usuario = $_REQUEST['id_usuario'];
    $id_rpa = $_REQUEST['id_rpa'];
    
    $qr_saida = "SELECT status, id_autonomo FROM saida WHERE id_saida = $id_saida;";
    $result = mysql_query($qr_saida) or die ("Erro ao consultar a saída.");
    $row_saida = mysql_fetch_assoc($result);
    if($row_saida['status']==1){
        $cont = 0;
//        $qr_rpa = mysql_query("SELECT id_rpa
//                                FROM rpa_autonomo
//                                WHERE id_autonomo = {$row_saida['id_autonomo']};") or die ("Erro ao consultar o rpa.");
//        $row_rpa = mysql_fetch_assoc($qr_rpa);
        $qr_rpaSaidaAssoc = mysql_query("SELECT B.id_saida, B.`status`, A.tipo_vinculo
                                FROM rpa_saida_assoc AS A
                                INNER JOIN saida AS B ON A.id_saida = B.id_saida
                                WHERE A.id_rpa = {$id_rpa} AND B.status != 0;") or die ("Erro ao consultar o rpa_saida_assoc");
        $qtdSaidasRpa = mysql_num_rows($qr_rpaSaidaAssoc);
        if($qtdSaidasRpa==1){
            $qr_saida = mysql_query("UPDATE saida SET status = 0 WHERE id_saida = $id_saida AND status = 1 LIMIT 1;") or die ("Erro ao desprocessar a saida.");
            $qr_rpaSaidaAssoc = mysql_query("DELETE FROM rpa_saida_assoc WHERE id_saida = $id_saida LIMIT 1;") or die ("Erro ao excluir. rpa_saida_assoc, id_saida $id_saida");
            $qr_saidaFiles = mysql_query("DELETE FROM saida_files WHERE id_saida = $id_saida LIMIT 1;") or die ("Erro ao excluir. saida_files, id_saida $id_saida.");
            $log->gravaLog('Pagamentos', "Saída Desprocessada: ID{$id_saida}");
            $qr_log = mysql_query("INSERT INTO log_desprocessar_saida (id_saida, id_usuario) VALUES ('$id_saida', '$id_usuario');") or die("Erro ao gravar o log.");
            $cont = 1;
        }else{
            while ($row_rpaSaidaAsso = mysql_fetch_array($qr_rpaSaidaAssoc)) {
                if($row_rpaSaidaAsso['id_saida'] == $id_saida && $row_rpaSaidaAsso['tipo_vinculo']>1 && $row_rpaSaidaAsso['status']==1){
                    echo 'exclui a gua';
                    $qr_saida = mysql_query("UPDATE saida SET status = 0 WHERE id_saida = $id_saida AND status = 1 LIMIT 1;") or die ("Erro ao desprocessar a saida.");
                    $qr_rpaSaidaAssoc = mysql_query("DELETE FROM rpa_saida_assoc WHERE id_saida = $id_saida LIMIT 1;") or die ("Erro ao excluir. rpa_saida_assoc, id_saida $id_saida");
                    $qr_saidaFiles = mysql_query("DELETE FROM saida_files WHERE id_saida = $id_saida LIMIT 1;") or die ("Erro ao excluir. saida_files, id_saida $id_saida.");
                    $qr_log = mysql_query("INSERT INTO log_desprocessar_saida (id_saida, id_usuario) VALUES ('$id_saida', '$id_usuario');") or die("Erro ao gravar o log.");
                    $log->gravaLog('Pagamentos', "Saída Desprocessada: ID{$id_saida}");
                    $cont = 1;
                    break;
                }
            }
        }
        if($cont==0){
            echo 'Para excluir esta saída, antes é necessário excluir as guias de GPS e IR vinculadas a mesma.';
            exit;
        } else {
            echo 'Saída desprocessada com sucesso...';
            echo "<script> 
                    setTimeout(function(){
                    window.parent.location.href = 'http://" . $_SERVER['HTTP_HOST'] . "/intranet/rh/pagamentos/index.php?id=1&regiao=$regiao&mes=$mes_consulta&ano=$ano_consulta&filtrar=1&tipo_pagamento=1&tipo_contrato=2';
                    parent.eval('tb_remove()')
                    },3000)    
            </script>";
            exit;
        }
    }else{
        echo 'Não é possível excluir esta saída, pois a mesma já foi paga.';
        exit;
    }
    exit;
}

function geraTimestamp($data) {
    $partes = explode('-', $data);
    return mktime(0, 0, 0, $partes[1], $partes[2], $partes[0]);
}

?>
<html>
    <head>
        <title>RH - Pagamentos</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

        <link href="../../net1.css" rel="stylesheet" type="text/css" />
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>        
        <script src="../../jquery/priceFormat.js" type="text/javascript"></script>
        <script>
            $(function() {
                $("#editar").on("click", function() {
                    $("#form1").attr('action','editar_rpa_saidas.php');
                    $("#form1").submit();
                           
                });
            });
        </script>
        <style>
            .aviso { color: #f96a6a;
                     font-weight: bold;
            }
            h3{ color: #0bbfe7; }
            body{background-color: #FFF;}
            .style_error{
                color: red;
                text-align: center;
            }
            .text_msg{
                font-style: normal;
                font-size: 10px;
            }
        </style>
    </head>
    <body class="novaintra">
        <div id="content">
           <form name="form1" id="form1" method="post">
           <h3> <?= $nome_completo; ?> </h3>
            <table class="grid" cellpadding="0" cellspacing="0"  width="600">
                <thead> 
                    <tr class="titulo">
                        <th>Status</th>
                        <th>Cod. Saída</th>
                        <th>Banco</th>
                        <th>Enviado Em</th>
                        <th>Enviado Por</th>
                        <th>Descrição</th>
                        <th>Especificação</th>
                        <th>Valor</th>
                        <th>Vencimento Em</th>
                        <th>Pago Por</th>
                        <th colspan="2">Arquivos</th>
                        <?php if ($row_saida['estorno'] == 2) {
                            echo '<th>Valor Estorno Parcial</th>';
                        } ?>
                        <?php if ($acoes->verifica_permissoes(89,$regiao)) { ?>
                            <th>Desprocessar saída</th>
                        <?php } ?>
                    </tr>
                </thead>
    <?php
    while ($row_saida = mysql_fetch_assoc($qr_saida)) {

        //ANEXOS E COMPROVANTES
        if ($row_saida['anexo'] != "") {
            $link_encryptado = encrypt('ID=' . $row_saida['id_saida'] . '&tipo=0');
            $anexo = "<a target=\"_blank\" title=\"Anexo\" href=\"../../novoFinanceiro/view/comprovantes.php?" . $link_encryptado . "\"><img src=\"../../imagens/icones/icon-docview.gif\"  /></a>";
        }
        if ($row_saida['comprovante'] != "") {
            $link_encryptado_pg = encrypt('ID=' . $row_saida['id_saida'] . '&tipo=1');
            $comp = "<a target=\"_blank\" title=\"Comprovante\" href=\"../../novoFinanceiro/view/comprovantes.php?" . $link_encryptado_pg . "\"><img src=\"../../imagens/icones/icon-docview.gif\"  /></a>";
        }
        ?>
    <tr style="font-size: 10px;" class="<?php echo ($i++ % 2 == 0) ? 'even' : 'odd'; ?>">
        <td>
            <?php
            if ($row_saida['estorno'] == 1) {
                $reenvio = true;
                echo "<span class='tx-orange' data-key='{$row['id_saida']}'>Estornada</span>";
            } else
            if ($row_saida['status'] == 1) {
                $reenvio = true;
                echo "<span class='tx-blue' data-key='{$row['id_saida']}'>Não pago</span>";
            } elseif ($row_saida['status'] == 2) {
                echo "<span class='tx-green' data-key='{$row['id_saida']}'>Pago</span>";
            } else {
                $reenvio = true;
                echo "<span class='tx-red' data-key='{$row['id_saida']}'>Deletado</span>";
            }
            ?>
        </td>
        <td><?php echo $row_saida['id_saida']; ?></td>
        <td><?php echo $row_saida['id_banco'] . " - " . $row_saida['nome_banco']; ?></td>
        <td><?php echo $row_saida['processado']; ?></td>
        <td><?php echo $row_saida['enviado_por']; ?></td>
        <td><?php echo $row_saida['descricao']; ?></td>
        <td><?php echo $row_saida['especifica']; ?></td>
        <td><?php echo $row_saida['valor']; ?></td>
        <td style="font-size: 16px; font-weight: bold;">
            <?php 
                $data_sql = date("Y-m-d", strtotime(str_replace("/", "-", $row_saida['data_vencimento'])));
                $style_error = "style_error";

                if($row_saida['data_vencimento'] == "00/00/0000"){
                    $msg = "Data de vencimento zerada";
                }else if($data_sql < date("Y-m-d") && $row_saida['status'] == 1){
                    $msg = "Vencida";
                }else if($data_sql > date("Y-m-d")){
                    $time_inicial = geraTimestamp($data_sql);
                    $time_final = geraTimestamp(date("Y-m-d"));
                    $diferenca =  $time_inicial - $time_final; // 19522800 segundos
                    $dias = (int) floor($diferenca / (60 * 60 * 24));
                    $msg = "<span class='text_msg'>Restando " . $dias . " dias para o vencimento</span>";
                }  
            ?>
            
            <?php echo "<p class='{$style_error}'>" . $row_saida['data_vencimento']  . "<br />" . $msg . "</p>"; ?>
        </td>
        <td><?php echo $row_saida['pago_por']; ?></td>
        <td><?php echo $anexo; ?></td>
        <td><?php echo (empty($comp)) ? '--' : $comp; ?></td>
        <input type="hidden" value="<?php echo $usuario['id_funcionario'];?>" name="id_usuario"/>
        <input type="hidden" value="<?php echo $row_saida['id_saida'];?>" name="id_saida" id="id_saida"/>
        <input type="hidden" value="<?php echo $mes_consulta;?>" name="mes_consulta"/>
        <input type="hidden" value="<?php echo $ano_consulta;?>" name="ano_consulta"/>
        <input type="hidden" value="<?php echo $regiao;?>" name="regiao"/>
        <input type="hidden" value="<?php echo $nome_completo;?>" name="nome_completo"/>
        <?php
            if ($acoes->verifica_permissoes(89)){
                if ($row_saida['status'] == 1) { 
                    $btnStatus = '';
                    $btnTipo = 'type="submit"';
                }else{
                    $btnStatus = 'disabled="disabled"';
                    $btnTipo = 'type="button"';
                }?>
                <td>
                    <center><input <?php echo $btnTipo;?> value="Excuir" name="excluir" <?php echo $btnStatus;?>/></center>
                </td>
            <?php
            }?>
        <?php
        if ($row_saida['estorno'] == 1 and !empty($row_saida['estorno_obs']) != '') {
            echo '<td>' . $row_saida['estorno_obs'] . '</td>';
        }

        if ($row_saida['estorno'] == 2 and !empty($row_saida['valor_estorno_parcial'])) {
            echo '<td>' . $row_saida['valor_estorno_parcial'] . '</td>';
        }
    ?>
    </tr>   
<?php } ?>
</table>

    <?php
    if ($row_verifica['verifica_saida'] == 'estorno') {

        echo " <div id='message-box' class='message-blue txcenter' style='margin-top: 10px; width:600px;'>
                 <a href='cadastro_rpa_guias.php?id_rpa=$id_rpa&tipo_guia=$tipo_guia&id_autonomo=$id_autonomo&mes_consulta=$mes_consulta&ano_consulta=$ano_consulta'>REENVIAR GUIA</a>
               </div>";
    }
    ?>
</div>
</body>
</html>