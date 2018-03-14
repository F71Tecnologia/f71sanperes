<?php
include('../../conn.php');
include("../../wfunction.php");
include("../../funcoes.php");
include ("../../classes_permissoes/acoes.class.php");

$mes = $_GET['mes'];
$ano = $_GET['ano'];
$tipo_guia = $_GET['tipo_guia']; // 1 - GPS, 2 - FGTS , 3 - PIS,  4 - IR , 5 - VALE TRANSPORTE, 6 - VALE ALIMENTACAO/REFEICAO, 7 - IR de FERIAS, 8 - SINDICATO
$projeto = $_GET['projeto'];
$regiao = $_GET['regiao'];
$id_folha = $_GET['id_folha'];
$tipo_contrato = $_GET['tipo_contrato'];
$mes_consulta = $_REQUEST['mes_consulta'];
$ano_consulta = $_REQUEST['ano_consulta'];

$acoes = new Acoes();

switch ($tipo_contrato) {

    case 2: $tipo_contrato_pg = 1;
        break;
    case 3: $tipo_contrato_pg = 2;
        break;
}
$nomes = array('1' => 'GPS', 2 => 'FGTS', 3 => 'PIS', 4 => 'IR', 7 => 'IR DE FÉRIAS');

$usuario = carregaUsuario();

$qr_folha = mysql_query("SELECT A.id_folha, B.nome as nome_projeto, C.regiao as nome_regiao FROM rh_folha as A 
                INNER JOIN projeto as B
                ON A.projeto = B.id_projeto
                INNER JOIN regioes as C
                ON C.id_regiao = A.regiao
                WHERE id_folha = $id_folha;");

$folha = mysql_fetch_assoc($qr_folha);

$qr_saida = mysql_query("SELECT A.tipo_descricao,A.tipo_pg, A.id_pg, B.id_saida, B.estorno,B.estorno_obs,
                            DATE_FORMAT(B.data_proc, '%d/%m/%Y')  as processado,
                            DATE_FORMAT(B.data_vencimento, '%d/%m/%Y')  as data_vencimento,
                             B.especifica,   B.nome as descricao, C.nome as enviado_por,
                             B.valor,B.id_banco,
                            D.nome as pago_por, E.nome AS nomeBanco,                           
                             (SELECT tipo_saida_file FROM saida_files WHERE id_saida = B.id_saida LIMIT 1) as anexo,
                            (SELECT tipo_pg FROM saida_files_pg WHERE id_saida = B.id_saida LIMIT 1) as comprovante,
                            B.status
                            FROM pagamentos as A
                            INNER JOIN saida as B
                            ON A.id_saida  = B.id_saida 
                            LEFT JOIN funcionario as C
                            ON C.id_funcionario = B.id_user
                            LEFT JOIN funcionario as D
                            ON D.id_funcionario = B.id_userpg
                            LEFT JOIN bancos AS E 
                            ON B.id_banco = E.id_banco
                            WHERE A.id_folha = $id_folha  
                            AND A.tipo_pg = '$tipo_guia' AND  A.tipo_contrato_pg = $tipo_contrato_pg");

$verifica_estorno = mysql_query("SELECT IF(B.estorno != 0 ,'estorno', B.status) as verifica_saida
                                    FROM pagamentos AS A
                                    LEFT JOIN saida AS B ON(A.id_saida=B.id_saida)
                                    WHERE  A.id_folha = $id_folha AND A.tipo_pg = $tipo_guia AND A.tipo_contrato_pg = $tipo_contrato_pg ORDER BY data_proc DESC LIMIT 1");
$row_verifica = mysql_fetch_assoc($verifica_estorno);
$tipo_guia = $_REQUEST['tipo_guia'];

if(isset($_REQUEST['excluir'])){
    $id_saida = $_REQUEST['id_saida'];
    $mes_consulta = $_REQUEST['mes_consulta'];
    $ano_consulta = $_REQUEST['ano_consulta'];
    $regiao = $_REQUEST['regiao'];
    $id_usuario = $_REQUEST['id_usuario'];
    $qr_saida = "SELECT status FROM saida WHERE id_saida = $id_saida;";
    $result = mysql_query($qr_saida);
    $row_saida = mysql_fetch_assoc($result);
    if($row_saida['status']==1){
        $qr_saida = mysql_query("UPDATE saida SET status = 0 WHERE id_saida = $id_saida AND status = 1 LIMIT 1;") or die ("Erro ao desprocessar a saida.");
        $qr_pg = mysql_query("DELETE FROM pagamentos WHERE id_saida = $id_saida LIMIT 1;") or die ("Erro ao excluir. pagamentos, id_saida $id_saida.");
        $qr_saidaFiles = mysql_query("DELETE FROM saida_files WHERE id_saida = $id_saida LIMIT 1;") or die ("Erro ao excluir. saida_files, id_saida $id_saida.");
        $qr_log = mysql_query("INSERT INTO log_desprocessar_saida (id_saida, id_usuario) VALUES ('$id_saida', '$id_usuario');") or die("Erro ao gravar o log.");
    }else{
        echo 'Não é possível excluir esta saída, pois a mesma já foi paga.';
        exit;
    }
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
        <script>
            $(function() {
                $(".addAnexo").click(function() {
                    //$( "#conteudo" ).load( "cadastro_1.php" );
                });
                $("tr").mouseover(function() {
                    var key = $(this).data("key");
                    $("#detalhes_" + key).show();
                    $("#detalhes_" + key + " div").hide();
                });
                $("tr").mouseover(function() {
                    var key = $(this).data("key");
                    $("#detalhes_" + key).show();
                    $("#detalhes_" + key + " div").css({width: "0px", height: "0px", cursor: "pointer"});
                });
                $("tr").mouseout(function() {
                    var key = $(this).data("key");
                    $("#detalhes_" + key).css({width: "30px", height: "23px", opacity: "0.6", background: "url('imagens/lupa.png')", backgroundSize: "26px", backgroundPositionX: "1px"}).hide();
                    $("#detalhes_" + key + " div").hide();
                });
                $(".detalhes").click(function() {
                    var key = $(this).data("key");
                    $(this).css({width: "214px", height: "89px", opacity: "1", cursor: "default", background: "#eee"});
                    $("#detalhes_" + key + " div").css({width: "160px", height: "0px"});
                    $("#detalhes_" + key + " div").show();
                });
                $("body").hover(function() {
                    $(".detalhes").hide();
                    $(".detalhes div").hide();
                });

            });
        </script>
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <style>
            .aviso { color: #f96a6a;
                     font-weight: bold;
            }
            h3{ color: #0bbfe7; }
            body{background-color: #FFF;}
            .esconderTitulo{ display: none;}
            .botaoGordo{
                margin-top: -61px;
                margin-bottom: 13px;
                margin-right: -452px;
                padding: 4px 18px;
            }

            #paiDetalhe{ position: relative;}
            .detalhes{ position: absolute; top: 0px; right: 0px; width: 30px; height:23px; z-index: 99999; opacity: 0.6; cursor: pointer; background: url("imagens/lupa.png"); background-size: 26px; background-position-x: 1px; display: none;}
            .detalhes div{ display: none;}
            /** #detalhes img{ width: 20px; margin-top: 3px; cursor: pointer;} **/
        </style>
    </head>
    <body class="novaintra">
        <div id="content">       
            <form name="form1" id="form1" method="post">
            <h3><?php echo $row_clt['clt']; ?></h3>
            <p><strong><?php echo $nomes[$tipo_guia]; ?></strong></p>
            <p><strong>Folha: </strong><?php echo $folha['id_folha']; ?></p>
            <p><strong>Região: </strong><?php echo $folha['nome_regiao']; ?></p>
            <p><strong>Projeto: </strong><?php echo $folha['nome_projeto']; ?></p>
            <?php
//            O QUE É ISSO?
//            if ($tipo_guia == 5 || $tipo_guia == 6) {
//                echo "<p><a href=cadastro_1.php?id_folha={$_REQUEST['id_folha']}&tipo_guia={$_REQUEST['tipo_guia']}&tipo_contrato={$_REQUEST['tipo_contrato']}&mes_consulta={$_REQUEST['mes_consulta']}&ano_consulta={$_REQUEST['ano_consulta']}&keepThis=true&TB_iframe=true&width=850 class='thickbox' style='float:right; margin-right:10px; margin-bottom:15px; font-size:14px; text-decoration:none; color:#333; background:#f2f2f2; padding: 7px 15px; '>Adicionar Anexo</a></p>";
//            }
            ?>
            <div id="conteudo">

            </div>
            <table class="grid" cellpadding="0" cellspacing="0"  width="800">
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
                        <th colspan="2" >Arquivos</th>
                        <?php if ($acoes->permissoes_folha(89,$regiao)) { ?>
                            <th>Desprocessar saída</th>
                        <?php } ?>
                    </tr>
                </thead>
                <?php
                while ($row_saida = mysql_fetch_assoc($qr_saida)) {
                    $valorFinal = str_replace(",", ".", $row_saida['valor']);
                    
                     
                    if ($row_saida['anexo'] != "") {
                        $link_encryptado = encrypt('ID=' . $row_saida['id_saida'] . '&tipo=0');
                        $anexo = "<a target=\"_blank\" title=\"Anexo\" href=\"../../novoFinanceiro/view/comprovantes.php?" . $link_encryptado . "\"><img src=\"../../financeiro/imagensfinanceiro/attach-32.png\"  /></a>";
                    }
                    if ($row_saida['comprovante'] != "") {
                        $link_encryptado_pg = encrypt('ID=' . $row_saida['id_saida'] . '&tipo=1');
                        $comp = "<a target=\"_blank\" title=\"Comprovante\" href=\"../../novoFinanceiro/view/comprovantes.php?" . $link_encryptado_pg . "\"><img src=\"../../financeiro/imagensfinanceiro/attach-32.png\"  /></a>";
                    }
                    
                    ?>
                    <tr style="font-size: 10px;" class="<?php echo ($i++ % 2 == 0) ? 'even' : 'odd'; ?>" data-key="<?php echo $row_saida['id_saida']; ?>">
                        <td>
                            <?php
                            if ($row_saida['estorno'] == 1) {
                                $reenvio = true;
                                echo "<span class='tx-orange' data-key='{$row['id_saida']}'>Estornada</span>";
                            } elseif ($row_saida['status'] == 1) {
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
                        <td><?php echo $row_saida['id_banco'] . " - " . $row_saida['nomeBanco']; ?></td>
                        <td><?php echo $row_saida['processado']; ?></td>
                        <td><?php echo $row_saida['enviado_por']; ?></td>
                        <td><?php echo $row_saida['descricao']; ?></td>
                        <td><?php echo $row_saida['especifica']; ?></td>
                        <td><?php echo number_format($valorFinal, 2, ",", "."); ?></td>
                        <td style="font-size: 16px; font-weight: bold;"><?php echo $row_saida['data_vencimento']; ?></td>
                        <td><?php echo $row_saida['pago_por']; ?></td>
                        <td><?php echo $anexo; ?></td>
                       

                       <!--  <td id="paiDetalhe">--><?php // echo $comp; ?>
                            <!--O QUE É ISSO?-->
                            <?php // if ($tipo_guia == 5 || $tipo_guia == 6) { ?>
<!--                                <div id="detalhes_<?php // echo $row_saida['id_saida']; ?>" class="detalhes" title="Detalhe" data-key="<?php // echo $row_saida['id_saida']; ?>">
                                    <div style="padding: 5px;">
                                        <?php
//                                        echo "<b style='font-size:11px'>Tipo cadastrado: <br /> - </b>";
//                                        echo ($row_saida['tipo_descricao'] == '1') ? "Unitário" : "Lote";
//                                        echo "<br /><b style='font-size:11px'>Pagamentos no documento: </b>";
//
//                                        $sql_tipo_pg = mysql_query("SELECT * FROM pagamentos_tipo WHERE id_pg = {$row_saida['id_pg']}");
//                                        $tipos_pg = array("1" => "Recarga", "2" => "Cancelamento", "3" => "Segunda Via");
//                                        while ($rowsTipos = mysql_fetch_assoc($sql_tipo_pg)) {
//                                            echo "<br/ > - " . $tipos_pg[$rowsTipos['tipo_pg']];
//                                        }
                                        ?>
                                    </div>
                                </div>-->
                            <?php // } ?>
                        <!--</td>-->
                        <?php if ($acoes->permissoes_folha(89,$regiao)){
                            if ($row_saida['status'] == 1) { ?>
                            <td>
                                <input type="hidden" value="<?php echo $usuario['id_funcionario'];?>" name="id_usuario"/>
                                <input type="hidden" value="<?php echo $row_saida['id_saida'];?>" name="id_saida"/>
                                <input type="hidden" value="<?php echo $mes_consulta;?>" name="mes_consulta"/>
                                <input type="hidden" value="<?php echo $ano_consulta;?>" name="ano_consulta"/>
                                <input type="hidden" value="<?php echo $regiao;?>" name="regiao"/>
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
                <?php } ?>
            </table>

            <?php
            if ($row_verifica['verifica_saida'] == 'estorno') {

                echo " <div id='message-box' class='message-blue txcenter' style='margin-top: 10px; width:600px;'>";
                echo "<a href='cadastro_1.php?id_folha=$id_folha&tipo_guia=$tipo_guia&tipo_contrato=$tipo_contrato&mes_consulta=$mes_consulta&ano_consulta=$ano_consulta&filtrar=1&tipo_pagamento=1&tipo_contrato=2&keepThis=true&amp;TB_iframe=true&amp;width=850'> 
                                        REENVIAR GUIA          </a>";
                echo "</div>";
            }
            ?>
            </form>
        </div>
    </body>
</html>