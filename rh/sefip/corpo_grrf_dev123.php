<?php 
include '../../conn.php';
include '../../classes/construcaoTXT.php';

function Data($data){
	return implode('',array_reverse(explode('-',$data)));
}

function CalcHora($hora1,$hora2) {
	$calc1=strtotime($hora2);
	$calc2=strtotime($hora1);
	$total=$calc1-$calc2;
	$H=round(($total/60)/60,4);
	$h=explode('.',$H);
	$M='0'.'.'.$h[1];
	$h=$h[0];
	$m=$M*60;
	$m=explode('.',$m);
	$s='0'.'.'.$m[1];
	$s=round($s*60);
	$m=$m[0];	
	if($h<0) $h=$h*(-1);
	if($h>=0 && $h<=9) $h='0'.$h;
	if($m>=0 && $m<=9) $m='0'.$m;
	if($s>=0 && $s<=9) $s='0'.$s;

	//$resposta=$h.':'.$m.':'.$s;
	$resposta=$h;
	
	return $resposta;
}



$data_recolhimento = $_REQUEST['data'];
$mes = $_REQUEST['mes'];
$ano = $_REQUEST['ano'];
$regiao = $_REQUEST['regiao'];
$projeto = $_REQUEST['projeto'];
$clt = $_REQUEST['clt'];
$qr_recisao = mysql_query("SELECT * FROM rh_recisao WHERE MONTH(data_demi) = '$mes' AND YEAR(data_demi) = '$ano' AND id_regiao = '$regiao' AND id_projeto = '$projeto' AND id_clt = '$clt' AND status = '1'");


$matrizDeControle = array();
while($row_recisao  = mysql_fetch_assoc($qr_recisao)):	
	
	// BUSCANDO DADOS DA EMPRESA PARA CRIAR O CABEÃ‡ALHO
	$query_empresa = mysql_query("SELECT *, REPLACE(REPLACE(cnae,'-',''),'.','') as cnae2 FROM rhempresa WHERE id_regiao = '$regiao' AND id_projeto = '$projeto'");
	$row_empresa = mysql_fetch_array($query_empresa);	
	
	$obj = new txt();
	$obj->dados('00'); // TIPO DE REGISTRO
	$obj->filler(51); // BRANCOS
	$obj->dados('2'); // TIPO DE REMESSA (2 - GRRF)
	$obj->dados('1'); // TIPO DE INSCRIÃ‡ÃƒO (1 - CNPJ)
	$obj->dados($obj->limpar($row_empresa['cnpj'])); // INSCRIÃ‡ÃƒO DO RESPONSÃ?VEL (1 - CNPJ)
	$obj->dados($obj->completa($row_empresa['razao'],30));
	$obj->dados($obj->completa($obj->nome($row_empresa['responsavel']),20)); // NOME RESPONSAVEL 
	$obj->dados($obj->completa('RUA JOAO CAETANO 359',50)); // RUA 
	$obj->dados($obj->completa('CENTRO',20)); // BAIRRO
	$obj->dados($obj->completa($obj->limpar($row_empresa['cep']),8)); // CEP
	$obj->dados($obj->completa($obj->limpar('ITABORAI'),20)); // CIDADE
	$obj->dados($obj->completa('RJ',2)); // UNIDADE DA FEDERAÃ‡ÃƒO
	$obj->dados($obj->completa($obj->limpar($row_empresa['tel']),12,'0','antes'));// TELEFONE
	$obj->dados($obj->completa($row_empresa['email'],60));// ENDEREÃ‡O INTERNET CONTATO
	$obj->dados($obj->completa($obj->limpar($data_recolhimento),8));// ENDEREÃ‡O INTERNET CONTATO
	$obj->filler(60); // BRANCOS
	$obj->fechalinha('*'); // FECHA LINHA
	
	// linha 1 
	
	$obj->dados('10'); // CAMPO OBRIGATORIO (SEMPRE 10)
	$obj->dados('1'); // TIPO DE INSCRIÃ‡ÃƒO (1 - CNPJ)
	$obj->dados($obj->limpar($row_empresa['cnpj'])); // INSCRIÃ‡ÃƒO DO RESPONSÃ?VEL (1 - CNPJ)
	$obj->dados($obj->completa('',36,'0'));// ZEROS
	$obj->dados($obj->completa($obj->nome($row_empresa['razao']),40)); // NOME EMPRESA / RAZÃƒO
	$obj->dados($obj->completa('RUA JOAO CAETANO 359',50)); // RUA , NÂº
	$obj->dados($obj->completa('CENTRO',20)); // BAIRRO
	$obj->dados($obj->completa($obj->limpar($row_empresa['cep']),8)); // CEP
	$obj->dados($obj->completa($obj->limpar('ITABORAI'),20)); // CIDADE
	$obj->dados($obj->completa('RJ',2)); // UNIDADE DA FEDERAÃ‡ÃƒO 
	$obj->dados($obj->completa($obj->limpar($row_empresa['tel']),12,'0','antes'));// TELEFONE
	$obj->dados($obj->completa($row_empresa['cnae2'],7)); // CNAE DA EMPRESA
	$obj->dados('1'); // SIMPLES, NÃƒO OPTANTE
	$obj->dados($obj->completa($row_empresa['fpas'],3)); // SIMPLES, NÃƒO OPTANTE
	$obj->filler(143); // BRANCOS
	$obj->fechalinha('*');// FECHA LINHA
	
	

    $qr_clt = mysql_query("SELECT * FROM rh_clt WHERE id_clt = '$row_recisao[id_clt]'");
    $row_clt = mysql_fetch_array($qr_clt);
	$qr_cruso = mysql_query("SELECT * FROM curso WHERE id_curso = '$row_clt[id_curso]'");
	$qr_horario = mysql_query("SELECT * FROM rh_horarios WHERE id_horario = '$row_clt[rh_horario]'");
	$qr_banco = mysql_query("SELECT id_nacional FROM bancos WHERE id_banco = '$row_clt[banco]'");
	$id_nacional = @mysql_result($qr_banco,0);
	$row_curso = mysql_fetch_assoc($qr_cruso);
	$row_horario = mysql_fetch_assoc($qr_horario);
	
	// filtro de pessoas duplicadas
	if(in_array($row_clt['id_clt'],$matrizDeControle)){
		/*ATENÇÂO AKI TEM QUE COLOCAR UM ERRO PARA QUE NãO CONTINUE CASO HAJA DUPLICADOS*/
		continue;
	}
	$nome_clt = $row_clt['nome'];
	$matrizDeControle[$nome_clt] = $row_clt['id_clt'];

    $obj->dados('40'); // tipo de registro 
    $obj->dados('1'); // TIPO DE INSCRIÃ‡ÃƒO (1 - CNPJ)
    $obj->dados($obj->limpar($row_empresa['cnpj'])); // INSCRIÇÃO DA EMPRESA
    $obj->dados('0'); // tipo de inscrição - tomador obra const. civil (não informado)
    $obj->dados($obj->completa('',14,'0')); // tipo de inscrição - tomador obra const. civil (não informado)
	$obj->dados($obj->completa($obj->limpar($row_clt['pis']),11)); // PIS
	$obj->dados($obj->limpar(Data($row_clt['data_entrada']))); // data admissão
	$obj->dados('01'); // categoria do empregador (01 - empregado)
	$obj->dados($obj->completa($obj->nome($row_clt['nome']),70)); // Nome do trabalhador
	$obj->dados($obj->completa($obj->limpar($row_clt['campo1']),7,'0','antes')); // CTPS
	$obj->dados($obj->completa($obj->limpar($row_clt['serie_ctps']),5,'0','antes')); // SERIE CTPS
	if($row_clt['sexo'] == 'M' or $row_clt['sexo'] == 'm'){
		$obj->dados('1'); // SEXO
	}else{
		$obj->dados('2'); // SEXO
	}
	$obj->dados($obj->completa($row_clt['escolaridade'],2,'0','antes')); // ESCOLARIDADE
	$obj->dados($obj->limpar(Data($row_clt['data_nasci']))); // data nascimento
	
	// calculo das horas trabalhadas por semana
	$horas = CalcHora($row_horario['entrada_1'],$row_horario['saida_2']);
	if(strstr($row_horario['dias_semana'], '-')){
		$partes = explode('-',$row_horario['dias_semana']);
		$dias = count($partes);
	}else{
		$dias = $row_horario['dias_semana'];
	}
	$horasPorSemanas = $horas * $dias;
	echo $row_curso['hora_semana'];
	$obj->dados($obj->completa($row_curso['hora_semana'],2,'0','antes')); // quantidade de horas trabalhadas por semana
	
	
	// verificando erro onde esta o cbo
	$qr_cbo  = mysql_query("SELECT cod FROM rh_cbo WHERE id_cbo = '$row_curso[cbo_codigo]'");
	$row_cbo = mysql_fetch_assoc($qr_cbo);
	$num_cbo = mysql_num_rows($qr_cbo);	
	if(empty($num_cbo)) {
		$cbo = $row_curso['cbo_codigo'];
	} else {
		$cbo = $row_cbo['cod'];
	}
	//
	$obj->dados($obj->completa($obj->limpar(substr($cbo, 0, 4)) ,6,'0','antes')); // CODIGO CBO
	$obj->dados($obj->limpar(Data($row_clt['data_entrada']))); // data de opção
	
	// RELACIONANDO O CODIGO DE MOVIMENTO
	$matrizMovimento = array(
			'I1' => '61',
			'I2' => '81',
			'I3' => '64',
			'H'  => '60',
			'J'  => '63',
			'L'  => '62'
	);
	
	foreach($matrizMovimento as $cod => $valor){
		if($row_recisao['motivo'] == $valor){
			$codigoMovimento = $cod;
		}
		break;
	}
	if(!in_array($row_recisao['motivo'],$matrizMovimento)){
		$codigoMovimento = 'L';
	}
	// /////////
	$obj->dados($obj->completa($codigoMovimento,2)); // Codigo de movimento
	
	$obj->dados($obj->limpar(Data($row_recisao['data_demi']))); // data de movimentação
	switch($codigoMovimento){
		case 'I1': 
			$codSaque = '01';
			break;
		case 'I2':
			$codSaque = '02';
			break;
		case 'I3':
			$codSaque = '03';
			break;
		case 'L': 
			$codSaque = '04';
			break;
	}
	if(in_array($codigoMovimento,array('H','J'))){
		$codSaque = '';
	}
	$obj->dados($obj->completa($codSaque,3,' ')); // código de saque
	
	$obj->dados('1'); // Aviso prévio (1 - trabalhado)
	
	$obj->dados($obj->limpar(Data($row_recisao['data_aviso']))); // data início do aviso previo
	
	$obj->dados('S'); // Reposição de Vaga
	
	$obj->dados($obj->completa('',8)); // data da HOmologação Dissídio Coletivo
	
	$obj->dados($obj->completa('',15,'0')); // Valor Dissídio
	$obj->dados($obj->completa('',15,'0')); // Remuneração mes anterior
	$obj->dados($obj->completa('',15,'0')); // Remuneração mes da rescisão
	$obj->dados($obj->completa('',15,'0')); // Aviso Prévio Indenizado
	
	$obj->dados('N'); // Indicativo Pensão aliminticia
	$obj->dados($obj->completa('',5,'0')); // Percentual da pensão alimenticia
	$obj->dados($obj->completa('',15,'0')); // Valor da Pénsão alimenticia
	
	$obj->dados($obj->limpar($row_clt['cpf'])); // CPF
	
	$obj->dados($obj->completa('',3,'0')); //  banco da conta do trabalhador \(Não informado porque existem N de agencias com mais de 4 digitos cadastradas)
	
	$obj->dados($obj->completa('',4,'0'));  // Agencia
	$obj->dados($obj->completa('',13,'0')); // Conta
	
	$obj->dados($obj->completa('',15,'0')); // Saldo para Fins Rescisórios
	
	$obj->filler(39); // brancos
		
	$obj->fechalinha('*');// FECHA LINHA
	
	// Ultima linha 

	$obj->dados('90');
	$obj->dados($obj->completa('',51,'9'));
	$obj->filler(306);
	$obj->fechalinha('*');
	
	
	// Gera o arquivo
	$diretorio = 'arquivos/grrf/';
	$nome = $row_clt['id_clt'].'_'.$mes.'_'.$ano.'.re';
	$caminho = $diretorio.$nome;
	if(file_exists($caminho)) unlink($caminho);
	$fp = fopen($caminho, "a");
	$escreve = fwrite($fp, $obj->arquivo);
	fclose($fp);

	mysql_query("INSERT INTO grrf (id_clt, mes, ano, id_regiao, id_projeto, user) VALUES ('$clt','$mes','$ano','$regiao','$projeto','$_COOKIE[logado]')");
	echo "<a target='_blank' href='arquivos/grrf/download.php?file=$nome'>Abrir arquivo</a>";

endwhile;




?>