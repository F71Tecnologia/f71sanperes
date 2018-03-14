<div id="estatisticas">

    <?php
    // Resumo por Movimento
    $movimentos_codigo = array('0001','0002',
        '5029',
        '5037', '5037',
        '4007', '4007',
        '5020', '5031', '5035', '4007',
        '5021', '5030', '5036', '4007',
        '5022', '6005', '5019',
        '7001', '8003', '0003');
    $movimentos_nome = array('SAL&Aacute;RIO','SALDO DE SALARIO RESCISAO',
        'D&Eacute;CIMO TERCEIRO SAL&Aacute;RIO',
        'F&Eacute;RIAS', 'VALOR PAGO NAS F&Eacute;RIAS',
        'RESCIS&Atilde;O', 'VALOR PAGO NA RESCIS&Atilde;O',
        'INSS', 'INSS SOBRE D&Eacute;CIMO TERCEIRO', 'INSS SOBRE F&Eacute;RIAS', 'INSS SOBRE RESCIS&Atilde;O',
        'IRRF', 'IRRF SOBRE D&Eacute;CIMO TERCEIRO', 'IRRF SOBRE F&Eacute;RIAS', 'IRRF SOBRE RESCIS&Atilde;O',
        'SAL&Aacute;RIO FAMILIA', 'SAL&Aacute;RIO MATERNIDADE', 'CONTRIBUI&Ccedil;&Atilde;O SINDICAL',
        'DESCONTO VALE TRANSPORTE', 'DESCONTO VALE REFEI&Ccedil;&Atilde;O','PENSAO');
    $movimentos_tipo = array('CREDITO','DEBITO',
        'CREDITO',
        'CREDITO', 'DEBITO',
        'CREDITO', 'DEBITO',
        'DEBITO', 'DEBITO', 'DEBITO', 'DEBITO',
        'DEBITO', 'DEBITO', 'DEBITO', 'DEBITO',
        'CREDITO', 'CREDITO', 'DEBITO',
        'DEBITO', 'DEBITO','DEBITO');
    $movimentos_valor = array($salario_total,$total_saldo_rescisao,
        $decimo_terceiro_total,
        $ferias_total, $ferias_desconto_total,
        $rescisao_total, $rescisao_desconto_total,
        $inss_total, $inss_dt_total, $inss_ferias_total, $inss_rescisao_total,
        $irrf_total, $irrf_dt_total, $irrf_ferias_total, $irrf_rescisao_total,
        $familia_total, $maternidade_total, $sindicato_total,
        $vale_transporte_total, $vale_refeicao_total,$total_pensao);

    // Criando Array de Movimentos Informativos
    $movimentos_informativos = array('8000');


    // Adicionando Mais Movimentos
    if (!empty($ids_movimentos_estatisticas)) {


        $chave = count($movimentos_codigo);
        $ids_movimentos_estatisticas = implode(',', $ids_movimentos_estatisticas);
        settype($movimentos_listados, 'array');



        $qr_movimentos = mysql_query("SELECT cod_movimento, nome_movimento, SUM(valor_movimento) as total ,tipo_movimento, id_mov 
			   								 FROM `rh_movimentos_clt`
											 WHERE id_movimento IN($ids_movimentos_estatisticas) 
                                                                                         AND id_mov NOT IN(62) AND status = 1
											 GROUP BY id_mov") or die(mysql_error());

//        if($_COOKIE['logado'] == 179){
//            print_r("SELECT cod_movimento, nome_movimento, SUM(valor_movimento) as total ,tipo_movimento, id_mov 
//                        FROM `rh_movimentos_clt`
//                        WHERE id_movimento IN($ids_movimentos_estatisticas) 
//                        AND id_mov NOT IN(62) AND status = 1
//                        GROUP BY id_mov");
//        }

        while ($movimento = mysql_fetch_array($qr_movimentos)) {

            $chave++;
            $movimentos_listados[] = $movimento['id_mov'];
            $movimentos_codigo[] = $movimento['cod_movimento'];
            $movimentos_nome[] = $movimento['nome_movimento'];
            $movimentos_tipo[] = $movimento['tipo_movimento'];
            $movimentos_valor[$chave] = $movimento['total'];
        }

        unset($chave);


        // Organizado as Arrays pelo CÃ³digo
        array_multisort($movimentos_codigo, $movimentos_nome, $movimentos_tipo, $movimentos_valor);
    }
    ?>

    <div id="resumo">

        <table cellspacing="1">
            <tr>
                <td colspan="4" class="secao_pai">Resumo por Movimentos</td>
            </tr>
            <tr class="secao">
                <td width="9%">COD</td>
                <td width="53%" class="movimento">MOVIMENTO</td>
                <td width="19%">RENDIMENTO</td>
                <td width="19%">DESCONTO</td>
            </tr>


            <?php foreach ($movimentos_valor as $chave => $valor) { ?>

                <?php if (!empty($valor)) { ?>
                    <tr class="linha_<?php if ($linha3++ % 2 == 0) {
                echo 'um';
            } else {
                echo 'dois';
            } ?>">
                        <td><?= $movimentos_codigo[$chave] ?></td>
                        <td class="movimento"><?= $movimentos_nome[$chave] ?></td>
                        <?php if ($movimentos_tipo[$chave] == 'CREDITO') {
                            if (!in_array($movimentos_codigo[$chave], $movimentos_informativos)) {
                                $movimentos_credito += $valor;
                            }
                            ?>
                            <td<?php if (in_array($movimentos_codigo[$chave], $movimentos_informativos)) { ?> style="color:#999;"<?php } ?>><?= formato_real($valor) ?></td>
                            <td>&nbsp;</td>   
                    <?php } elseif ($movimentos_tipo[$chave] == 'DEBITO' or $movimentos_tipo[$chave] == 'DESCONTO') {
                        if (!in_array($movimentos_codigo[$chave], $movimentos_informativos)) {
                            $movimentos_debito += $valor;
                        }
                        ?>
                            <td>&nbsp;</td>
                            <td<?php if (in_array($movimentos_codigo[$chave], $movimentos_informativos)) { ?> style="color:#999;"<?php } ?>><?= formato_real($valor) ?></td>       
        <?php } ?>

                    </tr>
    <?php } ?>
<?php } ?>

            <tr class="totais">
                <td colspan="2" align="right">TOTAIS:</td>
                <td><?= formato_real($movimentos_credito) ?></td>
                <td><?= formato_real($movimentos_debito) ?></td>
            </tr>
            <tr class="totais">
                <td colspan="2" align="right">L&Iacute;QUIDO:</td>
                <td><?= formato_real($movimentos_credito - $movimentos_debito) ?></td>
                <td>&nbsp;</td>
            </tr>
        </table>

        <div id="botoes"> 
        	<a href="" onclick="printDiv('estatisticas', '#botoes')" style="float:left; cursor:pointer; background: #E9E9E9; padding: 8px; padding-top: 11px; height: 18px; width: 154px; border-radius: 10px; font-family: verdana; font-size: 10px; font-weight: bold; text-transform: uppercase; color: #A49999; margin-bottom:8px;">Imprimir Estatísticas</a>
                <?php if ($_COOKIE['logado'] != 395) { ?>
            <a href="<?= $link_add_remove ?>" class="add_remove" style="clear:left; background: #E9E9E9; padding: 8px; padding-top: 11px; height: 18px; width: 154px; border-radius: 10px; font-family: verdana; font-size: 10px; font-weight: bold; text-transform: uppercase; color: #A49999;">Adicionar e remover</a>
                
            <?php if ($_REQUEST['btn'] != 1) { ?>
                <a href="sintetica_analitica.php?enc=<?= $_REQUEST['enc'] . '&btn=1' ?>" class="folha_analitica" style="background: #E9E9E9; padding: 8px; padding-top: 11px; height: 18px; width: 154px; border-radius: 10px; font-family: verdana; font-size: 10px; font-weight: bold; text-transform: uppercase; color: #A49999;">Folha Analítica</a>
            <?php } ?>
        <a href="<?= $link_finaliza ?>" data-link="<?= $link_finaliza ?>" class="finaliza valida_saldo_devedor"></a>                        
            <?php            
                }
                $qry_totM = "SELECT A.id_clt, A.nome, A.status AS status_rh, B.data_demi AS dt_demi_recisao, A.data_demi AS dt_demi_clt, A.data_entrada, A.data_aviso
                    FROM rh_clt AS A
                    LEFT JOIN rh_recisao AS B ON(A.id_clt = B.id_clt)
                    LEFT JOIN rh_eventos AS C ON(A.id_clt = C.id_clt)
                    WHERE A.id_clt NOT IN(
                        SELECT A.id_clt FROM rh_folha_proc AS A LEFT JOIN rh_clt AS B ON (A.id_clt = B.id_clt) LEFT JOIN curso AS C ON(B.id_curso = C.id_curso) WHERE A.id_folha = {$folha} AND A.status = 2
                    ) 
                    AND A.id_projeto = '{$row_folha['projeto']}' AND (A.status < 60 OR A.status = 69 OR A.status = 67 OR A.status = 90 OR A.status = 200 OR (B.status = 1 AND YEAR(B.data_demi) = {$ano} AND MONTH(B.data_demi) {$no_rescisao} '{$mes}') OR (A.status = 70 AND C.status = 1) OR (A.status = 80 AND C.status = 1)) AND (DATE_FORMAT(A.data_entrada, '%Y-%m') <= '{$ano}-{$mes}')
                    GROUP BY A.id_clt
                    ORDER BY A.nome ASC";
                $sql_totM = mysql_query($qry_totM) or die(mysql_error());
                $tot_falt = mysql_num_rows($sql_totM);
                
                if (true) {
//                if ($tot_falt == 0) {
                    if (in_array("86", $permissoesFolha)) {
            ?>
                        <a href="<?= $link_finaliza ?>" data-link="<?= $link_finaliza ?>" class="finaliza valida_saldo_devedor" style="background: #E9E9E9; padding: 8px; padding-top: 11px; height: 18px; width: 154px; border-radius: 10px; font-family: verdana; font-size: 10px; font-weight: bold; text-transform: uppercase; color: #A49999;">Finalizar</a>
            <?php
                    }                    
                } else {
                    $msgTrvTot = "A quantidade de participantes da folha não bate com a quantidade de participantes ativos <br>";
                    
                    while($res_totM = mysql_fetch_assoc($sql_totM)){
                        echo "<ul>";                        
                        $msgTrvTot .= "<li>" . $res_totM['id_clt'] . " - " . $res_totM['nome'] . "</li>";
                        echo "</ul>";
                    }
                }           
            ?>
            
            <?php if($_COOKIE['logado'] == 179){ ?>            
                  <a href="<?= $link_finaliza ?>" data-link="<?= $link_finaliza ?>" class="finaliza valida_saldo_devedor" style="background: #E9E9E9; padding: 8px; padding-top: 11px; height: 18px; width: 154px; border-radius: 10px; font-family: verdana; font-size: 10px; font-weight: bold; text-transform: uppercase; color: #A49999;">Finalizar</a>      
            <?php } ?>            
        <?php 
        /**
         * COISA NOVA, CRIAR PARA SO DEIXAR FINALIZAR 
         * SE NAO TIVER MOVIMENTOS DUPLICADOS 
         */
        $queryVerificaMovsDuplicado = "SELECT A.id_clt, B.nome as nome_clt, A.cod_movimento, A.nome_movimento, COUNT(A.cod_movimento) as qnt
                FROM rh_movimentos_clt AS A
                LEFT JOIN rh_clt AS B ON(A.id_clt  = B.id_clt)
                WHERE A.id_projeto = '{$row_folha['projeto']}' AND A.mes_mov = '{$row_folha['mes']}' AND A.ano_mov = '{$row_folha['ano']}' AND A.`status` = 1 AND A.cod_movimento != ''
                GROUP BY A.id_clt, A.cod_movimento HAVING qnt > 1";
        $sqlVerificaMovsDuplicado = mysql_query($queryVerificaMovsDuplicado) or die("Erro ao selecionar movimentos duplicados");
            if(mysql_num_rows($sqlVerificaMovsDuplicado) == 0){
                
                //TOTAL DE PARTICIPANTES
                if ($tot_falt == 0) {
        ?>   
            <!--<a href="javascript:;" data-link="<?= $link_finaliza ?>" class="finaliza valida_saldo_devedor kkk" style="background: #E9E9E9; padding: 8px; padding-top: 11px; height: 18px; width: 154px; border-radius: 10px; font-family: verdana; font-size: 10px; font-weight: bold; text-transform: uppercase; color: #A49999;">Finalizar</a>-->                        
        
                <?php } ?>
            
        </div>    
        
             
         <?php
         }else{ ?>  
        
        </div>
            <p style="background: #f7c6c6;
                      padding: 10px;
                      box-sizing: border-box;
                      margin-top: 12px;
                      color: red;
                      font-weight: bold;
                      font-size: 12px;
                      font-family: verdana;">
            Para finalizar a folha, é necessário resolver os seguintes conflitos, Participantes com Movimentos Duplicados:</p>
            <?php while($rowsVerificaMovsDuplicado = mysql_fetch_assoc($sqlVerificaMovsDuplicado)){ ?>     
                <ul>
                    <li style=" list-style: none;
                                text-align: left;
                                font-size: 12px;">
                        <?php echo $rowsVerificaMovsDuplicado['nome_clt']; ?>
                    </li>    
                </ul>
            <?php } ?>    
        <?php } ?>   
        
    </div>        

    <?php
    if ($qtd_cltFaltando == 1) {
        $msgTrv = "Existe {$qtd_cltFaltando} CLT cadastrado após a abertura da folha, que não entrou";
    } elseif ($qtd_cltFaltando > 1) {
        $msgTrv = "Existem {$qtd_cltFaltando} CLTS cadastrados após a abertura da folha, que não entraram";
    }
    ?>

<?php
////'INSS N&Atilde;O DESCONTADO',
///$total_base_inss_nao_descontado,
// Totalizadores
$queryBaseFgts = "SELECT SUM(base_inss) soma
        FROM rh_folha_proc A
        LEFT JOIN rh_clt B ON (A.id_clt = B.id_clt)
        LEFT JOIN rh_recisao C ON (A.id_clt = C.id_clt)
        WHERE A.id_folha IN ({$row_folha['id_folha']}) AND MONTH(C.data_demi) = {$row_folha['mes']} AND YEAR(C.data_demi) = {$row_folha['ano']} AND B.status IN (61,64,66) AND C.status = 1";

$sqlBaseFgts = mysql_query($queryBaseFgts) or die("erro ao selecionar rescisoes que nao incide em FGTS");
$rows = mysql_fetch_assoc($sqlBaseFgts);
$rescisaoNaoIncidem = $rows['soma'];

$base_fgts_total = $base_fgts_total - $rescisaoNaoIncidem;

$totalizadores_nome = array('L&Iacute;QUIDO', 'BASE DE INSS(FUNCION&Aacute;RIOS SEM DESCONTO)', 'BASE DE INSS', 'INSS', 'INSS DE F&Eacute;RIAS', 'TOTAL INSS', 'INSS (EMPRESA)', 'INSS (RAT)', 'INSS (TERCEIROS)'/* , 'INSS (RECOLHER)' */, 'BASE DE IRRF', 'IRRF', 'DDIR',  'BASE DE FGTS', 'BASE DE FGTS DE F&Eacute;RIAS', 'FGTS' /* , 'FGTS' */);
$totalizadores_valor = array($liquido_total, $total_base_inss_nao_descontado, $base_inss_total, $inss_completo_total, $inss_ferias_total, ($inss_completo_total + $inss_ferias_total), $base_inss_empresa, $base_inss_rat, $base_inss_terceiros, /* (($base_inss_empresa + $base_inss_rat + $base_inss_terceiros + $inss_completo_total) - $familia_total), */ $base_irrf_total , $irrf_completo_total, $ddir_total, ($base_fgts_total), $base_fgts_ferias_total,
    ($base_fgts_total) * 0.08/* , $fgts_completo_total */);
?>

    <div id="totalizadores">
        <table cellspacing="1">
            <tr>
                <td class="secao_pai" colspan="2">Totalizadores</td>
            </tr>
            <tr class="linha_um">
                <td class="secao">PARTICIPANTES:</td>
                <td class="valor"><?= $total_participantes ?></td>
            </tr>
<?php foreach ($totalizadores_valor as $chave => $valor) { ?>
                <tr class="linha_<?php if ($linha2++ % 2 == 0) {
        echo 'dois';
    } else {
        echo 'um';
    } ?>">
                    <td class="secao"><?= $totalizadores_nome[$chave] ?>:</td>
                    <td class="valor"><?= formato_real($valor) ?></td>
                </tr>
<?php } ?>
        </table>

    </div>

</div>

<?php
if (sizeof($ids_movimentos_estatisticas) > 0) {
    $qr_movimentos_faltas = mysql_query("SELECT cod_movimento, nome_movimento, SUM(valor_movimento) as total ,tipo_movimento, id_mov 
			   								 FROM `rh_movimentos_clt`                                                                                         
											 WHERE id_movimento IN($ids_movimentos_estatisticas) 
                                                                                          AND id_mov  = 62
											 GROUP BY id_mov") or die(mysql_error());
    if (mysql_num_rows($qr_movimentos_faltas) != 0) {
        ?>
        <table cellspacing="1" width="300">
            <tr>
                <td colspan="2" class="secao_pai">Total de faltas</td>
            </tr>
            <tr class="secao">
                <td width="9%">COD</td>
                <td width="9%">TOTAL</td>
            </tr>

        <?php
        while ($row_mov2 = mysql_fetch_assoc($qr_movimentos_faltas)):
            ?>  
                <tr class="linha_dois">
                    <td  align="right"  style="font-size:12px;"><?php echo $row_mov2['cod_movimento']; ?></td>
                    <td  align="right" style="font-size:12px;" > <?php echo number_format($row_mov2['total'], 2, ',', '.'); ?></td>                
                </tr>

            <?php
        endwhile;
        ?>
        </table>
        <p style="font-style:italic; text-align: left; font-size: 10px; color: #ff6666; margin-left: 70px;">*As faltas são abatidas no salário base.</p>


    <?php }
}
?>


<div class="clear"></div>

<!--MENSAGEM PARA CLTS CADASTRADOS APÓS FOLHA ABERTA-->
<?php if ($qtd_cltFaltando > 0) { ?>
    <div id='msg_red'>
        <p><?php echo $msgTrv; ?></p>
        <ul>
    <?php while ($res_cltAusente = mysql_fetch_assoc($sql_cltFin)) { ?>
                <li><?php echo $res_cltAusente['id_clt'] . " - " . $res_cltAusente['nome']; ?></li>
    <?php } ?>
        </ul>
        <p><strong>Favor, insira o participante na folha ou altere a data de entrada para o mês seguinte a folha</strong></p>
    </div>
<?php } ?>

<!--MENSAGEM PARA COMPARAÇAO DE TOTAIS DE PARTICIPANTES-->
<?php if ($total_participantes_clt != $total_participantes) { ?>
    <div id='msg_red'>
        <p><?php echo $msgTrvTot; ?></p>       
    </div>
<?php } ?>


<!-- MENSAGEM PARA SALARIO INCOMPATIVEL -->
<?php foreach ($arraySalarioIncompativel as $key => $value) { 
    if($value['liquido'] > ($value['salario'] * 3)){
        $arraySI[$key] = $value;
    }
} 

if($_COOKIE['debug'] == 666){
    echo '<pre>'; print_r($arraySI); echo '</pre>';
}
    
if(count($arraySI) > 0) { ?>
    <div id='msg_red'>
        <p>Salários Incompatíveis:</p>
        <ul>
            <?php foreach ($arraySI as $key => $value) { ?>
                <li><?= $key .' - ' . $value['nome'] . " - " . number_format($value['liquido'],2,',','.'); ?></li>
            <?php } ?>
         </ul>
    </div>
<?php } ?>

<?php 

if ($mes > 1) {
    $mesVerSal50 = $mes - 1;
    $anoVerSal50 = date('Y');
} else if ($mes == 1) {

    $mesVerSal50 = 12;
    $anoVerSal50 = date('Y') - 1;
}

$sqlSalario50 =    "SELECT A.id_clt, A.nome, A.salliquido
                    FROM rh_folha_proc A
                    LEFT JOIN rh_folha B ON A.id_folha = B.id_folha
                    WHERE projeto = 1 AND tipo_terceiro = 3 AND B.mes = $mesVerSal50 AND B.ano = $anoVerSal50";
$querySalario50 = mysql_query($sqlSalario50);

while ($rowSalario50 = mysql_fetch_assoc($querySalario50)) {
    
    $arrSalario50Anterior[$rowSalario50['id_clt']] = $rowSalario50;
    
}

foreach ($arrSalario50Atual as $key => $value) { if ($value > $arrSalario50Anterior[$key]['salliquido']*1.5 && !empty($arrSalario50Anterior[$key])) {
    $countSal50++;
} }
                
if(count($countSal50) > 0) { ?>
    <div id='msg_red'>
        <p>Salarios 50% mais altos que no mês anterior:</p>
        <ul>
            <?php foreach ($arrSalario50Atual as $key => $value) { if ($value > $arrSalario50Anterior[$key]['salliquido']*1.5 && !empty($arrSalario50Anterior[$key])) { ?>
                <li><?= $key .' - ' . $arrSalario50Anterior[$key]['nome'] . " - Atual: R$ " . number_format($value,2,',','.') . " - Anterior: R$ " . number_format($arrSalario50Anterior[$key]['salliquido'],2,',','.'); ?></li>
            <?php } } ?>
         </ul>
    </div>
<?php } ?>

