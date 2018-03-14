<?php

class GlobalClass {
    public static function getContratacao($tipo = null) {
        $tipo = (int) $tipo;
        $tipos = array("1" => "Autônomo", "2" => "CLT", "3" => "Cooperado", "4" => "Autonomo/PJ");
        if (!empty($tipo))
            return $tipos[$tipo];
        else
            return $tipos;
    }

    public static function carregaRegioes($master, $default = array("-1" => "-- Selecione --")) {
        $id_user = $_COOKIE['logado'];
        $qr = "SELECT B.id_regiao,B.regiao FROM funcionario_regiao_assoc AS A 
                                    LEFT JOIN regioes AS B ON (A.id_regiao = B.id_regiao)
                                    WHERE A.id_funcionario = {$id_user} AND A.id_master = {$master} ORDER BY regiao";
        $qrpermreg = mysql_query($qr);
        //echo $qr."<br>";
        $regioes = $default;
        while ($row_regs = mysql_fetch_assoc($qrpermreg)) {
            $regioes[$row_regs['id_regiao']] = $row_regs['id_regiao'] . " - " . $row_regs['regiao'];
        }

        return $regioes;
    }

    /**
     * 
     * @param string $master Id do master do usuário
     * @return string
     */
    public static function carregaProjetos($master, $default = array("-1" => "-- Selecione --"), $presta_contas = true) {
	$id_user = $_COOKIE['logado'];
	$ids_regs = array();
	$whereADM = "";

	$qrpermreg = mysql_query("SELECT * FROM funcionario_regiao_assoc WHERE id_funcionario = {$id_user} AND id_master = {$master}");
	while ($row_regs = mysql_fetch_assoc($qrpermreg)) {
		$ids_regs[] = $row_regs['id_regiao'];
	}
	
	if($presta_contas){
            $and_prest = "AND prestacontas = 1";
	}else{
            $and_prest = "";
	}

	$qr_projeto = mysql_query("SELECT * FROM projeto WHERE id_master = '{$master}' AND id_regiao IN (" . implode(",", $ids_regs) . ") AND status_reg = 1 {$and_prest} {$whereADM} ORDER BY nome");
	$projetos = $default;
	while ($row_projeto = mysql_fetch_assoc($qr_projeto)) {
		$projetos[$row_projeto['id_projeto']] = $row_projeto['id_projeto'] . " - " . $row_projeto['nome'];
	}
	return $projetos;
    }
    
    public static function carregaProjetosByRegiao($regiao, $default = array("-1" => "-- Selecione --")) {
        
        $qr_projeto = mysql_query("SELECT * FROM projeto WHERE id_regiao = {$regiao} AND status_reg = 1 ORDER BY nome");
        $projetos = $default;
        while ($row_projeto = mysql_fetch_assoc($qr_projeto)) {
            $projetos[$row_projeto['id_projeto']] = $row_projeto['id_projeto'] . " - " . $row_projeto['nome'];
        }
        return $projetos;
    }
    
    public static function carregaProjetosByMaster($master,$default = array("-1" => "-- Selecione --")) {
        
        $qr_projeto = mysql_query("SELECT * FROM projeto WHERE id_master = '{$master}' AND status_reg = 1 ORDER BY nome");
        $projetos = $default;
        while ($row_projeto = mysql_fetch_assoc($qr_projeto)) {
            $projetos[$row_projeto['id_projeto']] = $row_projeto['id_projeto'] . " - " . $row_projeto['nome'];
        }
        return $projetos;
    }
    
    public static function carregaSindicatos($default = array("-1" => "-- Selecione --")) {
        
        $qr_sindicato = mysql_query("SELECT * FROM rhsindicato WHERE status = 1 ORDER BY nome");
        $sindicatos = $default;
        while ($row_sindicato = mysql_fetch_assoc($qr_sindicato)) {
            $sindicatos[$row_sindicato['id_sindicato']] = $row_sindicato['id_sindicato'] . " - " . $row_sindicato['nome'];
        }
        return $sindicatos;
    }
    
    public static function carregaSindicatosByRegiao($regiao, $default = array("-1" => "-- Selecione --")) {
        
        $qr_sindicato = mysql_query("SELECT * FROM rhsindicato WHERE id_regiao = {$regiao} AND status = 1 ORDER BY nome");
        $sindicatos = $default;
        while ($row_sindicato = mysql_fetch_assoc($qr_sindicato)) {
            $sindicatos[$row_sindicato['id_sindicato']] = $row_sindicato['id_sindicato'] . " - " . $row_sindicato['nome'];
        }
        return $sindicatos;
    }
    
    public static function carregaJornadas($default = array("-1" => "-- Selecione --")) {

        $qr_jornada = mysql_query("SELECT * FROM tipo_jornada ORDER BY id_jornada ASC");
        $jornadas = $default;
        while ($row_jornada = mysql_fetch_assoc($qr_jornada)) {
            $jornadas[$row_jornada['id_jornada']] = $row_jornada['id_jornada'] . " - " . $row_jornada['nome'];
        }
        return $jornadas;
    }
    
    public static function carregaPagamentos($default = array("-1" => "-- Selecione --")) {

        $qr_pagamento = mysql_query("SELECT * FROM undSalFixo ORDER BY id_pagamento ASC");
        $pagamentos = $default;
        while ($row_pagamento = mysql_fetch_assoc($qr_pagamento)) {
            $pagamentos[$row_pagamento['id_pagamento']] = $row_pagamento['id_pagamento'] . " - " . $row_pagamento['nome'];
        }
        return $pagamentos;
    }
    
    public static function carregaTempoParcial($default = array("-1" => "-- Selecione --")) {

        $qr_tmpParc = mysql_query("SELECT * FROM tempo_parcial ORDER BY id_tmpParc ASC");
        $tmpParciais = $default;
        while ($row_tmpParc = mysql_fetch_assoc($qr_tmpParc)) {
            $tmpParciais[$row_tmpParc['id_tmpParc']] = $row_tmpParc['id_tmpParc'] . " - " . $row_tmpParc['nome'];
        }
        return $tmpParciais;
    }
    
    public static function carregaPrestadorByProjeto($projeto, $default = array("-1" => "-- Selecione --"),$tipo = false) {
        
        $qry = mysql_query("
        SELECT A.*, C.nome AS nomeTipo
        FROM prestadorservico A 
        LEFT JOIN nfse_codigo_servico_assoc B ON (B.id_prestador = A.id_prestador)
        LEFT JOIN entradaesaida C ON (C.id_entradasaida = B.id_tipo_entradasaida)
        WHERE A.id_projeto = {$projeto} AND A.status = '1' AND A.encerrado_em >= CURRENT_DATE() 
        ORDER BY A.c_razao") or die(mysql_error());
        $prestador = $default;
        while ($row_prestador = mysql_fetch_assoc($qry)) {
//            $nome1 = (!empty($row_prestador['c_fantasia'])) ? $row_prestador['c_fantasia'] : $row_prestador['c_responsavel'];
            $nome1 = (!empty($row_prestador['c_razao'])) ? $row_prestador['c_razao'] : $row_prestador['c_responsavel'];
            $nome2 = (!empty($row_prestador['c_cnpj'])) ? $row_prestador['c_cnpj'] : $row_prestador['c_cpf'];
            $nomeTipo = ($tipo) ? " ({$row_prestador['nomeTipo']})" : null;
//            $prestador[$row_prestador['id_prestador']] = str_pad($row_prestador['id_prestador'],4,"0",STR_PAD_LEFT) . " - " . $row_prestador['numero'] . " - " . $nome1 . " - " . $nome2 . $nomeTipo;
            $prestador[$row_prestador['id_prestador']] = str_pad($row_prestador['id_prestador'],4,"0",STR_PAD_LEFT) . " - " . $nome1 . " - " . $nome2 . $nomeTipo;
        }
        return $prestador;
    }

    public static function carregaFornecedor($default = array("-1" => "-- Selecione --")) {
        
        $qry = mysql_query("SELECT * FROM cad_fornecedor WHERE status = '1' ORDER BY razao");
        $prestador = $default;
        while ($row_prestador = mysql_fetch_assoc($qry)) {
            $prestador[$row_prestador['id_fornecedor']] = str_pad($row_prestador['id_fornecedor'], 4, "0", STR_PAD_LEFT) . " - " . $row_prestador['razao'] . " - " . $row_prestador['cnpj'];
        }
        return $prestador;
    }
    
    public static function carregaFornecedorByProjeto($projeto,$default = array("-1" => "-- Selecione --")) {
        
        $qry = mysql_query("SELECT * FROM prestadorservico WHERE id_projeto = {$projeto} AND status = '1' AND encerrado_em >= CURRENT_DATE() ORDER BY c_fantasia");
        $prestador = $default;
        while ($row_prestador = mysql_fetch_assoc($qry)) {
            $prestador[$row_prestador['id_prestador']] = str_pad($row_prestador['id_prestador'],4,"0",STR_PAD_LEFT) . " - " . $row_prestador['numero'] . " - " . $row_prestador['c_fantasia'] . " - " . $row_prestador['c_cnpj'];
        }
        return $prestador;
    }
    
    public static function carregaPrestadorInativoByProjeto($projeto, $default = array("-1" => "-- Selecione --")) {
        
        $qry = mysql_query("SELECT * FROM prestadorservico WHERE id_projeto = {$projeto} AND status = '1' AND encerrado_em < CURRENT_DATE() ORDER BY c_fantasia");
        $prestador = $default;
        while ($row_prestador = mysql_fetch_assoc($qry)) {
//            $prestador[$row_prestador['id_prestador']] = str_pad($row_prestador['id_prestador'],4,"0",STR_PAD_LEFT) . " - " . $row_prestador['numero'] . " - " . $row_prestador['c_fantasia'] . " - " . $row_prestador['c_cnpj'];
            $prestador[$row_prestador['id_prestador']] = str_pad($row_prestador['id_prestador'],4,"0",STR_PAD_LEFT) . " - " . $row_prestador['c_razao'] . " - " . $row_prestador['c_cnpj'];
        }
        return $prestador;
    }
    
    public static function carregaPrestadorOutrosByProjeto($projeto, $default = array("-1" => "-- Selecione --")) {
        
        $qry = mysql_query("SELECT * FROM prestadorservico WHERE id_projeto = {$projeto} AND status = '1' AND encerrado_em IS NULL ORDER BY c_fantasia");
        $prestador = $default;
        while ($row_prestador = mysql_fetch_assoc($qry)) {
//            $prestador[$row_prestador['id_prestador']] = str_pad($row_prestador['id_prestador'],4,"0",STR_PAD_LEFT) . " - " . $row_prestador['numero'] . " - " . $row_prestador['c_fantasia'] . " - " . $row_prestador['c_cnpj'];
            $prestador[$row_prestador['id_prestador']] = str_pad($row_prestador['id_prestador'],4,"0",STR_PAD_LEFT) . " - " . $row_prestador['c_razao'] . " - " . $row_prestador['c_cnpj'];
        }
        return $prestador;
    }
    
    public static function carregaFornecedorByRegiao($regiao, $default = array("-1" => "-- Selecione --")) {
        
        $qry = mysql_query("SELECT * FROM fornecedores WHERE id_regiao = {$regiao} AND status = '1' ORDER BY id_fornecedor");
        $fornecedor = $default;
        while ($row_fornecedor = mysql_fetch_assoc($qry)) {
            $fornecedor[$row_fornecedor['id_fornecedor']] = str_pad($row_fornecedor['id_fornecedor'], 2, "0", STR_PAD_LEFT) . " - " . $row_fornecedor['nome'];
        }        
        return $fornecedor;
    }
    
    public static function carregaNomes($default = array("-1" => "-- Selecione --")) {
        
        $qry = mysql_query("SELECT * FROM entradaesaida_nomes WHERE nome != '' GROUP BY nome ORDER BY nome");
        $nome = $default;
        while ($row_nome = mysql_fetch_assoc($qry)) {
            $nome[$row_nome['id_nome']] = $row_nome['nome'];
        }        
        return $nome;
    }
    
    public static function carregaNomesByTipo($tipo, $default = array("-1" => "-- Selecione --")) {
        
        $qry = mysql_query("SELECT * FROM entradaesaida_nomes WHERE id_entradasaida IN ({$tipo},0) AND nome != '' ORDER BY nome");
        $nome = $default;
        while ($row_nome = mysql_fetch_assoc($qry)) {
            $nome[$row_nome['id_nome']] = $row_nome['nome'];
        }        
        return $nome;
    }
    
    public static function carregaBancosByRegiao($regiao, $default = array("-1" => "-- Selecione --"), $agencia_conta = null) {
        
        $qr = mysql_query("SELECT * FROM bancos WHERE id_regiao = {$regiao} AND status_reg = 1 ORDER BY id_banco");
        $bancos = $default;
        while ($row_bancos = mysql_fetch_assoc($qr)) {
            if ($agencia_conta == null) {
                $bancos[$row_bancos['id_banco']] = $row_bancos['id_banco'] . " - " . $row_bancos['nome'];
            } else {
                $bancos[$row_bancos['id_banco']] = $row_bancos['id_banco'] . " - " . $row_bancos['nome'] . " - " . $row_bancos['agencia'] . " / " . $row_bancos['conta'];
            }
        }
        return $bancos;
    }
    
    public static function carregaBancosByMaster($master,$default = array("-1" => "-- Selecione --"),$agencia_conta = null) {
        
        $qr = mysql_query("SELECT * FROM bancos WHERE id_regiao IN (SELECT id_regiao FROM regioes WHERE id_master = '{$master}' AND status = 1 AND status_reg = 1) AND status_reg = 1 ORDER BY id_banco");
        $bancos = $default;
        while ($row_bancos = mysql_fetch_assoc($qr)) {
            if($agencia_conta == null){
                $bancos[$row_bancos['id_banco']] = $row_bancos['id_banco'] . " - " . $row_bancos['nome'];
            }else{
                $bancos[$row_bancos['id_banco']] = $row_bancos['id_banco'] . " - " . $row_bancos['nome'] . " - " . $row_bancos['agencia'] . " / " . $row_bancos['conta'];
            }
        }
        return $bancos;
    }
    
    public static function carregaParceirosByRegiao($regiao, $default = array("" => "-- Selecione --")) {
        if ($regiao == 37) {
            $qr_parceiros = mysql_query("SELECT parceiro_id, parceiro_nome FROM parceiros");
        } else {
            $qr_parceiros = mysql_query("SELECT parceiro_id, parceiro_nome FROM parceiros WHERE id_regiao = '{$regiao}'");
  	}
        
        $parceiros = $default;
        while ($row_parceiro = mysql_fetch_assoc($qr_parceiros)) {
            $parceiros[$row_parceiro['parceiro_id']] = $row_parceiro['parceiro_nome'];
        }
        return $parceiros;
    }
    
    /**
     * 
     * @param type $arquivo
     * @param type $pasta
     * @param type $tipos
     * @param type $nome
     * @return string
     */
    public static function uploadFile($arquivo, $pasta, $tipos, $nome = null) {
        
        if (isset($arquivo)) {
            $infos = explode(".", $arquivo["name"]);

            if (!$nome) {
                for ($i = 0; $i < count($infos) - 1; $i++) {
                    $nomeOriginal = $nomeOriginal . $infos[$i] . ".";
                }
            } else {
                $nomeOriginal = $nome . ".";
            }

            $tipoArquivo = end($infos);

            $tipoPermitido = false;
            foreach ($tipos as $tipo) {
                if (strtolower($tipoArquivo) == strtolower($tipo)) {
                    $tipoPermitido = true;
                }
            }
            if (!$tipoPermitido) {
                $retorno["erro"] = "Tipo nao permitido";
            } else {
                if (move_uploaded_file($arquivo['tmp_name'], $pasta . $nomeOriginal . $tipoArquivo)) {
                    $retorno["caminho"] = $pasta . $nomeOriginal . $tipoArquivo;
                } else {
                    $retorno["erro"] = "Erro ao fazer upload";
                }
            }
        } else {
            $retorno["erro"] = "Arquivo nao setado";
        }
        return $retorno;
    }
    
    public static function getDiasUteis($data_inicial, $data_final, $retornar_apenas_contagem = TRUE) {
        $data_inicial = implode('-', array_reverse(explode('/', $data_inicial)));
        $data_final = implode('-', array_reverse(explode('/', $data_final)));
        // Verifica os Feriados Federais
        $sql_federal = "SELECT *, date_format(data, '%d/%m/%Y') AS data_federalF FROM rhferiados WHERE data >='$data_inicial' AND data <= '$data_final' AND tipo = 'Federal'";
        echo "<br>" . $sql_federal . "<br>";
        $qr_feriados_federal = mysql_query($sql_federal);
        $feriados_federal = array();
        while ($row_feriado_regional = mysql_fetch_array($qr_feriados_federal)) {
            $feriados_federal[] = '"' . $row_feriado_regional['data'] . '"';
        }   
        $result = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '{$_COOKIE['logado']}'");
        $usuario = mysql_fetch_assoc($result);
        // Verifica os Feriados Regionais
        $sql_regional = "SELECT *, date_format(data, '%d/%m/%Y') AS data_regionalF FROM rhferiados WHERE data >= '$data_inicial' AND data <= '$data_final' AND id_regiao = '$usuario[id_regiao]' AND tipo = 'Regional'";
        echo $sql_regional . "<br>";
        $qr_feriados_regional = mysql_query($sql_regional);
        $feriados_regional = array();
        while ($row_feriado_regional = mysql_fetch_array($qr_feriados_regional)) {
            $feriados_regional[] = '"' . $row_feriado_regional['data'] . '"';
        }
        $array_feriados = array_merge($feriados_federal, $feriados_regional);
        $str_feriado = implode(', ', $array_feriados);
        // Retira os Finais de Semana e Feriados
        $sql_dias = "SELECT * FROM ano WHERE data >= '$data_inicial' AND data <= '$data_final' AND fds != 1 AND data NOT IN ($str_feriado)";
        echo $sql_dias;
        $qr_dias = mysql_query($sql_dias);
        if ($retornar_apenas_contagem) {
            return mysql_num_rows($qr_dias);
        } else {
            $retorno = array();
            while ($row = mysql_fetch_array($qr_dias)) {
                $retorno[] = $row['data'];
            }
            return $retorno;
        }
    }
    
    public static function getResposta($tipo, $mensagem) {
        $div = "";
        
        if ($tipo != "" && $mensagem != "") {
            $div .= "<div class='alert alert-dismissable alert-{$tipo} msg_cadsuporte'>";
            $div .= "<button type='button' class='close' data-dismiss='alert'>×</button>";
            $div .= "<p>{$mensagem}</p>";
            $div .= "</div>";
        }
        
        session_destroy();
        
        return $div;
    }
    
    public static function regioesMaster($default = null, $atributos) {
        $select = "";
        $select .= "<select {$atributos}>";
        
        if ($default == null) {
            $select .= "<option value='-1'>Selecione a Região</option>";
        } else {
            $select .= $default;
        }
        
        $sql = mysql_query("SELECT A.id_regiao, A.regiao, B.nome
                    FROM regioes AS A
                    LEFT JOIN master AS B ON(A.id_master = B.id_master)
                    WHERE A.status = 1 AND B.status = 1
                    ORDER BY B.id_master, A.id_regiao") or die(mysql_error());
        
        while ($rw_regioes_prestador = mysql_fetch_array($sql)) {
            if ($regiao_prest_forn == $rw_regioes_prestador[0]) {
                $selected = "selected=\"selected\"";
            } else {
                $selected = "";
            }

            if ($repeat != $rw_regioes_prestador[2]) {
               $select .= '<optgroup label="' . $rw_regioes_prestador[2] . '">';
            }

            $repeat = $rw_regioes_prestador[2];
            
            $select .= '<option ' . $selected . ' value="' . $rw_regioes_prestador[0] . '" >' . $rw_regioes_prestador[0] . ' - ' . $rw_regioes_prestador[1] . '</option>';
            
            if ($repeat != $rw_regioes_prestador[2] && !empty($repeat)) {
                $select .= '</optgroup>';
            }
            
            $repeat = $rw_regioes_prestador[2];
        }
        
        $select .= "</select>";
        
        return $select;
    }
    
}
?>