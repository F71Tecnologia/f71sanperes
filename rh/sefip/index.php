<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
} else {
    include('../../conn.php');
    include('../../classes/funcionario.php');
    include('../../classes_permissoes/botoes.class.php');
//    $ids_logado = array(87,255,260);
    
    $btn = new Botoes();
    
    $Fun = new funcionario();
    $Fun->MostraUser(0);
    $Master = $Fun->id_master;
}

if ($_GET['excluir'] == true) {
    if ($_GET['tipo'] == 1) {
        mysql_query("DELETE FROM sefip WHERE mes = '$_GET[mes]' AND ano = '$_GET[ano]' AND tipo_sefip = '1' LIMIT 1");
    } else {
        mysql_query("DELETE FROM sefip WHERE regiao = '$_GET[regiao]' AND projeto = '$_GET[projeto]' AND mes = '$_GET[mes]' AND ano = '$_GET[ano]' AND folha = '$_GET[folha]' AND tipo_sefip = '2' LIMIT 1");
    }
    header("Location: index.php?regiao=$_GET[regiao]");
}


$qr_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]'");
$row_user = mysql_fetch_assoc($qr_user);

$qr_regioes = mysql_query("SELECT * FROM regioes WHERE id_master = '$row_user[id_master]'");
while ($row_regiao = mysql_fetch_assoc($qr_regioes)):


    $regioes[] = $row_regiao['id_regiao'];


endwhile;
$regioes = implode(',', $regioes);
?>
<html>
    <head>
        <title>Gerar SEFIP</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <link rel="stylesheet" type="text/css" href="../../novoFinanceiro/style/form.css"/>
        <!-- higtslide -->
        <script type="text/javascript" src="../../js/highslide-with-html.js"></script>
        <link href="../../js/highslide.css" rel="stylesheet" type="text/css">
        <script type="text/javascript">
            hs.graphicsDir = '../../images-box/graphics/';
            hs.outlineType = 'rounded-white';
            hs.showCredits = false;
            hs.wrapperClassName = 'draggable-header';
        </script>
        <!-- higtslide -->
        <link href="../css/estrutura.css" rel="stylesheet" type="text/css">
        <link href="../../novoFinanceiro/style/form.css" rel="stylesheet" type="text/css">
        <script src="../../jquery/jquery-1.4.2.min.js" type="text/javascript"></script>
        <script src="../../jquery/jquery.tools.min.js" type="text/javascript"></script>
        <script language="javascript">
            function Reload() {
                setTimeout("location.href = '<?= $_SERVER['PHP_SELF'] ?>?regiao=<?= $_GET['regiao'] ?>&aberto=true'", 5000);
            }
            $(function() {
                indice = 0;
                $('.folha_mes').tooltip({
                    tip: '.tooltip',
                    onBeforeShow: function() {
                        var title = this.getTip();
                        var indice = 0;

                        title.find('.confirmadata').click(function() {
                            var href = $(this).parent().prev().attr('href');
                            var data = $(this).prev().val();
                            $(this).attr('href', href + '&data=' + data);
                            $(this).click();
                            /*window.open(href+'&data='+data, '_blank');
                             window.location.reload();*/
                        });
                        title.find('.dataSefip').click(function() {

                            $(this).next().fadeIn();
                            title.find('.date').keyup(function() {
                                var valor = $(this).val();
                                if (valor.length == 2 || valor.length == 5) {
                                    $(this).val(valor + "/");
                                }
                                var matriz = valor.split('/');
                                if (matriz[0] > 31) {
                                    alert("Digite um dia v�lido!");
                                    $(this).val('');
                                    return false;
                                }
                                if (matriz[1] > 12) {
                                    alert("Digite um mes v�lido!");
                                    $(this).val(matriz[0] + '/');
                                    return false;
                                }
                                if (matriz[2] > 2050) {
                                    alert("Digite um ano v�lido!");
                                    $(this).val(matriz[0] + '/' + matriz[1] + '/');
                                    return false;
                                }

                            });
                        });
                    },
                    onBeforeHide: function() {
                        var title = this.getTip();
                        title.find('.dataSefip').next().hide();
                        title.find('.date').val('');
                    }
                });

                $('.ano').click(function() {
                    $('.ano').not(this).find('table').hide();
                    $('.ano').find('.titulo').css('background-color', '#F1F1F1');
                    $(this).find('.titulo').css('background-color', '#bbb');
                    $(this).find('table').toggle();
                });


                // recisao
                $('.ano_recisao').click(function() {
                    $('.ano_recisao').not(this).next('.meses_recisao').slideUp('slow');
                    $(this).next('.meses_recisao').slideToggle('slow');
                });
                $('.mes_recisao').click(function() {
                    $('.mes_recisao').not(this).next().slideUp('slow');
                    $('.mes_recisao').removeClass('mes_focus');
                    $(this).addClass("mes_focus");
                    $(this).next().slideToggle('slow');
                });

                $('input[name*=data]').click(function() {
                    alert('ISSO AI');
                });

            });
        </script>
        <style type="text/css">
            .ano {
                text-align:center;
            }
            .ano table {
                display:none;
            }
            .folha_mes {
                cursor:pointer;
                width:100%;
            }
            .titulo {
                background-color:#F1F1F1; cursor:pointer; font-size:13px; padding:4px 0px 4px 0px; width:100%; text-align:center; font-weight:bold; margin-top:10px; clear:both;
            }
            .tooltip {
                display:none; background-color:#fff; border:1px solid #777; padding:5px; font-size:13px; -moz-box-shadow:2px 2px 11px #666; -webkit-box-shadow:2px 2px 11px #666; text-align:left; line-height:30px;
            }
            .tooltip a {
                color:#222; text-decoration:none;
            }
            .dados {
                font-size:13px;
            }
            .cabecalho {
                font-weight: bold; font-size:13px;
            }
            .ano_recisao{
                padding:4px; margin:5px 0px; background-color:#F1F1F1; text-align:center; cursor:pointer;
            }
            .mes_recisao{
                padding:0px 10px; margin: 3px 0px; cursor:pointer;  background-color:#F9F7F7;
            }
            .mes_focus{
                padding:0px 10px; margin: 3px 0px; cursor:pointer;  background-color:#CCC;
            }
            .recindidos table{
                font-size:12px;
            }


            #form{

                font-size:13px;	
                background-color: #F7F7F7;
            }

            #form h4{
                text-align:center;	
            }

        </style>
    </head>
    <body>
        <table align="center" cellpadding="0" cellspacing="0" class="corpo" id="topo">
            <tr>
                <td align="right"><?php include('../../reportar_erro.php'); ?></td>
            </tr>
            <tr>
                <td align="center">
                    <img src="imagens/logo_sefip.jpg" width="357" height="150">
                </td>
            </tr>
            <tr>
                <td>

                    <?php
                    $meses = array('Janeiro' => '01', 'Fevereiro' => '02', 'Mar�o' => '03', 'Abril' => '04', 'Maio' => '05', 'Junho' => '06', 'Julho' => '07', 'Agosto' => '08', 'Setembro' => '09', 'Outubro' => '10', 'Novembro' => '11', 'Dezembro' => '12', 'D�cimo Terceiro' => '13');

                    // Loop dos Anos

//                    for ($ano = 2009; $ano <= date('Y'); $ano++) {
                    for ($ano = date('Y'); $ano >= '2009'; $ano--){
                        $verifica_folha_ano = mysql_num_rows(mysql_query("SELECT * FROM rh_folha WHERE regiao IN($regioes) AND ano = '$ano'"));
                        if ($verifica_folha_ano == 0)
                            continue;
                        ?>


                        <div class="ano">
                            <div class="titulo">FOLHAS DE PAGAMENTO <span class="destaque"><?= $ano ?></span></div>

                            <table cellpadding="4" cellspacing="0" class="relacao">
                                <tr class="secao">
                                    <td width="50%">M�s Referente</td>
                                    <td width="50%" align="center">Total de Participantes</td>
                                </tr>

                                <?php
                                // Loop dos Meses

                                foreach ($meses as $nome_mes => $mes) {

                                    if ($mes != "13") {
                                        $qr_folha = mysql_query("SELECT rh_folha.projeto, rh_folha.regiao, rh_folha.id_folha, projeto.nome AS nome_projeto, regioes.regiao AS nome_regiao,
                                                                COUNT( rh_folha_proc.id_clt ) AS total_participante
										FROM rh_folha
										INNER JOIN projeto ON projeto.id_projeto = rh_folha.projeto
										INNER JOIN regioes ON regioes.id_regiao = rh_folha.regiao
										INNER JOIN rh_folha_proc ON rh_folha_proc.id_folha = rh_folha.id_folha
										WHERE rh_folha.status = '3'
										AND rh_folha.mes = '$mes'
										AND rh_folha.ano = '$ano'
										AND rh_folha.regiao IN($regioes)
										AND rh_folha.terceiro != '1' 
										AND rh_folha_proc.status =3
										GROUP BY rh_folha_proc.id_folha
                                                ");

                                        $total_folha = mysql_num_rows($qr_folha);
                                    } else {
                                        $qr = "SELECT rh_folha.projeto, rh_folha.regiao, rh_folha.id_folha, projeto.nome AS nome_projeto, regioes.regiao AS nome_regiao,
                                                                COUNT( rh_folha_proc.id_clt ) AS total_participante
										FROM rh_folha
										INNER JOIN projeto ON projeto.id_projeto = rh_folha.projeto
										INNER JOIN regioes ON regioes.id_regiao = rh_folha.regiao
										INNER JOIN rh_folha_proc ON rh_folha_proc.id_folha = rh_folha.id_folha
										WHERE rh_folha.status = '3' AND
										-- AND rh_folha.mes = '12'
                                                                                (rh_folha.mes = '12' OR (rh_folha.mes >= '11' AND rh_folha.tipo_terceiro=3))
										AND rh_folha.ano = '$ano'
										AND rh_folha.regiao IN($regioes)
										AND rh_folha.terceiro = '1'
										AND rh_folha_proc.status =3
										GROUP BY rh_folha_proc.id_folha";
                                        $qr_folha = mysql_query($qr);
                                        $total_folha = mysql_num_rows($qr_folha);
                                        echo "<!-- {$qr} -->";
                                    }
                                    
                                    if (!empty($total_folha)) {
                                        ?>		   

                                        <tr class="linha_<?php if ($cor++ % 2 == 0) { echo 'um'; } else { echo 'dois'; } ?>">
                                            <td>
                                                <div class="folha_mes" title="<span style='color:#c30;'><?= $nome_mes ?></span><br><span style='line-height:normal;'>

                                                     <?php
                                                     while ($folha = mysql_fetch_assoc($qr_folha)) {


                                                         @$nome_projeto = $folha['nome_projeto'];
                                                         $projeto = $folha['projeto'];
                                                         $nome_regiao = $folha['nome_regiao'];
                                                         $regiao = $folha['regiao'];


                                                         $total_participantes = $folha['total_participante'];
                                                         $total_geral_participantes += $total_participantes;

                                                         $qr_verifica_sefip = mysql_query("SELECT * FROM sefip WHERE mes = '$mes' AND ano = '$ano' AND regiao = '$regiao' AND projeto = '$projeto' AND folha = '$folha[id_folha]' AND tipo_sefip = '2'");
                                                         $row_sefip = mysql_fetch_assoc($qr_verifica_sefip);
                                                         $verifica_sefip = mysql_num_rows($qr_verifica_sefip);
                                                         
                                                         $parte_folha = (!empty($row_sefip['parte_folha']) and $row_sefip['parte_folha'] != 0)? '_'.$row_sefip['parte_folha'] :'' ;
                                                      
                                                         if (!empty($verifica_sefip)) {
                                                             if($btn->verifica_permissao(196)){
                                                                echo "<label style='color:#C30'>GERADA</label>";
                                                                echo "<a href='sefiptexto2.php?mes=" . $mes . "&ano=" . $ano . "&regiao=" . $regiao . "&projeto=" . $projeto . "&folha=" . $folha['id_folha'] . "' onClick='return false' class='dataSefip' title='Gerar SEFIP F71'>" . $nome_projeto . " - " . $nome_regiao . " (" . $total_participantes . ")</a>";
                                                                echo "<div style='display:none;'>
                                                                        <input type='text' name='date' class='date' size='7'/>
                                                                        <a href='#' class='confirmadata' style='color:#993'>Criar SEFIP F71</a>
                                                                    </div>
                                                                    <br>";
                                                             }else{
                                                                echo "<a href='arquivos/{$regiao}_{$projeto}_{$mes}_{$ano}{$parte_folha}.re' target='_blank' title='Visualizar SEFIP'>{$nome_projeto} - {$nome_regiao} ({$total_participantes})</a> 
                                                                        <a href='index.php?excluir=true&regiao={$regiao}&projeto={$projeto}&mes={$mes}&ano={$ano}&folha={$folha['id_folha']}' title='Excluir SEFIP' style='color:#C30'>excluir</a><br>";
                                                             }
                                                         } else {
                                                             echo "<a href='sefiptexto2.php?mes=" . $mes . "&ano=" . $ano . "&regiao=" . $regiao . "&projeto=" . $projeto . "&folha=" . $folha['id_folha'] . "' onClick='return false' class='dataSefip' title='Gerar SEFIP'>" . $nome_projeto . " - " . $nome_regiao . " (" . $total_participantes . ")</a>";
                                                             echo "<div style='display:none;'>
							        <input type='text' name='date' class='date' size='7'/>
									   <a href='#' class='confirmadata' style='color:#993'>Criar SEFIP</a>
							      </div><br>";
                                                         }
                                                     }
                                                     ?> 

                                                     <br></span>

                                                     <?php
                                                     
                                                    if($_COOKIE['logado'] == 1){
                                                         
                                                         /* $qr_mesmo_cnpj = mysql_query("SELECT A.cnpj, GROUP_CONCAT(C.regiao) as nomes_regiao, GROUP_CONCAT(C.id_regiao) as ids_regiao FROM rhempresa as A
                                                                                               INNER JOIN rh_folha as B
                                                                                               ON A.id_regiao = B.regiao
                                                                                               INNER JOIN regioes as C
                                                                                               ON C.id_regiao = B.regiao
                                                                                               WHERE B.mes = $mes AND B.ano = $ano AND B.status = 3
                                                                                               GROUP BY A.cnpj
                                                                                               HAVING  COUNT(A.cnpj) > 1");
                                                            while($row_mesmo_cnpj = mysql_fetch_assoc($qr_mesmo_cnpj)){

                                                                    echo '<strong>UNIDADES COM O CNPJ '.$row_mesmo_cnpj['cnpj'].'</strong>'; 

                                                                    echo "<a href='sefiptexto3.php?mes=" . $mes . "&ano=" . $ano . "&regioes=" . $row_mesmo_cnpj['ids_regiao']."' onClick='return false' class='dataSefip' title='Gerar SEFIP' style='color:#984d4d'> Gerar </a>";
                                                                     echo "<div style='display:none;'>
                                                                        <input type='text' name='date' class='date' size='7'/>
                                                                                   <a href='#' class='confirmadata' style='color:#993'>Criar SEFIP</a>
                                                                      </div><br>";                                                            
                                                            }                                                   
                                                     */
                                                     }
                                                     
                                                     
                                                     
                                                     
                                                     $qr_verifica_sefip = mysql_query("SELECT * FROM sefip WHERE mes = '$mes' AND ano = '$ano' AND tipo_sefip = '1'");
                                                     $verifica_sefip = mysql_num_rows($qr_verifica_sefip);

                                                     if (!empty($verifica_sefip)) {

                                                         echo "<a href='arquivos/" . $mes . "_" . $ano . ".re' target='_blank'>Visualizar SEFIP</a> <a href='index.php?excluir=true&mes=" . $mes . "&ano=" . $ano . "&tipo=1' title='Excluir SEFIP' style='color:#C30'>excluir</a><br>";
                                                     } else {

                                                         echo "<a href='sefiptexto.php?mes=$mes&ano=$ano&parte=1&parte_sefip=1&tipo_sefip=1' target='_blank' onClick='Reload()'>Gerar SEFIP</a>";

                                                         echo "<form name='form' action='simulacao/sefiptexto.php?mes=$mes&ano=$ano&parte=1&parte_sefip=1' method='post' id='form'>";
                                                         /*  echo '<h4>Simula��o do SEFIP</h4>';
                                                           $qr_folha2 = mysql_query("SELECT *
                                                           FROM rh_folha
                                                           WHERE status = '3'
                                                           AND mes = '$mes'
                                                           AND ano = '$ano'
                                                           AND regiao IN($regioes)
                                                           AND terceiro != '1'");
                                                           while ($row_folha = mysql_fetch_assoc($qr_folha2)):

                                                           $nome_regiao = mysql_result(mysql_query("SELECT regiao FROM regioes WHERE id_regiao = '$row_folha[regiao]'"), 0);

                                                           echo "<input type='radio' name='id_folha' value='" . $row_folha['id_folha'] . "' />" . $nome_regiao . '<br>';


                                                           endwhile;


                                                           echo "
                                                           <input type='submit' name='simular' value='SIMULAR SEFIP'/>
                                                           </form>
                                                           "; */
                                                     }
                                                     ?>"> <?= $nome_mes ?>
                                                </div>
                                            </td>
                                            <td align="center"><?= $total_geral_participantes ?></td>
                                        </tr>  
                                        <?php
                                        unset($total_geral_participantes);
                                    }
                                } // Fim do Loop dos Meses 
                                ?>
                            </table>
                        </div>  
                    <?php } // Fim do Loop dos Anos   ?>
                </td>
            </tr>
            <!-- GRRF criado por maikom james dia 01/10/2010  -->

            <?php
            if($_COOKIE['logado'] == 260){
                exit();
            }
            ?>
            
            <tr>
                <td>
                    <?php
                    $query_rescisao = mysql_query("SELECT * FROM rh_recisao");
                    ?> 
                    <?php //for ($ano = 2009; $ano <= date('Y'); $ano++): ?>
                    <?php for ($ano = (date('m') == 12) ? date('Y') + 1 : date('Y'); $ano >= '2009'; $ano--): ?>
                        <div class="ano_recisao">GRRF
                            <span class="destaque"><?= $ano ?></span>
                        </div>
                        <div class="meses_recisao" style="display:none;">
                            <?php foreach ($meses as $nome_mes => $num_mes): ?>
                                <?php 
                                
                                // 66, 64, 63 , 66
                                $sql_rescisao = "
                                                    SELECT 
                                                    rh_recisao.fgts_cod,
                                                    rhstatus.cod_movimentacao,
                                                    rh_recisao.id_clt,rh_recisao.nome,
                                                    regioes.id_regiao,regioes.regiao,
                                                    projeto.id_projeto,projeto.nome as nome_projeto
                                                     FROM (rh_recisao LEFT JOIN regioes
                                                    ON rh_recisao.id_regiao = regioes.id_regiao)
                                                    LEFT JOIN projeto 
                                                    ON rh_recisao.id_projeto = projeto.id_projeto
                                                    LEFT JOIN rhstatus ON rhstatus.id_status=rh_recisao.fgts_cod
                                                    WHERE MONTH(rh_recisao.data_demi) = '$num_mes'
                                                    AND YEAR(rh_recisao.data_demi) = '$ano'
                                                    AND rh_recisao.status = '1'
                                                    AND regioes.id_regiao IN($regioes)
                                                        AND rh_recisao.rescisao_complementar = 0
                                                    ORDER BY regioes.id_regiao ASC
                                            ";
//                                echo $sql_rescisao.'<br>';
                                $qr_recisao = mysql_query($sql_rescisao);
                                ?>
                                <div class="mes_recisao">
                                    <?= $nome_mes ?>
                                </div>

                                <div class='recindidos' style="display:none;">
                                    <?php if (isset($_GET['dev'])) { ?>
                                    <form method="post" action="corpo_grrf.php" target="_blank" >                                
                                            <input type="text" name="mes" value="<?= $num_mes; ?>" />
                                            <input type="text" name="ano" value="<?= $ano; ?>" />

                                            <input type="submit" value="Gerar" />
                                        <?php } ?>
                                            <table width="100%" cellpadding="2" cellspacing="0">
                                                <tr>                                                    
                                                    <th colspan="2">Projeto</th>
                                                    <th>Nome</th>
                                                    <th>PIS</th>
                                                    <th  align="center">COD.</th>
                                                    <th  align="center">GRRF</th>
                                                </tr>
                                        <?php while ($row_recisao = mysql_fetch_assoc($qr_recisao)): 
                                            
                                            //cod de saque 01 04 tem grrf
//                                            if(in_array(strtolower($row_recisao['cod_movimentacao']), array('i1','i2', 'i3', 'i4', 'l'))){
                                            
                                            ?>
                                            <?php
                                            $sql_row = "SELECT * FROM grrf WHERE id_clt = '$row_recisao[id_clt]' AND mes = '$num_mes' AND ano = '$ano' AND id_projeto = '$row_recisao[id_projeto]' AND id_regiao = '$row_recisao[id_regiao]'";
                                            $qr_grrf = mysql_query($sql_row);
                                            $num_grrf = mysql_num_rows($qr_grrf);
                                            ?>

                                            <?php if (isset($_GET['dev'])) { ?>
                                                <input type="hidden" name="clt[<?= $row_recisao[id_clt] ?>]" value="<?= $row_recisao[id_clt] ?>" />
                                            <?php } ?>
                                            
                                                <tr style="display: none" >
                                                    <td colspan="4"><?= $sql_row ?></td>
                                                </tr>
                                                <tr <?php
                                                if (!empty($num_grrf)) {
                                                    echo "bgcolor=\"#FF8A8A\"";
                                                }
                                                ?> >
                                                    <td ><?= $row_recisao['id_regiao'] . ' - ' . $row_recisao['regiao'] ?></td>
                                                    <td ><?= $row_recisao['id_projeto'] . ' - ' . $row_recisao['nome_projeto'] ?></td>
                                                    <td ><?= $row_recisao['id_clt'] . ' - ' . $row_recisao['nome'] ?></td>
                                                     <?php 
                                                    $idClt = $row_recisao['id_clt'];
                                                    $sqlPis = "SELECT pis FROM rh_clt WHERE id_clt = $idClt";
                                                    $queryPis = mysql_query($sqlPis);
                                                    $arrPis = mysql_fetch_assoc($queryPis);
                                                    ?>
                                                    <td align="center"><?= $arrPis['pis'] ?></td>
                                                    <td  align="center"><?= $row_recisao['cod_movimentacao'] ?></td>
                                                    <td  align="center"><a href="view/confirmacao.grrf.php?mes=<?= $num_mes ?>&ano=<?= $ano ?>&clt=<?= $row_recisao['id_clt'] ?>&regiao=<?= $row_recisao['id_regiao'] ?>&projeto=<?= $row_recisao['id_projeto'] ?>" onClick="return hs.htmlExpand(this, {objectType: 'iframe'})"><img border="0" src="../imagensrh/recisao.jpg" ></a>
                                                        <?php
                                                        $get_limpa_grrf = isset($_GET['limpa_grrf']) ? $_GET['limpa_grrf'] : FALSE;
                                                        $get_id_clt = isset($_GET['id_clt']) ? $_GET['id_clt'] : FALSE;

                                                        if (($get_limpa_grrf == 1) && ($row_recisao['id_clt'] == $get_id_clt)) {
                                                            while ($grrf_registro = mysql_fetch_array($qr_grrf)) {
                                                                $sql_del_grrf = 'DELETE FROM `f71_netsorrindo`.`grrf` WHERE `grrf`.`id` = ' . $grrf_registro['id'] . ' LIMIT 1';
//                                                            echo $sql_del_grrf.'<br>';
                                                                mysql_query($sql_del_grrf);
                                                            }
                                                        }
                                                        ?>
                                                        <?php if (isset($_GET['dev'])) { ?>
                                                            <a href="?dev&limpa_grrf=1&id_clt=<?= $row_recisao['id_clt']; ?>" >limpar grrf</a>
                                                        <?php } ?>
                                                    </td>
                                                </tr>
                                            
                                            <?php // } 
                                            endwhile; ?>
                                                </table>
                                        <?php if (isset($_GET['dev'])) { ?>
                                        </form>
                                    <?php } ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endfor; ?>
                </td>
            </tr>
            <!-- GRRF criado por maikom james dia 01/10/2010  -->
        </table>
    </body>
</html>