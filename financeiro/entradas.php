<?php
include ("include/restricoes.php");
include "../conn.php";
include ("../funcoes.php");



$regiao  = $_REQUEST['regiao'];
$id_user = $_COOKIE['logado'];

//ENCRIPTOGRAFANDO
$linkEnc = encrypt($regiao);
$linkEnc = str_replace("+","--",$linkEnc);

$query_regioes = mysql_query("SELECT id_master FROM regioes WHERE id_regiao  = '$regiao' LIMIT 1");
$id_master = @mysql_result($query_regioes,0);


if(empty($_REQUEST['id'])){



?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<head>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<title>Intranet - Financeiro - Entradas</title>

<script type="text/javascript" src="../jquery/jquery-1.4.2.min.js" ></script>
<script type="text/javascript">
$(function(){
	
	$('#subtipo').change(function(){
		if($(this).val() == 4){
			$('#campo_n_subtipo').hide().find('input[type=text]').val('');
		}else{
			$('#campo_n_subtipo').show();
		}
	});
	
	$('#select_tipo').change(function(){
		
		if($(this).val() != '12') {
			$('.bloco_notas').hide();
			return false;
		}
		
		$('.bloco_notas').show();
		
	});
	
	$('#regiao_notas').change(function (){
		
		var valor = $(this).val();
		$('#parceiros_notas').html('<option value="">Carregando...</option>');
		
		$.ajax({
			url : 'actions/combo.entradas.php',
			data : { 'parceiros' : true, 'regiao' : valor },
			success :  function(registro){

				$('#parceiros_notas').html('');
				
				/*$.each(registro, function(i,campos){
					
					$('#parceiros_notas').append('<option value="'+campos.parceiro_id+'">'+campos.parceiro_nome+'</option>'); 
					
				});
				$('#parceiros_notas').change();*/
				
				if(registro.ativos.length != 0){
					$('#parceiros_notas').append('<optgroup label="Ativos">');
					$.each(registro.ativos, function(i,campos){
						
						$('#parceiros_notas').append('<option value="'+campos.parceiro_id+'">'+campos.parceiro_nome+'</option>'); 
						
					});
					$('#parceiros_notas').append('</optgroup>');
				}
				
				if(registro.desativados.length != 0){
					$('#parceiros_notas').append('<optgroup label="Desativados">');
					$.each(registro.desativados, function(i,campos){
						
						$('#parceiros_notas').append('<option value="'+campos.parceiro_id+'">'+campos.parceiro_nome+'</option>'); 
						
					});
					$('#parceiros_notas').append('</optgroup>');
				}
				$('#parceiros_notas').change();				
				
			},
			dataType : 'json'
		});
		
		
	});
	
	
	$('#parceiros_notas').change(function (){
	
		var id_parceiro = $(this).val();
		
		$('#notas').html('<div style="text-align: center;">Carregando....</div>');
		
		
		$.ajax({
			
			url : 'actions/combo.entradas.php',
			data : { 'notas' : true, 'id_parceiro' :  id_parceiro},
			dataType : 'json',
			success : function(registros){
				
				
				
				if(registros.erro == '1'){
					$('#notas').html('<center>Nenhuma nota cadastrada!</center>');
					return false;
				}
				
				var table = '<table>';
				
				table += '<tr>\n\
								<td colspan="4" align="center">Notas</td>\n\
							</tr>';
				
				table += '<tr>\n\
								<td></td>\n\
								<td>Nº da nota</td>\n\
								<td>data</td>\n\
								<td>Valor</td>\n\
								<td>Ver Nota</td>\n\
							</tr>';
							
				
				var alternateColor = 0;
				// LOOP DAS NOTAS NÃO ASSOCIADAS
				$.each(registros.nao_associada, function (i, valor){
					

					if(alternateColor==0){
						
						var classe = 'linha_um';
						alternateColor = 1;
					}else{
						var classe = 'linha_dois';
						alternateColor = 0;
					}
					
					if(valor.anexo != ''){
						var link = '<a target="_blank" href="'+valor.anexo+'">Ver</a>';
					}else{
						var link = '';
					}
					
					table += '<tr class="' + classe + '" >\n\
								<td><input type="radio" name="radio_nota" value="' + valor.id_notas + '"/></td>\n\
								<td>' + valor.numero + '</td>\n\
								<td>' + valor.data_emissao + '</td>\n\
								<td>R$ ' + valor.valor + '</td>\n\
								<td align="center">'+link+'</td>\n\
							</tr>';
														
				});
				
				
				table += '</table>';
				
				// LOOP DAS NOTAS ASSOCIADAS
				if(registros.associada.length != 0){
					
					table +=  '<table>\n\
								<tr class="tr_title">\n\
									<td colspan="7"><center>Notas associados</center></td>\n\
								</tr>\n\
								<tr>\n\
									<td></td>\n\
									<td>Nº nota</td>\n\
									<td>data</td>\n\
									<td>valor</td>\n\
									<td>entradas</td>\n\
									<td>ver entradas</td>\n\
									<td>ver notas</td>\n\
								</tr>';
					
					
				
				
				$.each(registros.associada, function (i, valor){
					
					
					if(alternateColor==0){
						
						var classe = 'linha_um';
						alternateColor = 1;
					}else{
						var classe = 'linha_dois';
						alternateColor = 0;
					}
					
					if(valor.anexo != ''){
						var link = '<a target="_blank" href="'+valor.anexo+'">Ver</a>';
					}else{
						var link = '';
					}
					
					var ver_entrada = '<a href="'+valor.link_entrada+'" >ver Entradas</a>';
					
					
					if(valor.checked == 1){
						var marcado = 'checked="checked"';
					}else{
						var marcado = '';
					}

					table += '<tr class="'+alternateColor+'">\n\
									<td><input ' + marcado + ' type="radio" name="radio_nota" value="' + valor.id_notas +'" /></td>\n\
									<td>'+valor.numero+'</td>\n\
									<td>'+valor.data_emissao+'</td>\n\
									<td>'+valor.valor+'</td>\n\
									<td>'+valor.total_entrada+'</td>\n\
									<td>'+ver_entrada+'</td>\n\
									<td>'+link+'</td>\n\
								</tr>';
					
				});
				
				}
				
				table += '</table>';
				
				$('#notas').html(table);
				return false;
				
			}
			
		});
		
		
	});
	
	$('#notas table tr').live('mouseout', function(){
		
		$(this).removeClass('linha_ativa');
		
	});
	
	$('#notas table tr').live('click',function(){
		
		$(this).find('input').attr('checked', true);
		
	});
	
/*	$('#regiao_notas').change(function (){
		
		var valor = $(this).val();
		$('#parceiros_notas').html('<option value="">Carregando...</option>');
		
		$.ajax({
			url : 'actions/combo.entradas.php',
			data : { 'parceiros' : true, 'regiao' : valor },
			success :  function(registro){
				
				if(registro.erro == '1'){
					$('#parceiros_notas').html('<option value="">Nenhum parceiro</option>');
					$('#parceiros_notas').change();
					return false;
					
				}
				console.log(registro);
				$('#parceiros_notas').html('');
				
				$.each(registro, function(i,campos){
					
					$('#parceiros_notas').append('<option value="'+campos.parceiro_id+'">'+campos.parceiro_nome+'</option>'); 
					
				});
				$('#parceiros_notas').change();
				
				
			},
			dataType : 'json'
		});
		
		
	});
	
	$('#notas table tr').live('mouseover',function(){
	
		$(this).addClass('linha_ativa');
		
	});
	$('#notas table tr').live('mouseout', function(){
		
		$(this).removeClass('linha_ativa');
		
	});
	
	$('#notas table tr').live('click',function(){
		
		$(this).find('input').attr('checked', true);
		
	});
	
	$('#parceiros_notas').change(function (){
	
		var id_parceiro = $(this).val();
		
		$('#notas').html('<div style="text-align: center;">Carregando....</div>');
		
		
		$.ajax({
			
			url : 'actions/combo.entradas.php',
			data : { 'notas' : true, 'id_parceiro' :  id_parceiro},
			dataType : 'json',
			success : function(registros){

				if(registros.erro == '1'){
					$('#notas').html('<center>Nenhuma nota cadastrada!</center>');
					return false;
				}
				
				var table = '<table>';
				
				table += '<tr>\n\
								<td colspan="4" align="center">Notas</td>\n\
							</tr>';
				
				table += '<tr>\n\
								<td></td>\n\
								<td>Nº da nota</td>\n\
								<td>data</td>\n\
								<td>Valor</td>\n\
								<td>Ver Nota</td>\n\
							</tr>';
				var alternateColor = 0;
				$.each(registros, function (i, valor){
					

					if(alternateColor==0){
						
						var classe = 'linha_um';
						alternateColor = 1;
					}else{
						var classe = 'linha_dois';
						alternateColor = 0;
					}
					
					if(valor.anexo != ''){
						var link = '<a target="_blank" href="'+valor.anexo+'">Ver</a>';
					}else{
						var link = '';
					}
					table += '<tr class="'+classe+'" >\n\
								<td><input type="radio" name="radio_nota" value="'+valor.id_notas+'"/></td>\n\
								<td>'+valor.numero+'</td>\n\
								<td>'+valor.data_emissao+'</td>\n\
								<td>'+valor.valor+'</td>\n\
								<td align="center">'+link+'</td>\n\
							</tr>';
														
				});
				
				table += '</table>';
				
				$('#notas').html(table);
			}
			
		});
		
		
	});
*/	
	
});	
</script>

<style type="text/css">

<!--

body {

	

	font-family:Arial, Helvetica, sans-serif;

	margin-left: 0px;

	margin-top: 0px;

	margin-right: 0px;

	margin-bottom: 0px;
	

}

.menusCima {

	color:#FFF;

	font-size:12px;

	text-decoration:none;

}

.linkMenu {

	text-decoration:none;

	color:#FFF;

}

.titulosTab {

	color:#FFF;

	font-size:10px;

	font-weight:bold;

	border-bottom:#666 solid 1px;

}

.linhaspeq{

	font-size:11px;

}

.style25 {	font-size: 11px;

	font-weight: bold;

}

#notas table { width: 100%; }

.linha_um {
	background-color:#FAFAFA;
}

.linha_dois {
	background-color:#F3F3F3;
}

.linha_ativa {
	background-color: #adaaaa; 
	color: #FFFFFF;
}
tr.linha_um, tr.linha_dois {
	font-size	:	13px;
	padding		:	4px; 
	font-weight	:	normal;
}
-->

</style>

<link href="../net1.css" rel="stylesheet" type="text/css" />

</head>

<?php

print "

<script>

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

           

         function verifica_data (d) {  

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



function FormataValor(objeto,teclapres,tammax,decimais) 
{
    var tecla            = teclapres.keyCode;
    var tamanhoObjeto    = objeto.value.length;
    if ((tecla == 8) && (tamanhoObjeto == tammax))

    {
        tamanhoObjeto = tamanhoObjeto - 1 ;

    }
	
if (( tecla == 8 || tecla == 88 || tecla >= 48 && tecla <= 57 || tecla >= 96 && tecla <= 105 ) && ((tamanhoObjeto+1) <= tammax))

    {
        vr    = objeto.value;

        vr    = vr.replace( \"/\", \"\" );
        vr    = vr.replace( \"/\", \"\" );
        vr    = vr.replace( \",\", \"\" );
        vr    = vr.replace( \".\", \"\" );
        vr    = vr.replace( \".\", \"\" );
        vr    = vr.replace( \".\", \"\" );
        vr    = vr.replace( \".\", \"\" );
        tam    = vr.length;

        if (tam < tammax && tecla != 8)
        {
            tam = vr.length + 1 ;
        }

        if ((tecla == 8) && (tam > 1))
        {
            tam = tam - 1 ;
            vr = objeto.value;
            vr = vr.replace( \"/\", \"\" );
            vr = vr.replace( \"/\", \"\" );
            vr = vr.replace( \",\", \"\" );
            vr = vr.replace( \".\", \"\" );
            vr = vr.replace( \".\", \"\" );
            vr = vr.replace( \".\", \"\" );
            vr = vr.replace( \".\", \"\" );
        }

    

        //Cálculo para casas decimais setadas por parametro

        if ( tecla == 8 || tecla >= 48 && tecla <= 57 || tecla >= 96 && tecla <= 105 )

        {

            if (decimais > 0)

            {

                if ( (tam <= decimais) )

                { 

                    objeto.value = (\"0,\" + vr) ;

                }

                if( (tam == (decimais + 1)) && (tecla == 8))

                {

                    objeto.value = vr.substr( 0, (tam - decimais)) + ',' + vr.substr( tam - (decimais), tam ) ;    

                }

                if ( (tam > (decimais + 1)) && (tam <= (decimais + 3)) &&  ((vr.substr(0,1)) == \"0\"))

                {

                    objeto.value = vr.substr( 1, (tam - (decimais+1))) + ',' + vr.substr( tam - (decimais), tam ) ;

                }

                if ( (tam > (decimais + 1)) && (tam <= (decimais + 3)) &&  ((vr.substr(0,1)) != \"0\"))

                {

                    objeto.value = vr.substr( 0, tam - decimais ) + ',' + vr.substr( tam - decimais, tam ) ; 

                }

                if ( (tam >= (decimais + 4)) && (tam <= (decimais + 6)) ) { objeto.value = vr.substr( 0, tam - (decimais + 3) ) + '.' + vr.substr( tam - (decimais + 3), 3 ) + ',' + vr.substr( tam - decimais, tam ) ;  }

                 if ( (tam >= (decimais + 7)) && (tam <= (decimais + 9)) )
                {
				  objeto.value = vr.substr( 0, tam - (decimais + 6) ) + '.' + vr.substr( tam - (decimais + 6), 3 ) + '.' + vr.substr( tam - (decimais + 3), 3 ) + ',' + vr.substr( tam - decimais, tam ) ;
                }

                if ( (tam >= (decimais + 10)) && (tam <= (decimais + 12)) )
                {

                     objeto.value = vr.substr( 0, tam - (decimais + 9) ) + '.' + vr.substr( tam - (decimais + 9), 3 ) + '.' + vr.substr( tam - (decimais + 6), 3 ) + '.' + vr.substr( tam - (decimais + 3), 3 ) + ',' + vr.substr( tam - decimais, tam ) ;

                }

                if ( (tam >= (decimais + 13)) && (tam <= (decimais + 15)) )
                {

                     objeto.value = vr.substr( 0, tam - (decimais + 12) ) + '.' + vr.substr( tam - (decimais + 12), 3 ) + '.' + vr.substr( tam - (decimais + 9), 3 ) + '.' + vr.substr( tam - (decimais + 6), 3 ) + '.' + vr.substr( tam - (decimais + 3), 3 ) + ',' + vr.substr( tam - decimais, tam ) ;

                }

            }

            else if(decimais == 0)
            {				

                if ( tam <= 3 )     
				           { 

                     objeto.value = vr ;
                }

                if ( (tam >= 4) && (tam <= 6) )
                {

                    if(tecla == 8)
                    {
						objeto.value = vr.substr(0, tam);
                        window.event.cancelBubble = true;
                        window.event.returnValue = false;
                    }
                    objeto.value = vr.substr(0, tam - 3) + '.' + vr.substr( tam - 3, 3 ); 

                }

                if ( (tam >= 7) && (tam <= 9) )

                {
                    if(tecla == 8)
                    {
                        objeto.value = vr.substr(0, tam);
                        window.event.cancelBubble = true;
                        window.event.returnValue = false;
                    }
                    objeto.value = vr.substr( 0, tam - 6 ) + '.' + vr.substr( tam - 6, 3 ) + '.' + vr.substr( tam - 3, 3 ); 

                }

                if ( (tam >= 10) && (tam <= 12) )
                {
                     if(tecla == 8)
                    {
                        objeto.value = vr.substr(0, tam);
                        window.event.cancelBubble = true;
                        window.event.returnValue = false;
                    }

                    objeto.value = vr.substr( 0, tam - 9 ) + '.' + vr.substr( tam - 9, 3 ) + '.' + vr.substr( tam - 6, 3 ) + '.' + vr.substr( tam - 3, 3 ); 

                }

                if ( (tam >= 13) && (tam <= 15) )
                {
                    if(tecla == 8)
                    {
                        objeto.value = vr.substr(0, tam);
                        window.event.cancelBubble = true;
                        window.event.returnValue = false;
                    }
                    objeto.value = vr.substr( 0, tam - 12 ) + '.' + vr.substr( tam - 12, 3 ) + '.' + vr.substr( tam - 9, 3 ) + '.' + vr.substr( tam - 6, 3 ) + '.' + vr.substr( tam - 3, 3 ) ;

                }            

            }

        }

    }

    else if((window.event.keyCode != 8) && (window.event.keyCode != 9) && (window.event.keyCode != 13) && (window.event.keyCode != 35) && (window.event.keyCode != 36) && (window.event.keyCode != 46))

        {
			window.event.cancelBubble = true;
            window.event.returnValue = false;
        }
} 
</script></head>";
?>

<body>

<table width="700" border="0" bordercolor="#FFFFFF" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" class="bordaescura1px">

<tr>
	<td colspan="4"><a href="../novoFinanceiro/index.php?enc=<?php echo $linkEnc; ?>" style="color: #069"><img src="../img_menu_principal/voltar.png" title="VOLTAR" /></a></td>
</tr>

<tr>
	<td colspan="4">&nbsp;</td>
</tr>

  <tr>
    <td height="25" colspan="4" align="center" valign="middle" background="imagensfinanceiro/barra3.gif"><strong><span class="menusCima">    CADASTRAMENTO DE  ENTRADAS DO FINANCEIRO</span></strong><br /></td>

  </tr>

  <tr>
    <td height="25" colspan="4" align="center" bgcolor="#FFFFFF">
    <form action="entradas.php" method="post" enctype="multipart/form-data" name='form1' onsubmit="return validaForm()" id="form1"> 
    <table width="97%" border="0" cellspacing="1" cellpadding="0" class="bordaescura1px">
      <tr bordercolor="#FFFFFF" bgcolor="#FFFFFF">
        <td height="30" align="right" valign="middle" bgcolor="#EBEBEB" class="linhaspeq"><strong><span class="style2">PROJETO:</span></strong></td>
        <td height="30" align="left" valign="middle" bgcolor="#F6F6F6" class="linhaspeq">&nbsp;
          <?php

$result_projeto = mysql_query("SELECT * FROM projeto where id_regiao = '$regiao' AND status_reg = '1'");

print "<select name='projeto'>";

while($row_projeto = mysql_fetch_array($result_projeto)){

print "<option value=$row_projeto[0]>$row_projeto[id_projeto] - $row_projeto[nome] </option>";

}

print "</select>";

?></td>

      </tr>

      <tr bordercolor="#FFFFFF" bgcolor="#FFFFFF">
        <td height="30" align="right" valign="middle" bgcolor="#EBEBEB" class="linhaspeq"><strong><span class="style16">CONTA PARA CR&Eacute;DITO:</span></strong></td>
        <td height="30" align="left" valign="middle" bgcolor="#F6F6F6" class="linhaspeq">&nbsp;
          <?php

$result_banco = mysql_query("SELECT * FROM bancos WHERE id_regiao = '$regiao' AND interno = '1' AND status_reg = '1' ORDER BY id_banco DESC");

print "<select name='banco'>";

while($row_banco = mysql_fetch_array($result_banco)){

print "<option value=$row_banco[0]>$row_banco[id_banco] - $row_banco[nome] - $row_banco[agencia] / $row_banco[conta]</option>";

}

print "</select>";



?></td>

      </tr>
      <tr bordercolor="#FFFFFF" bgcolor="#FFFFFF">
        <td height="30" align="right" valign="middle" bgcolor="#EBEBEB" class="linhaspeq"><strong><span class="style2">NOME:</span></strong></td>
        <td height="30" align="left" valign="middle" bgcolor="#F6F6F6" class="linhaspeq">&nbsp;
          <input name="nome" type="text" size="70" id="nome" onChange="this.value=this.value.toUpperCase()"/></td>
      </tr>

      <tr bordercolor="#FFFFFF" bgcolor="#FFFFFF">
        <td height="30" align="right" valign="middle" bgcolor="#EBEBEB" class="linhaspeq"><strong><span class="style16">DESCRI&Ccedil;&Atilde;O:</span></strong></td>
        <td height="30" align="left" valign="middle" bgcolor="#F6F6F6" class="linhaspeq">&nbsp;
          <input name="especifica" type="text" size="70" id="especifica" onChange="this.value=this.value.toUpperCase()"/></td>
      </tr>

      <tr bordercolor="#FFFFFF" bgcolor="#FFFFFF">
        <td height="30" align="right" valign="middle" bgcolor="#EBEBEB" class="linhaspeq"><strong><span class="style16">TIPO:</span></strong></td>
        <td height="30" align="left" valign="middle" bgcolor="#F6F6F6" class="linhaspeq">&nbsp;
        <?php

$result_tipo = mysql_query("SELECT * FROM entradaesaida WHERE tipo='1' and grupo='5' ORDER BY nome");
print "<select name='tipo' id='select_tipo'>";


while($row_tipo = mysql_fetch_array($result_tipo)){
print "<option value=$row_tipo[0] title='$row_tipo[descricao]'>$row_tipo[0] - $row_tipo[nome]</option>";

}
print "</select>";
?></td>
      </tr>

      <?php // Regiao notas ?>
      <tr class="bloco_notas" bordercolor="#FFFFFF" bgcolor="#FFFFFF" style="display : none;" >
		<td height="30"  align="right" valign="middle" bgcolor="#EBEBEB" class="linhaspeq"><strong><span class="style16">Região :</span></strong></td>
		<td height="30" align="left" valign="middle" bgcolor="#F6F6F6" class="linhaspeq">&nbsp;
			<select name="regiao_notas" id="regiao_notas">
				<?php  
				if($regiao == 37){
					
					$qr_regioes = mysql_query("SELECT * FROM regioes WHERE status_reg = '1' AND status = '1' AND id_regiao NOT IN(43,36)");
				} else {
				$qr_regioes = mysql_query("SELECT * FROM regioes WHERE status_reg = '1' AND status = '1' AND id_master = '$id_master'");
					
				}
				
					print "<option value=''>Selecione a região...</option>";
					while($row_regioes = mysql_fetch_assoc($qr_regioes)):
				?>
					<option value="<?php echo $row_regioes['id_regiao']?>"><?php echo $row_regioes['id_regiao'] . " - " . $row_regioes['regiao']; ?></option>
				<?php endwhile;?>
			</select>
		</td>
	  </tr>
	  
	  <?php // Parceiros ?>
	  <tr class="bloco_notas" bordercolor="#FFFFFF" bgcolor="#FFFFFF" style="display : none;" >
	  	<td height="30"  align="right" valign="middle" bgcolor="#EBEBEB" class="linhaspeq"><strong><span class="style16">Parceiros :</span></strong></td>
	  	<td height="30" align="left" valign="middle" bgcolor="#F6F6F6" class="linhaspeq">&nbsp;
			<select name="parceiros_notas" id="parceiros_notas">
				<option value="">Selecione...</option>
			</select>
		</td>
	  </tr>
	  
	  <tr class="bloco_notas" bordercolor="#FFFFFF" bgcolor="#FFFFFF" style="display : none;" >
	  	<td colspan="2" id="notas"></td>
	  </tr>
	  <tr class="bloco_notas" bordercolor="#FFFFFF" bgcolor="#FFFFFF" style="display : none;" >
	  	<td height="30"  align="right" valign="middle" bgcolor="#EBEBEB" class="linhaspeq"><strong><span class="style16">Subtipo :</span></strong></td>
	  	<td height="30" align="left" valign="middle" bgcolor="#F6F6F6" class="linhaspeq">&nbsp;
			<select name="subtipo" id="subtipo">
              			<?php 
              			$subtipos = array(
							'1' => 'Doc',
							'2' => 'Ted',
							'3' => 'Cheque',
							'4' => 'Dinheiro' ,
							'5' => 'Transferência'
						);
						foreach($subtipos as $chave => $valor):
              			?>
              				<option <?php echo $selected; ?> value="<?php echo $chave; ?>"><?php echo $valor; ?></option>
              			<?php endforeach;?>
              		</select>
             <span id="campo_n_subtipo">Nº <input type="text" name="n_subtipo" id="n_subtipo" value="" /></span>
		</td>
	  </tr>
	
      <tr bordercolor="#FFFFFF" bgcolor="#FFFFFF">
        <td height="30" align="right" valign="middle" bgcolor="#EBEBEB" class="linhaspeq"><strong><span class="style16">CUSTO ADICIONAL:</span></strong></td>
        <td height="30" align="left" valign="middle" bgcolor="#F6F6F6" class="linhaspeq">&nbsp;
          <input name="adicional" type="text" size="20" id="adicional" onkeydown="FormataValor(this,event,17,2)"/></td>
      </tr>

      <tr bordercolor="#FFFFFF" bgcolor="#FFFFFF">
        <td height="30" align="right" valign="middle" bgcolor="#EBEBEB" class="linhaspeq"><strong><span class="style16">VALOR:</span></strong></td>
        <td height="30" align="left" valign="middle" bgcolor="#F6F6F6" class="linhaspeq">&nbsp;
          <input name="valor" type="text" size="20" id="valor" onkeydown="FormataValor(this,event,17,2)"/></td>
      </tr>

      <tr bordercolor="#FFFFFF" bgcolor="#FFFFFF">
        <td height="30" align="right" valign="middle" bgcolor="#EBEBEB" class="linhaspeq"><strong><span class="style16">DATA PARA CR&Eacute;DITO:</span></strong></td>
        <td height="30" align="left" valign="middle" bgcolor="#F6F6F6" class="linhaspeq">&nbsp;
          <input name="data_credito" type="text" id="data_credito" size="10" onkeyup="mascara_data(this)" maxlength="10" /></td>
      </tr>

      <tr bordercolor="#FFFFFF" bgcolor="#FFFFFF">
        <td height="37" colspan="2" align="center" valign="top" bgcolor="#FFFFFF">
          <div align="center"> <br />
            <input type="submit" name="Submit" value="GRAVAR ENTRADA" />
            </label>
            <?php

		print "
		<input name='id' type='hidden' id='id' value='1'>
        <input type='hidden' name='regiao' value='$regiao'>";		

		print "<script>function validaForm(){
           d = document.form1;



           if (d.nome.value == \"\"){

                     alert(\"O campo Nome deve ser preenchido!\");
                     d.nome.focus();
                     return false;

          }



           if (d.valor.value == \"\"){
                     alert(\"O campo Valor deve ser preenchido!\");
                     d.valor.focus();
                     return false;

          }		  

           if (d.data_credito.value == \"\"){

                     alert(\"O campo Data deve ser preenchido!\");
                     d.data_credito.focus();
                     return false;

          }
		return true;   }

		</script> ";
		?>
                    </div>
          </td>
      </tr>
    </table>
    </form>
    </td>
  </tr><!--

  <tr>

    <td height="25" colspan="4" align="center" valign="middle" background="imagensfinanceiro/barra3.gif"><strong><span class="menusCima">    CADASTRAMENTO DE  TIPOS DE DE ENTRADAS</span></strong><br /></td>

  </tr>

  <form action="saidas.php" method="post" name="form2" onSubmit="return validaForm2()">

  <tr>

    <td width="149" height="30" align="right" valign="middle" bgcolor="#EBEBEB" class="linhaspeq"><strong><span class="style2">&nbsp;NOME:</span></strong></td>

    <td width="518" height="30" align="left" valign="middle" bgcolor="#F6F6F6">&nbsp;

    <input name="nome" type="text" size="70" id="nome" onChange="this.value=this.value.toUpperCase()"/></td>

  </tr>

  <tr>

    <td height="30" align="right" valign="middle" bgcolor="#EBEBEB" class="linhaspeq">

    <strong><span class="style16">&nbsp;&nbsp;DESCRI&Ccedil;&Atilde;O:</span></strong></td>

    <td height="30" align="left" valign="middle" bgcolor="#F6F6F6">&nbsp;

    <input name="descricao" type="text" size="70" id="descricao" onChange="this.value=this.value.toUpperCase()"/></td>

  </tr>

  <tr>

    <td height="18" colspan="2" align="center" valign="top" bgcolor="#FFFFFF">

    <div align="center">

      <br />

      <input type="submit" name="Submit2" value="GRAVAR TIPO DE ENTRADA" />

      <?php
		print "
		<input name='id' type='hidden' id='id' value='2'>
        <input type='hidden' name='tipo' value='1'> 
		<input type='hidden' name='regiao' value='$regiao'>
		<script>function validaForm2(){
           d = document.form2;
           if (d.nome.value == \"\"){
                     alert(\"O campo Nome deve ser preenchido!\");
                     d.nome.focus();
                     return false;

          }
           if (d.descricao.value == \"\"){
                     alert(\"O campo Descrição deve ser preenchido!\");
                     d.descricao.focus();
                     return false;
          }

		return true;   }
	</script>

		";



?>

</div></td>
  </tr>  -->

  </form>
  <tr valign="top">
    <td height="20" colspan="4" bgcolor="#E2E2E2">&nbsp;</td>
  </tr>
  <tr valign="top">
    <td height="20" colspan="4" align="center"><a href="javascript:window.close()" style="text-decoration:none; color:#000">Fechar</a></td>
  </tr>
  </table>
</body>
</html>

<?php 

}else{
	/*
	echo '<pre>';
	print_r($_REQUEST);
	exit;
	*/
	//----------------------------------------------------------------------||

	//- AQUI COMEÇA A RODAR A SEGUNDA PARTE.. ONDE CADASTRAREMOS A ENTRADA -||

	//----------------------------------------------------------------------||

    
	//CADASTRANDO ENTRADAS
	$id_user     = $_COOKIE['logado'];
	$regiao 	 = $_REQUEST['regiao'];
	$projeto     = $_REQUEST['projeto'];
	$banco       = $_REQUEST['banco'];
	$nome 		 = $_REQUEST['nome'];
	$especifica  = addslashes($_REQUEST['especifica']);
	$tipo 		 = $_REQUEST['tipo'];
	$adicional   = $_REQUEST['adicional'];
	$valor 		 = $_REQUEST['valor'];
	$data_credito = $_REQUEST['data_credito'];
	$data_proc    = date('Y-m-d H:i:s');	
	$subtipo	  = $_REQUEST['subtipo'];
	$n_subtipo 	  = $_REQUEST['n_subtipo'];	
	$valor		  = str_replace(".","", $valor);
	$adicional 	  = str_replace(".","", $adicional);
	
if($_COOKIE['logado'] == 87){
        
        echo $adicional;
        exit;
    }
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

	 return "";

	 }

	}
	$data_credito2 = ConverteData($data_credito);

	mysql_query("INSERT INTO entrada(id_regiao,id_projeto,id_banco,id_user,nome,especifica,tipo,adicional,valor,data_proc,data_vencimento, subtipo , n_subtipo) values 

	('$regiao','$projeto','$banco','$id_user','$nome','$especifica','$tipo','$adicional','$valor','$data_proc','$data_credito2', '$subtipo', '$n_subtipo')") or die 

	(mysql_error());
        
	// 02/05/2011 por maikom referente ao notas no adminitrativo
	
	if(!empty($_REQUEST['radio_nota'])){
		
		$id_nota = $_REQUEST['radio_nota'];
		
		$id_ultima_entrada = mysql_insert_id();
		
		if(!empty($id_nota)){
			
			$id_nota = $_REQUEST['radio_nota'];
			mysql_query("INSERT INTO notas_assoc (id_notas, id_entrada) VALUES ('$id_nota', '$id_ultima_entrada')");
			
		}
	}
	
	$result_banco = mysql_query("SELECT saldo FROM bancos where id_banco = '$banco'");
	$row_banco = mysql_fetch_array($result_banco);
	$valor_antigo   = str_replace(",",".",$row_banco['saldo']);
	$valor_novo     = str_replace(",",".",$valor);
	$adicional_novo = str_replace(",",".",$adicional);
	$valor_agora    = $adicional_novo + $valor_novo;
	$valor_update   = $valor_antigo + $valor_agora;
	$valor_update_f = number_format($valor_update,2,",","");

//mysql_query("UPDATE bancos set saldo = '$valor_update_f' where id_banco = '$banco'")  or die ("O servidor não respondeu conforme deveria, tente novamente mais tarde, Obrigado!<br><br>".mysql_error());
print "
	<script>
	alert(\"Informações cadastradas com sucesso!\");	
	location.href=\"entradas.php?regiao=$regiao\"
	</script>";
}
?>