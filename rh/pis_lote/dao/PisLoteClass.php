<?php

/*
 * class PisLoteClass
 * 
 * 00-00-0000
 * 
 * Classe de formatação de dados para exportação do PIS
 * 
 * Versão: 3.0.0000 - 00/00/0000 - Versão Beta da Classe baseada na versão 23.0 da geração do layout do NIS
 * Versão: 3.0.0000 - 16/07/2015 - Jacques - Correção de geração de arquivo com substituição da quebra de linha com \n para chr
 * Versão: 3.0.0000 - 29/07/2015 - Jacques - Correção - passando todas as strings para maiúsculas e alteração do tamanho do campo id_pais_nasc para 4 dígitos 
 *                                 com zeros a esquerda. Alteração do modo chr(13)+chr(10) para \r\n (quebra de linha linux e windows.
 * Versão: 3.0.0000 - 10/08/2015 - Jacques - Correção - Troca de posição entre os códigos de campos do Trailer Parcial e Geral pois estavam inconsistentes.
 * Versão: 3.0.0000 - 11/08/2015 - Jacques - Implementação - Eliminação da lista dos registros com erros para envio no lote do PIS. Adição de validação
 *                                 para funcionários com mais de um ano de registros ou com menos de um mês.     
 * Versão: 3.0.1712 - 26/08/2015 - Jacques - Correção - Desfeita a troca de posição do campo 0420 do Trailer Parcial para o Trailer Geral. Foi constatado 
 *                                 Um divergência de informação no próprio layout de padrão da caixa que informa que o campo 0420 deve estar no registro
 *                                 Trailer Parcial, mas nos exemplos de estrutura completa do próprio layout estão no Trailer Geral.   
 * Versão: 3.0.2163 - 14/09/2015 - Faltou remover a linha 0420 do trailer geral                  
 * 
 * @author Não Definido
 *  
 * 
 */

class PisLoteClass {

    public $dados;
    public $uploaddir; // define no __construct
    private $arr_cod_campos; // codigo dos campos
    public $debug = FALSE;
    private $date;

    public function __construct() {
        $this->uploaddir = $_SERVER['DOCUMENT_ROOT'] . '/intranet/rh/pis_lote/arquivos_caixa/';
        $this->arr_cod_campos = array(
            '00' => array(
                '0900' => array('00' => array('tipo' => 'c', 'tam' => 01, 'obrg' => TRUE)),
                '0829' => array('00' => array('tipo' => 'n', 'tam' => 14, 'obrg' => TRUE)),
                '0313' => array('00' => array('tipo' => 'c', 'tam' => 40, 'obrg' => TRUE)),
                '0413' => array('00' => array('tipo' => 'c', 'tam' => 01, 'obrg' => TRUE)),
                '0903' => array('00' => array('tipo' => 'n', 'tam' => 08, 'obrg' => TRUE)),
                '0913' => array('00' => array('tipo' => 'n', 'tam' => 04, 'obrg' => TRUE)),
            ),
            '01' => array(
                '0378' => array('00' => array('tipo' => 'n', 'tam' => 14, 'obrg' => FALSE)),
                '0379' => array('00' => array('tipo' => 'n', 'tam' => 12, 'obrg' => FALSE)),
                '0104' => array('00' => array('tipo' => 'c', 'tam' => 50, 'obrg' => FALSE)),
            ),
            '02' => array(
                '0902' => array('00' => array('tipo' => 'c', 'tam' => 01, 'obrg' => TRUE)),
                '0422' => array('00' => array('tipo' => 'n', 'tam' => 11, 'obrg' => FALSE)),
                '0195' => array('00' => array('tipo' => 'c', 'tam' => 70, 'obrg' => TRUE)),
                '0197' => array('00' => array('tipo' => 'n', 'tam' => 08, 'obrg' => TRUE)),
                '0200' => array('00' => array('tipo' => 'c', 'tam' => 70, 'obrg' => TRUE)),
                '0199' => array('00' => array('tipo' => 'c', 'tam' => 70, 'obrg' => TRUE)),
                '0390' => array('00' => array('tipo' => 'n', 'tam' => 07, 'obrg' => FALSE)),
                '0386' => array(
                    '00' => array('tipo' => 'n', 'tam' => 4, 'obrg' => TRUE),
                    '01' => array('tipo' => 'c', 'tam' => 1, 'obrg' => TRUE)
                ),
                '0370' => array('00' => array('tipo' => 'n', 'tam' => 11, 'obrg' => TRUE)),
                '0373' => array(
                    '00' => array('tipo' => 'n', 'tam' => 07, 'obrg' => TRUE),
                    '01' => array('tipo' => 'n', 'tam' => 05, 'obrg' => TRUE),
                    '02' => array('tipo' => 'c', 'tam' => 02, 'obrg' => TRUE),
                    '03' => array('tipo' => 'n', 'tam' => 08, 'obrg' => TRUE),
                ),
                '0911' => array(
                    '00' => array('tipo' => 'n', 'tam' => 08, 'obrg' => TRUE),
                    '01' => array('tipo' => 'c', 'tam' => 01, 'obrg' => TRUE),
                    '02' => array('tipo' => 'c', 'tam' => 03, 'obrg' => TRUE),
                    '03' => array('tipo' => 'c', 'tam' => 40, 'obrg' => TRUE),
                    '04' => array('tipo' => 'c', 'tam' => 05, 'obrg' => TRUE),
                    '05' => array('tipo' => 'c', 'tam' => 07, 'obrg' => TRUE),
                    '06' => array('tipo' => 'c', 'tam' => 15, 'obrg' => FALSE),
                    '07' => array('tipo' => 'c', 'tam' => 40, 'obrg' => TRUE),
                    '08' => array('tipo' => 'c', 'tam' => 07, 'obrg' => TRUE),
                ),
                '0292' => array(
                    '00' => array('tipo' => 'n', 'tam' => 07, 'obrg' => TRUE),
                    '01' => array('tipo' => 'n', 'tam' => 14, 'obrg' => TRUE),
                    '02' => array('tipo' => 'n', 'tam' => 08, 'obrg' => TRUE)
                ),
            ),
        );
    }

    private function queryArray($sql) {

        $result = mysql_query($sql); //or die(mysql_error()); // error comentado para não travar a tela. leonardo em 10/03/2015

        $arr = array();
        while ($row = mysql_fetch_assoc($result)) {
            if (!validaPIS($row['pis'])) {
                $arr[] = $row;
            }
        }

        return $arr;
    }

    function getRelacao($post_regiao, $post_projeto, $ids_negados = array()) {




        $where = (!empty($post_regiao) && $post_regiao > 0) ? " A.id_regiao = '$post_regiao' " : ' ';
        $where .= (!empty($post_projeto) && $post_projeto > 0) ? " AND A.id_projeto = '$post_projeto'" : ' ';
        $ids_negados = !empty($ids_negados) ? 'IF(A.id_clt IN(' . implode(',', array_unique($ids_negados)) . ' ),1,2 ) AS flag ' : '  2 AS flag ';

//        $dev = TRUE;
//        $where_pis = ($dev) ? '' :  ' AND (A.pis IS NULL OR CHAR_LENGTH(A.pis)<=0)';
        $where_pis = ''; // retirado da query e tratando no php

        $sql = "SELECT $ids_negados, 
                    A.id_clt, 
                    D.nome AS unidade, 
                    A.nome, 
                    A.pis,
                    DATE_FORMAT(A.dtChegadaPais,'%d%m%Y') AS dtChegadaPais_f,
                    J.cod_pais_pis AS cod_pais_nasc, 
                    DATE_FORMAT(A.data_nasci, '%d%m%Y') as data_nasci_f, 
                    UCASE(A.mae) mae, 
                    UCASE(A.pai) pai, 
                    A.cpf, 
                    A.campo1 AS ctps, 
                    A.serie_ctps, 
                    UCASE(A.uf_ctps) AS uf_ctps, 
                    DATE_FORMAT(A.data_ctps,'%d%m%Y') AS data_ctps,
                    DATE_FORMAT(A.data_entrada, '%d%m%Y') AS data_entrada_f, 
                    DATE_FORMAT(A.data_entrada, '%d/%m/%Y') AS data_entrada_br,  
                    UCASE(E.nome) AS funcao, 
                    UCASE(F.especifica) AS especifica, 
                    A.id_projeto, 
                    G.id_empresa, 
                    UCASE(G.nome) AS nome_empresa, 
                    G.cnpj,  
                    H.codigo AS cod_nascionalidade, 
                    IF(H.codigo=10,1,IF(H.codigo=20,2,0)) AS detalhamento_nascionalidade, 
                    IF(H.codigo=10,I.cod_1,'NA') AS cod_municipio, 
                    A.cep, 
                    UCASE(A.tipo_endereco) AS tipo_endereco, 
                    UCASE(A.endereco) AS endereco, 
                    A.numero, 
                    UCASE(A.complemento) AS complemento, 
                    UCASE(A.bairro) AS bairro, 
                    (SELECT cod_1 FROM municipios WHERE id_municipio=A.id_municipio_end) AS cod_municipio_end             
                    #(SELECT cod_1 FROM municipios WHERE id_municipio=A.id_municipio_nasc) AS cod_municipio_nascimento
            FROM rh_clt as A
                LEFT JOIN projeto as D ON (D.id_projeto = A.id_projeto)
                INNER JOIN curso as E ON (E.id_curso = A.id_curso)
                LEFT JOIN rhstatus AS F ON (F.codigo = A.status)
                LEFT JOIN rhempresa AS G ON(G.id_projeto=D.id_projeto)
                LEFT JOIN cod_pais_rais H ON(A.id_pais_nacionalidade=H.id)
                LEFT JOIN municipios AS I ON(A.id_municipio_nasc=I.id_municipio)
                LEFT JOIN paises AS J ON (A.id_pais_nasc = J.id_pais)
            WHERE $where_pis $where ORDER BY A.id_projeto,A.nome";

//        print_array($sql);

        return $this->queryArray($sql);
    }

    public function montarArquivoCompleto($arr_relacao, $master) {


        $query_master = "SELECT * FROM master WHERE id_master = $master";
        $result_master = mysql_query($query_master);
        $arr_master = mysql_fetch_assoc($result_master);

        $cont = 1;

        // 00 - HEADER GERAL 
        $tipo_arquivo = 'C'; // C = Cadastro
        $cnpj = $arr_master['cnpj'];
        $nome_fantasia = $arr_master['nome'];
        $tipo_remessa = 'O'; // O = "original", R = "retificadora"
        $data_remessa = date('dmY');
        $codigo_processo = '0003'; // 0003 = estrutura completa, 0013 = simplificada

        $arr_h = array('0900' => $tipo_arquivo, '0829' => $cnpj, '0313' => $nome_fantasia, '0413' => $tipo_remessa, '0903' => $data_remessa, '0913' => $codigo_processo);

        $cont_header = 0;
        foreach ($arr_h as $k => $v) {
            $this->addDados(FormataDadosClass::campoNumerico($cont, 11)); // num ordem fisica
            $this->addDados(FormataDadosClass::campoNumerico(1, 18)); // código da origem
            $this->addDados(FormataDadosClass::campoNumerico(0, 11)); // nis
            // REGISTRO LÓGICO
            $this->addDados('00'); // tipo de Registro
            $this->addDados(FormataDadosClass::campoNumerico($cont_header, 3)); // sequencial do registro
            // CAMPO
            $this->addDados(FormataDadosClass::campoNumerico($k, 4)); // código do campo
            $this->addDados(FormataDadosClass::campoNumerico(0, 2)); // sequencial do campo
            $this->addDados(FormataDadosClass::campoNumerico(0, 2)); // flag do campo
            $this->addDados(FormataDadosClass::campoTexto($v, 180)); // conteúdo do campo
            // FILTER
            $this->addDados(FormataDadosClass::campoTexto(' ', 32)); // espaço reservado para CAIXA
            // RETORNO
            $this->addDados(FormataDadosClass::campoNumerico(0, 11)); // NIS ativo
            $this->addDados(FormataDadosClass::campoNumerico(0, 4)); // Retorno do Processamento
            $this->quebraLinha();
            $cont_header++;
            $cont++;
        }
        // FIM DO HEADER GERAL



        $cont_detalhe = 1;


        $nome_header_parcial = NULL;
        $cnpj_header_parcial = NULL;
        $nome_trailer_parcial = NULL;
        $cnpj_trailer_parcial = NULL;
        $total_empresa = 0;


        foreach ($arr_relacao as $row) {

            if ($row['flag'] == 2) {


                // TRAILER PARCIAL

                if ($row['cnpj'] != $cnpj_trailer_parcial && $cnpj_header_parcial != NULL) {
                    $total_empresa++; // somatorio no final
                    $cnpj = FormataDadosClass::campoTexto($cnpj_trailer_parcial, 180);

                    // @jacques - 10/08/2015 -

                    $arr_f = array('0378' => $cnpj, // Nome do Arquivo
                        '0912' => FormataDadosClass::campoNumerico($cont_parcial + 1, 9) // Total de registros
                    );



                    // @jacques - 10/08/2015 -
                    // $arr_f = array('0378' => $cnpj, // Nome do Arquivo
                    //                '0912' => FormataDadosClass::campoNumerico($cont_parcial + 1, 9)); // Total de Registros

                    $cont_f = 0;
                    foreach ($arr_f as $k_f => $v_f) {

                        $this->addDados(FormataDadosClass::campoNumerico($cont, 11)); // num ordem
                        $this->addDados(FormataDadosClass::campoNumerico(1, 18)); // cod origem
                        $this->addDados(FormataDadosClass::campoNumerico(9, 11, 9)); // nis
                        $this->addDados('98'); // tp registro
                        $this->addDados(FormataDadosClass::campoNumerico($cont_f, 3)); // seq registro
                        $this->addDados(FormataDadosClass::campoNumerico($k_f, 4)); // cod campo
                        $this->addDados('00'); // seq. footer
                        $this->addDados('00'); // flag campo
                        $this->addDados(str_pad($v_f, 180, ' ')); // conteudo campo
                        $this->addDados(FormataDadosClass::campoTexto(' ', 32)); // espaço reservado para CAIXA
                        $this->addDados(FormataDadosClass::campoNumerico(0, 11)); // NIS ativo
                        $this->addDados(FormataDadosClass::campoNumerico(0, 4)); // retorno proc
//                        if ($cont_f < 1) {
                        $this->quebraLinha();
//                        }
                        $cont_f++;
                        $cont++;

//                        $nome_trailer_parcial = $row['nome_empresa'];
//                        $cnpj_trailer_parcial = $row['cnpj'];
                    }
                }

                // TRAILER PARCIAL
                // 01 - HEADER PARCIAL 
                if ($row['cnpj'] != $cnpj_header_parcial) {

                    $cont_parcial = 1;

//                    $tipo_arquivo = 'C'; // C = Cadastro
                    $cnpj = $row['cnpj'];
                    $nome_fantasia = $row['nome_empresa'];
//                    $tipo_remessa = 'O'; // O = "original", R = "retificadora"
//                    $data_remessa = date('dmY');
//                    $codigo_processo = '0003'; // 0003 = estrutura completa, 0013 = simplificada
//                    $arr_h = array('0900' => $tipo_arquivo, '0829' => $cnpj, '0313' => $nome_fantasia, '0413' => $tipo_remessa, '0903' => $data_remessa, '0913' => $codigo_processo);
                    $arr_h = array('0378' => $cnpj, '0104' => $nome_fantasia);

                    $cont_header = 0;
                    foreach ($arr_h as $k => $v) {
                        $this->addDados(FormataDadosClass::campoNumerico($cont, 11)); // num ordem fisica
                        $this->addDados(FormataDadosClass::campoNumerico(1, 18)); // código da origem
                        $this->addDados(FormataDadosClass::campoNumerico(0, 11)); // nis
                        // REGISTRO LÓGICO
                        $this->addDados('01'); // tipo de Registro
                        $this->addDados(FormataDadosClass::campoNumerico($cont_header, 3)); // sequencial do registro
                        // CAMPO
                        $this->addDados(FormataDadosClass::campoNumerico($k, 4)); // código do campo
                        $this->addDados(FormataDadosClass::campoNumerico(0, 2)); // sequencial do campo
                        $this->addDados(FormataDadosClass::campoNumerico(0, 2)); // flag do campo
                        $this->addDados(FormataDadosClass::campoTexto($v, 180)); // conteúdo do campo
                        // FILTER
                        $this->addDados(FormataDadosClass::campoTexto(' ', 32)); // espaço reservado para CAIXA
                        // RETORNO
                        $this->addDados(FormataDadosClass::campoNumerico(0, 11)); // NIS ativo
                        $this->addDados(FormataDadosClass::campoNumerico(0, 4)); // Retorno do Processamento
                        $this->quebraLinha();
                        $cont_header++;
                        $cont++;
                        $cont_parcial++;
                    }

                    $nome_header_parcial = $row['nome_empresa'];
                    $cnpj_header_parcial = $row['cnpj'];
                    $cnpj_trailer_parcial = $row['cnpj'];
                }
                // FIM DO HEADER PARCAL
                // para corrigir possiveis erros
                $numero = (!empty($row['numero'])) ? $row['numero'] : 'SN';
                $tipo_endereco = $this->tipoLogradouro($row['endereco'], $row['tipo_endereco']);

                $nome_mae = (!empty($row['mae'])) ? $row['mae'] : 'IGNORADA';
                $nome_pai = (!empty($row['pai'])) ? $row['pai'] : 'IGNORADO';

                $arr_r = array('0902' => array('I'), //  Tipo de operação I = Inclusão
                    '0422' => array($row['id_clt']), // controle da empresa  - o id_clt para facilitar na importação do arquivo
                    '0195' => array($row['nome']), // Nome da pessoa
                    '0197' => array($row['data_nasci_f']), // Data de Nascimeto
                    '0200' => array($nome_mae), // Nome da Mãe
                    '0199' => array($nome_pai), // Nome do Pai
                    '0386' => array(FormataDadosClass::campoNumerico($row['cod_nascionalidade'], 4), $row['detalhamento_nascionalidade']), // Nacionalidade 10 - brasileira
                    '0370' => array($row['cpf']), // CPF
                    '0373' => array(FormataDadosClass::campoNumerico($row['ctps'], 7), FormataDadosClass::campoNumerico($row['serie_ctps'], 5), $row['uf_ctps'], $row['data_ctps']), // CTPS
                    '0911' => array($row['cep'], 1, $tipo_endereco, $row['endereco'], 'Num.', $numero, $row['complemento'], $row['bairro'], $row['cod_municipio_end']), // Endereco
                    '0292' => array('59', $row['cnpj'], $row['data_entrada_f']) // Dados de Vinculo
                );
                if ($row['cod_nascionalidade'] == 10) {
                    $arr_r['0390'] = array($row['cod_municipio']); // cod UF/municipio IBGE
                } else {
                    $arr_r['0387'] = array($row['cod_pais_nasc']); // País de Origem
                    $arr_r['0391'] = array($row['dtChegadaPais_f']); // data chegada
                    if ($row['detalhamento_nascionalidade'] == 2) {
                        $arr_r['0401'] = array(' '); // portaria de naturalização <------------------------------------------------
                        $arr_r['0815'] = array('00000000'); // data naturalizacao <------------------------------------------------
                    }
                }
                $cont_f = 0;
                foreach ($arr_r as $k => $arr_v) {
                    foreach ($arr_v as $sequencia_campo => $valor_campo) {

                        $this->addDados(FormataDadosClass::campoNumerico($cont, 11)); // num ordem fisica
                        $this->addDados(FormataDadosClass::campoNumerico(1, 18)); // código da origem
                        $this->addDados(FormataDadosClass::campoNumerico(0, 11)); // nis
                        // REGISTRO LÓGICO
                        $this->addDados(FormataDadosClass::campoNumerico(02, 2)); // tipo de Registro
                        $this->addDados(FormataDadosClass::campoNumerico($cont_f, 3)); // sequencial do registro
                        // CAMPO
                        $this->addDados(FormataDadosClass::campoNumerico($k, 4)); // código do campo
                        $this->addDados(FormataDadosClass::campoNumerico($sequencia_campo, 2)); // sequencial do campo
                        $this->addDados(FormataDadosClass::campoNumerico(0, 2)); // flag do campo
                        $this->addDados(FormataDadosClass::campoTexto($valor_campo, 180)); // conteúdo do campo
                        // FILTER
                        $this->addDados(FormataDadosClass::campoTexto(' ', 32)); // espaço reservado para CAIXA
                        // RETORNO
                        $this->addDados(FormataDadosClass::campoNumerico(0, 11)); // NIS ativo
                        $this->addDados(FormataDadosClass::campoNumerico(0, 4)); // Retorno do Processamento
                        $this->quebraLinha();
                        $cont_detalhe++;
                        $cont++;
                        $cont_parcial++;
                        $cont_f++;
                    }
                }
            }
        }

        // TRAILER PARCIAL
//                if ($row['cnpj'] != $cnpj_trailer_parcial && $cnpj_header_parcial != NULL) {
        $total_empresa++; // somatorio no final
        $cnpj = FormataDadosClass::campoTexto($cnpj_trailer_parcial, 180);

//        $arr_f = array('0378' => $cnpj, // Nome do Arquivo
//            '0912' => FormataDadosClass::campoNumerico($cont_parcial + 1, 9)); // Total de Registros

        $arr_f = array('0378' => $cnpj, // Nome do Arquivo
            '0912' => FormataDadosClass::campoNumerico($cont_parcial + 1, 9) // Total de registros
        );

        $cont_f = 0;

        foreach ($arr_f as $k_f => $v_f) {

            $this->addDados(FormataDadosClass::campoNumerico($cont, 11)); // num ordem
            $this->addDados(FormataDadosClass::campoNumerico(1, 18)); // cod origem
            $this->addDados(FormataDadosClass::campoNumerico(9, 11, 9)); // nis
            $this->addDados('98'); // tp registro
            $this->addDados(FormataDadosClass::campoNumerico($cont_f, 3)); // seq registro
            $this->addDados(FormataDadosClass::campoNumerico($k_f, 4)); // cod campo
            $this->addDados('00'); // seq. footer
            $this->addDados('00'); // flag campo
            $this->addDados(str_pad($v_f, 180, ' ')); // conteudo campo
            $this->addDados(FormataDadosClass::campoTexto(' ', 32)); // espaço reservado para CAIXA
            $this->addDados(FormataDadosClass::campoNumerico(0, 11)); // NIS ativo
            $this->addDados(FormataDadosClass::campoNumerico(0, 4)); // retorno proc
//                        if ($cont_f < 1) {
            $this->quebraLinha();
//                        }
            $cont_f++;
            $cont++;

//                        $nome_trailer_parcial = $row['nome_empresa'];
//                        $cnpj_trailer_parcial = $row['cnpj'];
        }
//                }
        // TRAILER PARCIAL
        //trailer final            
        $nome_arquivo = 'CADASTRONIS.D' . date('ymd_His_') . '.S01';

        $arr_nome = explode('_', $nome_arquivo);
        $nome_arquivo_2 = $arr_nome[0] . $arr_nome[2];

        $arr_f = array(//'0912' => FormataDadosClass::campoNumerico($cont + 2, 9),
            '0420' => FormataDadosClass::campoNumerico($total_empresa, 9), // Total de Empresas
            '0908' => $nome_arquivo_2 // Nome do Arquivo
        ); // Total de Registros (+ 2 por vai passar por 2 loops ainda)
//        $arr_f = array('0908' => $nome_arquivo_2, // Nome do Arquivo
//            '0420' => FormataDadosClass::campoNumerico($total_empresa, 9), // Total de Empresas
//            '0912' => FormataDadosClass::campoNumerico($cont + 2, 9)); // Total de Registros (+ 2 por vai passar por 2 loops ainda)


        $cont_f = 0;

        foreach ($arr_f as $k_f => $v_f) {

//            var_dump($v_f);

            $this->addDados(FormataDadosClass::campoNumerico($cont, 11)); // num ordem
            $this->addDados(FormataDadosClass::campoNumerico(1, 18)); // cod origem
            $this->addDados(FormataDadosClass::campoNumerico(9, 11, 9)); // nis
            $this->addDados('99'); // tp registro
            $this->addDados(FormataDadosClass::campoNumerico($cont_f, 3)); // seq registro
            $this->addDados(FormataDadosClass::campoNumerico($k_f, 4)); // cod campo
            $this->addDados('00'); // seq. footer
            $this->addDados('00'); // flag campo
            $this->addDados(str_pad($v_f, 180, ' ')); // conteudo campo
            $this->addDados(FormataDadosClass::campoTexto(' ', 32)); // espaço reservado para CAIXA
            $this->addDados(FormataDadosClass::campoNumerico(0, 11)); // NIS ativo
            $this->addDados(FormataDadosClass::campoNumerico(0, 4)); // retorno proc
//            if ($cont_f < 1) {
            $this->quebraLinha();
//            }
            $cont_f++;
            $cont++;
        }
        //trailer final


        return $this->criaArquivo($nome_arquivo, $nome_arquivo_2);
    }

    public function criaArquivo($nome_arquivo, $nome_arquivo_2) {

        $folder = 'arquivos/';

//        $name = 'CADASTRONIS.'.date('YmdH_is').'.S01';

        $path = $folder . $nome_arquivo;

        $file = fopen($path, 'w+');
        fwrite($file, $this->getDados());
        fclose($file);


        return array('download' => $nome_arquivo, 'name_file' => $nome_arquivo_2);
    }

    function addDados($var) {
        $this->dados .= $var;
        return $this;
    }

    function quebraLinha($quebra = "\r\n") {
        $this->dados .= $quebra;
        return $this;
    }

    public function getDados() {
        return substr($this->dados, 0, strlen($this->dados) - 2);
    }

    //--------------------------------------------------------------------------

    /*
     * Transforma o arquivo enviado pela CAIXA em um array
     */
    public function passFileToArray($nameFile) {


        $handle = fopen($this->uploaddir . $nameFile, "r");
        if ($handle) {
            while (!feof($handle)) {
                $buffer = fgets($handle, 4096);
                $arr[] = array(
                    'numOrdem' => substr($buffer, 0, 11),
                    'CodOrigem' => substr($buffer, 11, 18),
                    'NIS' => substr($buffer, 29, 11),
                    'TipoRegLogico' => substr($buffer, 40, 2),
                    'SeqRegLogico' => substr($buffer, 42, 3),
                    'CodCampo' => substr($buffer, 45, 4),
                    'SeqCampo' => substr($buffer, 49, 2),
                    'FlagCampo' => substr($buffer, 51, 2),
                    'conteudoCampo' => substr($buffer, 53, 180),
                    'NISAtivo' => substr($buffer, 265, 11),
                    'COdRetorno' => substr($buffer, 276, 4),
                );
            }
            fclose($handle);
        }
        return (isset($arr)) ? $arr : FALSE;
    }

    /*
     * Transforma o arquivo em um array cujas linhas são os clts
     */

    public function passFileToArrayClt($nameFile) {
        $arr_header = array(
            '0900' => 'tipoArq',
            '0829' => 'cnpjMaster',
            '0313' => 'nomeMaster',
            '0413' => 'tipoRemeca',
            '0903' => 'data',
            '0913' => 'codProc'
        );
        $arr_header_par = array(
            '0378' => 'cnpjProj',
            '0379' => 'ceiProj',
            '0104' => 'nomeProj',
        );
        $arr_reg = array(
            '0902' => 'tipoOp',
//            '0418'=>'nisAtendente',
//            '0419'=>'cpfAtendente', // ate agora descecessário
            '0422' => 'id_clt', // controle da empresa
            '0195' => 'nome',
            '0197' => 'data_nasci',
            '0200' => 'mae',
            '0199' => 'pai',
            '0390' => 'munNasc',
            '0201' => 'sexo',
            '0370' => 'cpf',
                // outras linhas a acrescentar se nescessario
        );

//        @Jacques - 10/08/2015 - Campo dos vetores divergentes do layout do arquivo de geração em lote do pis versão 23.0
//        $arr_traler_par = array(
//            '0378' => 'cnpj', 
//            '0912' => 'totalRegPar',
//        );
//        $arr_traler = array(
//            '0908' => 'nomeArq',
//            '0420' => 'totalEmpr',
//            '0912' => 'toralReg',
//        );
//        @Jacques - 10/08/2015 - Correção estrutural dos campos referentes ao layout do arquivo de geração em lote do pis versão 23.0
        $arr_traler_par = array(
            '0378' => 'cnpj',
            '0912' => 'totalRegPar',
            '0420' => 'totalEmpr'
        );
        $arr_traler = array(
            '0912' => 'toralReg',
            '0908' => 'nomeArq',
        );

        $handle = fopen($this->uploaddir . $nameFile, "r");
        $array = array();
        if ($handle) {
            $i = 0;
            while (!feof($handle)) {
                $buffer = fgets($handle, 4096);

                $tipoReg = substr($buffer, 40, 2);
                $codCampo = substr($buffer, 45, 4);
                $conteudo = substr($buffer, 53, 180);

                if ($tipoReg == 00) {
                    $array['empresa'][$arr_header[$codCampo]] = trim($conteudo);
                } else if ($tipoReg == 01) {

                    $j = 0;
                    $array['projeto'][$i][$arr_header_par[$codCampo]] = trim($conteudo);
                } else if ($tipoReg == 02) {
                    $j = ($codCampo == '0902') ? $j + 1 : $j; // muda pessoa
                    $array['projeto'][$i]['clt'][$j][$arr_reg[$codCampo]] = trim($conteudo);
                    $array['projeto'][$i]['clt'][$j]['novoPIS'] = substr($buffer, 265, 11);
                    $erro = substr($buffer, 276, 4);
                    if ($erro != "0000") {
                        $array['projeto'][$i]['clt'][$j]['erro'][] = $erro;
                    }
                } else if ($tipoReg == 98) {
                    $array['projeto'][$i][$arr_traler_par[$codCampo]] = $conteudo;
                    $i = ($codCampo == '0912') ? $i + 1 : $i; // muda projeto
                } else if ($tipoReg == 99) {
                    $array[$arr_traler[$codCampo]] = $conteudo;
                }
            }
            fclose($handle);
        }

        return (!empty($array)) ? $array : FALSE;
    }

    /*
     * Atualiza os PIS dos CLTs
     */

    public function atualizaPIS($id_clt, $novo_pis) {
        $query = "UPDATE rh_clt SET pis = '$novo_pis' WHERE id_clt = $id_clt";
        $result = mysql_query($query) or die("Erro ao atualizar pis.<br>Query: $query<br>" . mysql_error());
        return ($result) ? TRUE : FALSE;
    }

    public function moverArquivo($file_name, $file_tmp_name) {

        $uploadfile = $this->uploaddir . basename($file_name);

//        if (move_uploaded_file($file_tmp_name, $uploadfile)) {
        if (move_uploaded_file($file_tmp_name, $uploadfile)) {
            $arr = array('status' => TRUE, 'msg' => "Arquivo válido e enviado com sucesso.\n");
        } else {
            $arr = array('status' => FALSE, 'msg' => "Possível ataque de upload de arquivo!\n");
        }

//        echo 'Aqui está mais informações de debug:';
//        print_r($_FILES);

        return $arr;
    }

    public function tipoLogradouro($endereco, $tipoLogradouro) {
        if (!empty($tipoLogradouro)) {
            return $tipoLogradouro;
        }
        $array = array(
            'rua' => 'R',
            'avenida' => 'AV',
            'estrada' => 'EST',
            'rodovia' => 'ROD',
            'travessa' => 'TV'
        );

        $return = '';
        foreach ($array as $key => $value) {
            if (strripos($key, $endereco) !== FALSE) {
                $return = $value;
            }
        }
        return (empty($return)) ? 'R' : $return;
    }

    public function preparaArrayLinha($row) {
        $nome_mae = (!empty($row['mae'])) ? $row['mae'] : 'IGNORADA';
        $nome_pai = (!empty($row['pai'])) ? $row['pai'] : 'IGNORADO';

        $arr_r = array(
            '0902' => array('I'), //  Tipo de operação I = Inclusão
            '0422' => array($row['id_clt']), // controle da empresa  - o id_clt para facilitar na importação do arquivo
            '0195' => array($row['nome']), // Nome da pessoa
            '0197' => array($row['data_nasci_f']), // Data de Nascimeto
            '0200' => array($nome_mae), // Nome da Mãe
            '0199' => array($nome_pai), // Nome do Pai
//            '0390' => array($row['cod_municipio']), // cod UF/municipio IBGE
            '0386' => array($row['cod_nascionalidade'], $row['detalhamento_nascionalidade']), // Nacionalidade 10 - brasileira
            '0370' => array($row['cpf']), // CPF
            '0373' => array($row['ctps'], $row['serie_ctps'], $row['uf_ctps'], $row['data_ctps']), // CTPS
            '0911' => array($row['cep'], 1, 'RUA', $row['endereco'], 'Num.', $row['numero'], $row['complemento'], $row['bairro'], $row['cod_municipio_end']), // Endereco
            '0292' => array('59', $row['cnpj'], $row['data_entrada_f']), // Dados de Vinculo
            'error' => 0
        );
        if ($row['cod_nascionalidade'] == 10) {
            $arr_r['0390'] = array($row['cod_municipio']); // cod UF/municipio IBGE
        } else {
            $arr_r['0387'] = array($row['cod_pais_nasc']); // País de Origem
            $arr_r['0391'] = array($row['dtChegadaPais_f']); // data chegada
            if ($row['detalhamento_nascionalidade'] == 2) {
                $arr_r['0401'] = array(' '); // portaria de naturalização <------------------------------------------------
                $arr_r['0815'] = array('00000000'); // data naturalizacao <------------------------------------------------
            }
        }
        return $arr_r;
    }

    public function verificaErro($arr) {

        if (!isset($this->date)) {

            include_once($_SERVER['DOCUMENT_ROOT'] . '/intranet/classes/DateClass.php');

            $this->date = new DateClass();
        }

        $cod_campo = array_keys($this->arr_cod_campos['02']);

        $ids_erro = [];
        foreach ($arr as $key => $clt) {
            $row = $this->preparaArrayLinha($clt);

            foreach ($cod_campo as $value) {
                for ($i = 0; $i < count($this->arr_cod_campos['02'][$value]); $i++) {
                    $id = str_pad($i, 2, 0, STR_PAD_LEFT);
                    $valida1 = $this->arr_cod_campos['02'][$value][$id]['obrg'];

                    $valida2 = strlen($row[$value][$i]) == 0 && empty($row[$value][$i]);

                    // @jacques - Validação de funcionários com registros com mais de um ano de casa ou menos de um mês

                    $data = substr($row['0292']['2'], 0, 2) . '/' . substr($row['0292']['2'], 2, 2) . '/' . substr($row['0292']['2'], 4, 4);

                    $dias = $this->date->calculaIntervalo($data, '', 'dias');

                    $valida3 = ($dias->val() > 365 || $dias->val() < 30);

                    if (($valida1 && $valida2) || $valida3) {
                        $ids_erro[] = $clt['id_clt'];
                    }
                }
            }
        }
        return $ids_erro;
    }

    public function consultaErro($dados = array(), $order_by = '', $limit = '') {
        $gravidade = array('A' => 'Arquivo/Lote rejeitado', 'G' => 'Erro de sistema', 'I' => 'Mensagem informativa', 'N' => 'Dado rejeitado', 'O' => 'Operação rejeitada', 'P' => 'Validar perfil do usuário', 'Q' => 'Dado apropriado com inconsistência', 'R' => 'Registro rejeitado.');
        if (!is_array($dados)) {
            $where = (empty($dados)) ? '' : $dados;
        } else {
            array_filter($dados); //limpa campos vazios
            $cond[] = "status = 1";
            foreach ($dados as $key => $value) {
                $cond[] = "$key = '$value'";
            }
            $where = (!empty($cond)) ? "WHERE " . implode(' AND ', $cond) : '';
        }
        $query = "SELECT * FROM erros_pis WHERE $where $order_by $limit";

        if (!$this->debug) {
            $result = mysql_query($query) or die("Erro ao consultar<br> Query: $query<br>" . mysql_error());
            while ($row = mysql_fetch_assoc($result)) {
                $valores[$row['id']] = $row;
                $valores[$row['id']]['gravidade'] = $gravidade[$row['gravidade']];
            }
            $return = ($result) ? $valores : FALSE;
        } else {
            echo $query;
        }
        return $return;
    }

//    public function montarArquivo($arr_relacao) {
//
//
//        $cont = 1;
//
//        // 00 - HEADER GERAL 
//        $tipo_arquivo = 'C'; // C = Cadastro
//        $cnpj = $arr_relacao[0]['cnpj'];
//        $nome_fantasia = $arr_relacao[0]['nome_empresa'];
//        $tipo_remessa = 'O'; // O = "original", R = "retificadora"
//        $data_remessa = date('dmY');
//        $codigo_processo = '0013'; // 0003 = estrutura completa, 0013 = simplificada
//
//        $arr_h = array('0900' => $tipo_arquivo, '0829' => $cnpj, '0313' => $nome_fantasia, '0413' => $tipo_remessa, '0903' => $data_remessa, '0913' => $codigo_processo);
//
//        $cont_header = 0;
//        foreach ($arr_h as $k => $v) {
//            $this->addDados(FormataDadosClass::campoNumerico($cont, 11)); // num ordem fisica
//            $this->addDados(FormataDadosClass::campoNumerico(1, 18)); // código da origem
//            $this->addDados(FormataDadosClass::campoNumerico(0, 11)); // nis
//            // REGISTRO LÓGICO
//            $this->addDados('00'); // tipo de Registro
//            $this->addDados(FormataDadosClass::campoNumerico($cont_header, 3)); // sequencial do registro
//            // CAMPO
//            $this->addDados(FormataDadosClass::campoNumerico($k, 4)); // código do campo
//            $this->addDados(FormataDadosClass::campoNumerico(0, 2)); // sequencial do campo
//            $this->addDados(FormataDadosClass::campoNumerico(0, 2)); // flag do campo
//            $this->addDados(FormataDadosClass::campoTexto($v, 180)); // conteúdo do campo
//            // FILTER
//            $this->addDados(FormataDadosClass::campoTexto(' ', 32)); // espaço reservado para CAIXA
//            // RETORNO
//            $this->addDados(FormataDadosClass::campoNumerico(0, 11)); // NIS ativo
//            $this->addDados(FormataDadosClass::campoNumerico(0, 4)); // Retorno do Processamento
//            $this->quebraLinha();
//            $cont_header++;
//            $cont++;
//        }
//        // FIM DO HEADER GERAL
//
//
//        $cont_detalhe = 0;
//
//        foreach ($arr_relacao as $row) {
//
//            if ($row['flag'] == 2) {
//
//                $numero = (!empty($row['numero'])) ? $row['numero'] : 'SN';
//
//                $arr_r = array('0902' => array('I'), //  Tipo de operação I = Inclusão
//                    '0195' => array($row['nome']), // Nome da pessoa
//                    '0197' => array($row['data_nasci_f']), // Data de Nascimeto
//                    '0200' => array($row['mae']), // Nome da Mãe
//                    '0199' => array($row['pai']), // Nome do Pai
//                    '0390' => array($row['cod_municipio']), // cod UF/municipio IBGE
//                    '0386' => array($row['cod_nascionalidade'], $row['detalhamento_nascionalidade']), // Nacionalidade 10 - brasileira
//                    '0370' => array($row['cpf']), // CPF
//                    '0373' => array($row['ctps'], $row['serie_ctps'], $row['uf_ctps'], $row['data_ctps']), // CTPS
//                    '0911' => array($row['cep'], 1, $row['tipo_endereco'], $row['endereco'], 'Num.', $numero, $row['complemento'], $row['bairro'], $row['cod_municipio_nascimento']), // Endereco
//                    '0292' => array('59', $row['cnpj'], $row['data_entrada_f']) // Dados de Vinculo
//                );
//
//                foreach ($arr_r as $k => $arr_v) {
//                    foreach ($arr_v as $sequencia_campo => $valor_campo) {
//
//                        $this->addDados(FormataDadosClass::campoNumerico($cont, 11)); // num ordem fisica
//                        $this->addDados(FormataDadosClass::campoNumerico(1, 18)); // código da origem
//                        $this->addDados(FormataDadosClass::campoNumerico(0, 11)); // nis
//                        // REGISTRO LÓGICO
//                        $this->addDados(FormataDadosClass::campoNumerico(02, 2)); // tipo de Registro
//                        $this->addDados(FormataDadosClass::campoNumerico($cont_detalhe, 3)); // sequencial do registro
//                        // CAMPO
//                        $this->addDados(FormataDadosClass::campoNumerico($k, 4)); // código do campo
//                        $this->addDados(FormataDadosClass::campoNumerico($sequencia_campo, 2)); // sequencial do campo
//                        $this->addDados(FormataDadosClass::campoNumerico(0, 2)); // flag do campo
//                        $this->addDados(FormataDadosClass::campoTexto($valor_campo, 180)); // conteúdo do campo
//                        // FILTER
//                        $this->addDados(FormataDadosClass::campoTexto(' ', 32)); // espaço reservado para CAIXA
//                        // RETORNO
//                        $this->addDados(FormataDadosClass::campoNumerico(0, 11)); // NIS ativo
//                        $this->addDados(FormataDadosClass::campoNumerico(0, 4)); // Retorno do Processamento
//                        $this->quebraLinha();
//                        $cont_detalhe++;
//                        $cont++;
//                    }
//                }
//            }
//        }
//
//
//        $nome_arquivo = 'CADASTRONIS.D' . date('ymd_His_') . '.S01';
//
//        $arr_nome = explode('_', $nome_arquivo);
//        $nome_arquivo_2 = $arr_nome[0] . $arr_nome[2];
//
//        $arr_f = array('0908' => $nome_arquivo_2, // Nome do Arquivo
////                            '0420'=> array(), // Total de Empresas
//            '0912' => FormataDadosClass::campoNumerico($cont, 11)); // Total de Registros
//        $cont_f = 0;
//        foreach ($arr_f as $k_f => $v_f) {
//
////            var_dump($v_f);
//
//            $this->addDados(FormataDadosClass::campoNumerico($cont, 11)); // num ordem
//            $this->addDados(FormataDadosClass::campoNumerico(1, 18)); // cod origem
//            $this->addDados(FormataDadosClass::campoNumerico(9, 18, 9)); // nis
//            $this->addDados('99'); // tp registro
//            $this->addDados(FormataDadosClass::campoNumerico($cont_f, 3)); // seq registro
//            $this->addDados(FormataDadosClass::campoNumerico($k_f, 4)); // cod campo
//            $this->addDados('00'); // seq. footer
//            $this->addDados('00'); // flag campo
//            $this->addDados(str_pad($v_f, 180, ' ')); // conteudo campo
//            $this->addDados(FormataDadosClass::campoTexto(' ', 32)); // espaço reservado para CAIXA
//            $this->addDados(FormataDadosClass::campoNumerico(0, 11)); // NIS ativo
//            $this->addDados(FormataDadosClass::campoNumerico(0, 4)); // retorno proc
//
//            if ($cont_f < 1) {
//                $this->quebraLinha();
//            }
//            $cont_f++;
//            $cont++;
//        }
////            exit();
//
//
//        return $this->criaArquivo($nome_arquivo, $nome_arquivo_2);
//    }
}
