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

////////////////////////////////////////////////////////////////////////////////
// funcao para exibir o tipo de modificacao
function nomeTipoMod($tipo) {
    switch ($tipo) {
        case 1:
            return 'Criação';
        case 2:
            return 'Edição';
        case 3:
            return 'Exclusão';
        case 4:
            return 'Prorrogação';
        default:
            return NULL;
    }
}

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
/////////////////////////// array de anos //////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//$arrayAnos[-1] = '« Selecione o Ano »';
//for ($i = date('Y'); $i >= date('Y') - 10; $i--) {
//    $arrayAnos[$i] = $i;
//}

$arrayAnos = anosArray(null, null, array('' => "<< Ano >>"));

if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'detalhes') {
//    $evento = new Eventos();
//    $resp = $evento->getEventoById($_POST['id']);
    $resp = mysql_fetch_assoc(mysql_query("SELECT nome_status_de,nome_status_para,obs_de,obs_para,
            DATE_FORMAT(data_de,'%d/%m/%Y') AS data_de,
            DATE_FORMAT(data_retorno_de, '%d/%m/%Y') AS data_retorno_de,
            DATE_FORMAT(data_para,'%d/%m/%Y') AS data_para,
            DATE_FORMAT(data_retorno_para, '%d/%m/%Y') AS data_retorno_para
            FROM rh_eventos_log WHERE id_evento_log = '{$_POST['id']}'"));
            
    $resp['nome_status_de'] = utf8_encode($resp['nome_status_de']);
    $resp['nome_status_para'] = utf8_encode($resp['nome_status_para']);
    $resp['obs_de'] = utf8_encode($resp['obs_de']);
    $resp['obs_para'] = utf8_encode($resp['obs_para']);
            
    echo json_encode($resp);
    exit();
}



if (isset($_REQUEST['gerar']) || isset($_REQUEST['todos_projetos'])) {
    $cont = 0;
    $arrayStatus = array(10, 20, 30, 40, 50, 51, 52);
    $status = implode(",", $arrayStatus);

    $id_regiao = $_REQUEST['regiao'];
    $id_projeto = $_REQUEST['projeto'];

    $cond_funcao = (isset($_REQUEST['funcao']) && !empty($_REQUEST['funcao']) && $_REQUEST['funcao'] != '-1') ? " AND E.id_curso= '{$_REQUEST['funcao']}' " : "";

    $projeto = montaQueryFirst("projeto", "nome", array('id_projeto' => $id_projeto));

    if (!isset($_REQUEST['todos_projetos'])) {
        // verifica campos preenchidos e monta o where
        $where[] = (!empty($_REQUEST['regiao']) && $_REQUEST['regiao'] != '-1') ? "c.id_regiao='{$_REQUEST['regiao']}'" : NULL;
        $where[] = (!empty($_REQUEST['projeto']) && $_REQUEST['projeto'] != '-1') ? "c.id_projeto='{$_REQUEST['projeto']}'" : NULL;
        $where[] = (!empty($_REQUEST['nome_clt'])) ? "c.nome LIKE '%{$_REQUEST['nome_clt']}%'" : NULL;
        $where[] = (!empty($_REQUEST['mes']) && !empty($_REQUEST['ano']) && $_REQUEST['mes'] != '-1' && $_REQUEST['ano'] != '-1') ? "MONTH(a.data) = '{$_REQUEST['mes']}' AND YEAR(a.data) = '{$_REQUEST['ano']}'" : NULL;
        $count = count($where);
        for ($i = 0; $i <= $count; $i++) {
            if (empty($where[$i])) {
                unset($where[$i]);
            }
        }

        if (!empty($where)) {
            $cond_where = "WHERE " . implode(' AND ', $where);
        }
    } else {
        $cond_where = '';
    }


    $sql = "SELECT b.id_evento_log,c.id_clt,c.nome AS nome_clt,c.cpf,a.nome_status,DATE_FORMAT(a.`data`, '%d/%m/%Y') AS `data`,DATE_FORMAT(a.data_retorno, '%d/%m/%Y') AS data_retorno,a.dias,DATE_FORMAT(b.data_mod, '%d/%m/%Y %T') AS data_mod,b.tipo,d.nome AS usuario
            FROM rh_eventos AS a
            INNER JOIN rh_eventos_log AS b ON (a.id_evento = b.id_evento)
            INNER JOIN rh_clt AS c ON (a.id_clt = c.id_clt)
            INNER JOIN funcionario AS d ON (b.id_funcionario = d.id_funcionario) $cond_where ORDER BY b.id_evento_log ASC";

//    echo $sql;
    $qr_relatorio = mysql_query($sql) or die(mysql_error());
    $num_rows = mysql_num_rows($qr_relatorio);
}

$regiaoSel = (isset($_REQUEST['reg'])) ? $_REQUEST['reg'] : $usuario['id_regiao'];
$projetoSel = (isset($_REQUEST['pro'])) ? $_REQUEST['pro'] : null;

$regiaoSel = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $regiaoSel;
$projetoSel = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : $projetoSel;
$nome_clt = (isset($_REQUEST['nome_clt'])) ? $_REQUEST['nome_clt'] : null;
$anoSel = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : null;
$mesSel = (isset($_REQUEST['mes'])) ? $_REQUEST['mes'] : null;
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <title>:: Intranet :: Relatório de Log De Eventos </title>
        
        <link href="../favicon.png" rel="shortcut icon" />
        
        <!-- Bootstrap -->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
    </head>

    <body>
        <?php include("../template/navbar_default.php"); ?>      
        <div class="container">
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small> - Relatório de Log De Eventos</small></h2></div>
            <form action="" method="post" class="form-horizontal top-margin1" name="form" id="form">  
                <div class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print">Relatório</div>
                        <div class="panel-body">
                            <div class="form-group" >
                                <label for="select" class="col-sm-2 control-label hidden-print" >Região</label>
                                <div class="col-sm-4">
                                  <?php echo montaSelect($optRegiao, $regiaoSel, array('name' => "regiao", 'id' => 'regiao', 'class' => 'validate[required] form-control')); ?><span class="loader"></span>
                                </div>

                                <label for="select" class="col-sm-1 control-label hidden-print" >Projeto</label>
                                <div class="col-sm-3">
                                  <?php echo montaSelect(array("-1" => "« Selecione o Projeto »"), $projetoSel, array('name' => "projeto", 'id' => 'projeto', 'class' => 'validate[required] form-control')); ?> <span class="loader"></span> 
                                </div>
                            </div>
                            <div class="form-group" >    
                                <label for="select" class="col-sm-2 control-label hidden-print" >Nome do CLT</label>
                                <div class="col-sm-8">
                                    <input type="text" name="nome_clt" id="nome_clt" class="form-control" value="<?= $nome_clt ?>" /><span class="loader"></span> 
                                </div>
                            </div>
                            <div class="form-group" >
                                <label for="select" class="col-sm-2 control-label hidden-print" >Periodo</label>
                                <div class="col-sm-4">
                                  <?php echo montaSelect(mesesArray(), $mesSel, array('name' => "mes", 'id' => 'mes', 'class'=> 'form-control')); ?><span class="loader"></span>
                                </div>
                                <div class="col-sm-4">
                                   <?php echo montaSelect($arrayAnos, $anoSel, array('name' => "ano", 'id' => 'ano', 'class'=> 'form-control')); ?><span class="loader"></span>
                                </div>
                            </div>                       
                        </div>                       
                            
                        <span class="fleft erro"><?php if ($verifica_dirf != 0) echo 'Arquivo já existente!'; ?></span>
                        
                        <div class="panel-footer text-right hidden-print controls">
                            <?php if (!empty($qr_relatorio) && (isset($_POST['gerar'])) || isset($_REQUEST['todos_projetos'])){ ?>
                                <button type="button" onclick="tableToExcel('tabela', 'Log de Eventos')" value="Exportar para Excel" class="btn btn-success" ><span class="fa fa-file-excel-o"></span> Exportar para Excel</button>
                            <?php } ?>
                            <?php ///permissão para VISUALIZAR TODOS OS PROJETOS AO MESMO TEMPO
                                if($ACOES->verifica_permissoes(85)) { ?>
                                <button type="submit" name="todos_projetos" value="gerar de todos os projetos" id="todos_projetos" class="btn btn-warning"><span class="fa fa-filter"></span> Filtrar Todos os Projetos</button>
                            <?php } ?>
                                <button type="submit" name="gerar" id="gerar" value="gerar" class="btn btn-primary"><span class="fa fa-filter"></span> Filtrar</button>
                        </div>
                    </div> 
                    
                    <?php if (!empty($qr_relatorio) && (isset($_POST['gerar'])) || isset($_REQUEST['todos_projetos'])) { ?>
                        <table class="table table-striped table-hover table-condensed table-bordered text-sm valign-middle" id="tabela">
                        <thead>
                            <tr>
                                <th colspan="10"><?php echo $projeto['nome'] ?></th>
                            </tr>
                            <tr>
                                <th>#</th>
                                <th>NOME DO CLT</th>
                                <th style="width: 90px;">CPF</th>
                                <th>EVENTO</th>
                                <th style="width: 60px;">DATA</th>
                                <th style="width: 60px;">DATA DE RETORNO</th>   
                                <th>DIAS</th>   
                                <th style="width: 90px;">DATA DE MODIFICAÇÃO</th>   
                                <th style="width: 90px;">TIPO DE MODIFICAÇÃO</th>
                                <th style="text-align: center;">USUÁRIO</th>
                                <th>DETALHES</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            while ($row_rel = mysql_fetch_assoc($qr_relatorio)) {
                                $class = ($cont++ % 2 == 0) ? "even" : "odd"
                                ?>
                                <tr class="<?php echo $class ?>">
                                    <td><?= $row_rel['id_clt'] ?></td>
                                    <td> <?= $row_rel['nome_clt']; ?></td>
                                    <td> <?= $row_rel['cpf']; ?></td>
                                    <td> <?= $row_rel['nome_status']; ?></td>
                                    <td align="center"><?= $row_rel['data']; ?></td>                       
                                    <td align="center"><?= $row_rel['data_retorno']; ?></td>                       
                                    <td align="center"><?= $row_rel['dias']; ?></td>                       
                                    <td align="center"><?= $row_rel['data_mod']; ?></td>                       
                                    <td align="center"><?= nomeTipoMod($row_rel['tipo']); ?></td>                       
                                    <td align="center"><?= $row_rel['usuario']; ?></td>                       
                                    <td align="center"><a href="javascript:;" class="showDetalhes" data-id="<?= $row_rel['id_evento_log'] ?>"><div class="fa fa-file-text-o"></div></a></td>
                                </tr>                                
                            <?php } ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="9"><strong>Total:</strong></td>
                                <td align="center"><?php echo $num_rows ?></td>
                            </tr>
                        </tfoot>
                </table>
                <?php  } ?>
      
            </form>
            <?php include('../template/footer.php'); ?>
            <div class="clear"></div>
        </div>
        
        <!--<script src="../js/jquery-1.10.2.min.js"></script>-->
        <script src="../js/jquery-1.8.2.min.js"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/tooltip.js"></script>
        <script src="../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../resources/js/main.js"></script>
<!--        <script src="../js/jquery.validationEngine-2.6.js" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine-pt.js" type="text/javascript"></script>-->
        <script src="../js/global.js"></script>
        <script>
        var checkSelect = function (field) {
            var campo = field.val();
            if (campo == -1) {
                return 'Selecione uma Opção.';
            }
        };

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


            $('#projeto').ajaxGetJson("../methods.php", {method: "carregaFuncoes", default: "2"}, null, "funcao");

//            $("#form").validationEngine();

            $("#todos_projetos").click(function () {
                $("#regiao").removeClass('validate[required,funcCall[checkSelect]]');
                $("#projeto").removeClass('validate[required,funcCall[checkSelect]]');
                $("#form").submit();
            });
            
            $(".showDetalhes").click(function(){
                var id_evento = $(this).data("id");
               
                $.post("ver_relatorio_eventos_log.php", {id: id_evento, method: "detalhes"}, function(data){
//                   var item = "<h4>" + data.nome_status_de + "</h4>";
//                    var item = $(".detalhes").html(data.nome_status_de);
//                    console.log( data );                    
//                    bootDialog(data, "<h4>Detalhe do Log de Eventos</h4>", true, "success");
                      BootstrapDialog.show({
                        nl2br: false,
                        size: BootstrapDialog.SIZE_WIDE,
                        title: 'Detalhe do Log de Eventos',
                        message: data,
                        closable: true,
                        type: 'type-success',
                    });  
                });
            });
            
            
//             $(".showDetalhes").click(function(){
//                    var id_evento = $(this).data("id");
////                    thickBoxIframe("Detalhe de Rescisão", "detalheRescisao.php", {id_evento:rescisao}, 800, 600);
//                    thickBoxIframe("Detalhe do Log de Evento", '<?= $_SERVER['PHP_SELF']; ?>', {id: id_evento, method: "detalhes"}, 800, 600);
//                });

//            $(".showDetalhes").click(function () {
//                var id_evento = $(this).data("id");
//                $.post('<?= $_SERVER['PHP_SELF']; ?>', {id: id_evento, method: 'detalhes'}, function (data) {
//                    if (data != 0) {
//                        $("#status_de").html(data.nome_status_de);
//                        $("#status_para").html(data.nome_status_para);
//                        $("#data_inicio_de").html(data.data_de);
//                        $("#data_inicio_para").html(data.data_para);
//                        $("#data_retorno_para").html(data.data_retorno_para);
//                        $("#data_retorno_de").html(data.data_retorno_de);
//                        $("#obs_de").html(data.obs_de);
//                        $("#obs_para").html(data.obs_para);
//                        thickBox("Detalhes do Log de Evento", "#dialog", 255, 600);
//                    } else {
//                        alert('Falha ao carregar evento!');
//                        exit();
//                    }
//                }, 'json');
//            });
        });
</script>
        
    </body>
</html>
