<?php

// Verificando se o usuário está logado
if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="../../../login.php">Logar</a>';
    exit;
}

// Incluindo Arquivos
require('../../conn.php');
include('../../funcoes.php');

// Buscando a Folha
list($regiao, $folha) = explode('&', decrypt(str_replace('--', '+', $_REQUEST['enc'])));


// Consulta da Data
$data = mysql_result(mysql_query("SELECT data_proc FROM rh_folha WHERE id_folha = '$folha'"), 0);

// Incluindo Arquivos
include('../../classes/calculos.php');
include('../../classes/abreviacao.php');
include('../../classes/formato_valor.php');
include('../../classes/formato_data.php');
include('../../classes/valor_proporcional.php');
include('../../classes/FolhaClass.php');
include('../../classes/regiao.php');

$Regi = new regiao();
$Trab = new proporcional();
$objFolha = new Folha();
// Consulta da Folha
$qr_folha = mysql_query("SELECT *, date_format(data_inicio, '%d/%m/%Y') AS data_inicio_br,
									  date_format(data_fim, '%d/%m/%Y') AS data_fim_br,
									  date_format(data_proc, '%d/%m/%Y') AS data_proc_br 
							     FROM rh_folha WHERE id_folha = '$folha' AND status = '3'");
$row_folha = mysql_fetch_array($qr_folha);
$data_inicio = $row_folha['data_inicio'];
$data_fim = $row_folha['data_fim'];
$ano = $row_folha['ano'];
$mes = $row_folha['mes'];
$mes_int = (int) $mes;

// Consulta do Usuário que gerou a Folha
$qr_usuario = mysql_query("SELECT nome FROM funcionario WHERE id_funcionario = '$row_folha[user]'");

// Redefinindo Variáveis de Décimo Terceiro
if ($row_folha['terceiro'] != 1) {
    $decimo_terceiro = NULL;
} else {
    $decimo_terceiro = 1;
    $tipo_terceiro = $row_folha['tipo_terceiro'];
}

//empresa
$qr_empresa = mysql_query("SELECT * FROM rhempresa WHERE id_regiao = '$row_folha[regiao]' AND id_projeto = '$row_folha[projeto]]'");
$row_empresa = mysql_fetch_assoc($qr_empresa);

// Consulta da Região
$qr_regiao = mysql_query("SELECT id_regiao, regiao FROM regioes WHERE id_regiao = '$row_folha[regiao]'");
$regiao = mysql_result($qr_regiao, 0, 0);

// Consulta do Projeto
$qr_projeto = mysql_query("SELECT id_projeto, nome, id_master FROM projeto WHERE id_projeto = '$row_folha[projeto]'");
$projeto = mysql_result($qr_projeto, 0, 0);



// Consulta dos Participantes da Folha
$qr_participantes = mysql_query("SELECT folha.*,C.id_curso,C.nome as funcao FROM 
	(SELECT A.*, 
	  (SELECT id_curso_de FROM rh_transferencias WHERE id_clt=A.id_clt AND id_curso_de <> id_curso_para AND data_proc >= '$data_inicio' ORDER BY id_transferencia ASC LIMIT 1) AS de,
	  (SELECT id_curso_para FROM rh_transferencias WHERE id_clt=A.id_clt AND id_curso_de <> id_curso_para AND data_proc <= '$data_inicio' ORDER BY id_transferencia DESC LIMIT 1) AS para,
	                                           
	
	B.id_clt as id_curso2
	FROM rh_folha_proc AS A
	LEFT JOIN rh_clt AS B ON A.id_clt = B.id_clt
	WHERE A.id_folha = '$folha' AND A.status IN(3,4)
	ORDER BY A.nome ASC) as folha
LEFT JOIN curso AS C ON (IF(folha.para IS NOT NULL,C.id_curso=folha.para, IF(folha.de IS NOT NULL,C.id_curso=folha.de,C.id_curso=folha.id_curso)))
      ");
$total_participantes = mysql_num_rows($qr_participantes);

// Definindo Mês da Folha
$meses = array('Erro', 'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro');

if (!empty($decimo_terceiro)) {
    switch ($tipo_terceiro) {
        case 1:
            $mes_folha = '13º Primeira parcela';
            break;
        case 2:
            $mes_folha = '13º Segunda parcela';
            break;
        case 3:
            $mes_folha = '13º Integral';
            break;
    }
} else {
    $mes_folha = "$meses[$mes_int] / $ano";
}

// Percentual RAT
if ($ano >= 2011) {
    $percentual_rat = '0.01';
} else {
    $percentual_rat = '0.03';
}

    // Encriptografando Links
    $link_voltar = 'folha.php?enc=' . str_replace('+', '--', encrypt("$regiao&1")) . '&tela=1';
    $link_lista_banco = 'ver_lista_banco.php?enc=' . str_replace('+', '--', $_REQUEST['enc']) . '&tela=1';
?>
 

<html>
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
            <title>:: Intranet :: Folha Finalizada de CLT (<?= $folha ?>)</title>
            <link href="sintetica/folha.css" rel="stylesheet" type="text/css">
            <link href="../../favicon.ico" rel="shortcut icon">
            <link href="../../js/highslide.css" rel="stylesheet" type="text/css" />
            <script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
            <script src="../../js/highslide-with-html.js" type="text/javascript"></script>
            <script src="../../js/global.js" type="text/javascript"></script>
            <script type="text/javascript">
                hs.graphicsDir = '../../images-box/graphics/';
                hs.outlineType = 'rounded-white';

                $(function() {

                    $('#filtrar').click(function() {
                        var id_funcao = $('#funcoes').val();
                        var nome = $('#pesquisa').val().toLowerCase();
                        if (id_funcao != '') {
                            $('.funcao').each(function(index) {
                                if ($(this).val() == id_funcao) {
                                    $(this).parent().parent().show();
                                    if (nome != '') {
                                        if ($(this).next().val().toLowerCase().search(nome) >= 0) {
                                            $(this).next().parent().parent().show();
                                        } else {
                                            $(this).next().parent().parent().hide();
                                        }
                                    }
                                } else {
                                    $(this).parent().parent().hide();
                                }
                            })
                        }
                        if (nome != '' && id_funcao == '') {
                            $('.nome').each(function(index) {
                                if ($(this).val().toLowerCase().search(nome) >= 0) {
                                    $(this).parent().parent().show();
                                } else {
                                    $(this).parent().parent().hide();
                                }
                            })
                        }
                    })

                   
                });

            </script>
            <style type="text/css">
                .highslide-html-content { width:600px; padding:0px; }
                .rendimentos{
                    background-color:  #033;	
                }
                #tabela tr{
                    font-size:10px;
                    text-align: left;
                    padding: 10px;
                    box-sizing: border-box;
                }	
                #tabela td{
                    height: 30px;
                    text-align: left;
                    padding: 10px;
                    box-sizing: border-box;
                }	
                .totalizador tr, .totalizador td {
                    border: 1px solid #ccc;
                }
                
            </style>
        </head>
        <body>
            <div id="corpo">

                <table cellspacing="4" cellpadding="0" id="topo">
                    <tr height="30">
                        <td width="15%" rowspan="3" valign="middle" align="left">
                            <p><b>IABAS</b></p>
                            <p><b><?php echo "MÊS: ".$mes_folha; ?></b></p>  
                            <p><b>VIAMÃO/RS</b></p>
                            <p><b>SALÁRIO BASE: </b></p>
                            <p><b>DIAS ÚTEIS PRÓXIMO MÊS: </b></p>
                        </td>
                    </tr>
                   <tr>
                        <td width="15%" rowspan="3"  align="left">
                            <p><b>PARTICIPANTES:</b> <?= $total_participantes ?></p>
                            <p><b>TOTAL SALÁRIOS BRUTO:</b> <span id="spanTotalBruto"></span></p>
                            <p><b>TOTAL SALÁRIOS LÍQUIDO:</b> <span id="spanTotalLiquido"></span></p>
                        </td>
                    </tr>
                   <tr>
                        <td width="15%" rowspan="3"  align="left">
                            <p><b>LICENÇA SAÚDE: <span id="spanTotalLicenca"></span></b> </p>
                            <p><b>FÉRIAS:</b> <span id="spanTotalFerias"></span></p>
                            <p><b>DEMISSÃO:</b> <span id="spanTotalDemissao"></span></p>
                            <p><b>LICENÇA MATERNIDADE:</b> <span id="spanTotalMaternidade"></span></p>
                        </td>
                    </tr>
                </table>

                <p style="text-align: right;"><input type="button" onclick="tableToExcel('tabela', 'Folha Analitica')" value="Exportar para Excel" class="exportarExcel"></p>
                <table cellpadding="0" cellspacing="1" id="tabela" width="100%">       
                    <tr class="secao">
                        <td>NOME</td>
                        <td>FUNÇÃO</td>
                        <td>DIAS</td>
                        <td>SALÁRIO</td>
                        <td>INS.</td>
                        <td>GRAT.</td>
                        <td>DIF.SAL</td>
                        <td>AUX.DIST.</td>
                        <td>REFEIÇÃO</td>
                        <td>SAL.FAM</td>
                        <td>BRUTO</td>
                        <td>DESC.</td>
                        <td>LÍQUIDO</td>
                    </tr>
                    <?php 
                        $total_licenca      = 0;
                        $total_ferias       = 0;
                        $total_demissao     = 0;
                        $total_maternidade  = 0;
                        $total_sal_bruto    = 0;
                        $total_sal_liquido  = 0;
                    
                        while ($row_participante = mysql_fetch_array($qr_participantes)) { 
                        $objFolha->getFichaFinanceira($row_participante['id_clt'], $row_folha['ano'], $row_folha['mes']);
                        $movimentosRendDesc = $objFolha->getDadosFicha();
                    ?>
                        
                        <tr class="linha_<?php if ($linha++ % 2 == 0) { echo 'um'; } else { echo 'dois'; } ?> destaque">
                        
                            <!-- NOME -->    
                            <td><?php echo abreviacao($row_participante['nome'], 4, 1); ?></td>
                            <?php
                                $contracheque = str_replace('+', '--', encrypt("$regiao&$row_participante[id_clt]&$folha"));
                                $licensas = array('20', '30',  '51', '52', '80', '90', '100', '110');
                                $maternidade = array('50');
                                $ferias = array('40');
                                $rescisao = array('60', '61', '62', '63', '64', '65', '81', '101');
                                
                                if (in_array($row_participante['status_clt'], $licensas)) {
                                    //echo 'evento';
                                    $total_licenca++;  
                                } elseif (in_array($row_participante['status_clt'], $ferias) and $row_participante['dias_trab'] != 30) {
                                    //echo 'ferias';
                                    $total_ferias++;
                                } elseif (in_array($row_participante['status_clt'], $rescisao)) {
                                    //echo 'rescisao';
                                    $total_demissao++;
                                }  elseif (in_array($row_participante['status_clt'], $maternidade)) {
                                    //echo 'maternidade'
                                    $total_maternidade++;
                                }
                            ?>

                            <!--FUNÇÃO -->
                            <td>
                                <?php
                                //CARREGANDO A CBO
                                if (isset($row_participante['id_curso']) && !empty($row_participante['id_curso'])) {
                                    $qrCurso = mysql_query("SELECT A.nome,B.id_cbo,B.cod,B.nome as nomecbo FROM curso AS A
                                                                    LEFT JOIN rh_cbo AS B ON (B.id_cbo = A.cbo_codigo)
                                                                    WHERE A.id_curso = {$row_participante['id_curso']} LIMIT 1");
                                    $rcurso = mysql_fetch_assoc($qrCurso);
                                    echo $rcurso['nome'];
                                } else {
                                    $dtApuracao = $row_participante['ano'] . "-" . $row_participante['mes'] . "-01";
                                    $qrCurso = mysql_query("SELECT C.id_curso,C.nome,D.cod,D.nome as nomecbo FROM (
                                                                SELECT A.id_clt,B.id_curso,B.rh_horario,
                                                                   (SELECT id_curso_de FROM rh_transferencias WHERE id_clt=A.id_clt AND id_curso_de <> id_curso_para AND data_proc >= '{$dtApuracao}' ORDER BY id_transferencia ASC LIMIT 1) AS de,
                                                                   (SELECT id_curso_para FROM rh_transferencias WHERE id_clt=A.id_clt AND id_curso_de <> id_curso_para AND data_proc <= '{$dtApuracao}' ORDER BY id_transferencia DESC LIMIT 1) AS para,
                                                                   (SELECT id_horario_de FROM rh_transferencias WHERE id_clt=A.id_clt AND id_curso_de <> id_curso_para AND data_proc >= '{$dtApuracao}' ORDER BY id_transferencia ASC LIMIT 1) AS horario_de,
                                                                   (SELECT id_horario_para FROM rh_transferencias WHERE id_clt=A.id_clt AND id_curso_de <> id_curso_para AND data_proc <= '{$dtApuracao}' ORDER BY id_transferencia DESC LIMIT 1) AS horario_para  
                                                                FROM rh_folha_proc AS A
                                                                LEFT JOIN rh_clt AS B ON (A.id_clt=B.id_clt)
                                                                WHERE id_folha IN ({$folha}) AND A.status = 3 
                                                                AND A.id_clt = {$row_participante['id_clt']}) as temp

                                                                LEFT JOIN curso AS C ON (IF(temp.para IS NOT NULL,C.id_curso=temp.para, IF(temp.de IS NOT NULL,C.id_curso=temp.de,C.id_curso=temp.id_curso)))
                                                                LEFT JOIN rh_horarios AS E ON (IF(temp.horario_para IS NOT NULL,E.id_horario=temp.horario_para, IF(temp.de IS NOT NULL,E.id_horario=temp.horario_de,E.id_horario=temp.rh_horario)))
                                                                LEFT JOIN rh_cbo AS D ON (D.id_cbo=C.cbo_codigo)");
                                    $rcurso = mysql_fetch_assoc($qrCurso);
                                    echo $rcurso['nome'];
                                }
                                ?>
                            </td>
                            
                            <!-- DIAS TRABALHADOS -->
                            <td>
                                <?php
                                    if ($row_participante['valor_dt'] != '0.00') {
                                        echo $row_participante['meses'];
                                    } else {
                                        echo $row_participante['dias_trab'];
                                    }
                                ?>
                            </td>
                            
                            
                            <!-- SALÁRIO CONTRATUAL -->
                            <td>
                                <?php 
                                    echo "R$ " . formato_real($row_participante['sallimpo_real']); 
                                ?>
                            </td>
                            
                            <!-- INSS -->
                            <td>
                                <?php echo "R$ " . formato_real($row_participante['inss']); ?>
                            </td>
                            
                            <!-- GRATIFICAÇÂO -->
                            <td> 
                                <?php
                                    $gratificacao = 0;
                                    if (!empty($row_participante['ids_movimentos'])) {
                                        $qr_rendimentos = mysql_query("SELECT * FROM rh_movimentos_clt WHERE id_movimento IN($row_folha[ids_movimentos_estatisticas]) AND tipo_movimento  = 'CREDITO' AND id_clt = '$row_participante[id_clt]' AND id_mov IN(256,231,230,229,228,227,197,196,192)") or die(mysql_error());
                                        while ($row_rendimentos = mysql_fetch_assoc($qr_rendimentos)):
                                            $gratificacao  = $row_rendimentos['valor_movimento'];
                                            echo "R$ " . formato_real($row_rendimentos['valor_movimento']) ;
                                            $total_rendimentos += $row_rendimentos['valor_movimento'];
                                        endwhile;
                                    }
                                    
                                    if($gratificacao == 0){
                                        echo "R$ 0,00";
                                    }
                                ?>
                            </td>
                            
                            <!-- DIFERENÇA SALARIAL -->
                            <td> 
                                <?php
                                    $dif_salarial = 0;
                                    if (!empty($row_participante['ids_movimentos'])) {
                                        $qr_rendimentos = mysql_query("SELECT * FROM rh_movimentos_clt WHERE id_movimento IN($row_folha[ids_movimentos_estatisticas]) AND tipo_movimento  = 'CREDITO' AND id_clt = '$row_participante[id_clt]' AND id_mov IN(14)") or die(mysql_error());
                                        while ($row_rendimentos = mysql_fetch_assoc($qr_rendimentos)):
                                            $dif_salarial = $row_rendimentos['valor_movimento'];
                                            echo "R$ " . formato_real($row_rendimentos['valor_movimento']) ;
                                            
                                            $total_rendimentos += $row_rendimentos['valor_movimento'];
                                        endwhile;
                                    }   
                                    
                                    if($dif_salarial == 0){
                                        echo "R$ 0,00";
                                    }
                                ?>
                            </td>
                            
                            <!-- AUXILIO DISTANCIA -->
                            <td> 
                                <?php
                                    $aux_distancia = 0;
                                    if (!empty($row_participante['ids_movimentos'])) {
                                        $qr_rendimentos = mysql_query("SELECT * FROM rh_movimentos_clt WHERE id_movimento IN($row_folha[ids_movimentos_estatisticas]) AND tipo_movimento  = 'CREDITO' AND id_clt = '$row_participante[id_clt]' AND id_mov IN(193)") or die(mysql_error());
                                        while ($row_rendimentos = mysql_fetch_assoc($qr_rendimentos)):
                                            $aux_distancia = $row_rendimentos['valor_movimento'];
                                            echo "R$ " . formato_real($row_rendimentos['valor_movimento']) ;
                                            
                                            $total_rendimentos += $row_rendimentos['valor_movimento'];
                                        endwhile;
                                    }   
                                    
                                    if($aux_distancia == 0){
                                        echo "R$ 0,00";
                                    }
                                ?>
                            </td>
                            
                            
                            <!-- REFEIÇÃO -->
                            <td> 
                                <?php
                                    $refeicao = 0;
                                    if (!empty($row_participante['ids_movimentos'])) {
                                        $qr_rendimentos = mysql_query("SELECT * FROM rh_movimentos_clt WHERE id_movimento IN($row_folha[ids_movimentos_estatisticas]) AND tipo_movimento  = 'CREDITO' AND id_clt = '$row_participante[id_clt]' AND id_mov IN(65,201)") or die(mysql_error());
                                        while ($row_rendimentos = mysql_fetch_assoc($qr_rendimentos)):
                                            $refeicao = $row_rendimentos['valor_movimento'];
                                            echo "R$ " . formato_real($row_rendimentos['valor_movimento']) ;
                                            
                                            $total_rendimentos += $row_rendimentos['valor_movimento'];
                                        endwhile;
                                    }
                                    
                                    if($refeicao == 0){
                                        echo "R$ 0,00";
                                    }
                                    
                                ?>
                            </td>
                            
                            <!-- SALÁRIO FAMILIA -->
                            <td> 
                                <?php
                                    $familia = 0; 
                                    if (!empty($row_participante['ids_movimentos'])) {
                                        $qr_rendimentos = mysql_query("SELECT * FROM rh_movimentos_clt WHERE id_movimento IN($row_folha[ids_movimentos_estatisticas]) AND tipo_movimento  = 'CREDITO' AND id_clt = '$row_participante[id_clt]' AND id_mov IN(67,92,93,94,95,96,157,158,159,160,189,190,216,217,249,250,299,300)") or die(mysql_error());
                                        while ($row_rendimentos = mysql_fetch_assoc($qr_rendimentos)):
                                            
                                            $familia = $row_rendimentos['valor_movimento'];
                                            echo "R$ " . formato_real($row_rendimentos['valor_movimento']) ;
                                            
                                            $total_rendimentos += $row_rendimentos['valor_movimento'];
                                        endwhile;
                                    }   
                                    
                                    if($familia == 0){
                                        echo "R$ 0,00";
                                    }
                                ?>
                            </td>
                                                        
                            <!-- SALARIO BASE -->
                            <td>
                                <?php $total_sal_bruto += $row_participante['base_inss']; ?>
                                <?php echo "R$ " . number_format($row_participante['base_inss'],2,'.','.'); ?>
                            </td>
                            
                            <!-- DESCONTOS -->
                            <td>
                                <?php echo "R$ " . number_format($row_participante['desco'],2,'.','.'); ?>
                            </td>
                            
                            <!-- SALÁRIO LÍQUIDO -->
                            <td width="10%">
                                <?php $total_sal_liquido += $row_participante['salliquido']; ?>
                                <?php echo "R$ " . formato_real($row_participante['salliquido']); ?>
                            </td>
                            
                        </tr>

                        <?php
                        $totalizador_salario_maternidade += $row_participante['a6005'];
                        $ddir += $row_participante['a5049'];
                        if ($data_entrada > $data_inicio) {
                            //CALCULA TOTAL PARA ADMISSÃO
                            $array_totais["entrada"]["base"] += $row_participante['sallimpo_real'] + $row_participante['valor_dt'];
                            $array_totais["entrada"]["liquido"] += $row_participante['salliquido'];
                        } elseif (in_array($row_participante['status_clt'], $licensas)) {

                            //CALCULA O TOTAL LICENÇA 
                            $array_totais["linceca"]["base"] += $row_participante['sallimpo_real'] + $row_participante['valor_dt'];
                            $array_totais["linceca"]["liquido"] += $row_participante['salliquido'];
                        } elseif (in_array($row_participante['status_clt'], $ferias) and $row_participante['dias_trab'] != 30) {

                            //CALCULA O TOTAL FÉRIASa
                            $array_totais["ferias"]["base"] += $row_participante['sallimpo_real'] + $row_participante['valor_dt'];
                            $array_totais["ferias"]["liquido"] += $row_participante['salliquido'];
                        } elseif (in_array($row_participante['status_clt'], $rescisao)) {

                            //CALCULA O TOTAL RECISAO
                            $array_totais["rescisao"]["base"] += $row_participante['sallimpo_real'] + $row_participante['valor_dt'];
                            $array_totais["rescisao"]["liquido"] += $row_participante['salliquido'];
                        } elseif (!empty($faltas)) {

                            //CALCULA O TOTAL FALTAS
                            $array_totais["faltas"]["base"] += $row_participante['sallimpo_real'] + $row_participante['valor_dt'];
                            $array_totais["faltas"]["liquido"] += $row_participante['salliquido'];
                        }
                    } // Fim do Loop de Participantes 
                    ?>
                    
                        <input type="hidden" name="total_licenca" value="<?php echo $total_licenca; ?>" />    
                        <input type="hidden" name="total_ferias" value="<?php echo $total_ferias; ?>" />    
                        <input type="hidden" name="total_demissao" value="<?php echo $total_demissao; ?>" />    
                        <input type="hidden" name="total_maternidade" value="<?php echo $total_maternidade; ?>" />    
                        <input type="hidden" name="total_bruto" value="<?php echo $total_sal_bruto; ?>" />    
                        <input type="hidden" name="total_liquido" value="<?php echo $total_sal_liquido; ?>" />    
                        
                    </table>
                <div class="clear"></div>
                <br />
                <div id="totalizador">
                    <table cellpadding="0" cellspacing="1" id="tabela" class="totalizador" width="30%" >
                    <tr>
                        <td colspan="3">QUADRO DEMONSTRATIVO DE CUSTO</td>
                    </tr>
                    <tr>
                        <td>ITENS</td>
                        <td>%</td>
                        <td>VALOR</td>
                    </tr>
                    <tr>
                        <td>FOLHA DE PAGAMENTO</td>
                        <td></td>
                        <td><?php echo "R$ " . number_format($total_sal_liquido,2,',','.'); ?></td>
                    </tr>
                    <tr>
                        <td>IRRF</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>FGTS</td>
                        <td>8,00</td>
                        <?php $fgtsT = $total_sal_liquido * 0.08; ?>
                        <td><?php echo "R$ " . number_format($fgtsT ,2,',','.'); ?></td>
                    </tr>
                    <tr>
                        <td>INSS</td>
                        <td>27,8</td>
                        <?php $inssT = $total_sal_liquido * 0.278; ?>
                        <td><?php echo "R$ " . number_format($inssT ,2,',','.'); ?></td>
                    </tr>
                    <tr>
                        <td>FÉRIAS (PROVISÃO)</td>
                        <td>11,11</td>
                        <?php $feriasT = $total_sal_liquido * 0.1111; ?>
                        <td><?php echo "R$ " . number_format($feriasT ,2,',','.'); ?></td>
                    </tr>
                    <tr>
                        <td>PROVISÃO RESCISÓRIOS</td>
                        <td>31,82</td>
                        <?php $rescisaoT = $total_sal_liquido * 0.3182; ?>
                        <td><?php echo "R$ " . number_format($rescisaoT ,2,',','.'); ?></td>
                    </tr>
                    <tr>
                        <td>PIS/DARF</td>
                        <td>1,00</td>
                        <?php $pisT = $total_sal_liquido * 0.01; ?>
                        <td><?php echo "R$ " . number_format($pisT ,2,',','.'); ?></td>
                    </tr>
                    <tr>
                        <td>13° PROVISAO</td>
                        <td>8,33</td>
                        <?php $decimoT = $total_sal_liquido * 0.0833; ?>
                        <td><?php echo "R$ " . number_format($decimoT ,2,',','.'); ?></td>
                    </tr>
                </table>
            </div>
            </div>
            
            
        </body>
    </html>
    <script>
        $(function(){
            //TOTAL BRUTO
            var total_bruto = $("input[name='total_bruto']").val();
            $("#spanTotalBruto").html(total_bruto);
            
            
            //TOTAL LIQUIDO
            var total_liquido = $("input[name='total_liquido']").val();
            $("#spanTotalLiquido").html(total_liquido);
            
            //TOTAL LICENCA
            var total_licenca = $("input[name='total_licenca']").val();
            $("#spanTotalLicenca").html(total_licenca);
            
            
            //TOTAL FERIAS
            var total_ferias = $("input[name='total_ferias']").val();
            $("#spanTotalFerias").html(total_ferias);


            //TOTAL DEMISSAO
            var total_demissao = $("input[name='total_demissao']").val();
            $("#spanTotalDemissao").html(total_demissao);
            
            //TOTAL MATERNIDADE
            var total_maternidade = $("input[name='total_maternidade']").val();
            $("#spanTotalMaternidade").html(total_maternidade);
            
            
        });
    </script>