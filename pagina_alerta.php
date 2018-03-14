<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-5589-1" />
<title>Untitled Document</title>
</head>

<body>
<table align="center" style="background-color:#EEE;margin-top:150px; font-family:Verdana, Geneva, sans-serif;padding:5px; border: 2px solid #D7D7D7;" width="500" height="150">
		<tr>
        	<td>&nbsp;</td>
        </tr>
		
		<tr>
	    	<td align="center"><img src="../img_menu_principal/alerta.png"/></td>
	    </tr>
	  <tr>
	    	<td align="center">
	      <?php echo $menssagem;?>
	        </td>
	  </tr>
	  <tr><td>&nbsp;</td> </tr>
	  <?php if(isset($link_voltar)){ ?>
      
       <tr>
	  	<td align="center">	 
       
         		
	           <a href="<?php echo $link_voltar; ?>">&laquo; Voltar</a>	          
	    </td>
	  </tr>
      <?php }?>
	  
	</table>

</body>
</html>
