<?php
$pasta = scandir('../js');

foreach($pasta as $arquivo){


$total_caracteres = strlen($arquivo);

if($arquivo[$total_caracteres - 5] == '.'){

echo substr($arquivo,-4).'<br>';
	
}

if($arquivo[$total_caracteres - 4] == '.'){

echo substr($arquivo,-3).'<br>';
	
}

if($arquivo[$total_caracteres - 3] == '.'){

echo substr($arquivo,-2).'<br>';
	
}


	
	
}



?>