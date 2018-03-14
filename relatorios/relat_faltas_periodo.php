<?php
//error_reporting(E_ALL);

if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../login.php?entre=false';</script>";
}
 
include("../conn.php");
include("../wfunction.php");
include("../classes/BotoesClass.php");
include("../classes/BancoClass.php");
include("../classes/global.php");

$usuario = carregaUsuario();
$id_regiao = $usuario['id_regiao'];

$botoes = new BotoesClass("../img_menu_principal/");
$icon = $botoes->iconsModulos;

$projeto = ($_REQUEST['projeto'] > 0) ? $_REQUEST['projeto'] : null;
$data_ini = (!empty($_REQUEST['data_ini'])) ? $_REQUEST['data_ini'] : "01/".date('m/Y');
$data_fim = (!empty($_REQUEST['data_fim'])) ? $_REQUEST['data_fim'] : date('t', date('m-Y')."-01").date('/m/Y');

$sql = mysql_query("SELECT *
                FROM projeto
                WHERE id_master = {$usuario['id_master']} AND status_reg = '1'");
$projetos = array("-1" => "� Selecione �");
while ($rst = mysql_fetch_assoc($sql)) {
    $projetos[$rst['id_projeto']] = $rst['id_projeto'] . " - " . $rst['nome'];
}

if(isset($_REQUEST['data_ini']) && isset($_REQUEST['data_fim']) && isset($_REQUEST['projeto']) && isset($_REQUEST['filtrar']) && $_REQUEST['projeto'] >  0) {
    
    $nomeProjeto = mysql_result(mysql_query("SELECT nome FROM projeto WHERE id_projeto = $projeto LIMIT 1;"),0);
    
    $data_ini_bd = explode('/', $data_ini);
    $data_ini_bd = $data_ini_bd[2].$data_ini_bd[1];
    $data_fim_bd = explode('/', $data_fim);
    $data_fim_bd = $data_fim_bd[2].$data_fim_bd[1];
    $sql = "
    SELECT A.nome, C.*, B.nome nomeCurso
    FROM rh_clt A 
    INNER JOIN curso B ON (A.id_curso = B.id_curso)
    INNER JOIN (SELECT id_clt, cod_movimento, nome_movimento, tipo_qnt, TIME(SUM(qnt_horas)) qnt_horas, SUM(qnt) qnt_dias  FROM rh_movimentos_clt WHERE cod_movimento IN (8000, 50249) AND CONCAT(ano_mov,LPAD(mes_mov,'2','0')) BETWEEN $data_ini_bd AND $data_fim_bd AND status != 0 GROUP BY id_clt, cod_movimento) C ON (A.id_clt = C.id_clt)
    WHERE A.id_projeto = $projeto
    ORDER BY B.nome, A.nome";
    $qry = mysql_query($sql);
    while ($row = mysql_fetch_array($qry)) {
        $array[$row['nomeCurso']][$row['nome']][$row['cod_movimento']][($row['tipo_qnt'] == 1) ? 1 : 2] = ($row['tipo_qnt'] == 1) ? $row['qnt_horas'] : $row['qnt_dias'];
    }
    echo "<!-- $sql -->";
//    print_array($array);
}

$nome_pagina = "Relat�rio de Faltas por Per�odo";
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__);
$breadcrumb_config = array("nivel" => "../", "key_btn" => "3", "area" => "RECURSOS HUMANOS", "ativo" => $nome_pagina, "id_form" => "frmplanodeconta"); ?>

<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: <?= $nome_pagina ?></title>
        <link rel="shortcut icon" href="../favicon.png">
        <!-- Bootstrap -->        
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css">
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../resources/css/bootstrap-note.css" rel="stylesheet" media="all">
        <link href="../resources/css/bootstrap-dialog.min.css" rel="stylesheet" media="all">
        <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="all">
        <link href="../resources/css/ui-autocomplete-theme.css" rel="stylesheet" media="all">
        <link href="../resources/css/main.css" rel="stylesheet" media="all">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="all">
    </head>
    <body>
        <?php include("../template/navbar_default.php"); ?> 
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-header box-rh-header hidden-print">
                        <h2><?php echo $icon['3'] ?> - RECURSOS HUMANOS <small>- <?= $nome_pagina ?></small></h2>
                    </div>
                    <form action="" method="post" name="form_lote" id="form_lote" class="form-horizontal top-margin hidden-print" enctype="multipart/form-data">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <div class="form-group">
                                    <label for="projeto1" class="col-sm-2 control-label">Projeto</label>
                                    <div class="col-sm-4"><?= montaSelect($projetos, $projeto, "id='projeto' name='projeto' class='form-control validate[required,custom[select]]'") ?></div>
                                    <label for="" class="col-sm-1 control-label">Per�odo</label>
                                    <div class="col-sm-4">
                                        <div class="input-group">
                                            <input type="text" id='data_ini' name='data_ini' class='text-center data validate[required,custom[select]] form-control' value="<?= $data_ini ?>">
                                            <div class="input-group-addon">at�</div>
                                            <input type="text" id='data_fim' name='data_fim' class='text-center data validate[required,custom[select]] form-control' value="<?= $data_fim ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-footer text-right">
                                <?php if(count($array) > 0){ ?><button type="button" onclick="tableToExcel('tbRelatorio', 'Faltas')" value="Exportar Excel" class="btn btn-success"><i class="fa fa-file-excel-o"></i> Exportar Excel</button><?php } ?>
                                <button type="submit" id="criar" name="filtrar" value="Filtrar" class="btn btn-primary"><i class="fa fa-filter"></i> Filtrar</button>
                            </div>
                        </div>
                    </form>
                    <?php if(isset($_REQUEST['data_ini']) && isset($_REQUEST['data_fim']) && isset($_REQUEST['projeto']) && isset($_REQUEST['filtrar']) && $_REQUEST['projeto'] >  0) { ?>
                    <table id="tbRelatorio" class="table table-condensed table-hover text-sm valign-middle">
                        <thead>
                            <tr>
                                <td colspan="4" class="text-center text-bold"><?= $nomeProjeto ?></td>
                            </tr>
                            <tr>
                                <td class="text-center text-bold">Nome</td>
                                <td class="text-center text-bold">Faltas (Horas)</td>
                                <td class="text-center text-bold">Faltas (Dias)</td>
                                <!--<td class="text-center text-bold">Total</td>-->
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($array as $funcao => $array_funcionarios) { 
                                $total1 = $total2 = 0; ?>
                                <tr class="info">
                                    <td colspan="4" class="text-left text-bold"><?= $funcao ?></td>
                                </tr>
                                <?php foreach ($array_funcionarios as $nome => $value) { 
                                    $total1 += $value[8000][1] + $value[50249][1];
                                    $total2 += $value[8000][2] + $value[50249][2];
                                    //$total_50300 += $value[50300]; ?>
                                    <tr class="">
                                        <td class=""><?= $nome ?></td>
                                        <td class="text-center"><?= $value[8000][1] + $value[50249][1] ?></td>
                                        <td class="text-center"><?= $value[8000][2] + $value[50249][2] ?></td>
                                        <!--<td class="text-center"><?= $value[8000][1] + $value[50249][2] ?></td>-->
                                    </tr>
                                <?php } ?>
                                <tr class="active">
                                    <td class="text-right text-bold">TOTAL:</td>
                                    <td class="text-center text-bold"><?= $total1 ?></td>
                                    <td class="text-center text-bold"><?= $total2 ?></td>
                                </tr>
                                <tr>
                                    <td colspan="4">&nbsp;</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                    <?php } else { ?>
                    <div class="alert alert-danger">
                        <span class="fa fa-exclamation-triangle"></span> Nenhuma informa��o encontrada neste filtro!
                    </div>
                    <?php } ?>
                </div>
            </div>
            <?php include_once '../template/footer.php'; ?>
        </div>
        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../js/jquery.maskMoney_3.0.2.js"></script>
        <script src="../js/jquery.form.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/global.js"></script>
        <script src="../js/jquery.maskedinput-1.3.1.js"></script>
        <script src="../resources/js/financeiro/saida.js"></script>
        <script src="../js/jquery.validationEngine-2.6.js"></script>
        <script src="../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../js/jquery.form.js" type="text/javascript"></script>
        <script src="../resources/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
        <script>
        $(function(){
            $('form').validationEngine();
        })
        </script>
    </body>
</html>