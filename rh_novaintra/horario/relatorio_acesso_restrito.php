<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
    exit;
}

include "../../conn.php";
include "../../classes/funcionario.php";
include '../../classes_permissoes/regioes.class.php';
include "../../wfunction.php";
include "../../classes_permissoes/acoes.class.php";

$usuario = carregaUsuario();
$optRegiao = getRegioes();

$id_regiao = $_REQUEST['regiao'];
$id_projeto = $_REQUEST['projeto'];

if (isset($_REQUEST['gerar'])) {

    $sql = "select c.id_clt, c.id_projeto, c.id_regiao, c.nome as funcionario, h.*
                from 
                        rh_clt c inner join 
                        rh_horarios h on c.rh_horario = h.id_horario
                where c.id_projeto = 1 and c.id_regiao = 1 ";
    
    if(trim($_REQUEST['funcionario']))
    {
        $sql .= "and c.nome like '%{$_REQUEST['funcionario']}%'";
    }
    
    $result = mysql_query($sql);
    //echo "sql = [$sql]<br>\n";
    //exit();
}

$regiaoSel = (isset($_REQUEST['reg'])) ? $_REQUEST['reg'] : $usuario['id_regiao'];
$projetoSel = (isset($_REQUEST['pro'])) ? $_REQUEST['pro'] : null;

$regiaoSel = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $regiaoSel;
$projetoSel = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : $projetoSel;
$unidadeSel = (isset($_REQUEST['unidade'])) ? $_REQUEST['unidade'] : null;

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$breadcrumb_config = array("nivel" => "../../", "key_btn" => "3", "area" => "Recursos Humanos", "id_form" => "form1", "ativo" => "Gestão de Horário");

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Relatório Controle de Acesso</title>
        <link href="../../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" >
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../css/progress.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap-note.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/add-ons.min.css" rel="stylesheet">
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        <div class="container">
            <form name="form" action="" method="post" id="form" class="form-horizontal">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small> - Relatório Horários</small></h2></div>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-body">
                    <input type="hidden" name="hide_projeto" id="hide_projeto" value="<?php echo $projetoSel ?>" />
                    <input type="hidden" name="hide_funcao" id="hide_funcao" value="<?php echo $funcaoSel ?>" />
                    <div class="form-group">
                        <label class="col-md-1 control-label">Região:</label>
                        <div class="col-md-5">
                            <?php echo montaSelect($optRegiao, $regiaoSel, array('name' => "regiao", 'id' => 'regiao', 'class' => 'form-control')); ?>
                        </div>
                        <label class="col-md-1 control-label">Projeto:</label>
                        <div class="col-md-5">
                            <?php echo montaSelect(array("-1" => "« Selecione a Região »"), $projetoSel, array('name' => "projeto", 'id' => 'projeto', 'class' => 'form-control')); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-1 control-label">Funcionário:</label>
                        <div class="col-md-11">
                            <input type="text" name='funcionario' id='funcionario' class="form-control" value="<?=$_REQUEST['funcionario']?>"/>
                        </div>
                    </div>                    
                </div>
                <div class="panel-footer text-right">
                    <span class="fleft erro"><?php if ($verifica_dirf != 0) echo 'Arquivo já existente!'; ?></span>
                    <input type="submit" class="btn btn-primary" name="gerar" value="Gerar" id="gerar"/>
                </div>
            </div>

<?php if (isset($_REQUEST['gerar'])): ?>
	    <p style="text-align: right; margin-top: 20px"><input type="button" onclick="tableToExcel('tbRelatorio', 'Participantes Ativos')" value="Exportar para Excel" class="btn btn-success"></p>
            <table id='tbRelatorio' class="table table-condensed table-hover">
                <thead>
                    <tr class="novo_tr">
                        <th class="valign-middle">NOME</th>
                        <th class="center valign-middle">Horário</th>
                        <th class="center valign-middle">Horário Mensal</th>
                        <th class="center valign-middle">Horário Semanal</th>
                        <th class="center valign-middle">Adc Noturno</th>
                        <th class="center valign-middle">Hora Noturna</th>
                    </tr>
                </thead>
	       <?php 
	       $tot = 0;
	       while ($row = mysql_fetch_array($result)): ?>

		<tr class="linha_<?php echo ($alternateColor++%2==0)?"um":"dois"; ?>" style="font-size:12px;">
		    <td><?=$row['funcionario']?></td>
		    <td><?=$row['nome']?></td>
		    <td style="text-align: center;"><?=$row['horas_mes']?></td>
		    <td style="text-align: center;"><?=$row['horas_semanais']?></td>
		    <td style="text-align: center;"><?=($row['adicional_noturno']==1)?'Sim':'Não'?></td>
		    <td style="text-align: center;"><?=$row['horas_noturnas']?></td>
		</tr>

		<?php 
		endwhile; ?>
		
	    </table>
<?php endif;?>
            <?php include_once '../../template/footer.php'; ?>
            </form>
        </div>
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../js/jquery.mask.min.js" type="text/javascript"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script>
            $(function() {
                $('#data1').datepicker({
                    changeMonth: true,
                    changeYear: true
                });
                $('#data2').datepicker({
                    changeMonth: true,
                    changeYear: true
                });
                
                var id_destination = "projeto";
                $('#regiao').ajaxGetJson("../../methods.php", {method: "carregaProjetos"}, function(data) {
                    removeLoading();
                    $("#" + id_destination).html(data);
                    var selected = $("input[name=hide_" + id_destination + "]").val();
                    if (selected !== undefined) {
                        $("#" + id_destination).val(selected);
                    }
                    $('#projeto').trigger('change');
                }, "projeto");
                $('#projeto').change(function() {
                    var pro = $(this).val();
                    $.post("<?= $_SERVER['PHP_SELF'] ?>", {pro: pro, method: 'funcionarios', contratacao: $('#contratacao').val()}, function(data) {
                        var selected = "";
                        var unid = "<option value='-1'>« TODOS »</option>\n";
			$.each(data, function(k, v){
                        //for (var i in data.func) {
                            selected = "";
                            if (v.id == "<?= $unidadeSel ?>") {
                                selected = "selected=\"selected\" ";
                            }
                            //unid += "<option value='" + i + "' " + selected + ">" + data.func[i] + "</option>\n";
			    unid += "<option value='" + v.id + "' " + selected + ">" + v.nome + "</option>\n";
                        });
                        $("#func").html(unid);
                    }, 'json');
                });
		$('#contratacao').change(function() {
                    var pro = $('#projeto').val();
                    $.post("<?= $_SERVER['PHP_SELF'] ?>", {pro: pro, method: 'funcionarios', contratacao: $('#contratacao').val()}, function(data) {
                        var selected = "";
                        var unid = "<option value='-1'>« TODOS »</option>\n";
			$.each(data, function(k, v){
                        //for (var i in data.func) {
                            selected = "";
                            if (v.id == "<?= $unidadeSel ?>") {
                                selected = "selected=\"selected\" ";
                            }
                            unid += "<option value='" + v.id + "' " + selected + ">" + v.nome + "</option>\n";
                        });
                        $("#func").html(unid);
                    }, 'json');
		});
            });
	    function formata_data(obj,prox) {
		switch (obj.value.length) {
			case 2:
				obj.value = obj.value + "/";
				break;
			case 5:
				obj.value = obj.value + "/";
				break;
			case 9:
				prox.focus();
				break;
		}
	    }
        </script>
    </body>
</html>