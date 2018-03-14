<?php
if (empty($_COOKIE['logado'])) {
    print "Efetue o Login<br><a href='../login.php'>Logar</a>";
    exit;
}

include "../conn.php";
include "../classes/funcionario.php";
include '../classes_permissoes/regioes.class.php';
include "../wfunction.php";
include "../classes_permissoes/acoes.class.php";

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$optRegiao = getRegioes();
$ACOES = new Acoes();


$regiao = $usuario['id_regiao'];

$result_regiao = mysql_query("SELECT * FROM `regioes` WHERE `id_regiao` = '{$regiao}'");
$row_regiao = mysql_fetch_array($result_regiao);

if ((isset($_REQUEST['filtrar']) && !empty($_REQUEST['filtrar'])) || isset($_REQUEST['todos_projetos'])) {
    $filtro = true;

    $projeto = $_REQUEST['projeto'];

    $filtroData = ''; // define a variável filtroData como vazia
    $data_ini = '';
    $data_fim = '';
    if (isset($_REQUEST['from']) && !empty($_REQUEST['from'])) {
        $data_ini_periodo = converteData($_REQUEST['from']);
        $filtroData = " and data_ini >= '{$data_ini_periodo}'"; // altera filtroData para tadas maiores q data inicio
        $data_ini = $_REQUEST['from'];
    }
    if (isset($_REQUEST['to']) && !empty($_REQUEST['to'])) {
        $data_fim_periodo = converteData($_REQUEST['to']);
        $filtroData = " and  data_fim <= '$data_fim_periodo'"; // altera filtro data para datas menores q data fim
        $data_fim = $_REQUEST['to'];
    }

    if (isset($_REQUEST['from']) && isset($_REQUEST['to']) && !empty($_REQUEST['from']) && !empty($_REQUEST['to'])) {
        $filtroData = " and (f.data_ini between '{$data_ini_periodo}' and '{$data_fim_periodo}' /*or f.data_fim between '{$data_ini_periodo}' and '$data_fim_periodo'*/)"; // verifica datas entre o periodo do filtro
    }

    $sql = "SELECT f.id_clt, f.nome, DATE_FORMAT(f.data_ini,'%d/%m/%Y') as data_ini, DATE_FORMAT(f.data_fim,'%d/%m/%Y') as data_fim,p.nome as projeto, f.ir, f.total_liquido, 
            c.banco,c.agencia,c.conta,ba.nome AS banco_nome,cu.nome AS curso_nome,c.cpf,f.pensao_alimenticia
            FROM rh_ferias AS f 
            INNER JOIN projeto AS p ON (p.id_projeto = f.projeto) 
            INNER JOIN rh_clt AS c ON f.id_clt = c.id_clt
            INNER JOIN curso AS cu ON c.id_curso = cu.id_curso
            LEFT JOIN bancos AS ba ON c.banco = ba.id_banco
            WHERE f.regiao = '{$regiao}' ";
    if (!isset($_REQUEST['todos_projetos'])) {
        $sql .= "AND f.projeto = '{$projeto}' ";
    }

    $sql .= "AND f.status = 1 AND f.nome != '' {$filtroData} ORDER BY p.nome,f.nome";

    echo "<!--$sql-->";

    $result = mysql_query($sql) or die(mysql_error());
    $num_rows = mysql_num_rows($result);
}

/* VARIAVEIS PARA MANTER OS CAMPOS DO FORMULÁRIO SELECIONADO */
$projetoR = (isset($projeto)) ? $projeto : null;
$regiaoR = (isset($regiao)) ? $regiao : null;

$projetoR = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : $projetoR;
$regiaoR = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $regiaoR;

$projetosOp = array("-1" => "« Selecione »");
$query = "SELECT id_projeto,nome FROM projeto WHERE id_regiao = '$regiao'";
$result1 = mysql_query($query) or die(mysql_error());
while ($row = mysql_fetch_assoc($result1)) {
    $projetosOp[$row['id_projeto']] = $row['id_projeto'] . " - " . $row['nome'];
}

/*
 * @jacques - 11/11/2015
 * Totalizador do relatório
 */
$total = array(
    'pensao' => array(
        'geral' => 0
    ),
    'ir' => array(
        'linha' => 0,
        'grupo' => 0,
        'geral' => 0
    ),
    'total_liquido' => array(
        'linha' => 0,
        'grupo' => 0,
        'geral' => 0
    )
);
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Relatório de Férias (CLT)</title>

        <link href="../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
    </head>

    <body>
        <?php include("../template/navbar_default.php"); ?>      
        <div class="container">
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small> - Relatório de Férias (CLT)</small></h2></div>
            <form action="" method="post" class="form-horizontal top-margin1" name="form" id="form">  
                <div class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print">Relatório</div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="select" class="col-sm-3 control-label hidden-print" >Região</label>
                            <div class="col-sm-3">
                                <?php echo montaSelect($optRegiao, $regiaoR, array('name' => "regiao", 'id' => 'regiao', 'class' => 'form-control')); ?><span class="loader"></span>
                            </div>

                            <label for="select" class="col-sm-1 control-label hidden-print" >Projeto</label>
                            <div class="col-sm-3">
                                <?php echo montaSelect($projetosOp, $projetoR, array('name' => "projeto", 'id' => 'projeto', 'class' => 'validate[required] form-control')); ?> <span class="loader"></span> 
                            </div>
                        </div>

                        <div class="form-group datas">
                            <label for="data_ini" class="col-sm-3 control-label hidden-print">Período</label>
                            <div class="col-lg-7">
                                <div class="input-daterange input-group" id="bs-datepicker-range">
                                    <span class="input-group-addon ate"><i class="fa fa-calendar"></i></span>
                                    <input type="text" class="input form-control data" name="from" id="from" readonly="true" placeholder="Inicio de Periodo" value="<?php echo $data_ini; ?>">
                                    <span class="input-group-addon ate">até</span>
                                    <input type="text" class="input form-control data" name="to" id="to" readonly="true" placeholder="Fim de Periodo" value="<?php echo $data_fim; ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="panel-footer text-right hidden-print controls">
                        <span class="fleft erro"><?php if ($verifica_dirf != 0) echo 'Arquivo já existente!'; ?></span>
                        <?php if (isset($filtro) || isset($_REQUEST['filtrar'])) { ?>
                            <button type="button" onclick="tableToExcel('tabela', 'Ferias CLT')" value="Exportar para Excel" class="btn btn-success" ><span class="fa fa-file-excel-o"></span> Exportar para Excel</button>
                        <?php } ?>
                        <?php
                        ///permissão para VISUALIZAR TODOS OS PROJETOS AO MESMO TEMPO
                        if ($ACOES->verifica_permissoes(85)) {
                            ?>
                            <button type="submit" name="todos_projetos" value="gerar de todos os projetos" id="todos_projetos" class="btn btn-warning"><span class="fa fa-filter"></span> Filtrar Todos os Projetos</button>
                        <?php } ?>
                        <button type="submit" name="filtrar" id="filtrar" value="filtrar" class="btn btn-primary"><span class="fa fa-filter"></span> Filtrar</button>
                    </div>
                </div>

                <?php
                if ($filtro) {
                    if ($num_rows > 0) {
                        $count = 0;
                        ?>
                        <table class="table table-striped table-hover table-bordered table-condensed text-sm valign-middle" id="tabela">
                            <thead>
                                <tr>
                                    <th>Projeto</th>
                                    <th>Nome</th>
                                    <th>Função</th>
                                    <th>CPF</th>
                                    <th>Banco</th>
                                    <th>Agência</th>
                                    <th>Conta</th>
                                    <th>Início</th>
                                    <th>Fim</th>
                                    <th>Pensão</th>
                                    <th>IRRF</th>
                                    <th>Valor</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $projeto_anterior = ''; ?>
                                <?php while ($row = mysql_fetch_array($result)) { ?>
                                    <?php if ($projeto_anterior != '' && $projeto_anterior != $row['projeto']) { ?>
                                        <tr>
                                            <th colspan="9">TOTAL <?php echo $projeto_anterior; ?> </th>
                                            <th><?php echo number_format($total['pensao']['projeto'], 2, ',', '.'); ?></th>
                                            <th><?php echo number_format($total['ir']['projeto'], 2, ',', '.'); ?></th>
                                            <th><?php echo number_format($total['total_liquido']['projeto'], 2, ',', '.'); ?></th>
                                        </tr>

                                        <?php
                                        $total['pensao']['projeto'] = 0;
                                        $total['ir']['projeto'] = 0;
                                        $total['total_liquido']['projeto'] = 0;
                                    }
                                    ?>

                                    <tr class="<?php echo ($count++ % 2 == 0) ? "odd" : "even"; ?>">
                                        <td><?php echo $row['projeto'] . ' ' . $row['id_projeto']; ?></td>
                                        <td><?php echo $row['nome']; ?></td>
                                        <!--<td><?php echo mysql_result(mysql_query("SELECT A.nome FROM curso A INNER JOIN rh_clt B ON (A.id_curso = B.id_curso) WHERE B.id_clt = {$row['id_clt']} LIMIT 1;"), 0) ?></td>-->
                                        <td><?php echo $row['curso_nome']; ?></td>
                                        <td><?php echo $row['cpf']; ?></td>
                                        <td><?php echo $row['banco_nome']; ?></td>
                                        <td><?php echo $row['agencia']; ?></td>
                                        <td><?php echo $row['conta']; ?></td>
                                        <td><?php echo $row['data_ini']; ?></td>
                                        <td><?php echo $row['data_fim']; ?></td>
                                        <td><?php echo number_format($row['pensao_alimenticia'], 2, ',', '.'); ?></td>
                                        <td><?php echo number_format($row['ir'], 2, ',', '.'); ?></td>
                                        <td><?php echo number_format($row['total_liquido'], 2, ',', '.'); ?></td>
                                    </tr>
                                    <?php
                                    $total['pensao']['geral'] += $row['pensao_alimenticia'];
                                    $total['pensao']['projeto'] += $row['pensao_alimenticia'];

                                    $total['ir']['linha'] = $row['ir'];
                                    $total['ir']['grupo'] += $total['ir']['linha'];
                                    $total['ir']['geral'] += $total['ir']['linha'];
                                    $total['ir']['projeto'] += $total['ir']['linha'];

                                    $total['total_liquido']['linha'] = $row['total_liquido'];
                                    $total['total_liquido']['grupo'] += $total['total_liquido']['linha'];
                                    $total['total_liquido']['geral'] += $total['total_liquido']['linha'];
                                    $total['total_liquido']['projeto'] += $total['total_liquido']['linha'];

                                    $projeto_anterior = $row['projeto'];
                                }
                                ?>
                                <tr>
                                    <th colspan="9">TOTAL <?php echo $projeto_anterior; ?> </th>
                                    <th><?php echo number_format($total['pensao']['projeto'], 2, ',', '.'); ?></th>
                                    <th><?php echo number_format($total['ir']['projeto'], 2, ',', '.'); ?></th>
                                    <th><?php echo number_format($total['total_liquido']['projeto'], 2, ',', '.'); ?></th>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="9"></th>
                                    <th><?php echo number_format($total['pensao']['geral'], 2, ',', '.'); ?></th>
                                    <th><?php echo number_format($total['ir']['geral'], 2, ',', '.'); ?></th>
                                    <th><?php echo number_format($total['total_liquido']['geral'], 2, ',', '.'); ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    <?php } else { ?>

                        <div id='message-box' class='alert alert-warning'>
                            <span class="fa fa-exclamation-triangle"></span> Nenhum registro encontrado
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
                                    $("#regiao").ajaxGetJson("../methods.php", {method: "carregaProjetos"}, null, "projeto");

                                    $(".bt-image").on("click", function () {
                                        var action = $(this).data("type");
                                        var key = $(this).data("key");
                                        var emp = $(this).parents("tr").find("td:first").next().html();

                                        //THICKBOX VISUALIZA DOCUMENTOS
                                        if (action === "docs") {
                                            thickBoxIframe(emp, "actions.php", {prestador: key, method: "getDocs"}, "625-not", "500");
                                        } else if (action === "duplicar") {
                                            $("#prestador").val(key);
                                            $("#form1").attr('action', 'duplicar_prestador.php');
                                            $("#form1").submit();
                                        } else if (action === "prestador") {
                                            $("#prestador").val(key);
                                            $("#form1").attr('action', 'ver_prestador.php');
                                            $("#form1").submit();
                                        } else if (action === "editar") {
                                            $("#prestador").val(key);
                                            $("#form1").attr('action', 'form_prestador.php');
                                            $("#form1").submit();
                                        }
                                    });

                                    $("#novoPrest").click(function () {
                                        $("#form1").attr('action', 'form_prestador.php');
                                        $("#form1").submit();
                                    });

                                    //                $(function() {
                                    //                    $("#from").datepicker({
                                    //                        defaultDate: "+1w",
                                    //                        changeMonth: true,
                                    //                        changeYear: true,
                                    //                        onClose: function(selectedDate) {
                                    //                            $("#to").datepicker("option", "minDate", selectedDate);
                                    //                        }
                                    //                    });
                                    //                    $("#to").datepicker({
                                    //                        defaultDate: "+1w",
                                    //                        changeMonth: true,
                                    //                        changeYear: true,
                                    //                        onClose: function(selectedDate) {
                                    //                            $("#from").datepicker("option", "maxDate", selectedDate);
                                    //                        }
                                    //                    });
                                    //                });
                                });
        </script>

    </body>
</html>
