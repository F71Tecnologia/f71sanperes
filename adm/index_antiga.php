<?php
include "include/restricoes.php";
include('../conn.php');
include "../funcoes.php";
include "include/criptografia.php";
include "../empresa.php";
include "../classes/funcionario.php";


function __autoload($class_name) {
    require_once '../classes_permissoes/'.strtolower($class_name).'.class.php';
}

echo getcwd();
$a = new Botoes();

?>

<html>
<head>
<title>Administra&ccedil;&atilde;o Geral</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="css/estrutura.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/JavaScript">
function MM_jumpMenu(targ,selObj,restore){
  eval(targ+".location=	'"+selObj.options[selObj.selectedIndex].value+"'");
  if (restore) selObj.selectedIndex=0;
}
</script>


<style>
 .aviso{
	color:#F94448;
	font-size:14px;
	
	}


</style>
</head>
<body>

   <div id="corpo">
        <div id="menu" class="geral">
             <ul style="text-align:center; width:100%; margin-left:0px;">
			 <?php if(isset($_GET['m'])) {
			  $IMG = new empresa();
			  $IMG -> imagemCNPJ2($Master);
		     } ?>
             </ul>
             
              <div style="float:right">
		<?php include('../reportar_erro.php');?>
        </div>
        <div style="clear:right;"></div>
        
        
        </div>
        <div id="conteudo">  
        
        
        
         
       

          <?php if(empty($_GET['m'])) { ?>
               <h1><span>Administração Geral</span></h1>
         <p style="margin-bottom:40px;">&nbsp;</p>Selecione um Master: 
         
<select name='master' class='campotexto' id='master' onChange="MM_jumpMenu('parent',this,0)">
<option value=''>- Selecione -</option>
<?php
$qr_master = mysql_query("SELECT * FROM master WHERE status = '1'");
while($master = mysql_fetch_array($qr_master)) {
$link_master = encrypt("$master[0]&12");
$link_master = str_replace("+","--",$link_master);
print "<option value='index.php?m=$link_master'>$master[nome]</option>";
}
?>
</select>
 
        <p style="margin-bottom:40px;">&nbsp;</p>
        
		<?php } else { ?>
       <p style="margin-bottom:20px;">&nbsp;</p>
        Alterar Master: 

<select name='master' class='campotexto' id='master' onChange="MM_jumpMenu('parent',this,0)">
<option value=''>- Selecione -</option>
<?php
$qr_master = mysql_query("SELECT * FROM master WHERE status = '1'");
while ($master = mysql_fetch_array($qr_master)){
$link_master2 = encrypt("$master[0]&12");
$link_master2 = str_replace("+","--",$link_master2);
if ($Master == $master['0']){
	print "<option value=$master[0] selected>$master[nome]</option>";
} else {
	print "<option value='index.php?m=$link_master2'>$master[nome]</option>";
}
}
?>
</select>
<p style="margin-bottom:20px;">&nbsp;</p>
<?php include "include/menu.php"; ?>  
<p style="margin-bottom:20px;">&nbsp;</p>

 <?php
		}?>   
    
    
    
      
      </div>
        <div id="rodape"><?php include "include/rodape.php"; ?></div>
   </div>
</body>
</html>