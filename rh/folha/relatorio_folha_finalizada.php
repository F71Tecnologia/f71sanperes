<?php

// Verificando se o usuário está logado
if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="../../../login.php">Logar</a>';
    exit;
}

if (!empty($_REQUEST['data_xls'])) {
    $dados = utf8_encode($_REQUEST['data_xls']);

    ob_end_clean();
    header("Content-Encoding: iso-8859-1");
    header("Pragma: private");
    header("Cache-control: private, must-revalidate");
    header("Expires: 0");
    header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=relatorio_folha_finalizada.xls");

    echo "\xEF\xBB\xBF";
    echo "<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:x='urn:schemas-microsoft-com:office:excel/' xmlns='http://www.w3.org/TR/REC-html40'>";
    echo "  <head>";
    echo "  <title>Relatório de Folha Finalizada</title>";
    echo "      <!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->";
    echo "  </head>";
    echo "  <body>";
    echo "      $dados";
    echo "  </body>";
    echo "</html>";
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
require_once('../../wfunction.php');

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

if($_COOKIE['logado'] == 179){
    echo "SELECT folha.*,C.id_curso,C.nome as funcao FROM 
	(SELECT A.*, 
	  (SELECT id_curso_de FROM rh_transferencias WHERE id_clt=A.id_clt AND id_curso_de <> id_curso_para AND data_proc >= '2016-07-01' ORDER BY id_transferencia ASC LIMIT 1) AS de,
	  (SELECT id_curso_para FROM rh_transferencias WHERE id_clt=A.id_clt AND id_curso_de <> id_curso_para AND data_proc <= '2016-07-01' ORDER BY id_transferencia DESC LIMIT 1) AS para,
	                                           
	
	B.id_clt as id_curso2, B.nome_banco,C.unidade, B.id_unidade, B.tipo_conta
	FROM rh_folha_proc AS A
	LEFT JOIN rh_clt AS B ON A.id_clt = B.id_clt
        LEFT JOIN unidade AS C ON (B.id_unidade = C.id_unidade)
	WHERE A.id_folha = '$folha' AND A.status IN(3,4)
	ORDER BY A.nome ASC) as folha
LEFT JOIN curso AS C ON (IF(folha.para IS NOT NULL,C.id_curso=folha.para, IF(folha.de IS NOT NULL,C.id_curso=folha.de,C.id_curso=folha.id_curso)))
ORDER BY nome";
}

// Consulta dos Participantes da Folha
$qr_participantes = mysql_query("SELECT folha.*,C.id_curso,C.nome as funcao FROM 
	(SELECT A.*, 
	  (SELECT id_curso_de FROM rh_transferencias WHERE id_clt=A.id_clt AND id_curso_de <> id_curso_para AND data_proc >= '2016-07-01' ORDER BY id_transferencia ASC LIMIT 1) AS de,
	  (SELECT id_curso_para FROM rh_transferencias WHERE id_clt=A.id_clt AND id_curso_de <> id_curso_para AND data_proc <= '2016-07-01' ORDER BY id_transferencia DESC LIMIT 1) AS para,
	                                           
	
	B.id_clt as id_curso2, B.nome_banco,C.unidade, B.id_unidade, B.tipo_conta, B.agencia_dv AS ag_dv, B.conta_dv AS cont_dv
	FROM rh_folha_proc AS A
	LEFT JOIN rh_clt AS B ON A.id_clt = B.id_clt
        LEFT JOIN unidade AS C ON (B.id_unidade = C.id_unidade)
	WHERE A.id_folha = '$folha' AND A.status IN(3,4)
	ORDER BY A.nome ASC) as folha
LEFT JOIN curso AS C ON (IF(folha.para IS NOT NULL,C.id_curso=folha.para, IF(folha.de IS NOT NULL,C.id_curso=folha.de,C.id_curso=folha.id_curso)))
ORDER BY nome");

if($_COOKIE['logado'] == 353){
    echo $qr_participantes;
}

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
            <script src="../../resources/js/tooltip.js"></script>
            <script src="../../resources/js/main.js" type="text/javascript"></script>
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
                <form action="" method="post" class="form-horizontal top-margin1" name="form" id="form">

<!--                <table cellspacing="4" cellpadding="0" id="topo">
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
                </table>-->
                    

                    

                <p style="text-align: right;">
                    <button type="button" value="Exportar" class="btn btn-success" id="exportarExcel"><span class="fa fa-file-excel-o"></span> Exportar para Excel</button>
                        <input type="hidden" id="data_xls" name="data_xls" value="">
                    <button type="submit" form="formPdf" name="pdf" id="pdf" value="pdf" class="btn btn-danger" > Gerar PDF</button>
                </p>
                <div id="relatorio_exp">
                    <table cellpadding="0" cellspacing="1" id="tabela" width="100%">       
                        <tr class="secao">
                            <td>UNIDADE</td>
                            <td>NOME</td>
                            <td>FUNÇÃO</td>
                            <td>DIAS</td>
                            <td>BASE</td>
                            <td>RENDIMENTO</td>
                            <td>DESCONTO</td>
                            <td>INSS</td>
                            <td>IRRF</td>
                            <td>FAMILIA</td>
                            <td>CPF</td>
                            <td>TP CONTA</td>
                            <td>BANCO</td>
                            <td>AGÊNCIA</td>
                            <td>AGÊNCIA DV</td>
                            <td>CONTA</td>
                            <td>CONTA DV</td>
                            <td>LÍQUIDO</td>
                            <td>OBS</td>
                        </tr>
                        <?php 
                            $total_licenca      = 0;
                            $total_ferias       = 0;
                            $total_demissao     = 0;
                            $total_maternidade  = 0;
                            $total_sal_bruto    = 0;
                            $total_sal_liquido  = 0;
                            $unidade_ol=0;

                            while ($row_participante = mysql_fetch_array($qr_participantes)) { 
                            $objFolha->getFichaFinanceira($row_participante['id_clt'], $row_folha['ano'], $row_folha['mes']);
                            $movimentosRendDesc = $objFolha->getDadosFicha();
                            
//                            if($row_participante['id_unidade'] != $unidade_ol){
//                                echo "<tr class=\"secao\"><td colspan=17>{$row_participante['unidade']}</td></tr>";
//                                $unidade_ol=$row_participante['id_unidade'];
//                            }
                        ?>

                            <tr class="linha_<?php if ($linha++ % 2 == 0) { echo 'um'; } else { echo 'dois'; } ?> destaque">

                                <!-- NOME -->    
                                <td><?php echo abreviacao($row_participante['unidade'], 4, 1); ?></td>
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
                                        if(!in_array($folha, array(105,104,103))){
                                            echo "R$ " . number_format($row_participante['sallimpo_real'],2,',','.'); 
                                        }else{
                                            echo "R$ " . number_format($row_participante['salbase'],2,',','.'); 
                                        }
                                    ?>
                                </td>

                                <!-- RENDIMENTO -->
                                <td>
                                    <?php echo "R$ " . number_format($row_participante['rend'],2,',','.'); ?>
                                </td>


                                <!-- DESCONTOS -->
                                <td>
                                    <?php echo "R$ " . number_format($row_participante['desco'],2,',','.'); ?>
                                </td>

                                <!-- INSS -->
                                <td>
                                    <?php echo "R$ " . number_format($row_participante['inss'] + $row_participante['inss_ferias'] + $row_participante['inss_rescisao'],2,',','.'); ?>
                                </td>

                                <!-- IR -->
                                <td>
                                    <?php echo "R$ " . number_format($row_participante['imprenda'] + $row_participante['ir_ferias'] + $row_participante['ir_rescisao'],2,',','.'); ?>
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

                                <!-- CPF -->
                                <td>
                                    <?php echo $row_participante['cpf']; ?>
                                </td>
                                
                                <!-- TP CONTA -->
                                <td>
                                    <?php echo $row_participante['tipo_conta']; ?>
                                </td>

                                <!-- BANCO -->
                                <td>
                                    <?php echo $row_participante['nome_banco']; ?>
                                </td>

                                <!-- AGENCIA -->
                                <td>
                                    <?php echo $row_participante['agencia']; ?>
                                </td>
                                
                                <!-- AGENCIA DV -->
                                <td>
                                    <?php echo $row_participante['ag_dv']; ?>
                                </td>

                                <!-- CONTA -->
                                <td>
                                    <?php echo $row_participante['conta']; ?>
                                </td>
                                
                                <!-- CONTA DV -->
                                <td>
                                    <?php echo $row_participante['cont_dv']; ?>
                                </td>

                                <!-- SALÁRIO LÍQUIDO -->
                                <td>
                                    <?php $total_sal_liquido += $row_participante['salliquido']; ?>
                                    <?php echo "R$ " . formato_real($row_participante['salliquido']); ?>
                                </td>
                                
                                <!-- Observações -->
                                <td>
                                    <?php
                                        if(formato_real($row_participante['salliquido']) == '0,00'){
                                            //Verificação de EVENTOS
                                            $queyVerificaEvento = " 
                                                                SELECT A.id_evento, A.id_clt, A.id_projeto, A.`data`, A.data_retorno, nome_status
                                                                FROM rh_eventos AS A 
                                                                WHERE A.id_clt = '{$row_participante['id_clt']}' AND A.`status` = 1 
                                                                AND (('{$data_inicio}' BETWEEN A.data AND A.data_retorno) OR '{$data_inicio}' >= A.data AND A.data_retorno = '0000-00-00')
                                                                AND cod_status NOT IN (10, 68)
                                                                ORDER BY id_evento DESC
                                                                LIMIT 1";
                                            $sqlVerificaEvento = mysql_query($queyVerificaEvento) or die("Erro ao selecionar da Evento");
                                            $rowsVerificaEvento = mysql_fetch_assoc($sqlVerificaEvento);
                                            $retornoVerificaEvento = "OBS: {$rowsVerificaEvento['nome_status']} de " . implode('/', array_reverse(explode('-', $rowsVerificaEvento['data']))) . " até ";
                                            $retornoVerificaEvento .= ($rowsVerificaEvento['data_retorno'] == '0000-00-00') ? "data indeterminada" : implode('/', array_reverse(explode('-', $rowsVerificaEvento['data_retorno'])));
                                            if($rowsVerificaEvento){
                                                echo $retornoVerificaEvento;
                                            }                 
                                            
                                            //VERIFICAÇÃO DE RECISÃO
                                            $queyVerificaRecisao = "SELECT *
                                                                FROM rh_recisao
                                                                WHERE id_clt = '{$row_participante['id_clt']}' AND data_demi BETWEEN '{$data_inicio}' AND '{$data_fim}' AND STATUS = 1
                                                                ";
                                            $sqlVerificaRecisao = mysql_query($queyVerificaRecisao) or die("Erro ao selecionar da Recisao");
                                            $rowsVerificaRecisao = mysql_fetch_assoc($sqlVerificaRecisao);
                                            if($rowsVerificaRecisao){
                                                echo "RESCISÃO DO CONTRATO DE TRABALHO";
                                            }
                                            
                                            //Verificação de FERIAS
                                            $queyVerificaFerias = " 
                                                                    SELECT A.id_ferias, A.id_clt, A.projeto, A.data_fim, A.data_ini, nome
                                                                    FROM rh_ferias AS A 
                                                                    WHERE A.id_clt = '{$row_participante['id_clt']}' AND A.`status` = 1 
                                                                    AND '{$data_inicio}' BETWEEN A.data_ini AND A.data_fim
                                                                    ORDER BY id_ferias DESC
                                                                    LIMIT 1";
                                            $sqlVerificaFerias = mysql_query($queyVerificaFerias) or die("Erro ao selecionar da ferias");
                                            $rowsVerificaFerias = mysql_fetch_assoc($sqlVerificaFerias);
                                            $retornoVerificaFerias = "OBS: FICOU DE FÉRIAS DE " . implode('/', array_reverse(explode('-', $rowsVerificaFerias['data_ini']))) . " A " . implode('/', array_reverse(explode('-', $rowsVerificaFerias['data_fim'])));                                            
                                            if($rowsVerificaFerias){
                                                echo $retornoVerificaFerias;
                                            }
                                        }
                                    ?>
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
                            
                            <tr>
                                <td colspan="12">Participantes:</td>
                                <td colspan="1"><?php echo mysql_num_rows($qr_participantes) ?></td>
                                <td colspan="5" style="text-align:right"><?php echo 'R$ ' . number_format($total_sal_liquido,2,',','.')?></td>
                            </tr>
                            
                        </table>
                    </div>
                <div class="clear"></div>
                <br />
                
                </form>

            </div>
            
            <form style="display: none" action="../../relatorios/exportTablePdf.php" method="post" id="formPdf">
                        <input type="text" name="titlePdf" id="titlePdf" value="Relatório de Folha Finaliada"/>
                        <textarea name="tabelaPdf" id="tabelaPdf" value="" ></textarea>
                    </form>
        </body>
    </html>
    <script>
        $(function(){
            
                    var tabela = $('#relatorio_exp').html();
//                    var title = $('title').html();
                    $('#tabelaPdf').val(tabela);
//                    $('#titlePdf').val(title);

            
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
            
            $("#exportarExcel").click(function () {
                $("#relatorio_exp img:last-child").remove();

                var html = $("#relatorio_exp").html();

                $("#data_xls").val(html);
                $("#form").submit();
            });
        });
        
         
    </script>