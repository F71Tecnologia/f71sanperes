<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../login.php?entre=true';</script>";
    exit;
}

include("../conn.php");
include("../classes/funcionario.php");
include("../classes_permissoes/regioes.class.php");
include("../classes_permissoes/acoes.class.php");
include("../wfunction.php");

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$optRegiao = getRegioes();

$ACOES = new Acoes();

$mesesSel = (isset($_REQUEST['mes'])) ? $_REQUEST['mes'] : date('m');
$anoSel = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : date('Y');
$dt_ini = (isset($_REQUEST['data_ini'])) ? $_REQUEST['data_ini']:date("d/m/Y",  strtotime('-30 days'));
$dt_fim = (isset($_REQUEST['data_fim'])) ? $_REQUEST['data_fim']:date("d/m/Y");

$meses = mesesArray(null, '', "« Selecione o Mês »");
$anoOpt = anosArray(null, null, array('' => "« Selecione o Ano »"));

if (isset($_REQUEST['gerar']) || isset($_REQUEST['todos_projetos'])) {
    $regiao = $_REQUEST['regiao'];
    $projeto = $_REQUEST['projeto'];
    
    $sql = "SELECT A.id_clt, A.id_curso, A.nome, A.matricula, A.cpf, A.pis, A.tel_cel,
            DATE_FORMAT(A.data_entrada,'%d/%m/%Y') AS dt_admissao, E.nome AS projeto, /*C.letra AS
            funcao_letra, C.numero AS funcao_numero, */C.nome AS funcao, F.horas_mes, 
            IF(A.status < 60 || A.status = 200,'ATIVO','INATIVO') status_nome, 
            IF(A.data_saida != 0000-00-00, DATE_FORMAT(A.data_saida,'%d/%m/%Y'), 
            DATE_FORMAT(A.data_demi,'%d/%m/%Y')) dt_saida
            FROM rh_clt AS A
            LEFT JOIN curso AS C ON (A.id_curso = C.id_curso)
            LEFT JOIN regioes AS D ON (A.id_regiao = D.id_regiao)
            LEFT JOIN projeto AS E ON (A.id_projeto = E.id_projeto)
            LEFT JOIN rh_horarios AS F ON (A.rh_horario = F.id_horario)
            WHERE A.tipo_contratacao = 2 AND (A.status < 60 OR A.status = 200 OR A.status = 70) AND A.id_projeto = $projeto";
    
    if (!isset($_REQUEST['todos_projetos'])) {
        $sql .= " AND A.id_projeto = {$projeto} ";
    }
    $sql .= " ORDER BY A.nome";
    echo "<!-- SSQL: ".$sql." -->";
    $qr_relatorio = mysql_query($sql) or die(mysql_error());

}

$regiaoSel = (isset($_REQUEST['reg'])) ? $_REQUEST['reg'] : $usuario['id_regiao'];
$projetoSel = (isset($_REQUEST['pro'])) ? $_REQUEST['pro'] : null;

$regiaoSel = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $regiaoSel;
$projetoSel = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : $projetoSel;

$breadcrumb_config = array("nivel"=>"../", "key_btn"=>"4", "area"=>"Recursos Humanos", "id_form"=>"form1", "ativo"=>"Relatório de Admissões");
$breadcrumb_pages = array("Visualizar Projeto" => "../rh/ver.php");
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <title>:: Intranet :: Relatório Admissões</title>
        
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
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small> - Relatório de Admissões</small></h2></div>
            <form action="" method="post" class="form-horizontal top-margin1" name="form" id="form">
            
                <div class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print">Filtro</div>
                    <div class="panel-body">
                        <input type="hidden" name="hide_projeto" id="hide_projeto" value="<?php echo $projetoSel ?>" />
                        <div class="form-group">
                            <label for="select" class="col-sm-offset-1 col-sm-1 control-label hidden-print">Região</label>
                            <div class="col-sm-4">
                                <?php echo montaSelect($optRegiao, $regiaoSel, array('name' => "regiao", 'id' => 'regiao', 'class' => 'form-control')); ?>
                            </div>
                            <label for="select" class="col-sm-1 control-label hidden-print">Projeto</label>
                            <div class="col-sm-4">
                                <?php echo montaSelect($optProjeto, $projetoSel, array('name' => "projeto", 'id' => 'projeto', 'class' => 'validate[required] form-control')); ?><span class="loader"></span>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer text-right hidden-print">
                        <?php if (!empty($qr_relatorio) and ( isset($_POST['gerar']) || isset($_REQUEST['todos_projetos']))) { ?>
                            <button type="button" onclick="tableToExcel('tbRelatorio', 'Participantes Ativos')" class="btn btn-success"><span class="fa fa-file-excel-o"></span> Exportar para Excel</button>
                            <button type="button" form="formPdf" name="pdf" data-title="Relatório de Admissões" data-id="tbRelatorio" id="pdf" value="Gerar PDF" class="btn btn-danger"><i class="fa fa-file-pdf-o"></i> Gerar PDF</button>
                        <?php } ?>
                            <button type="submit" name="gerar" id="gerar" value="Gerar" class="btn btn-primary"><span class="fa fa-filter"></span> Gerar</button>
                    </div>
                </div>
            </form>
            
            <?php
            if (!empty($qr_relatorio) and ( isset($_POST['gerar']) || isset($_REQUEST['todos_projetos']))) { ?>
                <table class="table table-condensed table-bordered text-sm valign-middle" id="tbRelatorio">
                    <thead>
                        <tr>
                            <th>PROJETO</th>
                            <th>NOME</th>
                            <th>FUNÇÃO</th>
                           <!-- <th>SALARIO</th> 
                            <th>CARGA HORÁRIA</th> -->
                            <th>DATA DE ADMISSÃO</th>
                            <th>DATA DE SAÍDA</th>
                             <th>STATUS</th> 
                        </tr>               
                    </thead>
                    <tbody>
                    <?php while ($row_rel = mysql_fetch_assoc($qr_relatorio)) { 
                       /* $salarioInicial = null;
                        $qrsalario = "select * from rh_salario where id_curso = '{$row_rel['id_curso']}' order by data desc limit 1";
                        $rssalario = mysql_query($qrsalario);
                        $salarioAntigo = mysql_fetch_array($rssalario);
                        $salario1 = $salarioAntigo['salario_novo'];*/
                        //echo $salario1;
                        //$totalHistorico = mysql_num_rows($rssalario);
/*
                        if ($salarioAntigo['salario_antigo'] == '0' or $salarioAntigo['salario_antigo'] == '1'){
                            $salario1 = $salarioAntigo['salario_novo'];
                        } else {
                            $salario1 = $salarioAntigo['salario_antigo'];
                        }
                        if($totalHistorico == 0){
                                                    $salario1 = $row_curso['salario'];
                                                }

                        $salarioInicial = $salario1; */
                        $nomeCursoInicial = $row_curso['nome'];?>

                        <tr>
                            <td><?php echo $row_rel['projeto'] ?></td>
                            <td><?php echo $row_rel['nome'] ?></td>
                            <td align=""><?php echo $row_rel['funcao']." ".$row_rel['funcao_letra'].$row_rel['funcao_numero'] ?></td>
                            <!-- <td align="center"><?php //echo number_format($salarioInicial,2,',','.') ?></td> 
                            <td align="center"><?php //echo $row_rel['horas_mes'] ?></td> -->
                            <td align="center"><?php echo $row_rel['dt_admissao'] ?></td>
                            <td align="center"><?php echo $row_rel['dt_saida'] ?></td>
                             <td align="center"><?php echo $row_rel['status_nome'] ?></td> 
                        </tr>                                
                    <?php unset($total_mov); } ?>
                    </tbody>
                </table>
            <?php } ?>  
            <?php include('../template/footer.php'); ?>
            <div class="clear"></div>
        </div>
        
        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/tooltip.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/global.js"></script>
        <script src="../js/jquery.validationEngine-2.6.js" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine-pt_BR-2.6.js" type="text/javascript"></script>
        <script src="../js/jquery.ui.datepicker-pt-BR.js" type="text/javascript"></script>
        <script src="../js/jquery.form.js" type="text/javascript"></script>
        
        <script>
            $(function() {
                
                $('.data').datepicker({
                    dateFormat: 'dd/mm/yy',
                    changeMonth: true,
                    changeYear: true,
                    yearRange: '2005:c+1'
                });
                
                $("#form1").validationEngine();
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

            });
        </script>
    
    </body>
</html>