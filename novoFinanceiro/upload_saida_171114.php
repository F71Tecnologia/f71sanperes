<?php

include("include/restricoes.php");
include("../conn.php");
include("../funcoes.php");
include("../wfunction.php");
include("../classes_permissoes/regioes.class.php");
session_start();

function organiza_files($arquivos) {

    ///acertando os array com as informaÁıes dos arquivos   
    foreach ($arquivos as $chave => $valores) {
        foreach ($valores as $i => $anexo) {
            $array_anexo[$i][$chave] = $anexo;
        }
    }

    return $array_anexo;
}

$REGIOES = new Regioes;

$id_user = $_COOKIE['logado'];
$id_saida = $_REQUEST['id'];
$enc = $_REQUEST['enc'];

if (isset($_POST['cadastrar']) or isset($_POST['atualizar'])) {

    $id_user = $_REQUEST['logado'];

    $banco = $_REQUEST['banco'];
    $qr_banco = mysql_query("SELECT * FROM bancos WHERE id_banco = '$banco'");
    $row_banco = mysql_fetch_assoc($qr_banco);

    $regiao = $row_banco['id_regiao'];
    $projeto = $row_banco['id_projeto'];
    $nome = $_REQUEST['nome'];
    $especifica = $_REQUEST['descricao'];
    $tipo = $_REQUEST['tipo'];
    $adicional = str_replace('.', '', $_REQUEST['adicional']);
    $adicional = $adicional;
    $valor = str_replace('.', '', $_REQUEST['valor_liquido']);
    $data_vencimento = ConverteData($_REQUEST['data_vencimento']);
    $data_proc = date('Y-m-d H:i:s');
    $data_proc2 = date('Y-m-d');
    $valor_bruto = str_replace(',', '.', str_replace('.', '', $_REQUEST['valor_bruto']));
    $grupo = $_REQUEST['grupo'];
    $subgrupo = $_REQUEST['subgrupo'];
    $id_referencia = $_REQUEST['referencia'];
    $id_bens = ($id_referencia == 1) ? '' : $_REQUEST['bens'];
    $id_tipo_pag_saida = $_REQUEST['tipo_pagamento'];
    $nosso_numero = $_REQUEST['nosso_numero'];
    $codigo_barra = $_REQUEST['codigo_barra'];
    $n_documento = $_REQUEST['n_documento'];
    $link_nfe = $_REQUEST['link_nfe'];
    $tipo_boleto = $_REQUEST['tipo_boleto'];
    $campos_cod_barra_consumo = implode('', $_REQUEST['codigo_barra_consumo']);
    $campos_cod_barra_gerais = implode('', $_REQUEST['campo_codigo_gerais']);
    $estorno = $_REQUEST['estorno'];
    $descricao_estorno = trim($_REQUEST['descricao_estorno']);
    $valor_estorno_parcial = str_replace(",", ".", str_replace('.', '', $_POST['valor_estorno_parcial'])); //o outro campo valor, esta como varchar, esse esta como decimal, precisa remover a virgula e por 1 ponto
    $mes_competencia = $_REQUEST['mes_competencia'];
    $ano_competencia = $_REQUEST['ano_competencia'];
    $tipo_nf = $_REQUEST['tipo_nf'];
    $dt_emissao_nf = (!empty($_REQUEST['dt_emissao_nf'])) ? ConverteData($_REQUEST['dt_emissao_nf']) : '';
    
    ///SUBGRUPO
    switch ($grupo) {
        case 1:
        case 2:
        case 3:
        case 4: $subgrupo = 0;
            break;
    }

    //////////////////////////////////
    /////// TIPO DE PAGAMENTO ////////
    /////////////////////////////////
    /* CondiÁ„o para qunado for editar, caso seja selecionado um outro tipo de pagamento que n„o seja boleto,
     * limpar os campos referente a boletos na tabela
     * 
     */
    switch ($id_tipo_pag_saida) {

        case 1:
            if ($tipo_boleto == 1) {
                $campos_cod_barra_gerais = '';
                $nosso_numero = '';
            } else {
                $campos_cod_barra_consumo = '';
            }
            break;
        default: $nosso_numero = '';
            $campos_cod_barra_consumo = '';
            $campos_cod_barra_gerais = '';
            $tipo_boleto = '';
    }

    /////////////////////////////////
    /// PRESTADOR E FORNECEDOR///////
    //////////////////////////////
    $tipo_empresa = $_REQUEST['tipo_empresa'];
    $regiao_interno = $_REQUEST['regiao-prestador'];
    $projeto_interno = $_REQUEST['Projeto-prestador'];

    if (!empty($regiao_interno) and !empty($projeto_interno)) {

        $qr_regiao = mysql_query("SELECT regiao FROM regioes WHERE id_regiao = '$regiao_interno'");
        $row_regiao = mysql_fetch_assoc($qr_regiao);

        $qr_projeto = mysql_query("SELECT nome FROM projeto WHERE id_projeto = '$projeto_interno'");
        $row_projeto = mysql_fetch_assoc($qr_projeto);

        $dados_reg_pro = ' - Regiao: ' . ' - Regiao: ' . $row_regiao['regiao'] . ', Projeto - ' . $row_projeto['nome'];
    }

    if ($_REQUEST['tipo_empresa'] == 1) {
        $id_prestador = $_REQUEST['prestador'];
        $qr_prestador = mysql_query("SELECT c_razao, c_cnpj,  B.nome as nome_projeto, C.regiao as nome_regiao  
                                                FROM prestadorservico as A
                                                INNER JOIN projeto AS B
                                                ON B.id_projeto = A.id_projeto
                                                INNER JOIN regioes as C
                                                ON C.id_regiao = A.id_regiao
                                                WHERE id_prestador = '$id_prestador'");

        $row_prestador = mysql_fetch_assoc($qr_prestador);
        $nome_prestador = $row_prestador['c_razao'];
        $cnpj_prestador = $row_prestador['c_cnpj'];
        $nome_prestador = $row_prestador['c_razao'] . ' - Regiao: ' . $row_prestador['nome_regiao'] . ', Projeto: ' . $row_prestador['nome_projeto'];
    } else{
        $id_prestador = $_REQUEST['prestador_inativo'];
        $qr_prestador = mysql_query("SELECT c_razao, c_cnpj,  B.nome as nome_projeto, C.regiao as nome_regiao  
                                                FROM prestadorservico as A
                                                INNER JOIN projeto AS B
                                                ON B.id_projeto = A.id_projeto
                                                INNER JOIN regioes as C
                                                ON C.id_regiao = A.id_regiao
                                                WHERE id_prestador = '$id_prestador'");

        $row_prestador = mysql_fetch_assoc($qr_prestador);
        $nome_prestador = $row_prestador['c_razao'];
        $cnpj_prestador = $row_prestador['c_cnpj'];
        $nome_prestador = $row_prestador['c_razao'] . ' - Regiao: ' . $row_prestador['nome_regiao'] . ', Projeto: ' . $row_prestador['nome_projeto'];
    }
//    else {
//        $id_fornecedor = $_REQUEST['fornecedor'];
//        $qr_fornecedor = mysql_query("SELECT A.nome, A.cnpj,  B.nome as nome_projeto, C.regiao as nome_regiao  
//                                                FROM fornecedores as A
//                                                INNER JOIN projeto AS B
//                                                ON B.id_projeto = A.id_projeto
//                                                INNER JOIN regioes as C
//                                                ON C.id_regiao = A.id_regiao
//                                                WHERE id_fornecedor = '$id_fornecedor'");
//        $row_fornecedor = mysql_fetch_assoc($qr_fornecedor);
//        $nome_fornecedor = $row_fornecedor['nome'];
//        $cnpj_fornecedor = $row_fornecedor['cnpj'];
//        $nome_fornecedor = $row_fornecedor['nome'] . ' - Regiao: ' . $row_fornecedor['nome_regiao'] . ', Projeto: ' . $row_fornecedor['nome_projeto'];
//    }

    if (!empty($id_prestador) and ($tipo == 132 or $tipo == 32 or $grupo == 30 or $grupo == 80 or $grupo == 20)) {
        $nome = $nome_prestador;
    } elseif (!empty($id_fornecedor)) {
        $nome = $nome_fornecedor;
    }

    if (($nome != '' and is_numeric($nome)) or $tipo == 170) {
        $query_nomes = mysql_query("SELECT id_nome,nome FROM entradaesaida_nomes WHERE id_nome = '$nome'");
        $row_nomes = mysql_fetch_assoc($query_nomes);
        $nome = $row_nomes['nome'] . $dados_reg_pro;
        $id_nome = $row_nomes['id_nome'];
    }
    
    
} 
//////FIM VALIDA«√O CADASTRAR E EDITAR
//////////////////////////////////////////////////////
///// BLOCO DE INSER«√O DA SAÕDA NO BANCO DE DADOS ///
///////////////////////////////////////////////////////
if (isset($_POST['cadastrar'])) {

    $campos_sql['id_regiao'] = $regiao;
    $campos_sql['id_banco'] = $banco;
    $campos_sql['id_user'] = $id_user;
    $campos_sql['nome'] = $nome;
    $campos_sql['id_nome'] = $id_nome;
    $campos_sql['especifica'] = $especifica;
    $campos_sql['tipo'] = $tipo;
    $campos_sql['adicional'] = $adicional;
    $campos_sql['valor'] = $valor;
    $campos_sql['data_proc'] = $data_proc;
    $campos_sql['data_vencimento'] = $data_vencimento;
    $campos_sql['status'] = 1;
    $campos_sql['comprovante'] = 2;
    $campos_sql['nosso_numero'] = $nosso_numero;
    $campos_sql['tipo_boleto'] = $tipo_boleto;
    $campos_sql['cod_barra_consumo'] = $campos_cod_barra_consumo;
    $campos_sql['cod_barra_gerais'] = $campos_cod_barra_gerais;
    $campos_sql['id_referencia'] = $id_referencia;
    $campos_sql['id_bens'] = $id_bens;
    $campos_sql['id_tipo_pag_saida'] = $id_tipo_pag_saida;
    $campos_sql['entradaesaida_subgrupo_id'] = $subgrupo;
    //$campos_sql['tipo_empresa'] = $tipo_empresa;
    $campos_sql['tipo_empresa'] = 1;
    $campos_sql['id_fornecedor'] = $id_fornecedor;
    $campos_sql['nome_fornecedor'] = $nome_fornecedor;
    $campos_sql['cnpj_fornecedor'] = $cnpj_fornecedor;
    $campos_sql['id_prestador'] = $id_prestador;
    $campos_sql['nome_prestador'] = $nome_prestador;
    $campos_sql['cnpj_prestador'] = $cnpj_prestador;
    $campos_sql['n_documento'] = $n_documento;
    $campos_sql['link_nfe'] = $link_nfe;
    $campos_sql['mes_competencia'] = $mes_competencia;
    $campos_sql['ano_competencia'] = $ano_competencia;
    $campos_sql['valor_bruto'] = $valor_bruto;
    $campos_sql['dt_emissao_nf'] = $dt_emissao_nf;
    $campos_sql['tipo_nf'] = $tipo_nf;$campos_sql['id_projeto'] = $projeto;
    $campos_sql['id_banco'] = $banco;
    $campos_sql['id_user'] = $id_user;
    $campos_sql['nome'] = $nome;
    $campos_sql['id_nome'] = $id_nome;
    $campos_sql['especifica'] = $especifica;
    $campos_sql['tipo'] = $tipo;
    $campos_sql['adicional'] = $adicional;
    $campos_sql['valor'] = $valor;
    $campos_sql['data_proc'] = $data_proc;
    $campos_sql['data_vencimento'] = $data_vencimento;
    $campos_sql['status'] = 1;
    $campos_sql['comprovante'] = 2;
    $campos_sql['nosso_numero'] = $nosso_numero;
    $campos_sql['tipo_boleto'] = $tipo_boleto;
    $campos_sql['cod_barra_consumo'] = $campos_cod_barra_consumo;
    $campos_sql['cod_barra_gerais'] = $campos_cod_barra_gerais;
    $campos_sql['id_referencia'] = $id_referencia;
    $campos_sql['id_bens'] = $id_bens;
    $campos_sql['id_tipo_pag_saida'] = $id_tipo_pag_saida;
    $campos_sql['entradaesaida_subgrupo_id'] = $subgrupo;
    //$campos_sql['tipo_empresa'] = $tipo_empresa;
    $campos_sql['id_fornecedor'] = $id_fornecedor;
    $campos_sql['nome_fornecedor'] = $nome_fornecedor;
    $campos_sql['cnpj_fornecedor'] = $cnpj_fornecedor;
    $campos_sql['id_prestador'] = $id_prestador;
    $campos_sql['nome_prestador'] = $nome_prestador;
    $campos_sql['cnpj_prestador'] = $cnpj_prestador;
    $campos_sql['n_documento'] = $n_documento;
    $campos_sql['link_nfe'] = $link_nfe;
    $campos_sql['mes_competencia'] = $mes_competencia;
    $campos_sql['ano_competencia'] = $ano_competencia;
    $campos_sql['valor_bruto'] = $valor_bruto;
    $campos_sql['dt_emissao_nf'] = $dt_emissao_nf;
    $campos_sql['tipo_nf'] = $tipo_nf;
    
    //////////////////////////////
    /////////montado INSERT  ////
    /////////////////////////////
    if($tipo != 0){
        foreach ($campos_sql as $chave => $valor) {

            $campos[] = $chave;
            $valores[] = "'" . $valor . "'";
        }
        $campos = implode(',', $campos);
        $valores = implode(',', $valores);

        $sql_insert = "INSERT INTO saida ($campos) VALUES ($valores)";
        mysql_query($sql_insert);
        $id_saida = mysql_insert_id();

        // Prestadores de servi√ßo OBS. existe desde antes da LAGOS
        if (($tipo == '32' or $tipo == '132' or $grupo == 30 or $grupo == 80 or $grupo == 20) and !empty($id_prestador)) {

            if ($tipo == '132') {
                $tipo_prestador = 'NOTA';
            } elseif ($tipo == '32') {
                $tipo_prestador = 'FOLHA';
            }

            $query_prestador = mysql_query("SELECT MAX(parcela) FROM prestador_pg WHERE id_prestador = '$id_prestador'");
            $prestador = @mysql_result($query_prestador, 0);
            $prestador = $prestador + 1;

            mysql_query("INSERT INTO prestador_pg (id_prestador,	id_regiao,	id_saida, tipo,	valor,	data,	documento,	parcela, gerado,	status_reg,	comprovante)
            VALUES ('$id_prestador', '$regiao', '$id_saida', '$tipo_prestador', '$valor', '$data_credito2', '$especifica', '$prestador', '1', '1', '0');
            ");
        }
    }
}

///////////////////////////////////////////////////////////
//////////  ATUALIZAR SAÕDA  //////////////////////////////
///////////////////////////////////////////////////////////
if (isset($_POST['atualizar'])) {
    if($tipo != 0){    
        $id_saida = $_REQUEST['id_saida'];

        $query_saida = "SELECT * FROM saida WHERE id_saida = {$id_saida}";
        $rs_saida = mysql_query($query_saida);
        $row_saida = mysql_fetch_assoc($rs_saida);

        /*   echo '<pre>';
          print_r($_POST);
          echo'</pre>';
         */

        $array_grupo_prestadores = array("FOLHA" => 1, "RESERVA" => 2, "MATERIAL DE CONSUMO" => 20, "SERVI«OS DE TERCEIROS" => 30, "INVESTIMENTOS" => 80);

        if (!in_array($grupo, $array_grupo_prestadores)) {
            unset($id_fornecedor, $nome_fornecedor, $cnpj_fornecedor, $id_prestador, $nome_prestador, $cnpj_prestador, $tipo_empresa);
        }

        switch ($grupo) {
            case 10:
                $nome = $_REQUEST['nome_saida'];
                break;
        }

        if($tipo == 155){

            $nome = $row_saida['nome'];

        }

        //ALTERA«√O PARA QUANDO ATUALIZAR A RESCIS√O N√O SUMIR O NOME
        if ($tipo == 170) {

            $qr_resc_nome = mysql_query("select b.id_clt, b.nome, c.regiao, d.nome as projeto, 
                                                       (SELECT  nome_mes FROM ano_meses WHERE num_mes = MONTH(b.data_demi)) as mes, YEAR(b.data_demi) as ano
                                                        from saida as a
                                                       INNER JOIN rh_clt as b
                                                       ON a.id_clt = b.id_clt
                                                       INNER JOIN regioes as c
                                                       ON c.id_regiao = b.id_regiao
                                                       INNER JOIN projeto as d
                                                       ON b.id_projeto = d.id_projeto
                                                       WHERE id_saida = '$id_saida'");
            $row_resc = mysql_fetch_assoc($qr_resc_nome);
            $nome = '(' . $row_resc['id_clt'] . ') ' . $row_resc['nome'] . ', REGI√O: ' . $row_resc['regiao'] . ' - PROJETO: ' . $row_resc['projeto'] . ' RESCIS√O ' . $row_resc['mes'] . '/' . $row_resc[ano];
        }

        $alteraValor = "";
        if (isset($valor) && !empty($valor) && $row_saida['status'] == 1)
            $alteraValor = ", valor = '{$valor}'";

        if(empty($valor_estorno_parcial))
            $valor_estorno_parcial = 0;


        $sql = "UPDATE saida SET 
                                    id_regiao = '$regiao', id_projeto = '$projeto', id_banco = '$banco',  nome = '$nome', id_nome = '$id_nome', 
                                    especifica = '$especifica', tipo = '$tipo',  data_proc = '$data_proc', data_vencimento = '$data_vencimento',
                                    nosso_numero = '$nosso_numero', tipo_boleto = '$tipo_boleto', cod_barra_consumo = '$campos_cod_barra_consumo', 
                                    cod_barra_gerais = '$campos_cod_barra_gerais', id_referencia = '$id_referencia', id_bens = '$id_bens', 
                                    id_tipo_pag_saida =  '$id_tipo_pag_saida',  entradaesaida_subgrupo_id = '$subgrupo', 
                                    id_fornecedor = '$id_fornecedor', nome_fornecedor = '$nome_fornecedor',	cnpj_fornecedor = '$cnpj_fornecedor', 
                                    id_prestador = '$id_prestador', nome_prestador = '$nome_prestador', cnpj_prestador = '$cnpj_prestador', 
                                    estorno = '$estorno', estorno_obs = '$descricao_estorno', valor_estorno_parcial = $valor_estorno_parcial,
                                    mes_competencia = '$mes_competencia', ano_competencia = '$ano_competencia', valor_bruto = '$valor_bruto', 
                                    dt_emissao_nf = '$dt_emissao_nf', tipo_nf = '$tipo_nf', n_documento = '$n_documento' $alteraValor
                         WHERE id_saida = '$id_saida' LIMIT 1";

        mysql_query($sql) or die(mysql_error());

    }
}
/////////////////////////////////////////////////////////////////////////////
////////////    ADICIONANDO ANEXOS E COMPROVANTES    ////////////////////////
/////////////////////////////////////////////////////////////////////////////
if (isset($_POST['cadastrar']) or isset($_POST['atualizar'])) {
    
    
    $arquivos_anexo = $_FILES['anexo_upload'];
    $arquivos_comprovantes = $_FILES['anexo_upload2'];

    ///ANEXOS
    if (sizeof($arquivos_anexo) != 0) {
        
        $verifica_anexos = array();
        $array_anexo = organiza_files($arquivos_anexo);

        mysql_query("UPDATE saida SET tipo_arquivo = '', comprovante = '2' WHERE id_saida = '$id_saida' LIMIT 1;");
        ///VALIDANDO EXTENS’ES
        foreach ($array_anexo as $anexo) {
            switch ($anexo['type']) {
                case 'application/pdf': $extensao = '.pdf';
                    break;
                case 'image/png': $extensao = '.png';
                    break;
                case 'image/gif': $extensao = '.gif';
                    break;
                case 'image/jpeg' : $extensao = '.jpg';
                    break;
                case 'image/jpg' : $extensao = '.jpg';
                    break;
            }

            mysql_query("INSERT INTO saida_files (id_saida, tipo_saida_file) VALUES ('$id_saida', '$extensao')") or die(mysql_error());


            $id_saida_files = mysql_insert_id();

            $arquivo_origem = $anexo['tmp_name'];
            if ($_COOKIE['logado'] == 179) {
                $destino = '../comprovantes/' . $id_saida_files . '.' . $id_saida . $extensao;
            }else{
                $destino = '../comprovantes/' . $id_saida_files . '.' . $id_saida . $extensao;
            }
            
            //MOVE PARA O DESTINO
            move_uploaded_file($arquivo_origem, $destino);
            
            //VERIFICANDO SE O ARQUIVO ESTA NA PASTA INFORMADA 
            $arquivo = "../comprovantes/" . $id_saida_files . '.' . $id_saida . $extensao;
            if(is_file($arquivo)) {
                $verifica_anexos[] = $arquivo;
            }
            if(count($verifica_anexos) == 0){
                $_SESSION['msgError'] = "O anexo da saida: <b>" .$id_saida. "</b> n„o foi movido corretamente - teste";
                $_SESSION['saidaError'] = $id_saida;
            }
            
        }
    }

    ////COMPROVANTES 
    if (sizeof($arquivos_comprovantes) != 0) {
        
        $verifica_comprovante = array();
        $array_comprovante = organiza_files($arquivos_comprovantes);
        
        ///VALIDANDO EXTENS’ES
        foreach ($array_comprovante as $comprovante) {
            switch ($comprovante['type']) {
                case 'application/pdf': $extensao = '.pdf';
                    break;
                case 'image/png': $extensao = '.png';
                    break;
                case 'image/gif': $extensao = '.gif';
                    break;
                case 'image/jpeg' : $extensao = '.jpg';
                    break;
                case 'image/jpg' : $extensao = '.jpg';
                    break;
            }

            mysql_query("INSERT INTO saida_files_pg (id_saida, tipo_pg) VALUES ('$id_saida', '$extensao')");

            $id_pg = mysql_insert_id();
            $arquivo_origem = $comprovante['tmp_name'];
            $destino = '../comprovantes/' . $id_pg . '.' . $id_saida . '_pg' . $extensao;

            move_uploaded_file($arquivo_origem, $destino);
            
            //VERIFICANDO SE O ARQUIVO ESTA NA PASTA INFORMADA 
            $arquivo = "../comprovantes/" . $id_pg . '.' . $id_saida . '_pg' . $extensao;
            foreach (glob($arquivo) as $arquivo) {
                $verifica_comprovante[] = $arquivo;
            }
            if(count($verifica_comprovante) == 0){
                $_SESSION['msgError'] = "O comprovante da saida: <b>" .$id_saida. "</b> n„o foi movido corretamente - teste";
                $_SESSION['saidaError'] = $id_saida;
            }
        }
    }



    //////REDIRECIONAMENTO AP”S A CONCLUS√O DA INSER«’/ALTERA«√O DE DADOS
    if (isset($_POST['cadastrar'])) {

        // header("Location: index.php?enc=$_POST[link_enc]");
        echo '<script> 
            alert("Saida cadastrada com sucesso!");
            location.href="index.php?enc=' . $_POST['link_enc'] . '"
            </script>';
    } elseif (isset($_POST['atualizar'])) {
//        echo 'Aguarde...';
        
//        echo '<script> 
//            alert("Saida atualizada com sucesso!");
//            location.href="index.php?enc=' . $_POST['link_enc'] . '"
//            </script>';
        
        echo '<script>     
            alert("Saida atualizada com sucesso!");
            if (parent.window.hs) {
		var exp = parent.window.hs.getExpander();
		if (exp) {
			setTimeout(function() {
				exp.close();
                             parent.window.location.reload();
			},  1000);
                         

                        }else {
                        setTimeout(function(){
                        window.parent.location.reload();
                        parent.eval("tb_remove()")
                        },1000)  
                    }
        }
        </script>';
        
    }
}