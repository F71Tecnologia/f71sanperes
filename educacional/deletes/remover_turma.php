<?php
    include('../../conn.php');
    include ('../../classes/EduTurmasClass.php');
    include('../../wfunction.php');
    
    $id_turma = $_GET['id_turma'];
        
    $deleta_turma = new EduTurmasClass();
    
    $deleta_turma->removeTurma($id_turma, $arrayDados);
?>

