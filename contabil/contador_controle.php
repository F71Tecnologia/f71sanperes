<?php
header('Content-Type: text/html; charset=iso-8859-1');
if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}
include("../conn.php");
include("../classes/global.php");
include("../classes/ContabilContadorClass.php");
include("../wfunction.php");

$objContador = new ContabilContadorClass();

$sql_uf = "SELECT uf_id, uf_sigla FROM uf";
$opc['-1'] = 'UF';
$return = mysql_query($sql_uf);

while ($row = mysql_fetch_assoc($return)) {
    $opc[$row['uf_sigla']] = $row['uf_sigla'];
}

$estado = "";
    foreach ($opc as $k => $val) {
        $estado .= "<option value=\"{$k}\">" . utf8_encode($val) . "</option>";
    }
    $opc1 = array('O','P','K','E');
$controle = "";
    foreach ($opc1 as $k1 => $val) {
        $controle .= "<option value=\"{$k1}\">" . utf8_encode($val) . "</option>";
    }
        

// SALVAR ----------------------------------------------------------------------
if (isset($_REQUEST['cadastro-salvar']) && $_REQUEST['cadastro-salvar'] == 'Cadastrar') {
    $objContador->setNome($_REQUEST['nomeContador']);
    $objContador->setCrcUf($_REQUEST['estadoCRC']);
    $objContador->setCrc($_REQUEST['numeroCRC']);
    $objContador->setCrcControl($_REQUEST['controleCRC']);
    $objContador->setCpf($_REQUEST['cpf']);
    $objContador->setTelComercial($_REQUEST['telefone']);
    $objContador->setTelCelular($_REQUEST['celular']);
    $objContador->setEmail($_REQUEST['email']);
    $objContador->setProfissional($_REQUEST['tipo']);
    $objContador->setCadastro(date('Y-m-d H:i'));
    $objContador->setStatus(1);
    
    $verificar = $objContador->consulta();
    
    if($objContador->getNumRows()==0){
        $result = $objContador->insert();
    } else {
        $result = 2;
    }
    
    if ($result == 1) {
        echo json_encode(array('status' => TRUE, 'msg' => 'Cadastro de contador realizado com sucesso.'));
    }else if ($result == 2) {
        echo json_encode(array('status' => FALSE, 'msg' => 'Contador cadastrado...!'));
    } else {
        echo json_encode(array('status' => FALSE, 'msg' => 'Erro ao salvar Contador.'));
    }
    exit();
}

// alteração dos Contadores
if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'alterar_contador') {
    $contador_id = $_REQUEST['edita_id_contador'];
    $alterar_contador = $objContador->alteracao(
        $_REQUEST['edita_id_contador'], 
        $_REQUEST['edita_cpf'], 
        $_REQUEST['edita_crc_uf'], 
        $_REQUEST['edita_crc'], 
        $_REQUEST['edita_crc_control'], 
        addslashes($_REQUEST['edita_nome']), 
        $_REQUEST['edita_telefone'], 
        $_REQUEST['edita_celular'],
        $_REQUEST['edita_email'],            
        $_REQUEST['edita_profissional'] 
    );
 
    echo $alterar_contador; 
    exit();
}

// CONSULTAR -------------------------------------------------------------------
if (isset($_REQUEST['contadores']) && $_REQUEST['contadores'] == 'Listagem') {
    exit($print_array($_REQUEST));
    $filtra = $objContador->getCpf($_REQUEST['cpf']); ?>
    
    <p class="text-right"><button type="button" class="btn btn-success hidden-print" onclick="tableToExcel('tbRelatorio', 'Plano de Contas')"><span class="fa fa-file-excel-o"></span> Exportar para Excel</button></p>
    <table id="tbRelatorio" class="table table-condensed table-striped text text-sm">
        <thead>
            <tr>
                <th>Classificador</th>
                <th>Código</th>
                <th>Descrição</th>
                <th class="text text-center">Tipo</th>
                <th class="text text-center">Natureza</th>
                <th class="text text-center">Nível</th>
                <th colspan="2"></th>
            </tr>
        </thead>
        <tbody>
            <?php 
            foreach ($filtrar as $value) { ?>
                <tr id="tr-<?=$value['id_contador']?>" >
                <td class="text text-right hidden-print">
                    <button type="button" class="btn btn-warning btn-xs" id="edita_conta" name="edita_conta" value="<?=$value['id_conta']?>" data-id="<?=$value['id_conta']?>" data-projeto="<?=$value['id_projeto']?>" title="Editar" data-toggle="tooltip">
                    <span class="glyphicon glyphicon-edit"></span>
                    </button>
                    <button type="button" class="btn btn-danger btn-xs" id="cancela_conta" name="cancela_conta" data-cancelar_id="<?=$value['id_conta']?>" data-descricao="<?= $value['descricao'] ?>" data-classificador="<?= $value['classificador'] ?>" title="Excluir" data-toggle="tooltip">
                    <span class="glyphicon glyphicon-trash"></span>
                    </button> 
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
    
    <?php exit();
}

if (isset($_REQUEST['edita_contador'])) { 
    $editar_contador = $objContador->retorna_contador($_REQUEST['id_contador']);  ?>
    <div class="form-group">
        <div class="row">
            <label class="col-lg-3 label-control text text-sm">CPF</label>
            <div class="col-lg-5">
                <input type="hidden" id="edita_id_contador" name="edita_id_contador" value="<?= $editar_contador['id_contador']?>">
                <input type="text" value="<?= $editar_contador['cpf'] ?>" id="edita_cpf" name="edita_cpf" class="form-control text-center">
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="row">
            <label class="col-lg-3 control-label text text-sm">CRC</label>
            <div class="col-md-7">
                <div class="input-group">
                    <input type="text" value="<?= $editar_contador['crc_uf'] ?>" id="edita_crc_uf" name="edita_crc_uf" class="form-control">
                    <div class="input-group-addon"></div>
                    <input type="text" value="<?= $editar_contador['crc'] ?>" id="edita_crc" name="edita_crc" class="form-control">
                    <div class="input-group-addon"> - </div>
                    <input type="text" value="<?= $editar_contador['crc_control'] ?>" id="edita_crc_control" name="edita_crc_control" class="form-control">
                </div>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="row">
            <label class="col-lg-3 control-label text text-sm">Nome</label>
            <div class="col-lg-8">
                <input type="text" value="<?= $editar_contador['nome']?>" class="form-control" id="edita_nome" name="edita_nome">
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="row">
            <label class="col-lg-3 control-label text text-sm">Contato</label>
            <div class="col-lg-4">
                <input type="text" value="<?= $editar_contador['tel_comercial'] ?>" class="form-control text-center" id="edita_telefone" name="edita_telefone">
            </div>
            <div class="col-lg-4 control-label text-center">
                <input type="text" value="<?= $editar_contador['tel_celular'] ?>" class="form-control text-center" id="edita_celular" name="edita_celular">
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="row">
            <label class="col-lg-3"></label>
            <div class="col-lg-8">
                <input type="text" value="<?= $editar_contador['email'] ?>" class="form-control" id="edita_email" name="edita_email">
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="row">
            <label class="col-lg-3 control-label text text-sm">Profissional</label>
            <div class="col-lg-4">
                <select class="form-control" id="edita_profissional" name="edita_profissional">
                    <option value="1" <?php if ($editar_contador['profissional'] == "1") { echo "selected";}?> >Técnico Contabil</option>
                    <option value="2" <?php if ($editar_contador['profissional'] == "2") { echo "selected";}?> >Contador</option>
                </select>
            </div>
        </div>
    </div>
    
    <script>
        $(document).ready(function () {
            $("input[name='edita_cpf']").mask('999.999.999-99');
            $("input[name='edita_telefone']").mask('(99)9999-9999');
            $("input[name='edita_celular']").mask('(99)99999-9999');
        })
    </script>

    <?php exit();
}

if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'cancela_contador') {
    $objContador->setIdContador($_REQUEST['contador']);
    $cancelar_contador = $objContador->inativa();
 
    echo $cancelar_contador; 
    exit();
}


// -----------------------------------------------------------------------------
function str_to_float($string){
    return str_replace('@', ',', str_replace(',', '.', str_replace('.', '@', $string)));
}
