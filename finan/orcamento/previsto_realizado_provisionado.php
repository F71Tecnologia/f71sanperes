<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
}

include("../../conn.php");
include("../../funcoes.php");
include("../../wfunction.php");
include("../../classes_permissoes/acoes.class.php");

$acoes = new Acoes();
$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

/**
 * MONTA SELECT DAS UNIDADES PELO PROJETO
 */
if(isset($_REQUEST['method']) && $_REQUEST['method'] == 'unidades'){
    $sqlUnidades = mysql_query("SELECT id_unidade, unidade FROM unidade WHERE campo1 = {$_REQUEST['id_projeto']} ORDER BY unidade");
    $arrayUnidades = array("" => "-- TODAS --");
    while ($rowUnidades = mysql_fetch_assoc($sqlUnidades)) {
        $arrayUnidades[$rowUnidades['id_unidade']] = $rowUnidades['id_unidade'] . " - " . utf8_encode($rowUnidades['unidade']);
    }
    echo montaSelect($arrayUnidades, $_REQUEST['id_unidade'], "class='form-control validate[required]' id='id_unidade' name='id_unidade'");
    exit;
}

/**
 * MONTA SELECT DOS PROJETO
 */
$sqlProjetos = mysql_query("SELECT id_projeto, nome FROM projetos ORDER BY id_projeto");
$arrayProjetos = array("" => "-- TODOS --");
while ($rowProjetos = mysql_fetch_assoc($sqlProjetos)) {
    $arrayProjetos[$rowProjetos['id_projeto']] = $rowProjetos['id_projeto'] . " - " . utf8_encode($rowProjetos['nome']);
}


$mes = ($_REQUEST['mes']) ? $_REQUEST['mes'] : date('m');
$ano = ($_REQUEST['ano']) ? $_REQUEST['ano'] : date('Y');

if(isset($_REQUEST['filtrar'])){
    
    $auxProjeto = (!empty($_REQUEST['id_projeto'])) ? " AND B.id_unidade IN (SELECT id_unidade FROM unidade WHERE campo1 = {$_REQUEST['id_projeto']}) " : null; 
    $auxUnidade = (!empty($_REQUEST['id_unidade'])) ?" AND B.id_unidade = {$_REQUEST['id_unidade']}" : null; 
    
    /**
     * QUERY PARA AS SAIDAS
     */
    $sql = "
    SELECT E.id_grupo, LPAD(E.numero,2,'0') AS numero, E.nome_grupo, D.id_subgrupo, D.nome nome_subgrupo, SUM(A.valor) valor, A.status
    FROM saida A
    INNER JOIN saida_unidade B ON (A.id_saida = B.id_saida)
    LEFT JOIN entradaesaida C ON (A.tipo = C.id_entradasaida)
    LEFT JOIN entradaesaida_subgrupo D ON (D.id_subgrupo = SUBSTR(C.cod,1,5))
    LEFT JOIN entradaesaida_grupo E ON (E.id_grupo = D.entradaesaida_grupo)
    WHERE A.`status` NOT IN (0) AND A.ano_competencia = {$ano} AND A.mes_competencia = {$mes}
    $auxUnidade $auxProjeto
    GROUP BY E.id_grupo, D.id_subgrupo, status
    ORDER BY E.id_grupo, D.id_subgrupo;";
    $qry = mysql_query($sql);
    while($row = mysql_fetch_assoc($qry)){
        $arraySubGrupos[$row['numero']][$row['id_subgrupo']][$row['status']] = $row['valor'];
        $arraySubGrupos[$row['numero']][$row['id_subgrupo']]['descricao'] = $row['nome_subgrupo'];
        $arrayGrupos[$row['numero']][$row['status']] += $row['valor'];
        $arrayGrupos[$row['numero']]['descricao'] = $row['nome_grupo'];
    }
    
    /**
     * QUERY PARA OS ORÇAMENTOS
     */
    $sqlOrcamento = "
    SELECT C.codigo, SUM(C.valor) AS valor, C.mes
    FROM gestao_orcamentos A
    LEFT JOIN gestao_orcamentos_unidades_associativas B ON (A.id = B.id_orcamento)
    LEFT JOIN gestao_orcamentos_valores C ON (A.id = C.orcamento_id)
    WHERE DATE_FORMAT(ADDDATE(A.inicio, INTERVAL (C.mes-1) MONTH),'%m%Y') = DATE_FORMAT('{$ano}-{$mes}-01','%m%Y')
    $auxUnidade
    GROUP BY C.codigo";
    $qryOrcamento = mysql_query($sqlOrcamento);
    while($rowOrcamento = mysql_fetch_assoc($qryOrcamento)){
        $arrayOrcamento[$rowOrcamento['codigo']] = $rowOrcamento['valor'];
    }
}
//print_array($arrayOrcamento);

$nome_pagina = "RELATÓRIO PREVISTO, REALIZADO E PROVISIONADO";
$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"4", "area"=>"Financeiro", "id_form"=>"form1", "ativo"=>"$nome_pagina");
$breadcrumb_pages = array("Gestão de Orçamentos"=>"index.php");

?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: <?= $nome_pagina ?></title>
        <link href="../../favicon.png" rel="shortcut icon" />
        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <!--<link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">-->
        <!--<link href="../../resources/css/bootstrap-note.css" rel="stylesheet" media="screen">-->
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../resources/dropzone/dropzone.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-header box-financeiro-header"><h2><span class="glyphicon glyphicon-usd"></span> - Financeiro  - <small><?= $nome_pagina ?></small></h2></div>
                    
                    <form action="" method="post" id="form1" class="form-horizontal top-margin1" enctype="multipart/form-data" autocomplete="off">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="form-group">
                                <div class="col-sm-4">
                                    <div class="text-bold">Projeto:</div>
                                    <div class="">
                                        <?= montaSelect($arrayProjetos, $_REQUEST['id_projeto'], 'class="form-control" id="id_projeto" name="id_projeto"') ?>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="text-bold">Unidade:</div>
                                    <div class="" id="div_unidade">
                                        <?= montaSelect(array('SELECIONE UM PROJETO'), null, 'class="form-control" id="id_unidade" name="id_unidade"') ?>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="text-bold">Competência:</div>
                                    <div class="input-group" id="">
                                        <?= montaSelect(mesesArray(), $mes, 'class="form-control" id="mes" name="mes"') ?>
                                        <div class="input-group-addon">/</div>
                                        <?= montaSelect(anosArray(2016), $ano, 'class="form-control" id="ano" name="ano"') ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel-footer text-right">
                            <!--<a href="form_caixinha.php" class="btn btn-success"><i class="fa fa-plus"></i> Novo Lançamento</a>-->
                            <button name="filtrar" class="btn btn-primary"><i class="fa fa-filter"></i> FILTRAR</button>
                        </div>
                    </div>
                    </form>
                    <hr>
                    <?php if(count($arrayGrupos) > 0) { ?>
                    <table class="table table-bordered table-striped table-hover table-condensed valign-middle">
                        <tr>
                            <th>CODIGO</th>
                            <th>DESCRIÇÃO</th>
                            <th>PREVISTO</th>
                            <th>REALIZADO</th>
                            <th>PROVISIONADO</th>
                        </tr>
                        <?php foreach ($arrayGrupos as $id_grupo => $valueGrupo) { ?>
                            <tr>
                                <td><?= $id_grupo ?></td>
                                <td><?= $valueGrupo['descricao'] ?></td>
                                <td><?= number_format($valueGrupo[1],2,',','.') ?></td>
                                <td><?= number_format($valueGrupo[2],2,',','.') ?></td>
                                <td><?= number_format($arrayOrcamento[$id_grupo],2,',','.') ?></td>
                            </tr>
                            <?php foreach ($arraySubGrupos[$id_grupo] as $id_subgrupo => $valueSubGrupo) { ?>
                            <tr>
                                <td><?= $id_subgrupo ?></td>
                                <td><?= $valueSubGrupo['descricao'] ?></td>
                                <td><?= number_format($valueSubGrupo[1],2,',','.') ?></td>
                                <td><?= number_format($valueSubGrupo[2],2,',','.') ?></td>
                                <td><?= number_format($arrayOrcamento[$id_subgrupo],2,',','.') ?></td>
                            </tr>
                            <?php } ?>
                        <?php } ?>
                    </table>
                    <?php } else { ?>
                    <div class="alert alert-info text-bold">Nenhuma informação encontrada!</div>
                    <?php } ?>
                </div>
            </div>
            <?php include('../../template/footer.php'); ?>
        </div>
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../js/jquery.maskedinput-1.3.1.js"></script>
        <script src="../../js/jquery.maskMoney.js" type="text/javascript" ></script>
        <script src="../../resources/dropzone/dropzone.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script>
        $(function(){
            $('body').on('click', '.tr_tipo', function(){
                var $this = $(this);
                if($('.t'+$this.data('uni')+$this.data('key')+'.hide').length > 0){
                    $('.t'+$this.data('uni')+$this.data('key')).removeClass('hide');
                } else {
                    $('.t'+$this.data('uni')+$this.data('key')).addClass('hide');
                }
            });
            
            $('body').on('change', '#id_projeto', function(){
                var $this = $(this);
                $.post("", {bugger:Math.random(), method:'unidades', id_unidade:'<?= $_REQUEST['id_unidade'] ?>', id_projeto:$this.val()}, function(result){
                    $('#div_unidade').html(result);
                });
            });
            $('#id_projeto').trigger('change');
            
            $('body').on('click', '.del_caixinha', function(){
                var $this = $(this);
                bootConfirm('Confirma a exclusão do caixinha?','Confirmação',function(data){
                    if(data){
                        $.post("form_caixinha.php", {bugger:Math.random(), method:'inativar', id_caixinha:$this.data('key') }, function(result){
//                            $this.parent().parent().remove();
                            location.reload();
                        });
                    }
                },'warning');
            });
        })
        </script>
    </body>
</html>