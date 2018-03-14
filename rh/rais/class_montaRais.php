<?php

Class montaRais {
    public $ano_base;
    public $sequencial;
    
    public function __construct($anoBase) {
        $this->ano_base = $anoBase;
    }
    
    public function consultaEmpresas($cnpj){
        $return = montaQuery("rhempresa", "*", "id_empresa IN (" . implode(",", $cnpj) . ")", "nome", null, "array", false, "cnpj");
        return $return;
    }

    public function montaCabecalho($arquivo, $empresa) {
        /* Linha 1 */
        // MONTANDO O CABEÇALHO DO ARQUIVO
        $this->sequencial = 1;
        $sequencial = sprintf("%06s", $this->sequencial);
        fwrite($arquivo, $sequencial, 6);

        $cnpj = RemoveCaracteres($empresa['cnpj']);
        $cnpj = substr($cnpj, 0, 14);
        $cnpj = sprintf("%014s", $cnpj);
        fwrite($arquivo, $cnpj, 14);

        $prefixo = '00';
        fwrite($arquivo, $prefixo, 2);

        $registro = '0';
        fwrite($arquivo, $registro, 1);

        $constante = '1';
        $constante = sprintf("%01s", $constante);
        fwrite($arquivo, $constante, 1);

        $cpf = RemoveCaracteres($empresa['cnpj']);
        $cpf = substr($cpf, 0, 14);
        $cpf = sprintf("%014s", $cpf);
        fwrite($arquivo, $cpf, 14);

        $tipo_inscricao = 1;
        $tipo_inscricao = sprintf("%01s", $tipo_inscricao);
        fwrite($arquivo, $tipo_inscricao, 1);

        $nome = substr(RemoveAcentos(RemoveCaracteres($empresa['razao'])), 0, 40);
        $nome = sprintf("%-40s", $nome);
        fwrite($arquivo, $nome, 40);
        
   
        $endereco = explode('-', $empresa['endereco'], 5); // separa o endereço em 5 partes

        $logradouro = explode(',', $endereco[0]);       
      //$logradouro = explode(',', $empresa['endereco']);
        $logradouro = substr(RemoveAcentos(RemoveCaracteres(RemoveEspacos($logradouro[0]))), 0, 40);
      //$logradouro = substr(RemoveAcentos(RemoveCaracteres(RemoveEspacos($logradouro[0]))), 0, 40);
        $logradouro = sprintf("%-40s", $logradouro);
        fwrite($arquivo, $logradouro, 40);

        $numero = explode(',', $empresa['endereco']);
        $numero = explode('-', $numero[1]); 
         // $numero = substr(RemoveAcentos(RemoveCaracteres(RemoveEspacos($numero[0]))), 0, 6);
        $numero = substr(RemoveLetras(RemoveCaracteres(RemoveAcentos(RemoveEspacos($numero[0])))), 0, 6);
        $numero = sprintf("%06s", $numero);
        fwrite($arquivo, $numero, 6);

      //$complemento = substr(NULL, 0, 21);
        $complemento = substr(RemoveAcentos(RemoveCaracteres(RemoveEspacos($endereco[1]))), 0, 21);
      //$complemento = sprintf("%02s", $complemento);//2
        fwrite($arquivo, $complemento, 21);

      //$bairro = explode('-', $empresa['endereco']);
        $bairro = substr(RemoveAcentos(RemoveCaracteres(RemoveEspacos($endereco[2]))), 0, 19);//0
      //$bairro = substr(RemoveAcentos(RemoveCaracteres(RemoveEspacos($bairro[0]))), 0, 19);//0
        $bairro = sprintf("%-19s", $bairro);//-19
        fwrite($arquivo, $bairro, 19);

        $cep = substr(RemoveCaracteres(RemoveEspacos($empresa['cep'])), 0, 8);
        $cep = sprintf("%08s", $cep);
        fwrite($arquivo, $cep, 8);

        $cod_municipio = substr(RemoveCaracteres($empresa['cod_municipio']), 0, 7);
        $cod_municipio = sprintf("%07s", $cod_municipio);
        fwrite($arquivo, $cod_municipio, 7);

      //$cidade = explode('-', $empresa['endereco']);
        $cidade = substr(RemoveAcentos(RemoveCaracteres(RemoveEspacos($endereco[3]))), 0, 30);
        //$cidade = substr(RemoveAcentos(RemoveCaracteres(RemoveEspacos($cidade[2]))), 0, 30);
        $cidade = sprintf("%-30s", $cidade);
        fwrite($arquivo, $cidade, 30);

      //$uf = explode('-', $empresa['endereco']);
        $uf = substr(RemoveAcentos(RemoveCaracteres(RemoveEspacos($endereco[4]))), 0, 2);
      //$uf = substr(RemoveAcentos(RemoveCaracteres(RemoveEspacos($uf[3]))), 0, 2);
        $uf = sprintf("%02s", $uf);
        fwrite($arquivo, $uf, 2);

        $ddd_telefone = explode('(', $empresa['tel']);
        $ddd_telefone = substr(RemoveCaracteres(RemoveEspacos($ddd_telefone[1])), 0, 2);
        $ddd_telefone = sprintf("%02s", $ddd_telefone);
        fwrite($arquivo, $ddd_telefone, 2);

        $telefone = explode(')', $empresa['tel']);
        $telefone = substr(RemoveCaracteres(RemoveEspacos($telefone[1])), 0, 8);
        $telefone = sprintf("%09s", $telefone);
        fwrite($arquivo, $telefone,9);

        $indicador_retificacao = '2';
        fwrite($arquivo, $indicador_retificacao, 1);

        $data_retificacao = substr(NULL, 0, 8);
        $data_retificacao = sprintf("%08s", $data_retificacao);
        fwrite($arquivo, $data_retificacao, 8);

        $data = date('dmY');
        fwrite($arquivo, $data, 8);

        $email_responsavel = substr($empresa['email'], 0, 45);
        $email_responsavel = sprintf("%-45s", $email_responsavel);
        fwrite($arquivo, $email_responsavel, 45);

        $nome_responsavel = substr(RemoveCaracteres(RemoveAcentos($empresa['responsavel'])), 0, 52);
        $nome_responsavel = sprintf("%-52s", $nome_responsavel);
        fwrite($arquivo, $nome_responsavel, 52);

        $espacos1 = NULL;
        $espacos1 = sprintf("%24s", $espacos1);
        fwrite($arquivo, $espacos1, 24);

      //$tamanho_registro = '0551';
      //fwrite($arquivo, $tamanho_registro, 4);

        $cpf_responsavel = RemoveCaracteres($empresa['cpf']);
        $cpf_responsavel = substr($cpf_responsavel, 0, 11);
        $cpf_responsavel = sprintf("%011s", $cpf_responsavel);
        fwrite($arquivo, $cpf_responsavel, 11);

        $crea = NULL;
        $crea = substr($crea, 0, 12);
        $crea = sprintf("%012s", $crea);
        fwrite($arquivo, $crea, 12);

        $data_nascimento_responsavel = implode('', array_reverse(explode('-', $empresa['data_nasc'])));
        $data_nascimento_responsavel = substr($data_nascimento_responsavel, 0, 8);
        $data_nascimento_responsavel = sprintf("%08s", $data_nascimento_responsavel);
        fwrite($arquivo, $data_nascimento_responsavel, 8);

        $espacos2 = NULL;
        $espacos2 = sprintf("%159s", $espacos2);
        fwrite($arquivo, $espacos2, 159);

        fwrite($arquivo, "\r\n");

        /* Linha 2 */
        $sequencial2 = '2';
        $sequencial2 = sprintf("%06s", $sequencial2);
        fwrite($arquivo, $sequencial2, 6);

        $cnpj2 = $empresa['cnpj'];
        $cnpj2 = RemoveCaracteres($cnpj2);
        $cnpj2 = substr($cnpj2, 0, 14);
        $cnpj2 = sprintf("%014s", $cnpj2);
        fwrite($arquivo, $cnpj2, 14);

        $prefixo2 = '00';
        fwrite($arquivo, $prefixo2, 2);

        $registro2 = '1';
        fwrite($arquivo, $registro2, 1);

        $nome2 = RemoveAcentos($empresa['nome']);
        $nome2 = substr($nome, 0, 52);
        $nome2 = sprintf("%-52s", $nome2);
        fwrite($arquivo, $nome2, 52);
        
        $endereco2 = explode('-', $empresa['endereco'], 5); // divide o endereco em 5 partes
        
        $logradouro2 = explode(',', RemoveAcentos($endereco[0]));
        //$logradouro2 = explode(',', RemoveAcentos($empresa['endereco']));
        $logradouro2 = substr($logradouro2[0], 0, 40);
        $logradouro2 = sprintf("%-40s", $logradouro2);
        fwrite($arquivo, $logradouro2, 40);

        $numero2 = explode(',', $empresa['endereco']);
        $numero2 = explode('-', $numero2[1]);
        $numero2 = substr(RemoveLetras(RemoveCaracteres(RemoveAcentos(RemoveEspacos($numero2[0])))), 0, 6);
        $numero2 = sprintf("%06s", $numero2);
        fwrite($arquivo, $numero2, 6);

       // $complemento2 = '';
       // $complemento2 = substr($complemento2, 0, 21);
        $complemento2 = substr(RemoveAcentos(RemoveCaracteres(RemoveEspacos($endereco2[1]))), 0, 21);
        $complemento2 = sprintf("%-21s", $complemento2);
        fwrite($arquivo, $complemento2, 21);

        $bairro2 = substr(RemoveAcentos(RemoveCaracteres(RemoveEspacos($endereco2[2]))), 0, 19);//0
        $bairro2 = sprintf("%-19s", $bairro2);//-19
        fwrite($arquivo, $bairro2, 19);

      /*  $bairro2 = explode(',', $empresa['endereco']);
        $bairro2 = explode('-', $bairro2[1]);
        $bairro2 = str_replace(' ', '', $bairro2[1]);
        $bairro2 = substr($bairro2, 0, 19);
        $bairro2 = sprintf("%-19s", $bairro2);
        fwrite($arquivo, $bairro2, 19);*/

        $cep2 = $empresa['cep'];
        $cep2 = str_replace('-', '', $cep2);
        $cep2 = substr($cep2, 0, 8);
        $cep2 = sprintf("%08s", $cep2);
        fwrite($arquivo, $cep2, 8);

        $cod_municipio2 = $empresa['cod_municipio'];
        $cod_municipio2 = str_replace('-', '', $cod_municipio2);
        $cod_municipio2 = substr($cod_municipio2, 0, 7);
        $cod_municipio2 = sprintf("%07s", $cod_municipio2);
        fwrite($arquivo, $cod_municipio2, 7);
        
        $cidade2 = substr(RemoveAcentos(RemoveCaracteres(RemoveEspacos($endereco2[3]))), 0, 30);
        $cidade2 = sprintf("%-30s", $cidade2);
        fwrite($arquivo, $cidade, 30);

        $uf2 = substr(RemoveAcentos(RemoveCaracteres(RemoveEspacos($endereco2[4]))), 0, 30);
        $uf2 = sprintf("%02s", $uf2);
        fwrite($arquivo, $uf2, 2);

        $ddd_telefone2 = explode('(', $empresa['tel']);
        $ddd_telefone2 = substr($ddd_telefone2[1], 0, 2);
        $ddd_telefone2 = sprintf("%02s", $ddd_telefone2);
        fwrite($arquivo, $ddd_telefone2, 2);

        $telefone2 = explode(')', $empresa['tel']);
        $telefone2 = substr(RemoveCaracteres(RemoveEspacos($telefone2[1])), 0, 8);
        $telefone2 = sprintf("%09s", $telefone2);
        fwrite($arquivo, $telefone2,9);

        $email_responsavel2 = $empresa['email'];
        $email_responsavel2 = substr($email_responsavel2, 0, 45);
        $email_responsavel2 = sprintf("%-45s", $email_responsavel2);
        fwrite($arquivo, $email_responsavel2, 45);

        $cnae = $empresa['cnae'] . '00';
        $cnae = sprintf("%07s", $cnae);
        fwrite($arquivo, $cnae, 7);

        $natureza = $empresa['natureza'];
        $natureza = substr($natureza, 0, 4);
        $natureza = sprintf("%04s", $natureza);
        fwrite($arquivo, $natureza, 4);

        $proprietarios = $empresa['proprietarios'];
    //    $proprietarios = substr($proprietarios, 0, 2);
        $proprietarios = sprintf("%04s", $proprietarios);
        fwrite($arquivo, $proprietarios, 4);

        $data_base = '04';
        fwrite($arquivo, $data_base, 2);

        $tipo_inscricao = '1';
        fwrite($arquivo, $tipo_inscricao, 1);

        $tipo_rais = '0';
        fwrite($arquivo, $tipo_rais, 1);

        $zeros = '';
        $zeros = sprintf("%02s", $zeros);
        fwrite($arquivo, $zeros, 2);

        $matricula_cei = NULL;
        $matricula_cei = sprintf("%012s", $matricula_cei);
        fwrite($arquivo, $matricula_cei, 12);

        $ano_base_rais = $this->ano_base;
        fwrite($arquivo, $ano_base_rais, 4);

        $porte_empresa = '3';
        fwrite($arquivo, $porte_empresa, 1);

        $participacao_simples = '2';
        fwrite($arquivo, $participacao_simples, 1);

        $participacao_pat = '2';
        fwrite($arquivo, $participacao_pat, 1);

        $f1 = '';
        $f1 = sprintf("%030s", $f1);
        fwrite($arquivo, $f1, 30);

        $indicator_encerramento = '2';
        fwrite($arquivo, $indicator_encerramento, 1);

        $data_encerramento = NULL;
        $data_encerramento = sprintf("%08s", $data_encerramento);
        fwrite($arquivo, $data_encerramento, 8);

        $contribuicao_associativa = NULL;
        $contribuicao_associativa = sprintf("%014s", $contribuicao_associativa);
        fwrite($arquivo, $contribuicao_associativa, 14);

        $contribuicao_associativa_centavos = NULL;
        $contribuicao_associativa_centavos = sprintf("%09s", $contribuicao_associativa_centavos);
        fwrite($arquivo, $contribuicao_associativa_centavos, 9);

        $contribuicao_sindical = NULL;
        $contribuicao_sindical = sprintf("%014s", $contribuicao_sindical);
        fwrite($arquivo, $contribuicao_sindical, 14);

        $contribuicao_sindical_centavos = NULL;
        $contribuicao_sindical_centavos = sprintf("%09s", $contribuicao_sindical_centavos);
        fwrite($arquivo, $contribuicao_sindical_centavos, 9);

        $contribuicao_assistencial = NULL;
        $contribuicao_assistencial = sprintf("%014s", $contribuicao_assistencial);
        fwrite($arquivo, $contribuicao_assistencial, 14);

        $contribuicao_assistencial_centavos = NULL;
        $contribuicao_assistencial_centavos = sprintf("%09s", $contribuicao_assistencial_centavos);
        fwrite($arquivo, $contribuicao_assistencial_centavos, 9);

        $contribuicao_confederativa = NULL;
        $contribuicao_confederativa = sprintf("%014s", $contribuicao_confederativa);
        fwrite($arquivo, $contribuicao_confederativa, 14);

        $contribuicao_confederativa_centavos = NULL;
        $contribuicao_confederativa_centavos = sprintf("%09s", $contribuicao_confederativa_centavos);
        fwrite($arquivo, $contribuicao_confederativa_centavos, 9);

        $atividade_ano_base = '1';
        fwrite($arquivo, $atividade_ano_base, 1);

        $indicador_centralizacao_pagamento = '2';
        fwrite($arquivo, $indicador_centralizacao_pagamento, 1);

        $cnpj_estabelecimento_centralizador = '';
        $cnpj_estabelecimento_centralizador = sprintf("%014s", $cnpj_estabelecimento_centralizador);
        fwrite($arquivo, $cnpj_estabelecimento_centralizador, 14);

        $indicador_empresa_filiada_sindicato = '2';
        fwrite($arquivo, $indicador_empresa_filiada_sindicato, 1);
        
        $tipo_sis_control_ponto = '04';
        fwrite($arquivo, $tipo_sis_control_ponto,2);
        
        $espacos3 = '';
        $espacos3 = sprintf("%85s", $espacos3);
        fwrite($arquivo, $espacos3, 85);

        $exclusivo_empresa1 = '';
        $exclusivo_empresa1 = sprintf("%12s", $exclusivo_empresa1);
        fwrite($arquivo, $exclusivo_empresa1, 12);

        fwrite($arquivo, "\r\n");
    }

}
