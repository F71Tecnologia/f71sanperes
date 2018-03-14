<?php
include ("../../conn.php");
include ("../../classes/global.php");
include ("AssocContas.class.php");
include ("../../wfunction.php");

header('Content-Type: text/html; charset=iso-8859-1');

$assocContas = new PlanoContasAssoc();


if (isset($_REQUEST['associar_despesas']) && $_REQUEST['associar_despesas'] == 'Associar Despesa') {

    $prosoft_regiao1    = $_REQUEST['prosoft_regiao1'];
    $prosoft_projeto1   = $_REQUEST['prosoft_projeto1'];
    $prosoft_despesaP   = $_REQUEST['prosoft_despesaP'];      // id plano de contas Prosoft
    $prosoft_despesaL   = $_REQUEST['prosoft_despesaL'];      // id plano de contas Instituto Lagos
    
    if ($assocContas->checkAssoc($prosoft_despesaP, $prosoft_despesaL,  $prosoft_projeto1)) {
        $query = "INSERT INTO entradaesaida_plano_contas_assoc (id_plano_contas, id_entradasaida, id_projeto)
                VALUES ('{$prosoft_despesaP}','{$prosoft_despesaL}','{$prosoft_projeto1}')";

        $result = mysql_query($query) or die('Erro ao Associar Contas. Detalhes: ' . mysql_error());

        if ($result) {
            ?>
            <div class="alert alert-success alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>
                <p>Conta Despesa Associação Ok.</p>
            </div>
            <?php } else {
            ?>
            <div class="alert alert-danger alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>
                <p>Associação entre Planos de Contas... Não Realizado.</p>
            </div>
        <?php
        }
        exit();
    } else {
        ?>
            <div class="alert alert-danger alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>
                <p>Associação já existe.</p>
            </div>
        <?php
    }
}

if (isset($_REQUEST['associar_receitas']) && $_REQUEST['associar_receitas'] == 'Associar Receita') {
    $prosoft_regiao2    = $_REQUEST['prosoft_regiao2'];
    $prosoft_projeto2   = $_REQUEST['prosoft_projeto2'];
    $prosoft_receitaP   = $_REQUEST['prosoft_receitaP'];      // id plano de contas Prosoft
    $prosoft_receitaL   = $_REQUEST['prosoft_receitaL'];      // id plano de contas Instituto Lagos
    
    if ($assocContas->checkAssocR($prosoft_receitaP, $prosoft_receitaL, $prosoft_projeto2)) {

        $query = "INSERT INTO entradaesaida_plano_contas_assoc (id_plano_contas, id_entradasaida, id_projeto)
            VALUES ('{$prosoft_receitaP}','{$prosoft_receitaL}','{$prosoft_projeto2}')";

        $result = mysql_query($query) or die('Erro ao Associar Contas. Detalhes: ' . mysql_error());

        if ($result) { ?>
            <div class="alert alert-success alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>
                <p>Associação da Receita... Realizada.</p>
            </div>
            <?php } else {
            ?>
            <div class="alert alert-danger alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>
                <p>Associação entre Planos de Contas... Não Realizado.</p>
            </div>
        <?php
        }
        exit();
    } else {
        ?>
            <div class="alert alert-danger alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>
                <p>Associação já existe.</p>
            </div>
        <?php
    }
}

if (isset($_REQUEST['associar_empresas']) && $_REQUEST['associar_empresas'] == 'Associar Empresa') {

    $prosoft_regiao4 = $_REQUEST['prosoft_regiao4'];
    $prosoft_projeto4 = $_REQUEST['prosoft_projeto4'];
    $empresaprosoft = $_REQUEST['empresaprosoft'];

    $query = "INSERT INTO empresas_Prosoft_assoc (id_projeto, id_empresa)
              VALUES ('{$prosoft_projeto4}','{$empresaprosoft}')";

    $result = mysql_query($query) or die('Erro ao Associar Empresa. Detalhes: ' . mysql_error());

    if ($result) {
        ?>
        <div class="alert alert-success alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>
            <p>Associação da Empresa... Ok!</p>
        </div>
        <?php } else {
        ?>
        <div class="alert alert-danger alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>
            <p>Associação entre Planos de Contas... Não Realizado.</p>
        </div>
    <?php
    }

    exit();
}

if (isset($_REQUEST['associar_folha']) && $_REQUEST['associar_folha'] == 'Associar') {
    
    $array_F = array('5034','5035','5036','5037','80020');
    $array_R = array('r000','r001','r002','r003','r004','r005','r006','rc07','rd07','r008',
                    'r009','r010','r011','r012','14','54','56','62','63','66','151',
                    '152','197','199','202','203','232','279','12506','r477','r479','r480');
    
    $folha_regiao   = $_REQUEST['folha_regiao'];
    $folha_projeto  = $_REQUEST['folha_projeto'];
    $folha_codigo   = $_REQUEST['folha_codigo'];
    $folha_prosoft  = $_REQUEST['folha_prosoft'];
    $folha_tipo     = $_REQUEST['folha_tipo']; 
    
    if (in_array($folha_codigo, $array_F)) {
        $folha = 'F' ;
    } elseif (in_array($folha_codigo, $array_R)) {
        $folha = 'R';
    } else {
        $folha = 'N';
    }
    
    if (!$assocContas->checkAssocFolha($folha_projeto, $folha_codigo, $folha_prosoft, $folha_tipo)) {
        $query = "INSERT INTO contabil_folha_prosoft (id_codigo, id_plano_de_conta, id_projeto, tipo, folha)
                  VALUES ('{$folha_codigo}','{$folha_prosoft}','{$folha_projeto}','{$folha_tipo}','{$folha}')";

        $result = mysql_query($query) or die('Erro ao Associar Contas. Detalhes: ' . mysql_error());

        if ($result) { ?>
            <div class="alert alert-success alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>
                <p>Associação com Folha Pagamento Ok...</p>
            </div>
        <?php
        } else { ?>
            <div class="alert alert-danger alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>
                <p>Associação entre Planos de Contas Prosoft X Sistema...! Não Realizado!</p>
            </div>
        <?php
        }
        exit();
        } else { ?>
            <div class="alert alert-danger alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>
                <p>Associação já existe.</p>
            </div>
        <?php
    }
}

if (isset($_REQUEST['method']) && !empty($_REQUEST['method']) && $_REQUEST['method'] == "carregaEmpresas") {
    $opt = (!empty($_REQUEST['default'])) ? $_REQUEST['default'] : 1;
    $request = $_REQUEST['request'];
    $empresa = (!empty($request)) ? $_REQUEST[$request] : $_REQUEST['empressa'];
//    $rs = GlobalClass::carregaPrestadorByProjeto($empresa, $defaults[$opt]);
    $empresa = "";
    foreach ($rs as $k => $val) {
        $empresa .= "<option value=\"{$k}\">" . utf8_encode($val) . "</option>";
    }
    echo $empresa;
    exit;
}


















