<?php

/* * * Description of NFSeClass ** @author F71Leonardo ** */

class NFSe {

    public $xml;                        // raiz do xml
    public $ListaNfse;                  // Alias
    public $CompNfse;                   // Composição da NFSe (Alias)
    public $ConsultarNfseResposta;      // Composição da NFSe (Alias)
    public $InfNfse;                    // informações de identificação (alias)
    public $Servico;                    // informações do Serviços Prestados (Alias)
    public $PrestadorServico;           // informação do Prestador de Serviço (Alias)
    public $TomadorServico;             // informação do Tomador de Serviço (Alias)
    public $OrgaoGerador;               // Orgão Gerador (Alias)
    public $IdentificacaoRps;
    protected $prefeituras;

    public function __construct($filename = null, $prefeitura = null) {
        if (!empty($filename)) {
            $this->load($filename, $prefeitura);
        }

        $this->prefeituras = array(
            0 => 'RIO DE JANEIRO',
            1 => 'SÃO GONÇALO',
            2 => 'OUTRAS PREFEITURAS'
        );
    }

    public function load($filename, $prefeitura) {

        $this->xml = simplexml_load_file($filename);

        switch ($prefeitura) {
            case 0:
                $this->InfNfse = $this->xml->ListaNfse->CompNfse->Nfse->InfNfse;
                break;
            case 1:
                $this->InfNfse;
                break;
            case 2:
                $this->InfNfse = $this->xml->Nfse->InfNfse;
                break;
        }

        $this->IdentificacaoRps = $this->InfNfse->IdentificacaoRps;
        $this->Servico = $this->InfNfse->Servico;
        $this->PrestadorServico = $this->InfNfse->PrestadorServico;
        $this->TomadorServico = $this->InfNfse->TomadorServico;
        $this->OrgaoGerador = $this->InfNfse->OrgaoGerador;

        // converte automaticamente o xml para um array
        $this->nfse_xml_to_array($prefeitura);
    }

    public function salvarNFSe($cnpj_projeto, $cnpj_prestador, $array) {
        $array['Numero'] = str_replace('NFSe', '', $array['Numero']);
        // verificar se nota já foi gravada no DB
        $resposta = $this->verificarNFSe($array['Numero'], $array['CodigoVerificacao'], $array['PrestadorServico']);
        // verificar projeto cadastrado
        $projeto = $this->consultarProjetos($cnpj_projeto);
        // verificar prestador está cadastrado
        $prestador = $this->PrestadordeServico($cnpj_prestador, $projeto['id_projeto']);

        if ($resposta['status']) {
            return array('status' => FALSE, 'msg' => "Nota Fiscal de Serviço $nfse Já Cadastrada no Sistema");
        }
        $query = "INSERT INTO nfse (id_regiao, id_projeto, Numero, CodigoVerificacao, DataEmissao, NaturezaOperacao, OptanteSimplesNacional, 
                IncentivadorCultural, Competencia, ValorServicos, ValorPis, ValorCofins, ValorInss,ValorIr, ValorCsll, IssRetido, ValorIss, 
                BaseCalculo, Aliquota, ValorLiquidoNfse, CodigoTributacaoMunicipio, Discriminacao, CodigoMunicipio, PrestadorServico, InscricaoMunicipal, contrato_nr, status)
                VALUES ('{$projeto['id_regiao']}','{$projeto['id_projeto']}','{$nfse}','{$array['CodigoVerificacao']}','{$array['DataEmissao']}','{$array['NaturezaOperacao']}',
                '{$array['OptanteSimplesNacional']}','{$array['IncentivadorCultural']}','{$array['Competencia']}','{$array['ValorServicos']}','{$array['ValorPis']}',
                '{$array['ValorCofins']}','{$array['ValorInss']}','{$array['ValorIr']}','{$array['ValorCsll']}','{$array['IssRetido']}','{$array['ValorIss']}',
                '{$array['BaseCalculo']}','{$array['Aliquota']}','{$array['ValorLiquidoNfse']}','{$array['CodigoTributacaoMunicipio']}','{$array['Discriminacao']}',
                '{$array['CodigoMunicipio']}','{$prestador['id_prestador']}','{$array['inscricao_mun']}','{$prestador['numero']}','2')";

        $result = mysql_query($query) or die('Erro ao salvar NFSe. Detalhes: ' . mysql_error());

        $id_nfse = mysql_insert_id();
        
        $this->log($id_nfse, 2);

        return ($result) ? array('id_nfse' => $id_nfse, 'status' => true) : array('msg' => 'Erro ao salvar Nota Fiscal de Serviço.', 'status' => false);
    }

    public function inserir(array $array) {
        $x = $this->verificarNFSe($array['Numero'], $array['CodigoVerificacao'], $array['PrestadorServico']);
        if ($x['status']) {
            return array('status' => FALSE, 'msg' => $x['msg']);
        } else {
            foreach ($array as $key => $value) {
                $arr_campos[] = $key;
                $arr_valores[] = "'$value'";
            }
            $campos = implode(',', $arr_campos);
            $valores = implode(',', $arr_valores);
            $query = "INSERT INTO nfse ($campos) VALUES ($valores);";
            mysql_query($query) or die("Erro ao inserir\n$query\n" . mysql_error());
            $id_nfse = mysql_insert_id();
            
            $this->log($id_nfse, 2);
            return array('id_nfse' => $id_nfse, 'status' => true);
        }
    }

    public function update($id_nfse, array $array) {
        foreach ($array as $key => $value) {
            if (!empty($value)) {
                $arr_campos[] = "$key = '$value'";
            }
        }
        $campos = implode(',', $arr_campos);
        $query = "UPDATE nfse SET $campos WHERE id_nfse = $id_nfse";
        mysql_query($query) or die("Erro ao Editar\n$query\n" . mysql_error());
        return array('status' => TRUE, 'id_nfse' => $id_nfse);
    }

    public function anexar(array $array) {
        $upload = $this->uploadPDF($array['id_projeto'], $array['arquivo_pdf']);
        if ($upload['status']) {
            if (isset($array['id'])) {
                $this->excluirPDF($array['id']);
                $array['arquivo_pdf'] = $upload['name_file'];
                $id = $array['id'];
                unset($array['id']);
                foreach ($array as $key => $value) {
                    $arr_campos[] = "$key = '$value'";
                }
                $campos = implode(',', $arr_campos);

                $query = "UPDATE nfse_anexos SET $campos WHERE id = $id;";
                mysql_query($query) or die("Erro ao editar\n$query\n" . mysql_error());
                return array('status' => true);
            } else {
                $array['arquivo_pdf'] = $upload['name_file'];

                foreach ($array as $key => $value) {
                    $arr_campos[] = $key;
                    $arr_valores[] = "'$value'";
                }
                $campos = implode(',', $arr_campos);
                $valores = implode(',', $arr_valores);

                $query = "INSERT INTO nfse_anexos ($campos) VALUES ($valores);";
                mysql_query($query) or die("Erro ao inserir\n$query\n" . mysql_error());
                return array('id_nfse' => mysql_insert_id(), 'status' => true);
            }
        } else {
            return array('status' => FALSE);
        }
    }

    public function uploadPDF($id_projeto, $file) {
        $uploaddir = $_SERVER['DOCUMENT_ROOT'] . "/intranet/compras/notas_fiscais/nfse_anexos/";
        if (!file_exists($uploaddir)) {
            mkdir($uploaddir, 0777);
        }
        $uploaddir = $_SERVER['DOCUMENT_ROOT'] . "/intranet/compras/notas_fiscais/nfse_anexos/{$id_projeto}/";
        if (!file_exists($uploaddir)) {
            mkdir($uploaddir, 0777);
        }

        $arr_nome = explode('.', basename($file['name']));
        $nome_file = time() . '.' . end($arr_nome);
        $uploadfile = $uploaddir . $nome_file;

        if (move_uploaded_file($file['tmp_name'], $uploadfile)) {
            return array('status' => TRUE, 'name_file' => $nome_file);
        } else {
            return array('status' => FALSE);
        }
    }

    public function excluirPDF($id) {
        $query = "SELECT arquivo_pdf,id_projeto FROM nfse_anexos WHERE id = $id";
        $result = mysql_query($query) or die(mysql_error());
        $x = mysql_fetch_assoc($result);
        return unlink($_SERVER['DOCUMENT_ROOT'] . "/intranet/compras/notas_fiscais/nfse_anexos/{$x['id_projeto']}/{$x['arquivo_pdf']}");
    }

    public function verificarNFSe($num_nota, $cod_servico, $id_prestador) {
//        $query = "SELECT COUNT(id_nfse) AS qtd_nfse FROM nfse WHERE Numero = '$num_nota' AND CodigoVerificacao = '$cod_servico' AND PrestadorServico = '$id_prestador' AND status > 0";
        $query = "SELECT COUNT(id_nfse) AS qtd_nfse FROM nfse WHERE Numero = '$num_nota' AND PrestadorServico = '$id_prestador' AND status > 0";
        $x = mysql_fetch_assoc(mysql_query($query));
        if ($x['qtd_nfse'] > 0) {
            return array('status' => TRUE, 'msg' => "Nota Fiscal de Servi&ccedil;o j&aacute; Cadastrada no sistema.");
        } else {
            return array('status' => FALSE);
        }
    }

//    public function verificarNFSe($nfse = NULL, $prestador = NULL) {
//        if (!empty($prestador) && !empty($nfse)) {
//            $prestador = mascara_string("##.###.###/####-##", $prestador);
//            echo $query = "SELECT id_nfse, id_prestador FROM nfse AS a 
//                INNER JOIN prestadorservico AS b ON a.PrestadorServico = b.id_prestador
//                WHERE a.Numero = '$nfse' AND b.c_cnpj = '$prestador'";
//        } else {
//            return array('status' => FALSE, 'msg' => 'Impossível consultar prestador. Parâmetros insufucientes.');
//        }
//        $teste1 = mysql_fetch_assoc(mysql_query($query));
//        if (!empty($teste1['id_nfse'])) {
//            return array('status' => TRUE, 'id_nfse' => $teste1['id_nfse']);
//        } else {
//            return array('status' => FALSE, 'msg' => "Nota Fiscal de Serviço já Cadastrada no sistema.");
//        }
//    }

    public function consultaNFSe(array $dados, $status = 1) {
        if (!isset($dados['status'])) {
            $dados['status'] = $status;
        }

        $condicoes = $this->prepara_where($dados);

        $prest_prestador = (!empty($id_prestador)) ? " AND a.id_prestador = $id_prestador " : '';
        $prest_id_nfse = (!empty($id_nfse)) ? " AND a.id_nfse = $id_nfse" : '';
        $query = "SELECT a.*, b.*, c.nome AS nome_projeto, d.descricao AS descricao_cod_servico, e.arquivo_pdf, e.id AS id_anexo,e.id_projeto
                FROM nfse AS a 
                INNER JOIN prestadorservico AS b ON (a.PrestadorServico = b.id_prestador)
                INNER JOIN projeto AS c ON a.id_projeto = c.id_projeto
                INNER JOIN nfse_codigo_servico AS d ON a.CodigoTributacaoMunicipio = d.codigo
                LEFT JOIN nfse_anexos AS e ON a.Numero = e.numero_nota AND a.CodigoVerificacao = e.codigo_verificador AND e.arquivo_pdf LIKE '%.pdf'
                $condicoes ORDER BY a.id_projeto,b.c_razao";
        $query . '<br>';
        $result = mysql_query($query) or die('Erro ao consultar NFSe. Detalhes: ' . mysql_error());
        while ($row = mysql_fetch_assoc($result)) {
            $return[$row['id_nfse']] = $row;
        }
        return $return;
    }
//
    public function getNFSeById($id) {
        $query = "SELECT a.*, b.*, c.nome AS nome_projeto, d.descricao AS descricao_cod_servico#, e.arquivo_pdf, e.id AS id_anexo
                FROM nfse AS a 
                INNER JOIN prestadorservico AS b ON (a.PrestadorServico = b.id_prestador)
                LEFT JOIN projeto AS c ON a.id_projeto = c.id_projeto 
                LEFT JOIN nfse_codigo_servico AS d ON a.CodigoTributacaoMunicipio = d.codigo 
                #LEFT JOIN nfse_anexos AS e ON a.Numero = e.numero_nota AND a.CodigoVerificacao = e.codigo_verificador
                WHERE a.id_nfse = $id ORDER BY a.id_projeto,b.c_razao";
//        echo $query . '<br>';
        $result = mysql_query($query) or die('Erro ao consultar NFSe. Detalhes: ' . mysql_error());
        return mysql_fetch_assoc($result);
    }

    public function consultarCadastrados(array $dados) {
        $dados['status'] = 1;
        return $this->consultaNFSe($dados);
    }

    public function consultarServicoOk(array $dados) {
        $dados['status'] = 2;
        return $this->consultaNFSe($dados);
    }

    public function consultarRetencaoOk(array $dados) {
        $dados['status'] = 3;
        return $this->consultaNFSe($dados);
    }

    public function consultarPagamentoOk(array $dados) {
        $dados['status'] = 4;
        return $this->consultaNFSe($dados);
    }

    public function consultarProjetos($cnpj_projeto) {
        $qry = "SELECT id_projeto,id_regiao FROM rhempresa WHERE REPLACE(REPLACE(REPLACE(cnpj,'.',''),'/',''),'-','') = '{$cnpj_projeto}'";
//        echo $qry;
        $result = mysql_query($qry) or die('Erro ao consultar projetos. Detalhes: ' . mysql_error());
        $num_rows = mysql_num_rows($result);
        if ($num_rows > 1) {
            while ($row = mysql_fetch_array($result)) {
                $return = $row;
            }
            return $return;
        } else if ($num_rows == 1) {
            return mysql_fetch_assoc($result);
        }
    }

    // condição com prestador_tipo 4 (prestador de serviço) 
    public function PrestadordeServico($cnpj_prestador, $id_projeto) {
        $qry = "SELECT * FROM prestadorservico WHERE id_projeto = '{$id_projeto}' AND status != 0 AND REPLACE(REPLACE(REPLACE(c_cnpj, '.', ''),'/', ''), '-', '') = '{$cnpj_prestador}'"; // prestador_tipo = 4
        $result = mysql_query($qry) or die('Erro ao consultar prestador. Detalhes: ' . mysql_error());
        $num_rows = mysql_num_rows($result);
        if ($num_rows > 1) {
            while ($row = mysql_fetch_array($result)) {
                $return[$row['id_prestador']] = $row;
            }
            return $return;
        } else if ($num_rows == 1) {
            return mysql_fetch_assoc($result);
        }
    }

    public function consultarePrestador($cnpj_projeto, $prest_cnpj = NULL, $cnpj_prestador = NULL) {
        $prest_cnpj = (strlen($prest_cnpj) != 18 && !empty($prest_cnpj)) ? mascara_string('##.###.###/####-##', $prest_cnpj) : $prest_cnpj;
        $cond_prestador = (!empty($cnpj_prestador)) ? "AND REPLACE(REPLACE(REPLACE(c_cnpj, '.', ''), '/', ''),'-', '') = '{$cnpj_prestador}'" : "";
        $cond_cnpj = (!empty($prest_cnpj)) ? "AND c_cnpj = '$prest_cnpj'" : "";
        $query = "SELECT * FROM prestadorservico WHERE REPLACE(REPLACE(REPLACE(cnpj, '.', ''), '/', ''), '-', '') = '{$cnpj_projeto}' $cond_prestador $cond_cnpj";
        $result = mysql_query($query) or die('Erro ao consultar prestador. Detalhes: ' . mysql_error());
        $num_rows = mysql_num_rows($result);
        if ($num_rows > 1) {
            while ($row = mysql_fetch_array($result)) {
                $return[$row['id_prestador']] = $row;
            }
            return $return;
        } else if ($num_rows == 1) {
            return mysql_fetch_assoc($result);
        }
    }

    public function cancelar_NFSe($id_nfse) {
//        $cancelar = false;
        $this->log($id_nfse, 0);
        $qry = "UPDATE nfse SET status = '0' WHERE id_nfse = $id_nfse";
        return mysql_query($qry);
    }

    public function nfse_xml_to_array($prefeitura) {
        switch ($prefeitura) {
            case 0:
                return $this->modelo_nota_carioca();
            case 1:
                return $this->modelo_sao_goncalo();
            case 2:
                return $this->modelo_itaborai();
        }
    }

    public function selectPrefeituras($attr) {
        if (is_array($attr)) {
            foreach ($attr as $key0 => $value0) {
                $attr2 .= "$key0=\"$value0\" ";
            }
        } else {
            $attr2 = $attr;
        }
        $options = "<option>Selecione...</option>";
        foreach ($this->prefeituras as $key => $pref) {
            $options .= "<option value=\"$key\">$pref</option>";
        }
        return "<select $attr2>$options</select>";
    }

    protected function prepara_where($dados) {
        if (is_array($dados)) {
            $dados = array_filter($dados); //limpa campos vazios
            foreach ($dados as $key => $value) {
                $cond[] = "a.`$key` = '$value'";
            }
            return (!empty($cond)) ? "WHERE " . implode(' AND ', $cond) : '';
        } else {
            return "WHERE " . $dados;
        }
    }

    public function alteraStatus($id, $status) {
        
        $this->log($id, $status);
        $query = "UPDATE nfse SET status = $status WHERE id_nfse = $id";
        return mysql_query($query);
    }

    public function alteraISS($id, $aliquota, $iss) {
        if (empty($aliquota) || empty($iss)) {
            return FALSE;
        }
        $query = "UPDATE nfse SET Aliquota = $aliquota, ValorIss = $iss WHERE id_nfse = $id";
        return mysql_query($query);
    }

    public function exibirNota($id_nfse) {
        $query = "SELECT a.*,
                    b.razao AS projeto_razao, REPLACE(REPLACE(REPLACE(b.cnpj,'.',''),'-',''),'/','') AS projeto_cnpj, b.im AS projeto_im, b.endereco AS projeto_endereco, b.bairro AS projeto_bairro,ValorServicos,
                    c.c_razao AS prestador_razao, c.c_fantasia AS prestador_fantasia, REPLACE(REPLACE(REPLACE(c.c_cnpj,'.',''),'-',''),'/','') AS prestador_cnpj, d.descricao AS descricao_cod_servico,
                    b.endereco endereco_empresa,b.cidade municipio_empresa,b.uf uf_empresa,b.email email_empresa,
                    c.c_endereco endereco_prestador, c.c_uf uf_prestador, c.c_email email_prestador
                    FROM nfse AS a
                    INNER JOIN rhempresa AS b ON a.id_projeto = b.id_projeto
                    INNER JOIN prestadorservico AS c ON a.PrestadorServico = c.id_prestador
                    LEFT JOIN nfse_codigo_servico AS d ON a.CodigoTributacaoMunicipio = d.codigo
                    WHERE id_nfse = $id_nfse;";
        $result = mysql_query($query);
        return mysql_fetch_assoc($result);
    }

    public function exibeNFSe($dados) {
        if (count($dados) > 0)
            $condicoes = $this->prepara_where($dados);

        $query = "SELECT a.*, c.nome AS nome_projeto, b.c_razao,b.id_prestador,b.c_cnpj, e.id_subgrupo_entradasaida, e.id_tipo_entradasaida ,f.arquivo_pdf, f.id_projeto
                FROM nfse AS a 
                INNER JOIN prestadorservico AS b ON (a.PrestadorServico = b.id_prestador) 
                INNER JOIN projeto AS c ON a.id_projeto = c.id_projeto 
                LEFT JOIN nfse_codigo_servico AS d ON a.CodigoTributacaoMunicipio = d.codigo 
                LEFT JOIN nfse_codigo_servico_assoc AS e ON (d.id = e.id_codigo_servico AND b.id_prestador = e.id_prestador) 
                LEFT JOIN nfse_anexos AS f ON a.Numero = f.numero_nota AND a.CodigoVerificacao = f.codigo_verificador AND f.arquivo_pdf LIKE '%.pdf'
                $condicoes ORDER BY b.c_razao";
//        echo $query . '<br>';
        $result = mysql_query($query) or die('Erro ao consultar NFSe. Detalhes: ' . mysql_error());
        while ($row = mysql_fetch_assoc($result)) {
            $return[$row['id_nfse']] = $row;
        }
        return $return;
    }

    public function atualizarRetencoes($id_nfse, $array_atualiza) {
        $this->grava_historico($id_nfse, $array_atualiza);
        foreach ($array_atualiza as $key => $value) {
            $dados[] = "$key = '$value'";
        }
        $val = implode(',', $dados);
        $query = "UPDATE nfse SET $val WHERE id_nfse = $id_nfse";
        return mysql_query($query) or die(mysql_error());
    }

    protected function grava_historico($id_nfse, $array_atualiza) {

        $query = "SELECT ValorServicos, ValorCofins, ValorCsll, ValorInss, "
                . "ValorIr, ValorPis, ValorDeducao, ValorDesconto, BaseCalculo, "
                . "ValorIss, Aliquota, Credito, ValorPisCofinsCsll "
                . "FROM nfse WHERE id_nfse = $id_nfse";

        $result = mysql_query($query);
        $arr_de = mysql_fetch_assoc($result);

        foreach ($arr_de as $key => $value) {
            $dados[$key . '_de'] = "'$value'";
        }

        foreach ($array_atualiza as $key => $value) {
            $dados[$key . '_para'] = "'$value'";
        }
        $col = implode(',', array_keys($dados));
        $val = implode(',', array_values($dados));
        $query2 = "INSERT INTO nfse_historico ($col,id_nfse,data_proc) VALUES ($val,$id_nfse,NOW())";
        return mysql_query($query2) or die($query2 . '<br>' . mysql_error());
    }

    public function getQtdLiberadaByRegiao($id_regiao) {
        $query = "SELECT count(id_nfse) as qtd FROM nfse WHERE status = 3 AND id_regiao = $id_regiao;";
        $ret = mysql_query($query) or die("Erro na query: $query<br>" . mysql_error());
        $qtd = mysql_fetch_assoc($ret);
        return $qtd['qtd'];
    }

    public function getAnexos($id_nfse) {
        $query = "SELECT *,RIGHT(arquivo_pdf,3) AS extencao FROM nfse_anexos WHERE id_nfse = $id_nfse";
        $result = mysql_query($query);
        while ($row = mysql_fetch_array($result)) {
            $anexos[] = $row;
        }
        return $anexos;
    }

    public function getQtdLiberada() {
        $query = "SELECT A.id_projeto, B.nome, count(id_nfse) as qtd 
        FROM nfse A 
        LEFT JOIN projeto B ON (A.id_projeto = B.id_projeto)
        WHERE A.status = 3 GROUP BY A.id_projeto ;";
        $ret = mysql_query($query) or die("Erro na query: $query<br>" . mysql_error());
        while($row = mysql_fetch_assoc($ret)){
            $array[$row['id_projeto']] = $row;
        }
        return $array;
    }

    public function alterStatus($id_nfse, $status) {
        $query = "UPDATE nfse SET status = $status WHERE id_nfse = $id_nfse;";
        
        $this->log($id_nfse, $status);
        return mysql_query($query) or die($query . ' - ' . mysql_error());
    }

    private function log($id_nfse, $status) {
        $query = "INSERT INTO nfse_log (id_nfse, status, id_user, data_cad) VALUES ('{$id_nfse}', '{$status}', '{$_COOKIE['logado']}', NOW());";
        return mysql_query($query) or die($query . ' - ' . mysql_error());
    }

    public function getIdSaidasByIdNfse($id_nfse) {
        $query = "SELECT sai.*
                    FROM nfse_saidas ns 
                    INNER JOIN saida sai ON ns.id_saida = sai.id_saida
                    WHERE status > 0 AND id_nfse = $id_nfse";
        $result = mysql_query($query) or die('erro ao consultar status da saida  - ' . $query . ' - ' . mysql_error());
        while ($row = mysql_fetch_assoc($result)) {
            $retorno[] = $row;
        }
        return $retorno;
    }

    /*
     * =========================================================================
     * Modelos de importação dependendo da prefeitura
     * =========================================================================
     */

    protected function modelo_nota_carioca() {
        $prest_cnpj = (empty($this->PrestadorServico->IdentificacaoPrestador->Cnpj)) ? $this->PrestadorServico->IdentificacaoPrestador->Cpf : $this->PrestadorServico->IdentificacaoPrestador->Cnpj;
        $Tomador = (empty($this->TomadorServico->IdentificacaoTomador->CpfCnpj->Cnpj)) ? $this->TomadorServico->IdentificacaoTomador->CpfCnpj->Cpf : $this->TomadorServico->IdentificacaoTomador->CpfCnpj->Cnpj;

        $array = array(
            'Numero' => $this->InfNfse->Numero,
            'CodigoVerificacao' => $this->InfNfse->CodigoVerificacao,
            'DataEmissao' => $this->InfNfse->DataEmissao,
            'NaturezaOperacao' => $this->InfNfse->NaturezaOperacao,
            'OptanteSimplesNacional' => $this->InfNfse->OptanteSimplesNacional,
            'IncentivadorCultural' => $this->InfNfse->IncentivadorCultural,
            'Competencia' => $this->InfNfse->Competencia,
            'DataEmissaoRps' => $this->IdentificacaoRps->Numero,
            'Numero_rps' => $this->IdentificacaoRps->Numero,
            'Serie' => $this->IdentificacaoRps->Serie,
            'Tipo' => $this->IdentificacaoRps->Tipo,
            'ValorServicos' => $this->Servico->Valores->ValorServicos,
            'ValorDeducoes' => $this->Servico->Valores->ValorDeducoes,
            'ValorPis' => $this->Servico->Valores->ValorPis,
            'ValorCofins' => $this->Servico->Valores->ValorCofins,
            'ValorInss' => $this->Servico->Valores->ValorInss,
            'ValorIr' => $this->Servico->Valores->ValorIr,
            'ValorCsll' => $this->Servico->Valores->ValorCsll,
            'IssRetido' => $this->Servico->Valores->IssRetido,
            'ValorIss' => $this->Servico->Valores->ValorIss,
            'ValorIssRetido' => $this->Servico->Valores->ValorIssRetido,
            'OutrasRetencoes' => $this->Servico->Valores->OutrasRetencoes,
            'BaseCalculo' => $this->Servico->Valores->BaseCalculo,
            'Aliquota' => $this->Servico->Valores->Aliquota,
            'ValorLiquidoNfse' => $this->Servico->Valores->ValorLiquidoNfse,
            'DescontoIncondicionado' => $this->Servico->Valores->DescontoIncondicionado,
            'DescontoCondicionado' => $this->Servico->Valores->DescontoCondicionado,
            'ItemListaServico' => $this->Servico->ItemListaServico,
            'CodigoCnae' => $this->Servico->CodigoCnae,
            'CodigoTributacaoMunicipio' => $this->Servico->CodigoTributacaoMunicipio,
            'Discriminacao' => $this->Servico->Discriminacao,
            'CodigoMunicipio' => $this->Servico->CodigoMunicipio,
            'cnpj' => $this->PrestadorServico->IdentificacaoPrestador->Cnpj,
            'inscricao_mun' => $this->PrestadorServico->IdentificacaoPrestador->InscricaoMunicipal,
            'CnpjTS' => $this->TomadorServico->IdentificacaoTomador->CpfCnpj->Cnpj,
            'Cnpj' => $this->TomadorServico->IdentificacaoTomador->CpfCnpj->Cnpj,
            'InscricaoMunicipal' => $this->TomadorServico->IdentificacaoTomador->InscricaoMunicipal,
            'RazaoSocial' => $this->TomadorServico->RazaoSocial,
            'EnderecoTomador' => $this->TomadorServico->Endereco->Endereco
        );
        return $array;
    }

    protected function modelo_sao_goncalo() {
        
    }

}
