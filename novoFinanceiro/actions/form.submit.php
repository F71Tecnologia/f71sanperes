<?php
include ("../include/restricoes.php");
include_once "../../conn.php";

/**
 * 
 * @param type numero $Data
 * @return type
 */
function ConverteData($Data){
                if(strstr($Data, "/")) {
                        $rstData = implode('-', array_reverse(explode('/', $Data)));
                        return $rstData;
                } elseif(strstr($Data, "-")) {
                       $rstData = implode('/', array_reverse(explode('-', $Data)));
                       return $rstData;
                }
       } 
/*print_r($_POST);*/
 
//--------------------------------------------------------------------||
//- AQUI COMEÇA A RODAR A SEGUNDA PARTE.. ONDE CADASTRAREMOS A SAÍDA -||
//- CASO SEJA 1 VAI CADASTRAR UMA SAÍDA, CASE SEJA 2 VAI CADASTAR UM -||
//- NOVO TIPO DE SAÍDA												 -||
//--------------------------------------------------------------------||

//CADASTRANDO SAIDAS

/*projeto
banco
grupo
tipo
nome
descricao
adicional
real
data*/
//id_saida	id_regiao	id_projeto	id_banco	id_user	nome	especifica	tipo	adicional	valor	data_proc	data_vencimento	data_pg	comprovante	tipo_arquivo	id_userpg	id_compra	campo3	status
$id_user                = $_REQUEST['logado'];
$banco                  = $_REQUEST['banco'];
$qr_banco = mysql_query("SELECT * FROM bancos WHERE id_banco = '$banco'");
$row_banco = mysql_fetch_assoc($qr_banco);        

$regiao                 = $row_banco['id_regiao'];    
$projeto                = $row_banco['id_projeto'];
$nome                   = $_REQUEST['nome'];
$especifica             = utf8_decode($_REQUEST['descricao']);
$tipo                   = $_REQUEST['tipo'];
$adicional              = str_replace('.', '', $_REQUEST['adicional']);
$valor                  = str_replace('.', '', $_REQUEST['real']);
$data_credito           = $_REQUEST['data'];
$data_proc              = date('Y-m-d H:i:s');
$data_proc2             = date('Y-m-d');
$valor                  = $valor;
$valor_bruto            = str_replace(',', '.',str_replace('.','', $_REQUEST['valor_bruto']));
$adicional              =  $adicional;
$grupo                  = $_REQUEST['grupo'];
$subgrupo               = $_REQUEST['subgrupo'];
$id_referencia          = $_REQUEST['referencia'];
$id_bens                = $_REQUEST['bens'];
$id_tipo_pag_saida      = $_REQUEST['tipo_pagamento'];
$nosso_numero           = $_REQUEST['nosso_numero'];
$codigo_barra           = $_REQUEST['codigo_barra'];
$n_documento            = $_REQUEST['n_documento'];
$link_nfe               = $_REQUEST['link_nfe'];
$tipo_boleto           = $_REQUEST['tipo_boleto'];
$campos_cod_barra_consumo = implode('',$_REQUEST['codigo_barra_consumo']); 
$campos_cod_barra_gerais = implode('',$_REQUEST['campo_codigo_gerais']);
$estorno                 = $_REQUEST['estorno'];
$descricao_estorno       = trim($_REQUEST['descricao_estorno']);
$valor_estorno_parcial   = str_replace('.', '',$_POST['valor_estorno_parcial']);
$mes_competencia         = $_REQUEST['mes_competencia'];
$ano_competencia         = $_REQUEST['ano_competencia'];
$tipo_nf                 = $_REQUEST['tipo_nf'];
$dt_emissao_nf           = implode('-', array_reverse(explode('/',$_REQUEST['dt_emissao_nf'])));
//Validando as informa��es



    switch($grupo){
        
        case 1:
        case 2:
        case 3:
        case 4:  $subgrupo = 0;
            break;
    }
      

        if($id_referencia == 1){  $id_bens = ''; }
 

        ///tipo de pagamento
       switch($id_tipo_pag_saida){

           case 1:
                if($tipo_boleto == 1){ 
                     $campos_cod_barra_gerais = '';
                     $nosso_numero            = '';
                }  else { 
                      $campos_cod_barra_consumo = '';
                }
               
            break;
            
                    
          default:      $nosso_numero  = '';   
                        $campos_cod_barra_consumo = '';
                        $campos_cod_barra_gerais = '';
                        $tipo_boleto = '';
        }


/////////////////////////////////
/// PRESTADOR E FORNECEDOR///////
//////////////////////////////
$tipo_empresa       = $_REQUEST['tipo_empresa'];
$regiao_interno     = $_REQUEST['regiao-prestador'];
$projeto_interno    = $_REQUEST['Projeto-prestador'];

if(!empty($regiao_interno) and !empty($projeto_interno)){

$qr_regiao   = mysql_query("SELECT regiao FROM regioes WHERE id_regiao = '$regiao_interno'");
$row_regiao  = mysql_fetch_assoc($qr_regiao);

$qr_projeto  = mysql_query("SELECT nome FROM projeto WHERE id_projeto = '$projeto_interno'");
$row_projeto = mysql_fetch_assoc($qr_projeto);

$dados_reg_pro = ' - Regiao: '.' - Regiao: '.$row_regiao['regiao'].', Projeto - '.$row_projeto['nome'];

}

if($tipo_empresa == 1 ){
    
    $id_prestador   = $_REQUEST['prestador'];
    $qr_prestador   = mysql_query("SELECT c_razao, c_cnpj,  B.nome as nome_projeto, C.regiao as nome_regiao  
                                    FROM prestadorservico as A
                                    INNER JOIN projeto AS B
                                    ON B.id_projeto = A.id_projeto
                                    INNER JOIN regioes as C
                                    ON C.id_regiao = A.id_regiao
                                    WHERE id_prestador = '$id_prestador'");
    
    $row_prestador  = mysql_fetch_assoc($qr_prestador);  
    $nome_prestador = $row_prestador['c_razao'];
    $cnpj_prestador = $row_prestador['c_cnpj'];
    $nome_prestador           = $row_prestador['c_razao'].' - Regiao: '.$row_prestador['nome_regiao'].', Projeto: '.$row_prestador['nome_projeto'];    
    
} else {
    $id_fornecedor   = $_REQUEST['fornecedor']; 
    $qr_fornecedor   = mysql_query("SELECT A.nome, A.cnpj,  B.nome as nome_projeto, C.regiao as nome_regiao  
                                    FROM fornecedores as A
                                    INNER JOIN projeto AS B
                                    ON B.id_projeto = A.id_projeto
                                    INNER JOIN regioes as C
                                    ON C.id_regiao = A.id_regiao
                                    WHERE id_fornecedor = '$id_fornecedor'");
    $row_fornecedor  = mysql_fetch_assoc($qr_fornecedor);  
    $nome_fornecedor = $row_fornecedor['nome'];
    $cnpj_fornecedor = $row_fornecedor['cnpj'];
    $nome_fornecedor           =  $row_fornecedor['nome'].' - Regiao: '.$row_fornecedor['nome_regiao'].', Projeto: '.$row_fornecedor['nome_projeto']; 
} 
////////////////////////////////////





if(!empty($id_prestador) and ($tipo == 132 or $tipo == 32 or $grupo ==  30  or $grupo == 80 or $grupo == 20)) {
		
    $nome = $nome_prestador;
	
}  elseif(!empty($id_fornecedor)) {
    
    $nome = $nome_fornecedor;
    
}







$data_credito2 = ConverteData($data_credito);
 

if(($_REQUEST['nome'] != '' and is_numeric($nome)) or $tipo == 170) {   
       
       $query_nomes = mysql_query("SELECT id_nome,nome FROM entradaesaida_nomes WHERE id_nome = '$_REQUEST[nome]'");
       $row_nomes = mysql_fetch_assoc($query_nomes);
       $nome = $row_nomes['nome'].$dados_reg_pro;
       $id_nome = $row_nomes['id_nome'];
       
}





if(isset($_REQUEST['atualizar']) ){
    

         $tipo_nf                 = $_REQUEST['tipo_nf'];
         $n_documento             = $_REQUEST['n_documento'];
         $dt_emissao_nf           = implode('-', array_reverse(explode('/',$_REQUEST['dt_emissao_nf'])));
         $id_saida                = $_REQUEST['id_saida'];  

       

         
         
         
               if($grupo != 80 and $grupo != 30 and $grupo != 2 and $grupo != 1 and  $grupo != 20){
                   unset($id_fornecedor,$nome_fornecedor, $cnpj_fornecedor, $id_prestador,$nome_prestador,$cnpj_prestador, $tipo_empresa  );          
               }

           switch($grupo){               
               case 10:                                                    
                    $nome = utf8_decode($_REQUEST['nome_saida']);
                   break;
           }





               //ALTERA��O PARA QUANDO ATUALIZAR A RESCIS�O N�O SUMIR O NOME
               if($tipo == 170){

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
                      $nome = '('.$row_resc['id_clt'].') '.$row_resc['nome'].', REGI�O: '.$row_resc['regiao'].' - PROJETO: '.$row_resc['projeto'].' RESCIS�O '.$row_resc['mes'].'/'.$row_resc[ano];
                  }

     if($_COOKIE['logado'] == 87){
    
    echo $n_documento;
    echo '<br>';
    echo $dt_emissao_nf;
    echo '<br>';
    echo $tipo_nf;
    echo '<br>';
    echo $valor;
    echo "UPDATE saida SET id_regiao = '$regiao', id_projeto = '$projeto', id_banco = '$banco',  nome = '$nome', id_nome = '$id_nome', 
                     especifica = '$especifica', tipo = '$tipo',  data_proc = '$data_proc', data_vencimento = '$data_credito2',
                     nosso_numero = '$nosso_numero', tipo_boleto = '$tipo_boleto', cod_barra_consumo = '$campos_cod_barra_consumo', cod_barra_gerais = '$campos_cod_barra_gerais', id_referencia = '$id_referencia',
                     id_bens = '$id_bens', id_tipo_pag_saida =  '$id_tipo_pag_saida',  entradaesaida_subgrupo_id = '$subgrupo', tipo_empresa = '$tipo_empresa', id_fornecedor = '$id_fornecedor',
                    nome_fornecedor = '$nome_fornecedor',	cnpj_fornecedor = '$cnpj_fornecedor', id_prestador = '$id_prestador', nome_prestador = '$nome_prestador', cnpj_prestador = '$cnpj_prestador', estorno = '$estorno', estorno_obs = '$descricao_estorno', valor_estorno_parcial = '$valor_estorno_parcial',
                    mes_competencia = '$mes_competencia', ano_competencia = '$ano_competencia', valor_bruto = '$valor_bruto', dt_emissao_nf = '$dt_emissao_nf', tipo_nf = '$tipo_nf', n_documento = '$n_documento'
                     WHERE id_saida = '$id_saida' LIMIT 1";
    
    
    "12321<br>2013-07-23<br>3<br>UPDATE saida SET id_regiao = '45', id_projeto = '3316', id_banco = '119',  nome = 'ECO EMPRESA DE CONSULTORIA E ORGANIZA??O EM SISTEMA E EDITORA??O LTDA - Regiao: UPAS, Projeto: UPA MARECHAL HERMES', id_nome = '', 
                     especifica = 'DARF RETEN??O PIS/CONFINS/CSLL - COMPET?NCIA 06/2013 -NF 477', tipo = '199',  data_proc = '2013-07-23 15:27:01', data_vencimento = '2013-07-31',
                     nosso_numero = '', tipo_boleto = '', cod_barra_consumo = '', cod_barra_gerais = '', id_referencia = '1',
                     id_bens = '', id_tipo_pag_saida =  '',  entradaesaida_subgrupo_id = '22', tipo_empresa = '1', id_fornecedor = '',
                    nome_fornecedor = '',	cnpj_fornecedor = '', id_prestador = '474', nome_prestador = 'ECO EMPRESA DE CONSULTORIA E ORGANIZA??O EM SISTEMA E EDITORA??O LTDA - Regiao: UPAS, Projeto: UPA MARECHAL HERMES', cnpj_prestador = '39.185.269/0001-25', estorno = '', estorno_obs = '', valor_estorno_parcial = '',
                    mes_competencia = '06', ano_competencia = '2013', valor_bruto = '1318.54', dt_emissao_nf = '2013-07-23', tipo_nf = '3', n_documento = '12321'
                     WHERE id_saida = '63776' LIMIT 1";
    
    
exit;
    
}


               $sql = "UPDATE saida SET id_regiao = '$regiao', id_projeto = '$projeto', id_banco = '$banco',  nome = '$nome', id_nome = '$id_nome', 
                     especifica = '$especifica', tipo = '$tipo',  data_proc = '$data_proc', data_vencimento = '$data_credito2',
                     nosso_numero = '$nosso_numero', tipo_boleto = '$tipo_boleto', cod_barra_consumo = '$campos_cod_barra_consumo', cod_barra_gerais = '$campos_cod_barra_gerais', id_referencia = '$id_referencia',
                     id_bens = '$id_bens', id_tipo_pag_saida =  '$id_tipo_pag_saida',  entradaesaida_subgrupo_id = '$subgrupo', tipo_empresa = '$tipo_empresa', id_fornecedor = '$id_fornecedor',
                    nome_fornecedor = '$nome_fornecedor',	cnpj_fornecedor = '$cnpj_fornecedor', id_prestador = '$id_prestador', nome_prestador = '$nome_prestador', cnpj_prestador = '$cnpj_prestador', estorno = '$estorno', estorno_obs = '$descricao_estorno', valor_estorno_parcial = '$valor_estorno_parcial',
                    mes_competencia = '$mes_competencia', ano_competencia = '$ano_competencia', valor_bruto = '$valor_bruto', dt_emissao_nf = '$dt_emissao_nf', tipo_nf = '$tipo_nf', n_documento = '$n_documento'
                     WHERE id_saida = '$id_saida' LIMIT 1"; 

       
               mysql_query($sql) or die(mysql_error());
                   echo $id_saida;
               exit;
    
}




if($tipo == "19") { // VERIFICA SE É IGUAL A SAÍDA DE CAIXA
	$result_banco = mysql_query("SELECT * FROM bancos WHERE id_banco = '$banco'");
	$row_banco = mysql_fetch_array($result_banco);
	$saldo_atual = $row_banco['saldo'];
	$adicional = str_replace(",",".",$adicional);
	$valor = str_replace(",",".",$valor);
	$saldo_atual = str_replace(",",".",$saldo_atual);
	$valor_adicional = $adicional + $valor;
	$sobra = $saldo_atual - $valor_adicional;
//	$adicional = number_format($adicional,2,",","");
	$adicional = number_format($adicional,2,".","");
	$valor = number_format($valor,2,",","");
//	$valor_adicional = number_format($valor_adicional,2,",","");
	$valor_adicional = number_format($valor_adicional,2,".","");
	$sobra = number_format($sobra,2,",","");
	$verifica_caixinha = mysql_query("SELECT * FROM caixinha WHERE id_regiao = '$regiao'");
	$row_saldo_verifica = mysql_fetch_array($verifica_caixinha);
	$row_verifica = mysql_num_rows($verifica_caixinha);

	if(!empty($row_verifica)) {  // VERIFICA SE JA HOUVE SAÍDA DE CAIXA PARA REGIÃO SELECIONADA
		  $saldo_atual_caixinha = str_replace(",",".", $row_saldo_verifica['saldo']);
		  $valor_adicional_ff = str_replace(",",".", $valor_adicional);
		  $soma_do_caixinha = $saldo_atual_caixinha + $valor_adicional_ff;
		  $saldo_somado_caixinha = number_format($soma_do_caixinha,2,",","");
		  mysql_query("UPDATE caixinha SET saldo = '$saldo_somado_caixinha' WHERE id_caixinha = '$row_saldo_verifica[0]'") or die("Erro");
	  } else {  // SE NÃO HOUVE SAÍDA DE CAIXA, ELE INSERE A 1ª SAÍDA DE CAIXA DESSA REGIÃO
	   mysql_query("INSERT INTO caixinha(id_projeto,id_regiao,saldo,id_banco) VALUES 
										('$projeto','$regiao','$valor_adicional','$banco')") 
	   or die (mysql_error());
	  } // AQUI TERMINA SE JA HOUVE OU NÃO SAÍDA DE CAIXA




          

	// INSERE SAÍDA!
	mysql_query("INSERT INTO saida (id_regiao, id_projeto, id_banco, id_user, nome, id_nome, especifica, tipo, adicional, valor, data_proc, data_vencimento, status,comprovante, nosso_numero, tipo_boleto, cod_barra_consumo, cod_barra_gerais, id_referencia,id_bens, id_tipo_pag_saida, entradaesaida_subgrupo_id, tipo_empresa, id_fornecedor, nome_fornecedor,	cnpj_fornecedor, id_prestador, nome_prestador, cnpj_prestador, n_documento, link_nfe, mes_competencia, ano_competencia,valor_bruto,dt_emissao_nf, tipo_nf)
                VALUES ('$regiao', '$projeto', '$banco', '$id_user', '$nome','$id_nome', '$especifica', '$tipo', '$adicional', '$valor','$data_proc', '$data_credito2',  '1', '0', '$nosso_numero', '$tipo_boleto','$campos_cod_barra_consumo', '$campos_cod_barra_gerais','$id_referencia', '$id_bens', '$id_tipo_pag_saida', '$subgrupo', '$tipo_empresa','$id_fornecedor', '$nome_fornecedor','$cnpj_fornecedor', '$id_prestador', '$nome_prestador', '$cnpj_prestador','$n_documento','$link_nfe','$mes_competencia', '$ano_competencia', '$valor_bruto', '$dt_emissao_nf', '$tipo_nf')") or die("Erro");
	
	$ultimo_id = mysql_insert_id();
	mysql_query("UPDATE bancos SET saldo = '$sobra' WHERE id_banco = '$banco'") or die(mysql_error());
	exit;
        
        
}
// AKI TERMINA TUDO QUE FOR REFERENTE A CAIXINHA 






	
mysql_query("INSERT INTO saida (id_regiao, id_projeto, id_banco, id_user, nome, id_nome, especifica, tipo, adicional, valor, data_proc, data_vencimento, status,comprovante, nosso_numero, tipo_boleto, cod_barra_consumo, cod_barra_gerais, id_referencia,id_bens, id_tipo_pag_saida, entradaesaida_subgrupo_id, tipo_empresa, id_fornecedor, nome_fornecedor,	cnpj_fornecedor, id_prestador, nome_prestador, cnpj_prestador, n_documento, link_nfe, mes_competencia, ano_competencia, valor_bruto)
                VALUES ('$regiao', '$projeto', '$banco', '$id_user', '$nome','$id_nome', '$especifica', '$tipo', '$adicional', '$valor','$data_proc', '$data_credito2',  '1', '0', '$nosso_numero', '$tipo_boleto','$campos_cod_barra_consumo', '$campos_cod_barra_gerais','$id_referencia', '$id_bens', '$id_tipo_pag_saida', '$subgrupo', '$tipo_empresa','$id_fornecedor', '$nome_fornecedor','$cnpj_fornecedor', '$id_prestador', '$nome_prestador', '$cnpj_prestador','$n_documento','$link_nfe', '$mes_competencia', '$ano_competencia', '$valor_bruto')") or die(mysql_error());
	

$ultimo_id = mysql_insert_id();



// Prestadores de serviço
if(($tipo == '32' or $tipo == '132' or $grupo == 30 or $grupo == 80 or  $grupo == 20) and !empty($id_prestador)){
    
	if($tipo == '132'){
		$tipo_prestador = 'NOTA'; 
	}elseif($tipo == '32'){
		$tipo_prestador = 'FOLHA';
	}
        
        
        
        
	$query_prestador = mysql_query("SELECT MAX(parcela) FROM prestador_pg WHERE id_prestador = '$id_prestador'");
	$prestador = @mysql_result($query_prestador,0);
	$prestador = $prestador + 1;
	
	mysql_query("INSERT INTO prestador_pg (id_prestador,	id_regiao,	id_saida, tipo,	valor,	data,	documento,	parcela, gerado,	status_reg,	comprovante)
	VALUES ('$id_prestador', '$regiao', '$ultimo_id', '$tipo_prestador', '$valor', '$data_credito2', '$especifica', '$prestador', '1', '1', '0');
	");
        
 
}

   echo $ultimo_id;    

/* OBS
// VERIFICANDO SE ESSA SAÍDA JA FOI CADASTRADA POR OUTRO USUÁRIO
$result_verifica = mysql_query("SELECT * FROM saida WHERE valor = '$valor' AND data_vencimento = '$data_credito2'") or die(mysql_error());
$row_num_verifica = mysql_num_rows($result_verifica);
*/

/*
<script language= "JavaScript">

alert("Informações cadastradas com sucesso!");

opener.location.reload();

location.href="saidas.php?regiao=<?=$regiao?>&insert=true";

</script>

*/
?>