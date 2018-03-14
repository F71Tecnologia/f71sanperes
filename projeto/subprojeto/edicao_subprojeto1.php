<?php 
include('include/restricoes.php');
include('../../conn.php');
include('../../classes/formato_valor.php');
include('../../classes/formato_data.php');
include('../../funcoes.php');
include('../../adm/include/criptografia.php');


$id_subprojeto = $_GET['id'];
$regiao=$_GET['regiao'];

$query = mysql_query("SELECT * FROM subprojeto WHERE id_subprojeto = '$id_subprojeto'") or die("erro");
$row= mysql_fetch_assoc($query);



if(isset($_POST['enviar'])) {
	
				
									header("Location:edicao_subprojeto.php?m=$link_master&id=$id_subprojeto&regiao=$regiao&tp=$_POST[termos]");
									
								
}
									
?>



<html>
<head>
<title>:: Intranet :: Edi&ccedil;&atilde;o de Projeto</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="shortcut icon" href="../favicon.ico">
<link href="../../rh/css/estrutura_cadastro.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="../js/ramon.js"></script>
<script type="text/javascript" src="../js/jquery-1.3.2.js"></script>
<script type="text/javascript" src="../jquery/priceFormat.js"></script>
<script type="text/javascript">



</script>
</head>
<body>

<div id="corpo">
<table align="center" width="100%" cellspacing="0" cellpadding="12" style="font-size:13px; line-height:22px;">
  <tr>
    <td>
      <div style="border-bottom:2px solid #F3F3F3; margin:10px 0 18px 0;">
           <h2 style="float:left; font-size:18px;">EDITAR<span class="projeto"> RENOVAÇÃO</span>
           </h2>
    
           </p>
           <div class="clear"></div>
      </div>

    
      
	<form action="" method="post" name="form1" enctype="multipart/form-data" onSubmit="return validaForm()">

    
     <table cellpadding="0" align="center" cellspacing="1" class="secao">
      <tr>
        <td class="secao_pai" colspan="6">RENOVAÇÕES</td>
      </tr>
      <tr>
        <td height="31" class="secao">Tipo de documento:</td>
        <td colspan="5" rowspan="2">
           <p>
            <label>
               <input type="radio" name="termos" value="APOSTILAMENTO" id="termos_0" <?php if($row['tipo_subprojeto']=='APOSTILAMENTO'){echo 'checked';}?> >
              Apostilamento</label>
             <br>
             <label>
               <input type="radio" name="termos" value="TERMO DE PARCERIA " id="termos_0" <?php if($row['tipo_subprojeto']=='TERMO DE PARCERIA'){echo 'checked';}?> >
               Termo de Parceria </label>
             <br>
             <label>
               <input type="radio" name="termos" value="TERMO ADITIVO" id="termos_1" <?php if($row['tipo_subprojeto']=='TERMO ADITIVO'){echo 'checked';}?> >
               Termo Aditivo </label>
             <br>          
               <input type="radio" name="termos" value="NOVO CONV&Ecirc;NIO" id="termos_1"  <?php if($row['tipo_subprojeto']=='NOVO CONVÊNIO'){echo 'checked';}?> >
               Novo Conv&ecirc;nio</p></td>
      </tr>
      <tr>
        <td height="22" class="secao">&nbsp;</td>
      </tr>
      <tr>
        <td class="secao">&nbsp;</td>
        <td colspan="5" align="right"><input name="enviar" type="submit" id="enviar" value="Atualizar"/></td>
      </tr>
      
      
      <tr>
        
      </tr>
    </table>
	</form></td>
  </tr>
</table>

</div>
</body>
</html>