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
include('../../classes_permissoes/acoes.class.php');
include('../../funcoes.php');
include('../../wfunction.php');

if($_COOKIE['logado'] == 87){
error_reporting(1);
}

// Verificando se o usuário está logado
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
$Calc = new calculos();
$Trab = new proporcional();
$objFerias = new Ferias();


// Id da Folha
$enc   = explode('&', decrypt(str_replace('--', '+', $_REQUEST['enc'])));
$folha = $enc[1];

// Consulta da Folha
$qr_folha    = mysql_query("SELECT *, date_format(data_inicio, '%d/%m/%Y') AS data_inicio_br, 
                            date_format(data_fim, '%d/%m/%Y') AS data_fim_br,
                            date_format(data_proc, '%d/%m/%Y') AS data_proc_br 
                            FROM rh_folha WHERE id_folha = '$folha' AND status = '2'");

$row_folha   = mysql_fetch_array($qr_folha);
$data_inicio = $row_folha['data_inicio'];
$data_fim    = $row_folha['data_fim'];
$ano         = $row_folha['ano'];
$mes         = $row_folha['mes'];
$mes_int     = (int)$mes;

$CALC_NEW = new Calculos_new($ano);
$acoes_permitidas = new Acoes();
$dados = $acoes_permitidas->getAcoes($_COOKIE['logado'], $row_folha['regiao']);


// Consulta do Usuário que gerou a Folha
$qr_usuario = mysql_query("SELECT nome FROM funcionario WHERE id_funcionario = '$row_folha[user]'");

// Redefinindo Variáveis de Décimo Terceiro
if($row_folha['terceiro'] != 1) {
	$decimo_terceiro = NULL;
} else {
	$decimo_terceiro = 1;
	$tipo_terceiro   = $row_folha['tipo_terceiro'];
}

// Consulta da Região
$qr_regiao = mysql_query("SELECT id_regiao, regiao FROM regioes WHERE id_regiao = '$row_folha[regiao]'");
$regiao    = mysql_result($qr_regiao, 0, 0);

// Consulta do Projeto
$qr_projeto = mysql_query("SELECT id_projeto, nome, id_master,tipo_folha FROM projeto WHERE id_projeto = '$row_folha[projeto]'");

/////EDITADO POR ANDERSON - TIPO HORISTA
$projeto   			= mysql_result($qr_projeto, 0, 0);
$projeto_tipo_folha = mysql_result($qr_projeto, 0, 3);

// Consulta dos Participantes da Folha
$sql_folha = "SELECT A.* , B.desconto_inss as desconto_inss_clt  FROM rh_folha_proc as A
LEFT JOIN rh_clt as B
ON A.id_clt = B.id_clt
WHERE A.id_folha = '$folha' AND A.status = '2' 
ORDER BY A.nome ASC LIMIT 5";
//echo $sql_folha.'<br>';
$qr_participantes    = mysql_query($sql_folha); 
$total_participantes = mysql_num_rows($qr_participantes);

// Definindo Mês da Folha
$meses_pt = array('Erro','Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro');
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

if($ano >= 2011) {
	$percentual_rat = $percentual_fap;
} else {
	$percentual_rat = '0.03';
}


// Encriptografando Links
$link_voltar     = 'folha.php?enc='.str_replace('+', '--', encrypt("$regiao&1")).'&tela=1';
$link_add_remove = 'folha2.php?enc='.str_replace('+', '--', encrypt("$regiao&$folha&2"));
$link_finaliza   = 'acao_folha.php?enc='.str_replace('+', '--', encrypt("$regiao&$folha"));

// Definindo Usuários para Finalizar a Folha
$acesso_finalizacao = array('5','9','33','82','77','87');


//////////////////////////////////////////////////////////////////
/////DIAS TRABALHADOS DE ACORDO COM PER�ODO SELECIONADO NA FOLHA
//////////////////////////////////////////////////////////////////
  $dt_ini     = explode('-',$data_inicio);
  $dt_ini_seg = mktime(0, 0, 0, $dt_ini[1], $dt_ini[2], $dt_ini[0]);  
  $dt_fim     = explode('-',$data_fim);
  $dt_fim_seg = mktime(0, 0, 0, $dt_fim[1], $dt_fim[2], $dt_fim[0]);
  
  $total_dias_folha = round(($dt_fim_seg - $dt_ini_seg)/86400)+1;    //TOTAL DE DIAS ENTRE A DATA DE INICIO E T�RMINO DA FOLHA
  $ultimo_dia_mes   = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);
  
  
  
   if($mes_int != 2) {      
       
      
                if($ultimo_dia_mes == 31) {$total_dias_folha = $total_dias_folha - 1;}
           }else {   
               
               
               if( $total_dias_folha == $ultimo_dia_mes){ 
                   $total_dias_folha = 30;
               }     
           }
           

           
           
 //BUSCANDO INFORMA��O DO MOVIMENTOS
$qr_movimentos = mysql_query("SELECT * FROM rh_movimentos WHERE mov_lancavel = 1");
while($row_movimento = mysql_fetch_assoc($qr_movimentos)){

    $INF_MOVIMENTOS[$row_movimento['id_mov']]['cod'] = $row_movimento['cod'];
    $INF_MOVIMENTOS[$row_movimento['id_mov']]['categoria'] = $row_movimento['categoria'];
    $INF_MOVIMENTOS[$row_movimento['id_mov']]['descicao'] = $row_movimento['descicao'];

}           

 
           
?>