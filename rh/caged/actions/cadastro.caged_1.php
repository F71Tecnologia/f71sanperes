<?php

require("../../../conn.php");
require("class.caged.php");
include ('../../../wfunction.php');

$mes = $_REQUEST['mes'];
$ano = $_REQUEST['ano'];
$regiao = $_REQUEST['regiao'];
$projeto = $_REQUEST['projeto'];

$quantidade_movimentos = count($_REQUEST['ids_clt']);
print_r($quantidade_movimentos);
$ids_clt = implode(',', $_REQUEST['ids_clt']);
$data_referencia = $ano . '-' . $mes . '-01';

$qr_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]' ") or die(mysql_error());
$row_user = mysql_fetch_assoc($qr_user);

$qr_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_user[id_master]'");
$row_master = mysql_fetch_assoc($qr_master);


/////Buscando o total os trabalhadores ativos no primeiro dia do mês
$primeiro_dia = $ano . '-' . $mes . '-01';
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
                                 SELECT qr_admitidos.*, C.nome as nome_funcao, C.salario, C.cbo_codigo, D.id_regiao as id_regiao_transferencia, D.regiao as nome_regiao, E.id_projeto as  id_projeto_transferencia,E.nome as nome_projeto, F.horas_mes,F.horas_semanais
                                 FROM
                                 (SELECT A.id_curso,A.id_clt, A.id_regiao, A.id_projeto, A.pis, REPLACE( REPLACE(A.pis,'.',''),'-','') as pis_limpo, REPLACE( REPLACE(A.cpf,'.',''),'-','') as cpf_limpo, E.cnpj, D.cbo_codigo, 
                                        DATE_FORMAT(data_entrada,'%d/%m/%Y') as data, IF( MONTH(A.data_entrada) = '$mes' AND YEAR(A.data_entrada) = '$ano','ADMITIDO','') as movimento, A.nome as nome_clt, 
                                        IF(A.sexo = 'M',1,2) as clt_sexo, A.data_nasci, A.escolaridade, A.data_entrada,A.status_demi,A.data_demi, A.campo1,A.serie_ctps,A.uf_ctps,A.status,A.status_admi,
                                        REPLACE(A.cep,'-','') as cep_limpo, A.rh_horario,  A.deficiencia,                        
                                                         /*TRANSFERENCIAS*/
                                        (SELECT id_curso_de FROM rh_transferencias WHERE id_clt = A.id_clt AND id_curso_de <> id_curso_para AND data_proc >= '$data_referencia' ORDER BY id_transferencia ASC LIMIT 1) AS funcao_de,
                                        (SELECT id_curso_para FROM rh_transferencias WHERE id_clt = A.id_clt AND id_curso_de <> id_curso_para AND data_proc <= '$data_referencia' ORDER BY id_transferencia DESC LIMIT 1) AS funcao_para,    
                                        (SELECT id_regiao_de FROM rh_transferencias WHERE id_clt = A.id_clt AND id_regiao_de <> id_regiao_para AND data_proc >= '$data_referencia' ORDER BY id_transferencia ASC LIMIT 1) AS regiao_de,
                                        (SELECT id_regiao_para FROM rh_transferencias WHERE id_clt = A.id_clt AND id_regiao_de <> id_regiao_para AND data_proc <= '$data_referencia' ORDER BY id_transferencia DESC LIMIT 1) AS regiao_para,
                                        (SELECT id_projeto_de FROM rh_transferencias WHERE id_clt = A.id_clt AND id_projeto_de <> id_projeto_para AND data_proc >= '$data_referencia' ORDER BY id_transferencia ASC LIMIT 1) AS projeto_de,
                                        (SELECT id_projeto_para FROM rh_transferencias WHERE id_clt = A.id_clt AND id_projeto_de <> id_projeto_para AND data_proc <= '$data_referencia' ORDER BY id_transferencia DESC LIMIT 1) AS projeto_para,
                                        (SELECT id_horario_de   FROM rh_transferencias WHERE id_clt = A.id_clt AND id_horario_de <> id_horario_para AND data_proc >= '$data_referencia' ORDER BY id_transferencia ASC LIMIT 1) AS horario_de,
                                        (SELECT id_horario_para FROM rh_transferencias WHERE id_clt = A.id_clt AND id_horario_de <> id_horario_para AND data_proc <= '$data_referencia' ORDER BY id_transferencia DESC LIMIT 1) AS horario_para,
                                        G.cod AS cod_etnia
                                    FROM rh_clt AS A	                             
                                    INNER JOIN curso AS D ON (D.id_curso = A.id_curso)
                                    INNER JOIN rhempresa AS E ON (E.id_projeto = A.id_projeto)
                                    LEFT JOIN etnias AS G ON (A.etnia = G.id)
                                    WHERE YEAR(A.data_entrada) = '$ano' AND MONTH(A.data_entrada) = '$mes' AND A.id_clt IN($ids_clt)) AS qr_admitidos						  	
                                LEFT JOIN curso AS C ON (IF(qr_admitidos.funcao_para IS NOT NULL,C.id_curso   = qr_admitidos.funcao_para, IF(qr_admitidos.funcao_de IS NOT NULL,C.id_curso = qr_admitidos.funcao_de,C.id_curso = qr_admitidos.id_curso)))
                                LEFT JOIN regioes AS D ON (IF(qr_admitidos.regiao_para IS NOT NULL,D.id_regiao = qr_admitidos.regiao_para, IF(qr_admitidos.regiao_de IS NOT NULL,D.id_regiao = qr_admitidos.regiao_de,D.id_regiao = qr_admitidos.id_regiao)))    
                                LEFT JOIN projeto AS E ON (IF(qr_admitidos.projeto_para IS NOT NULL,E.id_projeto = qr_admitidos.projeto_para, IF(qr_admitidos.projeto_de IS NOT NULL,E.id_projeto = qr_admitidos.projeto_de,E.id_projeto = qr_admitidos.id_projeto))) 
				LEFT JOIN rh_horarios AS F ON (IF(qr_admitidos.horario_para IS NOT NULL,F.id_horario = qr_admitidos.horario_para, IF(qr_admitidos.horario_de IS NOT NULL,F.id_horario = qr_admitidos.horario_de,F.id_horario = qr_admitidos.rh_horario)))             

                                UNION
  
                                /**CONSULTA DOS DEMITIDOS**/
                                SELECT qr_demitidos.*, C.nome as nome_funcao, C.salario,C.cbo_codigo, D.id_regiao as id_regiao_transferencia, D.regiao as nome_regiao, E.id_projeto as  id_projeto_transferencia,E.nome as nome_projeto, F.horas_mes,F.horas_semanais
                                FROM
                                (SELECT A.id_curso,A.id_clt, A.id_regiao, A.id_projeto, A.pis, REPLACE( REPLACE(A.pis,'.',''),'-','') as pis_limpo, REPLACE( REPLACE(A.cpf,'.',''),'-','') as cpf_limpo, E.cnpj, D.cbo_codigo, 
                                    DATE_FORMAT(data_demi,'%d/%m/%Y') as data, IF( MONTH(A.data_demi) = '$mes' AND YEAR(A.data_demi) ='$ano','DEMITIDO','') as movimento, A.nome as nome_clt, 
                                    IF(A.sexo = 'M',1,2) as clt_sexo, A.data_nasci, A.escolaridade, A.data_entrada,A.status_demi,A.data_demi, A.campo1,A.serie_ctps,A.uf_ctps,A.status,A.status_admi, 
                                    REPLACE(A.cep,'-','') as cep_limpo,A.rh_horario,  A.deficiencia,  
                                                                  /*TRANSFERENCIAS*/
                                    (SELECT id_curso_de FROM rh_transferencias WHERE id_clt = A.id_clt AND id_curso_de <> id_curso_para AND data_proc >= '$data_referencia' ORDER BY id_transferencia ASC LIMIT 1) AS funcao_de,
                                    (SELECT id_curso_para FROM rh_transferencias WHERE id_clt = A.id_clt AND id_curso_de <> id_curso_para AND data_proc <= '$data_referencia' ORDER BY id_transferencia DESC LIMIT 1) AS funcao_para,    
                                    (SELECT id_regiao_de FROM rh_transferencias WHERE id_clt = A.id_clt AND id_regiao_de <> id_regiao_para AND data_proc >= '$data_referencia' ORDER BY id_transferencia ASC LIMIT 1) AS regiao_de,
                                    (SELECT id_regiao_para FROM rh_transferencias WHERE id_clt = A.id_clt AND id_regiao_de <> id_regiao_para AND data_proc <= '$data_referencia' ORDER BY id_transferencia DESC LIMIT 1) AS regiao_para,
                                    (SELECT id_projeto_de FROM rh_transferencias WHERE id_clt = A.id_clt AND id_projeto_de <> id_projeto_para AND data_proc >= '$data_referencia' ORDER BY id_transferencia ASC LIMIT 1) AS projeto_de,
                                    (SELECT id_projeto_para FROM rh_transferencias WHERE id_clt = A.id_clt AND id_projeto_de <> id_projeto_para AND data_proc <= '$data_referencia' ORDER BY id_transferencia DESC LIMIT 1) AS projeto_para,
                                    (SELECT id_horario_de FROM rh_transferencias WHERE id_clt = A.id_clt AND id_horario_de <> id_horario_para AND data_proc >= '$data_referencia' ORDER BY id_transferencia ASC LIMIT 1) AS horario_de,
                                    (SELECT id_horario_para FROM rh_transferencias WHERE id_clt = A.id_clt AND id_horario_de <> id_horario_para AND data_proc <= '$data_referencia' ORDER BY id_transferencia DESC LIMIT 1) AS horario_para,
                                    G.cod AS cod_etnia     
                                FROM rh_clt AS A 
                                INNER JOIN curso AS D ON (D.id_curso = A.id_curso)
                                INNER JOIN rhempresa AS E ON (E.id_projeto = A.id_projeto)
                                LEFT JOIN etnias AS G ON (A.etnia = G.id)
                                WHERE YEAR(A.data_demi) = '$ano' AND MONTH(A.data_demi) = '$mes' AND A.status IN(60,61,62,81,63,101,64,65,66) AND A.id_clt IN($ids_clt)) as qr_demitidos
                                    
                                LEFT JOIN curso AS C ON (IF(qr_demitidos.funcao_para IS NOT NULL,C.id_curso      = qr_demitidos.funcao_para,  IF(qr_demitidos.funcao_de IS NOT NULL,C.id_curso    = qr_demitidos.funcao_de,C.id_curso = qr_demitidos.id_curso)))
                                LEFT JOIN regioes AS D ON (IF(qr_demitidos.regiao_para IS NOT NULL,D.id_regiao   = qr_demitidos.regiao_para,  IF(qr_demitidos.regiao_de IS NOT NULL,D.id_regiao   = qr_demitidos.regiao_de,D.id_regiao = qr_demitidos.id_regiao)))    
                                LEFT JOIN projeto AS E ON (IF(qr_demitidos.projeto_para IS NOT NULL,E.id_projeto = qr_demitidos.projeto_para, IF(qr_demitidos.projeto_de IS NOT NULL,E.id_projeto = qr_demitidos.projeto_de,E.id_projeto = qr_demitidos.id_projeto)))  
                                LEFT JOIN rh_horarios AS F ON (IF(qr_demitidos.horario_para IS NOT NULL,F.id_horario = qr_demitidos.horario_para, IF(qr_demitidos.horario_de IS NOT NULL,F.id_horario = qr_demitidos.horario_de,F.id_horario = qr_demitidos.rh_horario)))  
                                ORDER BY id_projeto_transferencia,movimento,nome_clt ") or die(mysql_error());

/*
  // buscando os clt demitidos e admitidos neste mes
  $qr_clt_demitidos = mysql_query("SELECT *, 'demitido' as tipo_mov,REPLACE(REPLACE(pis,'-',''),'.','') as pis_limpo  FROM rh_clt WHERE YEAR(data_demi) = '$ano' AND MONTH(data_demi) = '$mes' AND status IN('60','61','62','81','100','80','63')  AND id_clt IN($ids_clt) AND status_reg = 1 ORDER BY nome ASC;") or die(mysql_error());
  $qr_clt_admitidos = mysql_query("SELECT *, 'admitido' as  tipo_mov, REPLACE(REPLACE(pis,'-',''),'.','') as pis_limpo FROM rh_clt WHERE YEAR(data_entrada) = '$ano' AND MONTH(data_entrada) = '$mes' AND (status != '60' OR status != '61' OR status != '62' OR status != '81' OR status != '100' OR status != '80' OR status != '63')  AND id_clt IN($ids_clt) AND status_reg = 1 ORDER BY nome ASC;");

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

 */
$qr_estabelecimentos = mysql_query("SELECT COUNT(cnpj) AS estabelecimentos FROM 
                                   (SELECT B.cnpj FROM rh_clt AS A
                                   LEFT JOIN rhempresa AS B ON (A.id_projeto = B.id_projeto)
                                   WHERE A.id_clt IN (7145,7706,7740,7698,6938,5045,7706,7415,7728) GROUP BY B.cnpj) as temp") or die(mysql_error());

$row_estabelecimentos = mysql_fetch_assoc($qr_estabelecimentos);

$file = new caged;
$DADOS = array();

function limpaData($data) {
    return implode('', array_reverse(explode('-', $data)));
}

/*
  Registro tipo A(AUTORIZAÇÃO)
 */

$sequencial = 1;
$DADOS['TIPO_REGISTRO'] = 'A'; //  DEFINE O REGISTRO A SER INFORMADO.
$DADOS['LAYOUT'] = 'L2009'; //
$DADOS['MEIOMAG'] = '0'; // MEIO MAGNETICO 
$DADOS['COMPETENCIA'] = $mes . $ano;
$DADOS['ALTERACAO'] = '2'; // 1- nada a alterar; 2 - Alterar dados cadastrais
$DADOS['SEQUENCIA'] = sprintf('%05s', $sequencial);
$DADOS['IDENTIFICADOR'] = '1'; // 1 - CNPJ; 2 - CEI;
$DADOS['NIDENTIFICADOR'] = sprintf('%014s', RemoveCaracteres($row_master['cnpj'])); // NUMERO IDENTIFICADOR DO ESTABELECIMENTO     
$DADOS['RAZAO'] = sprintf("%-35s", substr(RemoveAcentos(RemoveCaracteres($row_master['razao'])), 0, 35)); // NOME OU RAZÃO SOCIAL 35 POSIÇÕES
$DADOS['ENDERECO'] = sprintf("%-40s", substr(RemoveCaracteres($row_master['logradouro']), 0, 40)); // ENDEREÇO 40 POSIÇÕES
$DADOS['CEP'] = sprintf('%08s', RemoveEspacos(RemoveCaracteres($row_master['cep'])));
$DADOS['UF'] = sprintf('%-02s', $row_master['uf']); //UF DUAS POSI��ES 'RJ';
$DADOS['DDD'] = sprintf('%04s', RemoveCaracteres(substr($row_master['telefone'], 0, 4))); // DDD COM 4 POSIÇÕES
$DADOS['TELEFONE'] = sprintf('%08s', RemoveEspacos(RemoveCaracteres(substr($row_master['telefone'], 4))));
$DADOS['RAMAL'] = sprintf('%05s', '0'); //'00000';
$DADOS['ESTABELECIMENTOS'] = sprintf('%05s', $row_estabelecimentos['estabelecimentos']); // '00002';
$DADOS['MOVIMENTOS'] = sprintf('%05s', $quantidade_movimentos); //  QUANTIDADE DE REGITRO TIPO C 
echo $quantidade_movimentos;

/* 1ª LINHA */
$file->dados($DADOS['TIPO_REGISTRO']);
$file->dados($DADOS['LAYOUT']);
$file->filler(3); //3
//$file->dados($DADOS['MEIOMAG']);
$file->dados($DADOS['COMPETENCIA']);
$file->dados($DADOS['ALTERACAO']);
$file->dados($DADOS['SEQUENCIA']);
$file->dados($DADOS['IDENTIFICADOR']);
$file->dados($DADOS['NIDENTIFICADOR']);
$file->dados($DADOS['RAZAO']);
//$file->dados(substr($DADOS['RAZAO'], 0, 35));
$file->dados($DADOS['ENDERECO']);
//$file->dados($file->completa(substr($DADOS['ENDERECO'], 0, 40), 40));
$file->dados($DADOS['CEP']);
$file->dados($DADOS['UF']);
$file->dados($DADOS['DDD']);
$file->dados($DADOS['TELEFONE']);
$file->dados($DADOS['RAMAL']);
$file->dados($DADOS['ESTABELECIMENTOS']);
$file->dados($DADOS['MOVIMENTOS']);
$file->filler(92); //50
/* FEcha a 1 linha */
$file->fechalinha();





/* Grava na base */
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
  ano_caged = '$ano'
  ";
  $qr_consulta = mysql_query($sql) or die(mysql_error());
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
 */
/* FIM DO CADASTRAMENTO DOS DADOS NA TABELA CAGED */


//$cont = 1;

while ($row_clt = mysql_fetch_assoc($qr_trabalhadores)) {
    // $cont++;

    if ($row_clt['id_projeto_transferencia'] != $projetoAnt) {



        ////////////////////////////////////////////////
        /////  Registro tipo B(ESTABELECIMENTO)  ///////
        ///////////////////////////////////////////////        

        $query_empresa = mysql_query("select cnpj, razao,  logradouro, numero, complemento, cep, tel, bairro, uf, email, cnae
                FROM rhempresa 
                WHERE id_regiao = '$row_clt[id_regiao_transferencia]' AND id_projeto = '$row_clt[id_projeto_transferencia]' ;");
        $row_empresa = mysql_fetch_assoc($query_empresa);

        //verifica��o para projetos com o mesmo CNPJ
        if ($row_empresa['cnpj'] != $cnpjAnt) {

            $ids_projeto_clt = $row_clt['id_projeto_transferencia'];
            //$ids_projeto_clt = $row_clt['id_regiao_transferencia'];        

            $qr_total_clt = mysql_query(" SELECT COUNT(qr_admitidos.id_clt) as  qnt, D.id_regiao as id_regiao_transferencia, D.regiao as nome_regiao, E.id_projeto as  id_projeto_transferencia,E.nome as nome_projeto
                                                FROM
                                                              (SELECT A.id_curso,A.id_clt, A.id_regiao, A.id_projeto,     
                                                                     A.nome as nome_clt,A.data_entrada,A.status_demi,A.data_demi,
                                                                     /*TRANSFERENCIAS*/
                                                                     (SELECT id_regiao_de   FROM rh_transferencias WHERE id_clt = A.id_clt AND id_regiao_de <> id_regiao_para AND data_proc >= '$data_referencia' ORDER BY id_transferencia ASC LIMIT 1) AS regiao_de,
                                                                     (SELECT id_regiao_para FROM rh_transferencias WHERE id_clt = A.id_clt AND id_regiao_de <> id_regiao_para AND data_proc <= '$data_referencia' ORDER BY id_transferencia DESC LIMIT 1) AS regiao_para,
                                                                     (SELECT id_projeto_de   FROM rh_transferencias WHERE id_clt = A.id_clt AND id_projeto_de <> id_projeto_para AND data_proc >= '$data_referencia' ORDER BY id_transferencia ASC LIMIT 1) AS projeto_de,
                                                                     (SELECT id_projeto_para FROM rh_transferencias WHERE id_clt = A.id_clt AND id_projeto_de <> id_projeto_para AND data_proc <= '$data_referencia' ORDER BY id_transferencia DESC LIMIT 1) AS projeto_para

                                                                     FROM rh_clt as A	                             
                                                                             INNER JOIN curso as D 
                                                                            ON D.id_curso = A.id_curso       
                                                                            INNER JOIN rhempresa as E 
                                                                            ON E.id_projeto = A.id_projeto
                                                                            WHERE  A.data_entrada <= '$data_referencia' AND (A.data_demi > '$data_referencia' OR A.data_demi = '0000-00-00' OR A.data_demi IS NULL)) as qr_admitidos

                                            LEFT JOIN regioes AS D ON (IF(qr_admitidos.regiao_para IS NOT NULL,D.id_regiao = qr_admitidos.regiao_para, IF(qr_admitidos.regiao_de IS NOT NULL,D.id_regiao = qr_admitidos.regiao_de,D.id_regiao = qr_admitidos.id_regiao)))    
                                            LEFT JOIN projeto AS E ON (IF(qr_admitidos.projeto_para IS NOT NULL,E.id_projeto = qr_admitidos.projeto_para, IF(qr_admitidos.projeto_de IS NOT NULL,E.id_projeto = qr_admitidos.projeto_de,E.id_projeto = qr_admitidos.id_projeto)))
                                                                                              WHERE  E.id_projeto IN($ids_projeto_clt)
                                                                                             ");
            $row_total_clt = mysql_fetch_assoc($qr_total_clt);



            $DADOS['TIPO_REGISTRO'] = 'B';
            $DADOS['IDENTIFICADOR'] = 1;
            $DADOS['NIDENTIFICADOR'] = sprintf('%014s', RemoveCaracteres($row_empresa['cnpj']));
            $DADOS['SEQUENCIA'] = sprintf('%05s', ++$sequencial); // 
            $DADOS['PRIMEIRA'] = '2'; // 1 - PRIMEIRA DECLARAÇÃO; 2 2 - JÁ INFORMADO
            $DADOS['ALTERACAO'] = '2'; // 1 -NADA A ATUALIZAR; 2 - ALTERAR DADOS CADASTRAIS DO ESTABELECIMENTO; 3 - FECHAMENTO DO ESTABELECIMENTO
            $DADOS['CEP'] = sprintf('%08s', RemoveEspacos(RemoveCaracteres($row_empresa['cep'])));
            $DADOS['RAZAO'] = sprintf('%-40s', substr(RemoveCaracteres($row_empresa['razao']), 0, 40));
            $DADOS['ENDERECO'] = sprintf('%-40s', substr(RemoveEspacos(RemoveCaracteres($row_empresa['logradouro'])) . RemoveEspacos(RemoveCaracteres($row_empresa['numero'])) . RemoveEspacos(RemoveCaracteres($row_empresa['complemento'])), 0, 40));
            //$DADOS['BAIRRO'] = $row_empresa['bairro'];
            $DADOS['BAIRRO'] = sprintf('%-20s', substr($row_empresa['bairro'], 0, 20)); //
            $DADOS['UF'] = sprintf('%-02s', $row_empresa['uf']);
            //$DADOS['TOTAL_EMPREGADOS'] = $file->completa('1523',5,'0','antes');  ////ANTES DA ALTERAÇÃO 
            $DADOS['TOTAL_EMPREGADOS'] = $file->completa($row_total_clt['qnt'], 5, '0', 'antes');
            $DADOS['PORTE'] = '2';
            $DADOS['CNAE'] = sprintf('%07s', RemoveCaracteres($row_empresa['cnae']));
            $DADOS['DDD'] = sprintf('%04s', RemoveCaracteres(substr($row_empresa['tel'], 0, 4))); // DDD COM 4 POSIÇÕES
            $DADOS['TELEFONE'] = sprintf('%08s', RemoveEspacos(RemoveCaracteres(substr($row_empresa['tel'], 4))));
            $DADOS['EMAIL'] = sprintf('%-50s', $row_empresa['email']);
            //    $DADOS['CNAE'] = RemoveCaracteres($row_master['cnae']); // CNAE 7 POSIÇÃO
            // $DADOS['TELEFONE'] = '26351811'; // TELEFONE PARA CONTATO COM O RESPONSAVEL PELAS INFORMAÇÕES DO CAGED
            // $DADOS['EMAIL'] = $file->completa($row_master['email'], 50);


            $file->dados($DADOS['TIPO_REGISTRO']);
            $file->dados($DADOS['IDENTIFICADOR']);
            $file->dados($DADOS['NIDENTIFICADOR']);
            $file->dados($DADOS['SEQUENCIA']);
            $file->dados($DADOS['PRIMEIRA']);
            $file->dados($DADOS['ALTERACAO']);
            $file->dados($DADOS['CEP']);
            $file->filler(5);
            $file->dados($DADOS['RAZAO']);
            $file->dados($DADOS['ENDERECO']);
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

    $DADOS['TIPO_REGISTRO'] = 'C'; //C
    $DADOS['SEQUENCIA'] = sprintf('%05s', ++$sequencial); // 
    $DADOS['PIS'] = sprintf('%011s', substr($row_clt['pis_limpo'], 0, 11));
    $DADOS['SEXO'] = $row_clt['clt_sexo'];
    $DADOS['DATA_NASCIMENTO'] = limpaData($row_clt['data_nasci']);
    $DADOS['GRAU_INSTRUCAO'] = sprintf('%02s', $row_clt['escolaridade']);
    $DADOS['SALARIO'] = $file->completa(str_replace('.', '', $row_clt['salario']), 8, '0', 'antes');
    $DADOS['HORARIO'] = sprintf('%02d', 40);
    $DADOS['ADMISSAO'] = limpaData($row_clt['data_entrada']);
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

    $status_demi = array(60, 61, 62, 65, 66, 81, 100, 80, 63);
    $codigos_desligamento = array(61 => 31, 64 => 31, 60 => 32, 63 => 40, 65 => 40, 66 => 43, 101 => 50, 81 => 60);
    if ($row_clt['movimento'] == 'DEMITIDO') {
        $cod_movimentacao = $codigos_desligamento[$row_clt['status']];
        if (empty($cod_movimentacao)) // qualquer outro tipo de desligamento que n�o seja os listados acima 
            $cod_movimentacao = 31;  // ser� tratado como DISPENSA SEM JUSTA CAUSA 
        $DADOS['MOVIMENTO'] = sprintf('%02s', $cod_movimentacao);
    }else {
        $DADOS['MOVIMENTO'] = sprintf('%02s', $row_clt['status_admi']);
    }

    $dia = explode("-", $row_clt['data_demi']);
    $dia_saida = ($mes == $dia[1] and $ano == $dia[0] and $row_clt['movimento'] == 'DEMITIDO') ? $dia[2] : '';
    $DADOS['DESLIGAMENTO'] = $file->completa($dia_saida, 2);

    $DADOS['NOME'] = sprintf('%-40s', substr($row_clt['nome_clt'], 0, 40));
    $DADOS['CT'] = sprintf("%08s", RemoveCaracteres($row_clt['campo1'])); // Mudei aqui
//$DADOS['CT']				= $file->completa(RemoveCaracteres($row_clt['campo1']),8); COMENTEI 
    // VER ISSO
    $DADOS['SERIE_CT'] = sprintf('%04s', substr(RemoveCaracteres($row_clt['serie_ctps']), -4));
    $DADOS['UF_CT'] = sprintf('%-2s',$row_clt['uf_ctps']);
//    if (ctype_digit(RemoveCaracteres($row_clt['serie_ctps']))) {
//        if (strlen(RemoveCaracteres($row_clt['serie_ctps'])) == 4) { 
//            $serieCtpsAntiga = sprintf('%04s', RemoveCaracteres($row_clt['serie_ctps']));
//            $DADOS['SERIE_CT'] = $serieCtpsAntiga;
//            $DADOS['UF_CT'] = sprintf('%-2s',$row_clt['uf_ctps']);
//        } else if (strlen(RemoveCaracteres($row_clt['serie_ctps'])) == 5) {
//            $serieCtpsNova = sprintf('%04s', substr(RemoveCaracteres($row_clt['serie_ctps']), 0,4));
//            $DADOS['SERIE_CT'] = $serieCtpsNova;
//            $DADOS['UF_CT'] = sprintf('%-2s', substr(RemoveCaracteres($row_clt['serie_ctps']),-5));
//            
//        }
//    }
    // ATE AQUI
//$DADOS['SERIE_CT']			= (strlen(RemoveCaracteres($row_clt['serie_ctps']))>4) ? '    ' :  $file->completa(RemoveCaracteres($row_clt['serie_ctps']),4); 
//$DADOS['SERIE_CT']			= $file->completa(RemoveCaracteres($row_clt['serie_ctps']),4);
// buscando a raça do clt
//    $qr_etnia = mysql_query("SELECT cod FROM etnias WHERE id = '$row_clt[etnia]'");
    //   $cod = mysql_fetch_assoc($qr_etnia);
    //   $cod_etinia = $cod['cod'] * 1;

    $DADOS['RACA'] = sprintf('%01s', substr($row_clt['cod_etnia'], 1));
// verificando deficiencia
    if (empty($row_clt['deficiencia'])) {
        $DADOS['DEFICIENCIA'] = sprintf('%01s', '2');
        $DADOS['TIPO_DEFICIENCIA'] = sprintf('%01s', '0');
    } else {
        $DADOS['DEFICIENCIA'] = sprintf('%01s', '1');
        $qr_deficiencia = mysql_query("SELECT cod FROM deficiencias WHERE id = '$row_clt[deficiencia]'");
        $row_deficiencia = mysql_fetch_assoc($qr_deficiencia);
        $DADOS['TIPO_DEFICIENCIA'] = sprintf('%01s', $row_deficiencia['cod']);
    }


    $qr_cbo = mysql_query("SELECT cod FROM rh_cbo WHERE id_cbo = '$row_clt[cbo_codigo]'");
    $row_cbo = mysql_fetch_assoc($qr_cbo);
    // $num_cbo = mysql_num_rows($qr_cbo);
    $cbo = $row_cbo['cod'];

    // $DADOS['CBO'] = $file->completa(str_replace('-', '', str_replace('.', '', $cbo)), 6, 0, 'antes');
    $DADOS['CBO'] = sprintf('%06s', RemoveCaracteres($cbo));
    $DADOS['APRENDIZ'] = sprintf('%01s', '2');
    
    $DADOS['CPF'] = sprintf('%11s', $row_clt['cpf_limpo']);
    $DADOS['CEP'] = sprintf('%08s', $row_clt['cep_limpo']); ///$row_admitidos['cep']
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

    $projetoAnt = $row_clt['id_projeto_transferencia'];
};


// Gera o arquivo
$diretorio = '../Arquivos/';
$nome = "CGD_$_REQUEST[regiao]_$_REQUEST[projeto]_" . $mes . "_" . $ano . ".txt";
$caminho = $diretorio . $nome;
if (file_exists($caminho))
    unlink($caminho);
$fp = fopen($caminho, "a");
$escreve = fwrite($fp, $file->arquivo);
fclose($fp);


echo "<a href='" . $diretorio . "download.php?file=$nome'>Download</a>";
//header("Location:$diretorio"."download.php?file=$nome");
?>