<?php
if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br/><a href="../login.php">Logar</a>';
    exit;
}

include('../conn.php');
include('../wfunction.php');
include('../classes/SetorClass.php');
include('../classes/PlanoSaudeClass.php');
require_once('../classes/LogClass.php');

$usuario = carregaUsuario();
$log = new Log();

$id_clt = (!empty($_REQUEST['id_clt'])) ? $_REQUEST['id_clt'] : null;

if($id_clt){
    $sqlClt = "SELECT * FROM rh_clt WHERE id_clt = '{$id_clt}' LIMIT 1";
    $qryClt = mysql_query($sqlClt);
    $rowClt = mysql_fetch_assoc($qryClt);
//    print_array($rowClt);
}

$sqlVerFolha = "SELECT * FROM rh_folha_proc WHERE id_clt = $id_clt AND status > 0";
$queryVerFolha = mysql_query($sqlVerFolha);
$rowsVerFolha = mysql_num_rows($queryVerFolha);
if ($rowsVerFolha > 0) {
    $disabled = " readonly='readonly' ";
}

if(isset($_POST['salvar'])){
    
    $antes = $log->getLinha('rh_clt', $id_clt);
    
    $update = "UPDATE rh_clt SET id_curso = '{$_POST['id_curso']}', rh_horario = '{$_POST['rh_horario']}', id_unidade = '{$_POST['id_unidade']}',id_regiao = '{$_POST['id_regiao']}',id_projeto = '{$_POST['id_projeto']}' WHERE id_clt = {$_POST['id_clt']} LIMIT 1;";
    if(mysql_query($update)){
        $depois = $log->getLinha('rh_clt', $id_clt);
        
        $log->log(2,"Funcionário ID $id_clt Editado (Funções / Horários / Unidade)", 'rh_clt', $antes, $depois);
        
        header("Location: ver_clt.php?reg={$rowClt['id_regiao']}&clt={$rowClt['id_clt']}&ant=0&pro={$rowClt['id_projeto']}");
    }
    exit;
}

if(isset($_REQUEST['method']) && $_REQUEST['method'] == 'horarios'){
    $qr_horarios = mysql_query("SELECT id_horario, nome, entrada_1, saida_1, entrada_2, saida_2 FROM rh_horarios WHERE horas_semanais = (SELECT hora_semana FROM curso WHERE id_curso = '{$_REQUEST['id']}')");
    $verifica_horario = mysql_num_rows($qr_horarios);
    if (!empty($verifica_horario)) {
        $array[$row_horarios['id_horario']] = "-- Selecione --";
        while ($row_horarios = mysql_fetch_array($qr_horarios)) {
            $array[$row_horarios['id_horario']] = "{$row_horarios['id_horario']} - {$row_horarios['nome']} ( {$row_horarios['entrada_1']} - {$row_horarios['saida_1']} - {$row_horarios['entrada_2']} - {$row_horarios['saida_2']} )";
        }

        $html = montaSelect($array, $_REQUEST['rh_horario'], "class='form-control validate[required]' id='rh_horario' name='rh_horario'");
    } else {
//        $html = '<a href="../rh_novaintra/horario" target="_blank"><label style=" cursor: default; cursor: pointer; ">Clique aqui para cadastrar um hor&aacute;rio</label></a>';
        $html = '<a href="../rh_novaintra/curso" target="_blank"><label style=" cursor: default; cursor: pointer; ">Clique aqui para cadastrar um hor&aacute;rio</label></a>';
    }
    echo $html;
    exit;
}

/**
 * SELECIONA TODAS AS FUNÇÕES DO PROJETO
 */

if($_COOKIE['logado'] == 158){
   // echo "SELECT id_curso, nome, letra, numero, valor, salario FROM curso WHERE campo3 = '{$rowClt['id_projeto']}' AND tipo IN(0,2) AND status = '1' AND status_reg = '1' ORDER BY nome ASC";
}

$arRegFunc = array(1 => 1, 2 => 2, 3 => 2);
$sqlCurso = mysql_query("SELECT * FROM curso WHERE id_regiao = '{$arRegFunc[$rowClt['id_regiao']]}' /*AND campo3 = '{$_REQUEST['projeto']}'*/ AND status = '1' AND status_reg = '1' ORDER BY nome ASC");
//$sqlCurso = mysql_query("SELECT id_curso, nome, letra, numero, valor, salario FROM curso WHERE campo3 = '{$rowClt['id_projeto']}' AND tipo IN(0,2) AND status = '1' AND status_reg = '1' ORDER BY nome ASC");
$verifica_curso = mysql_num_rows($sqlCurso);
if (!empty($verifica_curso)) {
    $arrayFuncoes[''] = "-- SELECIONE --";
    while ($row_curso = mysql_fetch_assoc($sqlCurso)) {
        
        $salario = number_format((!empty($row_curso['valor'])) ? $row_curso['valor'] : $row_curso['salario'], 2, ',', '.');
        $nomeNovo = "{$row_curso['nome']} {$row_curso['letra']}{$row_curso['numero']}";
        $arrayFuncoes[$row_curso['id_curso']] = "{$row_curso['id_curso']} - {$nomeNovo} (Valor: $salario)";
        
        /**
         * seleção de curso nova
         */
        $arrayCursosNovo[$row_curso['nome']][$row_curso['letra']][$row_curso['numero']] = $row_curso;
    }
    $cursoLetras = array("A","B","C","D","E","F","G");
    if(count($arrayCursosNovo) > 0){
        $tabelaFuncoesNova = "<table class='table table-bordered table-condensed text-sm valign-middle'><tr><td>Cargo</td><td class='text-center'>Letra</td><td class='text-center'>1</td><td class='text-center'>2</td><td class='text-center'>3</td><td class='text-center'>4</td><td class='text-center'>5</td></tr>";
        foreach ($arrayCursosNovo as $nome => $value) {
            $tabelaFuncoesNova .= "<tr><td rowspan='".(count($value))."'>".$nome.'</td>';
            if(!$value['']){
                foreach ($cursoLetras as $letra) {
                    if($value[$letra]){
        //                $tabelaFuncoesNova .= (count($value) > 1) ? '<tr>' : '';
                        $tabelaFuncoesNova .= "<td class='text-center'>$letra</td>";
                        for ($i = 1; $i <= 5; $i++) {
                            switch ($i) {
                                case 1: $btn_cor = 'default'; break;
                                case 2: $btn_cor = 'warning'; break;
                                case 3: $btn_cor = 'primary'; break;
                                case 4: $btn_cor = 'info'; break;
                                case 5: $btn_cor = 'success'; break;
                            }
                            if($value[$letra][$i]){
                                $tabelaFuncoesNova .= "<td class='text-center'><button type='button' class='btn btn-{$btn_cor} nova_selecao_funcao' data-id='{$value[$letra][$i]['id_curso']}'>".number_format($value[$letra][$i]['valor'],2,',','.')."</button></td>";
                            } else {
                                $tabelaFuncoesNova .= "<td></td>";
                            }
                        }
                        $tabelaFuncoesNova .= '</tr>';
                    }
                }
            } else {
                $tabelaFuncoesNova .= "<td class='text-center'><button type='button' class='btn btn-default nova_selecao_funcao' data-id='{$value['']['']['id_curso']}'>".number_format($value['']['']['valor'],2,',','.')."</button></td><td colspan='5'></td>";
            }

            $tabelaFuncoesNova .= '</tr>';
        }
        $tabelaFuncoesNova .= '<table>';
    }
} else {
    $arrayFuncoes[''] = "Nenhum Curso Cadastrado para o Projeto";
}

/**
 * SELECIONA TODAS AS UNIDADES DO PROJETO
 */
$sqlUnidades = mysql_query("SELECT id_unidade, unidade FROM unidade WHERE campo1 = {$rowClt['id_projeto']} ORDER BY unidade");
$arrayUnidades = array("" => "-- SELECIONE --");
while ($rowUnidades = mysql_fetch_assoc($sqlUnidades)) {
    $arrayUnidades[$rowUnidades['id_unidade']] = $rowUnidades['id_unidade'] . " - " . $rowUnidades['unidade'];
}

/*SELECIONA O PROJETO */

$sqlProjeto = mysql_query("SELECT id_projeto, nome FROM projeto");
$arrayProjeto = array("" => "-- SELECIONE --");
while ($rowProjeto = mysql_fetch_assoc($sqlProjeto)) {
    $arrayProjeto[$rowProjeto['id_projeto']] = $rowProjeto['id_projeto'] . " - " . $rowProjeto['nome'];
}
/*SELECIONA A REGIAO*/

$sqlRegiao = mysql_query("SELECT id_regiao, regiao FROM regioes");
$arrayRegiao = array("" => "-- SELECIONE --");
while ($rowRegiao = mysql_fetch_assoc($sqlRegiao)) {
    $arrayRegiao[$rowRegiao['id_regiao']] = $rowRegiao['id_regiao'] . " - " . $rowRegiao['regiao'];
}

$nome_pagina = "Gerenciamento de CLT (Funções / Horários / Unidade)";
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$breadcrumb_config = array("nivel"=>"../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form1", "ativo"=>$nome_pagina);
//$breadcrumb_pages = $breadcrumb_caminhos[$caminho];
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: <?= $nome_pagina ?></title>
        <link href="../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <!--<link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" >-->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" >
        <link href="../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <!--<link href="../resources/css/bootstrap-note.css.css" rel="stylesheet" type="text/css">-->
    </head>
    <body>
    <?php include("../template/navbar_default.php"); ?>
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small> - <?= $nome_pagina ?></small></h2></div>
                </div>
            </div>
            <form action="" class="form-horizontal" method="post" id="form_clt">
                <div class="panel panel-default">
                    <div class="panel-heading text-bold">Gerenciamento do CLT <?= $rowClt['nome'] ?></div>
                    <div class="panel-body">
                        <div class="form-group">
                           
                            <div class="col-sm-6">
                                <div class="text-bold">Região:</div>
                                 <?= montaSelect($arrayRegiao,$rowClt['id_regiao'] , " class='form-control validate[required]' $disabled name='id_regiao' id='id_regiao'") ?>
                                <!--<div id="div_regiao" class="">Selecione uma função!</div>-->
                            </div>
                             <div class="col-sm-6">
                                <div class="text-bold">Projeto:</div>
                                <div class="input-group">
                                    <?= montaSelect($arrayProjeto, $rowClt['id_projeto'], " class='form-control validate[required]' $disabled name='id_projeto' id='id_projeto'") ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-6">
                                <div class="text-bold">Função:</div>
                                <div class="">
                                    <div class="input-group">
                                        <?= montaSelect($arrayFuncoes, $rowClt['id_curso'], " class='form-control validate[required]' $disabled name='id_curso' id='id_curso'") ?>
                                        <div class="input-group-addon pointer" id="btn-funcoes"><i class="fa fa-eye"></i></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="text-bold">Horário:</div>
                                <div id="div_horario" class="">Selecione uma função!</div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-12">
                                <div class="text-bold">Unidade:</div>
                                <div class="">
                                    <?= montaSelect($arrayUnidades, $rowClt['id_unidade'], " class='form-control validate[required]' $disabled name='id_unidade' id='id_unidade'") ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer text-right">
                        <input type="hidden" name="id_clt" value="<?= $rowClt['id_clt'] ?>">
                        <button name="salvar" type="submit" class="btn btn-primary"><i class="fa fa-save"></i> SALVAR</button>
                    </div>
                </div>
            </form>
            <?php include_once '../template/footer.php'; ?>
        </div><!-- /.content -->
        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../js/jquery.validationEngine-2.6.js"></script>
        <script src="../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../js/jquery.maskedinput-1.3.1.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/global.js"></script>
        <script>
            $(function() {
                
                // máscaras
                $("#cpf").mask("999.999.999-99", {placeholder: " "});
                $("#cep").mask("99999-999", {placeholder: " "});
                $(".tel").brTelMask();
                    
                $('#form_clt').validationEngine();
                
                $('body').on('click', '#btn-funcoes', function(){
                            
                    var msg = "<?= $tabelaFuncoesNova ?>";
                    new BootstrapDialog({
                        nl2br: false,
                        type: 'type-primary',
                        title: 'PLANO DE CARGOS E SALÁRIOS COMPLETO',
                        message: msg,
                        size: BootstrapDialog.SIZE_WIDE,
                        closable: true
                    }).open();
                });

                $('body').on('click', '.nova_selecao_funcao', function(){
                    $('#id_curso').val($(this).data('id')).trigger('change');
                    $('.modal, .modal-backdrop').remove();
                });
                
                <?php //if ($rowsVerFolha < 1) { ?>
                $('body').on('change', '#id_curso', function(){
                    if($(this).val() > 0) { 
                        $.post("", {bugger:Math.random(), method:'horarios', id:$(this).val(), rh_horario: '<?= $rowClt['rh_horario'] ?>'}, function(result){
                            $('#div_horario').html(result);
                        });
                    }
                });
                <?php //} ?>



                $('#id_regiao').change(function(){
                    var id_regiao = $(this).val();
                        $.ajax({
                            url: '../action.global.php?regiao=' + id_regiao,
                            success: function(resposta) {
                                $('#id_projeto').html(resposta);
                            }
                        });
                });


                $('#id_projeto').change(function() {
                     id_regiao = $('#id_regiao').val();
                     id_projeto = $('#id_projeto').val();
                    if(id_regiao && id_projeto){
                        $.ajax({
                            url: '../action.global_funcoes.php?regiao=' + id_regiao+'&projeto=' + id_projeto+'&area=curso',
                            success: function(resposta) {
                                $('#id_curso').html(resposta);
                            }
                        });
                        $.ajax({
                            url: '../action.global_funcoes.php?regiao=' + id_regiao+'&projeto=' + id_projeto+'&area=horario',
                            success: function(resposta) {
                                $('#id_horario').html(resposta);
                            }
                        });
                        $.ajax({
                            url: '../action.global_funcoes.php?regiao=' + id_regiao+'&projeto=' + id_projeto+'&area=unidade',
                            success: function(resposta) {
                                $('#id_unidade').html(resposta);
                            }
                        });
                    }
                });

                $('#id_curso').trigger('change');
                
            });
        </script>
    </body>
</html>