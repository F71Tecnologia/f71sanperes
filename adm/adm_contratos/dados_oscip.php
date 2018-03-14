<?php
include('../include/restricoes.php');
include('../../conn.php');
include('../../funcoes.php');
//include('../include/criptografia.php');
include('../../classes/formato_data.php');
include("../../wfunction.php");

$carregaUsuario = carregaUsuario();
$Master = $carregaUsuario['id_master'];

$usuario = mysql_fetch_assoc(mysql_query("SELECT * FROM funcionario WHERE id_funcionario = {$_COOKIE['logado']} LIMIT 1;"));

//IDS QUE TEM PERMISSAO PARA EXCLUIR DOCUMENTOS
$id_permitidos = array(5, 9, 24, 87, 204, 255, 257);

function verifica_status($periodo, $data, $n_periodo, $data_inicio, $data_termino) {

    list($ano, $mes, $dia) = explode('-', $data);

    if ($periodo == 'Dias') {
        //descobre a data de vencimento	
        $data_vencimento = mktime(0, 0, 0, $mes, $dia + $n_periodo, $ano);
        @$prazo_renovacao = $data_vencimento - 2592000;
        $data_atual = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
    }

    if ($periodo == 'Meses') {
        $data_vencimento = mktime(0, 0, 0, $mes + $n_periodo, $dia, $ano);
        @$prazo_renovacao = $data_vencimento - 2592000;
        $data_atual = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
    }

    if ($periodo == 'Anos') {
        $data_vencimento = mktime(0, 0, 0, $mes, $dia, $ano + $n_periodo);
        @$prazo_renovacao = $data_vencimento - 2505600;
        $data_atual = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
    }

    if ($periodo == 'Período') {
        list($ano_2, $mes_2, $dia_2) = explode('-', $data_termino);
        $data_vencimento = mktime(0, 0, 0, $mes_2, $dia_2, $ano_2);
        @$prazo_renovacao = $data_vencimento - 2592000;
        $data_atual = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
    }

    //quantidade de dias para a expiração
    $dif = ($data_vencimento - $data_atual) / 86400;

    if ($periodo != 'Indeterminado'){
        if ($dif == 0) {
            echo '<span style="color:#F60;font-weight:bold;">Expira hoje!</span>';
        } elseif ($prazo_renovacao < $data_atual and $data_vencimento > $data_atual) {
            echo '<span style="color:#09C;font-weight:bold;">Expira em ' . (int) $dif . ' dias!</span>';
        } elseif ($data_atual >= $data_vencimento) {
            echo '<span  style="color:#F00;font-weight:bold;"> Expirado</span>';
        } else {
            echo '<span style="color:#0C0;font-weight:bold;"> OK </span>';
        }
    } else if ($periodo == 'Indeterminado'){
        echo '<span style="color:#000;font-weight:bold;"> Prazo Indeterminado </span>';
    }
}

//fim função
?>                    

<html>
    <head>
        <title>:: Intranet :: Obriga&ccedil;&otilde;es da  OSCIP</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <link href="../css/estrutura.css" rel="stylesheet" type="text/css">
        <!--<link href="../../net1.css" rel="stylesheet" type="text/css" />-->
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />

        <link href="../../js/highslide.css" rel="stylesheet" type="text/css" /> 
        <script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../../js/global.js" type="text/javascript"></script>
        <script type="text/javascript" src="../../js/highslide-with-html.js"></script> 
        <script>
            hs.graphicsDir = '../../images-box/graphics/';
            hs.outlineType = 'rounded-white';
        </script>
        <!--<script type="text/javascript" src="../../jquery/jquery-1.4.2.min.js"></script>-->
        <script>
            $(document).ready(function() {

                var url = window.location.href;
                var itens = url.split("?")['1'];
                var status = itens.split("&")['1'];
                if (status == 'status=2') {
                    $("#btn").val('Voltar');
                } else {
                    $("#btn").val('Histórico');
                }




                $('.show').click(function() {
                    $('.show').not(this).removeClass('seta_aberto');
                    $('.show').not(this).addClass('seta_fechado');

                    if ($(this).attr('class') == 'show seta_aberto') {
                        $(this).removeClass('seta_aberto');
                        $(this).addClass('seta_fechado');
                    } else {
                        $(this).removeClass('seta_fechado');
                        $(this).addClass('seta_aberto');
                    }

                    $('.show').not(this).next().hide();
                    $(this).next().css({'width': '100%'}).slideToggle('fast');
                });

                $('.azul').parent().css({'background-color': '#e2edfe'});
                $('.vermelho').parent().css({'background-color': '#ffb8c0'});


                $(".bt-image").click(function() {
                    var action = $(this).attr("data-type");
                    var key = $(this).attr("data-key");
                    var master = $(this).attr("data-master");
                    if (action === "renovar") {
                        thickBoxIframe("Renovar Contrato", "renovar_oscip.php", {id: key, id_master: master, method: action}, "1000", "400", function(data){
                            if (data.type == "dialogclose"){
                                window.history.go(0);
                            }
                        });
                    }
                });

                $("#btn").click(function() {
                    if ($(this).val() == 'Histórico') {
                        var url = window.location.href;
                        var itens = url.split("?")['1'];
                        var status = itens.split("&")['1'];
                        status = 'status=2';
                        var hist = url.split("?")['0'] + '?' + itens.split("&")['0'] + '&' + status;

                        location.href = hist;
                    } else {
                        window.history.go(-1);
                    }
                });
                
                $(".ano").on("click", function(){
                    var ano = $(this).data("key");
                    $("."+ano).toggle();
                });
            });
        </script>
        <style>
            .bt-image{
                width: 18px;
                cursor: pointer;
            }                        
        </style>
    </head>
    <body>	
        <div id="corpo">
            <div id="menu" class="contrato">
                <?php //include('include/menu.php'); ?>
            </div>
            <div id="conteudo" style="text-transform:uppercase;">
                <h1><?php
                    if ($_REQUEST['status'] == 2) {
                        echo'<span>OBRIGAÇÕES DA INSTITUIÇÃO (VENCIDAS)</span>';
                    } else {
                        echo'<span>OBRIGAÇÕES DA INSTITUIÇÃO (VIGENTES)</span>';
                    } ?></h1>
                <?php
                if ($_REQUEST['status'] == 2) {
                    $aux_qr_oscip = 
                    " AND 
                        (
                            (periodo = 'Período' AND (oscip_data_termino < NOW())) OR 
                            (periodo = 'Dias' AND (DATE_ADD(data_publicacao, INTERVAL numero_periodo DAY) < NOW())) OR 
                            (periodo = 'Meses' AND (DATE_ADD(data_publicacao, INTERVAL numero_periodo MONTH) < NOW()))OR
                            (periodo = 'Anos' AND (DATE_ADD(data_publicacao, INTERVAL numero_periodo YEAR) < NOW()))
                        )";
                } else {
                    $aux_qr_oscip = 
                    " AND 
                        (
                            (periodo = 'Período' AND (oscip_data_termino >= NOW())) OR 
                            (periodo = 'Dias' AND (DATE_ADD(data_publicacao, INTERVAL numero_periodo DAY) >= NOW())) OR 
                            (periodo = 'Meses' AND (DATE_ADD(data_publicacao, INTERVAL numero_periodo MONTH) >= NOW())) OR 
                            (periodo = 'Anos' AND (DATE_ADD(data_publicacao, INTERVAL numero_periodo YEAR) >= NOW())) OR (PERIODO = 'INDETERMINADO') OR (periodo = 'Indeterminado') 
                        ) ";
                }
                ?>
                
                
                <?php $qr_tipo_oscip = mysql_query("SELECT * FROM tipo_doc_oscip WHERE 1 ORDER BY tipo_nome");
                while ($row_tipo = mysql_fetch_assoc($qr_tipo_oscip)):
                    $projeto_status = array(1 => 'Projetos Ativos', 0 => 'Projetos Inativos');
                    $qr_oscip = mysql_query("SELECT *, YEAR(data_publicacao) anoOscip
                                            FROM obrigacoes_oscip AS A
                                            WHERE status='1'  AND tipo_oscip='{$row_tipo['tipo_nome']}' AND id_master='$Master' $aux_qr_oscip ORDER BY data_publicacao DESC, id_oscip DESC;")or die("Erro no comando sql");
                    $verifica = mysql_num_rows($qr_oscip);
                    if ($verifica != 0) {
                        unset($verifica);
                        $status++;

                        //LOOP das publicações  do Anexo 1 em jornal
                        if ($row_tipo['tipo_id'] == 10 or $row_tipo['tipo_id'] == 19) {
                            foreach ($projeto_status as $status_projeto => $nome_status) : ?>
                                <a class="show seta_fechado" id="<?= $status ?>" href=".<?= $status ?>" onClick="return false" style="text-transform:uppercase;">
                                    <?php echo $row_tipo['tipo_nome']; ?> - <?php echo $nome_status; ?>
                                </a>
                                <table class="<?= $status ?>" style="width:100%; display:none;">
                                    <tr>
                                        <td>
                                            <?php
                                            //verifica as regiões ativas e inativas
                                            if ($status_projeto == 1) {
                                                $qr_regiao = mysql_query("SELECT * FROM regioes WHERE status = '1' AND status_reg = '1' AND id_master = '$Master'");
                                            } else {
                                                $qr_regiao = mysql_query("SELECT * FROM regioes WHERE status = '0' OR status_reg = '0' AND id_master = '$Master'");
                                            }
                                            while ($row_regiao = mysql_fetch_assoc($qr_regiao)):
                                                $qr_projeto = mysql_query("SELECT * FROM projeto WHERE id_master='$Master' AND status_reg = '$status_projeto' AND id_regiao = '{$row_regiao['id_regiao']}' ORDER BY id_projeto ");
                                                while ($row_projeto = mysql_fetch_assoc($qr_projeto)):
                                                    $mostra_oscip = mysql_query("SELECT * FROM obrigacoes_oscip WHERE  status='1'  AND tipo_oscip='{$row_tipo['tipo_nome']}' AND id_master='$Master'  AND id_projeto='{$row_projeto['id_projeto']}' $whereAno ORDER BY data_publicacao DESC")or die("Erro no comando sql");
                                                    $verifica2 = mysql_num_rows($mostra_oscip);
                                                    if ($verifica2 == 0) {
                                                        continue;
                                                    } ?>
                                                    <table cellspacing="1" cellpadding="4" class="relacao" width="100%" style=" width:100%;">
                                                        <tr><td colspan="9">&nbsp;</td></tr>   
                                                        <tr class="novo">
                                                            <td colspan="9" align="center" style="font-size:12px;font-weight:bold;">
                                                                <?php echo $row_projeto['id_projeto'] . ' - ' . $row_projeto['nome'] . ' (' . $row_projeto['regiao'] . ')'; ?>
                                                            </td>
                                                        </tr>
                                                        <tr class="secao">
                                                            <td align="center">Documento </td>  
                                                            <td align="center">Editar</td>
                                                            <?php
                                                            if (in_array($_COOKIE['logado'], $id_permitidos)) {
                                                                echo '<td align="center">Excluir</td>';
                                                            } ?>
                                                            <td align="center"> Anexos </td>
                                                            <td align="center"> Data de publicaç&atilde;o</td>
                                                            <td align="center"> Validade </td>
                                                            <td align="center"> Descric&atilde;o</td>
                                                            <td align="center"> Status </td>
                                                            <td align="center"> Última edição </td>
                                                        </tr>
                                                        <?php
                                                        while ($row_oscip = mysql_fetch_assoc($mostra_oscip)):
                                                            $data = $row_oscip['data_publicacao'];
                                                            $qr_anexo = mysql_query("SELECT * FROM obrigacoes_oscip_anexos WHERE id_oscip='{$row_oscip['id_oscip']}' AND status='1'  ")or die("Erro no comando sql");
                                                            $row_anexo = mysql_fetch_assoc($qr_anexo); ?>
                                                            <tr class=<?php
                                                            if ($data < $data_anterior) {
                                                                echo '"novo2"';
                                                            } else {
                                                                echo '"linha_projeto2"';
                                                            } $data_anterior = $data;
                                                            ?>>
                                                                <td align="center">
                                                                    <?= $row_oscip['tipo_oscip'] ?>
                                                                </td>
                                                                <td align="center">  <a href="edicao_oscip.php?m=<?= $link_master ?>&id=<?= $row_oscip['id_oscip'] ?>"><img src="../../imagens/editar_projeto.png"/></a></td>
                                                                <?php
                                                                if (in_array($_COOKIE['logado'], $id_permitidos)) { ?>
                                                                    <td align="center">
                                                                        <a href="excluir_oscip.php?m=<?= $link_master ?>&id=<?= $row_oscip['id_oscip'] ?>" onClick="return window.confirm('Deseja excluir este <?= $row_oscip['tipo_oscip'] ?> ?');">
                                                                            <img src="../../imagens/lixo.gif" width="25" heigth="25"/>
                                                                        </a> 
                                                                    </td>     
                                                                <?php } ?>		
                                                                <td align="center">
                                                                    <a href="exibir_oscip.php?m=<?= $link_master ?>&id=<?= $row_oscip['id_oscip'] ?>&tp=<?= $row_anexo['tipo_anexo'] ?>" onClick="return hs.htmlExpand(this, {objectType: 'iframe'})"> ver </a>
                                                                </td>
                                                                <td align="center">
                                                                    <?= formato_brasileiro($row_oscip['data_publicacao']); ?>
                                                                </td>
                                                                <td align="center">  
                                                                    <?php
                                                                    if ($row_oscip['periodo'] == 'Período') {
                                                                        list($ano, $mes, $dia) = explode('-', $row_oscip['oscip_data_inicio']);

                                                                        list($ano_2, $mes_2, $dia_2) = explode('-', $row_oscip['oscip_data_termino']);
                                                                        $data_inicio = mktime(0, 0, 0, $mes, $dia, $ano);
                                                                        $data_termino = mktime(0, 0, 0, $mes_2, $dia_2, $ano_2);

                                                                        $total = (int) ($data_termino - $data_inicio) / 86400;
                                                                        echo $total . ' dias';
                                                                    } else {
                                                                        echo $row_oscip['numero_periodo'] . ' ' . $row_oscip['periodo'];
                                                                    } ?>
                                                                </td>
                                                                <td align="center">
                                                                    <?= $row_oscip['descricao'] ?>                      
                                                                </td>
                                                                <td align="center">
                                                                    <?php
                                                                    echo verifica_status($row_oscip['periodo'], $row_oscip['data_publicacao'], $row_oscip['numero_periodo'], $row_oscip['oscip_data_inicio'], $row_oscip['oscip_data_termino']);
                                                                    ?>
                                                                </td>
                                                                <td>
                                                                    <?php
                                                                    $qr_funcionario = mysql_query("SELECT nome FROM funcionario WHERE id_funcionario='{$row_oscip['usuario_atualizacao']}'");
                                                                    $row_funcionario = mysql_fetch_assoc($qr_funcionario);
                                                                    $nome = explode(' ', $row_funcionario['nome']);

                                                                    if ($row_oscip['data_atualizacao'] != '0000-00-00') {
                                                                        echo'Editado por: ' . $nome[0] . ' em ' . formato_brasileiro($row_oscip['data_atualizacao']);
                                                                    } else {

                                                                        $qr_funcionario = mysql_query("SELECT nome FROM funcionario WHERE id_funcionario='$row_oscip[usuario]'");
                                                                        $row_funcionario = mysql_fetch_assoc($qr_funcionario);
                                                                        $nome = explode(' ', $row_funcionario['nome']);

                                                                        echo 'Cadastrado por: ' . $nome[0] . ' em ' . formato_brasileiro($row_oscip['data_usuario']);
                                                                    } ?>
                                                                </td>
                                                            </tr>
                                                            <?php
                                                        endwhile; //oscip
                                                        ?>
                                                    </table>
                                                    <?php
                                                endwhile; //projeto
                                            endwhile; //regiao 
                                            ?>
                                        </td>
                                    </tr>
                                </table>
                                <?php
                            endforeach; //PUBLICAÇÔES
                        } else { ?>
                            <a class="show seta_fechado" id="<?php echo $status; ?>" href=".<?php echo $status; ?>" onClick="return false">
                                <span style="text-transform:uppercase"><?php echo $row_tipo['tipo_nome']; ?> </span>
                            </a>      
                            <table cellspacing="1" cellpadding="4" class="relacao" width="100%" style="display:none;">
                                <tr class="secao">
                                    <?php
                                    if ($row_tipo['tipo_nome'] == 'Ofícios Enviados' OR $row_tipo['tipo_nome'] == 'Ofícios Recebidos') {
                                        echo ' <td align="center">Cod</td>';
                                    } ?>
                                    <td align="center">Documento </td>  
                                    <td align="center">Editar</td>
                                    <?php
                                    if (in_array($_COOKIE['logado'], $id_permitidos)) {
                                        echo ' <td align="center">Excluir</td>';
                                    } ?>
                                    <td align="center">Renovar</td>
                                    <td align="center"> Anexos </td>      
                                    <td align="center"> Data de publicaç&atilde;o</td>
                                    <td align="center"><?php
                                        if ($row_tipo['tipo_nome'] == 'Ofícios Enviados' OR $row_tipo['tipo_nome'] == 'Ofícios Recebidos') {
                                            echo 'N documento';
                                        } else {
                                            echo 'Validade';
                                        }
                                        ?></td>
                                    <td align="center"> Descric&atilde;o</td>
                                    <td align="center">status</td>
                                    <td align="center">Última edição</td>
                                </tr>
                                <?php
                                $auxAno = $contador = 0;
                                while ($row_oscip = mysql_fetch_assoc($qr_oscip)):
                                    
                                    //FAZER O TIPO PUBLICAÇÃO EM ANEXO
                                    $qr_anexo = mysql_query("SELECT * FROM obrigacoes_oscip_anexos WHERE id_oscip='{$row_oscip['id_oscip']}' AND status='1'  ")or die("Erro no comando sql");
                                    $row_anexo = mysql_fetch_assoc($qr_anexo);
                                    $classAno = $styleAno = "";
                                    if ($row_tipo['tipo_nome'] == 'Ofícios Enviados' OR $row_tipo['tipo_nome'] == 'Ofícios Recebidos') { 
                                        if($row_oscip[anoOscip] != $auxAno){ ?>
                                            <tr  class="ano" data-key="<?=$row_oscip[anoOscip]?>" style="background-color: #CCC; cursor: pointer;">
                                                <td colspan="<?=(!in_array($_COOKIE['logado'], $id_permitidos))?10:11?>"><?=$row_oscip[anoOscip]?></td>
                                            </tr>
                                            <?php $auxAno = $row_oscip[anoOscip];
                                        }
                                        $styleAno = "display: none;";
                                        $classAno = " dadosAno $auxAno ";
                                    }
                                    ?>

                                    <tr style="<?= $styleAno;?> " class="<?=$classAno?> <?php
                                    if ($contador != 0) {
                                        echo 'novo2';
                                    } else {
                                        echo 'linha_projeto2';
                                    }
                                    ?>">
                                            <?php
                                            if ($row_tipo['tipo_nome'] == 'Ofícios Enviados' OR $row_tipo['tipo_nome'] == 'Ofícios Recebidos') {
                                                echo ' <td align="center">' . $row_oscip[id_oscip] . '</td>';
                                            } ?>
                                        <td align="center">
                                            <?= $row_oscip['tipo_oscip'] ?>
                                        </td>         
                                        <td align="center">  <a href="edicao_oscip.php?m=<?= $link_master ?>&id=<?= $row_oscip['id_oscip'] ?>"><img src="../../imagens/editar_projeto.png"/></a></td>
                                        <?php
                                        if (in_array($_COOKIE['logado'], $id_permitidos)) { ?>
                                            <td align="center">
                                                <a href="excluir_oscip.php?m=<?= $link_master ?>&id=<?= $row_oscip['id_oscip'] ?>" onClick="return window.confirm('Deseja excluir este <?= $row_oscip['tipo_oscip'] ?> ?');">
                                                    <img src="../../imagens/lixo.gif" width="25" heigth="25"/>
                                                </a> 
                                            </td>     
                                        <?php } ?>    
                                        <?php
                                        if ($row_oscip['periodo'] != 'Indeterminado') { ?>
                                            <td class="center"><img src="../../imagens/renovar_projeto.png" title="Renovar" class="bt-image" data-type="renovar" data-key="<?php echo $row_oscip['id_oscip']; ?>" data-master="<?php echo $Master; ?>"/></td>  
                                        <?php } else { ?>
                                            <td align="center"></td>  
                                        <?php } ?>
                                        <td align="center">
                                            <a href="exibir_oscip.php?m=<?= $link_master ?>&id=<?= $row_oscip['id_oscip'] ?>&tp=<?= $row_anexo['tipo_anexo'] ?>" onClick="return hs.htmlExpand(this, {objectType: 'iframe'})"> ver </a>
                                        </td>           
                                        <td align="center">
                                            <?= formato_brasileiro($row_oscip['data_publicacao']); ?>
                                        </td>
                                        <td align="center"> 
                                            <?php
                                            //solicitado pela Regiane dia 19/09/2014
                                            if ($row_tipo['tipo_nome'] == 'Ofícios Enviados' OR $row_tipo['tipo_nome'] == 'Ofícios Recebidos') {
                                                echo $row_oscip['numero_oscip'];
                                            } else {
                                                if ($row_oscip['periodo'] == 'Período') {
                                                    list($ano, $mes, $dia) = explode('-', $row_oscip['oscip_data_inicio']);

                                                    list($ano_2, $mes_2, $dia_2) = explode('-', $row_oscip['oscip_data_termino']);
                                                    $data_inicio = mktime(0, 0, 0, $mes, $dia, $ano);
                                                    $data_termino = mktime(0, 0, 0, $mes_2, $dia_2, $ano_2);

                                                    $total = ($data_termino - $data_inicio) / 86400;

                                                    echo (int) $total . ' dias';
                                                } else {
                                                    echo $row_oscip['numero_periodo'] . ' ' . $row_oscip['periodo'];
                                                }
                                            } ?>
                                        </td>
                                        <td align="center">
                                            <?= $row_oscip['descricao'] ?>
                                        </td>
                                        <td align="center">
                                            <?php
                                            //codição para CRF
                                            if ($row_tipo['tipo_nome'] == 'Ofícios Enviados' OR $row_tipo['tipo_nome'] == 'Ofícios Recebidos') {
                                                if ($row_tipo['tipo_nome'] == 'Ofícios Enviados') {
                                                    $oficio = 'Ofícios Recebidos'; 
                                                } else if ($row_tipo['tipo_nome'] == 'Ofícios Recebidos') {
                                                    $oficio = 'Ofícios Enviados'; 
                                                }
                                                $sqlResposta = mysql_query("SELECT id_oscip, descricao FROM obrigacoes_oscip WHERE  status='1' AND tipo_oscip = '$oficio' AND id_master='{$usuario['id_master']}' AND resp_env_rec = {$row_oscip['id_oscip']} ORDER BY descricao DESC;");
                                                if (mysql_num_rows($sqlResposta) > 0) {
                                                    $rowResposta = mysql_fetch_assoc($sqlResposta); ?>
                                                        <a href="exibir_oscip.php?m=<?= $link_master ?>&id=<?= $rowResposta['id_oscip'] ?>&tp=<?= $row_anexo['tipo_anexo'] ?>" onClick="return hs.htmlExpand(this, {objectType: 'iframe'})"> Resposta em <?=$oficio . " " . $rowResposta['id_oscip']?> </a>
                                                    <?php
                                                } else if ($row_oscip['resp_env_rec'] > 0){ ?>
                                                        <a href="exibir_oscip.php?m=<?= $link_master ?>&id=<?= $row_oscip['resp_env_rec'] ?>&tp=<?= $row_anexo['tipo_anexo'] ?>" onClick="return hs.htmlExpand(this, {objectType: 'iframe'})"> Resposta de <?=$oficio . " " . $row_oscip['resp_env_rec']?> </a>
                                                    <?php
                                                } else {
                                                    echo verifica_status($row_oscip['periodo'], $row_oscip['data_publicacao'], $row_oscip['numero_periodo'], 0, $row_oscip['oscip_data_termino']);
                                                }
                                            } else {
                                                echo verifica_status($row_oscip['periodo'], $row_oscip['data_publicacao'], $row_oscip['numero_periodo'], 0, $row_oscip['oscip_data_termino']);
                                            } ?>
                                        </td>
                                        <td>
                                            <?php
                                            $qr_funcionario = mysql_query("SELECT nome FROM funcionario WHERE id_funcionario='$row_oscip[usuario_atualizacao]'");
                                            $row_funcionario = mysql_fetch_assoc($qr_funcionario);
                                            $nome = explode(' ', $row_funcionario['nome']);

                                            if ($row_oscip['data_atualizacao'] != '0000-00-00') {
                                                echo'Editado por: ' . $nome[0] . ' em ' . formato_brasileiro($row_oscip['data_atualizacao']);
                                            } else {

                                                $qr_funcionario = mysql_query("SELECT nome FROM funcionario WHERE id_funcionario='$row_oscip[usuario]'");
                                                $row_funcionario = mysql_fetch_assoc($qr_funcionario);
                                                $nome = explode(' ', $row_funcionario['nome']);

                                                echo 'Cadastrado por: ' . $nome[0] . ' em ' . formato_brasileiro($row_oscip['data_usuario']);
                                            } ?>
                                        </td>
                                    </tr>
                                    <?php
                                    $contador++;
                                endwhile;
                            } ?>
                        </table>
                    <?php } ?>
                    </table> 
                <?php endwhile; ?>     
            </div> 
            <p>
                <br/>
                <input type="button" name="btn" id="btn" value="Histórico" />
            </p>  
            <center> <div id="rodape"><?php include('include/rodape.php'); ?></div>
            </center>
        </div>
    </body>
</html>