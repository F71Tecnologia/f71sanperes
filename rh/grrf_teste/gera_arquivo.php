<?php

if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
    exit;
}

include "../../conn.php";
include "../../classes/funcionario.php";
include '../../classes_permissoes/regioes.class.php';
include("../../wfunction.php");



if(isset($_POST['gerar'])){
    
    
 $mes       = $_POST['mes'];   
 $ano       = $_POST['ano'];   
 $regiao    = $_POST['regiao'];   
 $projeto   = $_POST['projeto'];   
 $usuario   = carregaUsuario();   
 
$qr_empresa_master = mysql_query("SELECT  REPLACE(REPLACE(REPLACE(C.cnpj,'-',''),'.',''),'/','') as cnpj,
SUBSTR(C.razao,1,30) as razao, SUBSTR(C.responsavel,1,20) as responsavel,
SUBSTR(C.endereco,1,50) as endereco, 
SUBSTR(C.bairro,1, 20) as bairro,
REPLACE( REPLACE(C.cep, '-',''),'.','') as cep, C.cidade, C.uf, 
REPLACE(REPLACE(REPLACE(C.tel, '(', ''),')', ''),'-','') as telefone , C.email
FROM master as A
INNER JOIN regioes as B
ON A.id_master = B.id_master
INNER JOIN rhempresa as C
ON C.id_regiao = B.id_regiao


 WHERE  A.id_master = '$usuario[id_master]' AND B.sigla = 'AD'");
 
 
    
    
    
}
?>
