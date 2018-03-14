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

$breadcrumb_config = array("nivel" => "../index.php", "key_btn" => "6", "area" => "Sistema", "id_form" => "form1", "ativo" => "Log de Desprocessamento de Férias");
$breadcrumb_pages = array("Principal" => "../listaLogRelatorios_novo.php?regiao=");

if (isset($_REQUEST['gerar']) || isset($_REQUEST['todos_projetos'])) {
    $cont = 0;

    $id_regiao = $_REQUEST['regiao'];
    $id_projeto = $_REQUEST['projeto'];
    $ano = $_REQUEST['ano'];
    $mes = $_REQUEST['mes'];


    $condicao = (!isset($_REQUEST['todos_projetos'])) ? "(b.id_unidade_de = '$id_unidade' or b.id_unidade_para = '$id_unidade') and" : '';

    $projeto = montaQueryFirst("projeto", "nome", array('id_projeto' => $id_projeto));
    $sql = "SELECT b.nome as nome_clt,
                DATE_FORMAT(b.data_ini,'%d/%m/%Y') as data_ini,
                DATE_FORMAT(b.data_fim,'%d/%m/%Y') as data_fim,
                DATE_FORMAT(b.data_aquisitivo_ini,'%d/%m/%Y') as aquisitivo_ini,
                DATE_FORMAT(b.data_aquisitivo_fim,'%d/%m/%Y') as aquisitivo_fim,
                b.dias_ferias,
                DATE_FORMAT(a.data_mod,'%d/%m/%Y %T') as data_mod,
                (SELECT nome FROM funcionario WHERE id_funcionario = a.id_usuario) as usuario
            FROM rh_ferias_log AS a
            INNER JOIN rh_ferias AS b ON (a.id_ferias = b.id_ferias)
            WHERE MONTH(a.data_mod) = '$mes' AND YEAR(a.data_mod) = '$ano' AND b.regiao = $id_regiao AND b.projeto = $id_projeto AND status_log_ferias=0";
    echo "<!-- {$sql} -->";
    $qr_relatorio = mysql_query($sql) or die(mysql_error());
    $num_rows = mysql_num_rows($qr_relatorio);
}

$regiaoSel = (isset($_REQUEST['reg'])) ? $_REQUEST['reg'] : $usuario['id_regiao'];
$projetoSel = (isset($_REQUEST['pro'])) ? $_REQUEST['pro'] : null;

$regiaoSel = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $regiaoSel;
$projetoSel = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : $projetoSel;
$anoSel = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : date('Y');
$mesSel = (isset($_REQUEST['mes'])) ? $_REQUEST['mes'] : date('m');

////////////////////////////////////////////////////////////////////////////////
/////////////////////////// array de anos //////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
$arrayAnos[-1] = '« Selecione o Ano »';
for ($i = date('Y'); $i >= date('Y') - 10; $i--) {
    $arrayAnos[$i] = $i;
}
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Log de Desprocessamento de Férias</title>

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
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - Log de Desprocessamento de Férias</h2></div>
            <form action="" method="post" class="form-horizontal top-margin1" name="form" id="form1">  
                <div class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print">Relatório</div>
                        <input type="hidden" name="hide_projeto" id="hide_projeto" value="<?php echo $projetoSel ?>" />
                        
                    <div class="panel-body">
                       <div class="form-group">
                            <label class="control-label col-sm-2">Região</label>
                            <div class="col-sm-6">
                                <?php echo montaSelect($optRegiao, $regiaoSel, array('name' => "regiao", 'id' => 'regiao', 'class' => 'form-control validate[required,funcCall[checkDate]]')); ?>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="control-label col-sm-2">Projeto</label>
                            <div class="col-sm-6">
                                <?php echo montaSelect(array("-1" => "« Selecione a Região »"), $projetoSel, array('name' => "projeto", 'id' => 'projeto', 'class' => 'form-control validate[required,,funcCall[checkDate]]')); ?>
                            </div>
                        </div>    
                        
                        <div class="form-group">
                            <label class="control-label col-sm-2">Período</label>
                            <div class="col-sm-3">
                                <?php echo montaSelect(mesesArray(), $mesSel, array('name' => "mes", 'id' => 'mes', 'class' => 'form-control validate[required,funcCall[checkDate]]')); ?>
                            </div>
                            <div class="col-sm-3">
                                <?php echo montaSelect($arrayAnos, $anoSel, array('name' => "ano", 'id' => 'ano', 'class' => 'form-control validate[required,funcCall[checkDate]]')); ?>
                            </div>
                        </div>    
                         
                        <div class="panel-footer text-right hidden-print controls">
                            <span class="fleft erro"><?php if ($verifica_dirf != 0) echo 'Arquivo já existente!'; ?></span>
                            <?php
                        ///PERMISSÃO PARA VISUALIZAR TODOS OS PROJETOS AO MESMO TEMPO
                        if ($ACOES->verifica_permissoes(85)) {
                            ?>
                            <button type="submit" name="todos_projetos" value="Gerar de Todos Projetos" class="btn btn-primary" id="todos_projetos"><span class="fa fa-filter"></span> Todos Os Projetos</button>
                            <?php } ?>
                            <button type="submit" name="gerar" id="gerar" value="Gerar" class="btn btn-primary"><span class="fa fa-filter"></span> Gerar</button>
                        </div>
                    </div> 
               </div>
                
                <?php if (!empty($qr_relatorio) && isset($_POST['gerar']) || isset($_REQUEST['todos_projetos'])) { ?>
                    <p style="text-align: right; margin-top: 20px"><input type="button" onclick="tableToExcel('tbRelatorio', 'Participantes Ativos')" value="Exportar para Excel" class="exportarExcel btn btn-success"></p>    
                    <table id="tbRelatorio" class="table table-hover table-striped table-bordered text-sm valign-middle"> 
                        <thead>
                            <tr>
                                <th colspan="8"><?= (!isset($_REQUEST['todos_projetos']))? $projeto['nome'] : 'TODOS OS PROJETOS' ?></th>
                            </tr>
                            <tr class="bg-primary">
                               <th>NOME DO CLT</th>
                                <th>INÍCIO DO AQUISITIVO</th>
                                <th>FIM DO AQUISITIVO</th>
                                <th>INÍCIO DAS FÉRIAS</th>
                                <th>FIM DAS FÉRIAS</th>
                                <th>DIAS DE FÉRIAS</th>
                                <th>DATA DE DESPROCESSAMENTO</th>
                                <th>USUÁRIO RESPONSÁVEL</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row_rel = mysql_fetch_assoc($qr_relatorio)) {
                                $class = ($cont++ % 2 == 0) ? "even" : "odd"
                                ?>
                                <tr class="<?php echo $class ?>">
                                    <td><?php echo $row_rel['nome_clt'] ?></td>
                                    <td style="text-align: center;"><?php echo $row_rel['aquisitivo_ini'] ?></td>
                                    <td style="text-align: center;"><?php echo $row_rel['aquisitivo_fim'] ?></td>
                                    <td style="text-align: center;"><?php echo $row_rel['data_ini'] ?></td>
                                    <td style="text-align: center;"><?php echo $row_rel['data_fim'] ?></td>
                                    <td style="text-align: center;"><?php echo $row_rel['dias_ferias'] ?></td>
                                    <td style="text-align: center;"><?php echo $row_rel['data_mod'] ?></td>
                                    <td><?php echo $row_rel['usuario'] ?></td>
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
        
        <script src="../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <!--<script src="../js/jquery-1.10.2.min.js"></script>-->
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/tooltip.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/tableExport.jquery.plugin-master/tableExport.js" type="text/javascript"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../js/global.js" type="text/javascript"></script>
        
            
         <script>
            $(function() {
                var id_destination = "projeto";
                $('#regiao').ajaxGetJson("../methods.php", {method: "carregaProjetos"}, function(data) {
                    removeLoading();
                    $("#" + id_destination).html(data);
                    var selected = $("input[name=hide_" + id_destination + "]").val();
                    if (selected !== undefined) {
                        $("#" + id_destination).val(selected);
                    }
                    $('#projeto').trigger('change');
                }, "projeto");

                $("#projeto").change(function() {
                    var $this = $(this);
                    if ($this.val() != "-1") {
                        $.post('<?= $_SERVER['PHP_SELF'] ?>', {projeto: $this.val(), id_regiao: $('#regiao').val(), method: "carregafuncao"}, function(data) {
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
            
            $(document).ready(function() {
                // instancia o validation engine no formulário
                $("#form1").validationEngine();
            });
            checkDate = function(field){
                var date = field.val();
                if(date == -1){
                    return 'Selecione uma Data';
                }
            };
        </script>

    </body>
</html>

<!-- A -->