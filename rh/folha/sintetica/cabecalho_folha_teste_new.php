<?php
// Incluindo Arquivos
require('../../conn.php');
include('../../classes/calculos.php');
include('../../classes/calculos_new.php');
include('../../classes/abreviacao.php');
include('../../classes/formato_valor.php');
include('../../classes/formato_data.php');
include('../../classes/valor_proporcional.php');
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

// Id da Folha
$enc   = explode('&', decrypt(str_replace('--', '+', $_REQUEST['enc'])));
$folha = $enc[1];

// Consulta da Folha
$qr_folha    = mysql_query("SELECT A.* , DATE_FORMAT(A.data_inicio, '%d/%m/%Y') AS data_inicio_br, 
                            DATE_FORMAT(A.data_fim, '%d/%m/%Y') AS data_fim_br,
                            DATE_FORMAT(A.data_proc, '%d/%m/%Y') AS data_proc_br,
                                 B.nome as nome_projeto, B.tipo_folha, C.regiao as nome_regiao,C.id_master                       
                              FROM rh_folha as A
                              INNER JOIN projeto as B
                              ON A.projeto = B.id_projeto
                              INNER JOIN regioes as C
                              ON A.regiao = C.id_regiao
                               WHERE A.id_folha = '$folha' AND A.status = '2';");

$row_folha               = mysql_fetch_array($qr_folha);
$data_inicio             = $row_folha['data_inicio'];
$data_fim                = $row_folha['data_fim'];
$ano                     = $row_folha['ano'];
$mes                     = $row_folha['mes'];
$mes_int                 = (int)$mes;
$regiao                  = $row_folha['regiao'];
$projeto                 = $row_folha['projeto'];
$projeto_tipo_folha      = $row_projeto['tipo_folha'];


// Definindo Classes
$Calc       = new calculos();
$Trab       = new proporcional();
$CALC_NEW = new Calculos_new($ano);



// Consulta do Usuário que gerou a Folha
$qr_usuario = mysql_query("SELECT nome FROM funcionario WHERE id_funcionario = '$row_folha[user]'");




// Consulta dos Participantes da Folha
$qr_participantes    = mysql_query("SELECT A.id_folha_proc,  C.salario, C.nome, C.tipo_insalubridade, C.qnt_salminimo_insalu,
                                      B.id_clt,B.nome,  B.data_entrada,B.insalubridade, B.status,B.desconto_inss,B.tipo_desconto_inss, B.desconto_outra_empresa,B.tipo_contratacao,
                                      B.transporte,B.rh_sindicato,B.ano_contribuicao,B.id_curso, C.nome as nome_funcao
                                    FROM rh_folha_proc as A
                                    INNER  JOIN rh_clt as B
                                    ON A.id_clt = B.id_clt
                                    LEFT JOIN curso as C ON(C.id_curso = B.id_curso)                                    
                                    WHERE A.id_folha = '$folha' AND A.status = '2'
                                    ORDER BY A.nome ASC") or die(mysql_error()); 
$total_participantes = mysql_num_rows($qr_participantes);




// Redefinindo Variáveis de Décimo Terceiro
if($row_folha['terceiro'] != 1) {
	$decimo_terceiro = NULL;
} else {
	$decimo_terceiro = 1;
	$tipo_terceiro   = $row_folha['tipo_terceiro'];
}

// Definindo Mês da Folha
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
	$mes_folha = mesesArray($mes_int)." / $ano";
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

           
?>