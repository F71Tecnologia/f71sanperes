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

$breadcrumb_config = array("nivel" => "../index.php", "key_btn" => "6", "area" => "Sistema", "id_form" => "form1", "ativo" => "Relatório de Log de Eventos");
$breadcrumb_pages = array("Principal" => "../listaLogRelatorios_novo.php?regiao=");

if (isset($_REQUEST['gerar']) || isset($_REQUEST['todos_projetos'])) {
    $cont = 0;

    $id_regiao = $_REQUEST['regiao'];
    $id_projeto = $_REQUEST['projeto'];
    $ano = $_REQUEST['ano'];
    $mes = $_REQUEST['mes'];
    
   
    $cond_data = ((!empty($ano) && !empty($mes)) && ($ano != '-1' && $mes != "-1")) ? "AND MONTH(b.data_mod) = '$mes' AND YEAR(b.data_mod) = '$ano'" : "";

    $projeto = montaQueryFirst("projeto", "nome", array('id_projeto' => $id_projeto));
    $sql = "SELECT a.id_clt,b.id_evento_log,
            (SELECT nome FROM rh_clt WHERE id_clt = a.id_clt) AS nome_clt,
            (SELECT cpf FROM rh_clt WHERE id_clt = a.id_clt) AS cpf_clt,
            b.nome_status_de,b.nome_status_para,
            DATE_FORMAT(b.data_mod,'%d/%m/%Y %T') AS data_mod,
            b.tipo,
            (SELECT nome FROM funcionario WHERE id_funcionario = b.id_funcionario) AS usuario
            FROM rh_eventos AS a
            INNER JOIN rh_eventos_log AS b ON (a.id_evento = b.id_evento)
            WHERE a.id_regiao = '$id_regiao' AND a.id_projeto = '$id_projeto'
            $cond_data
            ORDER BY b.id_evento_log DESC";
    echo "<!-- {$sql} -->";
    $qr_relatorio = mysql_query($sql) or die(mysql_error());
    $num_rows = mysql_num_rows($qr_relatorio);
}

if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'detalhes') {
    $query = "SELECT *,
                DATE_FORMAT(data_de,'%d/%m/%Y') AS data_de,
                DATE_FORMAT(data_para,'%d/%m/%Y') AS data_para,
                DATE_FORMAT(data_retorno_de,'%d/%m/%Y') AS data_retorno_de,
                DATE_FORMAT(data_retorno_para,'%d/%m/%Y') AS data_retorno_para
                FROM rh_eventos_log WHERE id_evento_log = {$_REQUEST['id']}";
    //echo $query;
    $result = mysql_query($query);
    $evento_log = mysql_fetch_assoc($result);
    $evento_log['nome_status_de'] = utf8_encode($evento_log['nome_status_de']);
    $evento_log['nome_status_para'] = utf8_encode($evento_log['nome_status_para']);
    $evento_log['obs_para'] = utf8_encode($evento_log['obs_para']);
    $evento_log['obs_de'] = utf8_encode($evento_log['obs_de']);

    echo json_encode($evento_log);
    exit();
}

$regiaoSel = (isset($_REQUEST['reg'])) ? $_REQUEST['reg'] : $usuario['id_regiao'];
$projetoSel = (isset($_REQUEST['pro'])) ? $_REQUEST['pro'] : null;

$regiaoSel = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $regiaoSel;
$projetoSel = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : $projetoSel;
$anoSel = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : date('Y');
$mesSel = (isset($_REQUEST['mes'])) ? $_REQUEST['mes'] : date('m');

/////////////////////////////// array de anos //////////////////////////////////
$arrayAnos[-1] = '« Selecione o Ano »';
for ($i = date('Y'); $i >= date('Y') - 10; $i--) {
    $arrayAnos[$i] = $i;
}

/////////////// funcao para criar exibir o nome da modificacao /////////////////
function tipoModificacao($tipo) {
    $tipoArr = array(
        1 => 'Criação',
        2 => 'Edição',
        3 => 'Exclusão',
        4 => 'Prorrogação'
    );
    return $tipoArr[$tipo];
}

?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Relatório de Log de Eventos</title>

        <link href="../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <!--<link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">-->
        <style>
        #dialog{
            display: none;
        }
        </style>

    </head>
    <body>
<?php include("../template/navbar_default.php"); ?>      
        <div class="container">
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - Relatório de Log de Eventos</h2></div>
            <form action="" method="post" class="form-horizontal top-margin1" name="form" id="form1">  
                <div class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print">Relatório</div>
                        <input type="hidden" name="hide_projeto" id="hide_projeto" value="<?php echo $projetoSel ?>" />
                        
                    <div class="panel-body">
                       <div class="form-group">
                            <label class="control-label col-sm-2">Região</label>
                            <div class="col-sm-4">
                                <?php echo montaSelect($optRegiao, $regiaoSel, array('name' => "regiao", 'id' => 'regiao', 'class' => 'form-control')); ?>
                            </div>
                       </div>
                             
                        <div class="form-group">
                            <label class="control-label col-sm-2">Projeto</label>
                            <div class="col-sm-4">
                                <?php echo montaSelect(array("-1" => "« Selecione a Região »"), $projetoSel, array('name' => "projeto", 'id' => 'projeto', 'class' => 'form-control')); ?>
                            </div>
                        </div>    
                      
                        <div class="form-group">
                            <label class="control-label col-sm-2">Período</label>
                            <div class="col-sm-2">
                                <?php echo montaSelect(mesesArray(), $mesSel, array('name' => "mes", 'id' => 'mes', 'class'=> 'form-control')); ?>
                            </div>
                            <div class="col-sm-2">
                                <?php echo montaSelect($arrayAnos, $anoSel, array('name' => "ano", 'id' => 'ano', 'class' => 'form-control')); ?>
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
                                <th style="width: 100px;">CPF</th>
                                <th style="width: 100px;">EVENTO DE ORIGEM</th>
                                <th style="width: 100px;">EVENTO DE DESTINO</th>
                                <th style="width: 150px;">MODIFICADO EM</th>
                                <th style="width: 150px;">TIPO</th>
                                <th>USUÁRIO RESPONSÁVEL</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                           <?php
                            while ($row_rel = mysql_fetch_assoc($qr_relatorio)) {
                                $class = ($cont++ % 2 == 0) ? "even" : "odd"
                                ?>
                                <tr class="<?php echo $class ?>">
                                    <td><?php echo $row_rel['nome_clt'] ?></td>
                                    <td style="text-align: center;"><?php echo $row_rel['cpf_clt'] ?></td>
                                    <td style="text-align: center;"><?php echo $row_rel['nome_status_de'] ?></td>
                                    <td style="text-align: center;"><?php echo $row_rel['nome_status_para'] ?></td>
                                    <td style="text-align: center;"><?php echo $row_rel['data_mod'] ?></td>
                                    <td style="text-align: center;"><?php echo tipoModificacao($row_rel['tipo']) ?></td>
                                    <td><?php echo $row_rel['usuario'] ?></td>
                                    <td style="text-align:center;"><a href="#" class="detalhes" data-id="<?= $row_rel['id_evento_log'] ?>"><span class="fa fa-search"></span></a></td>
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
                <div id="dialog">
                    <table class="table table-hover table-striped table-bordered text-sm valign-middle">
                        <thead>
                            <tr>
                                <th colspan="2">Evento de Origem</th>
                                <th colspan="2">Evento de Destino</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>Nome:</strong></td>
                                <td id="nome_evento_de"></td>
                                <td><strong>Nome:</strong></td>
                                <td id="nome_evento_para"></td>
                            </tr>
                            <tr>
                                <td><strong>Data de Início:</strong></td>
                                <td id="data_de"></td>
                                <td><strong>Data de Início:</strong></td>
                                <td id="data_para"></td>
                            </tr>
                            <tr>
                                <td><strong>Data de Retorno:</strong></td>
                                <td id="data_retorno_de"></td>
                                <td><strong>Data de Retorno:</strong></td>
                                <td id="data_retorno_para"></td>
                            </tr>
                            <tr>
                                <td><strong>Observação:</strong></td>
                                <td id="obs_de"></td>
                                <td><strong>Observação:</strong></td>
                                <td id="obs_para"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </form>
            
            <!--<div class="clear"></div>-->
             <?php include('../template/footer.php'); ?>
        </div>
        
        <script src="../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <!--<script src="../js/jquery-1.10.2.min.js"></script>-->
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/tooltip.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="js/jquery.maskedinput-1.3.1.js"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine-2.6.js" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine-pt.js" type="text/javascript"></script>
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
//                $("#projeto").change(function() {
//                    var $this = $(this);
//                    if ($this.val() != "-1") {
//                        $.post('<?= $_SERVER['PHP_SELF'] ?>', {projeto: $this.val(), id_regiao: $('#regiao').val(), method: "carregafuncao"}, function(data) {
//                            var selected = "";
//                            if (data.stunid == 1) {
//                                var unid = "<option value='-1'>« Selecione »</option>\n";
//                                for (var i in data.unidade) {
//                                    selected = "";
//                                    if (i == "<?= $unidadeSel ?>") {
//                                        selected = "selected=\"selected\" ";
//                                    }
//                                    unid += "<option value='" + i + "' " + selected + ">" + data.unidade[i] + "</option>\n";
//                                }
//                                $("#unidade").html(unid);
//                            }
//                        }, "json");
//                    }
//                });

                $('#projeto').ajaxGetJson("../methods.php", {method: "carregaFuncoes", default: "2"}, null, "funcao");
            });
            $(document).ready(function() {
                // instancia o validation engine no formulário
                $("#form1").validationEngine();
                $(".detalhes").click(function() {
                    $.post("<?= $_SERVER['PHP_SELF'] ?>", {id: $(this).data('id'), method: 'detalhes'}, function(data) {
                        $("#nome_evento_de").html(data.nome_status_de);
                        $("#nome_evento_para").html(data.nome_status_para);
                        $("#data_de").html(data.data_de);
                        $("#data_para").html(data.data_para);
                        $("#data_retorno_de").html(data.data_retorno_de);
                        $("#data_retorno_para").html(data.data_retorno_para);
                        $("#obs_de").html(data.obs_de);
                        $("#obs_para").html(data.obs_para);
                        $("#dialog").show();
                        thickBoxModal("Detalhes", "#dialog", 255, 650);
                    }, 'json');
                });
                $("#mes,#ano").change(function(){
                    var $this = $(this);
                    if($this.val() != '-1'){
                        $("#mes").addClass("validate[required,custom[select]]");
                        $("#ano").addClass("validate[required,custom[select]]");
                    }else{
                        $("#mes").removeClass("validate[required,custom[select]]");
                        $("#ano").removeClass("validate[required,custom[select]]");
                    }
                });
            });
            
        </script>

    </body>
</html>

<!-- A -->