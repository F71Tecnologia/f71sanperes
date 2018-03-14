<?php 
require("../../../conn.php");
require("class.caged.php");

function RemoveCaracteres($variavel) {	
	$variavel = str_replace("  ", " ", $variavel);
	$variavel = str_replace("(", "", $variavel);
	$variavel = str_replace(")", "", $variavel);
	$variavel = str_replace("-", "", $variavel);
	$variavel = str_replace("/", "", $variavel);
	$variavel = str_replace(":", "", $variavel);
	$variavel = str_replace(",", " ", $variavel);
	$variavel = str_replace(".", "", $variavel);
	$variavel = str_replace(";", "", $variavel);
	$variavel = str_replace("\"", "", $variavel);
	$variavel = str_replace("\'", "", $variavel);
	return $variavel;
}

function RemoveEspacos($variavel) {
	$variavel = str_replace(" ", "", $variavel);	
	return $variavel;
}

function RemoveLetras($variavel) {
	$letras = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
    foreach($letras as $letra) {
		$variavel = str_replace($letra, '', $variavel);
	}
	return $variavel;
}

function Valor($variavel) {
	$variavel = str_replace(".", "", $variavel);
	return $variavel;
}

$mes = $_REQUEST['mes'];
$ano = $_REQUEST['ano'];

$user = $_COOKIE['logado'];

$query_empresa 	= mysql_query("SELECT * FROM rhempresa WHERE id_empresa = '1'");
$row_empresa 	= mysql_fetch_assoc($query_empresa);

$qr_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]' ") or die(mysql_error());
$row_user = mysql_fetch_assoc($qr_user);

$qr_master   = mysql_query("SELECT * FROM master WHERE id_master = '$row_user[id_master]'");
$row_master = mysql_fetch_assoc($qr_master);

$qr_regioes = mysql_query("SELECT * FROM regioes WHERE id_master = '$row_master[id_master]'");
while($row_regioes = mysql_fetch_assoc($qr_regioes)):

$regioes[] = $row_regioes['id_regiao'];


endwhile;
$regioes = implode(',', $regioes);



$intercesao = $_REQUEST['intercesao'];
if(!empty($intercesao)){
	$itens = explode(',',$intercesao);
	foreach($itens as $id){
		$sql_clts[] = "id_clt != '$id'";
	}
	$sql_intercesao = "AND (".implode(' AND ',$sql_clts).")";
}



/////Buscando o total os trabalhadores ativos no primeiro dia do mês 

$primeiro_dia  = $ano.'-'.$mes.'-01';
$qr_clt_total_primeiro_dia = mysql_query("SELECT * FROM (rh_clt 
											INNER JOIN projeto 
											ON projeto.id_projeto = rh_clt.id_projeto)
											INNER JOIN regioes ON projeto.id_regiao = regioes.id_regiao
											WHERE (rh_clt.status='200' OR rh_clt .status<60) AND  projeto.id_regiao != 36  AND projeto.status_reg=1 AND regioes.status = 1 AND regioes.status_reg  = 1 AND projeto.id_regiao IN($regioes)") or die(mysql_error());
$total_primeiro_dia = mysql_num_rows($qr_clt_total_primeiro_dia);


/////Buscando o total os trabalhadores ativos no último dia  dia do mês 
/*
$utm_dia    = cal_days_in_month(CAL_GREGORIAN,$mes, $ano);
$ultimo_dia = $ano.'-'.$mes.'-'.$utm_dia;

$qr_clt_total_ultimo_dia = mysql_query("SELECT * FROM rh_clt WHERE data_entrada <= '$ultimo_dia' AND (data_saida >= '$ultimo_dia' OR (data_saida = '0000-00-00' AND status<60 ))") or die(mysql_error());
$total_ultimo_dia = mysql_num_rows($qr_clt_total_ultimo_dia);
*/




// buscando os clt demitidos e admitidos neste mes
$qr_clt_demitidos = mysql_query("SELECT *, 'demitido' as tipo_mov,REPLACE(REPLACE(pis,'-',''),'.','') as pis_limpo  FROM rh_clt WHERE YEAR(data_demi) = '$ano' AND MONTH(data_demi) = '$mes' AND status IN('60','61','62','81','100','80','63') $sql_intercesao AND id_regiao IN($regioes) AND status_reg = 1 ORDER BY nome ASC;");
$qr_clt_admitidos = mysql_query("SELECT *, 'admitido' as  tipo_mov, REPLACE(REPLACE(pis,'-',''),'.','') as pis_limpo FROM rh_clt WHERE YEAR(data_entrada) = '$ano' AND MONTH(data_entrada) = '$mes' AND (status != '60' OR status != '61' OR status != '62' OR status != '81' OR status != '100' OR status != '80' OR status != '63') $sql_intercesao AND id_regiao IN($regioes) AND status_reg = 1 ORDER BY nome ASC;");

$num_admitidos = mysql_num_rows($qr_clt_admitidos);
$num_demitidos = mysql_num_rows($qr_clt_demitidos);
// TOTAL DE MOVIMENTOS
$num_total = $num_admitidos + $num_demitidos;

while($row_adm = mysql_fetch_assoc($qr_clt_admitidos)){
        
	$dados_banco[] = $row_adm;
}

while($row_dem = mysql_fetch_assoc($qr_clt_demitidos)){
	$dados_banco[] = $row_dem;
}


$file = new caged;
$DADOS = array();

function limpaData($data){
	return implode('',array_reverse(explode('-',$data)));
}



/*
Registro tipo A(AUTORIZAÇÃO)
*/
$DADOS['TIPO_REGISTRO'] = 'A'; //  DEFINE O REGISTRO A SER INFORMADO.
$DADOS['LAYOUT'] = 'L2009'; //
$DADOS['MEIOMAG'] = '0'; // MEIO MAGNETICO 
$DADOS['COMPETENCIA'] = $mes.$ano;
$DADOS['ALTERACAO'] = '2'; // 1- nada a alterar; 2 - Alterar dados cadastrais
$DADOS['SEQUENCIA'] = sprintf('%05d',1); // 
$DADOS['IDENTIFICADOR'] = '1'; // 1 - CNPJ; 2 - CEI;
$DADOS['NIDENTIFICADOR'] = RemoveCaracteres($row_master['cnpj']); // NUMERO IDENTIFICADOR DO ESTABELECIMENTO
$DADOS['RAZAO'] = RemoveCaracteres($row_master['razao']); // NOME OU RAZÃO SOCIAL 35 POSIÇÕES
$DADOS['ENDERECO'] =  RemoveCaracteres($row_master['logradouro']); // ENDEREÇO 40 POSIÇÕES
$DADOS['CEP'] = RemoveEspacos(RemoveCaracteres($row_master['cep']));
$DADOS['UF'] = 'RJ';
$DADOS['DDD'] = '00'.RemoveCaracteres(substr($row_master['telefone'],0,4)); // DDD COM 4 POSIÇÕES
$DADOS['TELEFONE'] = RemoveEspacos(RemoveCaracteres(substr($row_master['telefone'],4)));
$DADOS['RAMAL'] = '00000';
$DADOS['ESTABELECIMENTOS'] = '00001';
$DADOS['MOVIMENTOS'] =  sprintf('%05d',$num_total); //  QUANTIDADE DE REGITRO TIPO C


/* 1ª LINHA */
$file->dados($DADOS['TIPO_REGISTRO']);
$file->dados($DADOS['LAYOUT']);
$file->filler(3);
//$file->dados($DADOS['MEIOMAG']);
$file->dados($DADOS['COMPETENCIA']);
$file->dados($DADOS['ALTERACAO']);
$file->dados($DADOS['SEQUENCIA']);
$file->dados($DADOS['IDENTIFICADOR']);
$file->dados($DADOS['NIDENTIFICADOR']);
$file->dados($file->completa($DADOS['RAZAO'],35));
$file->dados($file->completa(substr($DADOS['ENDERECO'],0,40),40));
$file->dados($DADOS['CEP']);
$file->dados($DADOS['UF']);
$file->dados($DADOS['DDD']);
$file->dados($DADOS['TELEFONE']);
$file->dados($DADOS['RAMAL']);
$file->dados($DADOS['ESTABELECIMENTOS']);
$file->dados($DADOS['MOVIMENTOS']);
$file->filler(50);


/* FEcha a 1 linha*/
$file->fechalinha();
/*
Registro tipo B(ESTABELECIMENTO)
*/
$DADOS['TIPO_REGISTRO'] = 'B';
$DADOS['SEQUENCIA'] = sprintf('%05d',2); // 
$DADOS['PRIMEIRA'] = '2'; // 1 - PRIMEIRA DECLARAÇÃO; 2 2 - JÁ INFORMADO
$DADOS['BAIRRO'] = $file->completa('CENTRO',20); //

//$DADOS['TOTAL_EMPREGADOS'] = $file->completa('1523',5,'0','antes');  ////ANTES DA ALTERAÇÃO 
$DADOS['TOTAL_EMPREGADOS'] = $file->completa($total_primeiro_dia,5,'0','antes');

$DADOS['PORTE'] = '2';
$DADOS['CNAE'] = RemoveCaracteres($row_master['cnae']); // CNAE 7 POSIÇÃO
$DADOS['TELEFONE'] = '26351811'; // TELEFONE PARA CONTATO COM O RESPONSAVEL PELAS INFORMAÇÕES DO CAGED
$DADOS['EMAIL'] = $file->completa($row_master['email'],50);
/* 2ª LINHA */

$file->dados($DADOS['TIPO_REGISTRO']);
$file->dados($DADOS['IDENTIFICADOR']);
$file->dados($DADOS['NIDENTIFICADOR']);
$file->dados($DADOS['SEQUENCIA']);
$file->dados($DADOS['PRIMEIRA']);
$file->dados($DADOS['ALTERACAO']);
$file->dados($DADOS['CEP']);
$file->filler(5);
$file->dados($file->completa($DADOS['RAZAO'],40));
$file->dados($file->completa(substr($DADOS['ENDERECO'],0,40),40));
$file->dados($DADOS['BAIRRO']);
$file->dados($DADOS['UF']);
$file->dados($DADOS['TOTAL_EMPREGADOS']);
$file->dados($DADOS['PORTE']);
$file->dados($DADOS['CNAE']);
$file->dados($DADOS['DDD']);
$file->dados($DADOS['TELEFONE']);
$file->dados($DADOS['EMAIL']);
$file->filler(27);

/* fecha a segunda linha */
$file->fechalinha();



/*Grava na base*/
$sql = "SELECT id_caged FROM caged WHERE 
	tipo_caged	 = 	 '$DADOS[TIPO_REGISTRO]' AND 	 	 	 	 	 
	layout_caged =	 '$DADOS[LAYOUT]' AND 
	competencia_caged = '$DADOS[COMPETENCIA]' AND  	 	 	 	 
	alteracao_caged	 = 	 '$DADOS[ALTERACAO]' AND  	 	 
	identificador_caged	= '$DADOS[IDENTIFICADOR]' AND  	 	 	 	 
	nidentificador_caged = 	'$DADOS[NIDENTIFICADOR]' AND 	 	 	 	 	 	 	 
	razao_caged	= 	'$DADOS[RAZAO]' AND  	 	 	 	 	 	 
	endereco_caged	= '$DADOS[ENDERECO]' AND 
	cep_caged =  '$DADOS[CEP]' AND  	 	 	 	 	 
	uf_caged = 	'$DADOS[UF]' AND 		 	 	 	 	 	 	 
	ddd_caged = '$DADOS[DDD]' AND 			 	 	 	 	 	 	 
	tel_caged = '$DADOS[TELEFONE]' AND 		 	 	 	 	 	 	 
	ramal_caged = '$DADOS[RAMAL]' AND 	 	 	 	 	 	 	 
	estabelecimentos_caged	= 	'$DADOS[ESTABELECIMENTOS]' AND 	 	 	 	 	 	 	 
	movimentos_caged = 	'$DADOS[MOVIMENTOS]' AND  	 	 	 	 
	bairro_caged = 	'$DADOS[BAIRRO]' AND 	 	 	 	 	 	 	 
	empregados_caged = 	'$DADOS[TOTAL_EMPREGADOS]' AND 	 	 	 	 	 	 	 
	porte_caged	 = 	 	'$DADOS[PORTE]' AND  	 	 	 	 
	cnae_caged	 =  	'$DADOS[CNAE]' AND  	 	 	 	 	 
	email_caged	 = 	 '$DADOS[EMAIL]' AND 	 	 	 	 	 	 
	datacad_caged = CURDATE() AND	 	 	 	 	 	 	
	usercad_caged = '$user' AND 	 	 	 	 	 	 	
	status_caged = '1' AND  	 	 	 	 	 	
	mes_caged = '$mes' AND 			 	 	 	 	 	 	 
	ano_caged = '$ano'";
$qr_consulta = mysql_query($sql);
$num = mysql_num_rows($qr_consulta);
$id_caged = @mysql_result($qr_consulta,0);
if(empty($num)){
	$sql = "INSERT INTO caged SET
	tipo_caged	 = 	 '$DADOS[TIPO_REGISTRO]',	 	 	 	 	 
	layout_caged =	 '$DADOS[LAYOUT]',
	competencia_caged = '$DADOS[COMPETENCIA]', 	 	 	 	 
	alteracao_caged	 = 	 '$DADOS[ALTERACAO]', 	 	 
	identificador_caged	= '$DADOS[IDENTIFICADOR]', 	 	 	 	 
	nidentificador_caged = 	'$DADOS[NIDENTIFICADOR]',	 	 	 	 	 	 	 
	razao_caged	= 	'$DADOS[RAZAO]', 	 	 	 	 	 	 
	endereco_caged	= '$DADOS[ENDERECO]',
	cep_caged =  '$DADOS[CEP]', 	 	 	 	 	 
	uf_caged = 	'$DADOS[UF]',		 	 	 	 	 	 	 
	ddd_caged = '$DADOS[DDD]',			 	 	 	 	 	 	 
	tel_caged = '$DADOS[TELEFONE]',		 	 	 	 	 	 	 
	ramal_caged = '$DADOS[RAMAL]',	 	 	 	 	 	 	 
	estabelecimentos_caged	= 	'$DADOS[ESTABELECIMENTOS]',	 	 	 	 	 	 	 
	movimentos_caged = 	'$DADOS[MOVIMENTOS]', 	 	 	 	 
	bairro_caged = 	'$DADOS[BAIRRO]',	 	 	 	 	 	 	 
	empregados_caged = 	'$DADOS[TOTAL_EMPREGADOS]',	 	 	 	 	 	 	 
	porte_caged	 = 	 	'$DADOS[PORTE]', 	 	 	 	 
	cnae_caged	 =  	'$DADOS[CNAE]', 	 	 	 	 	 
	email_caged	 = 	 '$DADOS[EMAIL]',	 	 	 	 	 	 
	datacad_caged = CURDATE(),		 	 	 	 	 	 	
	usercad_caged = '$user',	 	 	 	 	 	 	
	status_caged = '1', 	 	 	 	 	 	
	mes_caged = '$mes',			 	 	 	 	 	 	 
	ano_caged = '$ano'";
	mysql_query($sql);
	$id_caged = mysql_insert_id();
}
/*FIM DO CADASTRAMENTO DOS DADOS NA TABELA CAGED*/


$cont = 3;

foreach($dados_banco as $row_admitidos):
/* linha 3(movimentação caged) */
/*$data_entrada = explode('-',$row_admitidos['data_entrada']);
if(($row_admitidos['data_demi'] == '0000-00-00' or empty($row_admitidos['data_demi'])) and $data_entrada[0] == $ano and $data_entrada[1] == $mes){
	$DADOS['TIPO_REGISTRO'] = 'C';
}else{
	$DADOS['TIPO_REGISTRO'] = 'B';
}*/
  
    
$DADOS['TIPO_REGISTRO'] = 'C';
$DADOS['SEQUENCIA'] = sprintf('%05d',$cont); // 
$DADOS['PIS'] = sprintf('%011d',trim($row_admitidos['pis_limpo'])); // 
if($row_admitidos['sexo'] == 'M'){
	$DADOS['SEXO'] = "1";
}else{
	$DADOS['SEXO'] = "2";
}
$DADOS['DATA_NASCIMENTO'] 	= limpaData($row_admitidos['data_nasci']); 
$DADOS['GRAU_INSTRUCAO'] 	= sprintf('%02d',$row_admitidos['escolaridade']);

// FAZ UMA BUSCA EM CURSOS PARA PEGAR O SALARIO
$qr_curso = mysql_query("SELECT salario,cbo_codigo FROM curso WHERE id_curso = '$row_admitidos[id_curso]'");
$row_curso = mysql_fetch_assoc($qr_curso);
$DADOS['SALARIO'] 			= $file->completa(str_replace('.','',$row_curso['salario']),8,'0','antes');

// FAZ UMA BUSCA EM HORARIOS PARA SAVER Q QUANTIDADE DE HORAS POR SEMANA;
$qr_horario = mysql_query("SELECT horas_mes FROM rh_horarios WHERE id_horario = '$row_admitidos[rh_horario]'");
$total_mes = mysql_fetch_assoc($qr_horario);
$total_mes = $total_mes['horas_mes'];
$horas_semanal = ceil($total_mes/4);
if($horas_semanal > 44 or $horas_semanal == 0){
	$horas_semanal = 44;
}


$DADOS['HORARIO']			= sprintf('%02d',$horas_semanal);
$DADOS['ADMISSAO']			= limpaData($row_admitidos['data_entrada']);
/*
 ADMISSÃO 
10 - Primeiro emprego 
20 - Reemprego 
25 - Contrato por prazo determinado 
     35 - Reintegração 
     70 - Transferência de entrada

     DESLIGAMENTO
     31 - Dispensa sem justa causa 
     32 - Dispensa por justa causa 
     40 - A pedido (espontâneo) 
     43 - Término de contrato por prazo determinado 
     45 - Término de contrato 
     50 - Aposentado 
     60 - Morte 
     80 - Transferência de saída
*/      
  $dia = explode("-",$row_admitidos['data_demi']);
  $dia_saida = ($mes == $dia[1] and $ano == $dia[0] and $row_admitidos['tipo_mov'] == 'demitido')? $dia[2]: '';

$status_demi = array(60,61,62,81,100,80,63);
if($row_admitidos['tipo_mov'] == 'demitido' and ($mes == $dia[1] and $ano == $dia[0])  ){
    
	$status_caged = array(31=>10,32=>9,40=>22,50=>20,60=>13);
	foreach($status_caged as $cod_caged => $cod_intranet){
		if($row_admitidos['status'] == $cod_intranet) {
				$cod_movimentacao = $cod_caged;
				continue;
		}
	}
        
	if(empty($cod_movimentacao)) $cod_movimentacao = 31;
	$DADOS['MOVIMENTO']			= $file->completa($cod_movimentacao,2);
      
        
}else{
	$DADOS['MOVIMENTO']			= $file->completa($row_admitidos['status_admi'],2);
}



$DADOS['DESLIGAMENTO']	    = $file->completa($dia_saida,2);
$DADOS['NOME']	    		= sprintf( '%-40s',substr($row_admitidos['nome'],0,40));


$DADOS['CT']				= $file->completa(RemoveCaracteres($row_admitidos['campo1']),8);
$DADOS['SERIE_CT']			= $file->completa(RemoveCaracteres($row_admitidos['serie_ctps']),4);
// buscando a raça do clt
$qr_etnia 	= mysql_query("SELECT cod FROM etnias WHERE id = '$row_admitidos[etnia]'");
$cod 		= mysql_fetch_assoc($qr_etnia);
$cod_etinia = $cod['cod'] * 1;
$DADOS['RACA'] 		= $file->completa($cod_etinia,1);


// verificando deficiencia
if(empty($row_admitidos['deficiencia'])){
	$DADOS['DEFICIENCIA'] = '2';
	$DADOS['TIPO_DEFICIENCIA'] = '2';
}else{
	$DADOS['DEFICIENCIA'] = '1';
	$qr_deficiencia = mysql_query("SELECT cod FROM deficiencias WHERE id = '$row_admitidos[deficiencia]'");
	$row_deficiencia = mysql_fetch_assoc($qr_deficiencia);
	$DADOS['TIPO_DEFICIENCIA'] = $file->completa($row_deficiencia['cod'],1);
}

$qr_curso = mysql_query("SELECT * FROM curso WHERE id_curso = '$row_admitidos[id_curso]'");
$curso    = mysql_fetch_array($qr_curso);

$qr_cbo  = mysql_query("SELECT cod FROM rh_cbo WHERE id_cbo = '$curso[cbo_codigo]'");
$row_cbo = mysql_fetch_assoc($qr_cbo);
$num_cbo = mysql_num_rows($qr_cbo);

if(empty($num_cbo)) {
	$cbo = $curso['cbo_codigo'];
} else {
	$cbo = $row_cbo['cod'];
}

$DADOS['CBO'] = $file->completa(str_replace('-','',str_replace('.','',$cbo)),6,0,'antes');
$DADOS['APRENDIZ'] = '2';
$DADOS['UF_CT'] = $file->completa($row_admitidos['uf_ctps'],2);
$DADOS['CPF'] = str_replace('-','',str_replace('.','',$row_admitidos['cpf']));
$DADOS['CEP'] = sprintf('%08d',str_replace('-','','')); ///$row_admitidos['cep']


// MONTANDO AS LINHAS COM OS ADMITIDOS
$file->dados($DADOS['TIPO_REGISTRO']);
$file->dados($DADOS['IDENTIFICADOR']);
$file->dados($DADOS['NIDENTIFICADOR']);
$file->dados($DADOS['SEQUENCIA']);
$file->dados($DADOS['PIS']);
$file->dados($DADOS['SEXO']);
$file->dados($DADOS['DATA_NASCIMENTO']);
$file->dados($DADOS['GRAU_INSTRUCAO']);
$file->filler(4);
$file->dados($DADOS['SALARIO']);
$file->dados($DADOS['HORARIO']);
$file->dados($DADOS['ADMISSAO']);
$file->dados($DADOS['MOVIMENTO']);
$file->dados($DADOS['DESLIGAMENTO']);
$file->dados($DADOS['NOME']);
$file->dados($DADOS['CT']);
$file->dados($DADOS['SERIE_CT']);
$file->filler(7);
$file->dados($DADOS['RACA']);
$file->dados($DADOS['DEFICIENCIA']);
$file->dados($DADOS['CBO']);
$file->dados($DADOS['APRENDIZ']);
$file->dados($DADOS['UF_CT']);
$file->dados($DADOS['TIPO_DEFICIENCIA']);
$file->dados($DADOS['CPF']);
$file->dados($DADOS['CEP']);
$file->filler(81);

$file->fechalinha();




$cont++;
endforeach;

// Gera o arquivo
$diretorio = '../Arquivos/';
$nome = "CGD".limpaData(date('Y-m-d')).".m";
$caminho = $diretorio.$nome;
if(file_exists($caminho))
	unlink($caminho);
$fp = fopen($caminho, "a");
$escreve = fwrite($fp, $file->arquivo);
fclose($fp);

echo '<a href='.$diretorio.'download.php?file='.$nome.'>Abrir arquivo</a>';
?>