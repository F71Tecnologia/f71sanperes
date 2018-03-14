<?php

include "../../conn.php";
include "../../funcoes.php";
include "../../classes/regiao.php";
include "../../classes/clt.php";
include "../../classes/curso.php";
include "../../wfunction.php";

// RECEBENDO VARIAVEIS
$enc = $_REQUEST['enc'];
$enc = str_replace("--", "+", $enc);
$link = decrypt($enc);
$decript = explode("&", $link);
$regiao = $decript[0];
$clt = $decript[1];
$id_folha = $decript[2];
//

$data = date('d/m/Y');
$ClassDATA = new regiao();
$ClassDATA->RegiaoLogado();
$Clt = new clt();
$Curso = new tabcurso();

$REFolha = mysql_query("SELECT * FROM rh_folha WHERE id_folha = '$id_folha'");
$RowFolha = mysql_fetch_array($REFolha);

$mes = $RowFolha['mes'];
$ano = $RowFolha['ano'];

//REGIÃO
$qr_regiao = mysql_query("SELECT * FROM regioes WHERE id_regiao = '$regiao'");
$row_regiao = mysql_fetch_assoc($qr_regiao);

//PROJETO
$qr_projeto = mysql_query("SELECT * FROM projeto WHERE id_projeto= '$RowFolha[projeto]'");
$row_projeto = mysql_fetch_assoc($qr_projeto);

//MASTER
$qr_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_regiao[id_master]'");
$row_master = mysql_fetch_assoc($qr_master);


//EMPRESA
$qr_empresa = mysql_query("SELECT * FROM rhempresa WHERE id_projeto = '$RowFolha[projeto]' AND id_regiao = '$RowFolha[regiao]'");
$row_empresa = mysql_fetch_assoc($qr_empresa);


//MÊs
$num_mes = sprintf('%02s', $mes);
$nome_mes = mysql_result(mysql_query("SELECT nome_mes FROM ano_meses WHERE num_mes = '$num_mes'"), 0);

$mes_ar = array(
    "01" => "jan",
    "02" => "fev",
    "03" => "mar",
    "04" => "abr",
    "05" => "mai",
    "06" => "jun",
    "07" => "jul",
    "08" => "ago",
    "09" => "set",
    "10" => "out",
    "11" => "nov",
    "12" => "dez"
);

$REfolhaproc = mysql_query("SELECT * FROM rh_folha_proc WHERE id_folha = '$id_folha' AND status = '3'  ORDER BY nome");

//CRIANDO ARQUIVO
$nomeReg = normalizaNometoFile($row_regiao['regiao']);
$nomeFile = "CC_{$id_folha}_{$nomeReg}_{$RowFolha['mes']}.{$RowFolha['ano']}.csv";
$nome_arquivo = "arquivos/{$nomeFile}";
$arquivo = fopen($nome_arquivo, 'w+');

//CABEÇALHO
$ROW_HEADER['ident']    = '1';
$ROW_DETAIL['count']    = '0';
$ROW_HEADER['razao']    = $row_empresa['razao'];
$ROW_HEADER['cnpj']     = $row_empresa['cnpj'];
$ROW_HEADER['ender']    = $row_empresa['endereco'];
$ROW_HEADER['cep']      = $row_empresa['cep'];
$ROW_HEADER['tel']      = $row_empresa['tel'];

$LINHA_HEADER = implode(';', $ROW_HEADER);
fwrite($arquivo, $LINHA_HEADER."\n");

$c = 1;
while ($RowFolhaPro = mysql_fetch_array($REfolhaproc)) {
    //echo "CLT: {$RowFolhaPro['nome']}<BR/>";
    /////////////////////////////////////////////////////////////
    //////////  CÁLCULO DE DEPENDENTES //////////////////////////
    /////////////////////////////////////////////////////////////
    $data_menor21 = mktime(0, 0, 0, $mes, $dia, $ano - 21);
    $menor21 = mysql_query("SELECT * FROM dependentes WHERE id_bolsista = '$RowFolhaPro[id_clt]' AND id_regiao = '$regiao'") or die(mysql_error());

    $row_menor21 = mysql_fetch_assoc($menor21);

    if (mysql_num_rows($menor21) != 0) {
        if ($row_menor21['data1'] != '0000-00-00') {
            $filhos++;
        }
        if ($row_menor21['data2'] != '0000-00-00') {
            $filhos++;
        }
        if ($row_menor21['data3'] != '0000-00-00') {
            $filhos++;
        }
        if ($row_menor21['data4'] != '0000-00-00') {
            $filhos++;
        }
        if ($row_menor21['data5'] != '0000-00-00') {
            $filhos++;
        }
        if ($row_menor21['data6'] != '0000-00-00') {
            $filhos++;
        }

        $data1 = explode('-', $row_menor21['data1']);
        $data1 = @mktime(0, 0, 0, $data1[1], $data1[2], $data1[0]);

        $data2 = explode('-', $row_menor21['data2']);
        $data2 = @mktime(0, 0, 0, $data2[1], $data2[2], $data2[0]);

        $data3 = explode('-', $row_menor21['data3']);
        $data3 = @mktime(0, 0, 0, $data3[1], $data3[2], $data3[0]);

        $data4 = explode('-', $row_menor21['data4']);
        $data4 = @mktime(0, 0, 0, $data4[1], $data4[2], $data4[0]);

        $data5 = explode('-', $row_menor21['data5']);
        $data5 = @mktime(0, 0, 0, $data5[1], $data5[2], $data5[0]);

        $data6 = explode('-', $row_menor21['data6']);
        $data6 = @mktime(0, 0, 0, $data6[1], $data6[2], $data6[0]);

        if ($data1 > $data_menor21 and $row_menor21['data1'] != '0000-00-00') {
            $total_filhos_menor_21++;
        }
        if ($data2 > $data_menor21 and $row_menor21['data2'] != '0000-00-00') {
            $total_filhos_menor_21++;
        }
        if ($data3 > $data_menor21 and $row_menor21['data3'] != '0000-00-00') {
            $total_filhos_menor_21++;
        }
        if ($data4 > $data_menor21 and $row_menor21['data4'] != '0000-00-00') {
            $total_filhos_menor_21++;
        }
        if ($data5 > $data_menor21 and $row_menor21['data5'] != '0000-00-00') {
            $total_filhos_menor_21++;
        }
        if ($data6 > $data_menor21 and $row_menor21['data6'] != '0000-00-00') {
            $total_filhos_menor_21++;
        }
    }
    if (empty($filhos)) {
        $filhos = 0;
    }
    if (empty($total_filhos_menor_21)) {
        $total_filhos_menor_21 = 0;
    }

    $qr_clt = mysql_query("SELECT *,DATE_FORMAT(data_entrada, '%d/%m/%Y')as databr, DATE_FORMAT(data_nasci, '%m') as mes_nasci  FROM rh_clt WHERE id_clt = '$RowFolhaPro[id_clt]'");
    $row_clt = mysql_fetch_assoc($qr_clt);


    //BANCO
    $qr_banco = mysql_query("SELECT razao FROM bancos WHERE id_banco = '{$row_clt['banco']}'");
    $banco = mysql_fetch_assoc($qr_banco);

    /////FUNÇÃO
    $qr_funcao = mysql_query("SELECT * FROM  curso where id_curso = '$row_clt[id_curso]' ");
    $row_funcao = mysql_fetch_assoc($qr_funcao);
    
    //////////////////////////////////////////////////////////////////////
    //////////////////////// INICIO DO PDF ///////////////////////////////
    //////////////////////////////////////////////////////////////////////
    
    $ROW_DETAIL1['ident']    = '2';
    $ROW_DETAIL1['count']    = $c;
    $ROW_DETAIL1['competen'] = $mes_ar[$num_mes] . '/' . $ano;
    $ROW_DETAIL1['cltnome']  = $row_clt['nome'];
    $ROW_DETAIL1['matri']    = $row_clt['matricula'];
    
    $ROW_DETAIL2['ident']   = '2';
    $ROW_DETAIL2['count']   = $c;
    $ROW_DETAIL2['cargo']   = $row_funcao['nome'];
    $ROW_DETAIL2['admis']   = $row_clt['databr'];
    
    $ROW_DETAIL3['ident']   = '2';
    $ROW_DETAIL3['count']   = $c;
    $ROW_DETAIL3['unida']   = $row_clt['locacao'];
    $ROW_DETAIL3['pis']     = $row_clt['pis'];
    
    $ROW_DETAIL4['ident']   = '2';
    $ROW_DETAIL4['count']   = $c;
    $ROW_DETAIL4['cpf']     = $row_clt['cpf'];
    $ROW_DETAIL4['rg']      = $row_clt['rg'];
    $ROW_DETAIL4['ctps']    = $row_clt['campo1'];
    $ROW_DETAIL4['serie']   = $row_clt['serie_ctps'];
    
    $ROW_DETAIL5['ident']   = '2';
    $ROW_DETAIL5['count']   = $c;
    $ROW_DETAIL5['banco']   = $banco['razao'];
    $ROW_DETAIL5['ag']      = $row_clt['agencia'];
    $ROW_DETAIL5['cc']      = $row_clt['conta'];
    
    //EXIBINDO OS MOVIMENTOS
    //salário base
    //verifica_falta
    $qr_movimento_clt = mysql_query("SELECT * FROM rh_movimentos_clt WHERE  id_movimento IN($RowFolha[ids_movimentos_estatisticas]) AND id_clt = '$RowFolhaPro[id_clt]' AND id_mov  IN(62)") or die(mysql_error());
    $row_falta = mysql_fetch_assoc($qr_movimento_clt);

    $salb = $RowFolhaPro['sallimpo_real'] + $row_falta['valor_movimento'];
    $total_vencimentos = $salb;

    $diasTrab = $RowFolhaPro['dias_trab'];

    ////////////////////////////////////////
    ///////////SALÁRIO BASE ///////////////
    ///////////////////////////////////////

    $borda = 0;
    $altura_mov = 0.4;
    $cod_salario = '0001';
    $nome_salario = 'SALÁRIO BASE';

    if ($RowFolha['terceiro'] == 1) {
        unset($cod_salario, $nome_salario, $salb);
    }
    
    $ROW_MOVIMEN[$c][0]['ident']   = '3';
    $ROW_MOVIMEN[$c][0]['count']   = $c;
    $ROW_MOVIMEN[$c][0]['codmo']   = $cod_salario;
    $ROW_MOVIMEN[$c][0]['movim']   = $nome_salario;
    $ROW_MOVIMEN[$c][0]['refer']   = $diasTrab;
    $ROW_MOVIMEN[$c][0]['venci']   = number_format($salb, 2, ',', '.');
    $ROW_MOVIMEN[$c][0]['desco']   = '';
    
    $array_outros_movimentos = array(5060, 5061, 9999, 5913, 5912, 9997, 10008, 10009, 10007, 10006, 10005, 10004, 10003, 10002, 10001, 10000, 6008, 8080, 5049);

    $qr_movimentos = mysql_query("SELECT DISTINCT (cod), descicao, categoria FROM rh_movimentos WHERE mov_lancavel !=1  AND id_mov NOT IN(77) AND cod NOT IN(0001,9991,50241,9996,5024,50250,10012,10014, 5044)  ORDER BY cod ASC") or die(mysql_error());
    $cMov = 1;
    while ($row_mov = mysql_fetch_assoc($qr_movimentos)):
        
        $nome_campo = 'a' . $row_mov['cod'];
        $categoria = $row_mov['categoria'];
        $valor_movimento = $RowFolhaPro[$nome_campo];
        $referencia = "";

        if ($valor_movimento != '0.00' and !in_array($row_mov['cod'], $array_outros_movimentos)) {
            switch ($row_mov['cod']) {
                case '5029': $total_decimo = $valor_movimento;
                    $total_vencimentos += $total_decimo;
                    //echo $total_decimo;
                    break;
                default :
                    if ($row_mov['cod'] == '5031') {
                        $fgts_13 = $valor_movimento;
                    }
                    if ($row_mov['cod'] == '5020') {
                        $referencia = ($RowFolhaPro["t_inss"] * 100) . "%";
                    }
                    if ($row_mov['cod'] == '5021') {
                        $referencia = ($RowFolhaPro["t_imprenda"] * 100) . "%";
                    }
                    if ($row_mov['cod'] == '5030') {
                        $irrf_13 = $valor_movimento;
                    }
                    if ($row_mov['cod'] == '6006') {
                        $referencia = "20%";
                        $nao_exibe_insalubridade = 1;
                    }
                    if ($row_mov['cod'] == '7001') {
                        $referencia = "6%";
                    }

                    if ($categoria == 'CREDITO') {
                        $vencimentos = number_format($valor_movimento, 2, ",", ".");
                        $desconto = '';
                        $total_vencimentos += $valor_movimento;
                    } else if ($categoria == 'DEBITO' or $categoria == 'DESCONTO') {
                        $desconto = number_format($valor_movimento, 2, ",", ".");
                        $vencimentos = '';
                        $total_desconto += $valor_movimento;
                    }
                    
                    $ROW_MOVIMEN[$c][$cMov]['ident']   = '3';
                    $ROW_MOVIMEN[$c][$cMov]['count']   = $c;
                    $ROW_MOVIMEN[$c][$cMov]['codmo']   = $row_mov['cod'];
                    $ROW_MOVIMEN[$c][$cMov]['movim']   = $row_mov['descicao'];
                    $ROW_MOVIMEN[$c][$cMov]['refer']   = $referencia;
                    $ROW_MOVIMEN[$c][$cMov]['venci']   = $vencimentos;
                    $ROW_MOVIMEN[$c][$cMov]['desco']   = $desconto;
                    $cMov++;

            }
        }
    endwhile;


    if (!empty($RowFolhaPro['ids_movimentos'])) {
        $qr_movimento_clt = mysql_query("SELECT A.*,B.percentual FROM rh_movimentos_clt AS A LEFT JOIN rh_movimentos AS B ON (A.id_mov=B.id_mov) WHERE A.id_movimento IN($RowFolha[ids_movimentos_estatisticas]) AND A.id_clt = '$RowFolhaPro[id_clt]' ") or die(mysql_error());
        if (mysql_num_rows($qr_movimento_clt) != 0) {

            while ($row_mov2 = mysql_fetch_assoc($qr_movimento_clt)):

                if ($row_mov2['cod_movimento'] == '6006' and $nao_exibe_insalubridade == 1)
                    continue;

                if ($row_mov2['tipo_movimento'] == 'CREDITO') {
                    $vencimentos = number_format($row_mov2['valor_movimento'], 2, ",", ".");
                    $desconto = '';
                    $total_vencimentos += $row_mov2['valor_movimento'];
                    $total_decimo += $row_mov2['valor_movimento'];
                } else if ($row_mov2['tipo_movimento'] == 'DEBITO' or $row_mov2['tipo_movimento'] == 'DESCONTO') {
                    $desconto = number_format($row_mov2['valor_movimento'], 2, ",", ".");
                    $vencimentos = '';
                    $total_desconto += $row_mov2['valor_movimento'];
                }

                $percent = (strstr($row_mov2['percentual'], ".")) ? ($row_mov2['percentual'] * 100) . "%" : $row_mov2['percentual'];
                $percent = ($percent == 0) ? "1" : $percent;

                if ($row_mov2['cod_movimento'] == "9997") {
                    $qr = "SELECT COUNT(data) as total FROM ano WHERE YEAR(data) = {$ano} AND MONTH(data) = {$mes} AND fds = 1 AND nome = 'Domingo'";
                    $percent = mysql_result(mysql_query($qr), 0);
                }

                if ($RowFolha['terceiro'] != 1) {
                    
                    $ROW_MOVIMEN[$c][$cMov]['ident']   = '3';
                    $ROW_MOVIMEN[$c][$cMov]['count']   = $c;
                    $ROW_MOVIMEN[$c][$cMov]['codmo']   = $row_mov2['cod_movimento'];
                    $ROW_MOVIMEN[$c][$cMov]['movim']   = $row_mov2['nome_movimento'];
                    $ROW_MOVIMEN[$c][$cMov]['refer']   = $percent;
                    $ROW_MOVIMEN[$c][$cMov]['venci']   = $vencimentos;
                    $ROW_MOVIMEN[$c][$cMov]['desco']   = $desconto;
                    $cMov++;
                }
                
                $dif_line += 0.5; 

            endwhile;
        }
    }

    //caso SEJA Décimo terceiro
    if ($RowFolha['terceiro'] == 1) {
        $ROW_MOVIMEN[$c][$cMov]['ident']   = '3';
        $ROW_MOVIMEN[$c][$cMov]['count']   = $c;
        $ROW_MOVIMEN[$c][$cMov]['codmo']   = '5029';
        $ROW_MOVIMEN[$c][$cMov]['movim']   = 'DÉCIMO TERCEIRO';
        $ROW_MOVIMEN[$c][$cMov]['refer']   = '';
        $ROW_MOVIMEN[$c][$cMov]['venci']   = number_format($total_decimo, 2, ',', '.');
        $ROW_MOVIMEN[$c][$cMov]['desco']   = '';
        $cMov++;
    }

    $ROW_TOTAL1['ident']    = '4';
    $ROW_TOTAL1['count']    = $c;
    $ROW_TOTAL1['valbrut']  = "R$ " . number_format($total_vencimentos, 2, ',', '.');
    $ROW_TOTAL1['totalde']  = "R$ " . number_format($total_desconto, 2, ',', '.');
    $ROW_TOTAL1['valorli']  = "R$ " . number_format($RowFolhaPro['salliquido'], 2, ",", ".");
    
  /* LINHA DETALHE DE CALULO */
    if ($RowFolha['terceiro'] == 1) {
        $salario_base = $total_decimo;
        $base_inss = $RowFolhaPro['base_inss'];
        $fgts =  $RowFolhaPro['fgts'];
        $ir = (!empty($irrf_13)) ? $salario_base - $fgts_13 : '0';
    } else {
        $salario_base = $RowFolhaPro['sallimpo'];
        $base_inss = $RowFolhaPro['salbase'];
        $fgts = $RowFolhaPro['fgts'];
        $ir = $RowFolhaPro['base_irrf'];
    }
    
    $ROW_TOTAL2['ident']    = '4';
    $ROW_TOTAL2['count']    = $c;
    $ROW_TOTAL2['salbase']  = "R$ " . number_format($base_inss, 2, ',', '.');
    $ROW_TOTAL2['salario']  = "R$ " . number_format($salario_base, 2, ',', '.');
    $ROW_TOTAL2['valfgts']  = "R$ " . number_format($fgts, 2, ',', '.');
    $ROW_TOTAL2['basirrf']  = "R$ " . number_format($ir, 2, ",", ".");

    ////////////////////////////////////////////////////////////////////////////
    ///////////////////////////// saudacao /////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////
    
    $saudacao = '';
    
    /////////////////////////// DIA DAS MÃES ///////////////////////////////////
    if ($num_mes + 1 == 5 && $row_clt['sexo'] == 'F' && !empty($row_clt['num_filhos'])) {
        $saudacao = "A EMPRESA $row_master[nome] DESEJA-LHE UM FELIZ DIA DAS MÃES!";
    }

    /////////////////////////// DIA DOS PAIS ///////////////////////////////////
    if ($num_mes + 1 == 8 && $row_clt['sexo'] == 'M' && !empty($row_clt['num_filhos'])) {
        $saudacao = "A EMPRESA $row_master[nome] DESEJA-LHE UM FELIZ DIA DAS PAIS!";
    }

    ////////////////////////////////// NATAL ///////////////////////////////////
    if ($num_mes + 1 == 12 && $RowFolha['terceiro'] != 1) {
        $saudacao = "A EMPRESA $row_master[nome] DESEJA-LHE UM FELIZ NATAL E UM PRÓSPERO ANO NOVO!";
    }

    //////////////////////////// AIVERSÁRIO ////////////////////////////////////
    if (sprintf('%02s', $num_mes + 1) == $row_clt['mes_nasci'] || ($num_mes == 12 && $row_clt['mes_nasci'] == 1)) {
        /////////////////////////// DIA DAS MÃES ///////////////////////////////
        if ($num_mes + 1 == 5 && $row_clt['sexo'] == 'F' && !empty($row_clt['num_filhos'])) {
            $saudacao = "A EMPRESA $row_master[nome] DESEJA-LHE UM FELIZ ANIVERSÁRIO E FELIZ DIA DAS MÃES!";
        } elseif
        /////////////////////////// DIA DOS PAIS ///////////////////////////////
        ($num_mes + 1 == 8 && $row_clt['sexo'] == 'M' && !empty($row_clt['num_filhos'])) {
            $saudacao = "A EMPRESA $row_master[nome] DESEJA-LHE UM FELIZ ANIVERSÁRIO E FELIZ DIA DAS PAIS!";
        } elseif
        ////////////////////////////////// NATAL ///////////////////////////////
        ($num_mes + 1 == 12 && $RowFolha['terceiro'] != 1) {
            $saudacao = "A EMPRESA $row_master[nome] DESEJA-LHE UM FELIZ ANIVERSÁRIO, UM FELIZ NATAL E UM PRÓSPERO ANO NOVO!";
        } elseif ($RowFolha['terceiro'] != 1) {
            /////////////////////////////// apenas ainversário /////////////////////
            $saudacao = "A EMPRESA $row_master[nome] DESEJA-LHE UM FELIZ ANIVERSÁRIO!";
        }
    }
    
    $ROW_FOOTER['ident']    = '5';
    $ROW_FOOTER['count']    = $c;
    $ROW_FOOTER['texto']    = $saudacao;
    
    unset($filhos, $total_filhos_menor_21, $total_decimo, $ir, $total_desconto, $total_vencimentos);
    
    //DETALHES
    $LINHA_DETAIL1 = implode(';', $ROW_DETAIL1);
    $LINHA_DETAIL2 = implode(';', $ROW_DETAIL2);
    $LINHA_DETAIL3 = implode(';', $ROW_DETAIL3);
    $LINHA_DETAIL4 = implode(';', $ROW_DETAIL4);
    $LINHA_DETAIL5 = implode(';', $ROW_DETAIL5);
    
    fwrite($arquivo, $LINHA_DETAIL1."\n");
    fwrite($arquivo, $LINHA_DETAIL2."\n");
    fwrite($arquivo, $LINHA_DETAIL3."\n");
    fwrite($arquivo, $LINHA_DETAIL4."\n");
    fwrite($arquivo, $LINHA_DETAIL5."\n");
    
    //MOVIMENTOS
    foreach($ROW_MOVIMEN as $k => $MOVCLT){
        if($c == $k){
            foreach($MOVCLT as $MOV){
                $LINHA_MOV = implode(';', $MOV);
                fwrite($arquivo, $LINHA_MOV."\n");
            }
        }
    }
    
    //TOTALIZADORES
    $LINHA_TOTAL1 = implode(';', $ROW_TOTAL1);
    $LINHA_TOTAL2 = implode(';', $ROW_TOTAL2);
    fwrite($arquivo, $LINHA_TOTAL1."\n");
    fwrite($arquivo, $LINHA_TOTAL2."\n");
    
    //RODAPE
    $LINHA_FOOTER = implode(';', $ROW_FOOTER);
    fwrite($arquivo, $LINHA_FOOTER."\n");
    
    $c++;
}

fclose($arquivo);

header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Content-type: application/x-msdownload");
header("Content-Length: " . filesize($nome_arquivo));
header("Content-Disposition: attachment; filename={$nomeFile}");
flush();

readfile($nome_arquivo);
exit;