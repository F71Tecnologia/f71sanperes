<?php
include('../conn.php');
include('../wfunction.php');

$funcionario = $_REQUEST['funcionario'];

//SELECIONA TODOS OS FUNCIONARIOS ATIVOS
$arrayFunc = montaQuery("funcionario", "id_funcionario, nome","status_reg = 1", "nome ASC");
//MONTA ARRAY
$arrayFuncionario = array(" " => "« Selecione »");
foreach ($arrayFunc as $key => $value) {
    $arrayFuncionario[$value['id_funcionario']] = $value['id_funcionario'] . " - " . $value['nome'];  
}

if (isset($_REQUEST['copiar'])) {
    $funcionarioDe = $_REQUEST['funcionarioDe'];
    $funcionarioPara = $_REQUEST['funcionarioPara'];
    
    //CONSULTA TODAS AS PERMISSOES DO FUNCIONARIO DE:
    $func_reg_assocDe = montaQuery("funcionario_regiao_assoc", "id_regiao, id_master", "id_funcionario = $funcionarioDe");
    $btn_assocDe = montaQuery("botoes_assoc", "botoes_id", "id_funcionario = $funcionarioDe");
    $acoes_assocDe = montaQuery("funcionario_acoes_assoc", "acoes_id", "id_funcionario = $funcionarioDe", null, null, "array", null, "acoes_id");
    
    //CONSULTA TODAS AS PERMISSOES DO FUNCIONARIO PARA:
    $func_reg_assocPara = montaQuery("funcionario_regiao_assoc", "id_regiao, id_master", "id_funcionario = $funcionarioPara");
    $btn_assocPara = montaQuery("botoes_assoc", "botoes_id", "id_funcionario = $funcionarioPara");
    $acoes_assocPara = montaQuery("funcionario_acoes_assoc", "acoes_id", "id_funcionario = $funcionarioPara", null, null, "array", null, "acoes_id");
    
    //REMOVE TODAS AS PERMISSOES DO FUNCIONARIO PARA:
    if(!empty($func_reg_assocPara)){
        echo "DELETE FROM funcionario_regiao_assoc WHERE id_funcionario = $funcionarioPara;"; 
    }

    if(!empty($btn_assocPara)){
        echo "DELETE FROM botoes_assoc WHERE id_funcionario = $funcionarioPara;"; 
    }
    
    if(!empty($acoes_assocPara)){
        echo "DELETE FROM funcionario_acoes_assoc WHERE id_funcionario = $funcionarioPara;"; 
    }
    
    //INSERE AS PERMISSOES DO FUNCIONARIO DE P/ O FUNCIONARIO PARA
    foreach ($func_reg_assocDe as $id_master => $regioes) {
        foreach ($regioes as $id_regiao) {
            echo "INSERT INTO funcionario_regiao_assoc (id_funcionario, id_regiao, id_master) VALUES ( '$funcionarioPara', '$id_regiao','$id_master' );";
        }
    }
    
    foreach ($btn_assocDe as $idBotao) {
        foreach ($idBotao as $id) {
            echo "INSERT INTO botoes_assoc (botoes_id, id_funcionario) VALUES ('$id', '$funcionarioPara');";
        }
    }
    
    $regioes_permitidas = montaQuery("funcionario_regiao_assoc", "id_regiao", "id_funcionario = '$funcionarioPara'");
    foreach ($regioes_permitidas as $valueRegioes) {
        foreach ($acoes_assocDe as $id_botao => $acoes) {
            foreach ($acoes as $idAcao) {
                echo "INSERT INTO funcionario_acoes_assoc (id_funcionario, acoes_id, id_regiao, botoes_id ) VALUES('$funcionarioPara', '$idAcao','{$valueRegioes['id_regiao']}','$id_botao');";
            }
        }
    }

    header("Location: http://www.netsorrindo.com/intranet/funcionario/");
}


?>


<html>
    <head>
         <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    </head>
    <body>
       <form action="" method="post" name="form2" id="form2" enctype="multipart/form-data" >
            <h4>Copiar as Permissões</h4>
            <p>
                <label>DE:</label> <?php echo montaSelect($arrayFuncionario, null, "name='funcionarioDe' id='funcionarioDe' class='validate[required]' style='width: 340px;'");?> 
                <input type="hidden" id="funcionarioPara" name="funcionarioPara" value="<?php echo $funcionario; ?>"/>
            </p>
            <p class="controls"> 
                <input type="submit" name="copiar" value="Copiar" />
            </p>
        </form>
    </body>
</html>
        