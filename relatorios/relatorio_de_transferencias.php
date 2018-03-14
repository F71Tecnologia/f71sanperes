<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../login.php?entre=true';</script>";
    exit;
}

if (!empty($_REQUEST['data_xls'])) {

    $dados = utf8_encode($_REQUEST['data_xls']);

    ob_end_clean();
    header("Content-Encoding: iso-8859-1");
    header("Pragma: private");
    header("Cache-control: private, must-revalidate");
    header("Expires: 0");
    header("Content-type: application/vnd.ms-excel");
//    header("Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
//    header("Content-type: application/xls");
    header("Content-Disposition: attachment; filename=relatorio_de_transferencias.xls");


    echo "\xEF\xBB\xBF";
    echo "<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:x='urn:schemas-microsoft-com:office:excel/' xmlns='http://www.w3.org/TR/REC-html40'>";
    echo "  <head>";
    echo "  <title>Relatório de Transferências</title>";
    echo "      <!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->";
    echo "  </head>";
    echo "  <body>";
    echo "      $dados";
    echo "  </body>";
    echo "</html>";
    exit;
}

include("../conn.php");
include("../classes/funcionario.php");
include("../wfunction.php");

$dataIni = implode('-', array_reverse(explode('/', $_REQUEST['dataIni'])));;
$dataFim = implode('-', array_reverse(explode('/', $_REQUEST['dataFim'])));;
$projetoSel = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : null;
$usuario = carregaUsuario();

$queryProj = mysql_query("SELECT id_projeto, nome FROM projeto WHERE id_regiao = {$usuario['id_regiao']}");
$projetoOpt[-1] = '-- Selecione --';
while ($rowProj = mysql_fetch_assoc($queryProj)) {    
    $projetoOpt[$rowProj['id_projeto']] = $rowProj['nome'];    
}

if (isset($_REQUEST['gerar'])) {
    $sql = "    SELECT 	A.id_clt, B.cpf, B.nome, DATE_FORMAT(A.data_transferencia, '%d/%m/%Y') data_transferencia, 
			IF(A.id_regiao_de != A.id_regiao_para,1,0) verifica_regiao,
			IF(A.id_projeto_de != A.id_projeto_para,1,0) verifica_projeto,
			IF(A.id_unidade_de != A.id_unidade_para,1,0) verifica_unidade,
			IF(A.id_horario_de != A.id_horario_para,1,0) verifica_horario,
			IF(A.id_sindicato_de != A.id_sindicato_para,1,0) verifica_sindicato,
			IF(A.id_curso_de != A.id_curso_para,1,0) verifica_curso,
			IF(A.id_tipo_pagamento_de != A.id_tipo_pagamento_para,1,0) verifica_tipopg,
			IF(A.id_banco_de != A.id_banco_para,1,0) verifica_banco,
                        C.regiao regiao_de, D.regiao regiao_para,
                        E.nome projeto_de, F.nome projeto_para, 
                        G.unidade unidade_de, H.unidade unidade_para, 
                        I.nome horario_de, J.nome horario_para,
                        K.nome sindicato_de, L.nome sindicato_para,
                        M.nome curso_de, N.nome curso_para,
                        FORMAT(M.valor,2,'pt_BR') valor_de, FORMAT(N.valor,2,'pt_BR') valor_para,
                        O.tipopg tipopg_de, P.tipopg tipopg_para,
                        IF(A.id_banco_de = '9999', 'Outro', IF(A.id_banco_de = '0', 'Nenhum Banco', Q.nome)) banco_de, IF(A.id_banco_para = '9999', 'Outro', IF(A.id_banco_para = '0', 'Nenhum Banco', R.nome)) banco_para
               FROM rh_transferencias A
               LEFT JOIN rh_clt B ON (A.id_clt = B.id_clt)
               LEFT JOIN regioes C ON (A.id_regiao_de = C.id_regiao) LEFT JOIN regioes D ON (A.id_regiao_para = D.id_regiao)
               LEFT JOIN projeto E ON (A.id_projeto_de = E.id_projeto) LEFT JOIN projeto F ON (A.id_projeto_para = F.id_projeto)
               LEFT JOIN unidade G ON (A.id_unidade_de = G.id_unidade) LEFT JOIN unidade H ON (A.id_unidade_para = H.id_unidade)
               LEFT JOIN rh_horarios I ON (A.id_horario_de = I.id_horario) LEFT JOIN rh_horarios J ON (A.id_horario_para = J.id_horario)
               LEFT JOIN rhsindicato K ON (A.id_sindicato_de = K.id_sindicato) LEFT JOIN rhsindicato L ON (A.id_sindicato_para = L.id_sindicato)
               LEFT JOIN curso M ON (A.id_curso_de = M.id_curso) LEFT JOIN curso N ON (A.id_curso_para = N.id_curso)
               LEFT JOIN tipopg O ON (A.id_tipo_pagamento_de = O.id_tipopg) LEFT JOIN tipopg P ON (A.id_tipo_pagamento_para = P.id_tipopg)
               LEFT JOIN bancos Q ON (A.id_banco_de = Q.id_banco) LEFT JOIN bancos R ON (A.id_banco_para = R.id_banco)
               WHERE A.id_regiao_para = {$usuario['id_regiao']} AND A.id_projeto_para = {$projetoSel} AND A.status = 1 AND (B.status < 60 || B.status = 200 || B.status = 70) AND DATE(A.data_transferencia) >= '$dataIni' AND DATE(A.data_transferencia) <= '$dataFim'
               ORDER BY A.data_transferencia";
    $query = mysql_query($sql);
    
    while ($row = mysql_fetch_assoc($query)) {
        
        $arr[] = $row;
        
    }
    
//    print_array($arr);
}

?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Relatório de Transferências</title>

        <link href="../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <!--<link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />-->
        <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">

    </head>
    <body>
        <?php include("../template/navbar_default.php"); ?>      
        <div class="container">
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - Participantes de Transferências</h2></div>
            <form action="" method="post" class="form-horizontal top-margin1" name="form" id="form">  
                <div class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print">Relatório</div>
                    <div class="panel-body">
                        <div class="form-group" >
                            <label for="select" class="col-sm-1 control-label hidden-print" >Projeto</label>
                            <div class="col-sm-3">
                                <?php echo montaSelect($projetoOpt, $projetoSel, array('name' => "projeto", 'id' => 'projeto', 'class' => 'form-control')); ?> <span class="loader"></span> 
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="select" class="col-sm-1 control-label hidden-print" >Período</label>
                            <div class="col-sm-2">
                                <input name="dataIni" class="data dataMask form-control" value="<?= $_REQUEST['dataIni'] ?>" placeholder="Data de Inicio..."/>
                            </div>
                            <label for="select" class="col-sm-1 control-label hidden-print" style="text-align:center">à</label>
                            <div class="col-sm-2">
                                <input name="dataFim" class="data dataMask form-control" value="<?= $_REQUEST['dataFim'] ?>" placeholder="Data de Fim..."/>
                            </div>
                        </div>
                    </div>

                    <div class="panel-footer text-right hidden-print controls">
                        <?php if (!empty($arr) && (isset($_POST['gerar'])) || isset($_REQUEST['todos_projetos'])) { ?>
                            <button type="button" onclick="tableToExcel('tbRelatorio', 'Participantes Ativos')" value="Exportar para Excel" class="btn btn-success" id="exportarExcel"><span class="fa fa-file-excel-o"></span> Exportar para Excel</button>
                            <button type="button" form="formPdf" name="pdf" data-title="Relatório de Participantes do Projeto" data-id="tbRelatorio" id="pdf" value="Gerar PDF" class="btn btn-danger"><i class="fa fa-file-pdf-o"></i> Gerar PDF</button>
                            <input type="hidden" id="data_xls" name="data_xls" value="">
                        <?php } ?>
                        <button type="submit" name="gerar" id="gerar" value="filtrar" class="btn btn-primary"><span class="fa fa-filter"></span> Filtrar</button>
                    </div>
                </div> 


                <?php if (!empty($arr) && (isset($_POST['gerar'])) || isset($_REQUEST['todos_projetos'])) { ?>
                    <div id="relatorio_exp">
                        <table class="table table-striped table-hover text-sm valign-middle table-bordered" id="tbRelatorio">
                            <thead>
                                <tr class="titulo">
                                    <th>NOME</th>
                                    <th>CPF</th>
                                    <th>COMPETÊNCIA</th>
                                    <th>ALTERAÇÃO</th>
                                </tr>
                            </thead>  
                            <?php foreach ($arr as $key => $value) { ?>

                                <tbody>
                                    <tr>
                                        <td><?= $value['nome'] ?></td>
                                        <td><?= $value['cpf'] ?></td>
                                        <td><?= $value['data_transferencia'] ?></td>
                                        <td> 
                                            <?php if ($value['verifica_regiao']) { ?>
                                                <strong>Região:</strong> <?= $value['regiao_de'] ?> para <?= $value['regiao_para'] ?>;<br/>
                                            <?php } ?>
                                            <?php if ($value['verifica_projeto']) { ?>
                                                <strong>Projeto:</strong> <?= $value['projeto_de'] ?> para <?= $value['projeto_para'] ?>;<br/>
                                            <?php } ?>
                                            <?php if ($value['verifica_unidade']) { ?>
                                                <strong>Unidade:</strong> <?= $value['unidade_de'] ?> para <?= $value['unidade_para'] ?>;<br/>
                                            <?php } ?>
                                            <?php if ($value['verifica_horario']) { ?>
                                                <strong>Horário:</strong> <?= $value['horario_de'] ?> para <?= $value['horario_para'] ?>;<br/>
                                            <?php } ?>
                                            <?php if ($value['verifica_sindicato']) { ?>
                                                <strong>Sindicato:</strong> <?= $value['sindicato_de'] ?> para <?= $value['sindicato_para'] ?>;<br/>
                                            <?php } ?>
                                            <?php if ($value['verifica_curso']) { ?>
                                                <strong>Função:</strong> <?= $value['curso_de'].' (R$ '.$value['valor_de'].')'; ?> para <?= $value['curso_para'].' (R$ '.$value['valor_para'].')' ?>;<br/>
                                            <?php } ?>
                                            <?php if ($value['verifica_tipopg']) { ?>
                                                <strong>Tipo de Pagamento:</strong> <?= $value['tipopg_de'] ?> para <?= (!empty($value['tipopg_para'])?$value['tipopg_para']:'Nenhum selecionado'); ?>;<br/>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                <?php } ?>
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
        <script src="../js/tableExport.jquery.plugin-master/tableExport.js" type="text/javascript"></script>
        <script src="../js/jquery.maskedinput-1.3.1.js"></script>
        <script>
                            $(function () {
                                
                                $(".dataMask").mask("99/99/9999");
                                
                                $("#exportarExcel").click(function (e) {
                                    $("#relatorio_exp img:last-child").remove();

                                    var html = $("#relatorio_exp").html();

                                    $("#data_xls").val(html);
                                    $("#form").submit();
                                });
                            });
        </script>

    </body>
</html>
<!-- A -->