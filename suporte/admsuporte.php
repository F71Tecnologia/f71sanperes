<?php
if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="../login.php">Logar</a>';
    exit;
}

if (isset($_REQUEST['fechados'])) {
    print_r($_REQUEST['fechados']);
}

include('../conn.php');
$id_user = $_COOKIE['logado'];

if (empty($_REQUEST['tela'])) {
    $tela = '1';
} else {
    $tela = $_REQUEST['tela'];
}

function suporte($status, $prioridade) {
    switch ($status) {
        case 1:
            $imagem = '<img src="imgsuporte/aberto.png" alt="Aberto" title="Aberto" width="18" height="18" />';
            break;
        case 2:
            $imagem = '<img src="imgsuporte/respondido.png" alt="Respondido" title="Respondido" width="18" height="18" />';
            break;
        case 3:
            $imagem = '<img src="imgsuporte/replicado.png" alt="Replicado" title="Replicado" width="18" height="18" />';
            break;
        case 4:
            $imagem = '<img src="imgsuporte/finalizado.png" alt="Fechado" title="Fechado" width="18" height="18" />';
            break;
    }

    switch ($prioridade) {
        case 1:
            $prioridade_cor = '#FC9';
            $prioridade_nome = 'Baixa';
            break;
        case 2:
            $prioridade_cor = '#FC6';
            $prioridade_nome = 'Média';
            break;
        case 3:
            $prioridade_cor = '#F90';
            $prioridade_nome = 'Alta';
            break;
        case 4:
            $prioridade_cor = '#F30';
            $prioridade_nome = 'Urgente';
            break;
    }

    $retorno = array($prioridade_cor, $prioridade_nome, $imagem);
    return $retorno;
}

$tipos_suporte = array('1' => 'INFORMA&Ccedil;&Atilde;O', '2' => 'RECLAMA&Ccedil;&Atilde;O', '3' => 'INCLUS&Atilde;O', '4' => 'EXCLUS&Atilde;O', '5' => 'ERRO', '6' => 'SUGEST&Atilde;O', '7' => 'ALTERA&Ccedil;&Atilde;O');
?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../favicon.ico">
        <link rel="stylesheet" href="../net1.css" type="text/css" />
        <link rel="stylesheet" href="../lightbox.css" type="text/css" />
        <script type="text/javascript" src="../js/jquery-1.3.2.js"></script>
        <script type="text/javascript" src="../js/prototype.js"></script>
        <script type="text/javascript" src="../js/scriptaculous.js?load=effects,builder"></script>
        <script type="text/javascript" src="../js/lightbox.js"></script>
        <title>Suporte</title>
        <script>
            jQuery(document).ready(function(){
                jQuery(".bt").click(function(){
                    var status = jQuery(this).attr("data-key");
                    if(status!="todos"){
                        jQuery(".mutacao").addClass("hidden");
                        jQuery(".mutacao[data-type="+status+"]").removeClass("hidden");
                    }else{
                        jQuery(".mutacao").removeClass("hidden");
                    }
                });
                
                jQuery(".fechado").hide();                
                //jQuery(".j_mostrar").hide();                
                jQuery(".j_visualizarFechado").click(function(){
                    jQuery(this).text("Fechar Visualização");
                    jQuery(".j_mostrar").show(1,function(){
                        jQuery(".fechado").toggle(1, function(){
                            jQuery("body").scrollTop(1350);
                        }); 
                        exit;
                    });  
                });                
            });
            
        </script>
        <style type="text/css">
            body {
                text-align:center; 
            }
            p {
                margin:0px;
            }
            a {
                color:#333; text-decoration:underline;
            }
            #corpo {
                width:90%; margin:0px auto; text-align:left; background-color:#FFF; padding:25px;
            }
            #topo {
                font-weight:bold; font-size:18px; background-color:#f5f5f5;border:1px solid #ddd; padding:20px; width:95%; margin:0px auto; text-align:center;
            }
            p.legendas {
                margin-top:20px; font-size:12px;
            }
            p.legendas img {
                margin-left:12px;
            }
            .secao_pai {
                font-weight:bold; font-size:13px; padding:50px 0px 10px 10px;
            }
            .secao {
                font-weight:bold; text-align:center; font-size:12px; background-color:#f2f6f9;
            }
            .linha_um, .linha_dois {
                text-align:center;
            }
            .linha_um {
                background-color:#f5f5f5;
            }
            .linha_dois {
                background-color:#ebebeb;
            }
            .linha_um td, .linha_dois td {
                border-bottom:1px solid #ccc;
            }
            .secao_resposta {
                background-color:#eee; font-weight:bold; text-align:right; padding-right:4px;
            }
        </style>
    </head>
    <body>
        <div id="corpo">
            <div id="topo">
                <img src="imgsuporte/suporte.png" width="39" height="39" /> SUPORTE
                <p class="legendas">
                    <?php if ($tela == '1') { ?>
                        <img src="imgsuporte/aberto.png" width="12" height="12" class="bt" data-key="1" /> ABERTO
                        <img src="imgsuporte/respondido.png" width="12" height="12" class="bt" data-key="2" /> RESPONDIDO
                        <img src="imgsuporte/replicado.png" width="12" height="12" class="bt" data-key="3" /> REPLICADO
                        <img src="imgsuporte/finalizado.png" width="12" height="12" class="bt" data-key="4" /> FECHADO <br /><br /><a href="javascript:;" style="text-decoration:none; text-transform: lowercase; background: #C30; padding: 10px 50px; color: #fff; border-radius: 3px;" class="j_visualizarFechado">VISUALIZAR TODOS OS CHAMADOS FECHADOS</a>
                       <?php
//                       if(isset($_GET['dev'])){
                        $total_aberto = 0;
                        $total_respondido = 0;
                        $total_replicado = 0;
                        $total_fechado = 0;
                        $sql_sup = "SELECT * FROM suporte";
                        $query_suporte = mysql_query($sql_sup);
                        while($row_suporte = mysql_fetch_array($query_suporte)){
                            if($row_suporte['status']==1){
                                $total_aberto++;
                            }
                            if($row_suporte['status']==2){
                                $total_respondido++;
                            }
                            if($row_suporte['status']==3){
                                $total_replicado++;
                            }
                            if($row_suporte['status']==4){
                                $total_fechado++;
                            }
                        } 
                       ?>  
                        <br><br>
              <!--    <table>
                    <thead>
                        <tr>
                            <th>Status do Chamado</th>
                            <th>Quantidade</th>
                        </tr>
                    </thead>
                  <tbody>
                        <tr>
                            <td>abertos</td>
                            <td><?//= $total_aberto; ?></td>
                        </tr>
                        <tr>
                            <td>respondidos</td>
                            <td><?//= $total_respondido; ?></td>
                        </tr>
                        <tr>
                            <td>replicados</td>
                            <td><?//= $total_replicado; ?></td>
                        </tr>
                        <tr>
                            <td>fechados</td>
                            <td><?//= $total_fechado; ?></td>
                        </tr>
                    </tbody>
                </table>-->
                        <p style="margin:0; padding:0; font-size: 13px;" class="bt" data-key="1"><a href="javascript:;">Total abertos: <?= $total_aberto; ?></a></p>
                        <p style="margin:0; padding:0; font-size: 13px;" class="bt" data-key="2"><a href="javascript:;">Total respondidos: <?= $total_respondido; ?></a></p>
                        <p style="margin:0; padding:0; font-size: 13px;" class="bt" data-key="3"><a href="javascript:;">Total replicados: <?= $total_replicado; ?></a></p>
                        <p style="margin:0; padding:0; font-size: 13px;" class="bt" data-key="4"><a href="javascript:;">Total fechados: <?= $total_fechado; ?></a></p>
                        <?php // } ?>
                        <br><br>
                        
                    <?php } else { ?>
                        <a href="admsuporte.php">Voltar</a>
                    <?php } ?>
                </p>
            </div>

            <?php if ($tela == '1') { //LISTAGEM DE CHAMADOS?>

                <table cellpadding="4" cellspacing="1" width="100%" border="0"  class="j_mostrar" >

                    <?php
                    foreach ($tipos_suporte as $tipo_id => $tipo_nome) {

                        if ($tipo_id == 8) {
                            $filtro1 = "WHERE sup.status = '4'";
                            $filtro2 = NULL;
                            $filtro3 = "ORDER BY sup.id_suporte DESC";
                        } else {
                            $filtro1 = "WHERE sup.tipo    = '$tipo_id'";
                            $filtro2 = "AND   sup.status != '4'";
                            $filtro3 = "ORDER BY sup.prioridade DESC, sup.id_suporte DESC";
                        }

                        $qr_suporte = mysql_query("SELECT sup.id_suporte, sup.assunto, sup.prioridade, sup.status, func.nome1, reg.regiao, date_format(sup.data_cad, '%d/%m/%Y às %H:%i') AS data_cad
							  	   FROM suporte sup
						     INNER JOIN funcionario func ON func.id_funcionario = sup.user_cad
						      LEFT JOIN regioes reg      ON reg.id_regiao = sup.id_regiao
							            $filtro1
							            $filtro2
						                $filtro3");
                        $total_suporte = mysql_num_rows($qr_suporte);


                        if (!empty($total_suporte)) {
                            ?>
                            <tr> 
                                <td colspan="7" class="secao_pai" > <?= $tipo_nome ?> (<?= $total_suporte ?> chamados)  </td>
                            </tr>
                            <tr class="secao">
                                <td width="10%">Chamado</td>
                                <td width="30%">Assunto</td>
                                <td width="18%">Aberto Por</td>
                                <td width="18%">Data de Abertura</td>
                                <td width="12%">Prioridade</td>
                                <td width="12%">Situa&ccedil;&atilde;o</td>
                            </tr>

                            <?php
                            while ($row_suporte = mysql_fetch_array($qr_suporte)) {

                                $suporte = suporte($row_suporte['status'], $row_suporte['prioridade']);
                                ?>

                                <tr class="mutacao linha_<?php
                if ($linha++ % 2 == 0) {
                    echo 'um';
                } else {
                    echo 'dois';
                }
                                ?>" data-type="<?= $row_suporte['status'] ?>">
                                    <td ><a href="admsuporte.php?tela=2&chamado=<?= $row_suporte['id_suporte'] ?>" title="Abrir o chamado"><?= sprintf('%04d', $row_suporte['id_suporte']) ?></a></td>
                                    <td><?= $row_suporte['assunto'] ?></td>
                                    <td><?= $row_suporte['nome1'] ?></td>
                                    <td><?= $row_suporte['data_cad'] ?></td>
                                    <td style="background-color:<?= $suporte[0] ?>;"><?= $suporte[1] ?></td>
                                    <td><?= $suporte[2] ?></td>
                                </tr>

                                <?php
                            }
                        }
                    }
                    ?>

                </table>

                <?php
            } elseif ($tela == '2') {
                //VISUALIZAR CHAMADO
                $user_cad = $_COOKIE['logado'];
                $chamado = $_REQUEST['chamado'];

                $qr_suporte = mysql_query("SELECT *, date_format(data_cad, '%d/%m/%Y às %H:%i') AS data_cad FROM suporte WHERE id_suporte = '$chamado'");
                $row_suporte = mysql_fetch_array($qr_suporte);

                $qr_funcionario = mysql_query("SELECT nome1 FROM funcionario WHERE id_funcionario = '$row_suporte[user_cad]'");
                $funcionario = mysql_result($qr_funcionario, 0);

                $data_cad = date('Y-m-d H:i:s');

                $ocorrencia = $tipos_suporte[$row_suporte['tipo']];

                switch ($row_suporte['prioridade']) {
                    case 1:
                        $prioridade_cor = '#FC9';
                        $prioridade_nome = 'Baixa';
                        break;
                    case 2:
                        $prioridade_cor = '#FC6';
                        $prioridade_nome = 'Média';
                        break;
                    case 3:
                        $prioridade_cor = '#F90';
                        $prioridade_nome = 'Alta';
                        break;
                    case 4:
                        $prioridade_cor = '#F30';
                        $prioridade_nome = 'Urgente';
                        break;
                }

                switch ($row_suporte['exibicao']) {
                    case 1:
                        $exibicao = 'Particular';
                        break;
                    case 2:
                        $exibicao = 'Todos do Grupo';
                        break;
                    case 3:
                        $exibicao = 'Pública';
                        break;
                }

                if (!empty($row_suporte['arquivo'])) {
                    $img = '<a href="arquivos/suporte_' . $row_suporte['id_regiao'] . '_' . $row_suporte['0'] . $row_suporte['arquivo'] . '" rel="lightbox" title="Anexo">Abrir anexo</a>';
                } else {
                    $oculta_anexo = ' style="display:none;"';
                }

                if (!empty($row_suporte['quant'])) {
                    $nome_arquivo = 'historico_chamado_' . $chamado . '.txt';
                } else {
                    $oculta_historico = ' style="display:none;"';
                }
                ?>

                <table cellspacing="1" cellpadding="4" style="border:0; width:100%; margin-top:20px; background-color:#fafafa;">
                    <tr>
                        <td width="15%" class="secao_resposta">Tipo de Ocorr&ecirc;ncia</td>
                        <td width="35%"><?= $ocorrencia ?></td>
                        <td width="15%" class="secao_resposta">Prioridade</td>
                        <td width="35%" style="background-color:<?= $prioridade_cor ?>;"><?= $prioridade_nome ?></td>
                    </tr>
                    <tr>
                        <td class="secao_resposta">Data de Abertura</td>
                        <td><?= $row_suporte['data_cad'] ?></td>
                        <td class="secao_resposta">Aberto Por</td>
                        <td><?= $funcionario ?></td>
                    </tr>
                    <tr>
                        <td class="secao_resposta">Assunto</td>
                        <td><?= $row_suporte['assunto'] ?></td>
                        <td class="secao_resposta">Exibi&ccedil;&atilde;o</td>
                        <td><?= $exibicao ?></td>
                    </tr>
                    <tr>
                        <td class="secao_resposta">Mensagem</td>
                        <td colspan="3"><?= nl2br($row_suporte['mensagem']) ?></td>
                    </tr>
                    <tr<?= $oculta_anexo ?>>
                        <td class="secao_resposta">Anexo</td>
                        <td colspan="3"><?= $img ?></td>
                    </tr>

                    <tr<?= $oculta_historico ?>>
                        <td class="secao_resposta">Histórico</td>
                        <td colspan="3">
                    <tr>
                        <td class="secao_resposta">Página de origem:</td>
                        <td colspan="3"><?= $row_suporte['suporte_pagina'] ?></td>
                    </tr>
                    <?php
                    $filename = "/home/ispv/public_html/intranet/suporte/arquivos/$nome_arquivo";
                    $handle = fopen($filename, "r");
                    $conteudo = fread($handle, filesize($filename));
                    $conteudo = str_replace("\n\r", "<br>", $conteudo);
                    $conteudo = str_replace("\r", "<br>", $conteudo);
                    print $conteudo;
                    fclose($handle);
                    ?>
                    </td>
                    </tr>
                    <?php if (!empty($row_suporte['resposta'])) { ?>
                        <tr>
                            <td colspan="4" class="secao_resposta" style="text-align:left;">Resposta</td>
                        </tr>
                        <tr>
                            <td colspan="4"><?= nl2br($row_suporte['resposta']); ?></td>
                        </tr>
                    <?php } if ($row_suporte['status'] != '2' and $row_suporte['status'] != '4') { ?>

                        <tr>
                            <td colspan="4" align="center">
                                <form action="admsuporte.php" method="post" name="form1">
                                    <table width="100%" border="0" cellspacing="1" cellpadding="4">
                                        <tr>
                                            <td colspan="4" class="secao_resposta" style="text-align:left;">Resposta</td>
                                        </tr>
                                        <tr>
                                            <td colspan="4"><textarea name="resposta" cols="70" rows="7" class="linha" id="StMensagem" 
                                                                      onChange="this.value=this.value.toUpperCase()"></textarea></td>
                                        <tr style="display:none;">
                                            <td class="secao_resposta">Enviar Arquivo</td>
                                            <td colspan="3">
                                                <input name="StAnexo" type="file" class="linha" id="StAnexo" size="40" />                
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">
                                                <input type="hidden" name="chamado" value="<?= $chamado ?>">
                                                <input type="hidden" name="tela" value="3">
                                                <input name="btnOK" type="submit" value="RESPONDER CHAMADO" />
                                            </td>
                                        </tr>
                                    </table>
                                </form>
                            </td>
                        </tr>
                    <?php } if ($row_suporte['status'] != '4') { ?>
                        <tr>
                            <td align="center" colspan="4">
                                <p>&nbsp;</p>
                                <img src="imgsuporte/finalizar.png" width="20" height="20" border="0"> <a href="suporte.php?tela=3&chamado=<?= $chamado ?>&regiao=<?= $row_suporte['id_regiao'] ?>" title="Fechar Chamado">FECHAR CHAMADO DEVIDO A FALTA DE INFORMA&Ccedil;&Otilde;ES OU SOLICITA&Ccedil;&Otilde;ES FORA DE CONTEXTO</a>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            </form>

            <?php
        } else {

            $user_res = $_COOKIE['logado'];
            $chamado = $_REQUEST['chamado'];
            $resposta = $_REQUEST['resposta'];
            $data = date('Y-m-d');

            mysql_query("UPDATE suporte SET resposta = '$resposta', user_res = '$user_res', data_res = '$data', ultima_alteracao = '$data', status = '2' WHERE id_suporte = '$chamado'");
            ?>

            <script>
                alert("Resposta enviada com sucesso!");
                location.href = 'admsuporte.php';
            </script>

        <?php } ?>
    </td>
</tr>
</table>
</div>

<div class="fechado" style="width: 89.7%; background: #FFF; margin: 0 auto; padding: 28px;">        
    <table cellpadding="4" cellspacing="1" width="100%" border="0"  class="j_mostrar" >

        <?php
        foreach ($tipos_suporte as $tipo_id => $tipo_nome) {

            if ($tipo_id == 8) {
                $filtro1 = "WHERE sup.status = '4'";
                $filtro2 = NULL;
                $filtro3 = "ORDER BY sup.id_suporte DESC";
            } else {
                $filtro1 = "WHERE sup.tipo    = '$tipo_id'";
                $filtro2 = "AND   sup.status = '4'";
                $filtro3 = "ORDER BY sup.prioridade DESC, sup.id_suporte DESC";
            }

            $qr_suporte = mysql_query("SELECT sup.id_suporte, sup.assunto, sup.prioridade, sup.status, func.nome1, reg.regiao, date_format(sup.data_cad, '%d/%m/%Y às %H:%i') AS data_cad
							  	   FROM suporte sup
						     INNER JOIN funcionario func ON func.id_funcionario = sup.user_cad
						      LEFT JOIN regioes reg      ON reg.id_regiao = sup.id_regiao
							            $filtro1
							            $filtro2
						                $filtro3");
            $total_suporte = mysql_num_rows($qr_suporte);


            if (!empty($total_suporte)) {
                ?>
                <tr> 
                    <td colspan="7" class="secao_pai" > FECHADOS (<?= $total_suporte ?> chamados)  </td>
                </tr>
                <tr class="secao">
                    <td width="10%">Chamado</td>
                    <td width="30%">Assunto</td>
                    <td width="18%">Aberto Por</td>
                    <td width="18%">Data de Abertura</td>
                    <td width="12%">Prioridade</td>
                    <td width="12%">Situa&ccedil;&atilde;o</td>
                </tr>

                <?php
                while ($row_suporte = mysql_fetch_array($qr_suporte)) {

                    $suporte = suporte($row_suporte['status'], $row_suporte['prioridade']);
                    ?>

                    <tr class="mutacao linha_<?php
            if ($linha++ % 2 == 0) {
                echo 'um';
            } else {
                echo 'dois';
            }
                    ?>" data-type="<?= $row_suporte['status'] ?>">
                        <td ><a href="admsuporte.php?tela=2&chamado=<?= $row_suporte['id_suporte'] ?>" title="Abrir o chamado"><?= sprintf('%04d', $row_suporte['id_suporte']) ?></a></td>
                        <td><?= $row_suporte['assunto'] ?></td>
                        <td><?= $row_suporte['nome1'] ?></td>
                        <td><?= $row_suporte['data_cad'] ?></td>
                        <td style="background-color:<?= $suporte[0] ?>;"><?= $suporte[1] ?></td>
                        <td><?= $suporte[2] ?></td>
                    </tr>

                    <?php
                }
            }
        }
        ?>

    </table>
</div>
</body>
</html>