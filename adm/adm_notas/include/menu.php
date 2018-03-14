<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<?php $pagina_atual = explode('/', $_SERVER['PHP_SELF']);
      $pagina_atual = $pagina_atual[4];
	  
	  
	    $pagina = $_SERVER['PHP_SELF'];
	  include('../../classes_permissoes/botoes.class.php');
	  $botao = new Botoes();
	  ?>
	   
      
     <img style="position:relative; top:7px; float:right;" src="../../imagens/logomaster<?=$Master?>.gif" width="66" height="46"> 
          <span style="position:relative; margin-top:20px;">  
          		 <?php include('../../reportar_erro.php'); ?> 
          </span>			
          
          
          
          

       <ul id="nota">
       		<li>
            <a href="../index.php<?='?m='.$link_master?>">P&aacute;gina inicial</a>
            </li>
             <?php if($botao->verifica_permissao(87)) {  ?>
       		<li>
            <a href="index.php<?='?m='.$link_master?>">Gerenciamento de notas</a>
            </li>
            <?php } ?>
            
             <?php if($botao->verifica_permissao(88)) {  ?>
                <li>
                <a href="cadastro.php<?='?m='.$link_master?>">Cadastro de notas</a>
                </li>     
              <?php } ?>
      </ul>