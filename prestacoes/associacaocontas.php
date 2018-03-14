<?php
include ("../conn.php");
include ("../classes/global.php");
include ("../wfunction.php");
header('Content-Type: text/html; charset=iso-8859-1');
if (isset($_REQUEST["associar_contas"])) { 
    
    $prosoft_regiao1 = $_REQUEST['prosoft_regiao1'];
    $prosoft_projeto1 = $_REQUEST['prosoft_projeto1'];
    $prosoft_contaP = $_REQUEST['prosoft_contaP'];      // id plano de contas Prosoft
    $prosoft_contaL = $_REQUEST['prosoft_contaL'];      // id plano de contas Instituto Lagos
    $prosoft_terceiro = $_REQUEST['prosoft_terceiro'];

    $query = "INSERT INTO
            entradaesaida_plano_contas_assoc (id_plano_contas, id_entradasaida, id_projeto, id_terceiro)
            VALUES ('{$prosoft_contaP}','{$prosoft_contaL}','{$prosoft_projeto1}','{$prosoft_terceiro}')";
    
    $result = mysql_query($query) or die('Erro ao Associar Contas. Detalhes: ' . mysql_error());
            
    if ($result) { ?>
        <div class="alert alert-success alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>
                <p>Associação feita sem Problemas.</p>
        </div>
        <?php
    } else { ?>
        <div class="alert alert-danger alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>
            <p>Não foi possível associar.</p>
        </div>
        <?php }
    
    exit();
}


