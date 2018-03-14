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
$optRegiao = getRegioes();
$ACOES = new Acoes();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)$ACOES = new Acoes();

$breadcrumb_config = array("nivel" => "../index.php", "key_btn" => "6", "area" => "Sistema", "id_form" => "form1", "ativo" => "Relatório de Quantidade de Acessos aos Relatórios");
$breadcrumb_pages = array("Principal" => "../listaLogRelatorios_novo.php?regiao=");

if (isset($_REQUEST['gerar']) || isset($_REQUEST['todos_projetos'])) {
    $data_ini = (isset($_REQUEST['data-ini']) && $_REQUEST['data-ini'] != '-1') ? $_REQUEST['data-ini'] : FALSE;
    $data_fim = (isset($_REQUEST['data-fim']) && $_REQUEST['data-fim'] != '-1') ? $_REQUEST['data-fim'] : FALSE;

    if ($data_ini && $data_fim) {
        $where = "AND DATE_FORMAT(data_acesso,'%d/%m/%Y') BETWEEN '$data_ini' AND '$data_fim'";
    } else {
        $where = '';
    }
    
    if (isset($_REQUEST['order-by'])) {
        switch ($_REQUEST['order-by']) {
            case 1:
                $orderby = "ORDER BY qtd ASC";
                break;
            case 2:
                $orderby = "ORDER BY qtd DESC";
                break;
            case 3:
                $orderby = "ORDER BY ultimo_acesso ASC";
                break;
            case 4:
                $orderby = "ORDER BY ultimo_acesso DESC";
                break;
            case 5:
                $orderby = "ORDER BY nome_arquivo";
                break;
            default :
                $orderby = "";
        }
    }
    
    $projeto = montaQueryFirst("projeto", "nome", array('id_projeto'=>$id_projeto));
    $sql = "SELECT nome_arquivo,COUNT(nome_arquivo) AS qtd,
            DATE_FORMAT(MAX(data_acesso),'%d/%m/%Y %T') AS ultimo_acesso,
            DATE_FORMAT(MIN(data_acesso),'%d/%m/%Y %T') AS primeiro_acesso,
            (SELECT nome FROM relatorios WHERE relatorios.id_relatorio = relatorios_log.id_relatorio) AS relatorio
            FROM relatorios_log
            WHERE id_usuario != 204 $where
            GROUP BY nome_arquivo
            $orderby";
    echo "<!-- {$sql} -->";
    $qr_relatorio = mysql_query($sql) or die(mysql_error());
    $num_rows = mysql_num_rows($qr_relatorio);
}

$regiaoSel = (isset($_REQUEST['reg'])) ? $_REQUEST['reg'] : $usuario['id_regiao'];
$projetoSel = (isset($_REQUEST['pro'])) ? $_REQUEST['pro'] : null;
$funcaoSel = (isset($_REQUEST['funcao'])) ? $_REQUEST['funcao'] : null;

$regiaoSel = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $regiaoSel;
$projetoSel = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : $projetoSel;

$data_iniSel = (isset($_REQUEST['data-ini'])) ? $_REQUEST['data-ini'] : NULL;
$data_fimSel = (isset($_REQUEST['data-fim'])) ? $_REQUEST['data-fim'] : NULL;
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Relatório de Quantidade de Acessos aos Relatórios</title>

        <link href="../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <!--<link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">-->

    </head>
    <body>
<?php include("../template/navbar_default.php"); ?>      
        <div class="container">
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - Relatório de Quantidade de Acessos aos Relatórios</h2></div>
            <form action="" method="post" class="form-horizontal top-margin1" name="form" id="form1">  
                <div class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print">Relatório</div>
                        <input type="hidden" name="hide_projeto" id="hide_projeto" value="<?php echo $projetoSel ?>" />
                        <input type="hidden" name="hide_funcao" id="hide_funcao" value="<?php echo $funcaoSel ?>" />
                        
                    <div class="panel-body">
                       <div class="form-group">
                            <label class="control-label col-sm-2">Intervalo:</label>
                            <div class="col-sm-4">
                                <div class="input-group">
                                    <input type="text" name="data-ini" id="from" class="form-control" value="<?= $data_iniSel ?>"/>
                                    <span class="input-group-addon">até</span>
                                    <input type="text" name="data-fim" id="to" class="form-control" value="<?= $data_fimSel ?>"/>
                                </div>
                            </div>
                            <label class="control-label">* Para exibir tudo, deixe as datas em branco</label>
                       </div>
                                               
                        <div class="form-group">
                            <label class="control-label col-sm-2">Ordenar:</label>
                            <div class="col-sm-4">
                                <select name="order-by" id="order-by" class="form-control">
                                    <option value="1">Data de Acesso (Crescente)</option>
                                    <option value="2" selected="selected">Data de Acesso (Decrescente)</option>
                                    <option value="3">Relatórios (Ordem Alfabética)</option>
                                    <option value="4">Usuários (Ordem Alfabética)</option>
                                </select>
                            </div>
                        </div>    
                         
                        <div class="panel-footer text-right hidden-print controls">
                            <span class="fleft erro"><?php if ($verifica_dirf != 0) echo 'Arquivo já existente!'; ?></span>
                            <button type="submit" name="gerar" id="gerar" value="Gerar" class="btn btn-primary"><span class="fa fa-filter"></span>Gerar</button>
                        </div>
                    </div> 
               </div>
                
                <?php if (!empty($qr_relatorio) && isset($_POST['gerar']) || isset($_REQUEST['todos_projetos'])) { ?>
                    <p style="text-align: right; margin-top: 20px"><input type="button" onclick="tableToExcel('tbRelatorio', 'Participantes Ativos')" value="Exportar para Excel" class="exportarExcel btn btn-success"></p>    
                    <table id="tbRelatorio" class="table table-hover table-striped table-bordered text-sm valign-middle"> 
                        <thead>
                             <tr>
                                <th colspan="5"><?php echo $projeto['nome'] ?></th>
                            </tr>
                            <tr class="bg-primary">
                                <th>RELATÓRIO</th>
                                <th>URL</th>
                                <th>QTD. ACESSOS</th>
                                <th>ÚLTIMO ACESSO</th>
                                <th>PRIMEIRO ACESSO</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row_rel = mysql_fetch_assoc($qr_relatorio)) { $class = ($cont++ % 2 == 0)?"even":"odd"?>
                            <tr class="<?php echo $class ?>">
                                <td><?php echo $row_rel['relatorio'] ?></td>
                                <td><?php echo $row_rel['nome_arquivo'] ?></td>
                                <td style="text-align: center;"> <?php echo $row_rel['qtd']; ?></td>
                                <td style="text-align: center;"> <?php echo $row_rel['ultimo_acesso']; ?></td>
                                <td style="text-align: center;"> <?php echo $row_rel['primeiro_acesso']; ?></td>
                            </tr>                                
                            <?php } ?>
                        </tbody>
                        <tfoot>
                            <tr class="bg-primary">
                                <td colspan="2">Total:</td>
                                <td align="center"><?php echo $num_rows ?></td>
                            </tr>
                        </tfoot>
                    </table>
                <?php } ?>
                
            </form>
            
            <!--<div class="clear"></div>-->
             <?php include('../template/footer.php'); ?>
        </div>
        
        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/tooltip.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/tableExport.jquery.plugin-master/tableExport.js" type="text/javascript"></script>
        <!--<script src="../js/jquery-1.8.3.min.js" type="text/javascript"></script>-->
        <script src="../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../js/global.js" type="text/javascript"></script>
        
            
         <script>
            $(function() {
                var id_destination = "projeto";
                $('#regiao').ajaxGetJson("../methods.php", {method: "carregaProjetos"}, function(data){
                    removeLoading();
                    $("#" + id_destination).html(data);
                    var selected = $("input[name=hide_" + id_destination + "]").val();
                    if (selected !== undefined) {
                        $("#" + id_destination).val(selected);
                    }
                    $('#projeto').trigger('change');
                }, "projeto");
                
                
                $('#projeto').ajaxGetJson("../methods.php", {method: "carregaFuncoes", default:"2"}, null, "funcao");
                    
                // intervalo de datas
                $("#from").datepicker({
                    defaultDate: "+1w",
                    changeMonth: true,
                    changeYear: true,
                    onClose: function(selectedDate) {
                        $("#to").datepicker("option", "minDate", selectedDate);
                    }
                });
                $("#to").datepicker({
                    defaultDate: "+1w",
                    changeMonth: true,
                    changeYear: true,
                    onClose: function(selectedDate) {
                        $("#from").datepicker("option", "maxDate", selectedDate);
                    }
                });
            });
        </script>

    </body>
</html>

<!-- A -->