<?php
if (!isset($_COOKIE['logado'])) {
    header("Location: /intranet/login.php?entre=true");
    exit;
}
if ((isset($_REQUEST['voltar']) && $_REQUEST['voltar'] == "voltar") || empty($_REQUEST['curso'])) {
    header('Location: index.php');
    exit;
}

include('../../conn.php');
include('../../classes/global.php');
include('../../wfunction.php');
include('../../classes/FuncoesClass.php');

$id_curso = $_REQUEST['curso'];

$usuario = carregaUsuario();
setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
        
$cursoQuery = montaQuery("curso
        LEFT JOIN rh_cbo ON (curso.cbo_nome = rh_cbo.nome OR curso.cbo_codigo = rh_cbo.id_cbo)
        LEFT JOIN tipo_contratacao ON (curso.tipo = tipo_contratacao.tipo_contratacao_id)
        LEFT JOIN rhsindicato ON (curso.id_sindicato = rhsindicato.id_sindicato)
        LEFT JOIN setor ON (curso.id_departamento = setor.id_setor)
        LEFT JOIN undSalFixo ON (curso.undSalFixo = undSalFixo.id_pagamento)
        ", "curso.inicio, curso.termino, curso.descricao, curso.nome, curso.tipo, curso.area, curso.local, curso.salario, curso.mes_abono, curso.dscSalVar, curso.horista_plantonista, curso.qnt_maxima, curso.tipo_insalubridade, curso.qnt_salminimo_insalu, curso.periculosidade_30, curso.parcelas, curso.hora_semana, curso.hora_mes, curso.quota, curso.num_quota, curso.valor_hora, curso.fracao_dsr_horista, curso.sobre_aviso, curso.gratificacao_funcao, curso.quebra_caixa, tipo_contratacao.tipo_contratacao_nome, rhsindicato.nome as sindicato, rh_cbo.nome as cbo_nome, rh_cbo.cod as cbo_cod, setor.nome as departamento_nome, setor.id_setor as id_departamento, undSalFixo.nome as unidadePagamento", "id_curso = {$id_curso}", null, null, '', false);
$curso = mysql_fetch_assoc($cursoQuery);
if (!isset($curso)) {
    $_SESSION['MESSAGE'] .= (((!empty($_SESSION['MESSAGE'])) ? "<br />" : "")."Falha ao carregar dados da função. Contate um administrador.");
    header('Location: index.php');
    exit;
}

//trata tipo de insalubridade
if ($curso['tipo_insalubridade'] == 0) {
    $insalubridade = "0%";
}
elseif ($curso['tipo_insalubridade'] == 1) {
    $insalubridade = "20%";
}
elseif ($curso['tipo_insalubridade'] == 2) {
    $insalubridade = "40%";
}

if (floatval($curso['valor_ad_cargo_confianca']) > 0) {
    $tipo_adicional = "Valor";
    $adicional_confianca = formataMoeda($curso['valor_ad_cargo_confianca']);
}
elseif (floatval($curso['percentual_ad_cargo_confianca']) > 0) {
    $tipo_adicional = "Percentual";
    $adicional_confianca = ($curso['percentual_ad_cargo_confianca']*100).'%';
}
else {
    $tipo_adicional = "Não tem";
    $adicional_confianca = "R$ 0,00";
}

session_write_close();

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form1", "ativo"=>"Função ".$curso['nome']);
$breadcrumb_pages = array(/*"Gestão de RH"=>"../../rh", */"Gestão de Funções"=>"index.php");
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Função <?= $curso['nome']?></title>
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
    </head>
    <body>
    <?php include("../../template/navbar_default.php"); ?>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small> - Função <?= $curso['nome']; ?></small></h2></div>
                </div>
            </div>
            <form action="" method="post" name="form1" id="form1" enctype="multipart/form-data">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Dados da Função
                        <input type="hidden" id="curso" name="curso" value="" />
                    </div>
                    <div class="panel-body">
                        <div class="col-xs-6 no-padding-l border-r">
                            <p><label>Tipo de Contratação:</label> <?= $curso['tipo_contratacao_nome']; ?></p>
                            <p><label>Sindicato:</label> <?= empty($curso['sindicato']) ? "Nenhum" : $curso['sindicato']; ?></p>
                            <p><label>Função para Horista:</label> <?= (empty($curso['horista_plantonista'])) ? 'Não' : 'Sim'; ?></p>
                            <?= (!empty($curso['horista_plantonista'])) ? '<p><label>Valor/Hora:</label> '.formataMoeda($curso['valor_hora']).'</p>' : ''; ?>
                            <?= (!empty($curso['horista_plantonista'])) ? '<p><label>Fração do Cálc. de DSR:</label> '.$curso['fracao_dsr_horista'].'</p>' : ''; ?>
                            <p><label>Área:</label> <?= $curso['area']; ?></p>
                            <p><label>Departamento:</label> <?= $curso['departamento_nome']." - ".$curso['id_departamento']; ?></p>
                            <p><label>CBO:</label> <?= (!empty($curso['cbo_cod'])) ? $curso['cbo_nome']." - ".$curso['cbo_cod'] : ""; ?></p>
                            <p><label>Salário:</label> <?= formataMoeda($curso['salario']); ?></p>
                            <p><label>Mês Abono:</label> <?= (!empty($curso['mes_abono'])) ? mesesArray($curso['mes_abono']) : "Não possui"; ?></p>
                            <p><label>Unidade de Pagamento:</label> <?= $curso['unidadePagamento']; ?></p>
                            <p><label>Descrição do Salário:</label> <?= (!empty($curso['dscSalVar'])) ? $curso['dscSalVar'] : 'Nenhuma'; ?></p>
                            <p><label>Qtd. Máxima de Contratação:</label> <?= $curso['qnt_maxima']; ?></p>
                        </div>
                        <div class="col-xs-6 no-padding-r">
                            <p><label>Horas Semanais:</label> <?= $curso['hora_semana']; ?></p>
                            <p><label>Horas Mensais:</label> <?= $curso['hora_mes']; ?></p>
                            <p><label>Insalubridade:</label> <?= $insalubridade; ?></p>
                            <p><label>Quantidade de Salários:</label> <?= $curso['qnt_salminimo_insalu']; ?></p>
                            <p><label>Periculosidade:</label> <?= (!empty($curso['periculosidade_30'])) ? '30%' : '0%'; ?></p>
                            <p><label>Gratificação por Função:</label> <?= formataMoeda($curso['gratificacao_funcao']); ?></p>
                            <p><label>Adicional por Cargo de Confiança:</label> <?= $adicional_confianca;?> (<?= $tipo_adicional ?>)</p>
                            <p><label>Quebra de Caixa:</label> <?= formataMoeda($curso['quebra_caixa']); ?></p>
                            <p><label>Sobreaviso:</label> <?= (!empty($curso['sobre_aviso'])) ? 'Sim' : 'Não'; ?></p>
                            <p><label>Descrição:</label> <?= (!empty($curso['descricao'])) ? $curso['descricao'] : 'Nenhuma'; ?></p>
                        </div>
                    </div>
                    <div class="panel-footer text-right">
                        <button type="button" class="btn btn-danger" name="voltar" id="voltar" value="voltar"><span class="fa fa-step-backward"></span> Voltar</button>
                        <button type="submit" class="btn btn-primary" value="edicao" name="form" id="editarFuncao" data-curso="<?= $id_curso ?>"><span class="fa fa-pencil"></span> Editar</button>
                    </div>
                </div>
            </form>
            <?php include_once '../../template/footer.php'; ?>
        </div>
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script>
            $(function() {
                $("#editarFuncao").click(function(e){
                    var curso = $(this).data("curso");
                    
                    if (curso.length === 0 || curso < 1){
                        bootAlert("ID da função não encontrado. Contate um administrador.", "ERRO", null, 'warning');
                        e.preventDefault();
                    }
                    else {
                        $("#curso").val(curso);
                        $("#form1").attr('action','form_curso.php');
                        $("#form1").submit();
                    }
                });
                
                // BOTÃO DE VOLTAR PÁGINA
                $("#voltar").click(function () {
                    $("#form1").attr('action', 'index.php').submit();
                });
            });
        </script>
    </body>
</html>