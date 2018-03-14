<?php
header('Content-Type: text/html; charset=iso-8859-1');
if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include("../../../conn.php");
include("../../../funcoes.php");
include("../../../wfunction.php");
include("../../../classes_permissoes/acoes.class.php");
include("classes/AssocReceitaClass.php");
include("classes/AssocContasClass.php");
include("classes/AssocBancosClass.php");
include("classes/AssocProvisaoClass.php");
include("../../../classes/c_planodecontasClass.php");

$usuario = carregaUsuario();
$objAcao = new Acoes();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEï¿½ALHO (TROCA DE MASTER E DE REGIï¿½ES)

$objAssociar = new AssocContasClass();
$objContas = new c_planodecontasClass();
$objBanco = new AssocBancosClass();
$objProvisao = new AssocProvisaoClass();
$objReceita = new AssocReceitaClass();

// SALVAR PROVISï¿½O DA FOLHA DE RESCISï¿½O ----------------------------------------
if (isset($_REQUEST['provisao-salvar']) && $_REQUEST['provisao-salvar'] == 'Provisionar') {
    $objProvisao->setIdRubrica($_REQUEST['finan']);
    $objProvisao->setIdContabil1($_REQUEST['contabil']);
    $objProvisao->setIdContabil2($_REQUEST['contabil_dre']);
    $objProvisao->setData(date('Y-m-d H:i'));
    $objProvisao->setIdFuncionario($_REQUEST['usuario']);
    $objProvisao->setIdProjeto($_REQUEST['projeto']);
    $objProvisao->setStatus(1);
    
    $verificar = $objProvisao->consulta();

    if ($objProvisao->getNumRows() == 0) {
        $result = $objProvisao->insert();
    } else {
        $result = 2;
    }

    if ($result == 1) {
        echo json_encode(array('status' => TRUE, 'msg' => 'Associacao Realizada. OK!'));
    } else if ($result == 2) {
        echo json_encode(array('status' => FALSE, 'msg' => 'Associacao ja Efetuada. Atencao!'));
    } else {
        echo json_encode(array('status' => FALSE, 'msg' => 'Associacao nao efetuada. Erro!'));
    }
    exit();
}

// SALVAR DESPESAS/ RECEITAS/ FOLHA -----------------------------------------------------
if (isset($_REQUEST['cadastro-salvar']) && $_REQUEST['cadastro-salvar'] == 'Cadastrar') {

    if($_REQUEST['contabil'] <= 0){
        $contabil = $_REQUEST['contabil_dre'];
    } else {
        $contabil = $_REQUEST['contabil'];
    }
    $objAssociar->setIdContabil($contabil);
    $objAssociar->setIdConta($_REQUEST['finan']);
    $objAssociar->setData(date('Y-m-d H:i'));
    $objAssociar->setStatus(1);
    $objAssociar->setIdFuncionario($_REQUEST['usuario']);
    $objAssociar->setIdProjeto($_REQUEST['projeto']);
    $objAssociar->setFolha($_REQUEST['folha']);
    $objAssociar->setIdFornecedor($_REQUEST['fornecedor']);

    $verificar = $objAssociar->consulta();

    if ($objAssociar->getNumRows() == 0) {
        $result = $objAssociar->insert();
    } else {
        $result = 2;
    }

    if ($result == 1) {
        echo json_encode(array('status' => TRUE, 'msg' => 'Associacao Realizada. OK!'));
    } else if ($result == 2) {
        echo json_encode(array('status' => FALSE, 'msg' => 'Associacao ja Efetuada. Atencao!'));
    } else {
        echo json_encode(array('status' => FALSE, 'msg' => 'Associacao nao efetuada. Erro!'));
    }
    exit();
}

// SALVAR CONTA CORRENTE ---------------------------------------------------------------------
if (isset($_REQUEST['cc-salvar']) && $_REQUEST['cc-salvar'] == 'Cadastrar_CC') {
    $objBanco->setIdConta($_REQUEST['contabil']);
    $objBanco->setIdBanco($_REQUEST['cc_banco']);
    $objBanco->setData(date('Y-m-d H:i'));
    $objBanco->setStatus(1);
    $objBanco->setIdFuncionario($_REQUEST['usuario']);
    $objBanco->setIdProjeto($_REQUEST['projeto']);

    $verificar = $objBanco->consulta_cc();

    if ($objBanco->getNumRows() == 0) {
        $result = $objBanco->insert();
    } else {
        $result = 2;
    }

    if ($result == 1) {
        echo json_encode(array('status' => TRUE, 'msg' => 'Associacao Realizada. OK!'));
    } else if ($result == 2) {
        echo json_encode(array('status' => FALSE, 'msg' => 'Associacao ja Efetuada. Atencao!'));
    } else {
        echo json_encode(array('status' => FALSE, 'msg' => 'Associacao nao efetuada. Erro!'));
    }
    exit();
}

// CONSULTAR -------------------------------------------------------------------
if (isset($_REQUEST['filtra']) && $_REQUEST['filtra'] == 'Filtrar') {

    $filtrar = $objAssociar->getPlanoContaFull($_REQUEST['projeto']); ?>

    <div class="form-group">
        <table id="tbRelatorio" class="table table-condensed table-striped text text-sm">
            <thead>
                <tr>
                    <th>Acesso</th>
                    <th>Classificação</th>
                    <th>Código</th>
                    <th>Descrição</th>
                    <th>Id</th>
                    <th>Fornecedor</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($filtrar as $value) { ?>
                    <tr id="tr-<?= $value['id'] ?>">
                        <td><?= $value['acesso'] ?></td>
                        <td class="text-uppercase"><?= $value['classificacao'] ?></td>
                        <td><?= $value['codigo'] ?></td>
                        <td><?= $value['descricao'] ?></td>
                        <td><?= $value['id_fornecedor'] ?></td>
                        <td class="fontpeq"><?= $value['fornecedor'] ?></td>
                        <td class="text text-right hidden-print">
                            <button type="button" class="btn btn-danger btn-xs" id="cancelar_assoc" value="Cancelar" name="cancelar_assoc" 
                                    data-cancelar_id="<?= $value['id'] ?>" data-nome_id="<?= $value['classificacao'] ?>" data-descricao="<?= $value['descricao'] ?>" title="Cancelar" data-toggle="tooltip">
                                <span class="glyphicon glyphicon-trash"></span>
                            </button> 
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <div class="col-sm-12 text-right">
        <div class="row">                        
            <div class="btn-group">
                <button onclick="javascript: location.href = 'form_assoc_receita.php?projeto=<?= $_REQUEST['projeto'] ?>'" type="button" class="btn btn-info btn-sm tamanho" id="nova_assoc_receita" name="nova_assoc_receita" value="" data-id="<?= $value['id_conta'] ?>" title="Novo" data-toggle="tooltip">
                    Receita 
                </button>
            </div>
            <div class="btn-group">
                <button onclick="javascript: location.href = 'form_assoc_subvencao.php?projeto=<?= $_REQUEST['projeto'] ?>'" type="button" class="btn btn-info btn-sm tamanho" id="nova_assoc_subvencao" name="nova_assoc_subvencao" value="" data-id="<?= $value['id_conta'] ?>" title="Novo" data-toggle="tooltip">
                    Renovação Contrato                  
                </button>
            </div>
        </div>
        <div class="row">                        
            <div class="btn-group">
                <button onclick="javascript: location.href = 'form_assoc_despesa.php?projeto=<?= $_REQUEST['projeto'] ?>'" type="button" class="btn btn-warning btn-sm tamanho" id="nova_assoc_despesa" name="novo_assoc_despesa" value="" data-id="<?= $value['id_conta'] ?>" title="Novo" data-toggle="tooltip">
                    Despesa
                </button>
            </div>
            <div class="btn-group">
                <button onclick="javascript: location.href = 'form_assoc_folha.php?projeto=<?= $_REQUEST['projeto'] ?>'" type="button" class="btn btn-warning btn-sm tamanho" id="nova_assoc_folha" name="nova_assoc_folha" value="" data-id="<?= $value['id_conta'] ?>" title="Novo" data-toggle="tooltip">
                    Folha Pagamento
                </button>
            </div>
            <div class="btn-group">
                <button onclick="javascript: location.href = 'form_assoc_provisao.php?projeto=<?= $_REQUEST['projeto'] ?>'" type="button" class="btn btn-warning btn-sm tamanho" id="nova_assoc_provisao" name="nova_assoc_provisao" value="" data-id="<?= $value['id_conta'] ?>" title="Novo" data-toggle="tooltip">
                    Provisão Rescisão
                </button>
            </div>
        </div>
        <div class="row">                        
            <div class="btn-group">
                <button onclick="javascript: location.href = 'form_assoc_contacorrente.php?projeto=<?= $_REQUEST['projeto'] ?>'" type="button" class="btn btn-default btn-sm tamanho" id="nova_assoc_contacorrente" name="nova_assoc_contacorrente" value="" data-id="<?= $value['id_conta'] ?>" title="Novo" data-toggle="tooltip">
                    Conta Corrente
                </button>
            </div>
        </div>
    </div>
    <?php
    exit();
}

if ($_REQUEST['method'] == 'cancelar_assoc') {
    $objAssociar->setIdAssoc($_REQUEST['id']);
    $cancelarAssociacao = $objAssociar->inativa();

    echo $cancelarAssociacao;
    exit();
}

if ($_REQUEST['method'] == 'folha') {
    if ($_REQUEST['folha'] == '1') { // Salarios folha
        $OpcaoFolha = $objAssociar->consultafolha($_REQUEST['folha']);
        foreach ($OpcaoFolha as $key => $row) {
            echo "<option value=\"{$key}\">{$row}</option>";
        }
    } else if ($_REQUEST['folha'] == '2') {// Recibos de Fï¿½rias
        $OpcaoFolha = $objAssociar->consultafolha($_REQUEST['folha']);
        foreach ($OpcaoFolha as $key => $row) {
            echo "<option value=\"{$key}\">{$row}</option>";
        }
    } else if ($_REQUEST['folha'] == '3') { // Termo de rescisï¿½o Trabalhista
        $OpcaoFolha = $objAssociar->consultafolha($_REQUEST['folha']);
        foreach ($OpcaoFolha as $key => $row) {
            echo "<option value=\"{$key}\">{$row}</option>";
        }
    }
    exit();
}

// SALVAR ASSOCIAÃ‡ÃƒO DA RECEITA ----------------------------------------
if (isset($_REQUEST['associar-salvar']) && $_REQUEST['associar-salvar'] == 'Associar') {
    $objReceita->setIdConta($_REQUEST['finan']);
    $objReceita->setIdContabil1($_REQUEST['contabil_a']);
    $objReceita->setIdContabil2($_REQUEST['contabil_p']);
    $objReceita->setData(date('Y-m-d H:i'));
    $objReceita->setIdFuncionario($_REQUEST['usuario']);
    $objReceita->setIdProjeto($_REQUEST['projeto']);
    $objReceita->setStatus(1);
    
    $verificar = $objReceita->consulta();

    if ($objReceita->getNumRows() == 0) {
        $result = $objReceita->insert();
    } else {
        $result = 2;
    }

    if ($result == 1) {
        echo json_encode(array('status' => TRUE, 'msg' => 'Associacao Realizada. OK!'));
    } else if ($result == 2) {
        echo json_encode(array('status' => FALSE, 'msg' => 'Associacao ja Efetuada. Atencao!'));
    } else {
        echo json_encode(array('status' => FALSE, 'msg' => 'Associacao nao efetuada. Erro!'));
    }
    exit();
}

// -----------------------------------------------------------------------------
function str_to_float($string) {
    return str_replace('@', ',', str_replace(',', '.', str_replace('.', '@', $string)));
}
