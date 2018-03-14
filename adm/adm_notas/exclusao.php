<?php
include('../include/restricoes.php');
include('../../conn.php');
include('../../funcoes.php');
include('../include/criptografia.php');




$nota = $_GET['id'];

$qr_notas = mysql_query("SELECT * FROM notas WHERE id_notas= '$nota'");
$row_notas= mysql_fetch_assoc($qr_notas);
$id_usuario=$_COOKIE['logado'];


if($_POST['escolha'] == 'sim') { 
	

	
		$inserir=mysql_query("UPDATE notas SET  ultima_edicao=NOW(), editado_por='$id_usuario', status='0' WHERE id_notas = '$_POST[id]' ");
	
	
	header("Location:index.php?m=$link_master");
	
} elseif ($_POST['escolha'] == 'nao') {
	header("Location:index.php".'?m='.$link_master);
}
							
?>
<html>
<head>
<title>Administra&ccedil;&atilde;o de Notas</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../css/estrutura.css" rel="stylesheet" type="text/css">
<script src="../../js/ramon.js" type="text/javascript"></script>

<script type="text/javascript" src="../../jquery/validationEngine/jquery.validationEngine-pt.js" ></script>
<script language="javascript" type="text/javascript">



function validaForm() {
	d = document.excluir;
	if (d.nome.value == '') {
		alert('O campo Nome deve ser preenchido!');
		d.nome.focus();
		return false;
	}
	return true;
}


</script>
</head>
<body>
<div id="corpo">
    <div id="menu" class="nota">
    	<?php include('include/menu.php'); ?>
    </div>
    <div id="conteudo">
  <h1><span>Exclus&atilde;o de Notas fiscais</span></h1>
        <form name="excluir" method="post" id="excluir" action="<?php echo $_SERVER['PHP_SELF']; ?>?m=<?=$link_master?>" onSubmit="return validaForm()">  
                
         Excluir nota Nº:<strong><?=$row_notas['numero']; ?></strong> 
            
           
             
            <p>
               <label>
                 <input type="radio" name="escolha" value="sim" id="escolha_0" class="validate[require]">
                 Sim</label>
               <br>
               <label>
                 <input type="radio" name="escolha" value="nao" id="escolha_1" class="validate[require]">
                 N&atilde;o</label>
               <br>
          </p>
          <input name="id" type="hidden" value="<?php echo $nota;?>"/>
          <input type="submit" value="OK" name="excluir"  id-="excluir" >
      </form>
    </div>
    <div id="rodape"><?php include('../include/rodape.php'); ?></div>
</div>
</body>
</html>