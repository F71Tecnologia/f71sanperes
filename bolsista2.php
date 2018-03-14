<?php
if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="login.php">Logar</a>';
    exit;
}
if(empty($_REQUEST["projeto"])){
    header("Location: ver2.php");
}

include_once('conn.php');
include_once('funcoes.php');
include_once('wfunction.php');
include_once("classes/EventoClass.php");
include_once('classes_permissoes/acoes.class.php');

$usuario = carregaUsuario();

$permissao = new Acoes();
$permissao = $permissao->getAcoes($_COOKIE['logado'], $_REQUEST['regiao']);

$projeto = $_REQUEST['projeto'];
$regiao = $usuario['id_regiao'];

//OBJ EVENTO
$data = date("Y-m-d");
$eventos = new Eventos();
$dadosEventos = $eventos->getTerminandoEventos($data, $regiao, $projeto);
$dadosArrayEventos = $eventos->array_dados;
$status = $eventos->getStatus();

// FUN��O NOME
function abreviacao($nome) {
    list($primeiro_nome, $segundo_nome, $terceiro_nome, $quarto_nome, $quinto_nome) = explode(' ', $nome);
    if ($quarto_nome == "DAS" or $quarto_nome == "DA" or $quarto_nome == "DE" or $quarto_nome == "DOS" or $quarto_nome == "DO" or $quarto_nome == "E") {
        $nome_abreviado = "$primeiro_nome $segundo_nome $terceiro_nome $quarto_nome $quinto_nome";
    } else {
        $nome_abreviado = "$primeiro_nome $segundo_nome $terceiro_nome $quarto_nome";
    }
    return $nome_abreviado;
}

if (isset($_REQUEST['pesquisa']) AND ! empty($_REQUEST['pesquisa'])) {
    $valorPesquisa = explode(' ', $_REQUEST['pesquisa']);
    foreach ($valorPesquisa as $valuePesquisa) {
        $pesquisa[] .= "A.nome LIKE '%" . $valuePesquisa . "%'";
    }
    $pesquisa = implode(' AND ', $pesquisa);
    $auxPesquisa = " AND (($pesquisa) OR (CAST(A.matricula AS CHAR) = '{$_REQUEST['pesquisa']}') OR (REPLACE(REPLACE(A.cpf, '.', ''), '-', '') = '{$_REQUEST['pesquisa']}' OR A.cpf = '{$_REQUEST['pesquisa']}'))";

    $query = "SELECT id_unidade FROM rh_clt A WHERE id_projeto = '$projeto' AND (status < '60' OR status = '200') AND status != 0 $auxPesquisa";
    $queryAut = "SELECT id_unidade FROM autonomo A WHERE status = '1' AND id_projeto = '$projeto' $auxPesquisa";
    
    if($_POST['classe'] == 'autonomo' )
        $query = $queryAut;
    
    if($_POST['classe'] == 'desativados' )
        $query = "$query UNION $queryAut";
    
    $sqlPesquisaUnidade = mysql_query($query);
    while ($rowPesquisaUnidade = mysql_fetch_assoc($sqlPesquisaUnidade)) {
        if ($rowPesquisaUnidade['id_unidade'] == '') {
            $pesquisaUnidade[-1] = "''";
        } else {
            $pesquisaUnidade[$rowPesquisaUnidade['id_unidade']] = $rowPesquisaUnidade['id_unidade'];
        }
    }
    $auxPesquisaUnidade = " AND id_unidade IN(" . implode(',', $pesquisaUnidade) . ")";
}
//echo "<pre>";print_r($_POST);echo "</pre>";exit;
if(!empty($_POST['classe']) AND $_POST['classe'] == 'autonomo'){
    $result_unidades = mysql_query("SELECT * FROM unidade WHERE campo1 = '$projeto' $auxPesquisaUnidade AND status_reg = 1 ORDER BY unidade ASC");
    if(mysql_num_rows($result_unidades) > 0){
        while ($row_unidades = mysql_fetch_array($result_unidades)) { ?>
            <button type="button" onclick="tableToExcel('tbRelatorio_a_<?=$row_unidades[0]?>', 'Relat�rio')" class="btn btn-success pull-right"><span class="fa fa-file-excel-o"></span>&nbsp;&nbsp;Exportar para Excel</button>
            <table id="tbRelatorio_a_<?=$row_unidades[0]?>" class="table table-condensed table-hover">
                <thead>
                    <tr>
                        <th colspan="8" class="">
                            <i class="fa fa-arrow-right text-warning"></i>
                            <?=$row_unidades['0'] . " - " . utf8_encode($row_unidades['unidade'])?>
                        </th>
                    </tr>
                    <tr class="info">
                        <th width="5%" align="center">COD</th>
                        <th width="35">NOME</th>
                        <th width="18">CARGO</th>
                        <th width="10%"  align="center">CPF</th>
                        <th width="7%" align="center">ENTRADA</th>
                        <th width="11%" align="center">CONTRATA&Ccedil;&Atilde;O</th>
                        <th width="5%" align="center">PONTO</th>
                        <th width="9%">CURR&Iacute;CULOS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result_participantes = mysql_query("
                    SELECT A.*, date_format(A.data_entrada, '%d/%m/%Y') AS data_entrada2, date_format(A.data_saida, '%d/%m/%Y') AS data_saida2, B.nome nomeCurso 
                    FROM autonomo A LEFT JOIN curso B ON (A.id_curso = B.id_curso)
                    WHERE A.id_unidade = '{$row_unidades['id_unidade']}' AND A.status = '1' AND A.id_projeto = '$projeto' $auxPesquisa ORDER BY A.nome ASC");
                    if(mysql_num_rows($result_participantes) > 0){
                        while ($row_bolsistas = mysql_fetch_array($result_participantes)) {
                            switch ($row_bolsistas['tipo_contratacao']) {
                                case 1: $contratacao = "AUT�NOMO"; break;
                                case 3: $contratacao = "COOPERADO"; break;
                                case 4: $contratacao = "AUT�NOMO PJ"; break;
                            }

                            // --------------- VERIFICANDO ASSINATURAS DE BOLSISTAS ---------------------------------------------------------
                            $color = "warning";

                            if ($row_bolsistas['campo3'] == "INSERIR")
                                $color = "danger";

                            if ($row_bolsistas['locacao'] == "1 - A CONFIRMAR")
                                $color = "danger";

                            if ($row_bolsistas['foto'] == "1") {
                                $nome_imagem = $regiao . "_" . $projeto . "_" . $row_bolsistas['0'] . ".gif";
                                $color = "success";
                            }

                            if (!empty($row_bolsistas['observacao'])) {
                                $color = "danger";
                                $obs = "title=\"Observa��es: $row_bolsistas[observacao]\"";
                            }

                            $Acurriculo = "<img src='imagens/naoassinado.gif' border='0' alt='Enviar' class='bt-upload-curriculo pointer' data-type='{$row_bolsistas['tipo_contratacao']}' data-id='{$row_bolsistas['id_autonomo']}'>";
                            if($row_bolsistas['curriculo']==1){
                                $Acurriculo = "<img src='imagens/assinado.gif' border='0' alt='Visualizar' class='bt-ver-curriculo pointer' data-type='{$row_bolsistas['tipo_contratacao']}' data-id='{$row_bolsistas['id_autonomo']}'>";
                            } ?>
                            <tr style="font-size:12px;" class="<?=$color?>">
                                <td align="center"><?=utf8_encode($row_bolsistas['campo3'])?></td>
                                <td><a class="pointer participante" href="ver_bolsista2.php?reg=<?=$regiao?>&bol=<?=$row_bolsistas['0']?>&pro=<?=$projeto?>" <?=$obs?>><?=abreviacao(utf8_encode($row_bolsistas['nome']))?></a></td>
                                <td><?=str_replace('CAPACITANDO EM', '', utf8_encode($row_bolsistas['nomeCurso']))?></td>
                                <td align="center"><?=$row_bolsistas['cpf']?></td>
                                <td align="center"><?=$row_bolsistas['data_entrada2']?></td>
                                <td align="center"><?=utf8_encode($contratacao)?></td>
                                <td align="center"><a href="folha_ponto2.php?id=2&unidade=<?=$row_unidades['0']?>&regiao=<?=$regiao?>&pro=<?=$projeto?>&id_bol=<?=$row_bolsistas['0']?>&tipo=aut&caminho=2" class="pointer">Gerar</a></td>
                                <td align="center"><?=$Acurriculo?></td>
                            </tr>
                            <?php
                            unset($obs);
                        } 
                    } else { ?>
                        <tr><td colspan="8">Nenhum Registro Encontrado!</td></tr> 
                    <?php } ?>
                </tbody>
            </table>
        <?php } 
    } else { ?>
        <table><tr><td>Nenhum Registro Encontrado!</td></tr></table>
    <?php } 
    exit;
}
if(!empty($_POST['classe']) AND $_POST['classe'] == 'desativados'){ 
    $result_total_inativos = mysql_query("SELECT A.*, date_format(A.data_entrada, '%d/%m/%Y') AS data_entrada2, date_format(A.data_saida, '%d/%m/%Y') AS data_saida2 FROM autonomo A WHERE A.status = '0' and A.id_projeto = '$projeto' $auxPesquisa ORDER BY A.nome ASC");
    $result_clt2 = mysql_query("SELECT A.*, date_format(A.data_entrada, '%d/%m/%Y') AS data_entrada2, date_format(A.data_demi, '%d/%m/%Y') AS data_saida2 FROM rh_clt A WHERE (A.status >= '60' AND A.status != '200') AND A.id_projeto = '$projeto' $auxPesquisa ORDER BY A.nome");

    $row_total_inativos = mysql_num_rows($result_total_inativos);
    $row_total_inativos += mysql_num_rows($result_clt2);
    if($row_total_inativos > 0){ ?>
        <button type="button" onclick="tableToExcel('tabela_desligados', 'Relat�rio')" class="btn btn-success pull-right margin_b10"><span class="fa fa-file-excel-o"></span>&nbsp;&nbsp;Exportar para Excel</button>
        <table class="table table-condensed table-hover" id="tabela_desligados">
            <thead>
            <tr>
                <th colspan="7" class="danger">PARTICIPANTES DESATIVADOS</th>
            </tr>
            <tr class="novo_tr">
                <th width="5%">COD</th>
                <th width="30%">NOME</th>
                <th width="20%">UNIDADE</th>
                <th width="10%" align="center">CPF</th>
                <th width="20%" align="center">ENTRADA - SA&Iacute;DA</th>
                <th width="15%" align="center">CONTRATA&Ccedil;&Atilde;O</th>
                <th width="15%" align="center">CURR&Iacute;CULO</th>
            </tr>
            </thead>
            <tbody>
                <?php
                while ($row2 = mysql_fetch_array($result_total_inativos)) {
                    switch ($row2['tipo_contratacao']) {
                        case 1: $contratacao = "AUT&Ocirc;NOMO"; break;
                        case 2: $contratacao = "CLT"; break;
                        case 3: $contratacao = "COOPERADO"; break;
                        case 4: $contratacao = "AUT&Ocirc;NOMO / PJ"; break;
                    }

                    $AtuCurrDes = "<img src='imagens/naoassinado.gif' border='0' alt='Enviar' class='bt-upload-curriculo pointer' data-type='{$row2['tipo_contratacao']}' data-id='{$row2['id_clt']}'>";
                    if($row2['curriculo']==1){
                        $AtuCurrDes = "<img src='imagens/assinado.gif' border='0' alt='Visualizar' class='bt-ver-curriculo pointer' data-type='{$row2['tipo_contratacao']}' data-id='{$row2['id_clt']}'>";
                    } ?>
                    <tr style="font-size:12px;">
                        <td><?=$row2['campo3']?></td>
                        <td><a class="participante" href="ver_bolsista2.php?reg=<?=$regiao?>&bol=<?=$row2['0']?>&pro=<?=$projeto?>"><?=utf8_encode($row2['nome'])?></a></td>
                        <td><?= utf8_encode($row2['locacao']) ?></td>
                        <td align="center"><?=$row2['cpf']?></td>
                        <td align="center"><?=$row2['data_entrada2'] . ' - ' . $row2['data_saida2']?></td>
                        <td align="center"><?=$contratacao?></td>
                        <td align="center"><?=$AtuCurrDes?></td>
                    </tr>
                    <?php
                }

                // -------------- AKI TERMINA APENAS BOLSISTAS E COME�A CLT ----------------------------

                while ($row_clt2 = mysql_fetch_array($result_clt2)) {

                    $CltCurrDes = "<img src='imagens/naoassinado.gif' border='0' alt='Enviar' class='bt-upload-curriculo pointer' data-type='2' data-id='{$row_clt2['id_clt']}'>";
                    if($row_clt2['curriculo']==1){
                        $CltCurrDes = "<img src='imagens/assinado.gif' border='0' alt='Visualizar' class='bt-ver-curriculo pointer' data-type='2' data-id='{$row_clt2['id_clt']}'>";
                    } ?>
                    <tr style="font-size:12px;">
                        <td><?=$row_clt2['matricula']?></td>
                        <td><a class="participante" href='rh/ver_clt2.php?reg=<?= $row_clt2['id_regiao'] ?>&clt=<?= $row_clt2['0'] ?>&ant=<?= $row_clt2['1'] ?>&pro=<?= $projeto ?>&pagina=bol'<?= $row_clt2[obs] ?>><?= utf8_encode($row_clt2['nome']) ?></a></td>
                        <td><?=utf8_encode($row_clt2['locacao'])?></td>
                        <td align="center"><?=$row_clt2['cpf']?></td>
                        <td align="center"><?=$row_clt2['data_entrada2'] . ' - ' . $row_clt2['data_saida2']?></td>
                        <td align="center">CLT</td>
                        <td align="center"><?=$CltCurrDes?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } else { ?>
        <table><tr><td>Nenhum Registro Encontrado!</td></tr></table>
    <?php } 
    exit;
}

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABE�ALHO (TROCA DE MASTER E DE REGI�ES)
$breadcrumb_config = array("nivel"=>"../intranet", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form1", "ativo"=>"Lista Participantes");
$breadcrumb_pages = array("Lista Projetos" => "ver2.php", "Visualizar Projeto" => "ver2.php?projeto=$projeto");

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Lista Participantes</title>
        <link href="favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="resources/css/main.css" rel="stylesheet" media="screen">
        <link href="resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="css/validationEngine.jquery.css" rel="stylesheet" type="text/css" >
        <link href="resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="css/progress.css" rel="stylesheet" type="text/css">
        <link href="resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="resources/css/bootstrap-note.css" rel="stylesheet" type="text/css">
        <link href="resources/css/add-ons.min.css" rel="stylesheet">
    </head>
    <body class="overflow-y-scroll">
        <?php include("template/navbar_default.php"); ?>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small> - Lista Participantes</small></h2></div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="col-lg-12 note">
                        <div class="col-lg-1 col-md-1 col-sm-12 col-xs-12 tr-bg-success">&nbsp;<span class="hidden-md hidden-lg">Regularizado com foto</span></div><div class="col-lg-3 col-md-3 hidden-sm hidden-xs">Regularizado com foto</div>
                        <div class="col-lg-1 col-md-1 col-sm-12 col-xs-12 tr-bg-warning">&nbsp;<span class="hidden-md hidden-lg">Regularizado</span></div><div class="col-lg-2 col-md-2 hidden-sm hidden-xs">Regularizado</div>
                        <div class="col-lg-1 col-md-1 col-sm-12 col-xs-12 tr-bg-danger">&nbsp;<span class="hidden-md hidden-lg">Com Observa&ccedil;&atilde;o / Sem C&oacute;digo / Sem Unidade</span></div><div class="col-lg-4 col-md-4 hidden-sm hidden-xs">Com Observa&ccedil;&atilde;o / Sem C&oacute;digo / Sem Unidade</div>
                        <div class="clear"></div>
                    </div>
                </div>
            </div>
            <?php if ($_REQUEST['sucesso'] == "edicao") { ?>
                <div class="alert alert-dismissable alert-success">
                    <button type="button" class="close" data-dismiss="alert">�</button>
                    Participante atualizado com sucesso!
                </div>
            <?php } ?>
            <div class="row">
                <div class="col-lg-12">
                    <form name="formPesquisa" id="form1" method="post" class="form-horizontal">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <input type="hidden" name="regiao" value="<?=$_REQUEST['regiao']?>">
                                <input type="hidden" name="projeto" value="<?=$_REQUEST['projeto']?>">
                                <input type="hidden" name="home" id="home" value="">
                                <label class="col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label">Busca:</label>
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                    <input type="text" name="pesquisa" id="pesquisa" class="form-control" placeholder="Nome, Matricula, CPF" value="<?=$_REQUEST['pesquisa']?>">
                                </div>
                                <button type="submit" class="col-lg-2 col-md-2 col-sm-2 col-xs-2 btn btn-primary"><i class="fa fa-search"></i> Pesquisar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <ul class="nav nav-tabs margin_b10">
                        <li class="tab active" data-tab="clt"><a href=".clt" data-toggle="tab">Clts</a></li>
                        <li class="tab" data-tab="autonomo"><a href=".autonomo" data-toggle="tab">Aut�nomos</a></li>
                        <li class="tab" data-tab="desativados"><a href=".desativados" data-toggle="tab">Desativados</a></li>
                    </ul>
                </div>
            </div>
            <div id="myTabContent" class="tab-content">
                <div class="tab-pane clt active">
                    <div class="row">
                        <div class="col-lg-12">
                            <?php
                            $result_unidades = mysql_query("SELECT * FROM unidade WHERE campo1 = '$projeto' $auxPesquisaUnidade AND status_reg = 1 ORDER BY unidade ASC");
                            if(mysql_num_rows($result_unidades) > 0){
                                while ($row_unidades = mysql_fetch_array($result_unidades)) { ?>
                                    <button type="button" onclick="tableToExcel('tbRelatorio_<?=$row_unidades[0]?>', 'Relat�rio')" class="btn btn-success pull-right"><span class="fa fa-file-excel-o"></span>&nbsp;&nbsp;Exportar para Excel</button>
                                    <table id="tbRelatorio_<?=$row_unidades[0]?>" class="table table-condensed table-hover">
                                        <thead>
                                            <tr>
                                                <th colspan="8" class="">
                                                    <i class="fa fa-arrow-right text-warning"></i>
                                                    <?=$row_unidades['0'] . " - " . $row_unidades['unidade']?>
                                                </th>
                                            </tr>
                                            <tr class="info">
                                                <th width="5%" align="center">COD</th>
                                                <th width="35">NOME</th>
                                                <th width="18">CARGO</th>
                                                <th width="10%"  align="center">CPF</th>
                                                <th width="7%" align="center">ENTRADA</th>
                                                <th width="11%" align="center">CONTRATA&Ccedil;&Atilde;O</th>
                                                <th width="5%" align="center">PONTO</th>
                                                <th width="9%">CURR�CULOS</th>
                                            </tr>
                                        </thead>
                                        <tbody id="cltTbody">
                                            <?php
                                            $result_clt = mysql_query("
                                            SELECT A.*, date_format(A.data_entrada, '%d/%m/%Y') AS data_entrada2, date_format(A.data_saida, '%d/%m/%Y') AS data_saida2, B.nome nomeCurso
                                            FROM rh_clt A LEFT JOIN curso B ON (B.id_curso = A.id_curso)
                                            WHERE A.id_projeto = '$projeto' AND A.id_unidade = '{$row_unidades['id_unidade']}' AND (A.status < '60' OR A.status = '200') AND A.status != 0
                                            $auxPesquisa
                                            ORDER BY A.nome ASC") or die(mysql_error());
                                            if(mysql_num_rows($result_clt) > 0){
                                                while ($row_clt = mysql_fetch_array($result_clt)) {
                                                    // --------------- VERIFICANDO ASSINATURAS DE CLT ---------------------------------------------------------
                                                    $color = "warning";
                                                    $textcor = "ok";

                                                    if ($row_clt['campo3'] == "INSERIR") {
                                                        $color = "danger";
                                                        $textcor = "!";
                                                    }

                                                    if ($row_clt['locacao'] == "1 - A CONFIRMAR") {
                                                        $color = "danger";
                                                        $textcor = "!";
                                                    }

                                                    if ($row_clt['foto'] == "1") {
                                                        $color = "success";
                                                        $textcor = "ok";
                                                    }

                                                    if (!empty($row_clt['observacao'])) {
                                                        $color = "danger";
                                                        $obs = "title=\"Observa��es: $row_clt[observacao]\"";
                                                        $textcor = "!";
                                                    }

                                                    $CLTcurriculo = "<img src='imagens/naoassinado.gif' border='0' alt='Enviar' class='bt-upload-curriculo pointer' data-type='2' data-id='{$row_clt['id_clt']}'>";
                                                    if($row_clt['curriculo']==1){
                                                        $CLTcurriculo = "<img src='imagens/assinado.gif' border='0' alt='Visualizar' class='bt-ver-curriculo pointer' data-type='2' data-id='{$row_clt['id_clt']}'>";
                                                    } ?>

                                                    <tr style="font-size:12px;" class="<?=$color?>">
                                                        <td align="center"> <?=$row_clt['matricula']?></td>
                                                        <td><a class="pointer participante" href="rh/ver_clt2.php?reg=<?=$row_clt['id_regiao']?>&clt=<?=$row_clt['0']?>&ant=<?=$row_clt['1']?>&pro=<?=$projeto?>&pagina=bol" <?=$obs?>> <?=abreviacao($row_clt['nome'])?> </a></td>
                                                        <td><?=str_replace('CAPACITANDO EM', '', $row_clt['nomeCurso'])?></td>
                                                        <td align="center"><?=$row_clt['cpf']?></td>
                                                        <td align="center"><?=$row_clt['data_entrada2']?></td>
                                                        <td align="center">CLT</td>
                                                        <td align='center'><a href="folha_ponto2.php?id=2&unidade=<?=$row_unidades['0']?>&regiao=<?=$regiao?>&pro=<?=$projeto?>&id_bol=<?=$row_bolsistas['0']?>&tipo=clt&caminho=2" class="pointer">Gerar</a></td>
                                                        <td align="center" ><?=$CLTcurriculo?></td>
                                                    </tr>
                                                    <?php
                                                    unset($obs);

                                                } 
                                            } else { ?>
                                                <tr><td colspan="8">Nenhum Registro Encontrado!</td></tr> 
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                <?php } 
                            } else { ?>
                                <table><tr><td>Nenhum Registro Encontrado!</td></tr></table>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="tab-pane autonomo">
                    <div class="row">
                        <div class="col-lg-12" id="autonomoTbody"></div>
                    </div>
                </div>
                <div class="tab-pane desativados">
                    <div class="row">
                        <div class="col-lg-12" id="desativadosTbody"></div>
                    </div>
                </div>
            </div>
            <?php include_once 'template/footer.php'; ?>
        </div><!-- /.content -->
        <script src="js/jquery-1.10.2.min.js"></script>
        <script src="resources/js/bootstrap.min.js"></script>
        <script src="resources/js/bootstrap-dialog.min.js"></script>
        <script src="js/jquery.validationEngine-2.6.js"></script>
        <script src="js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="js/jquery.maskedinput-1.3.1.js"></script>
        <script src="resources/js/main.js"></script>
        <script src="js/global.js"></script>
        <script src="http://malsup.github.com/jquery.form.js" type="text/javascript"></script>
        <script>
            $(function() {
                /*$("#data_prorrogada").datepicker();*/
                
                $("body").on("click", ".bt-upload-curriculo",function(){
                    var botao = $(this);
                    var nome = botao.parents("tr").find(".participante").html();
                    
                    var message = $("<form>",{
                        name:"curriculoForm", 
                        id:"curriculoForm", 
                        action:"rh/curriculo/upload_curriculo.php",
                        enctype:"multipart/form-data", 
                        method:"post"
                    }).append(
                        $('<input>',{ type:"hidden", name:"id_fun", id:"id_fun", value:botao.data('id') }),
                        $('<input>',{ type:"hidden", name:"tipo_contrato", id:"tipo_contrato", value:botao.data('type') }),
                        $('<p>',{ text:"Nome" }),
                        $('<p>',{ id:"nome_up_curriculo", text:nome }),
                        $('<p>',{ for:"file_curriculo", text:"Curr�culo" }),
                        $('<input>',{ type:"file", name:"file_curriculo", id:"file_curriculo", class:"validate[required,custom[docsType]]" }),
                        $('<p>',{ text:"S� ser�o aceitos curr�culos com extens�o: png, jpg, pdf, doc e docx." }),
                        $('<progress>',{ value:"0", max:"100" }),
                        $('<span>',{ id:"porcentagem" }),
                        //$('<input>',{ type:"submit", name:"up_curriculo", id:"up_curriculo", value:"Enviar" }),
                        $('<div>',{ id:"message_erro_curriculo" })
                    );
                    
                    bootDialog(
                        message, 
                        "Upload Curr�culo", 
                        [{
                            label: 'Enviar',
                            action: function (dialog) {
                                $('#curriculoForm').ajaxForm({
                                    clearForm: true,
                                    dataType:  'json',
                                    type:      'post',
                                    uploadProgress: function(event, position, total, percentComplete) {
                                        if(percentComplete > 99){
                                            percentComplete = 99;
                                        }
                                        $('progress').attr('value',percentComplete);
                                        $('#porcentagem').html(percentComplete+'% aguarde..');
                                    },
                                    success: function(data) {
                                        $('progress').attr('value','100');
                                        $('#porcentagem').html('100%');
                                        console.log(data);
                                        if(data.status == 1){
                                            $(".close").trigger('click');
                                            $('.actived').attr('src','imagens/assinado.gif').attr('alt','Visualizar').addClass('bt-ver-curriculo').removeClass('bt-upload-curriculo').removeClass('actived');
                                        }else{
                                            $("#message_erro_curriculo").html('Erro ao processar: '+data.msg);
                                        }
                                    }
                                });
                                botao.addClass('actived');
                                $("#curriculoForm").submit();
                            }
                        }],
                        "info"
                    );
                });
                
                $("body").on("click", ".bt-ver-curriculo",function(){
                    var botao = $(this);
                    var nome = botao.parents("tr").find(".participante").html();
                    
                    $('.actived').removeClass('actived');
                    botao.addClass('actived');
                    
                    $.post('rh/curriculo/visualiza.php',{id: botao.data('id'), tipo:botao.data('type')},function(data){
                        //console.log(data);
                        if(data.status == 1){
                            var message = $("<div>",{
                                id:"curriculoForm"
                            }).append(
                                $('<p>').append($('<a>',{ href:"rh/curriculo/"+data.doc, target:"_blanck", id:"aVerCurri"}).append($('<img>',{ href:"", src:"imagens/icons/att-"+data.type+".png", id:"imgVerCurri", class:"no-padding-hr"}))),
                                $('<p>').append(
                                    $('<input>',{ 
                                        type:"button", 
                                        name:"deletaCurriculo", 
                                        id:"deletaCurriculo", 
                                        value:"Deletar Curr�culo", 
                                        class:"btn btn-danger",
                                        "data-type":botao.data('type'),
                                        "data-id":botao.data('id'),
                                        "data-doc":data.doc
                                    })
                                )
                            );
                            bootAlert( message, "Ver Curr�culo: "+nome, null, "info" );
                        }
                    }, "json");
                });
                
                $("body").on("click", "#deletaCurriculo", function(){
                    var ok = confirm("Aten��o, essa a��o � irrevers�vel. Deseja realmente excluir este curr�culo?");
                    if(ok){
                        var botao = $(this);
                        $.post('rh/curriculo/upload_curriculo.php',{method:'deletaCurriculo', id_fun: botao.data('id'), tipo_contrato:botao.data('type'), doc:botao.data('doc')},function(data){
                            if(data.status == 1){
                                //$(".ui-icon-closethick").trigger('click');
                                $(".btn-default").trigger('click');
                                $('.actived').attr('src','imagens/naoassinado.gif').attr('alt','Enviar').addClass('bt-upload-curriculo').removeClass('bt-ver-curriculo').removeClass('actived');
                            }
                        }, "json");
                    }
                });
                
                $("body").on("click", ".prorrogar", function() {
                    //RECUPERA ID_EVENTO
                    var eventos = $(this).attr("data-key");
                    $("#id_evento").val(eventos);
                    //RECUPERA DATA_RETORNO
                    var data_retorno = $(this).attr("data-retorno");
                    $("#data_retorno").val(data_retorno);

                    $("#data_prorrogada").val('');
                    $("#dias").val('');

                    $("#modal_motivo").show();
                    thickBoxModal("Motivo de prorroga��o", "#modal_motivo", 300, 450);

                });

                $("body").on("click", "#calc-data", function() {
                    var id = $("#id_evento").val();
                    var dias = $("#dias").val();
                    $.post('methods.php', {id: id, calcData: true, qtdDias: dias}, function(data) {
                        if (data != 0) {
                            $("#data_prorrogada").val(data.data);
                        } else {
                            alert('Falha ao carregar evento!');
                            exit();
                        }
                    }, 'json');
                });

                $("#dias").change(function() {
                    if ($(this).val() < 0) {
                        $(this).val(0);
                    }
                });

                $("body").on("click", "#finalizar", function() {
                    var id_evento = $("#id_evento").val();
                    var id_user = $("#id_user").val();
                    var data_retorno = $("#data_retorno").val();
                    var mensagem = $("textarea[name='motivo']").val();
                    var data_prorrogada = $("#data_prorrogada").val();
                    $.ajax({
                        url: "methods.php",
                        type: "POST",
                        dataType: "json",
                        data: {
                            id_evento: id_evento,
                            id_user: id_user,
                            data_retorno: data_retorno,
                            mensagem: mensagem,
                            data_prorrogada: data_prorrogada,
                            method: "prorroga_evento"
                        },
                        success: function(data) {
                            if (data.status) {
                                thickBoxClose("#modal_motivo");
                                history.go(0);
                            } else {
                                var html = "";
                                $.each(data.erro, function(key, value) {
                                    html += "<p>" + value + "</p><br />";
                                });
                                $("#message_erro").html(html);
                            }
                        }
                    });

                });
                
                $("body").on("click", ".voltar", function() {
                    var eventos = $(this).attr("data-key");
                    $("#id_evento").val(eventos);
                    $.ajax({
                        url: "methods.php",
                        type: "POST",
                        dataType: "json",
                        data: {
                            method: "cadEvento",
                            id_evento: eventos
                        },
                        success: function(data) {
                            if (data.status) {
                                history.go(0);
                            }
                        }
                    });
                });
                
                $("body").on("hover", ".acao_ocultar", function() {
                    var $class = $(this).attr("data-remove");
                    $(this).animate({marginRight: "70px"}).addClass("acao_ativada");
                    $(this).html("<p class='acao_ativada' data-remove='" + $class + "' style='margin-left: 15px; font-weight: bold; color: chocolate; font-size: 15px; margin-top: -5px;'>Ocultar</p>");
                });

                $("body").on("click", ".acao_ativada", function() {
                    var $class = "." + $(this).attr("data-remove");
                    $($class).remove();
                });
                
                //SCRIPT PARA UPLOAD DE CURRICULO
                $("#curriculoForm").validationEngine({promptPosition : "topLeft"});
                $(".tab").on("click",function(){
                    var classe = $(this).data('tab');
                    var pesquisa = $("#pesquisa").val();
                    
                    if($("#"+classe+"Tbody").html().length == 0){
                        cria_carregando_modal();
                        $.post("bolsista2.php", {bugger:Math.random(), projeto:<?=$projeto?>, classe:classe, pesquisa:pesquisa}, function(resultado){
                            $("#"+classe+"Tbody").html(resultado);
                            remove_carregando_modal();
                        });
                    }
                });
            });
        </script>
    </body>
</html>