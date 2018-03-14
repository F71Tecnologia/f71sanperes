<?php


class SodexoClass{
    
    public $mes;
    public $ano;
    public $id_pedido;
    public $cod_cliente = 1717075;
    public $tipo_pedido;
    public $data_entrega;
    public $data_credito;
    public $brancos = NULL;
    
    public function __construct($mes, $ano, $id_pedido, $tipo_pedido, $data_entrega, $data_credito, $cod_cliente) {
        $this->mes = $mes;
        $this->ano = $ano;
        $this->id_pedido = $id_pedido;
        $this->tipo_pedido = $tipo_pedido;
        $this->data_entrega = $data_entrega;
        $this->data_credito = $data_credito;
        $this->cod_cliente = $cod_cliente;
    }
    
    public function getLinhaClt($id_clt) {
        $query = "
            SELECT 
                IF(A.id_linha1 > 0, B.id_operadora, '') AS cod_op1,
                IF(A.id_linha1 > 0, A.id_linha1, '') AS cod_linha1,
                IF(A.id_linha1 > 0, A.qtd1, '') AS qtd1,
                IF(B.valor_tarifa = 0, B.valor_tarifa, '') AS valor1,
                IF(A.id_linha1 > 0, A.cartao1, '') AS cartao1,

                IF(A.id_linha2 > 0, C.id_operadora, '') AS cod_op2,
                IF(A.id_linha2 > 0, A.id_linha2, '') AS cod_linha2,
                IF(A.id_linha2 > 0, A.qtd2, '') AS qtd2,
                IF(C.valor_tarifa = 0, C.valor_tarifa, '') AS valor2,
                IF(A.id_linha2 > 0, A.cartao2, '') AS cartao2,

                IF(A.id_linha3 > 0, D.id_operadora, '') AS cod_op3,
                IF(A.id_linha3 > 0, A.id_linha3, '') AS cod_linha3,
                IF(A.id_linha3 > 0, A.qtd3, '') AS qtd3,
                IF(D.valor_tarifa = 0, D.valor_tarifa, '') AS valor3,
                IF(A.id_linha3 > 0, A.cartao3, '') AS cartao3,

                IF(A.id_linha4 > 0, E.id_operadora, '') AS cod_op4,
                IF(A.id_linha4 > 0, A.id_linha4, '') AS cod_linha4,
                IF(A.id_linha4 > 0, A.qtd4, '') AS qtd4,
                IF(E.valor_tarifa = 0, E.valor_tarifa, '') AS valor4,
                IF(A.id_linha4 > 0, A.cartao4, '') AS cartao4,

                IF(A.id_linha5 > 0, F.id_operadora, '') AS cod_op5,
                IF(A.id_linha5 > 0, A.id_linha5, '') AS cod_linha5,
                IF(A.id_linha5 > 0, A.qtd5, '') AS qtd5,
                IF(F.valor_tarifa = 0, F.valor_tarifa, '') AS valor5,
                IF(A.id_linha5 > 0, A.cartao5, '') AS cartao5
            FROM rh_vt_valores_assoc AS A
            LEFT JOIN rh_vt_linha AS B ON(A.id_linha1 = B.id_vt_linha)
            LEFT JOIN rh_vt_linha AS C ON(A.id_linha2 = C.id_vt_linha)
            LEFT JOIN rh_vt_linha AS D ON(A.id_linha3 = D.id_vt_linha)
            LEFT JOIN rh_vt_linha AS E ON(A.id_linha4 = E.id_vt_linha)
            LEFT JOIN rh_vt_linha AS F ON(A.id_linha5 = F.id_vt_linha)
            WHERE A.id_clt = {$id_clt} AND A.status_reg = 1";
        $sql = mysql_query($query) or die(mysql_error());
        $res = mysql_fetch_assoc($sql);
        
        return $res;
    }
    
    //REGISTRO TIPO 0 - IDENTIFICAÇÃO DA EMPRESA E DO TIPO DE PEDIDO
    public function montaReg0($arquivo, $responsavel) {
        //01 TIPO DE REGISTRO
        $tpReg = "0";
        $tpReg = sprintf("%1s", $tpReg);
        fwrite($arquivo, $tpReg, 1);
        
        //02 CÓDIGO DO CLIENTE
        $codCliente = $this->cod_cliente;
        $codCliente = sprintf("%08s", RemoveCaracteres($codCliente));
        fwrite($arquivo, $codCliente, 8);
        
        //03 RAZAO SOCIAL DO CLIENTE
        $nomeResp = $responsavel['razao'];
        $nomeResp = sprintf("%-40s", RemoveAcentos(exPersonalizada($nomeResp, $this->caracteres)));
        fwrite($arquivo, $nomeResp, 40);
        
        //04 NUM. PEDIDO CLIENTE - OPCIONAL(CONTROLE DO CLIENTE)
        $numPedido = $this->id_pedido;
        $numPedido = sprintf("%06s", RemoveCaracteres($numPedido));
        fwrite($arquivo, $numPedido, 6);
        
        //05 PERIODO DE REFERENCIA
        $competencia = sprintf("%02d", $this->mes) . $this->ano;
        $competencia = sprintf("%6s", $competencia);
        fwrite($arquivo, $competencia, 6);
        
        //06 ADMINISTRADORA
        $administradora = sprintf("%03s", 005);
        fwrite($arquivo, $administradora, 3);
        
        //07 TIPO DE PEDIDO
        $tipo_pedido = sprintf("%03s", 1);
        fwrite($arquivo, $tipo_pedido, 3);
        
        //08 FILLER
        $brancos = sprintf("%-1s", $this->brancos);
        fwrite($arquivo, $brancos, 1);
        
        //09 DATA DE GERACAO DO ARQUIVO - OPCIONAL(CONTROLE DO CLIENTE)
        $data_geracao = date("dmY");
        $data_geracao = sprintf("%8s", $data_geracao);
        fwrite($arquivo, $data_geracao, 8);
        
        //10 DATA DE ENTREGA DO PEDIDO
        $data_entrega = $this->data_entrega;
        $data_entrega = sprintf("%8s", implode("", array_reverse(explode("-", $data_entrega))));
        fwrite($arquivo, $data_entrega, 8);
        
        //11 DATA PARA CREDITO NO CARTÃO
        $data_credito = $this->data_credito;
        $data_credito = sprintf("%8s", implode("", array_reverse(explode("-", $data_credito))));
        fwrite($arquivo, $data_credito, 8);
        
        //12 NUM. SEQUENCIAL ARQUIVO - OPCIONAL(CONTROLE DO CLIENTE)
        $brancos = sprintf("%-6s", $this->brancos);
        fwrite($arquivo, $brancos, 6);
        
        //13 NUMERO DE VERSAO ARQ. - OPCIONAL(CONTROLE DO CLIENTE)
        $brancos = sprintf("%-2s", $this->brancos);
        fwrite($arquivo, $brancos, 2);
        
        //14 FILLER
        $brancos = sprintf("%-529s", $this->brancos);
        fwrite($arquivo, $brancos, 529);
        
        fwrite($arquivo, "\r\n");
    }
    
    //REGISTRO TIPO 3 - INFORMAÇÕES PARA ENTREGA
    public function montaReg3($arquivo, $unidade) {
        //301 TIPO DE REGISTRO
        $tpReg = "3";
        $tpReg = sprintf("%1s", $tpReg);
        fwrite($arquivo, $tpReg, 1);
        
        //302 FILLER
        $brancos = sprintf("%-6s", $this->brancos);
        fwrite($arquivo, $brancos, 6);
        
        //303 CÓDIGO DEPTO
        $cod_departamento = $unidade['codigo_sodexo'];
        $cod_departamento = sprintf("%-18s", $cod_departamento);
        fwrite($arquivo, $cod_departamento, 18);
        
        //304 FILLER
        $brancos = sprintf("%-12s", $this->brancos);
        fwrite($arquivo, $brancos, 12);
        
        //305 NOME DEPTO
        $nome_departamento = $unidade['nome_unidade'];
        $nome_departamento = sprintf("%-35s", RemoveAcentos(exPersonalizada(RemoveCaracteres($nome_departamento, $this->caracteres))));
        fwrite($arquivo, $nome_departamento, 35);
        
        //306 FILLER
        $brancos = sprintf("%-5s", $this->brancos);
        fwrite($arquivo, $brancos, 5);
        
        //307 NOME RESP. DEPTO
        $nome_responsavel = "CAROLINA PONTES DO NASCIMENTO LARUCCI";
        $nome_responsavel = sprintf("%-20s", RemoveAcentos(exPersonalizada(RemoveCaracteres($nome_responsavel, $this->caracteres))));
        fwrite($arquivo, $nome_responsavel, 20);
        
        //308 FILLER
        $brancos = sprintf("%-14s", $this->brancos);
        fwrite($arquivo, $brancos, 14);
        
        //309 TIPO DE LOGRADOURO
        $tipo_logradouro = "AV";
        $tipo_logradouro = sprintf("%-6s", RemoveAcentos(exPersonalizada(RemoveCaracteres($tipo_logradouro, $this->caracteres))));
        fwrite($arquivo, $tipo_logradouro, 6);
        
        //310 DESCRIÇÃO DO LOGRADOURO
        $logradouro = "PAULISTA";
        $logradouro = sprintf("%-40s", RemoveAcentos(exPersonalizada(RemoveCaracteres($logradouro, $this->caracteres))));
        fwrite($arquivo, $logradouro, 40);
        
        //311 NÚMERO
        $numero = 1294;
        $numero = sprintf("%-8s", $numero);
        fwrite($arquivo, $numero, 8);
        
        //312 COMPLEMENTO
        $complemento = "ANDAR 11";
        $complemento = sprintf("%-20s", RemoveAcentos(exPersonalizada(RemoveCaracteres($complemento, $this->caracteres))));
        fwrite($arquivo, $complemento, 20);
        
        //313 BAIRRO
        $bairro = "BELA VISTA";
        $bairro = sprintf("%-15s", RemoveAcentos(exPersonalizada(RemoveCaracteres($bairro, $this->caracteres))));
        fwrite($arquivo, $bairro, 15);
        
        //314 FILLER
        $brancos = sprintf("%-5s", $this->brancos);
        fwrite($arquivo, $brancos, 5);
        
        //315 CIDADE
        $cidade = "SAO PAULO";
        $cidade = sprintf("%-20s", RemoveAcentos(exPersonalizada(RemoveCaracteres($cidade, $this->caracteres))));
        fwrite($arquivo, $cidade, 20);
        
        //316 FILLER
        $brancos = sprintf("%-10s", $this->brancos);
        fwrite($arquivo, $brancos, 14);
        
        //317 ESTADO
        $estado = "SP";
        $estado = sprintf("%-2s", RemoveAcentos(exPersonalizada(RemoveCaracteres($estado, $this->caracteres))));
        fwrite($arquivo, $estado, 2);
        
        //318 CEP
        $cep = 01310100;
        $cep = sprintf("%-8s", $cep);
        fwrite($arquivo, $cep, 8);
        
        //319 FILLER
        $brancos = sprintf("%-384s", $this->brancos);
        fwrite($arquivo, $brancos, 384);
        
        fwrite($arquivo, "\r\n");
    }
    
    //REGISTRO TIPO 4 - INFORMAÇÕES PARA COLABORADOR E BENEFÍCIOS SOLICITADOS
    public function montaReg4($arquivo, $empregado) {
        //401 TIPO DE REGISTRO
        $tpReg = "4";
        $tpReg = sprintf("%1s", $tpReg);
        fwrite($arquivo, $tpReg, 1);
        
        //402 FILLER
        $brancos = sprintf("%-36s", $this->brancos);
        fwrite($arquivo, $brancos, 36);
        
        //403 MATRÍCULA DO COLABORADOR / FUNCIONÁRIO
        $matricula = $empregado['matricula_sodexo'];
        $matricula = sprintf("%010s", $matricula);
        fwrite($arquivo, $matricula, 10);
        
        //404 NOME DO COLABORADOR / FUNCIONÁRIO
        $nome = $empregado['nome_empregado'];
        $nome = sprintf("%-40s", RemoveAcentos(exPersonalizada(RemoveCaracteres($nome, $this->caracteres))));
        fwrite($arquivo, $nome, 40);
        
        //405 FILLER
        $brancos = sprintf("%-1s", $this->brancos);
        fwrite($arquivo, $brancos, 1);
        
        //406 DATA NASCIMENTO
        $dtNasc = $empregado['data_nasci'];
        $dtNasc = sprintf("%-8s", implode('', array_reverse(explode('-', $dtNasc))));
        fwrite($arquivo, $dtNasc, 8);
        
        //407 CPF TITULAR
        $cpf = $empregado['cpf'];
        $cpf = sprintf("%11s", RemoveCaracteres($cpf));
        fwrite($arquivo, $cpf, 11);
        
        //408 SEXO
        $sexo = $empregado['sexo'];
        $sexo = sprintf("%1s", $sexo);
        fwrite($arquivo, $sexo, 1);
        
        if($this->tipo_pedido == 1){
            $cod_produto = "002";
            $cod_forma = "004";
        }elseif($this->tipo_pedido == 2){
            $cod_produto = "001";
            $cod_forma = "002";
        }elseif($this->tipo_pedido == 3){
            $cod_produto = "007";
            $cod_forma = "003";
        }
        
        //409 CÓDIGO PRODUTO
        $cod_produto = $cod_produto;
        $cod_produto = sprintf("%3s", $cod_produto);
        fwrite($arquivo, $cod_produto, 3);                
        
        //410 CÓDIGO FORMA
        $cod_forma = $cod_forma;
        $cod_forma = sprintf("%3s", $cod_forma);
        fwrite($arquivo, $cod_forma, 3); 
        
        //411 QTDE TALÕES
        $qtd_taloes = NULL;
        $qtd_taloes = sprintf("%05s", $qtd_taloes);
        fwrite($arquivo, $qtd_taloes, 5);
        
        //412 QTDE CHEQUES POR TALÃO
        $qtd_taloes_ = NULL;
        $qtd_taloes_ = sprintf("%02s", $qtd_taloes_);
        fwrite($arquivo, $qtd_taloes_, 2);
        
        //413 VALOR FACIAL / CRÉDITO
        $valor = $empregado['valor'];
        $valor = sprintf("%012s", RemoveCaracteres($valor));
        fwrite($arquivo, $valor, 12);
        
        //414 FILLER
        $brancos = sprintf("%-14s", $this->brancos);
        fwrite($arquivo, $brancos, 14);
        
        //415 NOME DO COLABORADOR PARA IMPRESSÃO NO CARTÃO
        $nome_impressao = NULL;
        $nome_impressao = sprintf("%-24s", $nome_impressao);
        fwrite($arquivo, $nome_impressao, 24);
        
        //416 FAIXA SALARIAL DO FUNCIONÁRIO
        $faixa = NULL;
        $faixa = sprintf("%-2s", $faixa);
        fwrite($arquivo, $faixa, 2);
        
        //417 FILLER
        $brancos = sprintf("%-68s", $this->brancos);
        fwrite($arquivo, $brancos, 68);
        
        //SOMENTE PARA VT
        if($this->tipo_pedido == 3){
            //418 NÚMERO DO RG
            $rg = $empregado['rg'];
            $rg = RemoveCaracteresGeral($rg);
            $rg = str_replace(" ", "", $rg);
            $rg = sprintf("%10s", RemoveCaracteres($rg));
            fwrite($arquivo, $rg, 10);
            
            //419 DÍGITO DO RG        
            $digito_rg = "";
            $digito_rg = sprintf("%2s", RemoveCaracteres($digito_rg));
            fwrite($arquivo, $digito_rg, 2);
            
            //420 UF EMISSORA DO RG
            $uf_rg = $empregado['uf_rg'];
            $uf_rg = sprintf("%-2s", RemoveAcentos(exPersonalizada(RemoveCaracteres($uf_rg, $this->caracteres))));
            fwrite($arquivo, $uf_rg, 2);
            
            //421 DATA EMISSÃO DO RG
            $dt_rg = $empregado['data_rg'];
            $dt_rg = sprintf("%-8s", implode('', array_reverse(explode('-', $dt_rg))));
            fwrite($arquivo, $dt_rg, 8);
            
            //422 ORGÃO EMISSOR DO RG
            $orgao_rg = $empregado['orgao'];
            $orgao_rg = RemoveCaracteresGeral($orgao_rg);
            $orgao_rg = str_replace(" ", "", $orgao_rg);
            $orgao_rg = sprintf("%6s", RemoveCaracteres($orgao_rg));
            fwrite($arquivo, $orgao_rg, 6);
            
            //423 FILLER
            $brancos = sprintf("%-14s", $this->brancos);
            fwrite($arquivo, $brancos, 14);
            
            //424 TIPO DE LOGRADOURO. (endereço do funcionário)
            $tipo_logradouro = acentoMaiusculo($empregado['tipo_endereco']);
            $tipo_logradouro = sprintf("%-6s", RemoveAcentos(exPersonalizada(RemoveCaracteres($tipo_logradouro, $this->caracteres))));
            fwrite($arquivo, $tipo_logradouro, 6);
            
            //425 DESCRIÇÃO DO LOGRADOURO
            $logradouro = acentoMaiusculo($empregado['endereco']);
            $logradouro = sprintf("%-40s", RemoveAcentos(exPersonalizada(RemoveCaracteres($logradouro, $this->caracteres))));
            fwrite($arquivo, $logradouro, 40);

            //426 NÚMERO
            $numero = $empregado['numero'];
            $numero = RemoveCaracteresGeral($numero);
            $numero = str_replace(" ", "", $numero);
            $numero = sprintf("%7s", RemoveCaracteres($numero));
            fwrite($arquivo, $numero, 7);
            
            //427 COMPLEMENTO
            $complemento = acentoMaiusculo($empregado['complemento']);
            $complemento = sprintf("%-20s", RemoveAcentos(exPersonalizada(RemoveCaracteres($complemento, $this->caracteres))));
            fwrite($arquivo, $complemento, 20);
            
            //428 BAIRRO
            $bairro = acentoMaiusculo($empregado['bairro']);
            $bairro = sprintf("%-15s", RemoveAcentos(exPersonalizada(RemoveCaracteres($bairro, $this->caracteres))));
            fwrite($arquivo, $bairro, 15);
            
            //429 FILLER
            $brancos = sprintf("%-5s", $this->brancos);
            fwrite($arquivo, $brancos, 5);
            
            //430 CIDADE
            $cidade = acentoMaiusculo($empregado['cidade']);
            $cidade = sprintf("%-15s", RemoveAcentos(exPersonalizada(RemoveCaracteres($cidade, $this->caracteres))));
            fwrite($arquivo, $cidade, 15);
            
            //431 FILLER
            $brancos = sprintf("%-10s", $this->brancos);
            fwrite($arquivo, $brancos, 10);

            //432 ESTADO
            $estado = acentoMaiusculo($empregado['uf']);
            $estado = sprintf("%-2s", RemoveAcentos(exPersonalizada(RemoveCaracteres($estado, $this->caracteres))));
            fwrite($arquivo, $estado, 2);
            
            //433 CEP
            $cep = $empregado['cep'];
            $cep = RemoveCaracteresGeral($cep);
            $cep = str_replace(" ", "", $cep);
            $cep = sprintf("%8s", RemoveCaracteres($cep));
            fwrite($arquivo, $cep, 8);

            //434 NOME DA MAE DO FUNCIONÁRIO
            $mae = acentoMaiusculo($empregado['uf']);
            $mae = sprintf("%-40s", RemoveAcentos(exPersonalizada(RemoveCaracteres($mae, $this->caracteres))));
            fwrite($arquivo, $mae, 40);
            
            //435 ESTADO CIVIL
            if(($empregado['civil'] == "Solteiro") || ($empregado['civil'] == "Solt.")){
                $estado_civil = "S";
            }elseif($empregado['civil'] == "Casado"){
                $estado_civil = "C";
            }elseif(($empregado['civil'] == "Divorciado") || ($empregado['civil'] == "Separado")){
                $estado_civil = "D";
            }elseif($empregado['civil'] == "Viúvo"){
                $estado_civil = "V";
            }else{
                $estado_civil = "";
            }
            
            $estado_civil = sprintf("%-1s", $estado_civil);
            fwrite($arquivo, $mae, 1);
            
            //436 E-mail do Beneficiário
            $email = acentoMaiusculo($empregado['email']);
            $email = sprintf("%-50s", RemoveAcentos(exPersonalizada(RemoveCaracteres($email, $this->caracteres))));
            fwrite($arquivo, $email, 50);
            
            //437 DDD do telefone do Beneficiário
            $ddd = "";
            $ddd = sprintf("%4s", RemoveCaracteres($ddd));
            fwrite($arquivo, $ddd, 4);
            
            //438 TELEFONE DO BENEFICIÁRIO
            $telefone = "";
            $telefone = sprintf("%8s", RemoveCaracteres($telefone));
            fwrite($arquivo, $telefone, 8);
            
            //439 CARGO DO BENEFICIARIO
            $cargo = acentoMaiusculo($empregado['cargo']);
            $cargo = sprintf("%-40s", RemoveAcentos(exPersonalizada(RemoveCaracteres($cargo, $this->caracteres))));
            fwrite($arquivo, $cargo, 40);
            
            //440 NÚMERO DO SIC-CURITIBA DO BENEFICIÁRIO (obrigatório somente para Operadoras de Curitiba)
            $brancos = sprintf("%-16s", $this->brancos);
            fwrite($arquivo, $brancos, 16);
            
            //441 FILLER
            $brancos = sprintf("%-128s", $this->brancos);
            fwrite($arquivo, $brancos, 128);
            
            //442 QUANT. DIAS ÚTEIS
            $dias_uteis = $empregado['dias_uteis'];
            $dias_uteis = sprintf("%3s", $dias_uteis);
            fwrite($arquivo, $dias_uteis, 3);
                        
            $linha = SodexoClass::getLinhaClt($empregado['id_clt']);                                
            
            //443 CÓDIGO OPERADORA 1
            $cod_op1 = $linha['cod_op1'];
            $cod_op1 = sprintf("%4s", $cod_op1);
            fwrite($arquivo, $cod_op1, 4);

            //444 CÓDIGO LINHA 1
            $cod_linha1 = $linha['cod_linha1'];
            $cod_linha1 = sprintf("%5s", $cod_linha1);
            fwrite($arquivo, $cod_linha1, 5);

            //445 QUANT. DE PASSES POR DIA 1
            $qtd1 = $linha['qtd1'];
            $qtd1 = sprintf("%4s", $qtd1);
            fwrite($arquivo, $qtd1, 4);

            //446 VALOR DO BILHETE 1
            $valor1 = $linha['valor1'];
            $valor1 = sprintf("%11s", RemoveCaracteres($valor1));
            fwrite($arquivo, $valor1, 11);

            //447 NÚMERO CARTÃO/CQ 1
            $cartao1 = $linha['cartao1'];
            $cartao1 = sprintf("%16s", RemoveCaracteres($cartao1));
            fwrite($arquivo, $cartao1, 16);

            //448 CÓDIGO OPERADORA 2
            $cod_op2 = $linha['cod_op2'];
            $cod_op2 = sprintf("%4s", $cod_op2);
            fwrite($arquivo, $cod_op2, 4);

            //449 CÓDIGO LINHA 2
            $cod_linha2 = $linha['cod_linha2'];
            $cod_linha2 = sprintf("%5s", $cod_linha2);
            fwrite($arquivo, $cod_linha2, 5);

            //450 QUANT. DE PASSES POR DIA 2
            $qtd2 = $linha['qtd2'];
            $qtd2 = sprintf("%4s", $qtd2);
            fwrite($arquivo, $qtd2, 4);

            //451 VALOR DO BILHETE 2
            $valor2 = $linha['valor2'];
            $valor2 = sprintf("%11s", RemoveCaracteres($valor2));
            fwrite($arquivo, $valor2, 11);

            //452 NÚMERO CARTÃO/CQ 2
            $cartao2 = $linha['cartao2'];
            $cartao2 = sprintf("%16s", RemoveCaracteres($cartao2));
            fwrite($arquivo, $cartao2, 16);

            //453 CÓDIGO OPERADORA 3
            $cod_op3 = $linha['cod_op3'];
            $cod_op3 = sprintf("%4s", $cod_op3);
            fwrite($arquivo, $cod_op3, 4);

            //454 CÓDIGO LINHA 3
            $cod_linha3 = $linha['cod_linha3'];
            $cod_linha3 = sprintf("%5s", $cod_linha3);
            fwrite($arquivo, $cod_linha3, 5);

            //455 QUANT. DE PASSES POR DIA 3
            $qtd3 = $linha['qtd3'];
            $qtd3 = sprintf("%4s", $qtd3);
            fwrite($arquivo, $qtd3, 4);

            //456 VALOR DO BILHETE 3
            $valor3 = $linha['valor3'];
            $valor3 = sprintf("%11s", RemoveCaracteres($valor3));
            fwrite($arquivo, $valor3, 11);

            //457 NÚMERO CARTÃO/CQ 3
            $cartao3 = $linha['cartao3'];
            $cartao3 = sprintf("%16s", RemoveCaracteres($cartao3));
            fwrite($arquivo, $cartao3, 16);

            //458 CÓDIGO OPERADORA 4
            $cod_op4 = $linha['cod_op4'];
            $cod_op4 = sprintf("%4s", $cod_op4);
            fwrite($arquivo, $cod_op4, 4);

            //459 CÓDIGO LINHA 4
            $cod_linha4 = $linha['cod_linha4'];
            $cod_linha4 = sprintf("%5s", $cod_linha4);
            fwrite($arquivo, $cod_linha4, 5);

            //460 QUANT. DE PASSES POR DIA 4
            $qtd4 = $linha['qtd4'];
            $qtd4 = sprintf("%4s", $qtd4);
            fwrite($arquivo, $qtd4, 4);

            //461 VALOR DO BILHETE 4
            $valor4 = $linha['valor4'];
            $valor4 = sprintf("%11s", RemoveCaracteres($valor4));
            fwrite($arquivo, $valor4, 11);

            //462 NÚMERO CARTÃO/CQ 4
            $cartao4 = $linha['cartao4'];
            $cartao4 = sprintf("%16s", RemoveCaracteres($cartao4));
            fwrite($arquivo, $cartao4, 16);

            //463 CÓDIGO OPERADORA 5
            $cod_op5 = $linha['cod_op5'];
            $cod_op5 = sprintf("%4s", $cod_op5);
            fwrite($arquivo, $cod_op5, 4);

            //464 CÓDIGO LINHA 5
            $cod_linha5 = $linha['cod_linha5'];
            $cod_linha5= sprintf("%5s", $cod_linha5);
            fwrite($arquivo, $cod_linha5, 5);

            //465 QUANT. DE PASSES POR DIA 5
            $qtd5 = $linha['qtd5'];
            $qtd5 = sprintf("%4s", $qtd5);
            fwrite($arquivo, $qtd5, 4);

            //466 VALOR DO BILHETE 5
            $valor5 = $linha['valor5'];
            $valor5 = sprintf("%11s", RemoveCaracteres($valor5));
            fwrite($arquivo, $valor5, 11);

            //467 NÚMERO CARTÃO/CQ 5
            $cartao5 = $linha['cartao5'];
            $cartao5 = sprintf("%16s", RemoveCaracteres($cartao5));
            fwrite($arquivo, $cartao5, 16);

            //468 FILLER
            $brancos = sprintf("%-94s", $this->brancos);
            fwrite($arquivo, $brancos, 94);            
        }
        
        fwrite($arquivo, "\r\n");
    }
    
    //REGISTRO TIPO 9 - FINALIZADOR DO PEDIDO
    public function montaReg9($arquivo) {
        //901 TIPO DE REGISTRO
        $tpReg = "9";
        $tpReg = sprintf("%1s", $tpReg);
        fwrite($arquivo, $tpReg, 1);
        
        //902 FILLER
        $brancos = sprintf("%-628s", $this->brancos);
        fwrite($arquivo, $brancos, 628);
    }        
    
}

?>