<?php
    include('../../conn.php');
    include ('../../classes/EduAlunosClass.php');
    include('../../wfunction.php');
    
    $id_aluno = $_GET['id_aluno'];
        
    $deleta_aluno = new EduAlunosClass();
    
    $deleta_aluno->removeAluno($id_aluno, $arrayDados);
?>
