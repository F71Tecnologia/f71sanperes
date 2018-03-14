<?php

if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
exit;
}

include "../conn.php";
$id = $_REQUEST['id'];
$user = $_COOKIE['logado'];
$data_cad = date('Y-m-d');
$id_regiao = $_REQUEST['id_regiao'];

/* 
Função para converter a data
De formato nacional para formato americano.
Muito útil para você inserir data no mysql e visualizar depois data do mysql.
*/


function ConverteData($Data){
 if (strstr($Data, "/"))//verifica se tem a barra /
 {
  $d = explode ("/", $Data);//tira a barra
 $rstData = "$d[2]-$d[1]-$d[0]";//separa as datas $d[2] = ano $d[1] = mes etc...
 return $rstData;
 } elseif(strstr($Data, "-")){
 $d = explode ("-", $Data);
 $rstData = "$d[2]/$d[1]/$d[0]"; 
 return $rstData;
 }else{
 return "0";
 }
}

switch($id){
	case 1:		//CADASTRO DE VEÍCULOS
	
	$marca = $_REQUEST['marca'];
	$modelo = $_REQUEST['modelo'];
	$ano = $_REQUEST['ano'];
	$fab = $_REQUEST['fab'];
	$placa = $_REQUEST['placa'];
	$apolice = $_REQUEST['apolice'];
	$telefone = $_REQUEST['telefone'];
	$regiao = $_REQUEST['regiao'];
		
	$arquivo = isset($_FILES['foto']) ? $_FILES['foto'] : FALSE;
	
	//AQUI TEM FOTO
	if($arquivo['error'] == 0){
	
	//aki a imagem nao corresponde com as extenções especificadas
	if($arquivo[type] != "image/x-png" && $arquivo[type] != "image/pjpeg" && $arquivo[type] != "image/gif" 
	&& $arquivo[type] != "image/jpe") {     

		print "<center>
		<hr><font size=2><b>
		Tipo de arquivo não permitido, os únicos padrões permitidos são .gif, .jpg , .jpeg ou .png<br>
		$arquivo[type] <br><br>
		<a href='javascript:history.go(-1)'>Voltar</a>
		</b></font>"; 
	exit;
	
	//aqui o arquivo é realente de imagem e vai ser carregado para o servidor
	} else {  
	
	$arr_basename = explode(".",$arquivo['name']); 
	$file_type = $arr_basename[1]; 
   
	if($file_type == "gif"){
		$tipo_name =".gif"; 
	}elseif($file_type == "jpg" or $arquivo[type] == "jpeg"){
		$tipo_name =".jpg"; 
    }elseif($file_type == "png") { 
		$tipo_name =".png"; 
	} 
	
	$foto = $tipo_name;
	
	mysql_query("INSERT INTO fr_carro (id_regiao,marca,modelo,ano,fab,placa,apolice,telefone,foto,user,data_cad)
	VALUES ('$regiao', '$marca', '$modelo', '$ano', '$fab', '$placa', '$apolice', '$telefone', '$foto', '$user', '$data_cad')") 
	or die (mysql_error());
	
	$id_insert = mysql_insert_id();

	// Resolvendo o nome e para onde o arquivo será movido
    $diretorio = "fotos/";

	$nome_tmp = "carro".$id_insert.$tipo_name;
	$nome_arquivo = "$diretorio$nome_tmp" ;
	
	move_uploaded_file($arquivo['tmp_name'], $nome_arquivo ) or die ("Erro ao enviar o Arquivo: $nome_arquivo");
	
	//aqui fecha o IF que verificar se o arquivo tem a extenção especificada
	
	} 
	
	}else{
		
		mysql_query("INSERT INTO fr_carro (id_regiao,marca,modelo,ano,fab,placa,apolice,telefone,user,data_cad)
		VALUES ('$regiao', '$marca', '$modelo', '$ano', '$fab', '$placa', '$apolice', '$telefone', '$user', '$data_cad')") 
		or die (mysql_error());
	}
	
	
	print "<script>
	location.href = 'frota.php?regiao=$id_regiao';
	</script>";
	
	break;
	
	case 2:		//CADASTRO DE COMBUSTIVEL
	
	$interno = $_REQUEST['interno']; //SIM OU NÃO ( 1 OU 2 )
	$veiculo = $_REQUEST['veiculo'];
	$carro = $_REQUEST['veiculo2'];
	$placa = $_REQUEST['placa'];
	
	$funcionario = $_REQUEST['funcionario']; //INTERNO OU NÃO ( 1 OU 2 )
	$userw = $_REQUEST['user'];
	$nome = $_REQUEST['nome'];
	$rg = $_REQUEST['rg'];
	
	$destino = $_REQUEST['destino'];
	$origem = $_REQUEST['regiao'];
	
	$kmatual = $_REQUEST['kmatual'];
	
	$dataT = $_REQUEST['dataT'];
	
	$dataT = ConverteData($dataT);
	
	mysql_query("INSERT INTO fr_combustivel(id_carro,id_user,id_regiao,funcionario,nome,rg,interno,carro,placa,data,destino,user_cad,data_cad,status_reg,kmatual) 
	VALUES ('$veiculo', '$userw', '$origem', '$funcionario', '$nome', '$rg', '$interno', '$carro', '$placa', '$dataT', '$destino','$user', '$data_cad', '1','$kmatual')");
	
	print "<script>
	location.href = 'frota.php?regiao=$id_regiao';
	</script>";
	
	break;
	
	case 3:
	
	$veiculo = $_REQUEST['veiculo'];
	$destino = $_REQUEST['destino3'];
	$origem = $_REQUEST['regiao3'];
	$responsavel = $_REQUEST['responsavel'];
	$data = $_REQUEST['data3'];
	$km = $_REQUEST['km'];
	
	$data = ConverteData($data);

	mysql_query("INSERT INTO fr_rota (id_carro,id_regiao,id_user,destino,data,kmini,user_cad,data_cad,status_reg) 
	VALUES ('$veiculo', '$origem', '$responsavel', '$destino', '$data', '$km', '$user', '$data', '1')");
	
	
	print "<script>
	location.href = 'frota.php?regiao=$id_regiao';
	</script>";
	
	break;
	
	case 4:
	
	//CASO NÃO VENHA O CAMPO KM.. VAI RODAR A PARTE DE PREENCHIMENTO DE ENTREGA
	if(empty($_REQUEST['km'])){
		$rota = $_REQUEST['rota'];
	
		$RE_rota = mysql_query("SELECT *, date_format(data, '%d/%m/%Y')as data FROM fr_rota WHERE id_rota = '$rota'");
		$RowRota = mysql_fetch_array($RE_rota);
		
		$RE_carros = mysql_query("SELECT * FROM fr_carro WHERE id_carro = '$RowRota[id_carro]'");
		$RowCarros = mysql_fetch_array($RE_carros);
			  
		$RE_re = mysql_query("SELECT * FROM regioes WHERE id_regiao = '$RowRota[id_regiao]'");
		$RowRE = mysql_fetch_array($RE_re);
		
		$data_entrega = date('d/m/Y H:i:s');
		
		print "
		<script language=\"javascript\">
		
		function mascara_data(d){  
		var mydata = '';  
		data = d.value;  
		mydata = mydata + data;  
		if (mydata.length == 2){  
		mydata = mydata + '/';  
		d.value = mydata;  
		}  
		if (mydata.length == 5){  
		mydata = mydata + '/';  
		d.value = mydata;  
		}  
		if (mydata.length == 10){  
		verifica_data(d);  
		}  
		} 


		function verifica_data(d) {  
		dia = (d.value.substring(0,2));  
		mes = (d.value.substring(3,5));  
		ano = (d.value.substring(6,10));  
		situacao = \"\";  
		// verifica o dia valido para cada mes  
		if ((dia < 01)||(dia < 01 || dia > 30) && (  mes == 04 || mes == 06 || mes == 09 || mes == 11 ) || dia > 31) {  
		situacao = \"falsa\";  
		}  
		// verifica se o mes e valido  
		if (mes < 01 || mes > 12 ) {  
		situacao = \"falsa\";  
		}  
		// verifica se e ano bissexto  
		if (mes == 2 && ( dia < 01 || dia > 29 || ( dia > 28 && (parseInt(ano / 4) != ano / 4)))) {  
		situacao = \"falsa\";  
		}  
		if (d.value == \"\") {  
		situacao = \"falsa\";  
		}  
		if (situacao == \"falsa\") {  
		alert(\"Data digitada é inválida, digite novamente!\"); 
		d.value = \"\";  
		d.focus();  
		}  
		}

		function CorFundo(campo,cor){
			var d = document;
				if(cor == 1){
				var color = \"#CCFFCC\";
			}else{
				var color = \"#FFFFFF\";
			}

			d.getElementById(campo).style.background=color;
	
		}
		

        function validaForm1(){
        	d = document.form1;

        	if (d.km.value == \"\"){
        		alert(\"O campo Km Final deve ser preenchido!\");
        		d.km.focus();
        		return false;
        	}
        }			

		</script>
		<form action='frotacad.php' method='post' name='form1' onSubmit=\"return validaForm1()\">
		<table width='100%' border='0' cellpadding='0' cellspacing='3' bgcolor='#000000'>
		  <tr class='campotexto'>
		    <td height='28' align='right' valign='middle' bgcolor='#CCFFCC'>Ve&iacute;culo:</td>
		    <td height='28' align='left' valign='middle' bgcolor='#CCCCCC'>&nbsp;&nbsp;$RowCarros[modelo]</td>
		    <td height='28' align='center' valign='middle' bgcolor='#CCFFCC'> Origem:</td>
		    <td height='28' align='left' valign='middle' bgcolor='#CCCCCC'>&nbsp;&nbsp;$RowRE[regiao]</td>
		  </tr>
		  <tr class='campotexto'>
		    <td width='188' height='28' align='right'  valign='middle' bgcolor='#CCFFCC'>Km Inicial:</td>
		    <td height='28' align='left' valign='middle' bgcolor='#CCCCCC'>&nbsp;&nbsp;$RowRota[kmini]</td>
		    <td height='28' align='center' valign='middle' bgcolor='#CCFFCC'>Km Final:</td>
		    <td height='28' align='left' valign='middle' bgcolor='#CCCCCC'>&nbsp;&nbsp;
				    <input name='km' type='text' id='km' size='13' class='campotexto'
					onfocus=\"CorFundo(this.id,1)\" onblur=\"CorFundo(this.id,2)\"/></td>
		  </tr>
		  <tr class='campotexto'>
		    <td height='28' align='right' valign='middle' bgcolor='#CCFFCC'>Data Saída:</td>
		    <td width='447' height='28' align='left' valign='middle' bgcolor='#CCCCCC'>&nbsp;&nbsp;$RowRota[data]</td>
		    <td width='132' height='28' align='center' valign='middle' bgcolor='#CCFFCC'>Data Entrega:</td>
		    <td width='345' height='28' align='left' valign='middle' bgcolor='#CCCCCC'>&nbsp;&nbsp;$data_entrega</td>
		  </tr>
		  <tr class='campotexto'>
		    <td height='28' colspan='4' align='center' valign='middle' bgcolor='#CCFFCC'><label>
		      <input type='hidden' name='id' id='id' value='4' />
			  <input type='hidden' name='rota' id='rota' value='$rota' />
		      <input type='submit' name='enviar3' id='enviar3' value='Gravar' />
		    </label></td>
		  </tr>
		</table>
		</form>
		
		";

	
	//COM O CAMPO KM PREENCHIDO VAMOS ATUALIZAR A ROTA, COLOCANDO O STATUS COMO 2 ( ENTREGUE )
	}else{
		
		$rota = $_REQUEST['rota'];
		$km = $_REQUEST['km'];
		
		$da_ent = date('Y-m-d H:i:s');
		
		mysql_query("UPDATE fr_rota SET kmfim = '$km', data_ent = '$da_ent', status_reg = '2' where id_rota = '$rota'");
		
		print "<script>
		location.href = 'frota.php?regiao=$id_regiao';
		</script>";
				
	}
	
	break;
	
	//CADASTRO DE MULTAS
	case 5:
	
	$veiculo2 = $_REQUEST['veiculo2'];
	$rota = $_REQUEST['rota'];
	$tipo = $_REQUEST['tipo'];
	$local = $_REQUEST['local'];
	$infrator = $_REQUEST['infrator'];
	$data4 = $_REQUEST['data4'];
	$cnh = $_REQUEST['cnh'];
	
	$data = ConverteData($data4);
		
	mysql_query("INSERT INTO fr_multa (id_carro,id_rota,id_user,tipo,local,data,cnh,user_cad,data_cad,status_reg)
	VALUES ('$veiculo2', '$rota', '$infrator', '$tipo', '$local', '$data', '$cnh', '$user', '$data_cad', '1')");
	
	print "<script>
	location.href = 'frota.php?regiao=$id_regiao';
	</script>";
	
	break;
}
?>

