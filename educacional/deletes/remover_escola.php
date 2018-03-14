<?php
    include('../../conn.php');
    include ('../../classes/EduEscolasClass.php');
    include('../../wfunction.php');
    
    $id_escola = $_GET['id_escola'];
        
    $deleta_escola = new EduEscolasClass();
    
    $deleta_escola->removeEscola($id_escola, $arrayDados);
?>

