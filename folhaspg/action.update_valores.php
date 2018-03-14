<?php

include('../conn.php');
include('../classes/calculos.php');

//$Calc_ir = new calculos();
$Calc = new calculos();


$id_folha_participante = $_GET['id_folha_participante'];

$liquido = mysql_real_escape_string($_GET['liquido']);
$descontos = mysql_real_escape_string($_GET['descontos']);
$ajuda_custo = mysql_real_escape_string($_GET['ajuda_custo']);
$sal_base = mysql_real_escape_string($_GET['sal_base']);
$id_cooperado = mysql_real_escape_string($_GET['id_coop']);
$ano_folha = mysql_real_escape_string($_GET['ano_folha']);
$mes_anterior = mysql_real_escape_string($_GET['mes_anterior']);
$horas_trabalhadas = mysql_real_escape_string($_GET['horas_trab']);
$rendimentos = mysql_real_escape_string($_GET['rendimentos']);
$valor_hora = mysql_real_escape_string($_GET['valor_hora']);
$irrf = mysql_real_escape_string($_GET['irrf']);

//if (isset($id_folha_participante)) {
//
//    $update = mysql_query("UPDATE folha_cooperado SET adicional = '$rendimentos',
//                                                    faltas = '$horas_trabalhadas',
//                                                    desconto = '$descontos',
//                                                    ajuda_custo = '$ajuda_custo',
//                                                    salario_liq = '$liquido',
//                                                    valor_hora = $valor_hora,
//                                                    ano = $ano_folha,
//                                                    irrf = $irrf
//                                                    WHERE id_folha_pro = '" . $id_folha_participante . "' LIMIT 1") or die(mysql_error());
//}

////////////////////////////////////////////////////////////////////////////////
// id_projeto e id_folha
$qr_projeto = mysql_query("select projeto,id_folha from folha_cooperado where id_folha_pro = '$id_folha_participante'");
$resp = mysql_fetch_assoc($qr_projeto);
$id_projeto = $resp['projeto'];
$folha = $resp['id_folha'];

// Consulta da Folha
$qr_folha = mysql_query("SELECT data_inicio FROM folhas WHERE id_folha = '$folha' AND status = '2'");
$row_folha = mysql_fetch_array($qr_folha);
$data_inicio = $row_folha['data_inicio'];
////////////////////////////////////////////////////////////////////////////////




////////////////////////////////////////////////
///////////////////// Calculando INSS  ///////
///////////////////////////////////////////////

$base_inss = $sal_base;
$base_inss += $rendimentos;
$base_inss -= $descontos;

// Consulta de Dados do Participante
$qr_cooperado = mysql_query("SELECT * FROM autonomo WHERE id_autonomo = '$id_cooperado'");
$row_cooperado = mysql_fetch_array($qr_cooperado);


$qr_inss = mysql_query("SELECT faixa, fixo, percentual, piso, teto
						   FROM rh_movimentos
						  WHERE cod = '5024'
                                                        AND '{$row_folha['data_inicio']}' BETWEEN data_ini AND data_fim");

$row_inss = mysql_fetch_array($qr_inss);

$minimo_inss = $row_inss['piso'];
$maximo_inss = $row_inss['teto'];

// Verifica se o INSS é Definido ou Percentual
if ($row_cooperado['tipo_inss'] == 1) { // Valor Definido
    $inss = $row_cooperado['inss'];
} else { // Valor Percentual
    if ($row_cooperado['tipo_contratacao'] == 3) {
        $taxa_inss = $row_cooperado['inss'] / 100;
    } else {
        $taxa_inss = 0.2;
    }


    $inss = $base_inss * $taxa_inss;
    
    if(isset($maximo_inss) && !empty($maximo_inss)){    
        if ($inss > $maximo_inss) {
            $inss = $maximo_inss;
        }
    }
    
}

//echo "<pre>";
//    print_r("Teto Inss: " . $maximo_inss . "<br />");
//    print_r("Tipo Inss: " . $row_cooperado['tipo_inss'] . "<br />");
//    print_r("INSS: " . $row_cooperado['inss']. "<br />");
//    print_r("Taxa INSS: " . $row_cooperado['inss'] / 100 . "<br />");
//    print_r("Tipo Contrata��o: " . $row_cooperado['tipo_contratacao'] . "<br />");
//    print_r("Base INSS: " . $base_inss . "<br />");
//    print_r("Valor INSS Final: " . $inss . "<br />");
//    
//echo "</pre>";
//exit();

//////////////////////////////////////////////////
//////////////////////////////////////////////////
/////////////  calculo do IR     ///////////////////
///////////////////////////////////////////////////
//$base_irrf = $sal_base + $rendimentos - $descontos - $inss;
//
//$minimo_irrf = 10.00;
//
//// Vestígio de IRRF do mês anterior
//$qr_vestigio = mysql_query("SELECT irrf FROM folha_cooperado WHERE mes = '$mes_anterior' AND id_autonomo = '$id_cooperado' 
//AND status = '3' AND irrf <= '$minimo_irrf'");
//$row_vestigio = mysql_fetch_array($qr_vestigio);
//$total_vestigio = mysql_num_rows($qr_vestigio);
//$valor_vestigio = $row_vestigio['irrf'];
//
/////verifica se entra IR
//$qr_ddir = mysql_query("SELECT * FROM rh_movimentos WHERE cod= '5021' AND anobase = '2011' AND faixa = 1 ") or die(mysql_error());
//$row_ddir = mysql_fetch_assoc($qr_ddir);
//
//
//
//
//if ($base_irrf > $row_ddir['v_ini']) {
//
//    // Dependentes
//    $qr_projeto = mysql_query("select projeto from folha_cooperado where id_folha_pro = '$id_folha_participante'");
//    $id_projeto = mysql_fetch_row($qr_projeto);
//    $RE_Depe = mysql_query("SELECT * FROM dependentes WHERE id_bolsista = '$id_cooperado' and id_projeto = " . $id_projeto['0'] . " and contratacao = 3 and (nome1 <> '' or nome2 <> '' or nome3 <> '' or nome4 <> '' or nome5 <> '' or nome6 <> '') ");
//    $RowDepe = mysql_fetch_array($RE_Depe);
//    $total_dep = mysql_num_rows($RE_Depe);
//
//    $irrf = $base_irrf;
//    
//    $anoDependente = date("Y-m-d", strtotime("-21 years"));
//    
//    /////Verificação de dependentes
//    if ($total_dep != 0) {
//        $qr_ddir = mysql_query("SELECT * FROM rh_movimentos WHERE cod= '5049' AND anobase = '$ano_folha'");
//        $row_ddir = mysql_fetch_assoc($qr_ddir);
//
//        for ($i = 1; $i <= 6; $i++) {
//            if (!empty($RowDepe['nome' . $i])) {
//                if ($RowDepe['data' . $i] > $anoDependente) {
//                    $irrf -= $row_ddir['fixo'];
//                }
//            }
//        }
//    }
//    
//    ///Verificando faixa dO Imposto de renda
//    $qr_ddir = mysql_query("SELECT * FROM rh_movimentos WHERE cod= '5021' AND anobase = '$ano_folha' ORDER BY faixa ASC");
//    while ($row_ddir = mysql_fetch_assoc($qr_ddir)):
//
//        if ($irrf > $row_ddir['v_ini'] and $irrf < $row_ddir['v_fim']) {
//
//            $irrf2 = $irrf * $row_ddir['percentual'];
//            $irrf2 = $irrf2 - $row_ddir['fixo'];
//        }
//    endwhile;
//} else {
//
//    $irrf2 = '0.00';
//}
//
//if ($irrf2 < $minimo_irrf) {
//    $resultado_irrf = '0.00';
//} else {
//    $resultado_irrf = $irrf2;
//}

$base_irrf = $sal_base + $rendimentos - $descontos - $inss;

$minimo_irrf = 10.00;

// Vestígio de IRRF do mês anterior
$qr_vestigio = mysql_query("SELECT irrf FROM folha_cooperado WHERE mes = '$mes_anterior' AND id_autonomo = '$id_cooperado' 
AND status = '3' AND irrf <= '$minimo_irrf'");
$row_vestigio = mysql_fetch_array($qr_vestigio);
$total_vestigio = mysql_num_rows($qr_vestigio);
$valor_vestigio = $row_vestigio['irrf'];

// Calculando IRRF
$Calc->MostraIRRF($base_irrf, $id_cooperado, $id_projeto, $data_inicio);
$irrf = $Calc->valor + $valor_vestigio;

// Esta variável será utilizada somente para gravar na base de dados
$gravar_irrf = $irrf;

if ($irrf < $minimo_irrf) {
    $irrf = 0;
}
$resultado_irrf = $irrf;

$liquido = $_GET['total_parcial'] - $inss - $irrf;

if (isset($id_folha_participante)) {
    $qr = "UPDATE folha_cooperado SET adicional = '$rendimentos', faltas = '$horas_trabalhadas', desconto = '$descontos', ajuda_custo = '$ajuda_custo',  salario_liq = '$liquido', valor_hora = $valor_hora, ano = $ano_folha, irrf = $irrf WHERE id_folha_pro = '" . $id_folha_participante . "' LIMIT 1;\r\n";
    $update = mysql_query($qr) or die(mysql_error());
}
// Criando Nome do Arquivo
$nome_arquivo    = 'cooperado_'.$folha.'.txt';
$caminho_arquivo = '../arquivos/folhacooperado/'.$nome_arquivo; 

// Abre o Arquivo TXT
if(!$arquivo = fopen($caminho_arquivo, 'a')) {
    echo "Erro abrindo arquivo ($caminho_arquivo)";
	exit;
}

// Escreve no Arquivo TXT
if(!fwrite($arquivo, $qr)) {
	echo "Erro escrevendo no arquivo ($caminho_arquivo)";
	exit;
}

echo json_encode(array('irrf' => $resultado_irrf, 'inss' => $inss, 'liquido' => $liquido));
