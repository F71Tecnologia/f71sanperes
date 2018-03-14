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

if (isset($_REQUEST['gerar']) || isset($_REQUEST['todos_projetos'])) {
    $cont = 0;

    $id_regiao = $_REQUEST['regiao'];
    $id_projeto = $_REQUEST['projeto'];
    $ano = $_REQUEST['ano'];
    $mes = $_REQUEST['mes'];
    $id_unidade = $_REQUEST['unidade'];


    $condicao = (!isset($_REQUEST['todos_projetos'])) ? "(b.id_unidade_de = '$id_unidade' or b.id_unidade_para = '$id_unidade') and" : '';

    $projeto = montaQueryFirst("projeto", "nome", array('id_projeto' => $id_projeto));
    $sql = "select a.id_clt,a.nome,b.unidade_de,b.unidade_para,b.motivo, DATE_FORMAT( b.data_proc , '%m/%Y' ) AS `data_proc`, b.id_transferencia,DATE_FORMAT( b.criado_em , '%d/%m/%Y' ) AS `criado_em`,
                (select nome from curso where id_curso = b.id_curso_de) as curso_de,
                (select nome from curso where id_curso = b.id_curso_para) as curso_para,
                c.nome as usuario
                from rh_clt as a
                inner join rh_transferencias as b on (b.id_clt = a.id_clt)
                inner join funcionario as c on (b.id_usuario = c.id_funcionario)
                where $condicao
                MONTH(b.data_proc) = '$mes' and YEAR(b.data_proc) = '$ano' order by b.unidade_de,b.unidade_para,a.nome;";
    echo "<!-- {$sql} -->";
    $qr_relatorio = mysql_query($sql) or die(mysql_error());
    $num_rows = mysql_num_rows($qr_relatorio);
}

$regiaoSel = (isset($_REQUEST['reg'])) ? $_REQUEST['reg'] : $usuario['id_regiao'];
$projetoSel = (isset($_REQUEST['pro'])) ? $_REQUEST['pro'] : null;

$regiaoSel = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $regiaoSel;
$projetoSel = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : $projetoSel;
$unidadeSel = (isset($_REQUEST['unidade'])) ? $_REQUEST['unidade'] : null;
$anoSel = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : null;
$mesSel = (isset($_REQUEST['mes'])) ? $_REQUEST['mes'] : null;

////////////////////////////////////////////////////////////////////////////////
/////////////////////////// array de anos //////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//$arrayAnos[-1] = '« Selecione o Ano »';
//for ($i = date('Y'); $i >= date('Y') - 10; $i--) {
//    $arrayAnos[$i] = $i;
//}

$arrayAnos = anosArray(null, null, array('' => "<< Ano >>"));

/* CARREGA AS FUNÇÕES E UNIDADES VIA AJAX, RETORNA UM JSON */
if (isset($_REQUEST['method']) && !empty($_REQUEST['method']) && $_REQUEST['method'] == "carregafuncao") {
    //UNIDADE
    $qrUnidade = mysql_query("SELECT id_unidade,unidade FROM unidade WHERE campo1 = '{$_REQUEST['projeto']}' ORDER BY unidade");
    $num_rowsU = mysql_num_rows($qrUnidade);
    $unidades = array();
    if ($num_rowsU > 0) {
        $return['stunid'] = 1;
        while ($row = mysql_fetch_assoc($qrUnidade)) {
            $unidades[utf8_encode($row['id_unidade'])] = utf8_encode($row['id_unidade'] . ' - ' . $row['unidade']);
        }
    } else {
        $return['stunid'] = 0;
        $unidades["-1"] = "nenhum curso encontrado";
    }

    $return['unidade'] = $unidades;

    echo json_encode($return);
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Relatório de Transferências Por Unidade</title>

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
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small> - Relatório de Transferências Por Unidade</small></h2></div>
            <form action="" method="post" class="form-horizontal top-margin1" name="form" id="form">  
                <div class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print">Relatório</div>
                    <div class="panel-body">
                        <div class="form-group" >
                            <label for="select" class="col-sm-4 control-label hidden-print" >Região</label>
                            <div class="col-sm-5">
                                <?php echo montaSelect($optRegiao, $regiaoSel, array('name' => "regiao", 'id' => 'regiao', 'class' => 'validate[required] form-control')); ?><span class="loader"></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="select" class="col-sm-4 control-label hidden-print" >Projeto</label>
                            <div class="col-sm-5">
                                <?php echo montaSelect(array("-1" => "« Selecione o Projeto »"), $projetoSel, array('name' => "projeto", 'id' => 'projeto', 'class' => 'validate[required] form-control')); ?> <span class="loader"></span> 
                            </div>
                        </div>

                        <div class="form-group" >
                            <label for="select" class="col-sm-4 control-label hidden-print" >Unidade</label>
                            <div class="col-sm-5">
                                <?php echo montaSelect(array("-1" => "« Selecione o Projeto »"), $unidadeSel, array('name' => "unidade", 'id' => 'unidade', 'class' => 'validate[required] form-control')); ?><span class="loader"></span>
                            </div>
                        </div> 

                        <div class="form-group" >
                            <label for="select" class="col-sm-4 control-label hidden-print" >Período</label>
                            <div class="col-sm-2">
                                <?php echo montaSelect(mesesArray(), $mesSel, array('name' => "mes", 'id' => 'mes', 'class' => 'validate[required,,funcCall[checkDate]] form-control')); ?><span class="loader"></span>
                            </div>    
                            <div class="col-sm-2">
                                <?php echo montaSelect($arrayAnos, $anoSel, array('name' => "ano", 'id' => 'ano', 'class' => 'validate[required,funcCall[checkDate]] form-control')); ?><span class="loader"></span>
                            </div>
                        </div> 
                    </div> 

                        <div class="panel-footer text-right hidden-print controls">
                        <span class="fleft erro"><?php if ($verifica_dirf != 0) echo 'Arquivo já existente!'; ?></span>
                        <?php if (!empty($qr_relatorio) && (isset($_POST['gerar'])) || isset($_REQUEST['todos_projetos'])){ ?>
                            <button type="button" onclick="tableToExcel('tabela', 'Relatório CNES')" value="Exportar para Excel" class="btn btn-success"><span class="fa fa-file-excel-o"></span> Exportar para Excel</button>
                        <?php } ?>
                        <?php ///permissão para VISUALIZAR TODOS OS PROJETOS AO MESMO TEMPO
                            if ($ACOES->verifica_permissoes(85)) { ?>
                            <button type="submit" name="todos_projetos" value="Gerar de Todos Projetos" id="todos_projetos" class="btn btn-warning"><span class="fa fa-filter"></span> Gerar de Todos os Projetos</span></button>
                        <?php } ?>
                            <button type="submit" name="gerar" value="Gerar" id="gerar" class="btn btn-primary"><span class="fa fa-filter"></span> Gerar</button>
                        </div>
                    </div>

                <?php if (!empty($qr_relatorio) && (isset($_POST['gerar'])) || isset($_REQUEST['todos_projetos'])) { ?>
                    <table class="table table-striped table-condensed table-bordered table-hover text-sm valign-middle" id="tabela">
                        <thead>
                            <tr>
                                <th colspan="9"><?= (!isset($_REQUEST['todos_projetos'])) ? $projeto['nome'] : 'TODOS OS PROJETOS' ?></th>
                            </tr>
                            <tr>
                                <th>NOME</th>
                                <th>UNIDADE DE ORIGEM</th>
                                <th>FUNÇÃO DE ORIGEM</th>
                                <th>UNIDADE DE DESTINO</th>
                                <th>FUNÇÃO DE DESTINO</th>
                                <th>MOTIVO</th>
                                <th style="width: 10%;">COMPETÊNCIA</th>   
                                <th style="width: 10%;">DATA DE CRIAÇÃO</th>   
                                <th>USUÁRIO RESPONSÁVEL</th>    

                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            while ($row_rel = mysql_fetch_assoc($qr_relatorio)) {
                                $class = ($cont++ % 2 == 0) ? "even" : "odd"
                                ?>
                                <tr class="<?php echo $class ?>">
                                    <td><?php echo $row_rel['nome'] ?></td>
                                    <td align="center"> <?php echo $row_rel['unidade_de']; ?></td>
                                    <td> <?php echo $row_rel['curso_de']; ?></td>
                                    <td align="center"> <?php echo $row_rel['unidade_para']; ?></td>
                                    <td> <?php echo $row_rel['curso_para']; ?></td>
                                    <td align="center"><?php echo $row_rel['motivo']; ?></td>                       
                                    <td align="center"><?php echo $row_rel['data_proc']; ?></td>                       
                                    <td align="center"><?php echo $row_rel['criado_em']; ?></td>                       
                                    <td align="center"><?php echo $row_rel['usuario']; ?></td>                       
                                </tr>                                
                            <?php } ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="8"><strong>TOTAL:</strong></td>
                                <td align="center"><?php echo $num_rows ?></td>
                            </tr>
                        </tfoot>
                    </table>
                <?php } ?>
            </form>
        <?php include('../template/footer.php'); ?>
    <div class="clear"></div>
</div>

<script src="../js/jquery-1.10.2.min.js"></script>
<script src="../resources/js/bootstrap.min.js"></script>
<script src="../resources/js/tooltip.js"></script>
<script src="../resources/js/main.js"></script>
<script src="../js/jquery.validationEngine-2.6.js" type="text/javascript"></script>
<script src="../js/jquery.validationEngine-pt.js" type="text/javascript"></script>
<script src="../js/jquery-ui-1.9.2.custom.min.js"></script>
<script src="../js/global.js"></script>

<!--<script src="../js/jquery-1.8.3.min.js" type="text/javascript"></script>-->

<script>
                                $(function () {
                                    var id_destination = "projeto";
                                    $('#regiao').ajaxGetJson("../methods.php", {method: "carregaProjetos"}, function (data) {
                                        removeLoading();
                                        $("#" + id_destination).html(data);
                                        var selected = $("input[name=hide_" + id_destination + "]").val();
                                        if (selected !== undefined) {
                                            $("#" + id_destination).val(selected);
                                        }
                                        $('#projeto').trigger('change');
                                    }, "projeto");

                                    $("#projeto").change(function () {
                                        var $this = $(this);
                                        if ($this.val() != "-1") {
                                            $.post('<?= $_SERVER['PHP_SELF'] ?>', {projeto: $this.val(), id_regiao: $('#regiao').val(), method: "carregafuncao"}, function (data) {
                                                var selected = "";
                                                if (data.stunid == 1) {
                                                    var unid = "<option value='-1'>« Selecione »</option>\n";
                                                    for (var i in data.unidade) {
                                                        selected = "";
                                                        if (i == "<?= $unidadeSel ?>") {
                                                            selected = "selected=\"selected\" ";
                                                        }
                                                        unid += "<option value='" + i + "' " + selected + ">" + data.unidade[i] + "</option>\n";
                                                    }
                                                    $("#unidade").html(unid);
                                                }
                                            }, "json");
                                        }
                                    });

                                    $('#projeto').ajaxGetJson("../methods.php", {method: "carregaFuncoes", default: "2"}, null, "funcao");
                                });

                                $(document).ready(function () {
                                    // instancia o validation engine no formulário
                                    $("#form1").validationEngine();
                                });
                                checkDate = function (field) {
                                    var date = field.val();
                                    if (date == -1) {
                                        return 'Selecione uma Data';
                                    }
                                };
</script>

</body>
</html>
