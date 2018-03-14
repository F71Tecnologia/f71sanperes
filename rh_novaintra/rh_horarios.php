<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../login.php?entre=true';</script>";
    exit;
}
session_start();
include_once("../conn.php");
include_once("../funcoes.php");
include_once("../wfunction.php");

$id = $_REQUEST['id'];
$id_user = $_COOKIE['logado'];

$usuario = carregaUsuario();
$regiao = $usuario['id_regiao'];

$data = date('d/m/Y');

$result_local = mysql_query("SELECT * FROM regioes where id_regiao = '$regiao'");

$row_local = mysql_fetch_array($result_local);
$qry_grupo = mysql_query("SELECT * FROM projeto WHERE id_regiao='$regiao'");
while ($dados_grupo = mysql_fetch_assoc($qry_grupo)) {
    $arrayProjetos[$dados_grupo[id_projeto]] = $dados_grupo;
}

if(!empty($_REQUEST['ajaxProjeto'])){ ?>
    <table class="table table-condensed table-hover table-striped">
        <thead>
            <tr class="bg-primary">
                <th class="valign-middle">C&oacute;d</th>
                <th class="valign-middle">Atividade</th>
                <th class="valign-middle">Hor&aacute;rio</th>
                <th class="valign-middle text-center" width="75px">Entrada 1</th>
                <th class="valign-middle text-center" width="75px">Sa&iacute;da 1</th>
                <th class="valign-middle text-center" width="75px">Entrada 2</th>
                <th class="valign-middle text-center" width="75px">Sa&iacute;da 2</th>
                <th class="valign-middle">Dias</th>
                <th class="valign-middle text-center" width="50px">Horas m&ecirc;s</th>
                <th class="valign-middle text-center" width="50px">Dias m&ecirc;s</th>
                <th class="valign-middle">Folgas</th>
                <th width="70px"></th>
            </tr>
        </thead>
        <?php
        $result_horarios = mysql_query("
        SELECT rh_horarios.*, curso.id_curso, curso.nome nomeCurso
        FROM rh_horarios
        INNER JOIN curso on (curso.id_curso = rh_horarios.funcao)
        where rh_horarios.id_regiao = '$regiao' AND curso.campo3 = ".$arrayProjetos[$_REQUEST['ajaxProjeto']][id_projeto]);
        $cont = 0;
        while ($row_horarios = mysql_fetch_assoc($result_horarios)) {
            if ($row_horarios['folga'] == "0") {
                $folga_p = "Sem Folgas";
            } elseif ($row_horarios['folga'] == "1") {
                $folga_p = "S&aacute;bado";
            } elseif ($row_horarios['folga'] == "2") {
                $folga_p = "Domingo";
            } elseif ($row_horarios['folga'] == "3") {
                $folga_p = "Sabado e Domingo";
            } elseif ($row_horarios['folga'] == "5") {
                $folga_p = "Plantonista";
            } ?>

            <tr>
                <td class="valign-middle"><?=$row_horarios[id_horario]?></td>
                <td class="valign-middle"><?=$row_horarios['id_curso']?></td>
                <td class="valign-middle"><?=utf8_encode($row_horarios['nome'])?></td>
                <td class="valign-middle"><input type="text" name="entrada1" value="<?=$row_horarios['entrada_1']?>" class="form-control no-padding-hr text-center entrada1 pula" rel="<?=$row_horarios['id_horario']?>" OnKeyUP="formatar('##:##:##', this)" maxlength="8"/></td>
                <td class="valign-middle"><input type="text" name="saida1" value="<?=$row_horarios['saida_1']?>" class="form-control no-padding-hr text-center saida1 pula" rel="<?=$row_horarios['id_horario']?>" OnKeyUP="formatar('##:##:##', this)" maxlength="8"/></td>
                <td class="valign-middle"><input type="text" name="entrada2" value="<?=$row_horarios['entrada_2']?>" class="form-control no-padding-hr text-center entrada2 pula" rel="<?=$row_horarios['id_horario']?>" OnKeyUP="formatar('##:##:##', this)" maxlength="8"/></td>
                <td class="valign-middle"><input type="text" name="saida2" value="<?=$row_horarios['saida_2']?>" class="form-control no-padding-hr text-center saida2 pula" rel="<?=$row_horarios['id_horario']?>" OnKeyUP="formatar('##:##:##', this)" maxlength="8"/></td>
                <td class="valign-middle text-center"><?=$row_horarios['dias_semana']?></td>
                <td class="valign-middle"><input type="text" name="horas_mes" value="<?=$row_horarios['horas_mes']?>" class="form-control no-padding-hr text-center horas_mes" rel="<?=$row_horarios['id_horario']?>"/></td>
                <td class="valign-middle"><input type="text" name="dias_mes" value="<?=$row_horarios['dias_mes']?>"   class="form-control no-padding-hr text-center dias_mes" rel="<?=$row_horarios['id_horario']?>"/></td>
                <td class="valign-middle"><?=$folga_p?></td>
                <td class="valign-middle center">
                    <a class="btn btn-xs btn-warning" href='rh_horarios_alterar.php?regiao=<?=$regiao?>&horario=<?=$row_horarios[id_horario]?>'>
                        <i title="Editar" class="fa fa-pencil"></i>
                    </a>
                    <a class="btn btn-xs btn-danger" href='rh_horarios_excluir.php?regiao=<?=$regiao?>&horario=<?=$row_horarios[id_horario]?>'>
                        <i title="Excluir" class="fa fa-ban"></i>
                    </a>
                </td>
            </tr>
        <?php } ?>
    </table>
    <?php    
    $qry_semhor = mysql_query("SELECT * FROM rh_clt WHERE id_regiao='$regiao' AND rh_horario = 0 AND id_projeto= ".$arrayProjetos[$_REQUEST['ajaxProjeto']][id_projeto]);
    $total_semhor = mysql_num_rows($qry_semhor); ?>

    <table class="table table-condensed table-hover table-striped">
        <?php if ($total_semhor <> 0) { ?>
            <tr>
                <td colspan="3" class="bg-danger valign-middle text-bold text-center">PESSOAS CADASTRADAS SEM HORÁRIO</td>
            </tr>
            <tr>
                <th class="danger valign-middle">Nome</th>
                <th class="danger valign-middle">Função</th>
                <th class="danger text-center valign-middle">EDITAR CADASTRO</th>
            </tr>
        <?php }

        while ($dados_semhor = mysql_fetch_assoc($qry_semhor)) {

            $qry_curso = mysql_query("SELECT * FROM curso WHERE id_curso = '$dados_semhor[id_curso]'") or die(mysql_error());
            $dados_curso = mysql_fetch_assoc($qry_curso); ?>

            <tr>
                <td><?=utf8_encode($dados_semhor['nome'])?></td>
                <td><?=utf8_encode($dados_curso['nome'])?></td>
                <td align='center'>
                    <a class="btn btn-xs btn-warning" href='alter_clt.php?clt=<?=$dados_semhor[id_clt]?>&pro=<?=$dados_semhor[id_projeto]?>&pagina=/intranet/rh/ver_clt.php'>
                        <i title="Editar" class="fa fa-pencil"></i> EDITAR CADASTRO
                    </a>
                </td>
            </tr>
            <?php $cont++;
        } ?>
    </table>
<?php exit; } 
//echo '<pre>'; print_r($arrayProjetos); echo '</pre>';
$countDiv=0;
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$breadcrumb_config = array("nivel"=>"../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form1", "ativo"=>"Controle de Horários");
$breadcrumb_pages = array("Gestão de RH"=>"index.php"); ?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Controle de Horários</title>
        <link href="../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../resources/css/bootstrap-note.css" rel="stylesheet" media="screen">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" >
        <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../css/progress.css" rel="stylesheet" type="text/css">
        <link href="../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
    </head>
    <body>
        <?php include("../template/navbar_default.php"); ?>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small> - Controle de Horários</small></h2></div>
                </div><!-- /.col-lg-12 -->
            </div><!-- /.row -->
            <ul class="nav nav-tabs margin_b10">
                <?php foreach ($arrayProjetos as $key => $dados_grupo) { ?>
                    <li data-key="<?=$key?>" class="tab <?=($countDiv == 0) ? 'active' : ''?>"><a href=".<?=$key?>" data-toggle="tab"><?=$dados_grupo[nome]?></a></li>
                <?php $countDiv++; } ?>
            </ul>
            <div id="myTabContent" class="tab-content">
                <?php foreach ($arrayProjetos as $key => $dados_grupo) { ?>
                    <div class="tab-pane <?=$key?> <?=(array_keys($arrayProjetos)[0] == $key) ? 'active' : ''?>">
                        <?php if(array_keys($arrayProjetos)[0] == $key){ ?>
                            <table class="table table-condensed table-hover table-striped">
                                <thead>
                                    <tr class="bg-primary">
                                        <th class="valign-middle">C&oacute;d</th>
                                        <th class="valign-middle">Atividade</th>
                                        <th class="valign-middle">Hor&aacute;rio</th>
                                        <th class="valign-middle text-center" width="75px">Entrada 1</th>
                                        <th class="valign-middle text-center" width="75px">Sa&iacute;da 1</th>
                                        <th class="valign-middle text-center" width="75px">Entrada 2</th>
                                        <th class="valign-middle text-center" width="75px">Sa&iacute;da 2</th>
                                        <th class="valign-middle">Dias</th>
                                        <th class="valign-middle text-center" width="50px">Horas m&ecirc;s</th>
                                        <th class="valign-middle text-center" width="50px">Dias m&ecirc;s</th>
                                        <th class="valign-middle">Folgas</th>
                                        <th width="70px"></th>
                                    </tr>
                                </thead>
                                <?php
                                $result_horarios = mysql_query("
                                SELECT rh_horarios.*, curso.id_curso, curso.nome nomeCurso
                                FROM rh_horarios
                                INNER JOIN curso on (curso.id_curso = rh_horarios.funcao)
                                where rh_horarios.id_regiao = '$regiao' AND curso.campo3 = $key");
                                $cont = 0;
                                while ($row_horarios = mysql_fetch_assoc($result_horarios)) {
                                    if ($row_horarios['folga'] == "0") {
                                        $folga_p = "Sem Folgas";
                                    } elseif ($row_horarios['folga'] == "1") {
                                        $folga_p = "S&aacute;bado";
                                    } elseif ($row_horarios['folga'] == "2") {
                                        $folga_p = "Domingo";
                                    } elseif ($row_horarios['folga'] == "3") {
                                        $folga_p = "Sabado e Domingo";
                                    } elseif ($row_horarios['folga'] == "5") {
                                        $folga_p = "Plantonista";
                                    } ?>

                                    <tr>
                                        <td class="valign-middle"><?=$row_horarios[id_horario]?></td>
                                        <td class="valign-middle"><?=$row_horarios['id_curso']?></td>
                                        <td class="valign-middle"><?=$row_horarios['nome']?></td>
                                        <td class="valign-middle"><input type="text" name="entrada1" value="<?=$row_horarios['entrada_1']?>" class="form-control no-padding-hr text-center entrada1 pula" rel="<?=$row_horarios['id_horario']?>" OnKeyUP="formatar('##:##:##', this)" maxlength="8"/></td>
                                        <td class="valign-middle"><input type="text" name="saida1" value="<?=$row_horarios['saida_1']?>" class="form-control no-padding-hr text-center saida1 pula" rel="<?=$row_horarios['id_horario']?>" OnKeyUP="formatar('##:##:##', this)" maxlength="8"/></td>
                                        <td class="valign-middle"><input type="text" name="entrada2" value="<?=$row_horarios['entrada_2']?>" class="form-control no-padding-hr text-center entrada2 pula" rel="<?=$row_horarios['id_horario']?>" OnKeyUP="formatar('##:##:##', this)" maxlength="8"/></td>
                                        <td class="valign-middle"><input type="text" name="saida2" value="<?=$row_horarios['saida_2']?>" class="form-control no-padding-hr text-center saida2 pula" rel="<?=$row_horarios['id_horario']?>" OnKeyUP="formatar('##:##:##', this)" maxlength="8"/></td>
                                        <td class="valign-middle text-center"><?=$row_horarios['dias_semana']?></td>
                                        <td class="valign-middle"><input type="text" name="horas_mes" value="<?=$row_horarios['horas_mes']?>" class="form-control no-padding-hr text-center horas_mes" rel="<?=$row_horarios['id_horario']?>"/></td>
                                        <td class="valign-middle"><input type="text" name="dias_mes" value="<?=$row_horarios['dias_mes']?>"   class="form-control no-padding-hr text-center dias_mes" rel="<?=$row_horarios['id_horario']?>"/></td>
                                        <td class="valign-middle"><?=$folga_p?></td>
                                        <td class="valign-middle center">
                                            <a class="btn btn-xs btn-warning" href='rh_horarios_alterar.php?regiao=<?=$regiao?>&horario=<?=$row_horarios[id_horario]?>'>
                                                <i title="Editar" class="fa fa-pencil"></i>
                                            </a>
                                            <a class="btn btn-xs btn-danger" href='rh_horarios_excluir.php?regiao=<?=$regiao?>&horario=<?=$row_horarios[id_horario]?>'>
                                                <i title="Excluir" class="fa fa-ban"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </table>
                            <?php    
                            $qry_semhor = mysql_query("SELECT * FROM rh_clt WHERE id_regiao='$regiao' AND rh_horario = 0 AND id_projeto = $key");
                            $total_semhor = mysql_num_rows($qry_semhor); ?>

                            <table class="table table-condensed table-hover table-striped">
                                <?php if ($total_semhor <> 0) { ?>
                                    <tr>
                                        <td colspan="3" class="bg-danger valign-middle text-bold text-center">PESSOAS CADASTRADAS SEM HORÁRIO</td>
                                    </tr>
                                    <tr>
                                        <th class="danger valign-middle">Nome</th>
                                        <th class="danger valign-middle">Função</th>
                                        <th class="danger text-center valign-middle">EDITAR CADASTRO</th>
                                    </tr>
                                <?php }

                                while ($dados_semhor = mysql_fetch_assoc($qry_semhor)) {

                                    $qry_curso = mysql_query("SELECT * FROM curso WHERE id_curso = '$dados_semhor[id_curso]'") or die(mysql_error());
                                    $dados_curso = mysql_fetch_assoc($qry_curso); ?>

                                    <tr>
                                        <td><?=$dados_semhor['nome']?></td>
                                        <td><?=$dados_curso['nome']?></td>
                                        <td align='center'>
                                            <a class="btn btn-xs btn-warning" href='alter_clt.php?clt=<?=$dados_semhor[id_clt]?>&pro=<?=$dados_semhor[id_projeto]?>&pagina=/intranet/rh/ver_clt.php'>
                                                <i title="Editar" class="fa fa-pencil"></i> EDITAR CADASTRO
                                            </a>
                                        </td>
                                    </tr>
                                    <?php $cont++;
                                } ?>
                            </table>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
            <?php include_once '../template/footer.php'; ?>
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
        <script type="text/javascript">
            $(function () {
                $('.horas_mes, .dias_mes, .entrada1, .saida1, .entrada2, .saida2').change(function () {
                    var valor = $(this).val();
                    var id_horario = $(this).attr('rel');

                    if ($(this).attr('name') == 'horas_mes') {
                        $.ajax({
                            url: 'action.altera_horarios.php?horas=' + valor + '&horario=' + id_horario,
                            success: function (resposta) { }
                        });
                    }

                    if ($(this).attr('name') == 'dias_mes') {
                        $.ajax({
                            url: 'action.altera_horarios.php?dias=' + valor + '&horario=' + id_horario,
                            success: function (resposta) { }
                        });
                    }

                    if ($(this).attr('name') == 'entrada1') {
                        $.ajax({
                            url: 'action.altera_horarios.php?entrada1=' + valor + '&horario=' + id_horario,
                            success: function (resposta) { }
                        });
                    }

                    if ($(this).attr('name') == 'saida1') {

                        $.ajax({
                            url: 'action.altera_horarios.php?saida1=' + valor + '&horario=' + id_horario,
                            success: function (resposta) { }
                        });
                    }

                    if ($(this).attr('name') == 'entrada2') {

                        $.ajax({
                            url: 'action.altera_horarios.php?entrada2=' + valor + '&horario=' + id_horario,
                            success: function (resposta) { }
                        });
                    }

                    if ($(this).attr('name') == 'saida2') {

                        $.ajax({
                            url: 'action.altera_horarios.php?saida2=' + valor + '&horario=' + id_horario,
                            success: function (resposta) { }
                        });
                    }
                });
                
                $('body').on('keyup', '.pula', function(){
                    if($(this).val().length >= 8){
                        $(this).blur();
                    }
                });
                
                $(".tab").on("click",function(){
                    var key = $(this).data('key');
                    //console.log($("div[class='tab-pane "+ key +"']").length);
                    if ( $("div[class='tab-pane "+ key +"']").length == 0){
                        cria_carregando_modal();
                        $.post("rh_horarios.php", {bugger:Math.random(), ajaxProjeto:key}, function(resultado){
                            console.log(resultado);
                            $("."+key).append(resultado);
                            remove_carregando_modal();
                        });
                    }
                });
            });
            
            function formatar(mascara, documento) {
                var i = documento.value.length;
                var saida = mascara.substring(0, 1);
                var texto = mascara.substring(i)

                if (texto.substring(0, 1) != saida) {
                    documento.value += texto.substring(0, 1);
                }
            }
        </script>
    </body>
</html>