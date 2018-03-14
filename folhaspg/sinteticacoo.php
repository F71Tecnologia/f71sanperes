<?php include('sintetica_coo/cabecalho_folha.php'); ?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>:: Intranet :: Folha Sint&eacute;tica de <?php
            if ($row_folha['contratacao'] == '3') {
                echo 'Cooperado';
            } elseif ($row_folha['contratacao'] == '4') {
                echo 'Aut&ocirc;nomo PJ';
            }
            ?> (<?= $folha ?>)</title>
        <link href="sintetica_coo/folha.css" rel="stylesheet" type="text/css">
        <link href="../favicon.ico" rel="shortcut icon">
        <link href="../js/highslide.css" rel="stylesheet" type="text/css" />
        <script src="../js/highslide-with-html.js" type="text/javascript"></script>
        <script type="text/javascript" src="../js/jquery-1.8.3.min.js"></script>
        <script type="text/javascript" src="../js/formatavalor.js"></script>
        <script type="text/javascript" src="sintetica_coo/scripts.js"></script>
        <script type="text/javascript">

            hs.graphicsDir = '../images-box/graphics/';
            hs.outlineType = 'rounded-white';
            hs.allowSizeReduction = false;
            //
            /*$(function() {
             console.log("INICIOU");*/
        </script>


        <style type="text/css">
            .valor_hora, .horas_trabalhadas, .rendimentos, .inss, .descontos, .ajuda_custo  {
                text-align:center; border:0; font-size:11px; color:#222; width:100%; font-family:verdana; outline:none; background-color:transparent; cursor:pointer;
            }

            .highslide-html-content { width:600px; padding:0px; }
        </style>
    </head>
    <body>
        <div id="corpo">
            <table cellspacing="4" cellpadding="0" id="topo">

                <td width="15%" rowspan="3" align="center">
                    <img src="../imagens/logomaster<?= mysql_result($qr_projeto, 0, 2) ?>.gif" width="110" height="79">
                </td>
                <td colspan="3" style="font-size:12px;">
                    <b><?= mysql_result($qr_projeto, 0, 1) . ' (' . htmlentities($mes_folha, ENT_COMPAT, 'utf-8') . ')' ?></b>
                </td>
                <td></td>
                </tr>
                <tr>
                    <td width="35%"><b>Data da Folha:</b> <?= $row_folha['data_inicio_br'] . ' &agrave; ' . $row_folha['data_fim_br'] ?></td>
                    <td width="30%"><b>Região:</b> <?= $regiao . ' - ' . mysql_result($qr_regiao, 0, 1) ?></td>
                    <td width="20%"><b>Participantes:</b> <?= $total_participantes ?></td>
                    <td><?php include('../reportar_erro.php'); ?></td>
                </tr>
                <tr>
                    <td><b>Data de Processamento:</b> <?= $row_folha['data_proc_br'] ?></td>
                    <td><b>Gerado por:</b> <?= abreviacao(mysql_result($qr_usuario, 0), 2) ?></td>
                    <td><b>Folha:</b> <?= $folha ?></td>
                    <td></td>
                </tr>
            </table>

            <table cellpadding="0" cellspacing="1" class="folha">
                <tr>
                    <td valign="bottom"><a href="<?= $link_voltar ?>" class="voltar">Voltar</a></td>
                    <td colspan="12" style="color:#C30; font-style:italic; font-size:11px; text-align:right; font-weight:bold; line-height:28px;">Para editar HORAS TRABALHADAS, RENDIMENTOS, DESCONTOS, INSS ou AJUDA DE CUSTO clique sobre o valor do mesmo; para ver QUOTAS clique sobre o mesmo.</td>
                </tr>
            </table>


            <table cellpadding="0" cellspacing="1" class="folha">     
                <tr class="secao">
                    <td width="5%">COD</td>
                    <td width="18%" align="left" style="padding-left:5px;">NOME</td>
                    <td width="6%" class="pequeno">VALOR/HORA</td>
                    <td width="6%">HORAS</td>
                    <td width="6%">BASE</td> 
                    <?php if ($row_folha['terceiro'] > 0) { ?> <td width="10%"> Meses Trabalhados</td><?php } ?>

                    <td width="8%" class="pequeno">RENDIMENTOS</td>
                    <td width="8%" class="pequeno">DESCONTOS</td>
                    <td width="6%">INSS</td>
                    <td width="6%">IRRF</td>
                    <td width="8%">QUOTA</td>
                    <td width="8%" class="pequeno">AJUDA CUSTO</td>
                    <td width="12%">L&Iacute;QUIDO</td>
                    <?php if ($ACOES->verifica_permissoes(71)) { ?>  
                        <td width="6%" class="pequeno">NOTA FISCAL</td>
                    <?php } ?>
                </tr>



                <?php
                
                
                if($_COOKIE['logado'] == 179){
                    echo "<pre>";
                        print_r($row_participante);
                    echo "</pre>";
                }
                
                $cont = 0;
                // Início do Loop dos Participantes da Folha
                while ($row_participante = mysql_fetch_array($qr_participantes)) {
                    $cont += 1;

                    // Id do Participante
                    $cooperado = $row_participante['id_autonomo'];

                    // Link para Relatório
                    $relatorio = str_replace('+', '--', encrypt("$cooperado&folha&$row_participante[id_folha_pro]"));

                    // Calculando a Folha
                    include('sintetica_coo/calculos_folha.php');
                    ?>

                    <tr class="linha_<?= ($linha++ % 2 == 0) ? 'um' : 'dois'; ?> destaque">
                        <td><span class="id_cooperado"><?php echo $cooperado; ?></span></td>
                        <td  align="left" style="padding-left:5px;"><?php echo abreviacao($row_cooperado['nome'], 4, 1); ?></td>
                        <!--<td ><span class="valor_hora"><?php echo formato_real($valor_hora); ?></span></td>-->
                        <td><input name="valor_hora" title="Editar Valor Hora" type="text" onKeyDown="FormataValor(this, event, 19, 2)" value="<?php echo formato_real($valor_hora, 2); ?>" class="valor_hora" /></td>
                        <td ><input name="horas_trabalhadas" title="Editar Horas Trabalhadas" type="text"  value="<?php echo (int) $horas_trabalhadas; ?>" class="horas_trabalhadas" rel="<?php echo $cont; ?>"/></td>
                        <td ><span class="base"><?php echo formato_real($salario_base); ?></span></td>


                        <?php if ($row_folha['terceiro'] > 0) { ?> 
                            <td>

                                <?php
                                $valor = $salario_base;
                                $totalizador_meses_trabalhados += $valor;
                                echo '<span class="meses_trabalhados">' . $meses_trabalhados . '</span> meses - R$ <span class="valor_meses_trabalhados">' . formato_real($valor) . '</span>';
                                /*
                                  list($ano_admissao, $mes_admissao, $dias_admissao) = explode('-', $row_cooperado['data_entrada']);
                                  $data_admissao_segundos = mktime(0, 0, 0, $mes_admissao, $dia_admissao, $ano_admissao);

                                  $data_fim_folha = explode('/', $row_folha['data_fim_br']);

                                  $data_hoje_segundos = mktime(0, 0, 0, $data_fim_folha[1], $data_fim_folha[0], $data_fim_folha[2]);

                                  if ($ano_admissao < $data_fim_folha[2]) {

                                  $inicio_ano_segundos = mktime(0, 0, 0, 1, 1, $data_fim_folha[2]);

                                  $meses_trabalhados = ( $data_hoje_segundos - $inicio_ano_segundos) / 2592000;
                                  $meses_trabalhados = (int) $meses_trabalhados;

                                  $valor = ($salario_base / 12) * $meses_trabalhados;
                                  $totalizador_meses_trabalhados += $valor;
                                  echo '<span class="meses_trabalhados">' . $meses_trabalhados . '</span> meses - R$ <span class="valor_meses_trabalhados">' . formato_real($valor) . '</span>';
                                  } else {

                                  $meses_trabalhados = ( $data_hoje_segundos - $data_admissao_segundos) / 2592000;
                                  $meses_trabalhados = (int) $meses_trabalhados;

                                  $valor = ($salario_base / 12) * $meses_trabalhados;
                                  $totalizador_meses_trabalhados += $valor;
                                  echo '<span class="meses_trabalhados">' . $meses_trabalhados . '</span> meses - R$ <span class="valor_meses_trabalhados">' . formato_real($valor) . '</span>';
                                  } */
                                ?>          
                            </td>

                        <?php } ?>

                        <td><input name="rendimentos" title="Editar Rendimentos" type="text" onKeyDown="FormataValor(this, event, 17, 2)" value="<?php echo formato_real($rendimentos); ?>" class="rendimentos" /></td>
                        <td><input name="descontos" title="Editar Descontos" type="text"  onKeyDown="FormataValor(this, event, 17, 2)" value="<?php echo formato_real($descontos); ?>" class="descontos" /></td>
                        <td><a href="sintetica_coo/edicao_inss.php?enc=<?php echo $relatorio; ?>" title="Editar INSS" onClick="return hs.htmlExpand(this, {objectType: 'iframe'})"> <span class="inss"><?php echo formato_real($inss); ?></span></a></td>
                        <td><span class="irrf" ><?php echo formato_real($irrf); ?></span></td>
                        <td><a href="relacao_quotas.php?id=<?php echo $cooperado; ?>" title="Visualizar Relatório de Quotas" onClick="return hs.htmlExpand(this, {objectType: 'iframe'})" ><span class="quota"><?php echo formato_real($valor_quota); ?></span></a></td>
                        <td><input name="ajuda_custo" type="text" onKeyDown="FormataValor(this, event, 17, 2)" value="<?php echo formato_real($ajuda_custo); ?>" <?php
//                               coemntado a pedido da gerente do RH em 19/06/2015
//                               if ($row_cooperado['tipo_inss'] == 1) {
//                                   echo 'disabled="disabled" style="color:#CCC;" title="Bloqueado pois o participante tem o INSS fixo"';
//                               } else {
//                                   echo 'title="Editar Ajuda de Custo" ';
//                               }
                            ?> class="ajuda_custo"/></td>
                        <td><span class="liquido"><?php echo formato_real($liquido); ?></span></td>

                        <?php if ($ACOES->verifica_permissoes(71)) { ?>  
                            <td><span class="nota_fiscal"><?php echo formato_real($nota_fiscal); ?></span>
                            <?php } ?>

                            <input name="ano_folha" type="hidden" class="ano_folha" value="<?php echo $row_folha['ano']; ?>" />
                            <input name="mes_anterior" type="hidden" class="mes_anterior" value="<?php echo $mes_anterior; ?>" /></td> 
                        </td>
                    <input type="hidden" name="id_folha_participante" class="id_folha_participante" value="<?= $row_participante['id_folha_pro'] ?>">
                    </tr>

                    <?php
                    include('sintetica_coo/update_participante.php');
                    include('sintetica_coo/totalizadores_resets.php');

                    // Fim do Loop de Participantes
                }
                ?>

                <tr class="totais">
                    <td colspan="4">
                        <?php if ($total_participantes > 10) { ?>
                            <a href="#corpo" class="ancora">Subir ao topo</a>
                        <?php } ?>
                        <div class="right">TOTAIS:</div>
                    </td>
                    <td><span class="totalizador_base"><?php echo formato_real($salario_base_total); ?></span></td>
                    <?php if ($row_folha['terceiro'] > 0) { ?>  <td> <?php echo formato_real($totalizador_meses_trabalhados); ?>  </td> <?php } ?>
                    <td><span class="totalizador_rendimentos"><?php echo formato_real($rendimentos_total); ?></span></td>
                    <td><span class="totalizador_descontos"><?php echo formato_real($descontos_total); ?></span></td>
                    <td><span class="totalizador_inss"><?php echo formato_real($inss_total); ?></span></td>
                    <td><span class="totalizador_irrf"><?php echo formato_real($irrf_total); ?></span></td>
                    <td><span class="totalizador_quota"><?php echo formato_real($valor_quota_total); ?></span></td>
                    <td><span class="totalizador_ajuda_custo"><?php echo formato_real($ajuda_custo_total); ?></span></td>
                    <?php
                    //Query para receber o salário líquido total da folha
//                    $qr_salario_liquido = mysql_query("SELECT sum(salario_liq) from folha_cooperado where id_folha = '$folha' and status = '2'");
//                    $result_salario_liquido = mysql_fetch_row($qr_salario_liquido);
//                    $liquido_total = $result_salario_liquido[0];
                    ?>
                    <td><span class="totalizador_liquido"><?php echo formato_real($liquido_total); ?></span></td>

                    <?php if ($ACOES->verifica_permissoes(71)) { ?>  
                        <td><span class="totalizador_nota_fiscal"><?php echo formato_real($nota_fiscal_total); ?></span></td>
                        <?php } ?> 
                </tr>


            </table>
            <?php include('sintetica_coo/estatisticas_folha.php'); ?>
        </div>
        <script>
            $(function () {

                $('.valor_hora, .horas_trabalhadas, .rendimentos, .descontos, .ajuda_custo, .liquido').blur(function () {
                    ///obtendo os valores
                    var linha_objeto = $(this).parent().parent();
                    var id_folha_participante = linha_objeto.find('.id_folha_participante').val();
                    var valor_hora = linha_objeto.find('.valor_hora').val().replace('.', '').replace(',', '.');
                    var horas_trabalhadas = linha_objeto.find('.horas_trabalhadas').val();
                    var base = linha_objeto.find('.base').html().replace('.', '').replace(',', '.');
                    var rendimentos = linha_objeto.find('.rendimentos').val().replace('.', '').replace(',', '.');
                    var descontos = linha_objeto.find('.descontos').val().replace('.', '').replace(',', '.');
                    var irrf = linha_objeto.find('.irrf').html().replace('.', '').replace(',', '.');
                    var quota = linha_objeto.find('.quota').html().replace('.', '').replace(',', '.');
                    var ajuda_custo = linha_objeto.find('.ajuda_custo').val().replace('.', '').replace(',', '.');
                    var liquido = linha_objeto.find('.liquido').html().replace('.', '').replace(',', '.');
                    var nota_fiscal = linha_objeto.find('.nota_fiscal').html().replace('.', '').replace(',', '.');
                    var id_cooperado = linha_objeto.find('.id_cooperado').html();
                    var ano_folha = linha_objeto.find('.ano_folha').val();
                    var mes_anterior = linha_objeto.find('.mes_anterior').val();
                    var qnt_meses_trabalhados = linha_objeto.find('.meses_trabalhados').html();
                    var inss = linha_objeto.find('.inss').html();
                    //                    var valor_meses_trabalhados  = $(this).parent().parent().find('.valor_meses_trabalhados').html();

                    var total_base = horas_trabalhadas * valor_hora;
                    var total_nota_fiscal = total_base + parseFloat(rendimentos) - parseFloat(descontos) + parseFloat(ajuda_custo);

                    var total_parcial = total_base + parseFloat(quota) - parseFloat(descontos) + parseFloat(rendimentos) + parseFloat(ajuda_custo);

                    /////Calculos e saida dos valores
                    $.ajax({
                        url: 'action.update_valores.php?horas_trab=' + horas_trabalhadas + '&rendimentos=' + rendimentos + '&descontos=' + descontos + '&ajuda_custo=' + ajuda_custo + '&id_folha_participante=' + id_folha_participante + '&sal_base=' + base + '&id_coop=' + id_cooperado + '&ano_folha=' + ano_folha + '&mes_anterior=' + mes_anterior + '&liquido=' + liquido + '&valor_hora=' + valor_hora + '&irrf=' + irrf + '&total_parcial=' + total_parcial+ '&inss='+inss,
                        dataType: 'json',
                        success: function (resposta) {
                            console.log(resposta);
                            var irrf = resposta.irrf;
                            var inss = resposta.inss;
//                            var total_liquido = total_base - inss - irrf - quota - parseFloat(descontos) + parseFloat(rendimentos) + parseFloat(ajuda_custo);
                            var total_liquido = resposta.liquido;

                            ////folha de abono natalino
                            var abono = (total_base / 12) * qnt_meses_trabalhados;

                            //imprimindo valores
                            linha_objeto.find('.base').html(float2moeda(total_base));
                            linha_objeto.find('.inss').html(float2moeda(inss));
                            linha_objeto.find('.irrf').html(float2moeda(irrf));
                            linha_objeto.find('.liquido').html(float2moeda(total_liquido));
                            linha_objeto.find('.nota_fiscal').html(float2moeda(total_nota_fiscal));
                            linha_objeto.find('.valor_meses_trabalhados').html(float2moeda(abono));

                            ///////calculando os totalizadores	
                            var T_base = 0;
                            $('.base').each(function () {
                                T_base = parseFloat($(this).html().replace('.', '').replace(',', '.')) + parseFloat(T_base);
                            });

                            var T_rendimentos = 0;
                            $('.rendimentos').each(function () {
                                T_rendimentos = parseFloat($(this).val().replace('.', '').replace(',', '.')) + parseFloat(T_rendimentos);
                            });

                            var T_desconto = 0;
                            $('.descontos').each(function () {

                                T_desconto = parseFloat($(this).val().replace('.', '').replace(',', '.')) + parseFloat(T_desconto);
                            });

                            var T_inss = 0;
                            $('.inss').each(function () {
                                T_inss = parseFloat($(this).html().replace('.', '').replace(',', '.')) + parseFloat(T_inss);
                            });

                            var T_irrf = 0;
                            $('.irrf').each(function () {
                                T_irrf = parseFloat($(this).html().replace('.', '').replace(',', '.')) + parseFloat(T_irrf);

                            });

                            var T_quota = 0;
                            $('.quota').each(function () {
                                T_quota = parseFloat($(this).html().replace('.', '').replace(',', '.')) + parseFloat(T_quota);
                            });

                            var T_ajuda_custo = 0;
                            $('.ajuda_custo').each(function () {
                                T_ajuda_custo = parseFloat($(this).val().replace('.', '').replace(',', '.')) + parseFloat(T_ajuda_custo);
                            });

                            var T_liquido = 0;
                            $('.liquido').each(function () {
                                T_liquido = parseFloat($(this).html().replace('.', '').replace(',', '.')) + parseFloat(T_liquido);
                            });

                            var T_nota_fiscal = 0;
                            $('.nota_fiscal').each(function () {
                                T_nota_fiscal = parseFloat($(this).html().replace('.', '').replace(',', '.')) + parseFloat(T_nota_fiscal);
                            });


                            ///atualizando totalizadores
                            //$('.totalizador_base').html(float2moeda(T_base - base +total_base ));

                            $('.totalizador_base').html(float2moeda(T_base));
                            $('.totalizador_rendimentos').html(float2moeda(T_rendimentos));
                            $('.totalizador_descontos').html(float2moeda(T_desconto));
                            $('.totalizador_inss').html(float2moeda(T_inss));
                            $('.totalizador_irrf').html(float2moeda(T_irrf));
                            $('.totalizador_quota').html(float2moeda(T_quota));
                            $('.totalizador_ajuda_custo').html(float2moeda(T_ajuda_custo));
                            $('.totalizador_liquido').html(float2moeda(T_liquido));
                            $('.totalizador_nota_fiscal').html(float2moeda(T_nota_fiscal));

                        }

                    }); //fim ajax
                });
            });


            function float2moeda(num) {
                x = 0;
                if (num < 0) {
                    num = Math.abs(num);
                    x = 1;
                }

                if (isNaN(num))
                    num = "0";
                cents = Math.floor((num * 100 + 0.5) % 100);

                num = Math.floor((num * 100 + 0.5) / 100).toString();

                if (cents < 10)
                    cents = "0" + cents;
                for (var i = 0; i < Math.floor((num.length - (1 + i)) / 3); i++)
                    num = num.substring(0, num.length - (4 * i + 3)) + '.'
                            + num.substring(num.length - (4 * i + 3));

                ret = num + ',' + cents;

                if (x == 1)
                    ret = ' - ' + ret;
                return ret;
            }

            function formato_valor(num) {
                return num.replace('.', '').replace(',', '.');
            }
        </script>
        <?php include('sintetica_coo/updates.php'); ?>
    </body>
</html>