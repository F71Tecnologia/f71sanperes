<?php
if (empty($_COOKIE['logado'])) {
    print "Efetue o Login<br><a href='../login.php'>Logar</a>";
    exit;
}

include('../conn.php');
include('../classes/global.php');
include('../wfunction.php');

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

if (isset($_REQUEST['method']) && !empty($_REQUEST['method']) && $_REQUEST['method'] == "carregaCargos") {

    $projSelect = $_REQUEST['proj'];

    $rs = mysql_query("SELECT * FROM curso WHERE campo3 IN({$projSelect}) ORDER BY nome");
    $cargo = utf8_encode("<option value=\"-1\">« Todos »</option>");
    while ($row = mysql_fetch_assoc($rs)) {
        $cargo .= "<option value=\"{$row['id_curso']}\">{$row['id_curso']} - " . utf8_encode($row['nome']) . "</option>";
    }

    echo $cargo;
    exit;
}

$id_user = $_COOKIE['logado'];
$result_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($result_user);
$result_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_user[id_master]'");
$row_master = mysql_fetch_array($result_master);

/* CARREGA TODOS OS PROJETOS */
$projetos = array();
$projeto = mysql_query("SELECT * FROM projeto WHERE id_regiao = {$row_user['id_regiao']} ORDER BY nome");
$projetos[' '] = "<< Selecione >>";
while ($linha = mysql_fetch_assoc($projeto)) {
    $projetos[$linha['id_projeto']] = $linha['nome'];
}


$meses = mesesArray(null);
//$ano            = array("2013" => 2013, "2014" => 2014);
$ano = anosArray(null, null, array('' => "<< Ano >>"));
$cargo = array("-1" => "Aguardando Projeto");
$status = array("todos" => "Todos", "admitido" => "Admitido", "demitido" => "Demitido");
$mesSelectI = (isset($_REQUEST['mesI'])) ? $_REQUEST['mesI'] : null;
$mesSelectF = (isset($_REQUEST['mesF'])) ? $_REQUEST['mesF'] : null;
$anoSelectI = (isset($_REQUEST['anoI'])) ? $_REQUEST['anoI'] : null;
$anoSelectF = (isset($_REQUEST['anoF'])) ? $_REQUEST['anoF'] : null;
$statusSelect = (isset($_REQUEST['status'])) ? $_REQUEST['status'] : null;
$inicio = $_REQUEST['anoI'] . "-" . sprintf("%02d", $_REQUEST['mesI']) . "-" . "01";
$final = $_REQUEST['anoF'] . "-" . sprintf("%02d", $_REQUEST['mesF']) . "-" . "31";
$statusSelect = (isset($_REQUEST['status'])) ? $_REQUEST['status'] : null;
$projSelect = (isset($_REQUEST['proj']) ? $_REQUEST['proj'] : null);
$cargoSelect = (isset($_REQUEST['cargo']) ? $_REQUEST['cargo'] : null);

if (isset($_REQUEST['filtrar']) && !empty($_REQUEST['filtrar'])) {
    $filtro = true;
    //TRATANDO STATUS DO CLT
    if ($statusSelect == "todos") {
        $criteria = "";
        $having = "";
    } else if ($statusSelect == "admitido") {
        $criteria = " AND (A.status < 60 OR A.status = 200) ";
        $having = "HAVING statusColaborador = '{$statusSelect}'";
    } else {
        $criteria = " AND (A.status >= 60 AND A.status != '200') ";
        $having = "HAVING statusColaborador = '{$statusSelect}'";
    }

    //TRATANDO CURSO
    if ($cargoSelect[0] == -1) {
        $criteria_curso = "";
    } else {
        $criteria_curso = "AND B.id_curso IN (" . implode(',', $cargoSelect) . ")";
    }

    $qrTec = "
    SELECT * 
    FROM
        (SELECT
            A.nome, 
            DATE_FORMAT(A.data_entrada, '%d/%m/%Y') as data_entrada, 
            DATE_FORMAT(A.data_saida, '%d/%m/%Y') as data_saida,
            A.status,
            B.campo2,
            IF(A.data_saida BETWEEN '{$inicio}' AND '{$final}','demitido','admitido') as statusColaborador
        FROM 
            rh_clt A 
            LEFT JOIN curso B on (B.id_curso = A.id_curso)
        WHERE ((A.data_entrada BETWEEN '{$inicio}' AND '{$final}') OR (A.data_saida BETWEEN '{$inicio}' AND '{$final}'))
            AND A.id_projeto IN($projSelect)
            AND A.tipo_contratacao = '2' 
            $criteria_curso
            $criteria 
        ORDER BY A.nome) AS tmp 
    $having";

    echo "<!--" . $qrTec . "-->";
    $qr = mysql_query($qrTec);
    $num_rows = mysql_num_rows($qr);
}
?>

<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Relatório de Exportação Técnica</title>

        <link href="../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">

    </head>
    <body>
<?php include("../template/navbar_default.php"); ?>      
        <div class="container">
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - Recursos Humanos <small> - Exportação Técnica</small></h2></div>
            <form action="" method="post" class="form-horizontal top-margin1" name="form" id="form">  
                <div class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print">Relatório</div>
                    <div class="panel-body">
                        <div class="form-group" > 

                            <label for="select" class="col-sm-3 control-label hidden-print">Mês/Ano início</label>
                            <div class="col-sm-3">
                                <?php echo montaSelect($meses, $mesSelectI, "id='mesI' name='mesI' class='validate[required] form-control'") ?><span class="loader"></span>
                            </div>
                            <div class="col-sm-3">
                                <?php echo montaSelect($ano, $anoSelectI, "id='anoI' name='anoI' class='validate[required] form-control'") ?><span class="loader"></span>
                            </div>
                        </div>

                        <div class="form-group" >
                            <label for="select" class="col-sm-3 control-label hidden-print">Mês/Ano Fim</label>
                            <div class="col-sm-3">
                                <?php echo montaSelect($meses, $mesSelectF, "id='mesF' name='mesF' class='validate[required] form-control'") ?> <span class="loader"></span> 
                            </div>
                            <div class="col-sm-3">
                                <?php echo montaSelect($ano, $anoSelectF, "id='anoF' name='anoF' class='validate[required] form-control'") ?> <span class="loader"></span> 
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="select" class="col-sm-3 control-label hidden-print">Projeto</label>
                            <div class="col-sm-3">
                                <?php echo montaSelect($projetos, $projSelect, "id='proj' name='proj' class='validate[required] form-control'") ?><span class="loader"></span>
                            </div>

                            <label for="select" class="col-sm-1 control-label hidden-print">Status</label>
                            <div class="col-sm-2">
                                <?php echo montaSelect($status, $statusSelect, "id='status' name='status' class='validate[required] form-control'") ?><span class="loader"></span>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="select" class="col-sm-3 control-label hidden-print">Cargo</label>
                            <div class="col-sm-6">
                                <?php echo montaSelect($cargo, $cargoSelect, "id='cargo' name='cargo[]' class='validate[required] form-control' multiple style='height: 150px;'") ?><span class="loader"></span>
                            </div>
                        </div>
                    </div>

                        <div class="panel-footer text-right hidden-print controls">
                            <?php if(isset($filtro)) { ?>
                                <button type="button" onclick="tableToExcel('tabela', 'Exportação Técnica')" value="Exportar para Excel" class="btn btn-success" ><span class="fa fa-file-excel-o"></span> Exportar para Excel</button>
                            <?php } ?>
                                <button type="submit" name="filtrar" id="filtrar" value="filtrar" class="btn btn-primary"><span class="fa fa-filter"></span> Filtrar</button>
                        </div>
                    </div>

                    <?php
                    if ($filtro) {
                        if ($num_rows > 0) {
                            $count = 0;
                            ?>
                            <br/>
                            <table class="table table-striped table-condensed table-bordered text-sm valign-middle" id="tbRelatorio">
                                <thead>
                                    <tr>
                                        <th colspan="5"><?= $projetos[$projSelect] ?></th>
                                    </tr>
                                    <tr>
                                        <th>Nome</th>
                                        <th>Cargo</th>
                                        <th>Data Admissão</th>
                                        <th>Data Demissão</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = mysql_fetch_assoc($qr)) { ?>
                                        <tr <?php echo ($count++ % 2 == 0) ? "odd" : "even" ?>>
                                            <td><?php echo RemoveAcentos($row['nome']); ?></td>
                                            <td> <?php echo RemoveAcentos($row['campo2']); ?></td>
                                            <td> <?php echo $row['data_entrada']; ?></td>
                                            <td> <?php echo ($row['data_saida']); ?></td>
                                            <td> <?php echo $row['statusColaborador']; ?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                                <tfoot>
                                    <tr class="<?php echo ($count++ % 2 == 0) ? "odd" : "even" ?>">
                                        <th align="center" colspan="2">Total</th>
                                        <th align="center" colspan="3"><?= $num_rows ?></th>
                                    </tr>
                                </tfoot>
                            </table>
                            <?php } else { ?>
                            <br/>
                            <div id='message-box' class='alert alert-warning'>
                                <span class="fa fa-"></span> <p>Nenhum registro encontrado</p>
                            </div>
                            <?php
                        }
                    }
                    ?>
                
            </form>
            <?php include('../template/footer.php'); ?>
            <div class="clear"></div>
        </div>

        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/tooltip.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/global.js"></script>

        <script>
                $(function () {
                    console.log($('#proj').val());
                    $('#proj').ajaxGetJson("expTecnica.php", {method: "carregaCargos"}, null, "cargo");
                });
        </script>

    </body>
</html>
