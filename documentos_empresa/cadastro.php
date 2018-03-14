<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
}else{

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
<script type="text/javascript" src="../js/jquery-1.3.2.js"></script>
<script type="text/javascript">
$().ready(function(){
	var regiao = "";
	var id_regiao = "";
	var funcionario = "";
	var id_funcionario = "";
	
	var ArrayID_regioes = new Array;
	var ArrayRegiao = new Array;
	
	$("#regioes").hide();
	$("#adicionar").hide();
	<?php if(!isset($_GET['IDdoc'])){?>
	$("#recebe").hide();
	var indice_responsaveis = "";
	<?php }else{?>
		var indice_responsaveis = 1;
	<?php }?>
	$(".checkbox").click(function(){
	   if($(this).is(':checked')){
		  
		  if(regiao == ""){
		   		regiao = $(this).attr('alt');
		   }else{
		  	 	regiao = regiao + ',' + $(this).attr('alt');
		   }
		   if(id_regiao == ""){
		   		id_regiao = $(this).val();
		   }else{
		   		id_regiao = id_regiao + ',' + $(this).val();
		   }
			//id_regiao.unshift($(this).val());
			//regiao.unshift($(this).attr('alt'));
	   }else{
		   regiao = regiao.split(",");
		   for(i=0;i < regiao.length+1; i++){
			   if(regiao[i] == $(this).attr('alt')){
				   regiao.splice(i, 1);
			   }
		   }
		   regiao = regiao.join(',');
		   
		   id_regiao = id_regiao.split(",");
		   for(i=0;i < id_regiao.length+1; i++){
			   if(id_regiao[i] == $(this).val()){
				   id_regiao.splice(i, 1);
			   }
		   }
		   id_regiao = id_regiao.join(',');
			
	   }
		
	});
	$("#funcionario").change(function(){
		funcionario = $(this).find('option:selected').text();
		id_funcionario = $(this).val();
		$("#regioes").show();
		$("#adicionar").show();
	});
	
	$("#adicionar").click(function(){
			indice_responsaveis = 1;
			$("#recebe").show();
			ArrayID_regioes = id_regiao.split(",");
			$("#recebe_funcionarios").html("");
			$(".checkbox").removeAttr("checked");
			$("#recebe").append("<div class='adcionados'><input type=\"hidden\" name=\"funcionario[]\" value='"+id_funcionario+"'/><input type=\"hidden\" name=\"regiao[]\" value='"+ArrayID_regioes.join(",")+"'/>"+funcionario+" - "+regiao+"</div>");
			$("#funcionario").find('option').eq(0).attr('selected','selected');
			$("#regioes").hide();
			$("#adicionar").hide();
			regiao = new Array;
			id_regiao = new Array;
			
			id_funcionario = "";
			funcionario = "";
			$("div.adcionados").click(function(){
				if(window.confirm('Tem certeza que deseja remover este Responsavel pelo envio')){
					$(this).remove();						   
				}
			});
			$('#button').removeAttr("disabled");

		
		
	});
	
	$("div.adcionados").click(function(){
				if(window.confirm('Tem certeza que deseja remover este Responsavel pelo envio')){
					$(this).remove();						   
				}
	});
	

	$('div.conteudo').hide();	
	$('div.titulo').click(function(){
		$('div.conteudo').hide('fast');				  
		$(this).next().slideToggle("fast");
	});

	
	
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
		}else if(indice_responsaveis == ""){
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
	
	$("#frequencia_documento").change(function(){
		if($(this).val() == '1'){
			$("#mes_referencia_documento option").eq(0).attr("selected", "selected");
		}
	});
	
});

function confirmaDeleta(url){
	if(window.confirm('Tem certeza que deseja deletar este documento?')){
		location.href=url;
	}
}
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
        	<td colspan="2" align="right"><?php include('../reportar_erro.php'); ?></td>
       </tr>
       <tr>
       		 <td colspan="2"><h1>Cadastrar Documento</h1></td>
      </tr>
     
    </thead>
    <tbody>
      <tr>
        <td width="104">Nome:</td>
        <td width="494"><label>
          <input type="text" name="nome" id="nome" value="<?=$update_row['nome_documento']?>" />
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
          <input name="id_master" type="hidden" id="id_master3" value="1" />
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
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="48%" valign="top"><div id="funcionarios">
              <table width="0" border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                  <td>Funcionario</td>
                  <td><select name="funcionario" id="funcionario">
                    <option value="">Selecione um funcionario</option>
                    <?php 
					while($row_funcionario = mysql_fetch_assoc($query_funcionario)){
					?>
                    <option value="<?=$row_funcionario['id_funcionario']?>"><?=$row_funcionario['id_funcionario']?> - <?=$row_funcionario['nome']?></option>
                    <?php }?>
                  </select></td>
                </tr>
                <tr>
                  <td colspan="2" id="recebe_funcionarios" >
                    
                    </td>
                </tr>
                </table>
            </div>
            </td>
            <td width="52%">&nbsp;</td>
          </tr>
          <tr>
            <td colspan="2" align="center" valign="top"><div id="regioes">
              <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td colspan="2" align="center">Regioes</td>
                </tr>
                <tr>
                  <td colspan="2"><?php while($row_regiao = mysql_fetch_assoc($query_regiao)){?>
                    <div class="regioes">
                      <input name="checkbox" type="checkbox" class="checkbox" value="<?=$row_regiao['id_regiao']?>" alt="<?=$row_regiao['regiao']?>"/>
                      <span class="nome_regiao">
                        <?=$row_regiao['regiao']?>
                      </span></div>
                    <?php }?></td>
                </tr>
              </table>
            </div></td>
          </tr>
          <tr>
            <td colspan="2" align="center" valign="top"><a href="#" id="adicionar">Adicionar</a></td>
            </tr>
          <tr >
            <td colspan="2" align="center" valign="top" id="recebe">
            
            <span style="color:#F00; font-size: 10px;">
            * clique no funcionario para tira-lo da lista!
            </span>
            <?php 
			 
			 if(isset($_GET['IDdoc'])){
				$qr_responsavel = mysql_query("SELECT * FROM doc_responsaveis WHERE id_documento = '$_GET[IDdoc]'");
				while($row_responsavel = mysql_fetch_assoc($qr_responsavel)){
					$qr_funcionario = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$row_responsavel[id_funcionario]'");
					$row_funcionario = mysql_fetch_assoc($qr_funcionario);
					$id_regiao = explode(",",$row_responsavel['ids_regioes']);
					echo '<div class=\'adcionados\'>';
					echo "<input type=\"hidden\" class='funcionario_id' name=\"funcionario[]\" value='$row_funcionario[id_funcionario]'/>";
					echo "<input type=\"hidden\" name=\"regiao[]\" value='$row_responsavel[ids_regioes]'/>";
					echo "<span style=\"color:#F00;\">";
					echo $row_funcionario['nome'];
					echo "</span>";
					echo " - ";
					foreach($id_regiao as $id){
						$qr_regioes  = mysql_query("SELECT * FROM regioes WHERE id_regiao = '$id'");
						$row_regioes_nome = mysql_fetch_assoc($qr_regioes);
						$ids[] = $row_regioes_nome['regiao'];
						
					}
					echo implode(",",$ids);
					echo '</div>';
				}
			 }
			?>
            </td>
          </tr>
          <tr >
            <td colspan="2" align="center">
              <label>
                <input type="button" name="button" id="cadastrar" value="Cadastrar Documento" />
              </label>
              </td>
          </tr>
        </table></td>
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
<p>&nbsp;</p>
</body>
</html>
<?php }// cookie logado?>