<?php
require("../conn.php");
$sql = "SELECT regiao, id_regiao FROM regioes WHERE status = 1 AND id_master = ".$_GET['mt'].";";
$query = mysql_query($sql);

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
	
	$("#loading").hide();
	$("#regioes").hide();
	$("#funcionario").keyup(function(){
		$("#regioes").hide();
		if($(this).val().length >= 4){
			$("#loading").show();
			$.post('actions/funcionarios.xml.php',
				   {funcionario : $(this).val()},
				   function(xml){
					   $("#loading").hide();
					   $("#recebe_funcionarios").html('');
						$(xml).find('funcionario').each(function(){
							var id_funcionario = $(this).find("id_funcionario").text();	
							var nome = $(this).find("nome").text();
							var regiao = $(this).find("regiao").text();
							$("#recebe_funcionarios").append("<div class='funcionario'>"+id_funcionario +" - "+nome+" - "+regiao+"</div>");
							
						});
						$(".funcionario").click(function(){
							$("#regioes").show();
							$(this).removeClass('funcionario').addClass('funcionarioSelect');
						});
				   }
				   );	
		}
	});
	$(".checkbox").change(function(){
		if(regiao != ""){
			regiao += ", ";
		}
		regiao += $(this).val();
		
	});
	
	$("#adicionar").click(function(){
		
		$("#regioes").hide();
		$("#recebe_funcionarios").html("");
		$(".checkbox").removeAttr("checked");
		$("#recebe").append(regiao);
		regiao = "";
		
	});
});
</script>

</head>

<body>
<form id="form1" name="form1" method="post" action="">
  <table width="600" border="0" align="center" cellpadding="0" cellspacing="0" class="tabela">
    <thead>
       <tr>
        <td colspan="2">Cadastrar Documento</td>
       </tr>
    </thead>
    <tbody>
      <tr>
        <td width="104">Nome:</td>
        <td width="494"><label>
          <input type="text" name="nome" id="nome" />
        </label></td>
      </tr>
      <tr>
        <td>Descri&ccedil;&atilde;o:</td>
        <td><label>
          <textarea name="descricao" rows="3" id="descricao"></textarea>
        </label></td>
      </tr>
      <tr>
        <td>Dia Limite:</td>
        <td><label>
          <input name="dia" type="text" id="dia" size="5"/>
        </label></td>
      </tr>
      <tr>
        <td colspan="2">
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="48%" valign="top"><div id="funcionarios">
              <table width="0" border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                  <td>Buscar </td>
                  <td><label>
                    <input type="text" name="funcionario" id="funcionario" />
                  </label></td>
                </tr>
                <tr>
                  <td colspan="2" id="recebe_funcionarios">
                  
                    </td>
                </tr>
                <tr id="loading">
                  <td colspan="2" >
                  	CARREGANDO...
                  </td>
                </tr>
                </table>
            </div>
            </td>
            <td width="52%">
            <div id="regioes">
              <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td colspan="2" align="center">Regioes</td>
                  </tr>
                <tr>
                  <td colspan="2">
                  <?php while($row = mysql_fetch_assoc($query)){?>
                  <div class="regioes">
                    <input name="checkbox" type="checkbox" class="checkbox" value="<?=$row['id_regiao']?>"/>
                    <?=$row['regiao']?></div>
                  <?php }?>
                    </td>
                </tr>
                </table>
            </div></td>
          </tr>
          <tr>
            <td colspan="2" align="center" valign="top"><a href="#" id="adicionar">Adicionar</a></td>
            </tr>
          <tr id="recebe">
            <td colspan="2" align="center" valign="top" >
            	
            </td>
          </tr>
        </table></td>
      </tr>
    </tbody>
  </table>
</form>
</body>
</html>