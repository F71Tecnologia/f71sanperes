<?php
/*
 * form_pis.php
 * 
 * 00-00-0000
 * 
 * Rotina para processamento de pis em lote
 * 
 * Versão: 3.0.1709 - 26/08/2015 - Jacques - Incluída footer com definição da tag_ver para controle de versão na tela do usuário.
 * 
 * @author Não definido
 * 
 */

if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="login.php">Logar</a>';
    exit;
}

//error_reporting(E_ALL);

function testeEmpt($var) {
    echo (empty($var) && strlen($var) == 0) ? 'warning' : '';
}

if (isset($_REQUEST['download']) && !empty($_REQUEST['download'])) {
    $file = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'arquivos' . DIRECTORY_SEPARATOR . $_REQUEST['download'];
    $name_file_download = isset($_REQUEST['name_file']) ? $_REQUEST['name_file'] : $_REQUEST['download'];

    header("Content-Type: application/save");
    header("Content-Length:" . filesize($file));
    header('Content-Disposition: attachment; filename="' . $name_file_download . '"');
    header("Content-Transfer-Encoding: binary");
    header('Expires: 0');
    header('Pragma: no-cache');
    $fp = fopen("$file", "r");
    fpassthru($fp);
    fclose($fp);
    exit();
}

include "../../conn.php";
include "../../wfunction.php";
include "../../funcoes.php";
include "../../classes/funcionario.php";
include "../../classes_permissoes/regioes.class.php";
include "../../classes/FormataDadosClass.php";
include "../../classes/abreviacao.php";
include "../../classes_permissoes/acoes.class.php";
include "dao/PisLoteClass.php";

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$breadcrumb_config = array("nivel" => "../../", "key_btn" => "3", "area" => "Recursos Humanos", "id_form" => "form1", "ativo" => "Gerar PIS em lote");
$breadcrumb_pages = array("Gestão de RH" => "../");
$optRegiao = getRegioes();
$optRegiao['-1'] = '« Todas as Regiões »';
ksort($optRegiao);

if (isset($_REQUEST['acao']) && $_REQUEST['acao'] == 'gravar_id') {
    $cookie_name = "idscltpis";

    $arr = isset($_COOKIE[$cookie_name]) ? explode(',', $_COOKIE[$cookie_name]) : array();

//    unset($_COOKIE[$cookie_name]);
//    setcookie($cookie_name, null, -1, '/');
//    echo " Array antigo \n";
//    print_r($arr);
//    echo "\n";
//    $cookie_name2 = "user";
//    $cookie_value2 = "John Doe 1";
//    setcookie($cookie_name2, $cookie_value2, time() + (86400 * 30), "/"); // 86400 = 1 day

    if (in_array($_REQUEST['id_clt'], $arr)) {
        foreach ($arr as $k => $v) {
            if ($v == $_REQUEST['id_clt']) {
                unset($arr[$k]);
            }
        }
    } else {
        $arr[$_REQUEST['id_clt']] = $_REQUEST['id_clt'];
    }

//    echo " Array novo \n";
//    print_r($arr);

    $cookie_value = implode(',', array_unique($arr));

//    echo "VAL = \n\n";
//    var_dump($cookie_name);
//    var_dump($cookie_value);

    setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/");

    echo json_encode(array('status' => TRUE));
//    echo "\n\n";
//    echo " CK  \n";
//    var_dump($_COOKIE);
//    exit('fim 3');
    exit();
}

if (isset($_REQUEST['gerar'])) {
    $cookie_name = "idscltpis";

    $ids_negados = isset($_COOKIE[$cookie_name]) ? array_filter(explode(',', $_COOKIE[$cookie_name])) : array();
    $post_regiao = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : FALSE;
    $post_projeto = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : FALSE;


    $dao = new PisLoteClass();

    $arr_relatorio = $dao->getRelacao($post_regiao, $post_projeto, $ids_negados);

    $ids_error = $dao->verificaErro($arr_relatorio);

    $ids_all = array_unique(array_merge($ids_error, $ids_negados));

    $arr_relatorio = $dao->getRelacao($post_regiao, $post_projeto, $ids_all);

    $cookie_value = implode(',', $ids_all);

    setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/");

    if (isset($_REQUEST['arquivo'])) {
        $arr = $dao->montarArquivoCompleto($arr_relatorio, $_REQUEST['master']);
        echo json_encode($arr);
        exit();
    }
}

$sql = "SELECT nome FROM `master` WHERE id_master = {$usuario['id_master']} AND status=1";
$result = mysql_query($sql);
$master = mysql_fetch_array($result);

$regiaoSel = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $usuario['id_regiao'];
$projetoSel = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : null;
?>
<html>
    <head>
        <title>:: Intranet :: PIS EM LOTE</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../../favicon.png" />       

        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap-rh.css" rel="stylesheet" type="text/css">        
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />        

        <link href="../../favicon.ico" rel="shortcut icon" />                

        <script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../../resources/js/bootstrap.min.js" type="text/javascript"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js" type="text/javascript"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js" type="text/javascript"></script>

        <script>
            $(function () {
                $('#regiao').change(function () {
                    if ($(this).val() <= 0) {
                        $('#projeto').html('<option value="-1">« Selecione »</option>');
                    }
                });
                $('#regiao').ajaxGetJson("../../methods.php", {method: "carregaProjetos"}, null, "projeto");

                $('#gerar_arquivo').click(function () {
                    var projeto = $('#post_projeto').val();
                    var regiao = $('#post_regiao').val();
                    $.post(window.location, {regiao: regiao, projeto: projeto, arquivo: 1, gerar: 1, master: $("#master").val()}, function (data) {
//                       console.log(data);
                        window.location = '?download=' + data.download + '&name_file=' + data.name_file;
                    }, 'json');
                });

                $(".detalhes").click(function () {
                    var id = $(this).data('id');
                    var html = $("#dados-" + id).html();
                    console.log(html);
                    bootDialog(html, 'Detalhes');
                });
            });
        </script>
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>

        <div class="container">
            <div class="row">
                <div class="col-xs-12">

                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS<small> - PIS em Lote</small></h2></div>

                    <ul class="nav nav-tabs" style="margin-bottom: 20px;">
                        <li role="presentation" class="active"><a href="#">Gerar Pis Em Lote</a></li>
                        <li role="presentation"><a href="indexx.php">Atualizar Pis em Lote</a></li>
                    </ul>
                </div>
                <div class="col-xs-12">
                    <form name="form" action="" method="post" id="form" class="form-horizontal top-margin1">

                        <div class="panel panel-default">
                            <div class="panel-body">                                                            
                                <div class="form-group">
                                    <label for="exampleInputEmail1" class="col-lg-2 control-label">Região:</label>
                                    <div class="col-lg-9">
                                        <?php echo montaSelect($optRegiao, $regiaoSel, array('name' => "regiao", 'id' => 'regiao', 'class' => 'form-control')); ?>
                                    </div>
                                </div>                            
                                <div class="form-group">
                                    <label for="exampleInputEmail1" class="col-lg-2 control-label">Projeto:</label>
                                    <div class="col-lg-9">
                                        <?php echo montaSelect(array("-1" => "« Selecione a Região »"), $projetoSel, array('name' => "projeto", 'id' => 'projeto', 'class' => 'form-control')); ?>                                        
                                    </div>
                                </div>
                            </div>
                            <div class="panel-footer text-right">
                                <input type="hidden" id="master" name="master" value="<?= $usuario['id_master']; ?>">
                                <input type="hidden" name="hide_projeto" id="hide_projeto" value="<?php echo $projetoSel ?>" />
                                <input type="submit" name="gerar" value="Gerar" id="gerar" class="btn btn-info" />
                            </div>
                        </div>

                        <?php
                        if (isset($_POST['gerar'])) {
                            if (!empty($arr_relatorio)) {
                                $arr_negados = array();
                                //                        print_r($arr_relatorio);
                                ?>

                                <span class="pull-right"><a class="btn btn-success" href="#" onclick="tableToExcel('tbRelatorio', 'Participantes Ativos')"><i class="fa fa-file-excel-o"></i> Exportar para Excel</a></span>

                                <p><span class="bg-success">&emsp;&emsp;</span> Funcionários SEM problemas de Cadastro.</p>
                                <p><span class="bg-warning">&emsp;&emsp;</span> Funcionários COM problemas de Cadastro. (Não entra na geração do arquivo de lote)</p>

                                <table class="table table-striped table-hover valign-middle" id="tbRelatorio" style="font-size: 14px;">
                                    <thead>
                                        <tr>
                                            <th colspan="9"><?php echo $projeto['nome'] ?></th>
                                        </tr>
                                        <tr>
                                            <th>ID</th>
                                            <th>NOME</th>
                                            <th>PROJETO</th>
                                            <th>PIS INVÁLIDO</th>
                                            <th>FUNÇÃO</th>
                                            <th>STATUS</th>
                                            <th>DATA DE ADMISSÃO</th>   
                                            <th>EXCLUIR DA LISTAGEM</th>   
                                            <th>DETALHES</th>   
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        foreach ($arr_relatorio as $row_rel) {
                                            //                                  $class = ($cont++ % 2 == 0) ? "even" : "odd";
                                            $class = (in_array($row_rel['id_clt'], $ids_error)) ? 'warning' : 'success';
                                            if ($row_rel['flag'] == 1) {
                                                $arr_negados[] = $row_rel;
                                            } else {
                                                ?>
                                                <tr class="<?php echo $class ?>">
                                                    <td><?php echo $row_rel['id_clt'] ?></td>
                                                    <td><?php echo $row_rel['nome'] ?></td>
                                                    <td><?php echo $row_rel['unidade'] ?></td>
                                                    <td class="center"><?php echo $row_rel['pis'] ?></td>
                                                    <td> <?php echo $row_rel['funcao']; ?></td>
                                                    <td> <?php echo $row_rel['especifica']; ?></td>
                                                    <td align="center"><?php echo $row_rel['data_entrada_br']; ?></td>                                                       
                                                    <td class="text-center">
                                                        <button type="button" class="btn btn-danger btn-sm remove_lista" data-id="<?= $row_rel['id_clt'] ?>">
                                                            <i class="fa fa-times"></i>
                                                        </button>
                                                    </td>
                                                    <td style="text-align:center;">
                                                        <button type="button" class="btn btn-info btn-sm detalhes" data-id="<?= $row_rel['id_clt'] ?>"><i class="fa fa-info-circle"></i></button>
                                                        <div id="dados-<?= $row_rel['id_clt'] ?>" class="hidden">
                                                            <h4>Dados do CLT</h4>
                                                            <p><span class="quadrado warning"></span> Dados em branco.</p>
                                                            <table class="table table-striped table-hover">
                                                                <tbody>
                                                                    <tr class="<?= testeEmpt($row_rel['id_clt']) ?>">
                                                                        <td><strong>#</strong></td>
                                                                        <td><?= $row_rel['id_clt'] ?></td>
                                                                    </tr>
                                                                    <tr class="<?= testeEmpt($row_rel['nome']) ?>">
                                                                        <td><strong>Nome:</strong></td>
                                                                        <td><?= $row_rel['nome'] ?></td>
                                                                    </tr>
                                                                    <tr class="<?= testeEmpt($row_rel['data_nasci_f']) ?>">
                                                                        <td><strong>Data de Nascimento:</strong></td>
                                                                        <td><?= $row_rel['data_nasci_f'] ?></td>
                                                                    </tr>
                                                                    <tr class="<?= testeEmpt($row_rel['mae']) ?>">
                                                                        <td><strong>Nome da mãe:</strong></td>
                                                                        <td><?= $row_rel['mae'] ?></td>
                                                                    </tr>
                                                                    <tr class="<?= testeEmpt($row_rel['pai']) ?>">
                                                                        <td><strong>Nome do Pai:</strong></td>
                                                                        <td><?= $row_rel['pai'] ?></td>
                                                                    </tr>
                                                                    <tr class="<?= testeEmpt($row_rel['cod_municipio']) ?>">
                                                                        <td><strong>Municício de nascimento (código):</strong></td>
                                                                        <td><?= $row_rel['cod_municipio'] ?></td>
                                                                    </tr>
                                                                    <tr class="<?= testeEmpt($row_rel['cod_nascionalidade']) ?>">
                                                                        <td><strong>Nacionalidade (Código):</strong></td>
                                                                        <td><?= $row_rel['cod_nascionalidade'] ?></td>
                                                                    </tr>
                                                                    <tr class="<?= testeEmpt($row_rel['detalhamento_nascionalidade']) ?>">
                                                                        <td><strong>Detalhamento Nacionalidade:</strong></td>
                                                                        <td><?= $row_rel['detalhamento_nascionalidade'] ?></td>
                                                                    </tr>
                                                                    <tr class="<?= testeEmpt($row_rel['cpf']) ?>">
                                                                        <td><strong>CPF:</strong></td>
                                                                        <td><?= $row_rel['cpf'] ?></td>
                                                                    </tr>
                                                                    <tr class="<?= testeEmpt($row_rel['ctps']) ?>">
                                                                        <td><strong>CTPS:</strong></td>
                                                                        <td><?= $row_rel['ctps'] ?></td>
                                                                    </tr>
                                                                    <tr class="<?= testeEmpt($row_rel['serie_ctps']) ?>">
                                                                        <td><strong>Série CTPS:</strong></td>
                                                                        <td><?= $row_rel['serie_ctps'] ?></td>
                                                                    </tr>
                                                                    <tr class="<?= testeEmpt($row_rel['uf_ctps']) ?>">
                                                                        <td><strong>UF CTPS:</strong></td>
                                                                        <td><?= $row_rel['uf_ctps'] ?></td>
                                                                    </tr>
                                                                    <tr class="<?= testeEmpt($row_rel['data_ctps']) ?>">
                                                                        <td><strong>Data CTPS:</strong></td>
                                                                        <td><?= $row_rel['data_ctps'] ?></td>
                                                                    </tr>
                                                                    <tr class="<?= testeEmpt($row_rel['cep']) ?>">
                                                                        <td><strong>CEP</strong></td>
                                                                        <td><?= $row_rel['cep'] ?></td>
                                                                    </tr>
                                                                    <tr class="<?= testeEmpt($row_rel['endereco']) ?>">
                                                                        <td><strong>Endereço</strong></td>
                                                                        <td><?= $row_rel['endereco'] ?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><strong>Complemento:</strong></td>
                                                                        <td><?= $row_rel['complemento'] ?></td>
                                                                    </tr>
                                                                    <tr class="<?= testeEmpt($row_rel['numero']) ?>">
                                                                        <td><strong>Num.</strong></td>
                                                                        <td><?= $row_rel['numero'] ?></td>
                                                                    </tr>
                                                                    <tr class="<?= testeEmpt($row_rel['bairro']) ?>">
                                                                        <td><strong>Bairro:</strong></td>
                                                                        <td><?= $row_rel['bairro'] ?></td>
                                                                    </tr>
                                                                    <tr class="<?= testeEmpt($row_rel['cod_municipio_end']) ?>">
                                                                        <td><strong>Município (código):</strong></td>
                                                                        <td><?= $row_rel['cod_municipio_end'] ?></td>
                                                                    </tr>
                                                                    <tr class="<?= testeEmpt($row_rel['cnpj']) ?>">
                                                                        <td><strong>CNPJ da Empresa:</strong></td>
                                                                        <td><?= $row_rel['cnpj'] ?></td>
                                                                    </tr>
                                                                    <tr class="<?= testeEmpt($row_rel['data_entrada_f']) ?>">
                                                                        <td><strong>Data de Entrada na Empresa:</strong></td>
                                                                        <td><?= $row_rel['data_entrada_f'] ?></td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </td>
                                                </tr>                                
                                                <?php
                                            }
                                        }
                                        ?>
                                    </tbody>
                                </table>
                                <br>
                                <a href="javascript:;" onclick="$('#tbRelatorioNegado').toggle();" style="text-align: right; display: block;">
                                    <?php
                                    if (count($arr_negados) > 0) {
                                        echo count($arr_negados);
                                        ?> registro(s) fora da listagem por problemas de cadastros ou excluídos da listagem. clique para exibir / ocultar.</a><br>
                                    <table id="tbRelatorioNegado" class="table table-striped table-hover valign-middle" style="page-break-after: auto; display: none; font-size: 14px;"> 
                                        <thead>
                                            <tr>
                                                <th colspan="9">Exclusos</th>
                                            </tr>
                                            <tr>
                                                <th>ID</th>
                                                <th>NOME</th>
                                                <th>PROJETO</th>
                                                <th>PIS INVÁLIDO</th>
                                                <th>FUNÇÃO</th>
                                                <th>STATUS</th>
                                                <th>DATA DE ADMISSÃO</th>   
                                                <th>INCLUIR NA LISTAGEM</th>  
                                                <th>DETALHES</th>   
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            foreach ($arr_negados as $row_rel) {

                                                $class = ($cont++ % 2 == 0) ? "even" : "odd";

                                                $class = (in_array($row_rel['id_clt'], $ids_error)) ? 'warning' : 'success';
                                                ?>
                                                <tr class="<?php echo $class ?>">
                                                    <td><?php echo $row_rel['id_clt'] ?></td>
                                                    <td><?php echo $row_rel['nome'] ?></td>
                                                    <td><?php echo $row_rel['unidade'] ?></td>
                                                    <td class="center"><?php echo $row_rel['pis'] ?></td>
                                                    <td> <?php echo $row_rel['funcao']; ?></td>
                                                    <td> <?php echo $row_rel['especifica']; ?></td>
                                                    <td align="center"><?php echo $row_rel['data_entrada_br']; ?></td>                       
                                                    <td align="center">
                                                        <a href="javascript:;" class="btn btn-default add_lista" data-id="<?= $row_rel['id_clt'] ?>">
                                                            <i class="fa fa-arrow-right text-success"></i>
                                                        </a>
                                                    </td>
                                                    <td style="text-align:center;">
                                                        <button type="button" class="btn btn-info detalhes" data-id="<?= $row_rel['id_clt'] ?>"><i class="fa fa-info-circle"></i></button>
                                                        <div id="dados-<?= $row_rel['id_clt'] ?>" class="hidden">
                                                            <h4>Dados do CLT</h4>
                                                            <p><span class="quadrado warning"></span> Dados em branco.</p>
                                                            <table class="table table-striped table-hover" style="font-size: 14px;">
                                                                <tbody>
                                                                    <tr class="<?= testeEmpt($row_rel['id_clt']) ?>">
                                                                        <td><strong>#</strong></td>
                                                                        <td><?= $row_rel['id_clt'] ?></td>
                                                                    </tr>
                                                                    <tr class="<?= testeEmpt($row_rel['nome']) ?>">
                                                                        <td><strong>Nome:</strong></td>
                                                                        <td><?= $row_rel['nome'] ?></td>
                                                                    </tr>
                                                                    <tr class="<?= testeEmpt($row_rel['data_nasci_f']) ?>">
                                                                        <td><strong>Data de Nascimento:</strong></td>
                                                                        <td><?= $row_rel['data_nasci_f'] ?></td>
                                                                    </tr>
                                                                    <tr class="<?= testeEmpt($row_rel['mae']) ?>">
                                                                        <td><strong>Nome da mãe:</strong></td>
                                                                        <td><?= $row_rel['mae'] ?></td>
                                                                    </tr>
                                                                    <tr class="<?= testeEmpt($row_rel['pai']) ?>">
                                                                        <td><strong>Nome do Pai:</strong></td>
                                                                        <td><?= $row_rel['pai'] ?></td>
                                                                    </tr>
                                                                    <tr class="<?= testeEmpt($row_rel['cod_municipio']) ?>">
                                                                        <td><strong>Municício de nascimento (código):</strong></td>
                                                                        <td><?= $row_rel['cod_municipio'] ?></td>
                                                                    </tr>
                                                                    <tr class="<?= testeEmpt($row_rel['cod_nascionalidade']) ?>">
                                                                        <td><strong>Nacionalidade (Código):</strong></td>
                                                                        <td><?= $row_rel['cod_nascionalidade'] ?></td>
                                                                    </tr>
                                                                    <tr class="<?= testeEmpt($row_rel['detalhamento_nascionalidade']) ?>">
                                                                        <td><strong>Detalhamento Nacionalidade:</strong></td>
                                                                        <td><?= $row_rel['detalhamento_nascionalidade'] ?></td>
                                                                    </tr>
                                                                    <tr class="<?= testeEmpt($row_rel['cpf']) ?>">
                                                                        <td><strong>CPF:</strong></td>
                                                                        <td><?= $row_rel['cpf'] ?></td>
                                                                    </tr>
                                                                    <tr class="<?= testeEmpt($row_rel['ctps']) ?>">
                                                                        <td><strong>CTPS:</strong></td>
                                                                        <td><?= $row_rel['ctps'] ?></td>
                                                                    </tr>
                                                                    <tr class="<?= testeEmpt($row_rel['serie_ctps']) ?>">
                                                                        <td><strong>Série CTPS:</strong></td>
                                                                        <td><?= $row_rel['serie_ctps'] ?></td>
                                                                    </tr>
                                                                    <tr class="<?= testeEmpt($row_rel['uf_ctps']) ?>">
                                                                        <td><strong>UF CTPS:</strong></td>
                                                                        <td><?= $row_rel['uf_ctps'] ?></td>
                                                                    </tr>
                                                                    <tr class="<?= testeEmpt($row_rel['data_ctps']) ?>">
                                                                        <td><strong>Data CTPS:</strong></td>
                                                                        <td><?= $row_rel['data_ctps'] ?></td>
                                                                    </tr>
                                                                    <tr class="<?= testeEmpt($row_rel['cep']) ?>">
                                                                        <td><strong>CEP</strong></td>
                                                                        <td><?= $row_rel['cep'] ?></td>
                                                                    </tr>
                                                                    <tr class="<?= testeEmpt($row_rel['endereco']) ?>">
                                                                        <td><strong>Endereço</strong></td>
                                                                        <td><?= $row_rel['endereco'] ?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><strong>Complemento:</strong></td>
                                                                        <td><?= $row_rel['complemento'] ?></td>
                                                                    </tr>
                                                                    <tr class="<?= testeEmpt($row_rel['numero']) ?>">
                                                                        <td><strong>Num.</strong></td>
                                                                        <td><?= $row_rel['numero'] ?></td>
                                                                    </tr>
                                                                    <tr class="<?= testeEmpt($row_rel['bairro']) ?>">
                                                                        <td><strong>Bairro:</strong></td>
                                                                        <td><?= $row_rel['bairro'] ?></td>
                                                                    </tr>
                                                                    <tr class="<?= testeEmpt($row_rel['cod_municipio_end']) ?>">
                                                                        <td><strong>Município (código):</strong></td>
                                                                        <td><?= $row_rel['cod_municipio_end'] ?></td>
                                                                    </tr>
                                                                    <tr class="<?= testeEmpt($row_rel['cnpj']) ?>">
                                                                        <td><strong>CNPJ da Empresa:</strong></td>
                                                                        <td><?= $row_rel['cnpj'] ?></td>
                                                                    </tr>
                                                                    <tr class="<?= testeEmpt($row_rel['data_entrada_f']) ?>">
                                                                        <td><strong>Data de Entrada na Empresa:</strong></td>
                                                                        <td><?= $row_rel['data_entrada_f'] ?></td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </td>
                                                </tr>
            <?php } ?>
                                        </tbody>
                                    </table>
        <?php } ?>

                                <div style="text-align: right; margin-top: 20px;">
                                    <input type="hidden" name="post_regiao" id="post_regiao" value="<?= $post_regiao; ?>">
                                    <input type="hidden" name="post_regiao" id="post_projeto" value="<?= $post_projeto; ?>">
                                    <input type="button" name="gerar_arquivo" value="Gerar Arquivo" id="gerar_arquivo" class="btn btn-primary">
                                </div>

    <?php } else { ?>
                                <div class="alert alert-warning">
                                    <p>0 registros encontrados.</p>
                                </div>
    <?php } ?>


<?php } ?>
                    </form>
                </div>
            </div>
<?php include_once '../../template/footer.php'; ?>
        </div>

        <script>
            function setCookie(cname, cvalue, exdays) {
                var d = new Date();
                d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
                var expires = "expires=" + d.toUTCString();
                document.cookie = cname + "=" + cvalue + "; " + expires;
            }

            function getCookie(cname) {
                var name = cname + "=";
                var ca = document.cookie.split(';');
                for (var i = 0; i < ca.length; i++) {
                    var c = ca[i];
                    while (c.charAt(0) == ' ')
                        c = c.substring(1);
                    if (c.indexOf(name) == 0)
                        return c.substring(name.length, c.length);
                }
                return "";
            }

            function change(id) {
                $.post(window.location, {'id_clt': id, 'acao': 'gravar_id'}, function (data) {
                    if (data.status == 1) {
                        location.reload();
                    }
                }, 'json');
            }
            console.log('teste2');
            $(document).ready(function () {
                var nome_cookie = 'idscltpis';
                $('body').on('click', '.remove_lista', function () {
                    var $this = $(this);
                    var id = $this.data('id');
                    var val_cookie = getCookie(nome_cookie);
                    setCookie(nome_cookie, val_cookie + ',' + id, 9999);
                    location.reload();
                });

                $('body').on('click', '.add_lista', function () {
                    var $this = $(this);
                    var id = $this.data('id');
                    var val_cookie = getCookie(nome_cookie);
                    var arr_cookie = val_cookie.split(',');
                    var index = arr_cookie.indexOf(id);
                    var new_cookie = '';
                    console.log(index);
                    console.log(id);
                    console.log(index);
                    $.each(arr_cookie, function (i, v) {
                        console.log(typeof parseInt(v));
                        console.log(typeof id);
                        if (id != parseInt(v)) {
                            console.log('entrou');
                            new_cookie += ',' + v;
                        }
                    });
val_cookie = arr_cookie.join();
                    setCookie(nome_cookie, new_cookie, 9999);
                    location.reload();
                });

            });

        </script>
    </body>
</html>