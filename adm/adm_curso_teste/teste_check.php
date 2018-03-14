<form action="" method="post">
   <input type="checkbox" name="itens[]" value="1" /> Item 1<br/>
   <input type="checkbox" name="itens[]" value="2" /> Item 2<br/>
   <input type="checkbox" name="itens[]" value="3" /> Item 3<br/>
   <input type="submit" value="Enviar" /> 
</form>

<?php
$itens= $_REQUEST['itens'];

if (!empty($itens)) {                
      $qtd = count($itens);
       for ($i = 0; $i < $qtd; $i++) {
            echo $itens[$i]."<br />";//imprime o item corrente
       }
 }

?>