<?php

abstract class ICagedClass {

    private $relacao = array();
    public $empresas = array();
    private $total_movimentos = array();
    private $dados_competencia = array();
    public $contador = array();
    public $sql_transferidos = '';
    public $sql_admitidos = '';
    public $sql_demitidos = '';

    function getTransferidos($id_master, $filtro_ano, $filtro_mes){
        $filtro_data = ( ($filtro_ano) && ($filtro_mes)) ? "  AND IF(B.id_transferencia IS NULL, DATE_FORMAT(A.data_entrada,'%Y-%m'), DATE_FORMAT(B.data_proc,'%Y-%m')) = ".$filtro_ano.'-'.$filtro_mes : '';
        //pegando os transferidos
        $this->sql_transferidos = "
                SELECT 
                    C.id_projeto, D.cnpj,
                   REPLACE(
                   REPLACE(
                   REPLACE(D.cep,'.',''),'/',''),'-','') AS cep_empresa, D.razao AS razao_empresa, D.endereco AS endereco_empresa, D.bairro AS bairro_empresa, D.uf AS uf_empresa,
                   REPLACE(
                   REPLACE(
                   REPLACE(D.cnpj,'.',''),'/',''),'-','') AS cnpj_limpo, D.cnae, D.tel AS tel_empresa,D.email AS email_empresa, C.nome AS nome_projeto, IF(B.id_projeto_para=C.id_projeto,'entrada','saida') AS tipo, A.id_clt, 

                   A.nome AS nome_funcionario, 
                   REPLACE(REPLACE(REPLACE(A.pis,'.',''),'/',''),'-','') AS pis_limpo, IF(A.sexo='M',1,2) AS sexo, DATE_FORMAT(A.data_nasci,'%d%m%Y') AS data_nasci_f, A.data_nasci, A.escolaridade, A.status,  A.status_admi,
                   DATE_FORMAT(A.data_entrada,'%d%m%Y') AS data_entrada_f, DATE_FORMAT(A.data_saida,'%d%m%Y') AS data_saida_f,
                    A.campo1 AS numero_ctps, A.serie_ctps, A.uf_ctps, A.cep AS cep_trabalhador, REPLACE(REPLACE(REPLACE(A.cpf,'.',''),'/',''),'-','') AS cpf_limpo, A.etnia, 
                     IF(CHAR_LENGTH(A.deficiencia)<=0,2,A.deficiencia)  AS deficiencia, IF(B.id_projeto_para=C.id_projeto, B.id_curso_para, B.id_curso_de) AS id_curso,
                     
                     IF(
	                      IF( (SELECT @var_salario_competencia:=G.salario_novo AS salario_competencia FROM rh_salario AS G 
	                                WHERE G.status=1 AND G.id_curso=E.id_curso AND DATE_FORMAT(G.data,'%Y-%m-%d')<=DATE_FORMAT(B.data_proc,'%Y-%m-%d') ORDER BY G.data DESC LIMIT 1),
									@var_salario_competencia,
					                            (SELECT @var_salario_competencia:=I.salario_antigo FROM rh_salario AS I WHERE  DATE_FORMAT(I.data,'%Y-%m-%d')>DATE_FORMAT(B.data_proc,'%Y-%m-%d') AND I.id_curso=E.id_curso AND I.status=1 ORDER BY DATE_FORMAT(I.data,'%Y-%m-%d') ASC LIMIT 1)
								 ), @var_salario_competencia ,E.salario) 
							 
							 AS salario_competencia


                   ,IF(B.id_transferencia IS NULL, A.data_entrada, B.data_proc) AS data_proc, DATE_FORMAT(IF(B.id_transferencia IS NULL, A.data_entrada, B.data_proc),'%d%m%Y') AS data_proc_f, IF(B.id_transferencia IS NULL, DATE_FORMAT(A.data_entrada,'%Y-%m'), DATE_FORMAT(B.data_proc,'%Y-%m')) AS data_competencia, F.cod AS cbo, 
                   G.horas_semanais AS hora_semana
                   FROM rh_clt AS A
                   INNER JOIN (
                   SELECT B.id_transferencia, 
                    B.id_projeto_de, B.id_projeto_para, B.data_proc, B.id_clt,  B.id_curso_para, B.id_curso_de
                   FROM rh_transferencias AS B
                   WHERE (SELECT REPLACE(REPLACE(REPLACE(cnpj,'.',''),'/',''),'-','') AS cnpj FROM rhempresa WHERE id_projeto=B.id_projeto_de)!=
						 (SELECT REPLACE(REPLACE(REPLACE(cnpj,'.',''),'/',''),'-','') AS cnpj FROM rhempresa WHERE id_projeto=B.id_projeto_para) AND B.`status`=1) AS B ON(B.id_clt=A.id_clt)
                   LEFT JOIN projeto AS C ON(B.id_projeto_de=C.id_projeto OR B.id_projeto_para = C.id_projeto)
                   LEFT JOIN rhempresa AS D ON(C.id_projeto=D.id_projeto)
                   LEFT JOIN curso AS E ON(E.id_curso=IF(B.id_projeto_para=C.id_projeto, B.id_curso_para, B.id_curso_de))
                   LEFT JOIN rh_cbo AS F ON(F.id_cbo=E.cbo_codigo)
                   LEFT JOIN rh_horarios AS G ON(G.id_horario=A.rh_horario)
                   WHERE D.cnpj IS NOT NULL AND C.id_master='$id_master' $filtro_data;";
       
        $result = mysql_query($this->sql_transferidos);
        
        $arr_transferidos = array();
        while ($resp = mysql_fetch_array($result)) {
            $arr_transferidos[] = $resp;
        }
        return $arr_transferidos;
    }
    function getAdmitidos($id_master, $filtro_ano, $filtro_mes){
        $filtro_data = ( ($filtro_ano) && ($filtro_mes)) ? " AND DATE_FORMAT(A.data_entrada,'%Y-%m') = ".$filtro_ano.'-'.$filtro_mes : ' ';
        // pegando os admitidos
        $this->sql_admitidos = "SELECT B.id_projeto, C.cnpj,
                   REPLACE(
                   REPLACE(
                   REPLACE(C.cep,'.',''),'/',''),'-','') AS cep_empresa, C.razao AS razao_empresa, C.endereco AS endereco_empresa, C.bairro AS bairro_empresa, C.uf AS uf_empresa,
                   REPLACE(
                   REPLACE(
                   REPLACE(C.cnpj,'.',''),'/',''),'-','') AS cnpj_limpo, C.cnae, C.tel AS tel_empresa,C.email AS email_empresa, B.nome, 'admissao' AS tipo, A.id_clt, 

                   A.nome AS nome_funcionario, 
                   REPLACE(REPLACE(REPLACE(A.pis,'.',''),'/',''),'-','') AS pis_limpo, IF(A.sexo='M',1,2) AS sexo, DATE_FORMAT(A.data_nasci,'%d%m%Y') AS data_nasci_f, A.data_nasci, A.escolaridade, A.status,  A.status_admi,
                    DATE_FORMAT(A.data_entrada,'%d%m%Y') AS data_entrada_f, DATE_FORMAT(A.data_saida,'%d%m%Y') AS data_saida_f,
                    A.campo1 AS numero_ctps, A.serie_ctps, A.uf_ctps, A.cep AS cep_trabalhador, REPLACE(REPLACE(REPLACE(A.cpf,'.',''),'/',''),'-','') AS cpf_limpo , A.etnia, 
                      IF(CHAR_LENGTH(A.deficiencia)<=0,2,A.deficiencia)  AS deficiencia, D.id_curso,
                      
                         IF(
                            IF( (SELECT @var_salario_competencia:=G.salario_novo AS salario_competencia FROM rh_salario AS G 
                                      WHERE G.status=1 AND G.id_curso=D.id_curso AND DATE_FORMAT(G.data,'%Y-%m-%d')<=DATE_FORMAT(A.data_entrada,'%Y-%m-%d') ORDER BY G.data DESC LIMIT 1),
                                                                      @var_salario_competencia,
                                                                  (SELECT @var_salario_competencia:=I.salario_antigo FROM rh_salario AS I WHERE  DATE_FORMAT(I.data,'%Y-%m-%d')>DATE_FORMAT(A.data_entrada,'%Y-%m-%d') AND I.id_curso=D.id_curso AND I.status=1 ORDER BY DATE_FORMAT(I.data,'%Y-%m-%d') ASC LIMIT 1)
                                                               ), @var_salario_competencia ,D.salario) 

                                                       AS salario_competencia



                   ,A.data_entrada AS data_proc, DATE_FORMAT(A.data_entrada,'%d%m%Y') AS data_proc_f, DATE_FORMAT(A.data_entrada,'%Y-%m') AS data_competencia, E.cod AS cbo, 
                   F.horas_semanais AS hora_semana
                   FROM rh_clt AS A
                   LEFT JOIN projeto AS B ON(A.id_projeto=B.id_projeto)
                   LEFT JOIN rhempresa AS C ON(B.id_projeto=C.id_projeto) 
                   LEFT JOIN curso AS D ON( D.id_curso= IF (( SELECT @id_curso:=id_curso_de FROM rh_transferencias WHERE id_clt=A.id_clt ORDER BY rh_transferencias.id_transferencia ASC LIMIT 1 ) IS NULL, A.id_curso, @id_curso ) )
                   LEFT JOIN rh_cbo AS E ON(E.id_cbo=D.cbo_codigo)
                   LEFT JOIN rh_horarios AS F ON(F.id_horario=A.rh_horario)
						 WHERE C.cnpj IS NOT NULL  AND B.id_master='$id_master' $filtro_data;";
       
        $result = mysql_query($this->sql_admitidos);
        
        $arr_admitidos = array();
        while ($resp = mysql_fetch_array($result)) {
            $arr_admitidos[] = $resp;
        }
        return $arr_admitidos;
    }
    function getDemitidos($id_master, $filtro_ano, $filtro_mes){
        $filtro_data = ( ($filtro_ano) && ($filtro_mes)) ? " AND DATE_FORMAT( A.data_demi,'%Y-%m') =  ".$filtro_ano.'-'.$filtro_mes : ' ';
        // pegando os demitidos
        $this->sql_demitidos = "SELECT B.id_projeto, C.cnpj,
                   REPLACE(
                   REPLACE(
                   REPLACE(C.cep,'.',''),'/',''),'-','') AS cep_empresa, C.razao AS razao_empresa, C.endereco AS endereco_empresa, C.bairro AS bairro_empresa, C.uf AS uf_empresa,
                   REPLACE(
                   REPLACE(
                   REPLACE(C.cnpj,'.',''),'/',''),'-','') AS cnpj_limpo, C.cnae, C.tel AS tel_empresa,C.email AS email_empresa, B.nome, 'demissao' AS tipo, A.id_clt, 

                   A.nome AS nome_funcionario, 
                   REPLACE(REPLACE(REPLACE(A.pis,'.',''),'/',''),'-','') AS pis_limpo, IF(A.sexo='M',1,2) AS sexo, DATE_FORMAT(A.data_nasci,'%d%m%Y') AS data_nasci_f, A.data_nasci, A.escolaridade, A.status,  A.status_admi,
                    DATE_FORMAT(A.data_entrada,'%d%m%Y') AS data_entrada_f, DATE_FORMAT(A.data_saida,'%d%m%Y') AS data_saida_f,
                    A.campo1 AS numero_ctps, A.serie_ctps, A.uf_ctps, A.cep AS cep_trabalhador, REPLACE(REPLACE(REPLACE(A.cpf,'.',''),'/',''),'-','') AS cpf_limpo , A.etnia, 
                      IF(CHAR_LENGTH(A.deficiencia)<=0,2,A.deficiencia)  AS deficiencia, D.id_curso,
                      
                       IF( (SELECT @var_salario_competencia:=G.salario_novo AS salario_competencia FROM rh_salario AS G 
                                WHERE G.status=1 AND G.id_curso=D.id_curso AND DATE_FORMAT(G.data,'%Y-%m-%d')<=DATE_FORMAT(A.data_saida,'%Y-%m-%d') ORDER BY G.data DESC LIMIT 1),
											@var_salario_competencia,
							                            (SELECT I.salario_antigo FROM rh_salario AS I WHERE  DATE_FORMAT(I.data,'%Y-%m-%d')>DATE_FORMAT(A.data_saida,'%Y-%m-%d') AND I.id_curso=D.id_curso AND I.status=1 ORDER BY DATE_FORMAT(I.data,'%Y-%m-%d') ASC LIMIT 1)
										 ) AS salario_competencia


						, A.data_demi AS data_proc, DATE_FORMAT( A.data_demi,'%d%m%Y') AS data_proc_f, DATE_FORMAT( A.data_demi,'%Y-%m') AS data_competencia, E.cod AS cbo, 
                                                F.horas_semanais AS hora_semana
                   FROM rh_clt AS A
                   LEFT JOIN projeto AS B ON(A.id_projeto=B.id_projeto)
                   LEFT JOIN rhempresa AS C ON(B.id_projeto=C.id_projeto) 
                   LEFT JOIN curso AS D ON( D.id_curso= IF (( SELECT @id_curso:=id_curso_de FROM rh_transferencias WHERE id_clt=A.id_clt ORDER BY rh_transferencias.id_transferencia ASC LIMIT 1 ) IS NULL, A.id_curso, @id_curso ) )
                   LEFT JOIN rh_cbo AS E ON(E.id_cbo=D.cbo_codigo)
                   LEFT JOIN rh_horarios AS F ON(F.id_horario=A.rh_horario)
						 WHERE C.cnpj IS NOT NULL  AND A.status_demi=1 AND B.id_master='$id_master' $filtro_data;";
       
        $result = mysql_query($this->sql_demitidos);
        
        $arr_demitidos = array();
        while ($resp = mysql_fetch_array($result)) {
            $arr_demitidos[] = $resp;
        }
        return $arr_demitidos;
    }
    function carregaRelacao($id_master, $filtro_ano, $filtro_mes) {
        
        $arr_transferidos = $this->getTransferidos($id_master, $filtro_ano, $filtro_mes);
        $arr_admitidos = $this->getAdmitidos($id_master, $filtro_ano, $filtro_mes);
        $arr_demitidos = $this->getDemitidos($id_master, $filtro_ano, $filtro_mes);
        
        
        
        $array_completo = array_merge($arr_transferidos,$arr_admitidos, $arr_demitidos);
        
        foreach($array_completo as $resp){
            $this->empresas[$resp['cnpj_limpo']]['cep_empresa'] = $resp['cep_empresa'];
            $this->empresas[$resp['cnpj_limpo']]['razao_empresa'] = $resp['razao_empresa'];
            $this->empresas[$resp['cnpj_limpo']]['endereco_empresa'] = $resp['endereco_empresa'];
            $this->empresas[$resp['cnpj_limpo']]['bairro_empresa'] = $resp['bairro_empresa'];
            $this->empresas[$resp['cnpj_limpo']]['uf_empresa'] = $resp['uf_empresa'];
            $this->empresas[$resp['cnpj_limpo']]['cnae'] = $resp['cnae'];
            $this->empresas[$resp['cnpj_limpo']]['tel_empresa'] = $resp['tel_empresa'];
            $this->empresas[$resp['cnpj_limpo']]['email_empresa'] = $resp['email_empresa'];
            $this->empresas[$resp['cnpj_limpo']][$resp['tipo']][$resp['data_competencia']][] = $resp['id_clt'];
            $this->relacao[$resp['cnpj_limpo']][$resp['data_competencia']][] = $resp;
            $this->total_movimentos[$resp['data_competencia']] += 1; 
            $this->dados_competencia[$resp['cnpj_limpo']][$resp['data_competencia']][$resp['tipo']] += 1;
        }
            
        return $this;
    }
    function getRelacaoPorCompetencia($competencia) {
        $arr = array();
//        echo '<pre>';
//            print_r($this->relacao);
//            echo '</pre>';
//            exit();
        foreach ($this->relacao as $cnpj => $row) {
            if(isset($row[$competencia])){
//                $arr[$cnpj] = (isset($row[$competencia]) && !empty($row[$competencia])) ? $row[$competencia] : array();
                $arr[$cnpj] = $row[$competencia];                
            }
        }
        return $arr;
    }
    
    function getSalariosAlterados($ano_mes_competencia){
        $sql = "SELECT A.id_curso, A.nome, A.valor, B.`data`, B.salario_antigo, B.salario_novo, B.`status` FROM curso AS A
	LEFT JOIN rh_salario AS B ON(A.id_curso=B.id_curso) WHERE B.status=1 AND DATE_FORMAT(B.data, '%Y-%m') <= '$ano_mes_competencia' GROUP BY A.id_curso ORDER BY B.data DESC;";
        $result = mysql_query($sql);
        $salarios = array();
        while($row = mysql_fetch_array($result)){
            $salarios[$row['id_curso']] = $row;
        }
        return $salarios;
    }
    function getMaster($id_master){
        $sql = "SELECT A.*, REPLACE(REPLACE(REPLACE(A.cnpj,'.',''),'/',''),'-','') AS cnpj_limpo,"
                . "REPLACE(REPLACE(REPLACE(A.telefone,'.',''),'/',''),'-','') AS telefone_limpo FROM `master` AS A WHERE A.id_master='$id_master'";
        $result = mysql_query($sql);
        $master = array();
        while($row = mysql_fetch_array($result)){
            $master = $row;
        }
        return $master;
    }

//    function getAlteracaoSalario($id_curso, $competencia = '2014-04-03') {
//        $sql = "SELECT A.data, A.salario_antigo, A.salario_novo, B.salario FROM rh_salario AS A LEFT JOIN curso AS B ON(A.id_curso=B.id_curso)"
//                . " WHERE A.id_curso='$id_curso' AND A.`status`=1 ORDER BY A.data DESC";
//        $result = mysql_query($sql);
//        $arr = array();
//        $salario_competencia = 0;
//        while ($row = mysql_fetch_array($result)) {
//            
//            //se data for maior ou igual a competencia pega
//            if(strtotime($row['data']) >= strtotime($competencia)){
//                $arr[$row['data']]['msg'] = $row['data'].' >= '.$competencia.' (C)';
//                $salario_competencia = $row['salario_novo'];
//                $arr[$row['data']]['teste'] = $row['salario_novo'];
//            }
//            $arr[$row['data']]['salario_antigo'] = $row['salario_antigo'];
//            $arr[$row['data']]['salario_novo'] = $row['salario_novo'];
//            $arr[$row['data']]['salario'] = $row['salario'];
//        }
//        echo $competencia .' = '. $salario_competencia . '<br>';
//        return $arr;
//
////        if(mysql_num_rows($result)<=0){
////            $sql = "SELECT salario FROM curso WHERE id_curso='$id_curso' ";
////            $row = mysql_fetch_array(mysql_query($sql));
////            $arr[$competencia]['salario_competencia'] = $row['salario'];
////        }else{
////           
////        }
//    }

    
    /*
     * $competencia = '0000-00' //ano mes
     */
    function getTotalMovimentos($competencia) {
        return isset($this->total_movimentos[$competencia]) ? $this->total_movimentos[$competencia] : 0;
    }
    function gravarTotalizadorClt($id_master, $filtro_ano,$filtro_mes) {
        
        $slq_update = "UPDATE totalizador_clt SET stauts='0' WHERE `id_master`='$id_master' AND `mes`='$filtro_mes' AND `ano`='$filtro_ano';";
        mysql_query($slq_update);;
        
        $id_user = isset($_COOKIE['logado']) ? $_COOKIE['logado'] : '';
        
        $novoArr = array();
        foreach($this->dados_competencia as $cnpj=>$dados ){
            ksort($dados);
            $novoArr[$cnpj] = $dados;
        }
        $arrayCompleto = array();
        foreach($novoArr as $cnpj=>$competencia ){
            $cont = 0;
            $primeiro_dia[] = '0';
            $ultimo_dia[] = '0';
            foreach($competencia as $ano_mes=>$val){
//                $ultimo_dia_mes_anterior[$cont] = ($competencia[$ano_mes]['entrada'] + $competencia[$ano_mes]['admissao']+$ultimo_dia_mes_anterior[($cont-1)]) - $competencia[$ano_mes]['saida'];
                $ultimo_dia[$cont] = ($competencia[$ano_mes]['entrada'] + $competencia[$ano_mes]['admissao']+$ultimo_dia[($cont-1)]) - $competencia[$ano_mes]['saida'];
                $primeiro_dia[$cont] = $ultimo_dia[($cont-1)];
                $novoArr[$cnpj][$ano_mes]['primeiro_dia'] = empty($primeiro_dia[$cont]) ? '0' : $primeiro_dia[$cont];
                $novoArr[$cnpj][$ano_mes]['ultimo_dia'] = empty($ultimo_dia[$cont]) ? '0' : $ultimo_dia[$cont];
                
                $arr_data = explode('-', $ano_mes);
                
                $sql = "INSERT INTO totalizador_clt(`id_master`, `cnpj`, `mes`, `ano`, `primeiro_dia`, `ultimo_dia`, `criado_por`, `status`) "
                        . " VALUES('$id_master', '$cnpj','$arr_data[1]','$arr_data[0]','$primeiro_dia[$cont]','$ultimo_dia[$cont]','$id_user',1)";
                echo "$sql\n\n";
                mysql_query($sql);
                $cont++;    
            }         
        }
        return $novoArr;
    }
    function getTotalizadoresClt($filtro_ano, $filtro_mes){
        $sql = "SELECT * FROM totalizador_clt WHERE mes='$filtro_mes' AND ano='$filtro_ano' WHERE `status`=1";
        $result = mysql_query($sql);
        $arr = array();
        while($resp = mysql_fetch_array($result)){
            $arr[$resp['cnpj']][$resp['ano'].'-'.$resp['mes']]['primeiro_dia'] = $resp['primeiro_dia'];
            $arr[$resp['cnpj']][$resp['ano'].'-'.$resp['mes']]['ultimo_dia'] = $resp['ultimo_dia'];
        }
        return $arr;
    }

//    function getTotalPrimeiroDia($data_competencia, $cnpj){
//        $competencia_anterior =  date('Y-m', strtotime('-1 month', strtotime( $data_competencia.'-01')));
//        return (count($this->empresas[$cnpj][$competencia_anterior]['entrada']) + count($this->empresas[$cnpj][$competencia_anterior]['admissao'])) - count($this->empresas[$cnpj][$competencia_anterior]['saida']);      
//    }
    function getTotalUltimoDia($cnpj, $data_competencia) {
        return (count($this->contador[$cnpj][$data_competencia]['entrada']) + count($this->contador[$cnpj][$data_competencia]['admissao'])) - count($this->contador[$cnpj][$data_competencia]['saida']);
    }
    
    //USADO PARA PEGAR O NOME DO CURSO CASO ERRO
    function getCurso($id_curso){
        $sql = "SELECT A.id_curso, A.nome AS nome_curso, A.hora_semana, A.hora_semana, B.regiao AS nome_regiao, C.nome AS nome_projeto FROM curso AS A
                LEFT JOIN regioes AS B ON(A.id_regiao=B.id_regiao)
                LEFT JOIN projeto AS C ON(A.campo3=C.id_projeto)
                WHERE id_curso='$id_curso'";
        $result = mysql_query($sql);
        return mysql_fetch_array($result);
    }
    
    function getCodigosDesligamento(){
        return array('61' => '31', '64' => '31', '60' => '32', '63' => '40', '65' => '40', '66' => '43', '101' => '50', '81' => '60');
    }
    function getArrayUF(){
        return array("AC","AL","AM","AP","BA","CE","DF","ES","GO","MA","MT","MS","MG","PA","PB","PR","PE","PI","RJ","RN","RO","RS","RR","SC","SE","SP","TO");
    }
    function getCodigosEtnias(){
        return array('1', '2', '4', '6', '8', '9');
    }

}
