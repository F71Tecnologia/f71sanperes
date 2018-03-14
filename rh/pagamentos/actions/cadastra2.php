<?php 
include "../../../conn.php";
require("../../../classes/uploadfile.php");

	
$id_user = $_COOKIE['logado'];
$folha = $_POST['id_folha'];
$mes = $_POST['mes'];
$ano = $_POST['ano'];
$clt = $_POST['id_clt'];
$tipo = $_POST['tipo']; // 170 - RESCISÃƒO, 156 - FÃ‰RIAS  
$nome = $_POST['nome'];
$ferias = $_POST['ferias'];
$especificacao = "";
$adicional = 0;
//$valor = str_replace('.', ',',$_POST['valor']);
$valor = str_replace(',','.',str_replace('.', '',$_POST['valor']));




$data = implode("-",array_reverse(explode("/",$_POST['data'])));
$banco = $_POST['bancos'];
$subgrupo = $_POST['subgrupo'];
$id_clt = $_POST['id_clt'];

$qr_banco  = mysql_query("SELECT * FROM bancos WHERE id_banco = '$banco'");
$row_banco = mysql_fetch_assoc($qr_banco);
$regiao    = $row_banco['id_regiao'];
$projeto   = $row_banco['id_projeto'];


$qr_clt = mysql_query("SELECT A.*, IF(A.nome_banco = '', B.nome, A.nome_banco ) as banco FROM rh_clt as A
                       LEFT JOIN  bancos as B
                       ON A.banco = B.id_banco
                       WHERE A.id_clt = $id_clt") or die(mysql_error());
$row_clt = mysql_fetch_assoc($qr_clt);

$nome .= ' - BANCO: '.$row_clt['banco'];
$nome .=' - CONTA: '.$row_clt['conta'];
$nome .= ' - AGÊNCIA: '.$row_clt['agencia'];
$nome .= ' - CPF: '.$row_clt['cpf'];


/*
$query_banco = mysql_query("SELECT * FROM bancos WHERE id_regiao = '$regiao' AND id_projeto = '$projeto' AND status_reg = '1'");
$num_banco = mysql_num_rows($query_banco);
if(empty($num_banco)){ echo "NÃ£o existem bancos para a regiao '$regiao' e projeto '$projeto'"; exit;}
$row_banco = mysql_fetch_assoc($query_banco);*/


 ///RECISÃO
$sql = "INSERT INTO saida (id_regiao, id_projeto, id_banco, id_user, nome, especifica,  tipo,  valor, data_proc, data_vencimento, status,comprovante, nosso_numero, tipo_boleto, cod_barra_gerais, id_referencia, id_tipo_pag_saida, entradaesaida_subgrupo_id, id_clt)
  VALUES ('$regiao', '$projeto', '$banco', '$_COOKIE[logado]', '$nome', '$nome', '$tipo', '$valor',NOW(), '$data',  '1', '2', '$nosso_numero', '2', '$cod_barra_gerais','1', '1', '$subgrupo', '$id_clt') ";
mysql_query($sql) or die(mysql_error());

$id_saida = mysql_insert_id();

mysql_query("INSERT INTO pagamentos_especifico (id_saida,  mes, ano, id_clt)
		VALUES('$id_saida', '$mes', '$ano', '$clt');");	 

mysql_query("INSERT INTO saida_files (id_saida, tipo_saida_file) VALUES ('$id_saida','.pdf');");
$query_comprovante = mysql_query("SELECT MAX(id_saida_file) FROM saida_files");

$id_comprovante = @mysql_result($query_comprovante,0);


////RESCISÃO - CASO TENHA A MULTA  FGTS    
///MULTA
/*
if($_POST['multa'] == 1){
    
   
        $nome .= 'MULTA FGTS';  
        $data_multa = $data = implode("-",array_reverse(explode("/",$_POST['multa_dt_vencimento'])));
        $valor_multa = $_POST['multa_valor'];
    
     
       $sql_multa = "INSERT INTO saida (id_regiao, id_projeto, id_banco, id_user, nome,   tipo,  valor, data_proc, data_vencimento, status,comprovante, nosso_numero, tipo_boleto, cod_barra_gerais, id_referencia, id_tipo_pag_saida, entradaesaida_subgrupo_id, id_clt)
	VALUES ('$regiao', '$projeto', '$banco', '$_COOKIE[logado]', '$nome', '167', '$valor_multa',NOW(), '$data_multa',  '1', '2', '$nosso_numero', '2', '$cod_barra_gerais','1', '1', '3', '$id_clt') ";
     
        mysql_query($sql_multa) or die(mysql_error());
        $id_saida2 = mysql_insert_id();
       
	$diretorio = "../../../comprovantes";
	$upload = new UploadFile($diretorio,array('jpg','gif','png','pdf','JPG','GIF','PNG','PDF'));
	$upload->arquivo($_FILES['multa_anexo_saida']);
	$upload->verificaFile();        
	mysql_query("INSERT INTO saida_files (tipo_saida_file, id_saida) VALUES ('.$upload->extensao', '$id_saida2');");	
	$id = mysql_insert_id();	
	$upload->NomeiaFile($id.'.'.$id_saida2);
	$upload->Envia();     
}*/


/*
if($tipo == 170){
	$arquivo_orginal = '../../arquivos/recisaopdf/rescisao_'.$clt.'_1.pdf';
	$arquivo_saida = '../../../comprovantes/'.$id_comprovante.'.'.$id_saida.'.pdf';

     	if (!copy($arquivo_orginal, $arquivo_saida)) {
		echo "Falha ao copiar arquivo...<br />Verifique se a rescisão complementar foi gerada.";
		exit;
	}
}else{
	$arquivo_orginal = '../../arquivos/ferias/ferias_'.$clt.'_'.$ferias.'.pdf';
	$arquivo_saida = '../../../comprovantes/'.$id_comprovante.'.'.$id_saida.'.pdf';

	if (!copy($arquivo_orginal, $arquivo_saida)) {
		echo "Falha ao copiar arquivo...<br />Verifique se o arquivo existe no sistema.";
		exit;
	}
}
 */

if($_COOKIE['logado'] == 87){
 
          echo 'Envio concluído...';            
                        echo "<script> 
                          setTimeout(function(){
                            window.parent.location.reload();
                            parent.eval('tb_remove()')
                            },3000)    
                    </script>";
    
    
    
} else {
    
echo '<script>parent.window.location.reload();
          if (parent.window.hs) {
           var exp = parent.window.hs.getExpander();
           if (exp) {
            
             exp.close();
           
           }
          }</script>';
}
?>