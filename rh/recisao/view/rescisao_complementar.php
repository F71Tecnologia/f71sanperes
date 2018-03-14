<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>Rescis&atilde;o de <?php echo $id_clt . ' - ' . $nome; ?></title>
        <link href="rescisao_1.css" rel="stylesheet" type="text/css" />
        <script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../../js/global.js" type="text/javascript"></script>
        <script src="../../js/jquery.price_format.2.0.min.js" type="text/javascript"></script>
        <style type="text/css" media="print">
            table.rescisao td.secao {
                background-color:#C0C0C0;
                text-align:center;
                font-size:14px;
                height:20px;
            }
        </style>
        <script>
            function number_format(number, decimals, dec_point, thousands_sep) {
                number = (number + '')
                        .replace(/[^0-9+\-Ee.]/g, '');
                var n = !isFinite(+number) ? 0 : +number,
                        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
                        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
                        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
                        s = '',
                        toFixedFix = function(n, prec) {
                            var k = Math.pow(10, prec);
                            return '' + (Math.round(n * k) / k)
                                    .toFixed(prec);
                        };
                // Fix for IE parseFloat(0.55).toFixed(0) = 0;
                s = (prec ? toFixedFix(n, prec) : '' + Math.round(n))
                        .split('.');
                if (s[0].length > 3) {
                    s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
                }
                if ((s[1] || '')
                        .length < prec) {
                    s[1] = s[1] || '';
                    s[1] += new Array(prec - s[1].length + 1)
                            .join('0');
                }
                return s.join(dec);
            }
            dados_credito = new Object();
            dados_debito = new Object();
            function somar_rescisorio_bruto() {
                var rescisorio_bruto = 0;
                
                $(".rescisorio_bruto").each(function(index) {
                    rescisorio_bruto = (eval($(this).val().replace('R$ ', '').replace('.', '').replace(',', '.')) + eval(rescisorio_bruto));
//                    dados[index] = { $(this).attr('name') : $(this).val()};
//                    teste1 = $(this).attr('name');
                    dados_credito[index] = {name: $(this).attr('name'), valor: $(this).val().replace('R$ ', '').replace('.', '').replace(',', '.')};
                });
//                console.log('Bruto rescisorio: ' + rescisorio_bruto);
                $('#total_rescisorio_bruto').html('R$ ' + number_format(rescisorio_bruto, 2, ',', '.'));
                return rescisorio_bruto;
            }
            function somar_deducoes() {
                var deducoes = 0;
                $(".deducoes").each(function(index) {
                    deducoes = (eval($(this).val().replace('R$ ', '').replace('.', '').replace(',', '.')) + eval(deducoes));
                    dados_debito[index] = {name: $(this).attr('name'), valor: $(this).val().replace('R$ ', '').replace('.', '').replace(',', '.')};
                });
//                console.log(dados);
                $('#total_deducoes').html('R$ ' + number_format(deducoes, 2, ',', '.'));
                return deducoes;
            }

            $(function() {
                $('.money').blur(function(e) {
                    rescisorio_bruto = somar_rescisorio_bruto();
                    deducoes = somar_deducoes();
                    valor_liquido_rescisorio = (rescisorio_bruto - deducoes);
                    console.log('bruto :' + rescisorio_bruto + ' - ' + deducoes + ' deduções');
                    console.log(valor_liquido_rescisorio);
                    $('#valor_rescisorio_liquido').html('R$ ' + number_format(valor_liquido_rescisorio, 2, ',', '.'));
                });
                $('#processar_rescisao').click(function() {
                    
                    if(confirm('Você deseja realmente processar a rescissão?')){
                        dados = new Object();
                        somar_rescisorio_bruto();
                        somar_deducoes();
//                        dados.mov_desconto = new Object();
//                        $(".mov_desconto").each(function(index) {
//                            dados.mov_desconto[index] = {name: $(this).attr('name'), valor: $(this).val().replace('R$ ', '').replace('.', '').replace(',', '.')};
//                        });
//                        dados.mov_credito = new Object();
//                        $(".mov_credito").each(function(index) {
//                            dados.mov_credito[index] = {name: $(this).attr('name'), valor: $(this).val().replace('R$ ', '').replace('.', '').replace(',', '.')};
//                        });

                        console.log(dados_credito);
                        console.log(dados_debito);

                        $.post('controlador.php', {acao: 'salva_rescisao_complementar', credito: dados_credito, debito: dados_debito, id_recisao: <?= $id; ?>, id_clt: <?= $id_clt; ?>}, function(dados) {
                            console.log(dados);
                        });
                    }
                });
            });
        </script>
    </head>
    <body>
        <div style="width: 100%; background: #CCC; height: 30px; position: fixed;" ><input type="button" value="Processar" id="processar_rescisao" /></div>
        <br><br>
                <table class="rescisao" cellpadding="0" cellspacing="1">
                    <tr>
                        <td colspan="6" class="secao"><h1>TERMO DE RESCIS&Atilde;O DO CONTRATO DE TRABALHO</h1></td>
                    </tr>
                    <tr>
                        <td colspan="6" class="secao">IDENTIFICA&Ccedil;&Atilde;O DO EMPREGADOR</td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div class="campo"><span class="numero">01</span> CNPJ/CEI</div>
                            <div class="valor"><?php echo $row_clt['cnpj']; ?></div>
                        </td>
                        <td colspan="4">
                            <div class="campo"><span class="numero">02</span> Raz&atilde;o Social/Nome</div>
                            <div class="valor"><?php echo $row_clt['razao']; ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            <div class="campo"><span class="numero">03</span> Endere&ccedil;o (logradouro, n&ordm;, andar, apartamento)</div>
                            <div class="valor"><?php echo $row_clt['logradouro_empresa']; ?></div>
                        </td>
                        <td colspan="2">
                            <div class="campo"><span class="numero">04</span> Bairro</div>
                            <div class="valor"><?php echo $row_clt['bairro_empresa']; ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="campo"><span class="numero">05</span> Munic&iacute;pio</div>
                            <div class="valor"><?php echo $row_clt['cidade_empresa']; ?></div>
                        </td>
                        <td>
                            <div class="campo"><span class="numero">06</span> UF</div>
                            <div class="valor"><?php echo $row_clt['uf_empresa']; ?></div>
                        </td>
                        <td>
                            <div class="campo"><span class="numero">07</span> CEP</div>
                            <div class="valor"><?php echo $row_clt['cep_empresa']; ?></div>
                        </td>
                        <td>
                            <div class="campo"><span class="numero">08</span> CNAE</div>
                            <div class="valor"><?php echo $row_clt['cnae_empresa']; ?></div>
                        </td>
                        <td colspan="2">
                            <div class="campo"><span class="numero">09</span> CNPJ/CEI Tomador/Obra</div>
                            <div class="valor"><?php echo $row_clt['cnpj']; ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6" class="secao">IDENTIFICA&Ccedil;&Atilde;O DO TRABALHADOR</td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div class="campo"><span class="numero">10</span> PIS/PASEP</div>
                            <div class="valor"><?php echo $row_clt['pis']; ?></div>
                        </td>
                        <td colspan="4">
                            <div class="campo"><span class="numero">11</span> Nome</div>
                            <div class="valor"><?php echo $row_clt['nome_funcionario']; ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            <div class="campo"><span class="numero">12</span> Endere&ccedil;o (logradouro, n&ordm;, andar, apartamento)</div>
                            <div class="valor"><?php echo $row_clt['endereco_funcionario']; ?></div>
                        </td>
                        <td colspan="2">
                            <div class="campo"><span class="numero">13</span> Bairro</div>
                            <div class="valor"><?php echo $row_clt['bairro_funcionario']; ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div class="campo"><span class="numero">14</span> Munic&iacute;pio</div>
                            <div class="valor"><?php echo $row_clt['cidade_funcionario']; ?></div>
                        </td>
                        <td>
                            <div class="campo"><span class="numero">15</span> UF</div>
                            <div class="valor"><?php echo $row_clt['uf_funcionario']; ?></div>
                        </td>
                        <td>
                            <div class="campo"><span class="numero">16</span> CEP</div>
                            <div class="valor"><?php echo $row_clt['cep_funcionario']; ?></div>
                        </td>
                        <td colspan="2">
                            <div class="campo"><span class="numero">17</span> Carteira de Trabalho (n&ordm;, s&eacute;rie, UF)</div>
                            <div class="valor"><?php echo $row_clt['numero_ctps'] . ' / ' . $row_clt['serie_ctps'] . ' / ' . $row_clt['uf_ctps']; ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div class="campo"><span class="numero">18</span> CPF</div>
                            <div class="valor"><?php echo $row_clt['cpf']; ?></div>
                        </td>
                        <td>
                            <div class="campo"><span class="numero">19</span> Data de nascimento</div>
                            <div class="valor"><?php echo $row_clt['data_nascimento']; ?></div>
                        </td>
                        <td colspan="3">
                            <div class="campo"><span class="numero">20</span> Nome da m&atilde;e</div>
                            <div class="valor"><?php echo $row_clt['mae']; ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6" class="secao">DADOS DO CONTRATO</td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <div class="campo"><span class="numero">21</span> Tipo de Contrato</div>
                            <div class="valor">      
                                <?php echo $row_clt['nome_tipo_contrato'];    ?>
                            </div>
                        </td>
                        <td colspan="3">
                            <div class="campo"><span class="numero">22</span> Causa do Afastamento</div>
                            <div class="valor"><?php echo $row_rescisao['causa_afastamento']; ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <div class="campo"><span class="numero">23</span> Remunera&ccedil;&atilde;o M&ecirc;s Anterior Afast.</div>
                            <div class="valor">R$ <?php echo formato_real($row_rescisao['sal_base']); ?></div>
                        </td>
                        <td>
                            <div class="campo"><span class="numero">24</span> Data de admiss&atilde;o</div>
                            <div class="valor"><?php echo $row_clt['data_entrada']; ?></div>
                        </td>
                        <td>
                            <div class="campo"><span class="numero">25</span> Data do Aviso Pr&eacute;vio</div>
                            <div class="valor"><?php echo formato_brasileiro($row_rescisao['data_aviso']); ?></div>
                        </td>
                        <td>
                            <div class="campo"><span class="numero">26</span> Data de afastamento</div>
                            <div class="valor"><?php echo $row_rescisao['data_demi_f']; ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <div class="campo"><span class="numero">27</span> C&oacute;d. afastamento</div>
                            <div class="valor"><?php echo $row_rescisao['codigo_afastamento']; ?></div>
                        </td>
                        <td>
                            <div class="campo"><span class="numero">28</span> Pens&atilde;o Aliment&iacute;cia (%) (TRCT)</div>
                            <div class="valor">0,00%</div>
                        </td>
                        <td>
                            <div class="campo"><span class="numero">29</span> Pens&atilde;o aliment&iacute;cia (%) (Saque FGTS)</div>
                            <div class="valor">0,00%</div>
                        </td>
                        <td>
                            <div class="campo"><span class="numero">30</span> Categoria do trabalhador</div>
                            <div class="valor">01</div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div class="campo"><span class="numero">31</span> C&oacute;digo Sindical</div>
                            <div class="valor"><?php echo $cod_sindicato; ?></div>
                        </td>
                        <td colspan="4">
                            <div class="campo"><span class="numero">32</span> CNPJ e Nome da Entidade Sindical Laboral</div>
                            <div class="valor"><?php echo $row_sindicato['cnpj'] . ' - ' . substr($row_sindicato['nome'], 0, 52); ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6" class="secao">DISCRIMINA&Ccedil;&Atilde;O DAS VERBAS RESCIS&Oacute;RIAS</td>
                    </tr>
                    <tr>
                        <td colspan="6" class="secao">VERBAS RESCIS&Oacute;RIAS</td>
                    </tr>
                    <tr>
                        <td width="17%" class="secao_filho">Rubrica</td>
                        <td width="16%" class="secao_filho">Valor</td>
                        <td width="17%" class="secao_filho">Rubrica</td>
                        <td width="16%" class="secao_filho">Valor</td>
                        <td width="17%" class="secao_filho">Rubrica</td>
                        <td width="16%" class="secao_filho">Valor</td>
                    </tr>
                    <?php
                    $cont = 0;
                    foreach ($movimentos_lancaveis_credito as $movimento) {
                        if (($cont % 3) == 0) {
                            echo '<tr>';
                        }
                        ?>
                        <td><span class="numero"><?= $movimento['campo_rescisao'] ?></span>&nbsp;<?= $movimento['descicao'] ?> #<?= $movimento['id_mov'] ?> </td>
                        <td><input type="" class="money rescisorio_bruto " value="0,00" name="<?= $movimento['id_mov'] ?>" /></td>                        
                        <?php
                        if (count($movimentos_lancaveis_credito) == ($cont + 1)) {
                            $sobra = count($movimentos_lancaveis_credito);
                            while (($sobra % 3) != 0) {
                                if ((($sobra + 1) % 3) != 0) {
                                    echo '<td></td><td></td>';
                                } else {
                                    echo '<td class="secao">TOTAL RESCISÓRIO BRUTO</td><td class="secao"><div class="valor" id="total_rescisorio_bruto">R$ 0,00</div></td>';
                                }
                                $sobra++;
                            }
                        }

                        if (( ($cont + 1) % 3) == 0) {
                            echo '</tr>';
                        }
                        $cont++;
                    }
                    ?>

                    <tr>
                        <td colspan="6" class="secao">DEDU&Ccedil;&Otilde;ES</td>
                    </tr>

                    <tr>
                        <td class="secao_filho">Desconto</td>
                        <td class="secao_filho">Valor</td>
                        <td class="secao_filho">Desconto</td>
                        <td class="secao_filho">Valor</td>
                        <td class="secao_filho">Desconto</td>
                        <td class="secao_filho">Valor</td>
                    </tr>
                    <?php
                    $cont = 0;
                    foreach ($movimentos_lancaveis_debito as $movimento) {
                        if (($cont % 3) == 0) {
                            echo '<tr>';
                        }
                        ?>
                        <td><span class="numero"><?= $movimento['campo_rescisao'] ?></span>&nbsp;<?= $movimento['descicao'] ?> #<?= $movimento['id_mov'] ?></td>
                        <td><input type="" class="money deducoes " value="0,00" name="<?= $movimento['id_mov'] ?>" /></td>                        
                        <?php
                        if (count($movimentos_lancaveis_debito) == ($cont + 1)) {
                            $sobra = count($movimentos_lancaveis_debito);
                            while (($sobra % 3) != 0) {
                                if ((($sobra + 1) % 3) != 0) {
                                    echo '<td></td><td></td>';
                                } else {
                                    echo '<td class="secao">TOTAL DAS DEDU&Ccedil;&Otilde;ES</td><td class="secao"><div class="valor" id="total_deducoes">R$ 0,00</div></td>'.
                                         '<tr><td></td><td></td><td></td><td></td><td class="secao">VALOR RESCISÓRIO LÍQUIDO</td><td class="secao"><div class="valor" id="valor_rescisorio_liquido">R$ 0,00</div></td></tr>';
                                }
                                $sobra++;
                            }
                        }
                        if (( ($cont + 1) % 3) == 0) {
                            echo '</tr>';
                        }
                        $cont++;
                    }
                    ?>

                </table>
                <table  class="rescisao" cellpadding="0" cellspacing="1" style="page-break-before: always; margin-top:20px;" >
                    <tr>
                        <td colspan="6" class="secao"><h1>TERMO DE QUITAÇÃO DO CONTRATO DE TRABALHO</h1></td>
                    </tr>

                    <tr>
                        <td colspan="6" class="secao">EMPREGADOR</td>
                    </tr>

                    <tr>
                        <td colspan="2">
                            <div class="campo"><span class="numero">01</span> CNPJ/CEI</div>
                            <div class="valor"><?php echo $row_clt['cnpj']; ?></div>
                        </td>
                        <td colspan="4">
                            <div class="campo"><span class="numero">02</span> Raz&atilde;o Social/Nome</div>
                            <div class="valor"><?php echo $row_clt['razao']; ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6" class="secao">TRABALHADOR</td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div class="campo"><span class="numero">10</span> PIS/PASEP</div>
                            <div class="valor"><?php echo $row_clt['pis']; ?></div>
                        </td>
                        <td colspan="4">
                            <div class="campo"><span class="numero">11</span> Nome</div>
                            <div class="valor"><?php echo $row_clt['nome_funcionario']; ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div class="campo"><span class="numero">17</span> Carteira de Trabalho (n&ordm;, s&eacute;rie, UF)</div>
                            <div class="valor"><?php echo $row_clt['numero_ctps'] . ' / ' . $row_clt['serie_ctps'] . ' / ' . $row_clt['uf_ctps']; ?></div>
                        </td>

                        <td colspan="2">
                            <div class="campo"><span class="numero">18</span> CPF</div>
                            <div class="valor"><?php echo $row_clt['cpf']; ?></div>
                        </td>
                        <td>
                            <div class="campo"><span class="numero">19</span> Data de nascimento</div>
                            <div class="valor"><?php echo $row_clt['data_nascimento']; ?></div>
                        </td>
                        <td colspan="3">
                            <div class="campo"><span class="numero">20</span> Nome da m&atilde;e</div>
                            <div class="valor"><?php echo $row_clt['mae']; ?></div>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="6" class="secao">CONTRATO</td>
                    </tr>

                    <tr>   
                        <td colspan="6">
                            <div class="campo"><span class="numero">22</span> Causa do Afastamento</div>
                            <div class="valor"><?php echo $row_rescisao['causa_afastamento']; ?></div>
                        </td>
                    </tr>
                    <tr>    
                        <td>
                            <div class="campo"><span class="numero">24</span> Data de admiss&atilde;o</div>
                            <div class="valor"><?php echo $row_clt['data_entrada']; ?></div>
                        </td>
                        <td>
                            <div class="campo"><span class="numero">25</span> Data do Aviso Pr&eacute;vio</div>
                            <div class="valor"><?php echo $row_rescisao['data_aviso_f']; ?></div>
                        </td>
                        <td>
                            <div class="campo"><span class="numero">26</span> Data de afastamento</div>
                            <div class="valor"><?php echo $row_rescisao['data_demi_f']; ?></div>
                        </td>
                        <td>
                            <div class="campo"><span class="numero">27</span> C&oacute;d. afastamento</div>
                            <div class="valor"><?php echo $row_rescisao['codigo_afastamento']; ?></div>
                        </td>  
                        <td colspan="2">
                            <div class="campo"><span class="numero">29</span> Pens&atilde;o aliment&iacute;cia (%) (Saque FGTS)</div>
                            <div class="valor">0,00%</div>
                        </td>  
                    </tr>
                    <tr>  
                        <td colspan="6">
                            <div class="campo"><span class="numero">30</span> Categoria do trabalhador</div>
                            <div class="valor">01</div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div class="campo"><span class="numero">31</span> C&oacute;digo Sindical</div>
                            <div class="valor"><?php echo $row_clt['cod_sindicato']; ?></div>
                        </td>
                        <td colspan="4">
                            <div class="campo"><span class="numero">32</span> CNPJ e Nome da Entidade Sindical Laboral</div>
                            <div class="valor"><?php echo $row_sindicato['cnpj'] . ' - ' . substr($row_sindicato['nome'], 0, 52); ?></div>
                        </td>
                    </tr>

                    <tr style="border: 0px;">
                        <td colspan="6" style="border: 0px;">
                            <div class="campo">
                                Foi prestada, gratuitamente, assist&ecirc;ncia na rescisão do contrato de trabalho, nos termos do art. 477, &sect; 1&ordm;,
                                da Consolida&ccedil;&atilde;o das Leis do Trabalho (CLT), sendo comprovado, neste ato, o efetivo pagamento das verbas rescis&oacute;rias
                                acima especificadas no corpo do TRCT, no valor líquido de R$ <?php echo formato_real($row_rescisao['total_liquido']); ?>, o qual devidamente rubricado pelas partes, é parte integrante
                                do presente Termo de Homologação. <br >
                                    </p>
                                    <p>As partes assistidas no presente ato de rescisão contratual foram identificadas como legitimas conforme previsto na Instrução Normativa/SRT nº 15/2010</p>
                                    <p>Fico ressalvado o direito de o trabalhador pleitear judicialmente os direitos informados no camppo 155, abaixo.</p>

                                    <p>____________________/___, ____ de _______________________ de _______. </p>
                                    <p>&nbsp;</p>
                                    <p>&nbsp;</p>
                                    <p>___________________________________________________________<br>
                                            150 Assinatura do Empregador ou Preposto
                                    </p>
                            </div>
                        </td>   
                    </tr>

                    <tr style="border: 0px;">
                        <td colspan="3" style="border: 0px;" >

                            <p>&nbsp;</p>
                            <p>&nbsp;</p>
                            <p>___________________________________________________________<br>
                                    151 Assinatura do Trabalhador
                            </p>
                        </td>
                        <td colspan="3" style="border: 0px;">
                            <p>&nbsp;</p>
                            <p>&nbsp;</p> 
                            <p>___________________________________________________________<br>
                                    152 Assinatura do Responsável Legal do Trabalhador
                            </p>
                        </td>
                    </tr>

                    <tr style="border: 0px;">
                        <td colspan="3"  style="border: 0px;">
                            <p>&nbsp;</p>
                            <p>&nbsp;</p>
                            <p>___________________________________________________________<br>
                                    153 Carimbo e Assinatura do Assistente
                            </p>

                        </td>
                        <td colspan="3"  style="border: 0px;">
                            <p>&nbsp;</p>
                            <p>&nbsp;</p>
                            <p>___________________________________________________________<br>
                                    154 Nome do Órgão Homologador
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6" >   <div class="campo"><span class="numero">155</span> Ressalvas</div> 
                            <p>&nbsp;</p>
                            <p>&nbsp;</p>
                            <p>&nbsp;</p>
                            <p>&nbsp;</p>
                            <p>&nbsp;</p>
                            <p>&nbsp;</p>
                            <p>&nbsp;</p>
                            <p>&nbsp;</p>
                            <p>&nbsp;</p>
                            <p>&nbsp;</p>
                            <p>&nbsp;</p>

                        </td>      
                    </tr>
                    <tr>
                        <td colspan="6">
                            <div class="campo"><span class="numero">156</span> Informações à CAIXA</div> 
                            <p>&nbsp;</p>

                        </td>
                    </tr>   
                    <tr>
                        <td colspan="6">
                            <p style="text-align:center;">
                                <strong> ASSISTÊNCIA NO ATO DE RESCISÃO CONTRATUAL É GRATUITA.</strong><br>
                                    Pode o trabalhador iniciar ação judicial quanto aos créditos resultantes das relações de trabalho até o limite de dois anos após a extinção do contrato de trabalho (inciso XXIX, art. 7º da Constituição Federal/1988).
                            </p>
                        </td>
                    </tr>
                </table>
                </body>
                </html>