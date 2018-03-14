<?php

if (empty($_COOKIE['logado'])) {
    print "<script>location.href = 'login.php?entre=true';</script>";
}

include('conn.php');
include('classes/phpQuery-onefile.php');

function get_tipos_de_logradouro($descricao_tp_logradouro) {
    $sql = "SELECT * FROM tipos_de_logradouro WHERE descricao_tp_logradouro='$descricao_tp_logradouro' LIMIT 1";
    return mysql_fetch_array(mysql_query($sql));
}

function simple_curl($url, $post = array(), $get = array()) {
    $url = explode('?', $url, 2);
    if (count($url) === 2) {
        $temp_get = array();
        parse_str($url[1], $temp_get);
        $get = array_merge($get, $temp_get);
    }

    $ch = curl_init($url[0] . "?" . http_build_query($get));
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    return curl_exec($ch);
}

$cep = isset($_REQUEST['cep']) ? $_REQUEST['cep'] : FALSE;


if ($cep) {
    $html = simple_curl('http://m.correios.com.br/movel/buscaCepConfirma.do', array(
        'cepEntrada' => $cep,
        'tipoCep' => '',
        'cepTemp' => '',
        'metodo' => 'buscarCep'
    ));


    phpQuery::newDocumentHTML($html, $charset = 'utf-8');


    $dados = array(
                'logradouro' => trim(pq('.caixacampobranco .resposta:contains("Logradouro: ") + .respostadestaque:eq(0)')->html()),
                'bairro' => trim(pq('.caixacampobranco .resposta:contains("Bairro: ") + .respostadestaque:eq(0)')->html()),
                'cidade/uf' => trim(pq('.caixacampobranco .resposta:contains("Localidade / UF: ") + .respostadestaque:eq(0)')->html()),
                'cep' => trim(pq('.caixacampobranco .resposta:contains("CEP: ") + .respostadestaque:eq(0)')->html())
    );

    $dados['cidade/uf'] = explode('/', $dados['cidade/uf']);
    $dados['cidade'] = trim($dados['cidade/uf'][0]);
    $dados['uf'] = trim($dados['cidade/uf'][1]);
    unset($dados['cidade/uf']);

    $array_logradouro = explode(' ', $dados['logradouro']);
    $row_tipos = get_tipos_de_logradouro($array_logradouro[0]);

    if (isset($_REQUEST['id_municipio'])) {
        $municipio = utf8_decode($dados['cidade']);
        $query = "SELECT id_municipio FROM municipios WHERE municipio = '$municipio'";
        $result = mysql_query($query);
        $row_municipio = mysql_fetch_array($result);
        $dados['id_municipio'] = $row_municipio['id_municipio'];
    }
    
}

/* carrega municípios pelo UF */
if (isset($_REQUEST['municipios'])) {
    
    $uf = isset($_REQUEST['uf']) ? $_REQUEST['uf'] : $dados['uf'];
    
    $query = "SELECT id_municipio, municipio FROM municipios WHERE sigla = '$uf'";
    $result = mysql_query($query);
    while ($row_municipio = mysql_fetch_array($result)) {
        $dados['municipios'][] = '('.$row_municipio['id_municipio'] . ')- ' . utf8_encode($row_municipio['municipio']);
    }
}
$dados['cod_tp_logradouro'] = $row_tipos['cod_tp_logradouro'];
$dados['id_tp_logradouro'] = $row_tipos['id_tp_logradouro'];
die(json_encode($dados));
