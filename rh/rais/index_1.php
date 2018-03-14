<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
    exit;
}

include "../../conn.php";
include "../../classes/funcionario.php";
include '../../classes_permissoes/regioes.class.php';
include("../../wfunction.php");

error_reporting(0);
$Fun = new funcionario();
$Fun->MostraUser(0);
$Master = $Fun->id_master;
$REGIOES = new Regioes();

$qr_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]'");
$row_user = mysql_fetch_assoc($qr_user);
$id_master = $row_user['id_master'];


//ANO
$optAnos = array();
for ($i = 2009; $i < date('Y'); $i++) {
    $optAnos[$i] = $i;
}
$anoSel = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : date('Y');

///REGIÕES
$regioes = montaQuery('regioes', "id_regiao,regiao", "id_master = '$id_master'");
$optRegiao = array();
foreach ($regioes as $valor) {
    $optRegiao[$valor['id_regiao']] = $valor['id_regiao'] . ' - ' . $valor['regiao'];
}
$regiaoSel = (isset($_REQUEST['id_regiao'])) ? $_REQUEST['id_regiao'] : '';





if(isset($_POST['ajax'])){
    
    
 $id_regiao = $_POST['regiao'];   
///PROJETO
$projeto = montaQuery('projeto', "id_projeto,nome", "id_regiao = '$id_regiao'");
$optProjeto = array();
$optProjeto[-1] = '<option value="">Selecione..</option>';
foreach ($projeto as $valor) {    
  echo '<option value="'.$valor['id_projeto'].'">'.$valor['id_projeto'].' - '.htmlentities($valor['nome']).'</option>';
}
exit;
    
}









if (isset($_POST['gerar'])) {

    
    //mysql_query("INSERT INTO rais (tipo,ano_base,autor,data) VALUES ('txt','$ano_base','$_COOKIE[logado]',NOW())");
    

   
    $ano_base = $_POST['ano'];   
    $id_regiao = $_POST['regiao'];
    $id_projeto = $_POST['projeto'];
    

        $qr_empresa = mysql_query("SELECT REPLACE(REPLACE(REPLACE(cnpj, '.', ''),'/',''),'-','') as cnpj, endereco, bairro, cidade, uf, cod_municipio, email, 
                                      cpf,razao,REPLACE(REPLACE(cep,'.',''),'-','') as cep,
                                      responsavel,
                                      SUBSTR(tel,2,2) as ddd,nat_juridica,proprietarios,cnae,
                                      DATE_FORMAT(data_nasc, '%d%m%Y') as data_nasc,
                                      TRIM(REPLACE(SUBSTR(tel,5,10),'-','')) as telefone
                                          FROM rhempresa WHERE id_regiao = '$id_regiao' AND id_projeto = '$id_projeto' ") or die(mysql_error());
        $empresa    = mysql_fetch_assoc($qr_empresa);

if($Master == 1){
    
         $qr_empresa = mysql_query("SELECT REPLACE(REPLACE(REPLACE(cnpj, '.', ''),'/',''),'-','') as cnpj, endereco, bairro, cidade, uf, cod_municipio, email, 
                                      cpf,razao,REPLACE(REPLACE(cep,'.',''),'-','') as cep,
                                      responsavel,
                                      SUBSTR(tel,2,2) as ddd,nat_juridica,proprietarios,cnae,
                                      DATE_FORMAT(data_nasc, '%d%m%Y') as data_nasc,
                                      TRIM(REPLACE(SUBSTR(tel,5,10),'-','')) as telefone
                                          FROM rhempresa WHERE id_regiao = '15' ") or die(mysql_error());
        $empresa    = mysql_fetch_assoc($qr_empresa);
    
    
    
        $qr_projetos_ativos = mysql_query("SELECT * FROM projeto WHERE status_reg = '1' AND id_master = 1 ");
        while($row_projetos_ativos = mysql_fetch_assoc($qr_projetos_ativos)) {
                $projetos_ativos[] = $row_projetos_ativos['id_projeto'];
        }
        $id_projeto = implode(',',$projetos_ativos);
}
        

        $qr_empregado  = mysql_query("SELECT DISTINCT(rh_clt .id_clt), rh_clt.nome, rh_folha_proc.ano 							
                                                                FROM rh_clt 
                                                                INNER JOIN rh_folha_proc ON rh_folha_proc.id_clt = rh_clt.id_clt	
                                                                WHERE rh_folha_proc.ano = '$ano_base'  AND rh_clt.id_regiao NOT IN ('2','4','6','7','11','13','20','26','36')
                                                                      AND rh_folha_proc.id_clt NOT IN(4723,
                                                            4648,
                                                            4758,
                                                            4714,
                                                            4542,
                                                            4644,
                                                            4698,
                                                            4654,4918,4898,4724)
                                                                AND rh_clt.id_projeto IN($id_projeto) AND rh_folha_proc.status = 3  AND rh_clt.status_reg = 1 ORDER BY `rh_clt`.`nome`  ASC") or die(mysql_error()); //retirado limit 0,100
        $empregado     = mysql_fetch_assoc($qr_empregado);
        $num_empregado = mysql_num_rows($qr_empregado);   
        $n_arquivo      = ($Master == 1)? '1.txt'  :$id_regiao.'_'.$id_projeto.'.txt';
        $nome_arquivo  = "arquivos/".$n_arquivo;
        
        $arquivo = fopen($nome_arquivo, "wa");

        // Linha 1
        $linha1['SEQUENCIAL'] = '1';
        $linha1['SEQUENCIAL']  = sprintf("%06s",$linha1['SEQUENCIAL']);

        $linha1['CNPJ'] = substr($empresa['cnpj'], 0, 14);
        $linha1['CNPJ'] = sprintf("%014s",$linha1['CNPJ']);

        $linha1['PREFIXO']  = '00';
        $linha1['REGISTRO'] = '0';
        $linha1['CONSTANTE'] = 1;

        $linha1['CNPJ_RESPONSAVEL']      = substr($empresa['cnpj'], 0, 14);
        $linha1['CNPJ_RESPONSAVEL']      = sprintf("%014s",$linha1['CNPJ_RESPONSAVEL']);

        $linha1['TIPO_INSCRICAO']        = 1;


        $linha1['RAZAO']  = substr(RemoveAcentos(RemoveCaracteres($empresa['razao'])), 0, 40);
        $linha1['RAZAO']  = sprintf("%-40s",$linha1['RAZAO']);


        $linha1['LOGRADOURO']  = explode(',', $empresa['endereco']);
        $linha1['LOGRADOURO']  = substr(RemoveAcentos(RemoveCaracteres(RemoveEspacos($linha1['LOGRADOURO'][0]))), 0, 40);
        $linha1['LOGRADOURO']  = sprintf("%-40s",$linha1['LOGRADOURO']);



        $linha1['NUMERO'] = explode(',', $empresa['endereco']);
        $linha1['NUMERO'] = explode('-', $linha1['NUMERO'][1]);
        $linha1['NUMERO'] = (is_numeric($linha1['NUMERO'][0]))?substr(RemoveAcentos(RemoveCaracteres(RemoveEspacos($linha1['NUMERO'][0]))), 0, 6) : 100000;
        $linha1['NUMERO'] = sprintf("%06s",$linha1['NUMERO']);



        $linha1['COMPLEMENTO'] = substr(NULL, 0, 21);
        $linha1['COMPLEMENTO'] = sprintf("%21s",$linha1['COMPLEMENTO']);

        $linha1['BAIRRO'] = substr(RemoveAcentos(RemoveCaracteres(RemoveEspacos($empresa['bairro']))), 0, 19);
        $linha1['BAIRRO'] = sprintf("%-19s",$linha1['BAIRRO']);

        $linha1['CEP']  = substr(RemoveCaracteres(RemoveEspacos($empresa['cep'])), 0, 8);
        $linha1['CEP']  =  sprintf("%08s",$linha1['CEP']);

        $linha1['COD_MUNICIPIO'] = substr(RemoveCaracteres($empresa['cod_municipio']), 0, 7);
        $linha1['COD_MUNICIPIO'] = sprintf("%07s",$linha1['COD_MUNICIPIO']);



        $linha1['NOME_MUNICIPIO'] = substr(RemoveAcentos(RemoveCaracteres(RemoveEspacos($empresa['cidade']))), 0, 30);
        $linha1['NOME_MUNICIPIO'] = sprintf("%-30s",$linha1['NOME_MUNICIPIO']);


        $linha1['UF'] = substr(RemoveAcentos(RemoveCaracteres(RemoveEspacos($empresa['uf']))), 0, 2);
        $linha1['UF'] = sprintf("%02s",$linha1['UF']);

        $linha1['DDD_TELEFONE'] = $empresa['ddd'];
        $linha1['DDD_TELEFONE'] = substr($linha1['DDD_TELEFONE'], 0, 2);
        $linha1['DDD_TELEFONE'] = sprintf("%02s",$linha1['DDD_TELEFONE']);


        $linha1['TELEFONE'] = $empresa['telefone'];
        $linha1['TELEFONE'] = substr($linha1['TELEFONE'], 0, 9);
        $linha1['TELEFONE'] =  sprintf("%09s",$linha1['TELEFONE']);

        $linha1['INDICACAO_RETIFICACAO'] = 2;

        $linha1['DATA_RETIFICACAO']    = substr(NULL, 0, 8);
        $linha1['DATA_RETIFICACAO']    = sprintf("%08s",$linha1['DATA_RETIFICACAO']);

        $linha1['DATA_GERACAO']      = $data = date('dmY');

        $linha1['EMAIL_RESPONSAVEL']  = substr($empresa['email'], 0, 45);
        $linha1['EMAIL_RESPONSAVEL']  = sprintf("%-45s",$linha1['EMAIL_RESPONSAVEL']);


        $linha1['NOME_RESPONSAVEL']    =  substr(RemoveCaracteres(RemoveAcentos($empresa['responsavel'])), 0, 52);
        $linha1['NOME_RESPONSAVEL']    =  sprintf("%-52s",$linha1['NOME_RESPONSAVEL']);


        $linha1['ESPACOS']             = sprintf("%24s",NULL);


        $linha1['CPF_RESPONSAVEL'] = RemoveCaracteres($empresa['cpf']);
        $linha1['CPF_RESPONSAVEL'] = substr($linha1['CPF_RESPONSAVEL'] , 0, 11);
        $linha1['CPF_RESPONSAVEL'] = sprintf("%011s",$linha1['CPF_RESPONSAVEL']);


        $linha1['CREA']  = NULL;
        $linha1['CREA']  = substr($linha1['CREA'], 0, 12);
        $linha1['CREA']  =  sprintf("%012s",$linha1['CREA']);

        $linha1['DATA_NASC_RESPONSAVEL'] = $empresa['data_nasc'];
        $linha1['DATA_NASC_RESPONSAVEL'] = substr($linha1['DATA_NASC_RESPONSAVEL'], 0, 8);
        $linha1['DATA_NASC_RESPONSAVEL'] = sprintf("%08s",$linha1['DATA_NASC_RESPONSAVEL']);


        $linha1['ESPACOS2'] = sprintf("%159s",NULL);
        $linha1['FIM'] = "\r\n";


        $linha1 = implode('',$linha1);

        fwrite($arquivo, $linha1);



        /*
        $tamanho_registro = '0551';
        fwrite($arquivo, $tamanho_registro, 4);*/




        // Linha 2

        $linha2['SEQUENCIAL'] = 2;
        $linha2['SEQUENCIAL'] = sprintf("%06s",$linha2['SEQUENCIAL']);

        $linha2['CNPJ'] = $empresa['cnpj'];
        $linha2['CNPJ'] = substr($linha2['CNPJ'], 0, 14);
        $linha2['CNPJ'] = sprintf("%014s",$linha2['CNPJ']);

        $linha2['PREFIXO'] = '00';


        $linha2['REGISTRO'] = '1';

        $linha2['RAZAO'] = substr(RemoveAcentos($empresa['razao']),0,52);
        $linha2['RAZAO'] = sprintf("%-52s",$linha2['RAZAO']);

        $linha2['LOGRADOURO']  = explode(',', $empresa['endereco']);
        $linha2['LOGRADOURO']  = substr(RemoveAcentos(RemoveCaracteres(RemoveEspacos($linha2['LOGRADOURO'][0]))), 0, 40);
        $linha2['LOGRADOURO']  = sprintf("%-40s",$linha2['LOGRADOURO']);

        $linha2['NUMERO'] = explode(',', $empresa['endereco']);
        $linha2['NUMERO'] = explode('-', $linha2['NUMERO'][1]);
        $linha2['NUMERO'] = (is_numeric($linha2['NUMERO'][0]))?substr(RemoveAcentos(RemoveCaracteres(RemoveEspacos($linha2['NUMERO'][0]))), 0, 6) : 100000;
        $linha2['NUMERO'] = sprintf("%06s",$linha2['NUMERO']);

        $linha2['COMPLEMENTO'] =  substr(NULL, 0, 21);
        $linha2['COMPLEMENTO'] = sprintf("%-21s",$linha2['COMPLEMENTO']); 

        $linha2['BAIRRO']      = substr(RemoveAcentos(RemoveCaracteres(RemoveEspacos($empresa['bairro']))),0,19);
        $linha2['BAIRRO']      =  sprintf("%-19s",$linha2['BAIRRO']);

        $linha2['CEP']         = $empresa['cep'];
        $linha2['CEP']         = sprintf("%8s",$linha2['CEP']);

        $linha2['COD_MUNICIPIO']   = str_replace('-', '',$empresa['cod_municipio']);
        $linha2['COD_MUNICIPIO']   = substr(RemoveAcentos(RemoveCaracteres(RemoveEspacos($linha2['COD_MUNICIPIO']))), 0, 7);
        $linha2['COD_MUNICIPIO']   = sprintf("%07s",$linha2['COD_MUNICIPIO']);

        $linha2['NOME_MUNICIPIO'] = substr(RemoveAcentos(RemoveCaracteres(RemoveEspacos($empresa['cidade']))), 0, 30);
        $linha2['NOME_MUNICIPIO'] = sprintf("%-30s",$linha2['NOME_MUNICIPIO']);

        $linha2['UF'] = substr(RemoveAcentos(RemoveCaracteres(RemoveEspacos($empresa['uf']))), 0, 2);
        $linha2['UF'] = sprintf("%02s",$linha2['UF']);

        $linha2['DDD_TELEFONE'] = $empresa['ddd'];
        $linha2['DDD_TELEFONE'] = substr($linha2['DDD_TELEFONE'], 0, 2);
        $linha2['DDD_TELEFONE'] = sprintf("%02s",$linha2['DDD_TELEFONE']);


        $linha2['TELEFONE'] = $empresa['telefone'];
        $linha2['TELEFONE'] = substr($linha2['TELEFONE'], 0, 9);
        $linha2['TELEFONE'] = sprintf("%09s",$linha2['TELEFONE']);

        $linha2['EMAIL_RESPONSAVEL']  = substr($empresa['email'], 0, 45);
        $linha2['EMAIL_RESPONSAVEL']  = sprintf("%-45s",$linha2['EMAIL_RESPONSAVEL']);

        $linha2['CNAE']     = RemoveAcentos(RemoveCaracteres(RemoveEspacos($empresa['cnae'])));
        $linha2['CNAE']     = sprintf("%07s",$linha2['CNAE']);

        $linha2['NATUREZA']     = substr($empresa['nat_juridica'],0,4);
        $linha2['NATUREZA']     = sprintf("%04s",$linha2['NATUREZA']);

        $linha2['NUMERO_PROPRIETARIO'] = substr($empresa['proprietarios'],0,4);
        $linha2['NUMERO_PROPRIETARIO'] =  sprintf("%04s", $linha2['NUMERO_PROPRIETARIO']);

        $linha2['DATA_BASE']        = '04';
        $linha2['TIPO_INSCRICAO']   = '1';
        $linha2['TIPO_RAIS']        = '0';
        $linha2['ZEROS']            = '00';

        $linha2['MATRICULA_CEI']   = sprintf("%012s",NULL);
        $linha2['ANO_BASE_RAIS']   = 2012;
        $linha2['PORTE_EMPRESA']    = 3;
        $linha2['SIMPLES']          = 2;
        $linha2['INDICADOR_PAT']    = 2;

        $linha2['PAT_ATE_5_SALARIOS']        = sprintf("%06s", NULL);
        $linha2['PAT_ACIMA_5_SALARIOS']      = sprintf("%06s", NULL);
        $linha2['PORCENTAGEM_SERVICO']       = sprintf("%03s", NULL);
        $linha2['PORCENTAGEM_ADM_COZINHA']   = sprintf("%03s", NULL);
        $linha2['PORCENTAGEM_REFEICAO_CONVENIO']        = sprintf("%03s", NULL);
        $linha2['PORCENTAGEM_REFEICAO_TRANSPORTADAS']   = sprintf("%03s", NULL);
        $linha2['PORCENTAGEM_CESTA_ALIMENTO']           = sprintf("%03s", NULL);
        $linha2['PORCENTAGEM_ALIMENTACAO_CONVENIO']     = sprintf("%03s", NULL);
        $linha2['INDICADOR_ENCERRAMENTO']               = sprintf("%01s", 2);
        $linha2['DATA_ENCERRAMENTO']                    = sprintf("%08s", NULL);
        $linha2['CNPJ_CONTRIB_ASSOCIATIVA']             = sprintf("%014s", NULL);
        $linha2['VALOR_CONTRIB_ASSOCIATIVA']            = sprintf("%09s", NULL);
        $linha2['CNPJ_CONTRIB_TRIBUTO']                 = sprintf("%014s", NULL);
        $linha2['VALOR_CONTRIB_TRIBUTO']                = sprintf("%09s", NULL);
        $linha2['CNPJ_CONTRIB_ASSISTENCIAL']           = sprintf("%014s", NULL);
        $linha2['VALOR_CONTRIB_ASSISTENCIAL']          = sprintf("%09s", NULL);
        $linha2['CNPJ_CONTRIB_CONFEDERATIVA']           = sprintf("%014s", NULL);
        $linha2['VALOR_CONTRIB_CONFEDERATIVA']          = sprintf("%09s", NULL);
        $linha2['ATIVIDADE_NO_ANO_BASE']                = sprintf("%01s", 1);
        $linha2['INDICADOR_CENTRALIZACAO']              = sprintf("%01s", 2);
        $linha2['CNPJ_CENTRALIZACAO']                   = sprintf("%014s", NULL);
        $linha2['INDICADOR_SINDICATO']                  = sprintf("%01s", 2);
        $linha2['ESPAÇOS']                              = sprintf("%87s", NULL);
        $linha2['INFORMACAO_EMPRESA']                   = sprintf("%12s", NULL);
        $linha2['FIM']  = "\r\n";


        $linha2 = implode('',$linha2);

        fwrite($arquivo, $linha2);



        // Linha 3

        $sequencial3 = '2';
        $qr_empregado  = mysql_query("SELECT A.id_clt, A.nome, B.ano, A.id_curso, A.data_nasci, A.nacionalidade, A.escolaridade, A.cpf, A.campo1, A.serie_ctps, A.data_entrada,A.etnia, A.deficiencia, A.sexo, A.rh_sindicato,A.id_regiao, A.pis,
                                        B.sallimpo
                                       FROM rh_clt AS A
                                       INNER JOIN rh_folha_proc AS B ON B.id_clt = A.id_clt
                                       WHERE B.ano = '$ano_base' AND A.id_regiao NOT IN ('2','4','6','7','11','13','20','26','36') AND A.id_projeto IN($id_projeto) AND B.status = 3 AND A.status_reg = 1
                                       AND B.id_clt NOT IN(4723,
                                                            4648,
                                                            4758,
                                                            4714,
                                                            4542,
                                                            4644,
                                                            4698,
                                                            4654,4918,4898,4724)
                                       GROUP BY A.id_clt
                                       ORDER BY A.nome ASC");
        while($empregado = mysql_fetch_assoc($qr_empregado)) {

        $qr_curso = mysql_query("SELECT * FROM curso WHERE id_curso = '$empregado[id_curso]'");
        $curso    = mysql_fetch_array($qr_curso);

        $qr_cbo  = mysql_query("SELECT cod FROM rh_cbo WHERE id_cbo = '$curso[cbo_codigo]'");
        $row_cbo = mysql_fetch_assoc($qr_cbo);
        $num_cbo = mysql_num_rows($qr_cbo);

        if(empty($num_cbo)) {
                $cbo = $curso['cbo_codigo'];
        } else {
                $cbo = $row_cbo['cod'];
        }

        $sequencial3++;
        $sequencial3 = sprintf("%06s",$sequencial3);
        fwrite($arquivo, $sequencial3, 6);

        $cnpj3 = substr(RemoveCaracteres($empresa['cnpj']), 0, 14);
        $cnpj3 = sprintf("%014s", $cnpj3);
        fwrite($arquivo, $cnpj3, 14);

        $prefixo3 = '00';
        fwrite($arquivo, $prefixo3, 2);

        $registro3 = '2';
        fwrite($arquivo, $registro3, 1);

        $empregado_pis = RemoveCaracteres($empregado['pis']);
        $empregado_pis = sprintf("%011s",$empregado_pis);
        fwrite($arquivo, $empregado_pis, 11);

        $empregado_nome = RemoveAcentos(RemoveCaracteres($empregado['nome']));
        $empregado_nome = sprintf("%-52s",$empregado_nome);
        fwrite($arquivo, $empregado_nome, 52);

        $empregado_data_nasc = implode('', array_reverse(explode('-', $empregado['data_nasci'])));
        $empregado_data_nasc = sprintf("%08s",$empregado_data_nasc);
        fwrite($arquivo, $empregado_data_nasc, 8);

      //  if($empregado['nacionalidade'] == 'BRASILEIRO' or $empregado['nacionalidade'] == 'Brasileiro' or $empregado['nacionalidade'] == 'brasileiro' or $empregado['nacionalidade'] == 'BRASILEIRA' or $empregado['nacionalidade'] == 'Brasileira' or $empregado['nacionalidade'] == 'brasileira') {
                $empregado_nacionalidade = '10';
                $empregado_ano_chegada   = NULL;
    //    }

        $empregado_nacionalidade = sprintf("%02s",$empregado_nacionalidade);
        fwrite($arquivo, $empregado_nacionalidade, 2);

        $empregado_ano_chegada = sprintf("%04s",$empregado_ano_chegada);
        fwrite($arquivo, $empregado_ano_chegada, 4);

        if($empregado['escolaridade'] < 12 and $empregado['escolaridade'] > 0) {
                $qr_cod_escolaridade = mysql_query("SELECT * FROM escolaridade WHERE id = '$empregado[escolaridade]'");
                $cod_escolaridade    = mysql_fetch_assoc($qr_cod_escolaridade);
        }

        $instrucao = number_format($cod_escolaridade['cod'],0,'.','.');
        $instrucao = sprintf("%02s",$instrucao);
        fwrite($arquivo, $instrucao, 2);

        $empregado_cpf = $empregado['cpf'];
        $empregado_cpf = str_replace('-', '', $empregado_cpf);
        $empregado_cpf = str_replace('.', '', $empregado_cpf);
        $empregado_cpf = sprintf("%011s",$empregado_cpf);
        fwrite($arquivo, $empregado_cpf, 11);

        if(strstr($empregado['campo1'],'/')) {
                $empregado_ctps = explode('/', $empregado['campo1']);
                $empregado_ctps = $empregado_ctps[0];
                $empregado_ctps_serie = $empregado_ctps[1];
        } else {
                $empregado_ctps = $empregado['campo1'];
                $empregado_ctps_serie = $empregado['serie_ctps'];
        }

        $empregado_ctps = sprintf("%08s",RemoveCaracteres(RemoveLetras(RemoveAcentos($empregado_ctps))));
        fwrite($arquivo, $empregado_ctps, 8);

        $empregado_ctps_serie = sprintf("%05s",RemoveCaracteres(RemoveLetras(RemoveAcentos($empregado_ctps_serie))));
        fwrite($arquivo, $empregado_ctps_serie, 5);

        $empregado_data_admissao = implode('', array_reverse(explode('-', $empregado['data_entrada'])));
        $empregado_data_admissao = sprintf("%08s",$empregado_data_admissao);
        fwrite($arquivo, $empregado_data_admissao, 8);

        $empregado_tipo_admissao = '2';
        $empregado_tipo_admissao = sprintf("%02s",$empregado_tipo_admissao);
        fwrite($arquivo, $empregado_tipo_admissao, 2);

     //   echo $empregado['nome'].'-'.$empregado['sallimpo'].'<br>';    
        
        $empregado_salario_contratual = $empregado['sallimpo'];
        $empregado_salario_contratual = str_replace('.', '', $empregado_salario_contratual);
        $empregado_salario_contratual = sprintf("%09s",$empregado_salario_contratual);
        fwrite($arquivo, $empregado_salario_contratual, 9);

        $empregado_tipo_salario = '1';
        $empregado_tipo_salario = sprintf("%01s",$empregado_tipo_salario);
        fwrite($arquivo, $empregado_tipo_salario, 1);

            if($empregado['id_clt'] == 4412){
                  $horas_semanais = '20';
            }else {
        $horas_semanais = '44';
            }
        
        $horas_semanais = sprintf("%02s",$horas_semanais);
        fwrite($arquivo, $horas_semanais, 2);

        $cbo = RemoveCaracteres($cbo);
        $cbo = sprintf("%06s", $cbo);
        fwrite($arquivo, $cbo, 6);

        $vinculo = '10';
        $vinculo = sprintf("%02s",$vinculo);
        fwrite($arquivo, $vinculo, 2);

        $qr_rescisao = mysql_query("SELECT * FROM rh_recisao WHERE id_clt = '$empregado[id_clt]' AND year(data_demi) = '$ano_base' AND motivo IN (60,61,62,80,81,100)");
        $rescisao = mysql_fetch_assoc($qr_rescisao);
        $verifica_rescisao = mysql_num_rows($qr_rescisao);

        if(!empty($verifica_rescisao)) {
                $dia_mes_desligamento = substr(implode('', array_reverse(explode('-', $rescisao['data_demi']))),0,4);
                $mes_desligamento = substr($dia_mes_desligamento, 2, 4);
                if ($rescisao['motivo'] == 60) {
                           $causa = '10';
                } elseif ($rescisao['motivo'] == 61) {
                           $causa = '11';
                } elseif ($rescisao['motivo'] == 62 or $rescisao['motivo'] == 100) {
                           $causa = '12';
                } elseif ($rescisao['motivo'] == 80) {
                           $causa = '76';
                } elseif ($rescisao['motivo'] == 81) {
                           $causa = '60';
                }
        } else {
                 $mes_desligamento = '13';
        }

        //$causa = '11';
        //$dia_mes_desligamento = '2702';
        //$mes_desligamento = '02';

        $causa = sprintf("%02s",$causa);
        fwrite($arquivo, $causa, 2);

        $dia_mes_desligamento = sprintf("%04s",$dia_mes_desligamento);
        fwrite($arquivo, $dia_mes_desligamento, 4);

        // remuneração no ano base

        for($f=1; $f<$mes_desligamento; $f++) {

                $tubarao     = sprintf('%02d', $f);
                $qr_folha    = mysql_query("SELECT REPLACE((salliquido - a8006),'.','') as salario FROM rh_folha_proc INNER JOIN rh_folha ON rh_folha_proc.id_folha = rh_folha.id_folha WHERE id_clt = '$empregado[id_clt]' AND rh_folha_proc.status = '3' AND rh_folha.status = '3' AND rh_folha_proc.mes = '$tubarao' AND rh_folha.ano = '$ano_base' AND rh_folha.terceiro = '2'");
                $row_folha   = mysql_fetch_assoc($qr_folha);
                $total_folha = mysql_num_rows($qr_folha);

                 if($empregado['id_clt'] == 4781){            
                    //    echo  ($row_folha['salario']).'<br>';
                    }
                
                if(!empty($total_folha)) {
                        $meses[] = $row_folha['salario'];
                } else {
                        $meses[] = NULL;
                }

        }

        if(!empty($verifica_rescisao)) {
            $meses[] = str_replace('.', '', $rescisao['total_liquido']);
        }

        
        
          
        
        $remuneracao_janeiro = $meses[0];
        $remuneracao_janeiro = sprintf("%09s",$remuneracao_janeiro);
        fwrite($arquivo, $remuneracao_janeiro, 9);

        $remuneracao_fevereiro = $meses[1];
        $remuneracao_fevereiro = sprintf("%09s",$remuneracao_fevereiro);
        fwrite($arquivo, $remuneracao_fevereiro, 9);

        $remuneracao_marco = $meses[2];
        $remuneracao_marco = sprintf("%09s",$remuneracao_marco);
        fwrite($arquivo, $remuneracao_marco, 9);

        $remuneracao_abril = $meses[3];
        $remuneracao_abril = sprintf("%09s",$remuneracao_abril);
        fwrite($arquivo, $remuneracao_abril, 9);

        $remuneracao_maio = $meses[4];
        $remuneracao_maio = sprintf("%09s",$remuneracao_maio);
        fwrite($arquivo, $remuneracao_maio, 9);

        $remuneracao_junho = $meses[5];
        $remuneracao_junho = sprintf("%09s",$remuneracao_junho);
        fwrite($arquivo, $remuneracao_junho, 9);

        $remuneracao_julho = $meses[6];
        $remuneracao_julho = sprintf("%09s",$remuneracao_julho);
        fwrite($arquivo, $remuneracao_julho, 9);

        $remuneracao_agosto = $meses[7];
        $remuneracao_agosto = sprintf("%09s",$remuneracao_agosto);
        fwrite($arquivo, $remuneracao_agosto, 9);

        $remuneracao_setembro = $meses[8];
        $remuneracao_setembro = sprintf("%09s",$remuneracao_setembro);
        fwrite($arquivo, $remuneracao_setembro, 9);

        $remuneracao_outubro = $meses[9];
        $remuneracao_outubro = sprintf("%09s",$remuneracao_outubro);
        fwrite($arquivo, $remuneracao_outubro, 9);

        $remuneracao_novembro = $meses[10];
        $remuneracao_novembro = sprintf("%09s",$remuneracao_novembro);
        fwrite($arquivo, $remuneracao_novembro, 9);

        $remuneracao_dezembro = $meses[11];
        $remuneracao_dezembro = sprintf("%09s",$remuneracao_dezembro);
        fwrite($arquivo, $remuneracao_dezembro, 9);

        unset($meses);

        // remuneração 13º salário

        $qr_salario13 = mysql_query("SELECT salliquido,rh_folha_proc.mes,tipo_terceiro,id_clt FROM rh_folha_proc INNER JOIN rh_folha ON rh_folha_proc.id_folha = rh_folha.id_folha WHERE id_clt = '$empregado[id_clt]' AND rh_folha_proc.status = '3' AND rh_folha.status = '3' AND rh_folha.ano = '$ano_base' AND rh_folha.terceiro = '1'");
        $numero_salario13 = mysql_num_rows($qr_salario13);
        if(!empty($numero_salario13)) {
                while($salario13 = mysql_fetch_assoc($qr_salario13)) {
                        if($salario13['tipo_terceiro'] == 3) {
                    $valor13_2 = str_replace('.', '', $salario13['salliquido']);
                    $mes13_2   = $salario13['mes'];
                        } elseif($salario13['tipo_terceiro'] == 1) {
                    $valor13   = str_replace('.', '', $salario13['salliquido']);
                    $mes13     = $salario13['mes'];
                        } elseif($salario13['tipo_terceiro'] == 2) {
                    $valor13_2 = str_replace('.', '', $salario13['salliquido']);
                    $mes13_2   = $salario13['mes'];
                        }
            }
        }

        $salario13_adiantamento_valor = $valor13;
        $salario13_adiantamento_valor = sprintf("%09s",$salario13_adiantamento_valor);
        fwrite($arquivo, $salario13_adiantamento_valor, 9);

        if(!empty($valor13)) {
                $salario13_adiantamento_mes = '11';
        }

        //$salario13_adiantamento_mes = $mes13;
        $salario13_adiantamento_mes = sprintf("%02s",$salario13_adiantamento_mes);
        fwrite($arquivo, $salario13_adiantamento_mes, 2);

        $salario13_final_valor = $valor13_2;
        $salario13_final_valor = sprintf("%09s",$salario13_final_valor);
        fwrite($arquivo, $salario13_final_valor, 9);

        if(!empty($valor13_2)) {
                $salario13_final_mes = '12';
        }

        //$salario13_final_mes = $mes13_2;
        $salario13_final_mes = sprintf("%02s",$salario13_final_mes);
        fwrite($arquivo, $salario13_final_mes, 2);

        $qr_etnia = mysql_query("SELECT * FROM etnias WHERE id = '$empregado[etnia]'");
        $etnia = mysql_fetch_assoc($qr_etnia);
        $etnia = number_format($etnia['cod'],0,'.','.');
        $etnia = sprintf("%01s",$etnia);
        fwrite($arquivo, $etnia, 1);

        $qr_deficiencia = mysql_query("SELECT * FROM deficiencias WHERE id = '$empregado[deficiencia]'");
        $deficiencia = mysql_fetch_assoc($qr_deficiencia);
        if (!empty($deficiencia['cod'])) {
                $indicador_deficiencia = '1';
                $tipo_deficiencia = number_format($deficiencia['cod'],0,'.','.');
        } else {
                $indicador_deficiencia = '2';
                $tipo_deficiencia = '0';
        } 

        $indicador_deficiencia = sprintf("%01s",$indicador_deficiencia);
        fwrite($arquivo, $indicador_deficiencia, 1);

        $tipo_deficiencia = sprintf("%01s",$tipo_deficiencia);
        fwrite($arquivo, $tipo_deficiencia, 1);

        $indicador_alvara = 2;
        $indicador_alvara = sprintf("%01s",$indicador_alvara);
        fwrite($arquivo, $indicador_alvara, 1);

        $aviso_previo_indenizado = NULL;
        $aviso_previo_indenizado = sprintf("%09s",$aviso_previo_indenizado);
        fwrite($arquivo, $aviso_previo_indenizado, 9);

        if ($empregado['sexo'] == 'M') {
                $empregado_sexo = '1';
        } elseif($empregado['sexo'] == "F") {
                $empregado_sexo = '2';
        }

        $empregado_sexo = sprintf("%01s",$empregado_sexo);
        fwrite($arquivo, $empregado_sexo, 1);

        // Afastamentos

        $qr_afastamentos = mysql_query("SELECT * FROM rh_eventos WHERE cod_status IN (70,20,50,30,90) AND id_clt = '$empregado[id_clt]' AND year(data) = '$ano_base' ORDER BY id_evento DESC LIMIT 0,3");
        while($afastamentos = mysql_fetch_assoc($qr_afastamentos)) {
                $afastamento_motivo[] = $afastamentos['cod_status'];
                $afastamento_inicio[] = $afastamentos['data'];
                $afastamento_final[]  = $afastamentos['data_retorno'];
                $afastamento_dias[]   = $afastamentos['dias'];
        }

           
        
        for($z=0; $z<=2; $z++) {

                if ($afastamento_motivo[$z] == 70) {
                        $afastamento_motivo_final[$z] = '10';
            } elseif ($afastamento_motivo[$z] == 20) {
                        $afastamento_motivo_final[$z] = '40';
            } elseif ($afastamento_motivo[$z] == 50) {
                        $afastamento_motivo_final[$z] = '50';
            } elseif ($afastamento_motivo[$z] == 30) {
                        $afastamento_motivo_final[$z] = '60';
            } elseif ($afastamento_motivo[$z] == 90) {
                        $afastamento_motivo_final[$z] = '70';
            }

             
                
                $afastamento_motivo_final[$z] = sprintf("%02s",$afastamento_motivo_final[$z]);
            fwrite($arquivo, $afastamento_motivo_final[$z], 2);

            $afastamento_inicio[$z] = substr(implode('', array_reverse(explode('-', $afastamento_inicio[$z]))),0,4);
                $afastamento_inicio[$z] = sprintf("%04s",$afastamento_inicio[$z]);
            fwrite($arquivo, $afastamento_inicio[$z], 4);

           list($ano_final,$mes_final, $dia_final) = explode('-', $afastamento_final[$z]); 
           
           if($ano_final == $ano_base+1){
               $dia_final = 31;
               $mes_final = 12;
           }
         
        
       
           
            $afastamento_final[$z] = substr($dia_final.$mes_final,0,4);
            $afastamento_final[$z] = sprintf("%04s",$afastamento_final[$z]);
            fwrite($arquivo, $afastamento_final[$z], 4);
             unset($dia_final,$mes_final,$ano_final);
          
            
            
            
        }

        $quantidade_dias_afastamento = $afastamento_dias[0] + $afastamento_dias[1] + $afastamento_dias[2];
        $quantidade_dias_afastamento = sprintf("%03s",$quantidade_dias_afastamento);
        fwrite($arquivo, $quantidade_dias_afastamento, 3);

        unset($afastamento_motivo);
        unset($afastamento_motivo_final);
        unset($afastamento_inicio);
        unset($afastamento_final);
        unset($afastamento_dias);
        unset($quantidade_dias_afastamento);
      

        //

        $qr_ferias_indenizadas = mysql_query("SELECT * FROM rh_recisao WHERE motivo IN (60,61,62,80,81,100) AND id_clt = '$empregado[id_clt]' AND year(data_demi) = '$ano_base'");
        $ferias_indenizadas = mysql_fetch_assoc($qr_ferias_indenizadas);
        $valor_ferias_indenizadas = $ferias_indenizadas['valor_total_ferias'];

        $valor_ferias_indenizadas = sprintf("%08s",$valor_ferias_indenizadas);
        fwrite($arquivo, $valor_ferias_indenizadas, 8);

        $valor_banco_horas = NULL;
        $valor_banco_horas = sprintf("%08s",$valor_banco_horas);
        fwrite($arquivo, $valor_banco_horas, 8);

        $quantidade_meses_banco_horas = NULL;
        $quantidade_meses_banco_horas = sprintf("%02s",$quantidade_meses_banco_horas);
        fwrite($arquivo, $quantidade_meses_banco_horas, 2);

        $valor_dissidio_coletivo = NULL;
        $valor_dissidio_coletivo = sprintf("%08s",$valor_dissidio_coletivo);
        fwrite($arquivo, $valor_dissidio_coletivo, 8);

        $quantidade_meses_dissidio_coletivo = NULL;
        $quantidade_meses_dissidio_coletivo = sprintf("%02s",$quantidade_meses_dissidio_coletivo);
        fwrite($arquivo, $quantidade_meses_dissidio_coletivo, 2);

        $valor_gratificacoes = NULL;
        $valor_gratificacoes = sprintf("%08s",$valor_gratificacoes);
        fwrite($arquivo, $valor_gratificacoes, 8);

        $quantidade_meses_gratificacoes = NULL;
        $quantidade_meses_gratificacoes = sprintf("%02s",$quantidade_meses_gratificacoes);
        fwrite($arquivo, $quantidade_meses_gratificacoes, 2);

        $valor_multa_rescisao = NULL;
        $valor_multa_rescisao = sprintf("%08s",$valor_multa_rescisao);
        fwrite($arquivo, $valor_multa_rescisao, 8);

        $cnpj_contribuicao_associativa1 = NULL;
        $cnpj_contribuicao_associativa1 = sprintf("%014s",$cnpj_contribuicao_associativa1);
        fwrite($arquivo, $cnpj_contribuicao_associativa1, 14);

        $valor_contribuicao_associativa1 = NULL;
        $valor_contribuicao_associativa1 = sprintf("%08s",$valor_contribuicao_associativa1);
        fwrite($arquivo, $valor_contribuicao_associativa1, 8);

        $cnpj_contribuicao_associativa2 = NULL;
        $cnpj_contribuicao_associativa2 = sprintf("%014s",$cnpj_contribuicao_associativa2);
        fwrite($arquivo, $cnpj_contribuicao_associativa2, 14);

        $valor_contribuicao_associativa2 = NULL;
        $valor_contribuicao_associativa2 = sprintf("%08s",$valor_contribuicao_associativa2);
        fwrite($arquivo, $valor_contribuicao_associativa2, 8);

        // Contribuição Sindical

        $qr_sindicato    = mysql_query("SELECT * FROM rhsindicato WHERE id_sindicato = '$empregado[rh_sindicato]'");
        $row_sindicato   = mysql_fetch_assoc($qr_sindicato);
        $total_sindicato = mysql_num_rows($qr_sindicato);

        //$cnpj_contribuicao_sindical = $row_sindicato['cnpj'];
        $cnpj_contribuicao_sindical = '30.132.856/0001-81';
        $cnpj_contribuicao_sindical = str_replace('.', '', $cnpj_contribuicao_sindical);
        $cnpj_contribuicao_sindical = str_replace('-', '', $cnpj_contribuicao_sindical);
        $cnpj_contribuicao_sindical = str_replace('/', '', $cnpj_contribuicao_sindical);
        $cnpj_contribuicao_sindical = sprintf("%014s",$cnpj_contribuicao_sindical);
        fwrite($arquivo, $cnpj_contribuicao_sindical, 14);

        //if(!empty($total_sindicato)) {
                $valor_sindicato = $curso['salario'] / 30;
        //}

        $calculo_sindical = $valor_sindicato;
        $calculo_sindical = number_format($calculo_sindical, 2,',','.');
        $calculo_sindical = str_replace(',', '', $calculo_sindical);
        $calculo_sindical = str_replace('.', '', $calculo_sindical);

        $valor_contribuicao_sindical = $calculo_sindical;
        $valor_contribuicao_sindical = sprintf("%08s",$valor_contribuicao_sindical);
        fwrite($arquivo, $valor_contribuicao_sindical, 8);

        //

        $cnpj_contribuicao_assistencial = NULL;
        $cnpj_contribuicao_assistencial = sprintf("%014s",$cnpj_contribuicao_assistencial);
        fwrite($arquivo, $cnpj_contribuicao_assistencial, 14);

        $valor_contribuicao_assistencial = NULL;
        $valor_contribuicao_assistencial = sprintf("%08s",$valor_contribuicao_assistencial);
        fwrite($arquivo, $valor_contribuicao_assistencial, 8);

        $cnpj_contribuicao_confederativa = NULL;
        $cnpj_contribuicao_confederativa = sprintf("%014s",$cnpj_contribuicao_confederativa);
        fwrite($arquivo, $cnpj_contribuicao_confederativa, 14);

        $valor_contribuicao_confederativa = NULL;
        $valor_contribuicao_confederativa = sprintf("%08s",$valor_contribuicao_confederativa);
        fwrite($arquivo, $valor_contribuicao_confederativa, 8);

        $empregado_cod_municipio = NULL;
        $empregado_cod_municipio = sprintf("%07s",$empregado_cod_municipio);
        fwrite($arquivo, $empregado_cod_municipio, 7);

        // horas extras trabalhadas

        $horas_extras = NULL;
        $horas_extras = sprintf("%036s",$horas_extras);
        fwrite($arquivo, $horas_extras, 36);

        //

        $empregado_indicador_filiado = '2';
        fwrite($arquivo, $empregado_indicador_filiado, 1);

        $exclusivo_empresa2 = '';
        $exclusivo_empresa2 = sprintf("%12s",$exclusivo_empresa2);
        fwrite($arquivo, $exclusivo_empresa2, 12);

        unset($empregado_pis);
        unset($empregado_nome);
        unset($empregado_data_nasc);
        unset($empregado_nacionalidade);
        unset($empregado_ano_chegada);
        unset($instrucao);
        unset($empregado_cpf);
        unset($empregado_ctps);
        unset($empregado_ctps_serie);
        unset($empregado_data_admissao);
        unset($empregado_salario_contratual);
        unset($cbo);
        unset($causa);
        unset($dia_mes_desligamento);
        unset($remuneracao_janeiro);
        unset($remuneracao_fevereiro);
        unset($remuneracao_marco);
        unset($remuneracao_abril);
        unset($remuneracao_maio);
        unset($remuneracao_junho);
        unset($remuneracao_julho);
        unset($remuneracao_agosto);
        unset($remuneracao_setembro);
        unset($remuneracao_outubro);
        unset($remuneracao_novembro);
        unset($remuneracao_dezembro);
        unset($salario13_adiantamento_valor);
        unset($salario13_adiantamento_mes);
        unset($salario13_final_valor);
        unset($salario13_final_mes);
        unset($valor13_2);
        unset($valor13);
        unset($mes13_2);
        unset($mes13);
        unset($etnia);
        unset($indicador_deficiencia);
        unset($tipo_deficiencia);
        unset($empregado_sexo);
        unset($quantidade_dias_afastamento);
        unset($cnpj_contribuicao_sindical);
        unset($valor_contribuicao_sindical);
        unset($valor_sindicato);

        fwrite($arquivo, "\r\n");

        }

        // Linha 4

        $sequencial4 = $num_empregado + 3;
        $sequencial4 = sprintf("%06s",$sequencial4);
        fwrite($arquivo, $sequencial4, 6);

        $cnpj4 = $empresa['cnpj'];
        $cnpj4 = str_replace('.', '', $cnpj4);
        $cnpj4 = str_replace('/', '', $cnpj4);
        $cnpj4 = str_replace('-', '', $cnpj4);
        $cnpj4 = substr($cnpj4, 0, 14);
        $cnpj4 = sprintf("%014s",$cnpj4);
        fwrite($arquivo, $cnpj4, 14);

        $prefixo4 = '00';
        fwrite($arquivo, $prefixo4, 2);

        $registro4 = '9';
        fwrite($arquivo, $registro4, 1);

        $total_registros1 = '1';
        $total_registros1 = sprintf("%06s",$total_registros1);
        fwrite($arquivo, $total_registros1, 6);

        $total_registros2 = $num_empregado;
        $total_registros2 = sprintf("%06s",$total_registros2);
        fwrite($arquivo, $total_registros2, 6);

        $espacos4 = '';
        $espacos4 = sprintf("%516s",$espacos4);
        fwrite($arquivo, $espacos4, 516);

        fwrite($arquivo, "\r\n");
        fclose($arquivo);

  

               header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
                header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
                header("Cache-Control: no-store, no-cache, must-revalidate");
                header("Cache-Control: post-check=0, pre-check=0", false);
                header("Pragma: no-cache");
                header("Content-type: application/x-msdownload");
                header("Content-Length: ".filesize($nome_arquivo));
                header("Content-Disposition: attachment; filename={$n_arquivo}");
                flush();

                readfile($nome_arquivo);
                exit;


}
?>
<html>
    <head>
        <title>Gerar RAIS</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
        <link href="../../net1.css" rel="stylesheet" type="text/css">
        <script src="../../jquery/jquery-1.4.2.min.js" type="text/javascript"></script>
        <script src="../../jquery/jquery.tools.min.js" type="text/javascript" ></script>      
        <script >
            $(function(){
                
               
            $('#regiao').change(function(){
                
               var regiao = $(this).val();
               $.post('index_1.php',{ ajax: 1, regiao: regiao}, function(data){
                   
                   $('#projeto').html(data);
               })
                
                
                
            })
                  
                
            });
            
        </script>


    </head>
    <body class="novaintra">       
        <div id="content">
            <div id="head">
                <img src="../../imagens/logomaster<?php echo $id_master; ?>.gif" class="fleft" style="margin-right: 25px;" width="140" height="100"/>
                <div class="fleft">
                    <h2>RAIS</h2>
                    <p>Gerar arquivo de RAIS</p>
                </div>
            </div>
            <br class="clear">
            <br/>

            <form  name="form" action="" method="post" id="form">
                <fieldset>
                    <legend>DIRF</legend>
                    <div class="fleft">
                        <p><label class="first">Ano:</label> <?php echo montaSelect($optAnos, $anoSel, array('name' => "ano", 'id' => 'ano')); ?></p>
                        <p><label class="first">Região:</label> <?php echo montaSelect($optRegiao, $regiaoSel, array('name' => "regiao", 'id' => 'regiao')); ?></p>
                        <p><label class="first">Projeto:</label> 
                            <select name="projeto" id="projeto"></select> 
                        </p>
                    </div>
  
                    <br class="clear"/>
                
                    <p class="controls" style="margin-top: 10px;">
                      <span class="fleft erro"><?php if($verifica_dirf != 0) echo 'Arquivo já existente!'; ?></span>
                      <input type="hidden" name="id_master" value="<?php echo $id_master;?>"/>                 
                        <input type="submit" name="historico" value="Exibir histórico" id="historico"/>
                        <input type="submit" name="gerar" value="Gerar" id="gerar"/>
                    </p>
                </fieldset>
        </form>
             <?php
                if(!empty($verifica_historico) and isset($_POST['historico'])){
                 
                    while($row_historico = mysql_fetch_assoc($qr_historico)){
                    ?> 
                    <span class="box_download fleft ">
                        <a href="arquivos/<?php echo $row_historico['id_master'].'_'.$row_historico['ano_calendario'].'.txt';?>" style="text-decoration:none;">
                            <img src="../../../imagens/download.png"/>
                            <br>
                            DIRF <?php echo $row_historico['ano_calendario'];?>
                        </a>
                    </span>
                    <?php
                    }                    
                } else {
                    echo '<div class="txcenter">Não existem arquivos de RAIS.</div>';
                    
                }
            
            
                
                
                ?>  
            
            
            
            
            
            
            
            <div class="clear"></div>
        </div>
  

</body>
</html>