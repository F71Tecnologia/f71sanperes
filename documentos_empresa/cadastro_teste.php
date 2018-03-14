<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
exit;
}

require("../conn.php");
$sql_regiao = "SELECT regiao, id_regiao FROM regioes WHERE status = 1 AND id_master = ".$_GET['mt'].";";
$query_regiao = mysql_query($sql_regiao);

$sql_funcionario = "SELECT * 
FROM  `funcionario` 
WHERE status_reg = '1' 
ORDER BY  `nome` ASC ";
$query_funcionario = mysql_query($sql_funcionario);

$sql_categoria = "SELECT * 
FROM  `categorias` 
ORDER BY  `nome_categoria` ASC ";
$query_categoria = mysql_query($sql_categoria);


if(isset($_GET['IDdoc'])){
	$update_query = mysql_query("SELECT * FROM documentos WHERE id_documento = ".$_GET['IDdoc']);
	$update_row = mysql_fetch_assoc($update_query);
}

?>	
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Cadastro de documentos</title>
<link rel="stylesheet" type="text/css" href="css/estilo.css"/>
<script type="text/javascript" src="../jquery/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="../jquery/tabs/jquery.idTabs.min.js"></script>
<script type="text/javascript">
$(function() {
	
	// Abas
  	$("ul.tab li a").click(function(){
		var idAba = $(this).attr('href');
		$('.base').hide();
		$(idAba).show();
	});
	// Botão avançar
	$('a.avanca').click(function(){
		var idAba = $(this).attr('href');	
		$('.base').hide();
		$(idAba).show();
	});
	// seleção de funcionario
	$('#funcionario').change(function(){
		var texto = $(this).find('option:selected').text();
		var valor = $(this).val();
		$('td#Funcionario').html("<p>"+texto+"<input type=\"hidden\" class=\"id_user\" value=\""+valor+"\" alt=\""+texto+"\" /></p>");
	});
	
	$("a[href*='#aba3']").click(function(){
		var id_regiao = new Array;
		var regiao = new Array;
		var mensagem = new Array;
		$(".checkbox").each(function(i){
			if($(this).is(':checked')){
				var valor = $(this).val();
				var texto = $('.textCheck').eq(i).text();
				id_regiao.push(valor);
				regiao.push(texto);
			}
		});
		for(i=0;i<id_regiao.length;i++){
			mensagem.push("<p>"+id_regiao[i]+" "+regiao[i]+"<input type=\"hidden\" class=\"id_regiao\" value=\""+id_regiao[i]+"\" alt=\""+regiao[i]+"\" /></p>");
		}
		$("td#regiao").html(mensagem.join(''));
				
	});
	
	$("a.finalizar").click(function(){
		var mensagem 	= new Array;
		var id_regiao 	=  new Array;
		var id_user 	= $('.id_user').val();
		var text_user 	= $('.id_user').attr('alt');
		apresentacao 	= 	"<div class=\"blocos\">";
		apresentacao 	+=	"<img src=\"../uploadfy/cancel.png\" width=\"16\" height=\"16\" class=\"fechar\" />";
		apresentacao 	+=	"<img src=\"../imagensmenu2/Edit.png\" width=\"16\" height=\"16\" class=\"edit\" />";
		apresentacao 	+= 	"<span>";
		apresentacao 	+= text_user;
		apresentacao 	+=	"<input type=\"hidden\" name=\"funcionario[]\" value=\""+id_user+"\" class=\"funSelecionado\"  />";
		apresentacao 	+=  "<img src=\"../rh/folha/sintetica/seta_um.gif\" width=\"9\" height=\"9\" style=\"cursor:pointer\" />";
		apresentacao 	+=	"</span><br /><div class=\"regioesSelecionadas\">";
		
		var ids_regioes = new Array;
		
		$('.id_regiao').each(function(i){
			ids_regioes.push($(this).val());
			apresentacao += "<p>"+$(this).val()+" "+$(this).attr('alt')+"</p>";
		});
		apresentacao += "<input type=\"hidden\" name=\"regiao[]\" value=\""+ids_regioes.join(',')+"\" />";
		apresentacao += "</div></div>";
		$("div#MatrizCompleta").append(apresentacao);
		
		Limpa();
		
		funcoes();
				
	});
	
	funcoes();
	
	
	$('div.conteudo').hide();	
	$('div.titulo').click(function(){
		$('div.conteudo').hide('fast');				  
		$(this).next().slideToggle("fast");
	});
	
	
	function funcoes(){
		$('.fechar').click(function(){
			$(this).parent().remove();	
		});
		
		// editando
		$('.edit').click(function(){
			// pegando o funcionario para editar!
			var funcionario = $(this).next('span').find("input[name*='funcionario[]']").val();
			$("#funcionario").find("option[value*='"+funcionario+"']").attr('selected','selected');
			var texto = $('#funcionario').find('option:selected').text();
			$('td#Funcionario').html("<p>"+texto+"<input type=\"hidden\" class=\"id_user\" value=\""+funcionario+"\" alt=\""+texto+"\" /></p>");
			
			// pegando as regioes para editar!
			$("input[name*='checkbox']").attr('checked','');
			
			var ids_regioes = $(this).next().next().next().find("input[name*='regiao[]']").val();
			ids_regioes = ids_regioes.split(',');
			alert(ids_regioes.length);
			for(i=0;i<ids_regioes.length;i++){
				$("input.checkbox[value*='"+ids_regioes[i]+"']").attr('checked','checked');
				alert($("input[value*='"+ids_regioes[i]+"'][type*='checkbox']").val());
			}
			ids_regioes = null;
			$("input[name*='checkbox']").attr('checked','');
			$(this).parent().remove();	
			
		});
	}
	
	function Limpa(){
		$("#Funcionario").text('');
		$("#regiao").text('');
		$("input[name*='checkbox']").attr('checked','');
		$('#funcionario').find('option').eq(0).attr('selected','selected');
		$('.base').hide();
		$('#aba1').show();
	}
	
	function verifica(){
		var indice = "";
		if($("#nome").val() == ""){
			alert("Preenha o campo nome!");
			indice = 1;
		}else if($("#categoria").val() == ""){
			alert("Selecione uma categoria!");
			indice = 1;
		}else if($("#dia").val() == ""){
			alert("Digite um dia!");
			indice = 1;
		}else if($("#frequencia_documento").val() == ""){
			alert("Selecione uma Frequencia!");
			indice = 1;
		}else if($("input[name*='funcionario[]']").val() == ""){
			alert("Adicione um responsavel!");
			indice = 1;
		}
		if(indice == ""){
			$("#form1").submit();
		}
	}
	$("#cadastrar").click(function(){
		 verifica();
	});
	function confirmaDeleta(url){
	if(window.confirm('Tem certeza que deseja deletar este documento?')){
		location.href=url;
	}
}

	
});
</script>

<style type="text/css">
<!--
body {
	background-color: #F9F9F9;
}
-->
</style></head>

<body>
<form id="form1" name="form1" method="post" action="<?php if(!isset($_GET['IDdoc'])){ ?>actions/insert.documentos.php<?php }else {?>actions/update.documentos.php?id_documento=<?= $_GET['IDdoc']?><?php }?>">
  <table width="600" border="0" align="center" cellpadding="0" cellspacing="0" class="tabela">
    <thead>
       <tr>
        <td colspan="2"><h1>Cadastrar Documento</h1></td>
       </tr>
    </thead>
    <tbody>
      <tr>
        <td width="104">Nome:</td>
        <td width="494"><label>
          <input type="text" name="nome" id="nome"  value="<?=$update_row['nome_documento']?>"/>
        </label></td>
      </tr>
      <tr>
        <td>Descri&ccedil;&atilde;o:</td>
        <td><label>
          <textarea name="descricao" rows="3" id="descricao"><?=$update_row['descricao_documento']?></textarea>
        </label></td>
      </tr>
      <tr>
        <td>Categoria:</td>
        <td><label>
          <select name="categoria" id="categoria">
          <option value="">Selecione uma categoria</option>
          <?php while($row_categoria = mysql_fetch_assoc($query_categoria)){?>
          <option value="<?=$row_categoria['id_categoria']?>" <?php if(isset($_GET['IDdoc'])){
			  															if($update_row['id_categoria'] == $row_categoria['id_categoria']){ 
																		echo "selected=\"selected\"";
																		}
		  															}
																		?>
                                                                        ><?=$row_categoria['nome_categoria']?></option>
          <?php }?>
          </select>
        </label></td>
      </tr>
      <tr>
        <td>Dia Limite:</td>
        <td><label>
          <input name="dia" type="text" id="dia" size="5" value="<?=$update_row['dia_documento']?>"/>
          <input name="id_master" type="hidden" id="id_master3" value="<?=$_GET['mt']?>"/>
          <input name="funcionarioCriador" type="hidden" id="funcionarioCriador3" value="<?= $_COOKIE['logado'] ?>" />
        </label></td>
      </tr>
      <tr>
        <td>Frequ&ecirc;ncia:</td>
        <td><table width="0" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td><select name="frequencia_documento" id="frequencia_documento">
              <option value="<?=$update_row['frequencia_documento']?>" selected="selected">Selecione uma frequencia</option>
                <option value="1" >Mensal</option>
                <option value="2">Trimestral</option>
                <option value="3">Semestral</option>
                <option value="4">Anual</option>
              </select></td>
              <td> M&ecirc;s de referencia</td>
              <td><label>
              
                <select name="mes_referencia_documento" id="mes_referencia_documento">
                <?php 
				$query_mes = mysql_query("SELECT * FROM ano_meses ORDER BY num_mes");
				while($row_mes = mysql_fetch_assoc($query_mes)){
					$selected = "";
					if(isset($_GET['IDdoc'])){
						if($update_row['mes_referencia_documento'] == $row_mes['num_mes']){
								$selected = " selected='selected' ";
						}
						if(!empty($selected)){
							echo "<option value='".$row_mes['num_mes']."' $selected >".$row_mes['nome_mes']."</option>";
						}else{
							echo "<option value='".$row_mes['num_mes']."'>".$row_mes['nome_mes']."</option>";
						}
					}else{
						if(date('m') == $row_mes['num_mes']){
							$selected = " selected='selected' ";
						}
						if(!empty($selected)){
							echo "<option value='".$row_mes['num_mes']."' $selected >".$row_mes['nome_mes']."</option>";
						}else{
							echo "<option value='".$row_mes['num_mes']."'>".$row_mes['nome_mes']."</option>";
						}
					}
				}
				
				?>
                </select>
              </label></td>
            </tr>
        </table></td>
      </tr>
      <tr>
        <td colspan="2">        
<div id="usual1" class="usual"> 
  <ul class="tab"> 
    <li><a href="#aba1" class="selected">Parte 1</a></li> 
    <li><a href="#aba2">Parte 2</a></li> 
    <li><a href="#aba3">Parte 3</a></li> 
  </ul> 
  <div id="aba1" style="display: block;" class="base">
  
    <p>Funcionario:
      <select name="funcionario" id="funcionario">
      		<option value="" selected="selected">
          		Selecione um funcionario
          	</option>
        <?php while($row_funcionario = mysql_fetch_assoc($query_funcionario)){?>
        	<option value="<?=$row_funcionario['id_funcionario']?>">
          		<?=$row_funcionario['id_funcionario']." - ".$row_funcionario['nome']?>
          	</option>
        <?php } ?>
      </select>
      </p>
    <p> <a href="#aba2" class="avanca">Avançar >></a></p>
  </div> 
  <div id="aba2" class="base" style="display: none;">
  		<div id="regioes">
              <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td colspan="2" align="center">Regioes</td>
                </tr>
                <tr>
                  <td colspan="2">
				  <?php while($row_regiao = mysql_fetch_assoc($query_regiao)){?>
                    	<div class="regioes">   	    			
                      	<input name="checkbox" type="checkbox" class="checkbox" value="<?=$row_regiao['id_regiao']?>" />
                      <?=$row_regiao['regiao']?>
                      	</div>
                    <?php }?>
                  </td>
                </tr>
              </table>
              <a href="#aba3" class="avanca">Avançar >></a>
         </div>
  </div> 
  <div id="aba3" class="base" style="display: none;">
  	<table width="100%" >
    	<tr>
        	<td width="20%">Funcionario:</td>
            <td id="Funcionario"></td>
            </tr>
    	<tr>
    	  <td colspan="2" align="center">
    	    Regi&otilde;es</td>
    	  </tr>
    	<tr>
    	  <td colspan="2" id="regiao">&nbsp;</td>
  	  </tr>
    </table>
  	<br />
    <a href="#" class="finalizar">Finalizar >></a>
  </div> 
</div> 
<div id="MatrizCompleta">

<?php 
	if(isset($_GET['IDdoc'])){
				$qr_responsavel = mysql_query("SELECT * FROM doc_responsaveis WHERE id_documento = '$_GET[IDdoc]'");
				while($row_responsavel = mysql_fetch_assoc($qr_responsavel)){
					$qr_funcionario = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$row_responsavel[id_funcionario]'");
					$row_funcionario = mysql_fetch_assoc($qr_funcionario);
					$id_regiao = explode(",",$row_responsavel['ids_regioes']);		
					
					print "<div class=\"blocos\">

							<img src=\"../uploadfy/cancel.png\" width=\"16\" height=\"16\" class=\"fechar\" />
							<img src=\"../imagensmenu2/Edit.png\" width=\"16\" height=\"16\" class=\"edit\" />
								<span>
									$row_funcionario[id_funcionario] - $row_funcionario[nome]
									<input type=\"hidden\" name=\"funcionario[]\" value=\"75\" class=\"funSelecionado\"  />
									<img src=\"../rh/folha/sintetica/seta_um.gif\" width=\"9\" height=\"9\" style=\"cursor:pointer\" />
								</span>
								<br />
								<div class=\"regioesSelecionadas\">";
								foreach($id_regiao as $id){
									$qr_regioes  = mysql_query("SELECT regiao FROM regioes WHERE id_regiao = '$id'");
									$row_regioes_nome = mysql_fetch_assoc($qr_regioes);
									print "<p>$id - $row_regioes_nome[regiao]</p>";
									
								}
								
					print "<input type=\"hidden\" name=\"regiao[]\" value='$row_responsavel[ids_regioes]'/>
								</div>
							</div>";
				}
	}

?>

	
</div>
        </td>
      </tr>
       <tr >
            <td colspan="2" align="center">
              <label>
                <input type="button" name="button" id="cadastrar" value="Cadastrar Documento" />
              </label>
              </td>
          </tr>
    </tbody>
  </table>
</form>


<?php 
$query_documentos = mysql_query("SELECT * FROM documentos WHERE status_documento = '1' ORDER BY id_documento DESC");
$alternateColor = 0;
while($row_documentos = mysql_fetch_assoc($query_documentos)){
?>

<div class="titulo" style="cursor:pointer">
	  <span style="size:15px; font-family:'Trebuchet MS', Arial, Helvetica, sans-serif; color:#003C9F;">
    &gt; 
   <?=$row_documentos['id_documento']?>
    - 
    <?=$row_documentos['nome_documento']?>
     - 
     <?php 
		$query_tipo = mysql_query("SELECT * FROM categorias WHERE id_categoria = ".$row_documentos['id_categoria']);
		$row_tipo = mysql_fetch_assoc($query_tipo);
		echo $row_tipo['nome_categoria'];
	?>
    -
    <?php 
	switch($row_documentos['frequencia_documento']){
		case 1: 
				echo "Mensal";
				break;
		case 2: 
				echo "Trimestral";
				break;
		case 3:
				echo "Semestral";
				break;
		case 4:
				echo "Anual";
				break;
	}
	?>
     - 
    <?php
	$query_mes_referencia = mysql_query("SELECT nome_mes FROM ano_meses WHERE num_mes = '$row_documentos[mes_referencia_documento]'");
	echo mysql_result($query_mes_referencia,0); ?>
     </span>
     <a href="?mt=<?=$_GET['mt']?>&IDdoc=<?=$row_documentos['id_documento']?>">editar</a> 
     - <a onclick="confirmaDeleta('actions/delete.documentos.php?id_documento=<?=$row_documentos['id_documento']?>&id_master=<?=$_GET['mt']?>')" href="#">deletar</a>
</div>
<div class="conteudo">
<table width="100%">
<tr>
<td>Responsaveis pelo envio</td>
<td>Regioes</td>
<td>Dia</td>
</tr>

	  <?php 
		$query_responsaveis = mysql_query("SELECT nome,res.id_funcionario FROM documentos as doc, doc_responsaveis as res, funcionario as fun WHERE doc.id_documento = res.id_documento AND res.id_funcionario = fun.id_funcionario AND doc.id_documento = ".$row_documentos['id_documento']);
		while($row_responsaveis = mysql_fetch_assoc($query_responsaveis)){
			$id_responsaveis[] = $row_responsaveis['id_funcionario'];
		?>
  <tr style="font-size:12px;" bgcolor="<? if($alternateColor++%2==0) { ?>#EEEEEE<? } else { ?>#FFFFFF<? } ?>">
    <td align="left"><?=$row_responsaveis['nome']?></td>
    <td align="left">
    <div id="regi">
	<?php 
			$query_doc_responsaveis = mysql_query("SELECT ids_regioes FROM doc_responsaveis WHERE id_documento = '$row_documentos[id_documento]' AND id_funcionario = '$row_responsaveis[id_funcionario]'");
				while($row_doc_responsaveis = mysql_fetch_assoc($query_doc_responsaveis)){
					$regioes_ids = explode(",",$row_doc_responsaveis['ids_regioes']);
					foreach($regioes_ids as $reg){
						$query_regioes = mysql_query("SELECT * FROM regioes WHERE id_regiao = ".$reg);
						$row_regioes = mysql_fetch_assoc($query_regioes);
						echo $row_regioes['regiao']."<br />";
					} 
				}
		?>
        </div>
   </td>

  
  <td align="left">
  <?= $row_documentos['dia_documento'] ?>
  </td>
  <?php }
	?> 
  </table>
</div>

<?php 
}
?>
</body>
</html>
