<?php

//CARREGA TODOS OS MASTER
$master = GlobalClass::carregaMaster();

//PERMISS�ES DO USUARIO
$permi_user = new Permissoes();
$permissao_master = $permi_user->getPermissaoMaster($_COOKIE["logado"]);

?>
