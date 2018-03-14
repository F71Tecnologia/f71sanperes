<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
}else{

include "../conn.php";

if(empty($_REQUEST['financeiro'])){

$regiao = $_REQUEST['regiao'];
$id_compra = $_REQUEST['compra'];

$result = mysql_query("SELECT *,
date_format(data_produto, '%d/%m/%Y')as data_produto, 
date_format(data_requisicao, '%d/%m/%Y')as data_requisicao, 
date_format(prazo1, '%d/%m/%Y')as prazo1,  
date_format(prazo2, '%d/%m/%Y')as prazo2, 
date_format(prazo3, '%d/%m/%Y')as prazo3 
FROM compra2 where id_compra = '$id_compra'");
$row = mysql_fetch_array($result);

$result_reg = mysql_query("SELECT * FROM regioes where id_regiao = '$regiao'", $conn);
$row_reg = mysql_fetch_array($result_reg);

$result_user = mysql_query("SELECT nome1 FROM funcionario where id_funcionario = '$row[id_user_pedido]'", $conn);
$row_user = mysql_fetch_array($result_user);

$data = date('d/m/Y');

if($row['tipo'] == "1"){
$tipo = "Produto";
}else{
$tipo = "Serviço";
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link href="../adm/css/estrutura.css" rel="stylesheet" type="text/css">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Intranet - Controle de Cota&ccedil;&otilde;es</title>
<script type="text/javascript" src="../jquery/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="../jquery/mascara/jquery.maskedinput-1.2.2.js"></script>
<script type="text/javascript" src="../jquery/priceFormat.js"></script>
<script type="text/javascript">
$(function(){
	
	  	
$('#primeiro_pg').mask('99/99/9999');
$('#valor').priceFormat({
	
		 prefix: '',
        centsSeparator: ',',
        thousandsSeparator: ''
      });	
$('#valor2').priceFormat({
	
		 prefix: '',
        centsSeparator: ',',
       thousandsSeparator: ''
      });	



$('.data_pg').live('focus', function(){

	$(this).mask('99/99/9999');
});


$('.valores').live('focus', function(){
	
	
	$(this).priceFormat({
	
		 prefix: '',
        centsSeparator: ',',
        thousandsSeparator: '.'
      });	
})


$(document).ready(function(){
	
	var opcao = $('#opcao').val();
	

	
	
	$("#mostrarvariavel").change(function(event){
	  event.preventDefault();
	  $("#fixo").hide("slow");
	  event.preventDefault();
	  $("#variavel").show(600);
	});
 
	$("#mostrarfixo").change(function(event){
	  event.preventDefault();
	  $("#variavel").hide("slow");
	  event.preventDefault();
	  $("#fixo").show(600);
	});
	
});

 	
$('#ok').click(function(){
	
	$('#valores_saida').html('');
	
	var data_pg  = $('#primeiro_pg').val().split('/');
	var parcelas = parseFloat($('#n_parcelas').val());
	var valor = parseFloat($('#valor').val());
	var i = 0;
	var valordiv = 0;
	//var result = num.toFixed(2)	
	
	valordiv = valor / parcelas;
	valordiv = valordiv.toFixed(2);
	valordiv = valordiv.replace('.', ",");


	var mes = data_pg[1];
	var ano = data_pg[2];
	var dia = data_pg[0];
	var dia2 = data_pg[0];
	
	 
	//aviso
	$('#valores_saida').append('<h4 style="font-style: italic;">Confira as datas e os valores.</h4>') 
	 
	for(i=1; i<=parcelas; i++){
	
	
	
	if(i==1){
		
		 data_pg = dia+'/'+formato_mes_data(mes)+'/'+ano; 
	
	} else {
				
		
		if(mes_anterior == 12)
		{  
			ano = parseInt(ano) + 1;
			mes = 1;
		} else {
		
			mes++;	
		}
		
	
	
		 
		 if(dia2 >= 29 && dia2 <=31) {
			 console.log(dias2)
		
				 switch(mes)
			    {
			        case 1 :
			        case 3 :
			        case 5 :
			        case 7 :
			        case 8 :
			        case 10:
			        case 12:
			            dia = 31;
			            break;
			        case 4 :
			        case 6 :
			        case 9 :
			        case 11:
			               dia = 30;
			            break;
			
			        case 2 :
			            if( ( (ano % 4 == 0) && ( ano % 100 != 0) ) || (ano % 400 == 0) )
			                dia = 29;
			            else
			                dia = 28;
			            break;
			    }
		 }
		
		
		data_pg = dia+'/'+formato_mes_data(mes)+'/'+ano;
		
	}
	
	
	
	
	
	
	
	$('#valores_saida').append('<div><span><strong>Parcela '+i+'</strong></span> Data: <input type="text" name="data_pg[]" value="'+data_pg+'" class="data_pg"/> Valor: <input name="valor_parcela[]" value="'+valordiv+'" class="valores"/> ');	
	
	
	mes_anterior = mes;
	
	}
	
	$('#valores_saida').append('<br> <span style=" width: 100%"><input type="submit" name="concluir" value="enviar para o financeiro"/></span>');
	
	
	
});
	
	
});
function formato_mes_data(mes){
if(mes.toString().length == 1) { return '0'+mes; } else { return mes; };		
}

</script>


</head>
<style>
table tr {
border:1px solid  #CCC;
height:30px;	
}
</style>

<body>
<div id="corpo">
	<div id="conteudo">

      <?php
include "../empresa.php";
$img= new empresa();
$img -> imagemCNPJ();
?>
        <h3 style="color:#FF5959">  AUTORIZA&Ccedil;&Atilde;O DA COMPRA</h3>
         <h3>   NUMERO DO PROCESSO: <?php print "$row[num_processo]";?></h3>
          <?php
		  
		  $cases = $row['fornecedor_escolhido'];
		  switch($cases){
		  case 1:
		  $id_fornecedor = $row['fornecedor1'];
		  $imposto = $row['imposto1'];
		  $frete = $row['frete1'];
		  $desconto = $row['desconto1'];
		  $valor_uni = $row['valor_uni1'];
		  $valor = $row['preco1'];
		  $prazo = $row['prazo1'];
		  $obs = $row['obs1'];
		  $marca = $row['marca1'];
		  break;
		  case 2:
		  $id_fornecedor = $row['fornecedor2'];
  		  $imposto = $row['imposto2'];
		  $frete = $row['frete2'];
		  $desconto = $row['desconto2'];
		  $valor_uni = $row['valor_uni2'];
		  $valor = $row['preco2'];
		  $prazo = $row['prazo2'];
		  $obs = $row['obs2'];
		  $marca = $row['marca2'];
		  break;
		  case 3:
		  $id_fornecedor = $row['fornecedor3'];
		  $imposto = $row['imposto3'];
		  $frete = $row['frete3'];
		  $desconto = $row['desconto3'];
		  $valor_uni = $row['valor_uni3'];
		  $valor = $row['preco3'];
		  $prazo = $row['prazo3'];
		  $obs = $row['obs3'];
		  $marca = $row['marca3'];
		  break;
		  }
		  
		  $result_fornecedor = mysql_query("SELECT * FROM fornecedor_site where fornecedor_site_id = '$row[fornecedor_escolhido]'");
		  $row_fornecedor = mysql_fetch_array($result_fornecedor);
		  

		  
          ?>
          <table class="relacao"  style="margin-top:30px;">
          <tr>
          	<td class="secao">Fornecedor 1:</td>
          	<td align="left"><?php echo $row_fornecedor['razao']?> </td>
          	<td class="secao">email:</td>
          	<td align="left"><?php echo $row_fornecedor['email'];?></td>
          
          	<td class="secao">Telefone:</td>
          	<td align="left"><?php echo $row_fornecedor['telefone'];?></td>
          </tr>
          </table>
          
          
                    
           <table width="96%" align="center" border = "1" cellpadding="0" cellspacing="0" class="relacao" style=" border-collapse:collapse;"> 
              <tr>
                <td colspan="6" class="titulo_tabela1">  DESCRI&Ccedil;&Atilde;O DO PROCESSO</td>
              </tr>
              <tr >
                <td  valign="middle" class="secao">ITEM: &nbsp;</td>
                <td colspan="5" align="left" valign="middle" >&nbsp;<?php print "$row[nome_produto]";?></td>
              </tr>
              <tr >
                <td  align="right" valign="middle" ><strong>DESCRI&Ccedil;&Atilde;O: &nbsp;</strong></td>
                <td  colspan="5" align="left" valign="middle">&nbsp;<?php print "$row[descricao_produto]";?></td>
              </tr>
              <tr >
                <td  valign="middle" class="secao"><strong>Quantidade: &nbsp;</strong></td> 
                <td  colspan="5" align="left" valign="middle" >&nbsp;
				<?php print "$row[quantidade]";?></td>
              </tr>
              <tr >
                <td  align="right" valign="middle" class="secao"><strong>necessidade: &nbsp;</strong></td>
                <td  colspan="5" align="left" valign="middle" >&nbsp;
				<?php print "$row[necessidade]";?></td>
              </tr>
             
             
              <tr class="titulo_tabela1">
                <td  colspan="2" >NECESSARIO PARA:</td>
                <td >DATA DO PEDIDO</td>
                <td >DATA PARA ENTREGA</td>
                <td ><div align="center">
                  <div align="center" class="style11">QUANTIDADE</div>
                </div>
                </td>
                <td>SOLICITADO PO</td>
              </tr>
              <tr >
                <td  colspan="2" align="center" valign="middle" ><?php print "$row[data_produto]";?></td>
                <td align="center" valign="middle" ><?php print "$row[data_requisicao]";?></td>
                <td  align="center" valign="middle" ><?php print "$prazo";?></td>
                <td  align="center" valign="middle" ><?php print "$row[quantidade]";?></td>
                <td  align="center" valign="middle" ><?php print "$row_user[nome1]";?></td>
              </tr>
              
</table>
<br> 
<br>
<br> 
<p>
SELECIONE O TIPO DE SAÍDA PARA O FINANCEIRO:
</p> 
      
<p>
<input type="radio" id="mostrarfixo" name="opcao" value="1" />
FIXA &nbsp;&nbsp;&nbsp;&nbsp;
<input type="radio" id="mostrarvariavel" name="opcao" value="0" />VARIÁVEL 
<br> 
<br>
<br>   
</p>   
   
<div id='variavel' style="display:none">
<table width="100%">
  <tr>
 <form name="form3" id="form3" action="atualizar_prestador.php">        

  	<td colspan="2" align="center">DADOS DA SAÍDA PARA O FINANCEIRO</td>
  </tr>
  <tr>
  <td align="right">VALOR LIMITE:</td>
  <td align="left"><input type="text" name="valorlimite" id="valor2"/></td>
  </tr>
  <tr>
  <td colspan="2" align="center"><br><br>
                  <input type="hidden" name="id" value="3" />
                  <input type="hidden" name="regiao" value="<?php print "$regiao";?>" />
                  <input type="hidden" name="compra" value="<?php print "$row[0]";?>" />

  <input type="submit" name="ENVIAR PARA O FINANCEIRO" value="ENVIAR PARA O FINANCEIRO"</td>
  </tr>
  </table>
</form>
</div>

         
<div id='fixo' style="display:none">
         
 <form name="formsaida" id="formsaida" method="post" action="gerar_saida.php">        
<table width="100%">
  <tr>
  	<td colspan="2" align="center">DADOS DA SAÍDA PARA O FINANCEIRO</td>
  </tr>
  <tr>
  	<td align="right">Data do 1º pagamento: </td>
    <td align="left"><input type="text" name="primero_pg" id="primeiro_pg"/></td>
  </tr>
  <tr>
  	<td align="right">Valor:</td>
    <td align="left"><input type="text" name="valor" id="valor"/></td>
  </tr>
  
  <tr>
  	<td align="right">Parcelas:</td>
    <td align="left"><input type="text" name="n_parcelas" id="n_parcelas" /></td>
  </tr>
  <tr>
  	<td align="right">Banco:</td>
  	<td align="left">
    <select name="banco" id="banco">
    <?php
	$qr_banco = mysql_query("SELECT * FROM bancos WHERE id_regiao = '$regiao'");
	while($row_banco = mysql_fetch_assoc($qr_banco)):
	
	echo '<option value="'.$row_banco['id_banco'].'">'.$row_banco['nome'].' C: '.$row_banco['conta'].' AG: '.$row_banco['agencia'].'</option> ';	
	
	endwhile;
	?>
    </select>
    </td>
  </tr>
  <tr>
  	<td align="right">Especificação:</td>
    <td align="left"> <input type="text" name="especificacao" id="especificacao"/> </td>
  </tr>  
  <tr>
  	<td colspan="2"><input type="button" name="ok" id="ok" value="Visualizar"/></td>
  </tr>
</table>    
  

<input type="hidden" name="regiao" value="<?php print "$regiao";?>" />
<input type="hidden" name="compra" value="<?php print "$row[0]";?>" />   
<input type="hidden" name="projeto" value="<?php print "$row[2]";?>" />   
<input type="hidden" name="nome" value="<?php print "$row[nome_produto]";?>" />   




  <div id="valores_saida"></div>
 </form>    
            <div align="center"><br />
              <?php print "<a href='../gestaocompras2.php?id=1&regiao=$regiao'><img src='../imagens/voltar.gif' border=0></a>"; ?> <Br />
              
            </div></td>
        </tr>
        
        <tr >
          <td ><div align="center"><span class="style12"><strong><br />
<?php
$end = new empresa();
$end -> endereco('#003300','7px');
?>&nbsp;&nbsp; &nbsp; </span></div></td>
        </tr>
      </table>

<?php

$rod = new empresa();
$rod -> rodape();
?>

</div>
</div>
</body>
</html>
<?php
}else{
	
	?>
    

<?php

$regiao = $_REQUEST['regiao'];
$compra = $_REQUEST['compra'];
$id = $_REQUEST['id'];

switch ($id) {
case 1:

print "<link href='../adm/css/estrutura.css' rel='stylesheet' type='text/css'>
<br>
<div id='corpo'>
<div id='conteudo'>
<hr>
<center>
SELECIONE A REGIÃO<br><br>
<form action='confirmandocompra.php' method='post' name=form1'>
<select name='regiao' class='campotexto' id='regiao'>

";
$sql = mysql_query("SELECT * from regioes");
while ($row = mysql_fetch_array($sql)){
  print "<option value=$row[id_regiao]>$row[regiao] - $row[sigla]</option>";
}
print "</select><br><br>
<input type='submit' name='Enviar' id='Enviar' value='Enviar'>
<input type='hidden' name='id' value='2' />
<input type='hidden' name='compra' value='$compra' />
<input type='hidden' name='financeiro' value='1' />
</form>
";

break;

case 2:

$regiao = $_REQUEST['regiao'];
$id_user = $_COOKIE['logado'];
$compra = $_REQUEST['compra'];
$data = date('Y-m-d');
$data_proc = date('Y-m-d H:i:s');
$tipo = "66";

$result = mysql_query("SELECT * FROM compra2 where id_compra = '$compra'");
$row = mysql_fetch_array($result);


	
	mysql_query("UPDATE compra2 SET id_user_aprovacao='$id_user', data_decisao='$data', acompanhamento = '7' where id_compra = '$compra'") or die ("<center>ERRO!<br> tente novamente mais tarde<br><br>".mysql_error());


	///mysql_query("UPDATE compra SET id_user_aprovacao='$id_user', data_decisao='$data', acompanhamento = '5' where id_compra = '$compra'") or die ("<center>ERRO!<br> tente novamente mais tarde<br><br>".mysql_error());



mysql_query("INSERT INTO saida(id_regiao,id_user,nome,especifica,tipo,valor,data_proc,data_vencimento,id_compra) values 
('$regiao','$id_user','$row[nome_produto]','$row[descricao_produto]','$tipo','$row[preco_final]','$data_proc','$row[data_produto]','$compra')") or die ("O servidor não respondeu conforme deveria, tente novamente mais tarde, Obrigado!<br><hr>".mysql_error());

$id_saida = mysql_insert_id();

$qr_assoc = mysql_query("INSERT INTO compra_saida_assoc (id_compra, id_saida) VALUES ('$compra', '$id_saida')");


print "<link href='adm/css/estrutura.css' rel='stylesheet' type='text/css'>
<br>
<div id='corpo'>
<div id='conteudo'>
<link href=\"../net.css\" rel=\"stylesheet\" type=\"text/css\">
<br>
<hr>
<center>
<font color=#FFFFFF>
Informações gravadas com sucesso!<br><br>
</font>
<br><br>
<a href='../gestaocompras.php?id=1&regiao=$regiao'><img src='imagens/voltar.gif' border=0></a>
</center>
";


break;

case 3:

$valorlimite = $_REQUEST['valorlimite'];
$qry_select = mysql_query("SELECT * FROM prestadorservico WHERE id_compra='$row[0]'");
$dados_prest = mysql_fetch_assoc($qry_select);

echo "Prestador= ".$row['0'];

	mysql_query("UPDATE prestadorservico SET 
		valor_limite = '$valorlimite' WHERE id_prestador = '$dados_prest[0]' ") or die ("Erro<br>".mysql_error());
		
		
print "<link href='adm/css/estrutura.css' rel='stylesheet' type='text/css'>
<br>
<div id='corpo'>
<div id='conteudo'>
<link href=\"../net.css\" rel=\"stylesheet\" type=\"text/css\">
<br>
<hr>
<center>
<font color=#FFFFFF>
Informações gravadas com sucesso!<br><br>
</font>
<br><br>
<a href='../gestaocompras.php?id=1&regiao=$regiao'><img src='imagens/voltar.gif' border=0></a>
</center>
";

echo "OK";





break;
}

}
?>
</div>
</div>
</body>
</html>
<?php

}



?>