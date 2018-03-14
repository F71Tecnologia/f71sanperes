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

$mes     = $_REQUEST['mes'];
$ano     = $_REQUEST['ano'];
$regiao  = $_REQUEST['regiao'];
$projeto = $_REQUEST['projeto'];
$ids_clt = implode(',',$_REQUEST['ids_clt']);
$data_referencia = $ano.'-'.$mes.'-01';
$TOTAL_ESTABELECIMENTO = $_REQUEST['total_estabelecimento'];





$qr_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]' ") or die(mysql_error());
$row_user = mysql_fetch_assoc($qr_user);

$qr_master   = mysql_query("SELECT * FROM master WHERE id_master = '$row_user[id_master]'");
$row_master = mysql_fetch_assoc($qr_master);




/////Buscando o total os trabalhadores ativos no primeiro dia do mês
$primeiro_dia  = $ano.'-'.$mes.'-01';
$qr_clt_total_primeiro_dia = mysql_query("SELECT * FROM (rh_clt 
                                            INNER JOIN projeto 
                                            ON projeto.id_projeto = rh_clt.id_projeto)
                                            INNER JOIN regioes ON projeto.id_regiao = regioes.id_regiao
                                            WHERE (rh_clt.status='200' OR rh_clt .status<60) AND  projeto.id_regiao != 36  
                                            AND projeto.status_reg=1 AND regioes.status = 1 AND regioes.status_reg  = 1 
                                            AND projeto.id_regiao = '$regiao' AND projeto.id_projeto = '$projeto'") or die(mysql_error());
$total_primeiro_dia = mysql_num_rows($qr_clt_total_primeiro_dia);


/////Buscando o total os trabalhadores ativos no último dia  dia do mês 
/*
$utm_dia    = cal_days_in_month(CAL_GREGORIAN,$mes, $ano);
$ultimo_dia = $ano.'-'.$mes.'-'.$utm_dia;

$qr_clt_total_ultimo_dia = mysql_query("SELECT * FROM rh_clt WHERE data_entrada <= '$ultimo_dia' AND (data_saida >= '$ultimo_dia' OR (data_saida = '0000-00-00' AND status<60 ))") or die(mysql_error());
$total_ultimo_dia = mysql_num_rows($qr_clt_total_ultimo_dia);
*/
 
 $qr_trabalhadores = mysql_query(" /**CONSULTA DOS ADMITIDOS**/
                                    SELECT qr_admitidos.*, C.nome as nome_funcao, C.salario, C.cbo_codigo, D.id_regiao as id_regiao_transferencia, D.regiao as nome_regiao, E.id_projeto as  id_projeto_transferencia,E.nome as nome_projeto, F.horas_mes,F.horas_semanais,G.cnpj as cnpj_transferencia
                                    FROM
                                                  (SELECT A.id_curso,A.id_clt, A.id_regiao, A.id_projeto, A.pis, 
                                                         REPLACE( REPLACE(A.pis,'.',''),'-','') as pis_limpo,
                                                         REPLACE( REPLACE(A.cpf,'.',''),'-','') as cpf_limpo,
                                                         E.cnpj,
                                                         D.cbo_codigo,
                                                         DATE_FORMAT(data_entrada,'%d/%m/%Y') as data,
                                                         IF( MONTH(A.data_entrada) = '$mes' AND YEAR(A.data_entrada) = '$ano','ADMITIDO','') as movimento,										
                                                         A.nome as nome_clt, 
                                                        IF(A.sexo = 'M',1,IF(A.sexo = 'F',1,' ')) as clt_sexo,
                                                        A.data_nasci, A.escolaridade, A.data_entrada,A.status_demi,A.data_demi, A.campo1,A.serie_ctps,A.uf_ctps,A.etnia,A.status,A.status_admi,
                                                        REPLACE(A.cep,'-','') as cep_limpo, A.rh_horario,  A.deficiencia,
                                                          
                                                         /*TRANSFERENCIAS*/
                                                         (SELECT id_curso_de 	  FROM rh_transferencias WHERE id_clt = A.id_clt AND id_curso_de <> id_curso_para AND data_proc >= '$data_referencia' ORDER BY id_transferencia ASC LIMIT 1) AS funcao_de,
                                                         (SELECT id_curso_para  FROM rh_transferencias WHERE id_clt = A.id_clt AND id_curso_de <> id_curso_para AND data_proc <= '$data_referencia' ORDER BY id_transferencia DESC LIMIT 1) AS funcao_para,    
                                                         (SELECT id_regiao_de   FROM rh_transferencias WHERE id_clt = A.id_clt AND id_regiao_de <> id_regiao_para AND data_proc >= '$data_referencia' ORDER BY id_transferencia ASC LIMIT 1) AS regiao_de,
                                                         (SELECT id_regiao_para FROM rh_transferencias WHERE id_clt = A.id_clt AND id_regiao_de <> id_regiao_para AND data_proc <= '$data_referencia' ORDER BY id_transferencia DESC LIMIT 1) AS regiao_para,
                                                         (SELECT id_projeto_de   FROM rh_transferencias WHERE id_clt = A.id_clt AND id_projeto_de <> id_projeto_para AND data_proc >= '$data_referencia' ORDER BY id_transferencia ASC LIMIT 1) AS projeto_de,
                                                         (SELECT id_projeto_para FROM rh_transferencias WHERE id_clt = A.id_clt AND id_projeto_de <> id_projeto_para AND data_proc <= '$data_referencia' ORDER BY id_transferencia DESC LIMIT 1) AS projeto_para,
                                                    (SELECT id_horario_de   FROM rh_transferencias WHERE id_clt = A.id_clt AND id_horario_de <> id_horario_para AND data_proc >= '$data_referencia' ORDER BY id_transferencia ASC LIMIT 1) AS horario_de,
                                                         (SELECT id_horario_para FROM rh_transferencias WHERE id_clt = A.id_clt AND id_horario_de <> id_horario_para AND data_proc <= '$data_referencia' ORDER BY id_transferencia DESC LIMIT 1) AS horario_para
                                                        FROM rh_clt as A	                             
                                                                 INNER JOIN curso as D 
                                                                ON D.id_curso = A.id_curso       
                                                                INNER JOIN rhempresa as E 
                                                                ON E.id_projeto = A.id_projeto
                                                                WHERE YEAR(A.data_entrada) = '$ano'
							AND MONTH(A.data_entrada) = '$mes' AND A.id_clt IN($ids_clt)) as qr_admitidos
										  	
                                LEFT JOIN curso AS C ON (IF(qr_admitidos.funcao_para IS NOT NULL,C.id_curso   = qr_admitidos.funcao_para, IF(qr_admitidos.funcao_de IS NOT NULL,C.id_curso = qr_admitidos.funcao_de,C.id_curso = qr_admitidos.id_curso)))
                                LEFT JOIN regioes AS D ON (IF(qr_admitidos.regiao_para IS NOT NULL,D.id_regiao = qr_admitidos.regiao_para, IF(qr_admitidos.regiao_de IS NOT NULL,D.id_regiao = qr_admitidos.regiao_de,D.id_regiao = qr_admitidos.id_regiao)))    
                                LEFT JOIN projeto AS E ON (IF(qr_admitidos.projeto_para IS NOT NULL,E.id_projeto = qr_admitidos.projeto_para, IF(qr_admitidos.projeto_de IS NOT NULL,E.id_projeto = qr_admitidos.projeto_de,E.id_projeto = qr_admitidos.id_projeto))) 
				LEFT JOIN rh_horarios AS F ON (IF(qr_admitidos.horario_para IS NOT NULL,F.id_horario = qr_admitidos.horario_para, IF(qr_admitidos.horario_de IS NOT NULL,F.id_horario = qr_admitidos.horario_de,F.id_horario = qr_admitidos.rh_horario)))             
                                LEFT JOIN rhempresa as G  ON(G.id_projeto = E.id_projeto)
                                UNION
  
                                /**CONSULTA DOS DEMITIDOS**/
                                SELECT qr_demitidos.*, C.nome as nome_funcao, C.salario,C.cbo_codigo, D.id_regiao as id_regiao_transferencia, D.regiao as nome_regiao, E.id_projeto as  id_projeto_transferencia,E.nome as nome_projeto, F.horas_mes,F.horas_semanais,G.cnpj as cnpj_transferencia
                                FROM
                                                  (SELECT A.id_curso,A.id_clt, A.id_regiao, A.id_projeto, A.pis, 
                                                                  REPLACE( REPLACE(A.pis,'.',''),'-','') as pis_limpo,
                                                                  REPLACE( REPLACE(A.cpf,'.',''),'-','') as cpf_limpo,
                                                                  E.cnpj,	 
                                                                  D.cbo_codigo,
                                                                  DATE_FORMAT(data_demi,'%d/%m/%Y') as data, 
                                                                  IF( MONTH(A.data_demi) = '$mes' AND YEAR(A.data_demi) ='$ano','DEMITIDO','') as movimento,										
                                                                  A.nome as nome_clt, 
                                                                IF(A.sexo = 'M',1,IF(A.sexo = 'F',1,' ')) as clt_sexo,
                                                                A.data_nasci, A.escolaridade, A.data_entrada,A.status_demi,A.data_demi, A.campo1,A.serie_ctps,A.uf_ctps,A.etnia,A.status,A.status_admi,
                                                                REPLACE(A.cep,'-','') as cep_limpo,A.rh_horario,  A.deficiencia, 
                                                                  
                                                                  /*TRANSFERENCIAS*/
                                                                  (SELECT id_curso_de 	  FROM rh_transferencias WHERE id_clt = A.id_clt AND id_curso_de <> id_curso_para AND data_proc >= '$data_referencia' ORDER BY id_transferencia ASC LIMIT 1) AS funcao_de,
                                                                  (SELECT id_curso_para  FROM rh_transferencias WHERE id_clt = A.id_clt AND id_curso_de <> id_curso_para AND data_proc <= '$data_referencia' ORDER BY id_transferencia DESC LIMIT 1) AS funcao_para,    
                                                                  (SELECT id_regiao_de   FROM rh_transferencias WHERE id_clt = A.id_clt AND id_regiao_de <> id_regiao_para AND data_proc >= '$data_referencia' ORDER BY id_transferencia ASC LIMIT 1) AS regiao_de,
                                                                  (SELECT id_regiao_para FROM rh_transferencias WHERE id_clt = A.id_clt AND id_regiao_de <> id_regiao_para AND data_proc <= '$data_referencia' ORDER BY id_transferencia DESC LIMIT 1) AS regiao_para,
                                                                  (SELECT id_projeto_de   FROM rh_transferencias WHERE id_clt = A.id_clt AND id_projeto_de <> id_projeto_para AND data_proc >= '$data_referencia' ORDER BY id_transferencia ASC LIMIT 1) AS projeto_de,
                                                                  (SELECT id_projeto_para FROM rh_transferencias WHERE id_clt = A.id_clt AND id_projeto_de <> id_projeto_para AND data_proc <= '$data_referencia' ORDER BY id_transferencia DESC LIMIT 1) AS projeto_para,
                                                                  (SELECT id_horario_de   FROM rh_transferencias WHERE id_clt = A.id_clt AND id_horario_de <> id_horario_para AND data_proc >= '$data_referencia' ORDER BY id_transferencia ASC LIMIT 1) AS horario_de,
                                                         	  (SELECT id_horario_para FROM rh_transferencias WHERE id_clt = A.id_clt AND id_horario_de <> id_horario_para AND data_proc <= '$data_referencia' ORDER BY id_transferencia DESC LIMIT 1) AS horario_para
                                                     
                                                FROM rh_clt as A                                
                                                INNER JOIN curso as D 
                                                ON D.id_curso = A.id_curso
                                                INNER JOIN rhempresa as E 
                                                ON E.id_projeto = A.id_projeto
                                                WHERE YEAR(A.data_demi) = '$ano' AND MONTH(A.data_demi) = '$mes' AND A.status IN(60,61,62,81,63,101,64,65,66) AND A.id_clt IN($ids_clt)) as qr_demitidos

                                LEFT JOIN curso AS C ON (IF(qr_demitidos.funcao_para IS NOT NULL,C.id_curso      = qr_demitidos.funcao_para,  IF(qr_demitidos.funcao_de IS NOT NULL,C.id_curso    = qr_demitidos.funcao_de,C.id_curso = qr_demitidos.id_curso)))
                                LEFT JOIN regioes AS D ON (IF(qr_demitidos.regiao_para IS NOT NULL,D.id_regiao   = qr_demitidos.regiao_para,  IF(qr_demitidos.regiao_de IS NOT NULL,D.id_regiao   = qr_demitidos.regiao_de,D.id_regiao = qr_demitidos.id_regiao)))    
                                LEFT JOIN projeto AS E ON (IF(qr_demitidos.projeto_para IS NOT NULL,E.id_projeto = qr_demitidos.projeto_para, IF(qr_demitidos.projeto_de IS NOT NULL,E.id_projeto = qr_demitidos.projeto_de,E.id_projeto = qr_demitidos.id_projeto)))  
				LEFT JOIN rh_horarios AS F ON (IF(qr_demitidos.horario_para IS NOT NULL,F.id_horario = qr_demitidos.horario_para, IF(qr_demitidos.horario_de IS NOT NULL,F.id_horario = qr_demitidos.horario_de,F.id_horario = qr_demitidos.rh_horario)))  
                                LEFT JOIN rhempresa as G  ON(G.id_projeto = E.id_projeto)
                                ORDER BY  cnpj_transferencia ,id_projeto_transferencia,movimento,nome_clt ") or die(mysql_error());



$file = new caged;
$DADOS = array();

function limpaData($data){
	return implode('',array_reverse(explode('-',$data)));
}



$TOTAL_MOVIMENTOS_INFORMADOS = mysql_num_rows($qr_trabalhadores);
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
$DADOS['RAZAO'] = $row_master['razao']; // NOME OU RAZÃO SOCIAL 35 POSIÇÕES
$DADOS['ENDERECO'] =  RemoveCaracteres($row_master['logradouro']); // ENDEREÇO 40 POSIÇÕES
$DADOS['CEP'] = RemoveEspacos(RemoveCaracteres($row_master['cep']));
$DADOS['UF'] = 'RJ';
$DADOS['DDD'] = '00'.RemoveCaracteres(substr($row_master['telefone'],0,4)); // DDD COM 4 POSIÇÕES
$DADOS['TELEFONE'] = RemoveEspacos(RemoveCaracteres(substr($row_master['telefone'],4)));
$DADOS['RAMAL'] = '00000';
$DADOS['ESTABELECIMENTOS'] = sprintf('%05d',$TOTAL_ESTABELECIMENTO);
$DADOS['MOVIMENTOS'] =  sprintf('%05d',$TOTAL_MOVIMENTOS_INFORMADOS); //  QUANTIDADE DE REGITRO TIPO C


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
$file->dados(substr($DADOS['RAZAO'],0,35));
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





/*Grava na base*/
/*
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
	ano_caged = '$ano',
        id_regiao  = '$regiao',
        id_projeto = '$projeto'";
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
	ano_caged = '$ano',
        id_regiao  = '$regiao',
        id_projeto = '$projeto'";
	mysql_query($sql);
	$id_caged = mysql_insert_id();
}
*/
/*FIM DO CADASTRAMENTO DOS DADOS NA TABELA CAGED*/


$cont = 2;

while($row_clt = mysql_fetch_assoc($qr_trabalhadores)){


    if($row_clt['id_projeto_transferencia'] != $projetoAnt){
      


                ////////////////////////////////////////////////
                /////  Registro tipo B(ESTABELECIMENTO)  ///////
                ///////////////////////////////////////////////        
                
                $query_empresa 	= mysql_query("select cnpj, razao,  endereco,cep, SUBSTR(tel,2,2) as ddd, substr(tel,5,8) as telefone, bairro,uf,email
                FROM rhempresa 
                WHERE id_regiao = '$row_clt[id_regiao_transferencia]' AND id_projeto = '$row_clt[id_projeto_transferencia]' ;");
                $row_empresa 	= mysql_fetch_assoc($query_empresa);
                
                //verifica��o para projetos com o mesmo CNPJ
                if($row_empresa['cnpj'] != $cnpjAnt){
                 
                  if($row_clt['id_regiao_transferencia'] == 1 or $row_clt[id_regiao_transferencia] == 2 or $row_clt[id_regiao_transferencia]== 3){
                      $ids_projeto_clt = '1,2,3';
                  }  else {
                       $ids_projeto_clt = $row_clt['id_regiao_transferencia'];
                  }
                  
                  ///conta o total de participantes no primeiro dia
                  $qr_total_primeiro_dia = mysql_query("SELECT clt.*, C.id_regiao, C.regiao, D.id_projeto FROM 
                                                        (SELECT 
                                                                        A.id_clt,A.nome, A.data_entrada, A.data_demi, A.status,id_regiao, id_projeto,
                                                                        (SELECT id_regiao_de FROM rh_transferencias WHERE id_clt=A.id_clt AND id_regiao_de <> id_regiao_para AND data_proc >= '$ano-$mes-01' ORDER BY id_transferencia ASC LIMIT 1) AS regiao_de,
                                                                        (SELECT id_regiao_para FROM rh_transferencias WHERE id_clt=A.id_clt AND id_regiao_de <> id_regiao_para AND data_proc <= '$ano-$mes-01' ORDER BY id_transferencia DESC LIMIT 1) AS regiao_para,
                                                                        (SELECT id_projeto_de FROM rh_transferencias WHERE id_clt=A.id_clt AND id_projeto_de <> id_projeto_para AND data_proc >= '$ano-$mes-01' ORDER BY id_transferencia ASC LIMIT 1) AS projeto_de,
                                                                        (SELECT id_projeto_para FROM rh_transferencias WHERE id_clt=A.id_clt AND id_projeto_de <> id_projeto_para AND data_proc <= '$ano-$mes-01' ORDER BY id_transferencia DESC LIMIT 1) AS projeto_para,
                                                                        (SELECT data_proc FROM rh_transferencias WHERE id_clt=A.id_clt AND id_projeto_de <> id_projeto_para AND data_proc <= '$ano-$mes-01' ORDER BY id_transferencia DESC LIMIT 1) AS data_proc

                                                        FROM rh_clt as A
                                                        WHERE  A.data_entrada <='$ano-$mes-01'
                                                        AND (A.data_demi > '$ano-$mes-01' OR A.data_demi = '0000-00-00' OR  A.data_demi IS NULL 
                                                        OR  ( (A.status <60 OR A.status = 200)  AND A.data_demi IS NOT NULL))) as clt      
                                                        LEFT JOIN regioes AS C ON (IF(clt.regiao_para IS NOT NULL,C.id_regiao=clt.regiao_para, IF(clt.regiao_de IS NOT NULL,C.id_regiao=clt.regiao_de,C.id_regiao=clt.id_regiao)))
                                                        LEFT JOIN projeto AS D ON (IF(clt.projeto_para IS NOT NULL,D.id_projeto = clt.projeto_para, IF(clt.projeto_de IS NOT NULL,D.id_projeto = clt.projeto_de,D.id_projeto = clt.id_projeto)))

                                                         WHERE  D.id_projeto IN($ids_projeto_clt)");
                  
                  $total_primeiro_dia = mysql_num_rows($qr_total_primeiro_dia);
                  
                  
                  
                  
                    
                 $qr_total_clt = mysql_query(" SELECT COUNT(qr_admitidos.id_clt) as  qnt, D.id_regiao as id_regiao_transferencia, D.regiao as nome_regiao, E.id_projeto as  id_projeto_transferencia,E.nome as nome_projeto
                                                FROM
                                                              (SELECT A.id_curso,A.id_clt, A.id_regiao, A.id_projeto,     
                                                                     A.nome as nome_clt,A.data_entrada,A.status_demi,A.data_demi,
                                                                     /*TRANSFERENCIAS*/
                                                                     (SELECT id_regiao_de   FROM rh_transferencias WHERE id_clt = A.id_clt AND id_regiao_de <> id_regiao_para AND data_proc >= '2013-10-01' ORDER BY id_transferencia ASC LIMIT 1) AS regiao_de,
                                                                     (SELECT id_regiao_para FROM rh_transferencias WHERE id_clt = A.id_clt AND id_regiao_de <> id_regiao_para AND data_proc <= '2013-10-01' ORDER BY id_transferencia DESC LIMIT 1) AS regiao_para,
                                                                     (SELECT id_projeto_de   FROM rh_transferencias WHERE id_clt = A.id_clt AND id_projeto_de <> id_projeto_para AND data_proc >= '2013-10-01' ORDER BY id_transferencia ASC LIMIT 1) AS projeto_de,
                                                                     (SELECT id_projeto_para FROM rh_transferencias WHERE id_clt = A.id_clt AND id_projeto_de <> id_projeto_para AND data_proc <= '2013-10-01' ORDER BY id_transferencia DESC LIMIT 1) AS projeto_para

                                                                     FROM rh_clt as A	                             
                                                                             INNER JOIN curso as D 
                                                                            ON D.id_curso = A.id_curso       
                                                                            INNER JOIN rhempresa as E 
                                                                            ON E.id_projeto = A.id_projeto
                                                                            WHERE  A.data_entrada <= '$ano-$mes-01' AND (A.data_demi > '$ano-$mes-01' OR A.data_demi = '0000-00-00' OR A.data_demi IS NULL)) as qr_admitidos

                                            LEFT JOIN regioes AS D ON (IF(qr_admitidos.regiao_para IS NOT NULL,D.id_regiao = qr_admitidos.regiao_para, IF(qr_admitidos.regiao_de IS NOT NULL,D.id_regiao = qr_admitidos.regiao_de,D.id_regiao = qr_admitidos.id_regiao)))    
                                            LEFT JOIN projeto AS E ON (IF(qr_admitidos.projeto_para IS NOT NULL,E.id_projeto = qr_admitidos.projeto_para, IF(qr_admitidos.projeto_de IS NOT NULL,E.id_projeto = qr_admitidos.projeto_de,E.id_projeto = qr_admitidos.id_projeto)))
                                                                                              WHERE  E.id_projeto IN($ids_projeto_clt)
                                                                                             ");
                 $row_total_clt = mysql_fetch_assoc($qr_total_clt);
                 
                    
                ///echo $row_empresa['cnpj'].'<br>';
                $DADOS['TIPO_REGISTRO'] = 'B';
                $DADOS['IDENTIFICADOR'] =  1;
                $DADOS['NIDENTIFICADOR'] = RemoveCaracteres($row_empresa['cnpj']);
                $DADOS['SEQUENCIA']     = sprintf('%05d',$cont++); // 
                $DADOS['PRIMEIRA']      = '2'; // 1 - PRIMEIRA DECLARAÇÃO; 2 2 - JÁ INFORMADO
                $DADOS['CEP']           = RemoveEspacos(RemoveCaracteres($row_empresa['cep']));
                $DADOS['RAZAO']         = RemoveCaracteres($row_empresa['razao']);    
                $DADOS['ENDERECO']      = RemoveCaracteres($row_empresa['endereco']);
                $DADOS['BAIRRO']        = $file->completa($row_empresa['bairro'],20); //
                $DADOS['UF']            = $row_empresa['uf'];
                $DADOS['CNAE']          = RemoveCaracteres($row_empresa['cnae']);
                $DADOS['DDD']           = '00'.RemoveCaracteres(substr($row_empresa['ddd'],0,4)); // DDD COM 4 POSIÇÕES
                $DADOS['TELEFONE']      =  RemoveEspacos(RemoveCaracteres(substr($row_empresa['telefone'],4)));
                $DADOS['EMAIL']         = $row_empresa['email'];
                //$DADOS['TOTAL_EMPREGADOS'] = $file->completa('1523',5,'0','antes');  ////ANTES DA ALTERAÇÃO 
                $DADOS['TOTAL_EMPREGADOS'] = $file->completa($total_primeiro_dia,5,'0','antes');

                $DADOS['PORTE'] = '2';
                $DADOS['CNAE'] = RemoveCaracteres($row_master['cnae']); // CNAE 7 POSIÇÃO
                $DADOS['TELEFONE'] = '26351811'; // TELEFONE PARA CONTATO COM O RESPONSAVEL PELAS INFORMAÇÕES DO CAGED
                $DADOS['EMAIL'] = $file->completa($row_master['email'],50);


                $file->dados($DADOS['TIPO_REGISTRO']);
                $file->dados($DADOS['IDENTIFICADOR']);
                $file->dados($DADOS['NIDENTIFICADOR']);
                $file->dados($DADOS['SEQUENCIA']);
                $file->dados($DADOS['PRIMEIRA']);
                $file->dados($DADOS['ALTERACAO']);
                $file->dados($DADOS['CEP']);
                $file->filler(5);
                $file->dados($file->completa(substr($DADOS['RAZAO'],0,40),40));
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

                }
                
                $cnpjAnt = $row_empresa['cnpj'];
 }    
    
    
    
    
$DADOS['TIPO_REGISTRO'] = 'C';
$DADOS['SEQUENCIA']     = sprintf('%05d',$cont); // 
$DADOS['PIS']           = sprintf('%011s',substr($row_clt['pis_limpo'],0,11)); 
$DADOS['SEXO'] = $row_clt['clt_sexo'];
$DADOS['DATA_NASCIMENTO'] 	= limpaData($row_clt['data_nasci']); 
$DADOS['GRAU_INSTRUCAO'] 	= sprintf('%02d',$row_clt['escolaridade']);
$DADOS['SALARIO'] 		= $file->completa(str_replace('.','',$row_clt['salario']),8,'0','antes');
$DADOS['HORARIO']		= sprintf('%02d',$row_clt['horas_semanais']);
$DADOS['ADMISSAO']		= limpaData($row_clt['data_entrada']);
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
  $dia = explode("-",$row_clt['data_demi']);
  $dia_saida = ($mes == $dia[1] and $ano == $dia[0] and $row_clt['movimento'] == 'DEMITIDO')? $dia[2]: '';

$status_demi = array(60,61,62,65,66,81,100,80,63);
$codigos_desligamento = array( 61 =>31, 64 => 31, 60 => 32, 63 => 40, 65 => 40, 66 => 43, 101 => 50, 81 => 60);
if($row_clt['movimento'] == 'DEMITIDO' ){   
    
    $cod_movimentacao = $codigos_desligamento[$row_clt['status']];
    if(empty($cod_movimentacao)) $cod_movimentacao = 31;
    $DADOS['MOVIMENTO']			= $file->completa($cod_movimentacao,2);
      
        
}else{
	$DADOS['MOVIMENTO']			= $file->completa($row_clt['status_admi'],2);
}



$DADOS['DESLIGAMENTO']                  = $file->completa($dia_saida,2);
$DADOS['NOME']                          = sprintf( '%-40s',substr($row_clt['nome_clt'],0,40));
$DADOS['CT']				= $file->completa(RemoveCaracteres($row_clt['campo1']),8);
$DADOS['SERIE_CT']			= $file->completa(RemoveCaracteres($row_clt['serie_ctps']),4);
// buscando a raça do clt
$qr_etnia 	= mysql_query("SELECT cod FROM etnias WHERE id = '$row_clt[etnia]'");
$cod 		= mysql_fetch_assoc($qr_etnia);
$cod_etinia = $cod['cod'] * 1;
$DADOS['RACA'] 		= $file->completa($cod_etinia,1);


// verificando deficiencia
if(empty($row_clt['deficiencia'])){
	$DADOS['DEFICIENCIA'] = '2';
	$DADOS['TIPO_DEFICIENCIA'] = '2';
}else{
	$DADOS['DEFICIENCIA'] = '1';
	$qr_deficiencia = mysql_query("SELECT cod FROM deficiencias WHERE id = '$row_clt[deficiencia]'");
	$row_deficiencia = mysql_fetch_assoc($qr_deficiencia);
	$DADOS['TIPO_DEFICIENCIA'] = $file->completa($row_deficiencia['cod'],1);
}


$qr_cbo  = mysql_query("SELECT cod FROM rh_cbo WHERE id_cbo = '$row_clt[cbo_codigo]'");
$row_cbo = mysql_fetch_assoc($qr_cbo);
$num_cbo = mysql_num_rows($qr_cbo);
$cbo     = $row_cbo['cod'];

$DADOS['CBO'] = $file->completa(str_replace('-','',str_replace('.','',$cbo)),6,0,'antes');
$DADOS['APRENDIZ'] = '2';
$DADOS['UF_CT'] = $file->completa($row_clt['uf_ctps'],2);
$DADOS['CPF'] = $row_clt['cpf_limpo'];
$DADOS['CEP'] = sprintf('%08d',$row_clt['cep_limpo']); ///$row_admitidos['cep']


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

$projetoAnt = $row_clt['id_projeto_transferencia'];
};



// Gera o arquivo
$diretorio = '../Arquivos/';
$nome = "CGD_$_REQUEST[regiao]_$_REQUEST[projeto]_".$mes."_".$ano.".m";
$caminho = $diretorio.$nome;
if(file_exists($caminho))
	unlink($caminho);
$fp = fopen($caminho, "a");
$escreve = fwrite($fp, $file->arquivo);
fclose($fp);


//echo "<a href='".$diretorio."download.php?file=$nome'>Download</a>";
header("Location:$diretorio"."download.php?file=$nome");
?>
