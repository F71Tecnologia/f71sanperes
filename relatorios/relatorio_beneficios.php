<?php
if(empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="../login.php">Logar</a>';
    exit;
}

include('../conn.php');
include('../wfunction.php');
include("../classes/ValeTransporteClass.php");
include("../classes/global.php");

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__);
$global = new GlobalClass();

$breadcrumb_config = array("nivel"=>"../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form-lista", "ativo"=>"Relatório de Participantes");
$breadcrumb_pages = array("Gestão de RH"=>"/intranet/rh/principalrh.php");

$objTransporte = new ValeTransporteClass();

$filtro = $_REQUEST['filtrar'];

if(isset($filtro)){
    $projeto = $_REQUEST['projeto'];
    $detalhado = $_REQUEST['vt_detalhado'];
    
    if($projeto > '0'){
        $projeto_sql = "AND a.id_projeto = {$projeto}";
    }else{
        $projeto_sql = null;
    }
    
    if($detalhado){
        $chek_detalhado = "checked";
    }
    
    /*
    * CONDICAO PARA INSTITUCIONAL
    * PEGAR DIRETO DO SINDICATO
    * CONDIÇÃO 
    */
    if($projeto == 1){
        $valor_vr = "IF(d.valor_refeicao > 0, d.valor_refeicao, a.valor_refeicao)";
    }else{
        $valor_vr = "IF(a.valor_refeicao > 0, a.valor_refeicao, IF(b.valor_refeicao > 0, b.valor_refeicao, d.valor_refeicao))";
    }
    
    $sql_beneficios = "SELECT a.id_clt, a.nome, a.matricula, a.matricula_sodexo, a.data_nasci, a.data_entrada, a.cpf, a.rg, a.id_curso, b.nome AS nome_funcao, a.id_unidade, a.status, a.mae,
        c.unidade AS nome_unidade, d.id_sindicato AS sindicato, e.horas_semanais, f.especifica AS nome_status,
        {$valor_vr} AS valor_vr, d.valor_alimentacao AS valor_va
        FROM rh_clt AS a
        INNER JOIN curso AS b ON a.id_curso = b.id_curso
        INNER JOIN unidade AS c ON a.id_unidade = c.id_unidade
        INNER JOIN rhsindicato AS d ON a.rh_sindicato = d.id_sindicato
        INNER JOIN rh_horarios AS e ON a.rh_horario = e.id_horario
        INNER JOIN rhstatus AS f ON a.status = f.codigo
        WHERE (a.status < 60 OR a.status = 70) {$projeto_sql}
        ORDER BY c.unidade, a.nome";
    $qr_beneficios = mysql_query($sql_beneficios);
    $total_beneficios = mysql_num_rows($qr_beneficios);            
    
    $sql_beneficiosVT = "SELECT a.id_clt, a.nome, a.matricula, a.matricula_sodexo, a.data_nasci, a.data_entrada, a.cpf, a.rg, a.id_curso, b.nome AS nome_funcao, a.id_unidade, a.status, a.mae,
        c.unidade AS nome_unidade, d.id_sindicato AS sindicato, e.horas_semanais, f.especifica AS nome_status, c.endereco AS endereco_unidade, CONCAT(a.endereco, ' ', a.numero, ', ', a.bairro, ', ', a.cidade, ' - ', a.uf) AS endereco_casa,
        {$valor_vr} AS valor_vr, d.valor_alimentacao AS valor_va
        FROM rh_clt AS a
        INNER JOIN curso AS b ON a.id_curso = b.id_curso
        INNER JOIN unidade AS c ON a.id_unidade = c.id_unidade
        INNER JOIN rhsindicato AS d ON a.rh_sindicato = d.id_sindicato
        INNER JOIN rh_horarios AS e ON a.rh_horario = e.id_horario
        INNER JOIN rhstatus AS f ON a.status = f.codigo
        WHERE (a.status < 60 OR a.status = 70) AND a.transporte = 1 {$projeto_sql}
        ORDER BY c.unidade, a.nome";
    $qr_beneficiosVT = mysql_query($sql_beneficiosVT);        
}
?>
<!doctype html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <title>Relatório de Benefícios</title>
        <!-- Bootstrap -->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../resources/css/bootstrap-note.css" rel="stylesheet" media="screen">
        <link href="../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../resources/css/main.css" rel="stylesheet" media="all">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="all">
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" >
        <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="all">
        <link href="../css/progress.css" rel="stylesheet" type="text/css">
        <link href="../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <script src="../js/jquery-1.10.2.min.js"></script>
    </head>
    <body>
	<?php include("../template/navbar_default.php"); ?>
        
        <div class="<?=($container_full) ? 'container-full' : 'container'?>">
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS<small> - Relatório de Participantes</small></h2></div>
            
            <form action="" method="post" class="form-horizontal top-margin1" name="form1" id="form1">                                                
                <div class="panel panel-default hidden-print">
                    <div class="panel-heading">Relatório de Benefícios</div>
                    <div class="panel-body">
                        <div class="form-group">                            
                            <label for="select" class="col-sm-2 control-label hidden-print" >Projeto</label>
                            <div class="col-sm-5">
                                <?php echo montaSelect($global->carregaProjetosByRegiao($usuario['id_regiao'], $default = array("-1" => "« Todos os Projetos »")), $projeto, "id='projeto' name='projeto' class='required[custom[select]] form-control'"); ?>
                            </div>
                            
                            <!--<label for="select" class="col-sm-2 control-label hidden-print" >VT Detalhado:</label>-->
                            <div class="col-sm-3">
                                <div class="input-group">
                                    <label class="input-group-addon pointer" for="vt_detalhado">
                                <input type="checkbox" name="vt_detalhado" id="vt_detalhado" value="1" <?php echo $chek_detalhado; ?> />
                                    </label>
                                    <label class="form-control pointer" for="vt_detalhado">VT Detalhado</label>
                                </div>
                            </div>                                               
                    </div>
                    <div class="panel-footer text-right">
                        <?php if(!empty($filtro) && (!empty($total_beneficios)) || (isset($_POST['filtrar']))) { ?>
                            <button type="button" value="exportar para excel" onclick="tableToExcel('table_excel', 'Inss')" class="btn btn-success"><span class="fa fa-file-excel-o"></span> Exportar para Excel</button>
                            <button type="button" form="formPdf" name="pdf" data-title="Relatório de Desconto de FGTS" data-id="table_excel" id="pdf" value="Gerar PDF" class="btn btn-danger"><i class="fa fa-file-pdf-o"></i> Gerar PDF</button>
                        <?php } ?>
                            <button type="submit" name="filtrar" id="filt" value="Filtrar" class="btn btn-primary"><span class="fa fa-filter"></span> Filtrar</button>
                    </div>
                </div>
                </div>
                
                <?php
                if($filtro) {
                    if($total_beneficios > 0) {
                        if(!$detalhado){
                ?>                
                
                <table class="table table-bordered table-hover table-condensed text-sm valign-middle" id="table_excel">
                    <thead>                    
                        <tr>
                            <th>Matrícula</th>
                            <th>Matrícula Sodexo</th>
                            <th>Nome</th>
                            <th>CPF</th>
                            <th>Situação</th>
                            <th class="sorter-shortDate dateFormat-ddmmyyyy">Entrada</th>
                            <th class="sorter-shortDate dateFormat-ddmmyyyy">Nascimento</th>
                            <th>Função</th>
                            <th>Unidade</th>
                            <th>VA</th>
                            <th>VR</th>
                            <th>VT</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row_beneficios = mysql_fetch_assoc($qr_beneficios)) { ?>
                        <tr class="linhasParticipantes">
                            <td><?php echo $row_beneficios['matricula']; ?></td>
                            <td><?php echo $row_beneficios['matricula_sodexo']; ?></td>
                            <td><?php echo $row_beneficios['nome']; ?></td>
                            <td><?php echo $row_beneficios['cpf']; ?></td>
                            <td>
                                <?php
                                if ($row_beneficios['status'] != 10) {
                                    echo "<span class='label label-warning'>{$row_beneficios['nome_status']}</span>";
                                } else {
                                    echo $row_beneficios['nome_status'];
                                }
                                ?>
                            </td>
                            <td><?php echo converteData($row_beneficios['data_entrada'], 'd/m/Y'); ?></td>
                            <td><?php echo converteData($row_beneficios['data_nasci'], 'd/m/Y'); ?></td>
                            <td><?php echo $row_beneficios['nome_funcao']; ?></td>
                            <td><?php echo $row_beneficios['nome_unidade']; ?></td>
                            <td><?php echo formataMoeda($row_beneficios['valor_va'], 1); ?></td>
                            <td><?php echo formataMoeda($row_beneficios['valor_vr'], 1); ?></td>
                            <td>
                                <a href="javascript:;" class="ver_vt" data-key="<?php echo $row_beneficios['id_clt']; ?>">
                                    <?php echo formataMoeda($objTransporte->getValorDia($row_beneficios['id_clt']), 1); ?>
                                </a>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
<!--                    <tfoot>
                        <tr class='danger'>
                            <td class='text-right' colspan='3'>Total Geral:</td>
                            <td><?php echo number_format($matrizTotalGeral['base_inss'] - $matrizTotalGeral['base_n_incide'] + $matrizTotalGeral['base_fgts_acerto'], 2, ',', '.');  ?></td>
                            <td><?php echo number_format(($matrizTotalGeral['base_inss'] - $matrizTotalGeral['base_n_incide'] + $matrizTotalGeral['base_fgts_acerto']) * 0.08, 2, ',', '.');  ?></td>
                        </tr>
                    </tfoot>-->
                </table>  
                <?php }else{ ?>
<!--                <p class="pull-right">
                    <button type="button" value="exportar para excel" onclick="tableToExcel('table_excel', 'Inss')" class="btn btn-success"><span class="fa fa-file-excel-o"></span> Exportar para Excel</button>
                    <button type="button" form="formPdf" name="pdf" data-title="Relatório de Desconto de FGTS" data-id="table_excel" id="pdf" value="Gerar PDF" class="btn btn-danger"><i class="fa fa-file-pdf-o"></i> Gerar PDF</button>
                </p>-->
                <table class="table table-bordered table-hover table-condensed text-sm valign-middle" id="table_excel">
                    <thead>                    
                        <tr>
                            <th>Nome</th>
                            <th>CPF</th>                            
                            <th>RG</th>                            
                            <th class="sorter-shortDate dateFormat-ddmmyyyy">Nascimento</th>
                            <th>Mãe</th>                            
                            <th>Itinerário</th>
                            <th>Linhas</th>
                            <th>VT/Dia</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $unidade = "";
                        
                        while($row_beneficios = mysql_fetch_assoc($qr_beneficiosVT)) { 
                        ?>
                        <?php                                                  
                        if($unidade != $row_beneficios['nome_unidade']){
                            $unidade = $row_beneficios['nome_unidade'];
                            echo "<tr class='text-center'><td colspan='8'>{$unidade}</td></tr>";
                        }
                        ?>
                        <tr class="linhasParticipantes">
                            <td><?php echo $row_beneficios['nome']; ?></td>
                            <td><?php echo $row_beneficios['cpf']; ?></td>
                            <td><?php echo $row_beneficios['rg']; ?></td>
                            <td><?php echo converteData($row_beneficios['data_nasci'], 'd/m/Y'); ?></td>
                            <td><?php echo $row_beneficios['mae']; ?></td>                            
                            <td>
                                <strong>Unidade: </strong><?php echo $row_beneficios['endereco_unidade']; ?><br>
                                <strong>Casa: </strong><?php echo $row_beneficios['endereco_casa']; ?>
                            </td>
                            <td>
                                <?php
                                $res_detalhesVT = $objTransporte->getInfoVt($row_beneficios['id_clt']);
                                                                                                
                                $linha1 = (!empty($res_detalhesVT['linha1'])) ? $res_detalhesVT['linha1'] : "Não Informada";
                                $valor1 = formataMoeda($res_detalhesVT['valor1'], 1);
                                $linha2 = (!empty($res_detalhesVT['linha2'])) ? $res_detalhesVT['linha2'] : "Não Informada";
                                $valor2 = formataMoeda($res_detalhesVT['valor2'], 1);
                                $linha3 = (!empty($res_detalhesVT['linha3'])) ? $res_detalhesVT['linha3'] : "Não Informada";
                                $valor3 = formataMoeda($res_detalhesVT['valor3'], 1);
                                $linha4 = (!empty($res_detalhesVT['linha4'])) ? $res_detalhesVT['linha4'] : "Não Informada";
                                $valor4 = formataMoeda($res_detalhesVT['valor4'], 1);
                                $linha5 = (!empty($res_detalhesVT['linha5'])) ? $res_detalhesVT['linha5'] : "Não Informada";
                                $valor5 = formataMoeda($res_detalhesVT['valor5'], 1);
                                $linha6 = (!empty($res_detalhesVT['linha6'])) ? $res_detalhesVT['linha6'] : "Não Informada";
                                $valor6 = formataMoeda($res_detalhesVT['valor6'], 1);
                                
//                                echo "<b>Linha 1:</b> {$linha1} <b>Valor:</b> {$valor1}<br>";
//                                echo "<b>Linha 2:</b> {$linha1} <b>Valor:</b> {$valor1}<br>";
//                                echo "<b>Linha 3:</b> {$linha1} <b>Valor:</b> {$valor1}<br>";
//                                echo "<b>Linha 4:</b> {$linha1} <b>Valor:</b> {$valor1}<br>";
//                                echo "<b>Linha 5:</b> {$linha1} <b>Valor:</b> {$valor1}<br>";
//                                echo "<b>Linha 6:</b> {$linha1} <b>Valor:</b> {$valor1}";
                                ?>
                                <table class="table text-sm valign-middle">
                                    <tbody>
                                        <?php if(($linha1 != "Não Informada") || ($valor1 > 0)){ ?>
                                        <tr>
                                            <td><b>Linha 1:</b><br> <?php echo $linha1; ?></td>
                                            <td><b>Valor 1:</b><br> <?php echo $valor1; ?></td>
                                        </tr>
                                        <?php } ?>
                                        
                                        <?php if(($linha2 != "Não Informada") || ($valor2 > 0)){ ?>
                                        <tr>
                                            <td><b>Linha 2:</b><br> <?php echo $linha2; ?></td>
                                            <td><b>Valor 2:</b><br> <?php echo $valor2; ?></td>
                                        </tr>
                                        <?php } ?>
                                        
                                        <?php if(($linha3 != "Não Informada") || ($valor3 > 0)){ ?>
                                        <tr>
                                            <td><b>Linha 3:</b><br> <?php echo $linha3; ?></td>
                                            <td><b>Valor 3:</b><br> <?php echo $valor3; ?></td>
                                        </tr>
                                        <?php } ?>
                                        
                                        <?php if(($linha4 != "Não Informada") || ($valor4 > 0)){ ?>
                                        <tr>
                                            <td><b>Linha 4:</b><br> <?php echo $linha4; ?></td>
                                            <td><b>Valor 4:</b><br> <?php echo $valor4; ?></td>
                                        </tr>
                                        <?php } ?>
                                        
                                        <?php if(($linha5 != "Não Informada") || ($valor5 > 0)){ ?>
                                        <tr>
                                            <td><b>Linha 5:</b><br> <?php echo $linha5; ?></td>
                                            <td><b>Valor 5:</b><br> <?php echo $valor5; ?></td>
                                        </tr>
                                        <?php } ?>
                                        
                                        <?php if(($linha6 != "Não Informada") || ($valor6 > 0)){ ?>
                                        <tr>
                                            <td><b>Linha 6:</b><br> <?php echo $linha6; ?></td>
                                            <td><b>Valor 6:</b><br> <?php echo $valor6; ?></td>
                                        </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </td>
                            <td><?php echo formataMoeda($objTransporte->getValorDia($row_beneficios['id_clt']), 1); ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <?php } ?>
                
                <?php } else { ?>
                    <div class="alert alert-danger top30">                    
                        Nenhum registro encontrado
                    </div>
                <?php }
                } ?>        
            </form>
            
            <?php include('../template/footer.php'); ?>
        </div>
    
        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../resources/js/tooltip.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../jquery/tablesorte/jquery.tablesorter.js"></script>
        <script src="../js/global.js"></script>
        <script>
        $(function () {            
            $("table").tablesorter({
                dateFormat : "mmddyyyy", // set the default date format
                
                // or to change the format for specific columns, add the dateFormat to the headers option:
                headers: {
                    0: { sorter: "shortDate" } //, dateFormat will parsed as the default above
                    // 1: { sorter: "shortDate", dateFormat: "ddmmyyyy" }, // set day first format; set using class names
                    // 2: { sorter: "shortDate", dateFormat: "yyyymmdd" }  // set year first format; set using data attributes (jQuery data)
                }
            });
            
            $(".ver_vt").click(function(){
                var key = $(this).data("key");
                
                $.post("ver_vt.php", {id: key}, function(data){
                    bootDialog(data,'Visualização de VT', true);
                });
            });
        });
        </script>
    </body>
</html>