<?php

//include_once 'Grrf.class.php';

class DaoGrrf {
    
    function mysqlQueryToArray($qr) {
        $arr = array();
        while ($res = mysql_fetch_assoc($qr)) {
            $arr[] = $res;
        }
        return $arr;
    }
    
    function __construct(Grrf $grrf) {
        $this->mes = $grrf->getMes();
        $this->ano = $grrf->getAno();
        $this->regiao = $grrf->getClt()->getRegiao()->getIdRegiao();
        $this->clt = $grrf->getClt();
        $this->projeto = $grrf->getClt()->getProjeto()->getIdProjeto();
    }
    public function buscaRescisao($id_rescisao = FALSE) {
        $where_rescisao_2 = " MONTH(data_demi) = '$this->mes' AND YEAR(data_demi) = '$this->ano' AND id_regiao = '$this->regiao' AND id_projeto = '$this->projeto' AND id_clt = '{$this->clt->getIdClt()}' AND status = '1' ";
        $where_rescisao = ($id_rescisao) ? " A.id_recisao='$id_rescisao' " : $where_rescisao_2;
        $sql = "SELECT A.id_recisao, A.adiantamento_13, A.gratificacao, A.motivo, B.codigo_saque, A.cod_saque, A.cod_movimentacao as cod_mov, B.cod_movimentacao, A.plantonista,
            A.id_clt, YEAR(A.data_demi) AS ano_rescisao, A.sal_base, A.data_demi, A.data_aviso, A.terceiro_ss, A.aviso_valor, A.lei_12_506,
            A.saldo_salario, A.dt_salario,A.avos_dt, A.insalubridade, A.adicional_noturno, A.dsr, rescisao_complementar,
            IF(B.cod_movimentacao='I3' OR A.rescisao_complementar = 1,3, IF(A.aviso='indenizado',2 ,IF(A.aviso='trabalhado','1',3))) AS aviso_codigo, A.aviso, A.a479,
            A.ferias_vencidas, A.ferias_pr, A.umterco_fp, A.sal_familia, A.inss_ss, A.inss_dt, A.ir_ss, A.ir_dt, A.avos_fp
                FROM rh_recisao AS A INNER JOIN  rhstatus AS B ON B.codigo= A.motivo   WHERE $where_rescisao";                
//       echo $sql."<br>";
        return $this->mysqlQueryToArray(mysql_query($sql));
    }
    
    // pega tudo que tem incidência no FGTS na rescisao
    public function getMesRescisao(){
//        $sql = "SELECT * FROM rh_movimentos_clt 
//                                WHERE id_clt = {$this->clt->getIdClt()}
//                                AND mes_mov = 16 AND ano_mov = '2014' 
//                                AND status = 1 AND (incidencia = '5020,5021,5023' OR id_mov = 62 ) 
//                                AND id_mov not in(66,61,199) ";
        
    }
    
    public function getFPAS($id) {
        $qry = "SELECT *
            FROM fpas AS A
            WHERE A.id = {$id}";
        $sql = mysql_query($qry) or die(mysql_error());
        $res = mysql_fetch_assoc($sql);
        
        return $res;
    }

    public function buscaMovimentos(Grrf $grrf) {
        
        $mes = $grrf->getMes();
        $ano = $grrf->getAno();
        $clt = $grrf->getClt();
        
        $sql_movimento = "SELECT SUM( valor_movimento ) AS valor_movimento FROM `rh_movimentos_clt` WHERE id_clt='$clt' AND YEAR(data_movimento) = '$ano' AND MONTH(data_movimento)='$mes' AND tipo_movimento='CREDITO'";
        return mysql_fetch_array(mysql_query($sql_movimento));
    }

    public function getEmpresa(Grrf $grrf) {
        
        $regiao = $grrf->getRegiao();
        $projeto = $grrf->getProjeto();
        
        $query_empresa = mysql_query("SELECT *, REPLACE(REPLACE(cnae,'-',''),'.','') as cnae2 FROM rhempresa WHERE id_regiao = '$regiao' AND id_projeto = '$projeto'");
        $row_empresa = mysql_fetch_array($query_empresa);
    }

    

    

    public function criarArquivo($dados = array()) {
        while ($row_recisao = mysql_fetch_assoc($qr_recisao)):
            $dados['cnpj'] = $row_empresa['cnpj_matriz'];
            $dados['endereco_empresa'] = preg_replace("/(  +)/i", " ", $row_empresa['endereco']);

            $obj = new txt();
            $obj->dados('00'); // TIPO DE REGISTRO
            $obj->filler(51); // BRANCOS
            $obj->dados('2'); // TIPO DE REMESSA (2 - GRRF)
            $obj->dados('1'); // TIPO DE INSCRIÃ‡ÃƒO (1 - CNPJ)
            $obj->dados($obj->completa($obj->limpar($dados['cnpj']), 14)); // INSCRIÃ‡ÃƒO DO RESPONSÃ?VEL (1 - CNPJ)
            $obj->dados($obj->completa(RemoveCaracteres(RemoveAcentos($row_empresa['razao'])), 30));
            $obj->dados($obj->completa(RemoveCaracteres(RemoveAcentos($row_empresa['responsavel'])), 20)); // NOME RESPONSAVEL 
            $obj->dados($obj->completa(RemoveCaracteres(RemoveAcentos($dados['endereco_empresa'])), 50)); // RUA 
            $obj->dados($obj->completa(RemoveCaracteres(RemoveAcentos($row_empresa['bairro'])), 20)); // BAIRRO
            $obj->dados($obj->completa($obj->limpar($row_empresa['cep']), 8)); // CEP
            $obj->dados($obj->completa(RemoveCaracteres(RemoveAcentos($row_empresa['cidade'])), 20)); // CIDADE
            $obj->dados($obj->completa($row_empresa['uf'], 2)); // UNIDADE DA FEDERAÃ‡ÃƒO
            $obj->dados($obj->completa($obj->limpar($row_empresa['tel']), 12, '0', 'antes')); // TELEFONE
            $obj->dados($obj->completa($row_empresa['email'], 60)); // ENDEREÃ‡O INTERNET CONTATO
            $obj->dados($obj->completa($obj->limpar($data_recolhimento), 8)); // ENDEREÃ‡O INTERNET CONTATO
            //	$obj->dados($obj->completa($data_recolhimento,8));// DATA RECOLHIMENTO

            $obj->filler(60); // BRANCOS
            $obj->fechalinha('*'); // FECHA LINHA
            //
	// linha 1 

            $obj->dados('10'); // CAMPO OBRIGATORIO (SEMPRE 10)
            $obj->dados('1'); // TIPO DE INSCRIÃ‡ÃƒO (1 - CNPJ)
            $obj->dados($obj->limpar($row_empresa['cnpj_matriz'])); // INSCRIÃ‡ÃƒO DO RESPONSÃ?VEL (1 - CNPJ)
            $obj->dados($obj->completa('', 36, '0')); // ZEROS
            $obj->dados($obj->completa($obj->nome(RemoveCaracteres(RemoveAcentos($row_empresa['razao']))), 40)); // NOME EMPRESA / RAZÃƒO
            $obj->dados($obj->completa(RemoveCaracteres(RemoveAcentos($dados['endereco_empresa'])), 50)); // RUA , NÂº
            $obj->dados($obj->completa(RemoveCaracteres(RemoveAcentos($row_empresa['bairro'])), 20)); // BAIRRO
            $obj->dados($obj->completa($obj->limpar($row_empresa['cep']), 8)); // CEP
            $obj->dados($obj->completa(RemoveCaracteres(RemoveAcentos($obj->limpar($row_empresa['cidade']))), 20)); // CIDADE
            $obj->dados($obj->completa('RJ', 2)); // UNIDADE DA FEDERAÃ‡ÃƒO 
            $obj->dados($obj->completa($obj->limpar($row_empresa['tel']), 12, '0', 'antes')); // TELEFONE
            $obj->dados($obj->completa($row_empresa['cnae2'], 7)); // CNAE DA EMPRESA
            $obj->dados('1'); // SIMPLES, NÃƒO OPTANTE
            $obj->dados($obj->completa($row_empresa['fpas'], 3)); // SIMPLES, NÃƒO OPTANTE

            $obj->filler(143); // BRANCOS
            $obj->fechalinha('*'); // FECHA LINHA

            $qr_clt = mysql_query("SELECT * FROM rh_clt WHERE id_clt = '$row_recisao[id_clt]'");
            $row_clt = mysql_fetch_array($qr_clt);

            $qr_cruso = mysql_query("SELECT * FROM curso WHERE id_curso = '$row_clt[id_curso]'");
            $qr_horario = mysql_query("SELECT * FROM rh_horarios WHERE id_horario = '$row_clt[rh_horario]'");
            $qr_banco = mysql_query("SELECT id_nacional FROM bancos WHERE id_banco = '$row_clt[banco]'");
            $id_nacional = @mysql_result($qr_banco, 0);
            $row_curso = mysql_fetch_assoc($qr_cruso);
            $row_horario = mysql_fetch_assoc($qr_horario);

            // filtro de pessoas duplicadas
            if (in_array($row_clt['id_clt'], $matrizDeControle)) {
                /* ATENÇÂO AKI TEM QUE COLOCAR UM ERRO PARA QUE NãO CONTINUE CASO HAJA DUPLICADOS */
                continue;
            }
            $nome_clt = $row_clt['nome'];
            $matrizDeControle[$nome_clt] = $row_clt['id_clt'];

            $tipo_iscricao_tomador = '0';
            $tomador['cnpj'] = '00000000000000';


            $obj->dados('40'); // tipo de registro 
            $obj->dados('1'); // TIPO DE INSCRIÃ‡ÃƒO (1 - CNPJ)
            $obj->dados($obj->limpar($row_empresa['cnpj_matriz'])); // INSCRIÇÃO DA EMPRESA
            $obj->dados($tipo_iscricao_tomador); // tipo de inscrição - tomador obra const. civil (não informado)
            $obj->dados($obj->limpar($tomador['cnpj'])); // tipo de inscrição - tomador obra const. civil (não informado)
            $obj->dados($obj->completa($obj->limpar($row_clt['pis']), 11)); // PIS
            $obj->dados($obj->limpar(Data($row_clt['data_entrada']))); // data admissão            

            $obj->dados('01'); // categoria do empregador (01 - empregado)
            $obj->dados($obj->completa($obj->nome($row_clt['nome']), 70)); // Nome do trabalhador
            $obj->dados($obj->completa($obj->limpar($row_clt['campo1']), 7, '0', 'antes')); // CTPS
            $obj->dados($obj->completa($obj->limpar($row_clt['serie_ctps']), 5, '0', 'antes')); // SERIE CTPS
            if ($row_clt['sexo'] == 'M' or $row_clt['sexo'] == 'm') {
                $obj->dados('1'); // SEXO
            } else {
                $obj->dados('2'); // SEXO
            }
            $obj->dados($obj->completa($row_clt['escolaridade'], 2, '0', 'antes')); // ESCOLARIDADE
            $obj->dados($obj->limpar(Data($row_clt['data_nasci']))); // data nascimento
            // calculo das horas trabalhadas por semana
            $horas = CalcHora($row_horario['entrada_1'], $row_horario['saida_2']);
            if (strstr($row_horario['dias_semana'], '-')) {
                $partes = explode('-', $row_horario['dias_semana']);
                $dias = count($partes);
            } else {
                $dias = $row_horario['dias_semana'];
            }
            $horasPorSemanas = $horas * $dias;
            //	echo $row_curso['hora_semana'];
            $obj->dados($obj->completa('40', 2, '0', 'antes')); // quantidade de horas trabalhadas por semana
            // verificando erro onde esta o cbo
            $qr_cbo = mysql_query("SELECT cod FROM rh_cbo WHERE id_cbo = '$row_curso[cbo_codigo]'");
            $row_cbo = mysql_fetch_assoc($qr_cbo);
            $num_cbo = mysql_num_rows($qr_cbo);
            if (empty($num_cbo)) {
                $cbo = $row_curso['cbo_codigo'];
            } else {
                $cbo = $row_cbo['cod'];
            }

            $obj->dados($obj->completa($obj->limpar(substr($cbo, 0, 4)), 6, '0', 'antes')); // CODIGO CBO
            $obj->dados($obj->limpar(Data($row_clt['data_entrada']))); // data de opção            

            $sql = "SELECT * FROM rhstatus WHERE codigo=" . $row_recisao['motivo'];

            $query_rhstatus = mysql_query($sql);
            $res_rhstatus = mysql_fetch_array($query_rhstatus);

            $dados['codigo_movimentacao'] = $res_rhstatus['cod_movimentacao'];



            $data_aviso = $row_recisao['data_aviso'];
            if ($res_recisao['aviso'] == 'indenizado') {
                $aviso = 2;
                $aviso_previo_indenizado = '0';
                $data_aviso = '00000000';
                if ($dados['codigo_movimentacao'] != 'I3') { // CONDICAO - SE O CODIGO DE MOVIMENTACAO FOR IGUAL A I3 NAO DEVE SER INFORMADO O AVISO PREVIO!
                    $aviso_previo_indenizado = (number_format($row_recisao['terceiro_ss'], 2, '', '') + number_format($row_recisao['aviso_valor'], 2, '', '') + number_format($row_recisao['ferias_aviso_indenizado'], 2, '', ''));
                    $aviso_previo_indenizado = $valor_movimento + $aviso_previo_indenizado;
                }
            } elseif ($res_recisao['aviso'] == 'trabalhado') {
                $aviso = 1;
            } else {
                $aviso = '3'; //entraria ausência/dispença        
            }

            $obj->dados($obj->completa($dados['codigo_movimentacao'], 2)); // Codigo de movimento

            $obj->dados($obj->limpar(Data($row_recisao['data_demi']))); // data de movimentação

            $obj->dados($obj->completa($res_rhstatus['codigo_saque'], 3, ' ')); // código de saque

            $sql = 'SELECT * FROM rh_recisao WHERE id_clt=' . $row_clt['id_clt'];

            $q_recisao = mysql_query($sql);
            $res_recisao = mysql_fetch_array($q_recisao);

            $remu_mes = (str_replace('.', '', $row_recisao['saldo_salario']) + str_replace('.', '', $row_recisao['terceiro_ss']));
            $valor_movimento = str_replace('.', '', $row_movimento['valor_movimento']);
            $remu_mes = str_replace('.', '', $remu_mes + $valor_movimento);




            $obj->dados($aviso); // Aviso prévio (1 - trabalhado 2- Indenizado 3-Ausencia/Dispensa)
            $obj->dados($obj->limpar(Data($data_aviso))); // data início do aviso previo
            $obj->dados('S'); // Reposição de Vaga
            $obj->dados($obj->completa('', 8)); // data da HOmologação Dissídio Coletivo
            $obj->dados($obj->completa('', 15, '0')); // Valor Dissídio
            $obj->dados($obj->completa('', 15, '0')); // Remuneração mes anterior
            $data_demi = explode('-', $row_recisao['data_demi']);
            if (isset($data_demi[1]) && is_numeric($data_demi[1])) {
                $mes_recisao = $data_demi[1];
            } else {
                $mes_recisao = '';
            }
            $obj->dados($obj->completa($remu_mes, 15, '0', 'antes')); // Remuneração mes da rescisão        
            $obj->dados($obj->completa($obj->limpar($aviso_previo_indenizado), 15, '0', 'antes')); // Aviso Prévio Indenizado

            if ($row_clt['pensao_alimenticia'] == 1) {
                $percentual_pensao_alimenticia = $row_clt['pensao_percentual'];
                $valor_pensao_alimenticia = $row_recisao['valor_pensao'];
                $flag_pensao_alimenticia = 'S';
            } else {
                $valor_pensao_alimenticia = '';
                $percentual_pensao_alimenticia = '';
                $flag_pensao_alimenticia = 'N';
            }
            $obj->dados($flag_pensao_alimenticia); // Indicativo Pensão aliminticia

            $obj->dados($obj->completa($percentual_pensao_alimenticia, 5, '0')); // Percentual da pensão alimenticia
            $obj->dados($obj->completa($valor_pensao_alimenticia, 15, '0')); // Valor da Pénsão alimenticia

            $obj->dados($obj->limpar($row_clt['cpf'])); // CPF

            $banco = '';
            $agencia = '';
            $conta = '';

            $sql = 'SELECT id_nacional FROM id_banco=' . $row_clt['banco'];

            $banco = '0';
            $agencia = '0';
            $conta = '0';

            $obj->dados($obj->completa($banco, 3, '0')); //  banco da conta do trabalhador \(Não informado porque existem N de agencias com mais de 4 digitos cadastradas)

            $obj->dados($obj->completa($agencia, 4, '0'));  // Agencia


            $obj->dados($obj->completa($conta, 13, '0')); // Conta

            $obj->dados($obj->completa($valor_base_informado, 15, '0', 'antes')); // Saldo para Fins Rescisórios

            $obj->filler(39); // brancos

            $obj->fechalinha('*'); // FECHA LINHA
            //
            // Ultima linha 
            $obj->dados('90');
            $obj->dados($obj->completa('', 51, '9'));
            $obj->filler(306);
            $obj->fechalinha('*');

            // Gera o arquivo
            $diretorio = 'arquivos/grrf/';
            $nome = 'GRRF.re';
            $caminho = $diretorio . $nome;
            if (file_exists($caminho))
                unlink($caminho);
            $fp = fopen($caminho, "a");
            $escreve = fwrite($fp, $obj->arquivo);
            fclose($fp);

            mysql_query("INSERT INTO grrf (id_clt, mes, ano, id_regiao, id_projeto, user) VALUES ('$clt','$mes','$ano','$regiao','$projeto','$_COOKIE[logado]')");
            echo "<a target='_blank' href='arquivos/grrf/download.php?file=$nome'>Abrir arquivo</a>";

        endwhile;
    }

}