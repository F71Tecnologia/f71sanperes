<?php
/*
 * relatorio_corpo_clinico.php
 * 
 * ??-??-????
 * 
 * Tela de relatório para listagem de corpo clínico
 * 
 * Versão: 3.0.5175 - 21/12/2015 - Jacques - Excluída a opção por período para geração data fim ao definir data para seleção de registros
 * 
 * @author Não Definido
 * 
 */
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../login.php?entre=true';</script>";
    exit;
}
session_start();
/* include('sintetica/cabecalho_folha.php'); */
include_once("../conn.php");
include_once("../funcoes.php");
include_once("../wfunction.php");

if(!empty($_REQUEST['regiao'])){
    
    $sql = "SELECT * FROM projeto WHERE id_regiao = {$_REQUEST['regiao']} ORDER BY id_projeto";
    $qry = mysql_query($sql)or die(mysql_error());
    $r = '<option value="">Todos</option>';
    while($row = mysql_fetch_assoc($qry)) { 
        $aux = ($row['id_projeto'] == $_REQUEST['projeto']) ? 'selected' : null;
        $r .= '<option value="'.$row['id_projeto'].'" '.$aux.' >'.$row['id_projeto'].' - '.utf8_encode($row['nome']).'</option>';
    } 
    echo $r;
    exit;
}

$usuario = carregaUsuario();

$id_regiao = (!empty($_REQUEST['id_regiao'])) ? $_REQUEST['id_regiao'] : $usuario['id_regiao'];
$id_projeto = (!empty($_REQUEST['id_projeto'])) ? $_REQUEST['id_projeto'] : 0;
$inicial = (!empty($_REQUEST['inicial'])) ? implode('-', array_reverse(explode('/', $_REQUEST['inicial']))) : null;
$final = (!empty($_REQUEST['final'])) ? implode('-', array_reverse(explode('/', $_REQUEST['final']))) : null;
$status = (!empty($_REQUEST['status'])) ? $_REQUEST['status'] : 1;

$auxStatus = ($status == 1) ? ' AND (A.status < 60 || A.status = 200) ' : ' AND (A.status >= 60 && A.status != 200) ';
$auxRegiao = ($id_regiao > 0) ? " AND A.id_regiao = $id_regiao " : '';
$auxProjeto = ($id_projeto > 0) ? " AND A.id_projeto = $id_projeto " : '';

if(isset($_REQUEST['id_regiao'])){
    $sql_clt = "
    SELECT A.nome, A.conselho, A.status 
    FROM rh_clt A LEFT JOIN curso B ON (A.id_curso = B.id_curso) 
    WHERE B.cbo_codigo IN(5433,5410,5489,5462,5494,5436,5425,5426,2987,2894) AND A.data_entrada <=  '$final' $auxRegiao $auxProjeto $auxStatus
    ORDER BY A.nome";
    
    $query_clt = mysql_query($sql_clt);
    
    $sql_master = "
    SELECT * FROM master WHERE id_master = {$usuario['id_master']}";
    $query_master = mysql_fetch_assoc(mysql_query($sql_master));
}

$dadosHeader        = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$breadcrumb_config  = array("nivel" => "../", "key_btn" => "3", "area" => "Recursos Humanos", "id_form" => "form1", "ativo" => "Corpo Clinico");
$breadcrumb_pages   = array("Gestão de RH" => "../principalrh.php");
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Corpo Clinico</title>
        <link href="../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../resources/css/bootstrap-note.css" rel="stylesheet" media="all">
        <link href="../resources/css/main.css" rel="stylesheet" media="all">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="all">
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" >
        <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="all">
        <link href="../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
    </head>
    <body>
        <?php include("../template/navbar_default.php"); ?>
        <div class="container">
            <div class="row hidden-print">
                <div class="col-sm-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small> - Corpo Clinico</small></h2></div>
                </div><!-- /.col-sm-12 -->
            </div><!-- /.row -->
            <form action="" id="form1" name="form1" method="post" class="form-horizontal">
                <div class="panel panel-default hidden-print">
                    <div class="panel-body">
                        <div class="form-group">
                            <label class="col-sm-1 col-sm-offset-1 control-label">Região:</label>
                            <div class="col-sm-4">
                                <?= montaSelect(getRegioes(null,null,0), $id_regiao, 'class="form-control" name="id_regiao" id="id_regiao"'); ?>
                            </div>
                            <label class="col-sm-1 control-label">Projeto:</label>
                            <div class="col-sm-4">
                                <select class="form-control" name="id_projeto" id="id_projeto"></select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-1 col-sm-offset-1 control-label">Período:</label>
                            <div class="col-sm-4">
                                <div class="input-group">
                                    <input type="hidden" class="data form-control text-center" name="inicial" style="cursor: pointer; background-color: #FFF;" value="<?=implode('/', array_reverse(explode('-', $inicial)))?>">
                                    <div class="input-group-addon">até</div>
                                    <input type="text" class="data form-control text-center" name="final" style="cursor: pointer; background-color: #FFF;" value="<?=implode('/', array_reverse(explode('-', $final)))?>">
                                </div>
                            </div>
                            <label class="col-sm-1 control-label">Status:</label>
                            <div class="col-sm-4">
                                <?= montaSelect(array('1' => 'Ativo', '2' => 'Inativo'), $status, 'class="form-control" name="status"'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer text-right">
                        <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Gerar</button>
                    </div>
                </div>
            </form>
            <?php if(isset($_REQUEST['id_regiao']) && mysql_num_rows($query_clt) > 0){ ?>
            <p class="hidden-print"><input type="button" onclick="tableToExcel('tbRelatorio', 'Participantes Movimentos Folha')" value="Exportar para Excel" class="btn btn-success"></p>    
            <table class="table table-condensed table-hover table-striped table-bordered text-sm valign-middle" id="tbRelatorio">
                <thead>
                    <tr class="no-border">
                        <th class="no-border text-center" colspan="7">
                            <img src="../imagens/conselho.png" style="width: 150px; margin-left: 20px;">
                            <span class="text-left" style="float: right; background-color: #FFF; width: 150px; margin-top: 50px; margin-right: 20px; border: 1px solid #000; padding: 5px;">
                                Nº de Registro:<br>
                                52&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - PJ
                            </span>
                        </th>
                    </tr>
                    <tr class="no-border"><th class="no-border" colspan="7">VIII - CORPO CLÍNICO DO ESTABELECIMENTO</th></tr>
                    <tr class="no-border"><td class="no-border" colspan="7">NOME DO ESTABELECIMENTO: <strong><?=$query_master['razao']?></strong></td></tr>
                    <tr class="no-border"><td class="no-border" colspan="7">RESPONSÁVEL TÉCNICO</td></tr>
                    <tr class="no-border"><th class="no-border" colspan="4">Nome: </th><th class="no-border" colspan="3">CRM: </th></tr>
                    <tr class="no-border"><th colspan="7">SOMENTE RELACIONAR OS MÉDICOS E IDENTIFICAR COM O "X" OS CAMPOS ABAIXO, INCLUSIVE OS CAMPOS DE INCLUIR E EXCLUIR MÉDICOS</th></tr>
                    <tr class="bg-dark-gray">
                        <th class="valign-middle text-center">CRM</th>
                        <th class="valign-middle text-center">NOME</th>
                        <th class="valign-middle text-center">C. Clin.</th>
                        <th class="valign-middle text-center">Sócio</th>
                        <th class="valign-middle text-center">Diretor</th>
                        <th class="valign-middle text-center">Incluir</th>
                        <th class="valign-middle text-center">Excluir</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while($row_clt = mysql_fetch_assoc($query_clt)) { ?>
                        <tr>
                            <td class="" style="width: 100px;"><?= substr(preg_replace("/\D/","", $row_clt['conselho']),0,2).'-'.substr(preg_replace("/\D/","", $row_clt['conselho']),2) ?></td>
                            <td class=""><?= $row_clt['nome'] ?></td>
                            <td class="text-center">X</td>
                            <td class="text-center"><?= $row_clt[''] ?></td>
                            <td class="text-center"><?= $row_clt[''] ?></td>
                            <td class="text-center"><?= ($row_clt['status'] < 60 || $row_clt['status'] == 200) ? 'X' : '' ?></td>
                            <td class="text-center"><?= ($row_clt['status'] >= 60 && $row_clt['status'] != 200) ? 'X' : ''  ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
                <tr class="no-border"><th class="no-border" colspan="7">O MÉDICO RESPONSÁVEL, ABAIXO ASSINADO, ASSUME PERANTE O CONSELHO REGIONAL DE MEDICINA DO ESTADO DO RIO DE JANEIRO, A INTEIRA RESPONSABILIDADE PELOS DADOS DECLARADOS NESTE DOCUMENTO.</th></tr>
                <tr class="no-border">
                    <td class="no-border" colspan="7">
                        <table class="no-border" border="0">
                            <tr class="no-border" >
                                <td class="no-border" style="width: 400px;">
                                    Local:
                                </td>
                                <td class="no-border" style="width: 300px;">
                                    Data:
                                </td>
                                <td class="no-border" style="width: 300px; text-align: center; padding: 25px 0px 25px 0px; border: none;">
                                    _______________________________________<br>(Assinatura do diretor Técnico)
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <?php } else { ?>
            <div id="message-box" class="alert alert-warning">
                <span class="fa fa-exclamation-triangle"></span> Nenhum médico encontrado neste período
            </div>
            <?php } ?>
            <?php //include_once '../template/footer.php'; ?>
        </div>
        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../js/jquery.validationEngine-2.6.js"></script>
        <script src="../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../js/jquery.mask.min.js" type="text/javascript"></script>
        <script src="../js/jquery.maskMoney.js" type="text/javascript" ></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/global.js"></script>
        <script src="../jquery/priceFormat.js" type="text/javascript"></script>
        <script>
        $(function(){
            $('body').on('change', '#id_regiao', function(){
                $.post("", {bugger:Math.random(), regiao:$(this).val(), projeto:<?=$id_projeto?>}, function(resposta){
                    //console.log(resposta);
                    $("#id_projeto").html(resposta);
                });
            });
            $('#id_regiao').trigger('change');
        });
        </script>
    </body>
</html>