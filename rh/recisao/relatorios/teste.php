<?php

 // verifica se não está vazio os valores do checkBox do POST
if(!empty($_POST['check_list'])) {

    //varre o array enviado pelo POST

    foreach($_POST['check_list'] as $check) {
            echo $check . "<br>"; //exibe pra nos o valor recebido atualmente e pula linha
            
    }
}
?>