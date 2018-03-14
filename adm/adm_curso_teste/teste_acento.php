<?php
 function strtolower2($Texto) {
  $Array1 = array('à','á','â','ã','é','è','ê','
ó','ò','ô','õ','ú','ù','û','ü','ä'
,'ë','ï','ö','ç');
  $Array2 = array('À','Á','Â','Ã','É','È','Ê','
Ó','Ò','Ô','Õ','Ú','Ù','Û','Ü','Ä'
,'Ë','Ï','Ö','Ç');
  for ($X = 0; $X < count($Array2); $X++) {
   $Texto = str_replace($Array1[$X],$Array2[$X],$Texto);
  }
  return strtoupper($Texto);
 } 

echo strtolower2("TËSTÀNÔ ÊSTÉ SCRÏPT");

?>