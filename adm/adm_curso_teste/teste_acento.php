<?php
 function strtolower2($Texto) {
  $Array1 = array('�','�','�','�','�','�','�','
�','�','�','�','�','�','�','�','�'
,'�','�','�','�');
  $Array2 = array('�','�','�','�','�','�','�','
�','�','�','�','�','�','�','�','�'
,'�','�','�','�');
  for ($X = 0; $X < count($Array2); $X++) {
   $Texto = str_replace($Array1[$X],$Array2[$X],$Texto);
  }
  return strtoupper($Texto);
 } 

echo strtolower2("T�ST�N� �ST� SCR�PT");

?>