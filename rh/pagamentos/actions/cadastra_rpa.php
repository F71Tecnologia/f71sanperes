<?php 


require("../../../conn.php");


print_r($_REQUEST);


exit;
$id_rpa           = $_POST['id_rpa'];
$id_autonomo      = $_POST['id_autonomo'];
$tipo_saída       = $_POST['tipo_saida'];
$subgrupo         = $_POST['subgrupo'];
$nome             = $_POST['nome'];
//$valor            = $_POST['valor'];
$valor            = str_replace(',','.',str_replace('.','',$_POST['valor']));
$data             = implode('-',array_reverse(explode('-',$_POST['data'])));
$banco            = $_POST['banco'];
$cod_barra_gerais = ($_POST['cod_barra_gerais'] != 'NaN') ? $_POST['cod_barra_gerias']:'';
$tipo_guia        = $_POST['tipo_guia'];

$qr_rpa = mysql_query("SELECT A.id_rpa, A.id_autonomo, A.valor_liquido , C.nome as nome_projeto, D.regiao as nome_regiao
                       FROM rpa_autonomo as A
                       INNER JOIN  autonomo as B
                       ON A.id_autonomo = B.id_autonomo
                       INNER JOIN projeto as C
                       ON B.id_projeto = C.id_projeto
                       INNER JOIN  regioes as D
                       ON D.id_regiao = B.id_regiao
                       WHERE A.id_rpa = $id_rpa");
$row_rpa = mysql_fetch_assoc($qr_rpa);


//DADOS DO BANCO
$qr_banco  = mysql_query("SELECT * FROM bancos WHERE id_banco = $banco");
$row_banco = mysql_fetch_assoc($qr_banco);


$especifica = $nome.' - PROJETO: '.$row_rpa['nome_projeto'].' REGIÃO: '.$row_rpa['nome_regiao'];


 $sql = "INSERT INTO saida (id_regiao, id_projeto, id_banco, id_user, nome, id_nome, especifica, tipo, adicional, valor, data_proc, data_vencimento, status,comprovante, nosso_numero, tipo_boleto, cod_barra_gerais, id_referencia, id_tipo_pag_saida, entradaesaida_subgrupo_id)
	VALUES ('$row_banco[id_regiao]', '$row_banco[id_projeto]', '$banco', '$_COOKIE[logado]', '$nome','', '$especifica', '$tipo', '', '$valor',NOW(), '$data',  '1', '2', '$nosso_numero', '2', '$cod_barra_gerais','1', '1', '$subgrupo') ";
		
//if(mysql_query($sql)){

   $id_saida = mysql_insert_id();    
   
 // $insert = "INSERT INTO rpa_saida_assoc (id_rpa, )"
   
   

echo $id_saida;
    
//};
exit;

$banco 		= $_POST['banco'];
$qr_bancos = mysql_query("SELECT id_regiao,id_projeto FROM bancos WHERE id_banco = '$banco'");
$qr_bancos = mysql_fetch_array($qr_bancos);

$regiao 	= $qr_bancos[0];
$projeto 	= $qr_bancos[1];



$id_user 	= $_COOKIE['logado'];
$nome 		= utf8_decode($_POST['nome']);
$especificacao  = "";
$tipo 		= $_POST['tipo'];
$adicional 	= 0;
$valor 		= str_replace('.','',$_POST['valor']);
$data		= implode('-',array_reverse(explode('/',$_POST['data'])));
$tipo_contrato = $_POST['tipo_contrato'];
$id_folha = $_POST['id_folha'];
$mes_pg = $_POST['mes_pg'];
$ano_pg = $_POST['ano_pg'];
$tipo_pg = $_POST['tipo_pg'];
$subgrupo = $_POST['subgrupo'];
$folha_projeto = $_POST['folha_projeto'];
$folha_regiao  = $_POST['folha_regiao'];




if($_POST['cod_barra_gerais'] != 'NaN'){
$cod_barra_gerais = $_POST['cod_barra_gerais'];
}


    
    
$qr_nome = mysql_query("SELECT * FROM entradaesaida_nomes WHERE id_projeto = '$folha_projeto'") OR die(mysql_error());
if(mysql_num_rows($qr_nome) == 0){

    $qr_empresa = mysql_query("SELECT nome, cnpj, id_projeto FROM rhempresa WHERE id_projeto = '$folha_projeto'") OR die(mysql_error());
    $row_empresa = mysql_fetch_assoc($qr_empresa);

    $insert_nome = mysql_query("INSERT INTO entradaesaida_nomes (id_entradasaida, id_projeto, nome, cpfcnpj)
                                VALUES 
                                ('$tipo', '$row_empresa[id_projeto]', '$row_empresa[nome]', '$row_empresa[cnpj]')") OR die(mysql_error());
    $id_nome = mysql_insert_id();


}

    $sql = "INSERT INTO saida (id_regiao, id_projeto, id_banco, id_user, nome, id_nome, especifica, tipo, adicional, valor, data_proc, data_vencimento, status,comprovante, nosso_numero, tipo_boleto, cod_barra_gerais, id_referencia, id_tipo_pag_saida, entradaesaida_subgrupo_id)
	VALUES ('$regiao', '$projeto', '$banco', '$_COOKIE[logado]', '$nome','$id_nome', '$nome', '$tipo', '$adicional', '$valor',NOW(), '$data',  '1', '2', '$nosso_numero', '2', '$cod_barra_gerais','1', '1', '$subgrupo') ";
					
mysql_query($sql);

$id_saida = mysql_insert_id();

// controle de pagamentos adicionado em 20/09/2010 as 17:00 hs
$array_tipo = array(1 => 'CLT', 2 => 'COOP');
foreach($array_tipo as $chave => $tip){
	if($tip == $tipo_contrato){
		$tipo_contrato_pg = $chave;
		break;
	}
}
$sql_pagamentos  = "INSERT INTO pagamentos (id_saida,tipo_contrato_pg,id_folha, mes_pg, ano_pg, tipo_pg) VALUES ('$id_saida','$tipo_contrato_pg','$id_folha','$mes_pg','$ano_pg','$tipo_pg')";
mysql_query($sql_pagamentos);

echo $id_saida;
?>