<?php
include('../../conn.php');
include('../../funcoes.php');
include('../../wfunction.php');
include('../../classes/clt.php');
include('../../classes/abreviacao.php');
include('../../classes/formato_valor.php');
include('../../classes/formato_data.php');
include('../../classes/valor_proporcional.php');

$usuario = carregaUsuario();
// Recebendo a Vari�vel Criptografada
//list($regiao, $id_clt, $id) = explode('&', decrypt(str_replace('--', '+', $_REQUEST['enc'])));

$idHeaderRescisao = $_REQUEST[id];
// Consulta da Rescis�o
$qr_rescisao = mysql_query("SELECT * FROM rh_recisao WHERE id_regiao = $usuario[id_regiao] AND recisao_provisao_de_calculo = 1 AND status = 1 AND id_recisao_lote = $idHeaderRescisao") or die(mysql_error());
while($row_rescisao = mysql_fetch_array($qr_rescisao)){

if ($row_rescisao['aviso'] == 'trabalhado' or $row_rescisao['motivo'] == 63) {

    $tipo_aviso = 'Aviso Pr�vio trabalhado';
    $data_afastamento = $row_rescisao['data_demi'];
} else {

    $tipo_aviso = 'Aviso Pr�vio indenizado';
    $data_afastamento = $row_rescisao['data_aviso'];
}




// Tipo da Rescis�o
$qr_motivo = mysql_query("SELECT * FROM rhstatus WHERE codigo = '$row_rescisao[motivo]'");
$row_motivo = mysql_fetch_array($qr_motivo);

// Informa��es do Participante
$Clt = new clt();
$Clt->MostraClt($row_rescisao[id_clt]);
$pis = $Clt->pis;
$nome = $Clt->nome;
$codigo = $Clt->campo3;
$endereco = $Clt->endereco;
$bairro = $Clt->bairro;
$cidade = $Clt->cidade;
$uf = $Clt->uf;
$cep = $Clt->cep;
$cartrab = $Clt->campo1;
$serie_cartrab = $Clt->serie_ctps;
$uf_cartrab = $Clt->uf_ctps;
$cpf = $Clt->cpf;
$data_nasci = $Clt->data_nasci;
$mae = $Clt->mae;
$data_entrada = $Clt->data_entrada;
$data_demi = $Clt->data_demi;
$data_aviso = $Clt->data_aviso;
$rh_sindicato = $Clt->rh_sindicato;
$id_projeto_clt = $Clt->id_projeto;

$tipo_contrato = $Clt->tipo_contrato;




// Sindicato do Participante
$qr_sindicato = mysql_query("SELECT * FROM rhsindicato WHERE id_sindicato = '$rh_sindicato'");
$row_sindicato = mysql_fetch_assoc($qr_sindicato);

$qr_regiao = mysql_query("SELECT * FROM regioes WHERE id_regiao = '$regiao'");
$row_regiao = mysql_fetch_assoc($qr_regiao);

$qr_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_regiao[id_master]' ");
$row_master = mysql_fetch_assoc($qr_master);


// Informa��es da Empresa
$qr_empresa = mysql_query("SELECT * FROM rhempresa WHERE id_regiao = '1' AND id_projeto = '1'"); //CNPJ DA MATRIZ (ADM)
$row_empresa = mysql_fetch_assoc($qr_empresa);

$cnpj_empresa = $row_empresa['cnpj'];
$razao_empresa = $row_empresa['razao'];
//$logradouro         = explode('-',$row_empresa['endereco']);		
$endereco_empresa = $row_empresa['endereco'];
$municipio_empresa = $row_empresa['cidade'];
$uf_empresa = $row_empresa['uf'];
$cep_empresa = $row_empresa['cep'];
$bairro_empresa = $row_empresa['bairro'];
$cnae = $row_empresa['cnae'];


// Aviso Pr�vio
if ($row_rescisao['motivo'] == 65) {
    $aviso_previo_debito = $row_rescisao['aviso_valor'];
} else {
    $aviso_previo_credito = $row_rescisao['aviso_valor'];
}


$cod_sindicato = (empty($row_sindicato['codigo_sindical'])) ? "999.000.000.00000-3" : $row_sindicato['codigo_sindical'];



// Multa de Atraso

if ($row_rescisao['motivo'] == '64') {
    $multa_479 = $row_rescisao['a479'];
    $multa_480 = NULL;
} elseif ($row_rescisao['motivo'] == '63') {
    $multa_479 = NULL;
    $multa_480 = $row_rescisao['a480'];
}


//Pegando os valores dos moviemntos e inserindo nos campos de acordo com o ANEXO VIII da rescis�o, o n�mero do campo encontra-se na tabela rh_movimento
$qr_movimentos = mysql_query("SELECT B.descicao, B.id_mov, A.valor_movimento, B.campo_rescisao
                            FROM rh_movimentos_clt as A 
                            LEFT JOIN
                            rh_movimentos as B ON (A.id_mov = B.id_mov)
                            WHERE A.id_clt = '$row_rescisao[id_clt]' 
                            AND A.mes_mov = 16 AND A.status = 1") or die(mysql_error());


while ($row_movimentos = mysql_fetch_assoc($qr_movimentos)) {

    
    $movimentos[$row_movimentos['campo_rescisao']] += $row_movimentos['valor_movimento'];
   
    
}

print_r($movimentos);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>Rescis&atilde;o de <?php echo $row_rescisao[id_clt] . ' - ' . $nome; ?></title>
        <link href="rescisao_1.css" rel="stylesheet" type="text/css" />
        <style type="text/css" media="print">
            table.rescisao td.secao {
                background-color:#C0C0C0;
                text-align:center;
                font-size:14px;
                height:20px;
            }
            
            @page {
                margin-left: 65px;
                margin-right: 65px;
            }
        </style>

    </head>
    <body>
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
                    <div class="valor"><?php echo $cnpj_empresa; ?></div>
                </td>
                <td colspan="4">
                    <div class="campo"><span class="numero">02</span> Raz&atilde;o Social/Nome</div>
                    <div class="valor"><?php echo $razao_empresa; ?></div>
                </td>
            </tr>
            <tr>
                <td colspan="4">
                    <div class="campo"><span class="numero">03</span> Endere&ccedil;o (logradouro, n&ordm;, andar, apartamento)</div>
                    <div class="valor"><?php echo $endereco_empresa; ?></div>
                </td>
                <td colspan="2">
                    <div class="campo"><span class="numero">04</span> Bairro</div>
                    <div class="valor"><?php echo $bairro_empresa; ?></div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="campo"><span class="numero">05</span> Munic&iacute;pio</div>
                    <div class="valor"><?php echo $municipio_empresa; ?></div>
                </td>
                <td>
                    <div class="campo"><span class="numero">06</span> UF</div>
                    <div class="valor"><?php echo $uf_empresa; ?></div>
                </td>
                <td>
                    <div class="campo"><span class="numero">07</span> CEP</div>
                    <div class="valor"><?php echo $cep_empresa; ?></div>
                </td>
                <td>
                    <div class="campo"><span class="numero">08</span> CNAE</div>
                    <div class="valor"><?php echo $cnae; ?></div>
                </td>
                <td colspan="2">
                    <div class="campo"><span class="numero">09</span> CNPJ/CEI Tomador/Obra</div>
                    <div class="valor">&nbsp;</div>
                </td>
            </tr>
            <tr>
                <td colspan="6" class="secao">IDENTIFICA&Ccedil;&Atilde;O DO TRABALHADOR</td>
            </tr>
            <tr>
                <td colspan="2">
                    <div class="campo"><span class="numero">10</span> PIS/PASEP</div>
                    <div class="valor"><?php echo $pis; ?></div>
                </td>
                <td colspan="4">
                    <div class="campo"><span class="numero">11</span> Nome</div>
                    <div class="valor"><?php echo $nome; ?></div>
                </td>
            </tr>
            <tr>
                <td colspan="4">
                    <div class="campo"><span class="numero">12</span> Endere&ccedil;o (logradouro, n&ordm;, andar, apartamento)</div>
                    <div class="valor"><?php echo $endereco; ?></div>
                </td>
                <td colspan="2">
                    <div class="campo"><span class="numero">13</span> Bairro</div>
                    <div class="valor"><?php echo $bairro; ?></div>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <div class="campo"><span class="numero">14</span> Munic&iacute;pio</div>
                    <div class="valor"><?php echo $cidade; ?></div>
                </td>
                <td>
                    <div class="campo"><span class="numero">15</span> UF</div>
                    <div class="valor"><?php echo $uf; ?></div>
                </td>
                <td>
                    <div class="campo"><span class="numero">16</span> CEP</div>
                    <div class="valor"><?php echo $cep; ?></div>
                </td>
                <td colspan="2">
                    <div class="campo"><span class="numero">17</span> Carteira de Trabalho (n&ordm;, s&eacute;rie, UF)</div>
                    <div class="valor"><?php echo $cartrab . ' / ' . $serie_cartrab . ' / ' . $uf_cartrab; ?></div>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <div class="campo"><span class="numero">18</span> CPF</div>
                    <div class="valor"><?php echo $cpf; ?></div>
                </td>
                <td>
                    <div class="campo"><span class="numero">19</span> Data de nascimento</div>
                    <div class="valor"><?php echo formato_brasileiro($data_nasci); ?></div>
                </td>
                <td colspan="3">
                    <div class="campo"><span class="numero">20</span> Nome da m&atilde;e</div>
                    <div class="valor"><?php echo $mae; ?></div>
                </td>
            </tr>
            <tr>
                <td colspan="6" class="secao">DADOS DO CONTRATO</td>
            </tr>
            <tr>
                <td colspan="3">
                    <div class="campo"><span class="numero">21</span> Tipo de Contrato</div>
                    <div class="valor">
                        <?php
                        switch ($tipo_contrato) {
                            case 1:
                                echo '1. Contrato de Trabalho por Prazo Indeterminado';
                                break;
                            case 2:
                                echo '2. Contrato de Trabalho por Prazo Determinado';
                                break;
                            case 3:
                                echo '3. Contrato de Trabalho Tempor�rio';
                                break;
                        }
                        ?>
                    </div>
                </td>
                <td colspan="3">
                    <div class="campo"><span class="numero">22</span> Causa do Afastamento</div>
                    <div class="valor"><?php echo $row_motivo['causa_afastamento']; ?></div>
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <div class="campo"><span class="numero">23</span> Remunera&ccedil;&atilde;o M&ecirc;s Anterior Afast.</div>
                    <div class="valor">R$ <?php echo formato_real($row_rescisao['sal_base']); ?></div>
                </td>
                <td>
                    <div class="campo"><span class="numero">24</span> Data de admiss&atilde;o</div>
                    <div class="valor"><?php echo formato_brasileiro($data_entrada); ?></div>
                </td>
                <td>
                    <div class="campo"><span class="numero">25</span> Data do Aviso Pr&eacute;vio</div>
                    <div class="valor"><?php echo formato_brasileiro($data_aviso); ?></div>
                </td>
                <td>
                    <div class="campo"><span class="numero">26</span> Data de afastamento</div>
                    <div class="valor"><?php echo formato_brasileiro($data_demi);
                        ?></div>
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <div class="campo"><span class="numero">27</span> C&oacute;d. afastamento</div>
                    <div class="valor"><?php echo $row_motivo['codigo_afastamento']; ?></div>
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
            <tr>
                <td><span class="numero">50</span> Saldo de <?php echo sprintf('%02d', $row_rescisao['dias_saldo']); ?> dias Sal&aacute;rio (l&iacute;quido de <?php echo $row_rescisao['faltas']; ?> faltas acrescidas do DSR)</td>
                <td><div class="valor">R$ <?php echo formato_real($row_rescisao['saldo_salario']); ?></div></td>
                <td><span class="numero">51</span> Comiss&otilde;es</td>
                <td><div class="valor">R$ <?php echo formato_real($row_rescisao['comissao']); ?></div></td>
                <td><span class="numero">52</span> Gratifica&ccedil;&otilde;es</td>
                <td><div class="valor">R$ <?php echo formato_real($movimentos[52]) ?></div></td>
            </tr>
            <tr>
                <td><span class="numero">53</span> Adicional de Insalubridade</td>
                <td><div class="valor">R$ <?php echo formato_real($row_rescisao['insalubridade']); ?></div></td>
                <td><span class="numero">54</span> Adicional de Periculosidade</td>
                <td><div class="valor">R$<?php echo formato_real($movimentos[54]); ?> </div></td>
                <td><span class="numero">55</span> Adicional Noturno <!--0 horas 20%--></td>
                <td><div class="valor">R$ <?php echo formato_real($movimentos[55]); ?></div></td>
            </tr>
            <tr>
                <td><span class="numero">56</span> Horas Extras 0 horas 0,00%</td>
                <td><div class="valor">R$ <?php echo formato_real($movimentos[56]); ?>  </div></td>
                <td><span class="numero">57</span> Gorjetas</td>
                <td><div class="valor">R$ 0,00</div></td>
                <td><span class="numero">58</span> Descanso Semanal Remunerado (DSR)</td>
                <td><div class="valor">R$ <?php echo formato_real($movimentos[58]); ?></div></td>
            </tr>
            <tr>

                <td><span class="numero">59</span> Reflexo do &quot;DSR&quot; sobre Sal&aacute;rio Vari&aacute;vel</td>
                <td><div class="valor">R$ 0,00</div></td>
                <td><span class="numero">60</span> Multa Art. 477, &sect; 8&ordm;/CLT</td>
                <td><div class="valor">R$ <?php echo formato_real($row_rescisao['a477']); ?></div></td>
                <td><span class="numero">61</span> Multa Art. 479/CLT</td>
                <td><div class="valor">R$ <?php echo formato_real($multa_479); ?></div></td>
            </tr>
            <tr>

                <td><span class="numero">62</span> Sal&aacute;rio-Fam&iacute;lia</td>
                <td><div class="valor">R$ <?php echo formato_real($row_rescisao['sal_familia']); ?></div></td>
                <td><span class="numero">63</span> 13&ordm; Sal&aacute;rio Proporcional <?php echo sprintf('%02d', $row_rescisao['avos_dt']); ?>/12 avos</td>
                <td><div class="valor">R$ <?php echo formato_real($row_rescisao['dt_salario']); ?></div></td>
                <td><span class="numero">64</span> 13&ordm; Sal&aacute;rio Exerc&iacute;cio 0/12 avos</td>
                <td><div class="valor">R$ 0,00</div></td>
            </tr>
            <tr>
                <td><span class="numero">65</span> F&eacute;rias Proporcionais <?php echo sprintf('%02d', $row_rescisao['avos_fp']); ?>/12 avos</td>
                <td><div class="valor">R$ <?php echo formato_real($row_rescisao['ferias_pr']); ?></div></td>
                <td><span class="numero">66</span> F&eacute;rias Vencidas <br />
                    <?php
                    $qr_historico = mysql_query("SELECT * FROM rh_ferias WHERE id_clt = '$row_rescisao[id_clt]' AND status = '1' ORDER BY id_ferias DESC LIMIT 1");
                    $row_historico = mysql_fetch_assoc($qr_historico);
                    
                    ?>
                    Per. Aquisitivo de <?php echo formato_brasileiro($row_historico['data_aquisitivo_ini']); ?> <em>&agrave;</em> <?php echo formato_brasileiro($row_historico['data_aquisitivo_fim']); ?> <br />
                    <?php
                    
                    if ($row_rescisao['ferias_vencidas'] != '0.00') {
                        echo '12';
                    } else {
                        echo '0';
                    }
                    ?>/12 avos</td>
                <td><div class="valor">R$ <?php echo formato_real($row_rescisao['ferias_vencidas']); ?></div></td>

                <td><span class="numero">68</span> Ter&ccedil;o Constitucional de F&eacute;rias</td>
                <td><div class="valor">R$ <?php echo formato_real($row_rescisao['umterco_fv'] + $row_rescisao['umterco_fp']); ?></div></td>
            </tr>

            <tr>

                <td><span class="numero">69</span> <?php echo $tipo_aviso; ?></td>
                <td><div class="valor">R$  <?php echo formato_real($aviso_previo_credito); ?></div></td>
                <td><span class="numero">70</span> 13&ordm; Sal&aacute;rio (Aviso-Pr&eacute;vio Indenizado)</td>
                <td><div class="valor">R$ <?php echo formato_real($row_rescisao['terceiro_ss']); ?></div></td>
                <td><span class="numero">71</span> F&eacute;rias (Aviso-Pr&eacute;vio Indenizado)</td>
                <td><div class="valor"> R$ <?php echo formato_real($row_rescisao['ferias_aviso_indenizado']); ?></div></td>
            </tr>
            <tr>

                <td><span class="numero">72</span> F&eacute;rias em dobro</td>
                <td><div class="valor"> R$ <?php echo formato_real($row_rescisao['fv_dobro']) ?></div></td>
                <td><span class="numero">73</span> 1/3 f&eacute;rias em dobro</td>
                <td><div class="valor">R$ <?php echo formato_real($row_rescisao['um_terco_ferias_dobro']) ?></div></td>
                <td><span class="numero">80</span> Diferen&ccedil;a Salarial</td>
                <td><div class="valor">R$ <?php echo formato_real($movimentos[80]); ?></div></td>

            </tr>

            <tr>
                <td><span class="numero">82</span> Ajuda de Custo Art. 470/CLT</td>
                <td><div class="valor">R$ <?php echo formato_real($movimentos[82]); ?></div></td>
                
                    <?php if (!empty($row_rescisao['lei_12_506'])) { ?>
                        <td><span class="numero">95</span> Lei 12.506</td>
                        <td><div class="valor">R$ <?php echo formato_real($row_rescisao['lei_12_506']); ?></div></td>          
                    <?php } else { ?>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    <?php } ?> 
                
                
                   <?php if (!empty($movimentos[96])) { ?>   
                    <td><span class="numero">96 </span> Indeniza��o Art 9. Lei n� 7.238/84</td>
                    <td ><div class="valor"> R$ <?php echo formato_real($movimentos[96]); ?></div></td>
                    <?php
                } else {
                    echo '<td>&nbsp;</td>
                    <td>&nbsp;</td>';
                }
                ?>    
            </tr>

            <tr>                
                <td><span class="numero">99</span> Ajuste do Saldo Devedor</td>
                <td><div class="valor">R$ <?php echo formato_real($row_rescisao['arredondamento_positivo']); ?></div></td>
                
                    <?php if (!empty($row_rescisao['aux_distancia'])) { ?>   
                    <td><span class="numero">107  </span> Vale Transporte</td>
                    <td ><div class="valor"> R$ <?php echo formato_real($movimentos[107]); ?></div></td>
                    <?php
                } else {
                    echo '<td>&nbsp;</td>
                            <td>&nbsp;</td>';
                }
                ?>
                <?php if (!empty($movimentos[108])) { ?>   
                    <td><span class="numero">108 </span> Vale Refei��o</td>
                    <td ><div class="valor"> R$ <?php echo formato_real($movimentos[108]); ?></div></td>
                    <?php
                } else {
                    echo '<td>&nbsp;</td>
                        <td>&nbsp;</td>';
                }
                ?>
            </tr>   
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td class="secao">TOTAL RESCIS&Oacute;RIO BRUTO</td>
                <td class="secao"><div class="valor">R$ <?php echo formato_real($row_rescisao['total_rendimento']); ?></div></td>
            </tr>



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
            <tr>
                <td><span class="numero">100</span> Pens&atilde;o Aliment&iacute;cia</td>
                <td><div class="valor">R$  <?php echo formato_real($movimentos[100]); ?></div></td>
                <td><span class="numero">101</span> Adiantamento Salarial</td>
                <td><div class="valor">R$ <?php echo formato_real($movimentos[101]); ?></div></td>
                <td><span class="numero">102</span> Adiantamento de 13&ordm; Sal&aacute;rio</td>
                <td><div class="valor">R$ <?php echo formato_real($row_rescisao['adiantamento_13']); ?></div></td> 
            </tr>
            <tr>
                <td><span class="numero">103</span> Aviso-Pr&eacute;vio Indenizado</td>
                <td><div class="valor">R$ <?php echo formato_real($aviso_previo_debito); ?></div></td>
                <td><span class="numero">104</span> Multa Art. 480/CLT</td>
                <td><div class="valor">R$ <?php echo @formato_real($multa_480); ?></div></td>
                <td><span class="numero">105</span> Empr&eacute;stimo em Consigna&ccedil;&atilde;o</td>
                <td><div class="valor">R$ 0,00</div></td>
            </tr>

            <tr>
                <td><span class="numero">106</span> Vale Transporte</td>
                <td><div class="valor">R$ <?php echo formato_real($movimentos[106]); ?></div></td>
                <td><span class="numero">109</span> Vale Alimenta��o</td>
                <td><div class="valor">R$ <?php echo formato_real($movimentos[109]); ?></div></td>

                <td><span class="numero">112.1</span> Previd&ecirc;ncia Social</td>
                <td><div class="valor">R$ <?php echo formato_real($row_rescisao['inss_ss']) ?></div></td>

            </tr>


            <tr>   
                <td><span class="numero">112.2</span> Previd&ecirc;ncia Social - 13&ordm; Sal&aacute;rio</td>
                <td><div class="valor">R$ <?php echo formato_real($row_rescisao['inss_dt']); ?></div></td>
                <td><span class="numero">114.1</span> IRRF</td>
                <td><div class="valor">R$ <?php echo formato_real($row_rescisao['ir_ss']); ?></div></td>
                <td><span class="numero">114.2</span> IRRF sobre 13&ordm; Sal&aacute;rio</td>
                <td><div class="valor">R$ <?php echo formato_real($row_rescisao['ir_dt']); ?></div></td>  
            </tr>
            <tr>
                <?php if (!empty($row_rescisao['devolucao'])) { ?>
                    <td><span class="numero">115<?php echo $i++; ?></span> Devolu&ccedil;&atilde;o de Cr&eacute;dito Indevido</td>
                    <td><div class="valor">R$ <?php echo formato_real($row_rescisao['devolucao']); ?></div></td>        
                    <?php
                } else {
                    echo '   <td>&nbsp;</td>
                     <td>&nbsp;</td>';
                }
                ?>
                <td><span class="numero">115.<?php echo $i++; ?></span> Outros</td>
                <td><div class="valor">R$ <?php echo formato_real($movimentos[115]); ?></div></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td><span class="numero">117</span> Faltas</td>
                <td><div class="valor">R$ <?php echo formato_real($row_rescisao['valor_faltas'] + $movimentos[117]); ?></div></td>
                <td><span class="numero">116</span> IRRF F&eacute;rias</td>
                <td><div class="valor">R$ <?php echo formato_real($row_rescisao['ir_ferias']); ?></div></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>

            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td class="secao">TOTAL DAS DEDU&Ccedil;&Otilde;ES</td>
                <td class="secao">R$ <?php
                if ($row_rescisao[id_clt] == '3881') {
                    echo formato_real($row_rescisao['total_deducao'] + '2168.06');
                } else {
                    echo formato_real($row_rescisao['total_deducao']);
                }
                ?></td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td class="secao">VALOR RESCIS&Oacute;RIO L&Iacute;QUIDO</td>
                <td class="secao">R$ <?php
                if ($row_rescisao[id_clt] == '3881') {
                    echo formato_real($row_rescisao['total_rendimento'] - ($row_rescisao['total_deducao'] + '2168.06'));
                } else {
                    echo formato_real($row_rescisao['total_liquido']);
                }
                ?></td>
            </tr>

        </table>


<?php if ($row_rescisao['um_ano'] == 1) { ?>

            <table  class="rescisao" cellpadding="0" cellspacing="1" style="page-break-before: always; margin-top:20px; ">
                <tr>
                    <td colspan="6" class="secao"><h1>TERMO DE HOMOLOGA��O DE RESCIS�O DO CONTRATO DE TRABALHO</h1></td>
                </tr>     
                <tr>
                    <td colspan="6" class="secao">EMPREGADOR</td>
                </tr>

                <tr>
                    <td colspan="2">
                        <div class="campo"><span class="numero">01</span> CNPJ/CEI</div>
                        <div class="valor"><?php echo $cnpj_empresa; ?></div>
                    </td>
                    <td colspan="4">
                        <div class="campo"><span class="numero">02</span> Raz&atilde;o Social/Nome</div>
                        <div class="valor"><?php echo $razao_empresa; ?></div>
                    </td>
                </tr>

                <td colspan="6" class="secao">TRABALHADOR</td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div class="campo"><span class="numero">10</span> PIS/PASEP</div>
                        <div class="valor"><?php echo $pis; ?></div>
                    </td>
                    <td colspan="4">
                        <div class="campo"><span class="numero">11</span> Nome</div>
                        <div class="valor"><?php echo $nome; ?></div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div class="campo"><span class="numero">17</span> 17 CTPS (n�, s�rie, UF)</div>
                        <div class="valor"><?php echo $cartrab . ' / ' . $serie_cartrab . ' / ' . $uf_cartrab; ?></div>
                    </td>

                    <td colspan="2">
                        <div class="campo"><span class="numero">18</span> CPF</div>
                        <div class="valor"><?php echo $cpf; ?></div>
                    </td>
                    <td>
                        <div class="campo"><span class="numero">19</span> Data de nascimento</div>
                        <div class="valor"><?php echo formato_brasileiro($data_nasci); ?></div>
                    </td>
                    <td colspan="3">
                        <div class="campo"><span class="numero">20</span> Nome da m&atilde;e</div>
                        <div class="valor"><?php echo $mae; ?></div>
                    </td>
                </tr>

                <tr>
                    <td colspan="6" class="secao">CONTRATO</td>
                </tr>

                <tr>   
                    <td colspan="6">
                        <div class="campo"><span class="numero">22</span> Causa do Afastamento</div>
                        <div class="valor"><?php echo $row_motivo['especifica']; ?></div>
                    </td>
                </tr>
                <tr>    
                    <td>
                        <div class="campo"><span class="numero">24</span> Data de admiss&atilde;o</div>
                        <div class="valor"><?php echo formato_brasileiro($data_entrada); ?></div>
                    </td>
                    <td>
                        <div class="campo"><span class="numero">25</span> Data do Aviso Pr&eacute;vio</div>
                        <div class="valor"><?php echo formato_brasileiro($row_rescisao['data_aviso']); ?></div>
                    </td>
                    <td>
                        <div class="campo"><span class="numero">26</span> Data de afastamento</div>
                        <div class="valor"><?php echo formato_brasileiro($data_demi); ?></div>
                    </td>
                    <td>
                        <div class="campo"><span class="numero">27</span> C&oacute;d. afast.</div>
                        <div class="valor"><?php echo $row_motivo['codigo_afastamento']; ?></div>
                    </td>  
                    <td colspan="2">
                        <div class="campo"><span class="numero">29</span>Pens�o Aliment�cia (%) (FGTS)</div>
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
                        <div class="valor"><?php echo $cod_sindicato; ?></div>
                    </td>
                    <td colspan="4">
                        <div class="campo"><span class="numero">32</span> CNPJ e Nome da Entidade Sindical Laboral</div>
                        <div class="valor"><?php echo $row_sindicato['cnpj'] . ' - ' . substr($row_sindicato['nome'], 0, 52); ?></div>
                    </td>
                </tr>

                <tr style="border: 0px;">
                    <td colspan="6" style="border: 0px;">
                        <div class="campo">
                            Foi prestada, gratuitamente, assist&ecirc;ncia na rescis�o do contrato de trabalho, nos termos do art. 477, &sect; 1&ordm;,
                            da Consolida&ccedil;&atilde;o das Leis do Trabalho (CLT), sendo comprovado, neste ato, o efetivo pagamento das verbas rescis&oacute;rias
                            acima especificadas no corpo do TRCT, no valor l�quido de R$ <?php echo formato_real($row_rescisao['total_liquido']); ?>, o qual devidamente rubricado pelas partes, � parte integrante
                            do presente Termo de Homologa��o. <br />
                            </p>
                            <p>As partes assistidas no presente ato de rescis�o contratual foram identificadas como legitimas conforme previsto na Instru��o Normativa/SRT n� 15/2010</p>
                            <p>Fico ressalvado o direito de o trabalhador pleitear judicialmente os direitos informados no camppo 155, abaixo.</p>

                            <p>____________________/___, ____ de _______________________ de _______. </p>
                            <p>&nbsp;</p>
                            <p>&nbsp;</p>
                            <p>___________________________________________________________</br>
                                150 Assinatura do Empregador ou Preposto
                            </p>
                        </div>
                    </td>   
                </tr>

                <tr style="border: 0px;">
                    <td colspan="3" style="border: 0px;">
                        <p>&nbsp;</p>
                        <p>&nbsp;</p>
                        <p>___________________________________________________________</br>
                            151 Assinatura do Trabalhador
                        </p>

                    </td>
                    <td colspan="3" style="border: 0px;">
                        <p>&nbsp;</p>
                        <p>&nbsp;</p> 
                        <p>___________________________________________________________</br>
                            152 Assinatura do Respons�vel Legal do Trabalhador
                        </p>
                    </td>
                </tr>

                <tr style="border: 0px;">
                    <td colspan="3"  style="border: 0px;">
                        <p>&nbsp;</p>
                        <p>&nbsp;</p>
                        <p>___________________________________________________________</br>
                            153 Carimbo e Assinatura do Assistente
                        </p>

                    </td>
                    <td colspan="3"  style="border: 0px;">
                        <p>&nbsp;</p>
                        <p>&nbsp;</p>
                        <p>___________________________________________________________</br>
                            154 Nome do �rg�o Homologador
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
                        <div class="campo"><span class="numero">156</span> Informa��es � CAIXA</div> 
                        <p>&nbsp;</p>

                    </td>
                </tr>   
                <tr>
                    <td colspan="6">
                        <p style="text-align:center;">
                            <strong> ASSIST�NCIA NO ATO DE RESCIS�O CONTRATUAL � GRATUITA.</strong><bR>
                                Pode o trabalhador iniciar a��o judicial quanto aos cr�ditos resultantes das rela��es de trabalho at� o limite de dois anos ap�s a extin��o do contrato de trabalho (inciso XXIX, art. 7� da Constitui��o Federal/1988).
                        </p>
                    </td>
                </tr>
            </table>

<?php } else { ?>

            <table  class="rescisao" cellpadding="0" cellspacing="1" style="page-break-before: always; margin-top:20px; ">
                <tr>
                    <td colspan="6" class="secao"><h1>TERMO DE  QUITA&Ccedil;&Atilde;O DE RESCIS&Atilde;O DO CONTRATO DE TRABALHO</h1></td>
                </tr>     
                <tr>
                    <td colspan="6" class="secao">EMPREGADOR</td>
                </tr>     
                <tr>
                    <td colspan="2">
                        <div class="campo"><span class="numero">01</span> CNPJ/CEI</div>
                        <div class="valor"><?php echo $cnpj_empresa; ?></div>
                    </td>
                    <td colspan="4">
                        <div class="campo"><span class="numero">02</span> Raz&atilde;o Social/Nome</div>
                        <div class="valor"><?php echo $razao_empresa; ?></div>
                    </td>
                </tr>     
                <td colspan="6" class="secao">TRABALHADOR</td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div class="campo"><span class="numero">10</span> PIS/PASEP</div>
                        <div class="valor"><?php echo $pis; ?></div>
                    </td>
                    <td colspan="4">
                        <div class="campo"><span class="numero">11</span> Nome</div>
                        <div class="valor"><?php echo $nome; ?></div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div class="campo"><span class="numero">17</span> CTPS (n�, s�rie, UF)</div>
                        <div class="valor"><?php echo $cartrab . ' / ' . $serie_cartrab . ' / ' . $uf_cartrab; ?></div>
                    </td>

                    <td colspan="2">
                        <div class="campo"><span class="numero">18</span> CPF</div>
                        <div class="valor"><?php echo $cpf; ?></div>
                    </td>
                    <td>
                        <div class="campo"><span class="numero">19</span> Data de nascimento</div>
                        <div class="valor"><?php echo formato_brasileiro($data_nasci); ?></div>
                    </td>
                    <td colspan="3">
                        <div class="campo"><span class="numero">20</span> Nome da m&atilde;e</div>
                        <div class="valor"><?php echo $mae; ?></div>
                    </td>
                </tr>     
                <tr>
                    <td colspan="6" class="secao">CONTRATO</td>
                </tr>
                <tr>   
                    <td colspan="6">
                        <div class="campo"><span class="numero">22</span> Causa do Afastamento</div>
                        <div class="valor"><?php echo $row_motivo['especifica']; ?></div>
                    </td>
                </tr>
                <tr>    
                    <td>
                        <div class="campo"><span class="numero">24</span> Data de admiss&atilde;o</div>
                        <div class="valor"><?php echo formato_brasileiro($data_entrada); ?></div>
                    </td>
                    <td>
                        <div class="campo"><span class="numero">25</span> Data do Aviso Pr&eacute;vio</div>
                        <div class="valor"><?php echo formato_brasileiro($row_rescisao['data_aviso']); ?></div>
                    </td>
                    <td>
                        <div class="campo"><span class="numero">26</span> Data de afastamento</div>
                        <div class="valor"><?php echo formato_brasileiro($data_demi); ?></div>
                    </td>
                    <td>
                        <div class="campo"><span class="numero">27</span> C&oacute;d. Afast.</div>
                        <div class="valor"><?php echo $row_motivo['codigo_afastamento']; ?></div>
                    </td>  
                    <td colspan="2">
                        <div class="campo"><span class="numero">29</span> Pens�o Aliment�cia (%) (FGTS)</div>
                        <div class="valor">0,00%</div>
                    </td>  
                </tr>
                <tr>  
                    <td colspan="6">
                        <div class="campo"><span class="numero">30</span> Categoria do trabalhador</div>
                        <div class="valor">01</div>
                    </td>
                </tr>
                <tr style="border: 0px;">
                    <td colspan="6" style="border: 0px;">
                        <div class="campo">
                            <p> Foi realizada a rescis�o do contrato de trabalho do trabalhador acima qualificado, nos termos do artigo n� 477 da 
                                Consolida��o das Leis do Trabalho (CLT). A assist�ncia � rescis�o prevista no �1� do art. n� 477 da CLT n�o � devida, 
                                tendo em vista a dura��o do contrato de trabalho n�o ser superior a um ano de servi�o e n�o existir previs�o de 
                                assist�ncia � rescis�o contratual em Acordo ou Conven��o Coletiva de Trabalho da categoria a qual pertence o 
                                trabalhador.</p>
                            <p> No dia <?php echo implode('/', array_reverse(explode('-', $row_rescisao['data_demi']))) ?> foi realizado, nos termos do art. 23 da Instru��o Normativa/SRT n� 15/2010, o efetivo pagamento das 
                                verbas rescis�rias especificadas no corpo do TRCT, no valor l�quido de R$ <?php echo number_format($row_rescisao['total_liquido'], 2, ',', '.'); ?> ,o qual, devidamente rubricado pelas partes, � parte integrante do 
                                presente Termo de Quita��o.</p>
                            <br />
                            <p>____________________/___, ____ de _______________________ de _______. </p>
                            <p>&nbsp;</p>
                            <p>&nbsp;</p>
                            <p>___________________________________________________________</br>
                                150 Assinatura do Empregador ou Preposto
                            </p>
                        </div>
                    </td>   
                </tr>
                <tr style="border: 0px;">
                    <td colspan="3" style="border: 0px;">
                        <p>&nbsp;</p>
                        <p>&nbsp;</p>
                        <p>___________________________________________________________</br>
                            151 Assinatura do Trabalhador
                        </p>
                    </td>
                    <td colspan="3" style="border: 0px;">
                        <p>&nbsp;</p>
                        <p>&nbsp;</p> 
                        <p>___________________________________________________________</br>
                            152 Assinatura do Respons�vel Legal do Trabalhador
                        </p>
                    </td>
                </tr> 
                <tr style="border: 0px; height: 300px;">
                    <td colspan="6" style="border: 0px;">   

                    </td>      
                </tr>
                <tr>
                    <td colspan="6">
                        <p style="text-align:center;">
                            <strong> ASSIST�NCIA NO ATO DE RESCIS�O CONTRATUAL � GRATUITA.</strong><bR>
                                Pode o trabalhador iniciar a��o judicial quanto aos cr�ditos resultantes das rela��es de trabalho at� o limite de dois anos ap�s a extin��o do contrato de trabalho (inciso XXIX, art. 7� da Constitui��o Federal/1988).
                        </p>
                    </td>
                </tr>
            </table>
            <div style="page-break-before: always;"></div>

<?php } ?>
<?php } ?>
    </body>
</html>