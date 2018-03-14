<?php 
/*
 * Script baseado no pagar.selecao_old.php para geração do arquivo de remessa eletrônica
 * Criado....: 10/04/2015
 * Atualizado: 10/04/2015
 * 
 * Esta ação abre a classe CNAB240Rem para geração de remessa eletrônica
*/


include ("../include/restricoes.php");
include "../../conn.php";
include ('../../classes/Cnab240Class.php'); 


$id_user = $_COOKIE['logado'];

if(sizeof($_POST['saidas']) >0) {
	
	$id['saida'] = $_POST['saidas'];
	
} else {

	$id['entrada'] = $_POST['entradas'];
}


$strIds = implode(',',$id['saida']);

$objCnab240 = new CNAB240();


$objCnab240->setUser('JACQUES');
$objCnab240->setIdsSaidas($strIds);
$objCnab240->setPath('/home/ispv/public_html/intranet/novoFinanceiro/arquivos_cnab240/');



if($objCnab240->RunRemessa()){;
    

    $objCnab240->OutPutRemessa();
   
    foreach($id as $tabela => $saida){

        foreach($saida as $id_saida){

            $id_pro = $id_saida;
            $data_hoje = date("Y-m-d");

            $result = mysql_query("SELECT * FROM $tabela WHERE id_$tabela = '$id_saida'");
            $row = mysql_fetch_array($result);

            $regiao = $row['id_regiao'];


            $result_bancos = mysql_query("SELECT * FROM bancos WHERE id_banco = '$row[id_banco]'");
            $row_bancos = mysql_fetch_array($result_bancos);

            $valor = str_replace(",", ".", $row['valor']);
            $adicional = str_replace(",", ".", $row['adicional']);
            $valor_banco = str_replace(",", ".", $row['valor']);


            $valor_final = $valor + $adicional;

            if($tabela == 'saida'){
                    $saldo_banco_final = $valor_banco - $valor_final;
            }else{
                    $saldo_banco_final = $valor_banco + $valor_final;
            }

            $valor_f = number_format($valor_final,2,",",".");
            $saldo_banco_final = number_format($saldo_banco_final,2,",",".");
            $saldo_banco_final_banco = number_format($saldo_banco_final,2,",","");


            if($row['status'] == "1"){
                    //mysql_query("UPDATE $tabela set status = '2', data_pg = '$data_hoje', id_userpg = '$id_user' , hora_pg = NOW() WHERE id_$tabela = '$id_saida'");
                    //mysql_query("UPDATE bancos set saldo = '$saldo_banco_final_banco' WHERE id_banco = '$row[id_banco]'");

                    //if($row['tipo'] == "66"){
                    //  mysql_query("UPDATE compra SET acompanhamento = '6' WHERE id_compra = '$row[id_compra]'");
                    //}
            }

        }

    }
    
}

?>
<script>
    location.href = '../index_jacques.php';
    alert('<?=$objCnab240->getError()?>');
</script>
