<?php

include('../../conn.php');
include('../../funcoes.php');
include('../../classes/RescisaoClass.php');
include('../../classes/clt.php');
include('../../classes/abreviacao.php');
include('../../classes/formato_valor.php');
include('../../classes/formato_data.php');
include('../../classes/valor_proporcional.php');
include('classes/MovimentoRescisaoClass.php');

function printHelper($var) {
    echo '<pre>';
    print_r($var);
    echo '</pre>';
}

function getClt($id_clt) {
    $sql = "SELECT A.pis, A.nome AS nome_funcionario, A.endereco AS endereco_funcionario, A.bairro AS bairro_funcionario, A.cidade AS cidade_funcionario, A.uf AS uf_funcionario, A.cep AS cep_funcionario, A.campo1 AS numero_ctps, A.serie_ctps, A.uf_ctps, A.cpf, DATE_FORMAT(A.data_nasci, '%d/%m/%Y') AS data_nascimento, A.mae,
            C.logradouro AS logradouro_empresa, C.complemento AS complemento_empresa, C.bairro AS bairro_empresa, A.tipo_contrato, IF(A.tipo_contrato=1,'1. Contrato de Trabalho por Prazo Indeterminado',IF(A.tipo_contrato=2,'2. Contrato de Trabalho por Prazo Determinado', IF(A.tipo_contrato=3,'3. Contrato de Trabalho Temporário','Contrato de trabalho não especificado'))) AS nome_tipo_contrato,
            C.cidade AS cidade_empresa, C.uf AS uf_empresa, C.numero AS numero_empresa, C.cnpj, C.razao, C.endereco AS endereco_empresa, 
            C.cep AS cep_empresa, C.cnae AS cnae_empresa, IF(B.codigo_sindical IS NULL,'999.000.000.00000-3',B.codigo_sindical) AS cod_sindicato, DATE_FORMAT(A.data_entrada, '%d/%m/%Y') AS data_entrada
            FROM rh_clt AS A
            LEFT JOIN rhsindicato AS B ON(A.rh_sindicato= B.id_sindicato)
            LEFT JOIN rhempresa AS C ON(A.id_projeto= C.id_projeto)
            WHERE A.id_clt=$id_clt";
//    echo $sql.'<br>';
    $result = mysql_query($sql);
    $row_clt = mysql_fetch_array($result);
    return $row_clt;
}

list($regiao, $id_clt, $id) = explode('&', decrypt(str_replace('--', '+', $_REQUEST['enc'])));


$obj_movimento = new MovimentoRescisaoClass();

$movimentos_lancaveis_debito = $obj_movimento->getMovimentosLancaveis('DEBITO');
$movimentos_lancaveis_credito = $obj_movimento->getMovimentosLancaveis('CREDITO');


$obj_rescisao = new Rescisao();
$row_rescisao = $obj_rescisao->getRescisao($id);
$row_rescisao = $row_rescisao[0];

$row_clt = getClt($id_clt);

//var_dump($row_empresa);

$cnpj_empresa = $row_clt['cnpj'];
$razao_empresa = $row_clt['razao'];
$cep_empresa = $row_clt['cep'];
$cnae = $row_clt['cnae_empresa'];
$endereco_empresa = $row_clt['logradouro_empresa'];
$cnpj = $row_clt['cnpj'];
$municipio_empresa = $row_clt['cidade_empresa'];
$uf_empresa = $row_clt['uf_empresa'];
$bairro_empresa = $row_clt['bairro_empresa'];
$pis = $row_clt['pis'];


include_once 'view/rescisao_complementar.php';
exit();