<?php

// Incluindo Arquivos
require('../../conn.php');
include('../../classes/calculos.php');
include('../../classes/calculos_new.php');
include('../../classes/abreviacao.php');
include('../../classes/formato_valor.php');
include('../../classes/formato_data.php');
include('../../classes/valor_proporcional.php');
include('../../classes/EventoClass.php');
include('../../classes/FeriasClass.php');
include('../../classes/MovimentoClass.php');
include('../../classes/RescisaoClass.php');
include('../../classes/CalculoFolhaClass.php');
include('../../classes_permissoes/acoes.class.php');
include('../../funcoes.php');
include('../../wfunction.php');

// A $DATA2 DEVER¡ SER MAIOR QUE A $DATA1
// O FORMATO DAS DATAS DEVEM SER DD/MM/AAAA
function mesesdiferenca($data1, $data2) {

    if($data1 && $data2) {
        $vetorData1 = explode("/", $data1);
        $vetorData2 = explode("/", $data2);
        $resultado = ($vetorData2[2] - $vetorData1[2]) * 12;
        if ($vetorData1[1] > $vetorData2[1]) {
            $resultado -= ($vetorData1[1] - $vetorData2[1]);
        }else if ($vetorData2[1] > $vetorData1[1]) {
            $resultado += ($vetorData2[1] - $vetorData1[1]);
        }
    }else {
        $resultado = 0;
    }

    return $resultado + 1;
}

if($_COOKIE['logado'] == 87){
error_reporting(1);
}

// Verificando se o usu√°rio est√° logado

if(empty($_COOKIE['logado'])) {
	print 'Efetue o Login<br><a href="../../login.php">Logar</a>';
	exit;
}
// Aumentando Tempo Limite de Resposta do Servidor
set_time_limit(120);

function verifica_array($valor){
    if(!empty($valor)){
        
        return $valor;
        
    }
    
}

// Definindo Classes
$Calc         = new calculos();
$Trab         = new proporcional();
$objFerias    = new Ferias();
$objEvento    = new Eventos();
$objRescisao  = new Rescisao();

// Id da Folha
$enc   = explode('&', decrypt(str_replace('--', '+', $_REQUEST['enc'])));
$folha = $enc[1];




// Consulta da Folha
$qr_folha    = mysql_query("SELECT *, date_format(data_inicio, '%d/%m/%Y') AS data_inicio_br, 
                            date_format(data_fim, '%d/%m/%Y') AS data_fim_br,
                            date_format(data_proc, '%d/%m/%Y') AS data_proc_br 
                            FROM rh_folha WHERE id_folha = '$folha' AND status = '2'");



$row_folha   = mysql_fetch_array($qr_folha);
//
//echo "*************************DATA INICIO******************************";
//print_r($row_folha);
//echo $folha;
//echo "*************************DATA INICIO******************************<br>";
//

$data_inicio = $row_folha['data_inicio'];
$data_fim    = $row_folha['data_fim'];
$ano         = $row_folha['ano'];
$mes         = $row_folha['mes'];
$mes_int     = (int)$mes;

//Classe de movimentos
$objMovimento = new Movimentos();
$objMovimento->carregaMovimentos($ano);

//clase de calculos da folha
$objCalcFolha = new Calculo_Folha();
$objCalcFolha->CarregaTabelas($ano);

        
$CALC_NEW = new Calculos_new($ano);
$acoes_permitidas = new Acoes();
$permissoesFolha = $acoes_permitidas->getAcoes($_COOKIE['logado'], $row_folha['regiao']);


// Consulta do Usu√°rio que gerou a Folha
$qr_usuario = mysql_query("SELECT nome FROM funcionario WHERE id_funcionario = '$row_folha[user]'");

// Redefinindo Vari√°veis de D√©cimo Terceiro
if($row_folha['terceiro'] != 1) {
	$decimo_terceiro = NULL;
} else {
	$decimo_terceiro = 1;
	$tipo_terceiro   = $row_folha['tipo_terceiro'];
}

// Consulta da Regi√£o
$qr_regiao = mysql_query("SELECT id_regiao, regiao FROM regioes WHERE id_regiao = '$row_folha[regiao]'");
$regiao    = mysql_result($qr_regiao, 0, 0);

// Consulta do Projeto
$qr_projeto = mysql_query("SELECT id_projeto, nome, id_master,tipo_folha FROM projeto WHERE id_projeto = '$row_folha[projeto]'");

/////EDITADO POR ANDERSON - TIPO HORISTA
$projeto   			= mysql_result($qr_projeto, 0, 0);
$projeto_tipo_folha = mysql_result($qr_projeto, 0, 3);

$where = "";
//if($_COOKIE['logado'] == 179){
//    $where = " AND B.id_clt IN(4145,4271,4146)"; 
//}

//if ($_COOKIE['logado'] == 345) {
//    $where = ' AND A.id_clt IN (4753,3845,2785,2415)';
//}

// Consulta dos Participantes da Folha
$qr_participantes    = mysql_query("SELECT A.* , B.desconto_inss as desconto_inss_clt, 
    B.matricula as matricula, B.cpf as cpf, DATE_FORMAT(B.data_entrada,'%d/%m/%Y') AS entrada, B.status AS status_clt,
    A.sallimpo AS salBase, C.salario AS salario, B.agencia_dv, B.conta_dv FROM rh_folha_proc as A
    LEFT JOIN rh_clt AS B ON (A.id_clt = B.id_clt)
    LEFT JOIN curso AS C ON(B.id_curso = C.id_curso)
    WHERE A.id_folha = '$folha' AND A.status = '2' $where
    ORDER BY A.nome ASC");


$total_participantes = mysql_num_rows($qr_participantes);

if($decimo_terceiro) {
    $no_rescisao = ">";
}else{
    $no_rescisao = ">=";
}
 
//COMPARA TOTAL DE PARTICIPANTES ATIVOS, COM OS DA FOLHA, PARA TRAVAR O BOT√O FINALIZAR
$sql_participantes_clt = mysql_query("SELECT A.id_clt, A.nome, A.status AS status_rh, B.data_demi AS dt_demi_recisao, A.data_demi AS dt_demi_clt, A.data_entrada, A.data_aviso
                FROM rh_clt AS A
                LEFT JOIN rh_recisao AS B ON(A.id_clt = B.id_clt)
                LEFT JOIN rh_eventos AS C ON(A.id_clt = C.id_clt)
                WHERE A.id_projeto = {$row_folha['projeto']}
                AND (A.status < 60  OR A.status = 69 OR A.status = 67 OR A.status = 90 OR A.status = 200 OR (B.status = 1 AND YEAR(B.data_demi) = {$ano} AND MONTH(B.data_demi) {$no_rescisao} {$mes})
                OR (A.status = 70 AND C.status = 1) OR (A.status = 80 AND C.status = 1))
                AND (DATE_FORMAT(A.data_entrada, '%Y-%m') <= '{$ano}-{$mes}')
                GROUP BY A.id_clt
                ORDER BY A.nome ASC") or die(mysql_error()); 
                //-- AND (A.status < 60 || A.status = 200)
                
//                echo "SELECT A.id_clt, A.nome, A.status AS status_rh, B.data_demi AS dt_demi_recisao, A.data_demi AS dt_demi_clt, A.data_entrada, A.data_aviso
//                FROM rh_clt AS A
//                LEFT JOIN rh_recisao AS B ON(A.id_clt = B.id_clt)
//                LEFT JOIN rh_eventos AS C ON(A.id_clt = C.id_clt)
//                WHERE A.id_projeto = {$row_folha['projeto']}
//                AND (A.status < 60  OR A.status = 69 OR A.status = 67 OR A.status = 90 OR A.status = 200 OR (B.status = 1 AND YEAR(B.data_demi) = {$ano} AND MONTH(B.data_demi) >= {$mes})
//                OR (A.status = 70 AND C.status = 1) OR (A.status = 80 AND C.status = 1))
//                AND (DATE_FORMAT(A.data_entrada, '%Y-%m') <= '{$ano}-{$mes}')
//                GROUP BY A.id_clt
//                ORDER BY A.nome ASC";
                
$total_participantes_clt = mysql_num_rows($sql_participantes_clt);


/*************TRAZENDO O NOME DAS PESSOAS COM PROBLEMAS********************/
$dados_inconsistente = array();

 $query_ver_inconsistencia_l = "SELECT * FROM (
                                        SELECT B.id_clt as id_participante, B.nome as nome_participante
                                        FROM rh_folha_proc AS A
                                                LEFT JOIN rh_clt AS B ON (A.id_clt = B.id_clt)
                                                LEFT JOIN curso AS C ON(B.id_curso = C.id_curso)
                                        WHERE A.id_folha = '{$folha}' AND A.status = '2' AND (B.status < 60 || B.status = 200) $criteria
                                        ORDER BY A.nome ASC) AS tb01

                                        LEFT JOIN (
                                        SELECT A.id_clt, A.nome
                                        FROM rh_clt AS A
                                                LEFT JOIN rh_recisao AS B ON(A.id_clt = B.id_clt)
                                                LEFT JOIN rh_eventos AS C ON(A.id_clt = C.id_clt)
                                        WHERE A.id_projeto = '{$row_folha['projeto']}' AND (A.status < 60 OR A.status = 69 OR A.status = 67 OR A.status = 90 OR A.status = 200 OR (B.status = 1 AND YEAR(B.data_demi) = '{$ano}' 
                                        AND MONTH(B.data_demi) {$no_rescisao} '{$mes}') OR (A.status = 70 AND C.status = 1) OR (A.status = 80 AND C.status = 1)) AND (DATE_FORMAT(A.data_entrada, '%Y-%m') <= '{$ano}-{$mes}')
                                        
                                        GROUP BY A.id_clt ORDER BY A.nome ASC)  AS tb02 
                                    ON(tb01.id_participante = tb02.id_clt)";
                                    
                                    //AND (A.status < 60 || A.status = 200)    
                                        
$query_ver_inconsistencia_left = mysql_query($query_ver_inconsistencia_l) or die("Erro ao identificar inconsistencia");
while($rows_inconsistente_left = mysql_fetch_assoc($query_ver_inconsistencia_left)){
    if(empty($rows_inconsistente_left['id_clt'])){
        //if(($rows_inconsistente_left['status'] < 60) || ($rows_inconsistente_left['status'] == 200)){
            $dados_inconsistente[$rows_inconsistente_left['id_participante']] = $rows_inconsistente_left['nome_participante'];
        //}
    }
}   

                                        
$query_ver_inconsistencia_r = "SELECT * FROM (
                                    SELECT B.id_clt as id_participante, B.nome as nome_participante
                                    FROM rh_folha_proc AS A
                                            LEFT JOIN rh_clt AS B ON (A.id_clt = B.id_clt)
                                            LEFT JOIN curso AS C ON(B.id_curso = C.id_curso)
                                    WHERE A.id_folha = '{$folha}' AND A.status = '2' AND (B.status < 60 || B.status = 200) $criteria
                                    ORDER BY A.nome ASC) AS tb01

                                    RIGHT JOIN (
                                    SELECT A.id_clt, A.nome
                                    FROM rh_clt AS A
                                            LEFT JOIN rh_recisao AS B ON(A.id_clt = B.id_clt)
                                            LEFT JOIN rh_eventos AS C ON(A.id_clt = C.id_clt)
                                    WHERE A.id_projeto = '{$row_folha['projeto']}' AND (A.status < 60 OR A.status = 69 OR A.status = 67 OR A.status = 90 OR A.status = 200 OR (B.status = 1 AND YEAR(B.data_demi) = '{$ano}' 
                                    AND MONTH(B.data_demi) {$no_rescisao} '{$mes}') OR (A.status = 70 AND C.status = 1) OR (A.status = 80 AND C.status = 1)) AND (DATE_FORMAT(A.data_entrada, '%Y-%m') <= '{$ano}-{$mes}')
                                    
                                    GROUP BY A.id_clt ORDER BY A.nome ASC)  AS tb02 
                                ON(tb01.id_participante = tb02.id_clt)";
                                    
                                    //AND (A.status < 60 || A.status = 200)

$query_ver_inconsistencia_right = mysql_query($query_ver_inconsistencia_r) or die("Erro ao identificar inconsistencia");
while($rows_inconsistente_right = mysql_fetch_assoc($query_ver_inconsistencia_right)){
    if(empty($rows_inconsistente_right['id_participante'])){
        $dados_inconsistente[$rows_inconsistente_right['id_clt']] = $rows_inconsistente_right['nome'];
    }
}
        


// Definindo M√™s da Folha
$meses_pt = array('Erro','Janeiro','Fevereiro','Mar√ßo','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro');
if(!empty($decimo_terceiro)) {
	switch($tipo_terceiro) {
		case 1:
		$mes_folha = '13&ordm; Primeira parcela';
		break;
		case 2: 
		$mes_folha = '13&ordm; Segunda parcela';
		break;
		case 3:
		$mes_folha = '13&ordm; Integral';
		break;
	}
} else {
	$mes_folha = "$meses_pt[$mes_int] / $ano";
}

// Criando Array dos Movimentos nas Colunas da Tabela
$qr_codigos = mysql_query("SELECT distinct(cod) FROM rh_movimentos WHERE cod != '0001' ORDER BY cod ASC");
while($codigo = mysql_fetch_array($qr_codigos)) {
	$codigos[] = $codigo['0'];
}

$qr_colunas = mysql_query("DESCRIBE rh_folha_proc");
while($coluna = mysql_fetch_assoc($qr_colunas)) {
    $colunas[] = substr($coluna['Field'], 1);
}

$codigos_posteriores = array('5019', '5020', '5021', '5022', '5029', '5030', '5031', '5035', '5036', '5037', '5049', '6005', '7001', '8003', '80002', '50222', '50492');
$movimentos_tabela   = array_intersect($codigos,$colunas);
$movimentos_tabela   = array_diff($movimentos_tabela,$codigos_posteriores);

// Percentual RAT
$percentual_fap = mysql_result(mysql_query("SELECT percentual FROM rh_movimentos WHERE cod = '9991'"),0);

//if($ano >= 2011) {
//	$percentual_rat = $percentual_fap;
//} else {
//	$percentual_rat = '0.03';
//}

//if ($ano >= 2011 && $ano <= 2014) {
//    $percentual_rat = '0.01';
//}elseif($ano >= 2015){
//    $percentual_rat = '0.0112';
//} else {
//    $percentual_rat = '0.03';
//}

// Percentual RAT
$percentual_rat = '0.02';

/**
 * VERIFICANDO ALIQUOTAS
 * RAT E FAP DO rhempresa
 */
$queryRatFap = "SELECT * FROM rhempresa WHERE id_regiao = '{$row_folha['regiao']}' AND id_projeto = '{$row_folha['projeto']}'";
$sqlRatFap = mysql_query($queryRatFap) or die('Erro ao selecionar aliquotas RAT ou FAP');
$percentual_fap  = 0;
while($rowsRatFap = mysql_fetch_assoc($sqlRatFap)){
    if($_COOKIE['logado'] == 179){
        echo "<pre>";
            print_r($rowsRatFap);
        echo "</pre>";
        //exit();
    }
    
    $percentual_fap = $rowsRatFap['fap'];
    $percentual_rat = $rowsRatFap['aliquotaRat'];
                
}


// Encriptografando Links
$link_voltar     = 'folha.php?enc='.str_replace('+', '--', encrypt("$regiao&1")).'&tela=1';
$link_add_remove = 'folha2.php?enc='.str_replace('+', '--', encrypt("$regiao&$folha&2"));
$link_finaliza   = 'acao_folha.php?enc='.str_replace('+', '--', encrypt("$regiao&$folha"));

// Definindo Usu√°rios para Finalizar a Folha
$acesso_finalizacao = array('5','9','33','82','77','87');


//////////////////////////////////////////////////////////////////
/////DIAS TRABALHADOS DE ACORDO COM PERÕODO SELECIONADO NA FOLHA
//////////////////////////////////////////////////////////////////
  $dt_ini     = explode('-',$data_inicio);
  $dt_ini_seg = mktime(0, 0, 0, $dt_ini[1], $dt_ini[2], $dt_ini[0]);  
  $dt_fim     = explode('-',$data_fim);
  $dt_fim_seg = mktime(0, 0, 0, $dt_fim[1], $dt_fim[2], $dt_fim[0]);
  
  $total_dias_folha = round(($dt_fim_seg - $dt_ini_seg)/86400)+1;    //TOTAL DE DIAS ENTRE A DATA DE INICIO E T…RMINO DA FOLHA
  $ultimo_dia_mes   = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);
  
  
  
   if($mes_int != 2) {      
       
      
                if($ultimo_dia_mes == 31) {$total_dias_folha = $total_dias_folha - 1;}
           }else {   
               
               
               if( $total_dias_folha == $ultimo_dia_mes){ 
                   $total_dias_folha = 30;
               }     
           }
           

           
           
 //BUSCANDO INFORMA«√O DO MOVIMENTOS
$qr_movimentos = mysql_query("SELECT * FROM rh_movimentos WHERE mov_lancavel = 1 OR id_mov IN(259,258)");
while($row_movimento = mysql_fetch_assoc($qr_movimentos)){

    $INF_MOVIMENTOS[$row_movimento['id_mov']]['cod'] = $row_movimento['cod'];
    $INF_MOVIMENTOS[$row_movimento['id_mov']]['categoria'] = $row_movimento['categoria'];
    $INF_MOVIMENTOS[$row_movimento['id_mov']]['descicao'] = $row_movimento['descicao'];

}           

 
           
?>