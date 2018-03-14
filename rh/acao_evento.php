<?php
// Incluindo Arquivos
require('../conn.php');
include('../classes/abreviacao.php');
include('../funcoes.php');
include('../wfunction.php');
include('../classes/EventoClass.php');
session_start();

$usuario = carregaUsuario(); // carrega dados do usuário
$eventos = new Eventos();


// Variáveis
$clt = $_GET['clt'];
$regiao = $_GET['regiao'];

// excluir evento
if (isset($_POST['set_status']) && ($_POST['set_status'] == 'zerar')) {
    $id_evento = isset($_POST['id']) ? $_POST['id'] : NULL;
    $id_clt = isset($_POST['clt']) ? $_POST['clt'] : NULL;
    $retorno = $eventos->removeEvento($id_evento, $id_clt);
    //$retorno = false;
    if ($retorno) {
        echo json_encode(array('status' => true, 'msg' => 'Exclusão realizada com sucesso!'));
    } else {
        echo json_encode(array('status' => false, 'msg' => 'Não foi possível remover evento.'));
    }
    exit();
}

// carrega dados da tabela rh_eventos no modal
if (isset($_POST['carregaCampos'])) {
    $campos_eventos = $eventos->getEventoById($_POST['id']);
    echo json_encode($campos_eventos);
    exit();
}

// Consulta do Participante
$qr_clt = mysql_query("SELECT * FROM rh_clt WHERE id_clt = '$clt' AND id_regiao = '$regiao'");
$row_clt = mysql_fetch_assoc($qr_clt);

// Ação do Formulário
if (isset($_POST['pronto'])) {

    extract($_POST);

    $data = implode('-', array_reverse(explode('/', $data)));

    if ($evento != 10 and ! empty($dias)) {
        $data_retorno = date('Y-m-d', strtotime("+" . $dias . " days", strtotime($data)));
    }

    if ($evento == 200) {
        $sql_update_clt = ", data_demi = '$data'";
    }
    $qr_status = mysql_query("SELECT * FROM rhstatus WHERE codigo = '$evento'");
    $row_status = mysql_fetch_assoc($qr_status);

    // verifica se clt está em evento
    $query_teste = "SELECT * FROM rh_eventos
                    WHERE id_clt = '$clt' 
                    AND '$data' <= data
                    AND status = 1;";
    $resp = mysql_query($query_teste);
    if (mysql_num_rows($resp)) {
        ?>
        <h2>NÃO É POSSÍVEL CRIAR EVENTO.</h2>
        <p><button onClick="javascript:history.back()" class="botao">Voltar</button></p>
        <?php
        //header('location:form_eventos.php?enc=' . str_replace('+', '--', encrypt("$regiao&$clt&$ultimo_evento&$data")));
        exit();
    } else {
        $dados_evento = array(
            'id_clt' => $clt,
            'id_regiao' => $regiao,
            'id_projeto' => $projeto,
            'data' => $data,
            'data_retorno' => $data_retorno,
            'dias' => $dias,
            'obs' => $observacao
        );
        $result = $eventos->cadEvento($evento, $dados_evento);
//        $query = "INSERT INTO rh_eventos(id_clt,id_regiao,id_projeto,nome_status,cod_status,id_status,data,data_retorno,dias,obs)
//              VALUES ('$clt','$regiao','$projeto','{$row_status['especifica']}','{$row_status['codigo']}','{$row_status['id_status']}','$data','$data_retorno','$dias','$observacao')";
//        $result = mysql_query($query);
        $ultimo_evento = $result['ultimo_id'];
        if ($result) {
            ?>

            <script type="text/javascript">
                parent.window.location.href = 'form_evento.php?enc=<?= str_replace('+', '--', encrypt("$regiao&$clt&$ultimo_evento&$data")) ?>';
                if (parent.window.hs) {
                    var exp = parent.window.hs.getExpander();
                    if (exp) {
                        exp.close();
                    }
                }
            </script>

            <?php
        }
    }
    exit();
}

// dados para o select de eventos
$qr_status = mysql_query("SELECT * FROM rhstatus WHERE status_reg = '1' AND codigo NOT IN (10,90,40,200) AND tipo IS NULL ORDER BY tipo,especifica ASC");
$options['-1'] = "-- Selecione uma Ocorrência --";
while ($row_status = mysql_fetch_assoc($qr_status)) {
    if (($row_status['codigo'] == '50' and ( $row_clt['sexo'] == 'f' or $row_clt['sexo'] == 'F')) or ( $row_status['codigo'] == '51' and ( $row_clt['sexo'] == 'm' or $row_clt['sexo'] == 'M')) or ( $row_status['codigo'] != '50' and $row_status['codigo'] != '51')) {
        $options[$row_status['codigo']] = abreviacao($row_status['especifica']);
    }
}
?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>:: Intranet :: <?= $row_clt['nome'] ?></title>
        <link href="folha/sintetica/folha.css" rel="stylesheet" type="text/css">
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css">
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <script src="../js/ramon.js" type="text/javascript"></script>
        <script type="text/javascript" src="../js/jquery-1.8.3.min.js"></script>
        <script type="text/javascript" src="../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script type="text/javascript" src="../js/jquery.ui.datepicker-pt-BR.js"></script>
        <script src="../js/jquery.validationEngine-2.6.js" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine-pt_BR-2.6.js" type="text/javascript"></script>
        <script src="../js/global.js" type="text/javascript"></script>
        <!--<script type="text/javascript" src="http://code.jquery.com/jquery-1.10.2.min.js"></script>-->
        <!--script type="text/javascript">
        $(document).ready(function() {
            $('#imprime').click(function() {
                
                parent.window.location.href='form_evento.php?enc=<?= str_replace('+', '--', encrypt("$regiao&$clt&$ultimo_evento&$data")) ?>';
                         if(parent.window.hs) {
                           var exp = parent.window.hs.getExpander();
                           if(exp) {
                                 exp.close();
                           }
                         }
                
            })
        });
        </script-->
        <script type="text/javascript">
                $(function() {
                    $('.data').datepicker({
                        dateFormat: 'dd/mm/yy',
                        changeMonth: true,
                        changeYear: true,
                        yearRange: '2005:c+1'
                    });

                    $("#evento").change(function() {
                        // verifica se está aguardando demição
                        if ($(this).val() == 200) {
                            $("#dias").removeClass("validate[required]");
                        } else {
                            $("#dias").addClass("validate[required]");
                        }
                    });

                    // habilita novo evento sem que o funcionário volte para atv normal
                    var novo_evento = true;
                    $("#novo_evento").click(function() {
                        if (novo_evento) {
                            var confirmacao = confirm("ATENÇÃO! Ao criar um novo evento para este funcionário, ele passará direto do evento atual para o novo evento sem passar por \"Atividade Normal\". Deseja realmente criar um novo evento?");
                            if (confirmacao) {
                                $("#cel_evento").html('<?= montaSelect($options, null, 'name="evento" id="evento" class="validate[required,custom[select]]"') ?>');
                                $("#row_dias").removeClass("hidden");
                                $("#dias").addClass("validate[required]");
                                $("#row_retorno").removeClass("hidden");
                                novo_evento = false;
                                $(this).html("Cancelar");
                                $.post("../methods.php", {method: 'verificaRetorno', id_clt: '<?= $clt ?>'}, function(data) {
                                    $("#data").val(data.dados.data_retornoBR);
                                    $("#data").after("<span id='label-data'>" + data.dados.data_retornoBR + "</span>");
                                    $("#data").css("display", "none");
                                }, 'json');
                            }
                        } else {
                            $("#cel_evento").html('<b>Atividade Normal</b><input type="hidden" name="evento" value="10">');
                            $("#row_dias").addClass("hidden");
                            $("#row_retorno").addClass("hidden");
                            $("#data_retorno").val('');
                            $("#dias").removeClass("validate[required]").val('');
                            $("#data").val('');
                            $("#observacao").val('');
                            $("#label-data").remove();
                            $("#data").css("display", "initial");
                            novo_evento = true;
                            $(this).html("Novo Evento");
                        }
                    });

                    $("#data").change(function() {
//            $.post("../methods.php", {method: 'verificaRetorno', id_clt: '<?= $clt ?>'}, function(data) {
//                if (data.dados.data_retornoBR < $(this).val()) {
//                    alert('ATENÇÂO: Data de início é anteriro à data de retorno do evento atual.');
//                }
//            }, 'json');
                        $.post("../methods.php", {method: 'novo_retorno', data: $("#data").val(), dias: $("#dias").val()},
                        function(data) {
                            $("#data_retorno").val(data.data_retorno);
                        }, 'json');
                    });

                    $("#data_retorno").change(function() {
                        $.post("../methods.php",
                                {method: 'calcDias', data: $("#data").val(), data_retorno: $("#data_retorno").val()},
                        function(data) {
                            $("#dias").val(data.dias);
                        }, 'json');
                    });

                    $("#dias").change(function() {
                        $.post("../methods.php",
                                {method: 'novo_retorno', data: $("#data").val(), dias: $("#dias").val()},
                        function(data) {
                            $("#data_retorno").val(data.data_retorno);
                        }, 'json');
                    });

                });

                $(document).ready(function() {
                    $("form[name=form1]").validationEngine();
                });
//    function validaForm() {
//        d = document.form1;
//        if (d.evento.value == 'Selecione uma ocorrencia') {
//            alert('Selecione uma ocorrencia');
//            d.evento.focus();
//            return false;
//        }
//        if (d.data.value == '') {
//            alert('Insira a data de ocorrencia');
//            d.data.focus();
//            return false;
//        }
//        if (d.evento.value != 200) {
//            if (d.dias.value == '') {
//                alert('Insira a duracao da ocorrencia');
//                d.dias.focus();
//                return false;
//            }
//        }
//        return true;
//    }
        </script>
        <style type="text/css">
            body {
                background-color:#FFF; margin:0px;
            }
            .secao {
                text-align:right !important; padding-right:3px; font-weight:bold;
            }
            .ui-datepicker{
                font-size: 12px;
            }
            .hidden{
                display:none;
            }
        </style>
    </head>
    <body>
        <div id="corpo">
            <table cellspacing="4" cellpadding="0" id="topo">
                <tr>
                    <td>
                        <?= '(' . $clt . ') ' . $row_clt['nome'] ?>
                    </td>
                </tr>
            </table>
            <form action="<?= $_SERVER['PHP_SELF'] ?>" method="post" name="form1" onSubmit="return validaForm()" style="width: 100%;">

                <table cellpadding="0" cellspacing="0" id="relatorio" style="width: 100%;">
                    <tr id="salario" style="border:0px;">
                        <td>Formul&aacute;rio de Evento</td>
                        <td><a href="alter_clt.php?clt=<?= $clt ?>&pro=<?= $row_clt['id_projeto'] ?>" target="_blank">Editar Cadastro <img src="folha/sintetica/seta_transparente.png"></a></td>
                    </tr>
                    <tr class="linha_um">
                        <td colspan="2">

                            <table cellspacing="0" cellpadding="4" style="font-size:12px; width:100%; margin:10px auto; background-color:#eee;" id="tableForm">
                                <?php if ($row_clt['status'] != 10) { ?>
                                    <tr>
                                        <td>&ensp;</td>
                                        <td>
                                            <button type="button" id="novo_evento">Novo Evento</button>
                                        </td>
                                    </tr>
                                <?php } ?>
                                <tr>
                                    <td class="secao">Ocorr&ecirc;ncia:</td>
                                    <td id="cel_evento">
                                        <?php
                                        if ($row_clt['status'] != 10) {
                                            echo '<b>Atividade Normal</b><input type="hidden" name="evento" value="10">';
                                        } else {
                                            echo montaSelect($options, null, 'name="evento" id="evento" class="validate[required,custom[select]]"');
                                            ?> 
       
                                        <?php } ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="36%" class="secao">Data da Ocorr&ecirc;ncia:</td>
                                    <td width="64%"><input name="data" id="data" class="data validate[required]" type="text" size="11" maxlength="10" onKeyUp="mascara_data(this)"></td>
                                </tr>
                                <?php
                                $class = ($row_clt['status'] != 10) ? 'class="hidden"' : '';
                                $required = ($row_clt['status'] == 10) ? 'class="validate[required]"' : '';
                                ?>
                                <tr id="row_dias" <?= $class ?>>
                                    <td class="secao">Dura&ccedil;&atilde;o da Ocorr&ecirc;ncia:</td>
                                    <td><input name="dias" id="dias" type="text" size="3" maxlength="3" <?= $required ?>> <b>dias</b></td>
                                </tr>
                                <tr id="row_retorno" <?= $class ?>>
                                    <td class="secao">Retorno da Ocorrência:</td>
                                    <td><input name="data_retorno" id="data_retorno" class="data" type="text" size="11" maxlength="10" onKeyUp="mascara_data(this)"></td>
                                </tr>
                                <tr>
                                    <td class="secao">Observa&ccedil;&atilde;o:</td>
                                    <td><textarea name="observacao" id="observacao" cols="20" rows="3" class="validate[required]"></textarea></td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td>
                                        <input type="hidden" name="clt"     value="<?= $clt ?>" />
                                        <input type="hidden" name="projeto" value="<?= $row_clt['id_projeto'] ?>" />
                                        <input type="hidden" name="regiao"  value="<?= $regiao ?>" />
                                        <input type="hidden" name="pronto"  value="1" />
                                        <input type="submit" class="botao"  value="Concluir">
                                    </td>
                                </tr>
                            </table>

                        </td>  
                    </tr>
                    <tr>
                        <td colspan="2">

                            <table cellspacing="0" cellpadding="4" style="font-size:12px; width:100%; margin:10px auto; background-color:#eee;">
                                <tr>                       
                                    <td>Evento</td>
                                    <td>Data</td>
                                    <td>Data de retorno</td>
                                    <td>Dias</td>
                                    <td style="text-align: center;">Editar</td>
                                    <td style="text-align: center;">Prorrogar</td>
                                    <td style="text-align: center;">Excluir</td>
                                    <td style="text-align: center;">Imprimir</td>
                                </tr>
                                <?php
                                $qr_historico_eventos = mysql_query("SELECT *, DATE_FORMAT( data_retorno,'%d/%m/%Y') as data_retorno_br,  DATE_FORMAT( data,'%d/%m/%Y') as data_br  FROM rh_eventos WHERE id_clt = '$clt' AND id_regiao = '$regiao'  AND status = '1' ORDER BY data DESC, id_evento DESC")or die(mysql_error());
                                $count = 0;
                                while ($row_evento = mysql_fetch_assoc($qr_historico_eventos)):
                                    $count++;
                                    $link_encri = str_replace('+', '--', encrypt("$regiao&$clt&$row_evento[id_evento]&$row_evento[data]"));
                                    ?>
                                    <tr>
                                        <td><?= $row_evento['nome_status'] ?></td>
                                        <td><?= $row_evento['data_br'] ?></td>
                                        <td><?= $row_evento['data_retorno_br'] ?></td>
                                        <td><?= $row_evento['dias'] ?></td>
                                        <!--td><a href="#"><img src="../imagens/icon-edit.gif" id="editarInf"/></a></td-->
                                        <td style="text-align: center;"><a href="edit_form.php?enc=<?php echo $link_encri; ?>" target="blank"><img src="../imagens/icon-edit.gif" id="editarInf"/></a></td>
                                        <td style="text-align: center;"> 
                                            <?php
                                            if ($row_evento['cod_status'] != 10) {
                                                if ($count == 1) {
                                                    ?> 
                                                    <a href="#" class="prorrogar" data-id="<?= $row_evento['id_evento'] ?>"><img src="../imagens/icones/icon-calendar.gif" title="Prorrogar" alt="Prorrogar"></a>
                                                <?php } else { ?> 
                                                    <img src="../imagens/icones/icon-calendar-old.png" alt="Não é possível prorrogar este evento." title="Não é possível prorrogar este evento."> 
                                                    <?php
                                                }
                                            } else {
                                                ?>
                                                <img src="../imagens/icones/icon-calendar-old.png" alt="Não é possível prorrogar este evento." title="Não é possível prorrogar este evento."> 
                                            <?php } ?>
                                        </td>

                                        <td style="text-align: center;"> <?php if ($count == 1) { ?> <a href="#" onclick="zerarStatus(<?= $row_evento['id_evento'] ?>,<?= $row_evento['id_clt'] ?>)"><img src="../imagens/icon-excluir.png"/></a><?php } else { ?> <img src="../imagens/icon-excluir-old.png"/> <?php } ?></td>
                                        <td style="text-align: center;"><a href="form_evento.php?enc=<?php echo $link_encri; ?>" target="blank"><img src="../imagens/impressora2.png" width="18px;" id="imprime"/></a></td>
                                    </tr>
                                    <?php
                                endwhile;
                                ?>
                            </table>
                        </td>
                    </tr>
                </table>
                <div id="dialog-form" title="Motivo da Prorrogação" style="font-size:14px;">
                    <input type="hidden" name="id_evento" id="id_evento" value="" />
                    <input type="hidden" name="id_user" id="id_user" value="<?php echo $_COOKIE['logado'] ?>" />
                    <input type="hidden" name="data_retorno" id="data_retorno" value="" />
                    <p style="text-align: left;">
                        <label for="dias" style=" font-weight: bold;" >Quantidade de dias (a partir da data atual de retorno):</label><br />
                        <input type="number" name="dias" id="dias-2" min="0" style=" width: 4em; height: 30px; margin: 3px 0px;" > <button type="button" id="calc-data">Calcular Prorrogação</button>
                    </p>
                    <p style="text-align: left;">
                        <label for="data_termino" style=" font-weight: bold;" >Data de Prorrogação:</label><br />
                        <input type="text" name="data_prorrogada" id="data_prorrogada"  class="data" style=" width: 8em; height: 30px; margin: 3px 0px;" >
                    </p>
                    <p style="text-align: left;">
                        <label for="obs" style=" font-weight: bold;" >Motivo:</label><br />
                        <textarea name="obs" id="obs" style=" width: 425px; height: 80px; margin: 3px 0px;"></textarea>
                    </p>
                    <div id="message_erro"></div>
                    <!-- Allow form submission with keyboard without duplicating the dialog button -->
                    <input type="button" tabindex="-1" style="position:absolute; top:-1000px" id="submit-prorrogar">
                </div>
            </form>

            <script>
                $(function() {
                    $('.prorrogar').click(function() {
                        var input_data_prorrogada = $("#data_prorrogada");
                        var input_obs = $("#obs");
                        var id = $(this).data('id');


                        $.post('<?= $_SERVER['PHP_SELF']; ?>', {id: id, carregaCampos: true}, function(data) {
                            if (data != 0) {
                                input_data_prorrogada.val(data.data_retorno_br);
                                input_obs.val(data.obs);

                                $("#data_retorno").val(data.data_retorno_br);
                                $("#id_evento").val(id);
                            } else {
                                alert('Falha ao carregar evento!');
                                exit();
                            }
                        }, 'json');
                        dialog.dialog("open");
                    });
                    dialog = $("#dialog-form").dialog({
                        autoOpen: false,
                        height: 320,
                        width: 550,
                        modal: true,
                        buttons: {
                            "Salvar": salvarProrrogacao,
                            Cancel: function() {
                                dialog.dialog("close");
                            }
                        }
                    });

                    $('#calc-data').click(function() {
                        var id = $("#id_evento").val();
                        var dias = $("#dias-2").val();
                        $.post('../methods.php', {id: id, calcData: true, qtdDias: dias}, function(data) {
                            if (data != 0) {
                                $("#data_prorrogada").val(data.data);
                            } else {
                                alert('Falha ao carregar evento!');
                                exit();
                            }
                        }, 'json');
                    });

                    $("#dias-2").change(function() {
                        if ($(this).val() < 0) {
                            $(this).val(0);
                        }
                    });

                });
                function zerarStatus(id, clt) {

                    //console.log($(this).parents("tr"));

                    $.post('<?= $_SERVER['REQUEST_URI']; ?>', {id: id, clt: clt, set_status: 'zerar'}, function(data) {
                        if (data.status) {
                            alert('Evento deletado com sucesso!');
                            window.location.reload();
                        } else {
                            alert('Não foi possível deletar evento!');
                        }
                    }, 'json');
                }
                function salvarProrrogacao() {
                    var id_evento = $("#id_evento").val();
                    var id_user = $("#id_user").val();
                    var data_retorno = $("#data_retorno").val();
                    var mensagem = $("#obs").val();
                    var data_prorrogada = $("#data_prorrogada").val();
                    $.ajax({
                        url: "../methods.php",
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
                                thickBoxClose("#dialog-form");
                                //history.go(0);
                                window.location.reload();
                            } else {
                                var html = "";
                                $.each(data.erro, function(key, value) {
                                    html += "<p>" + value + "</p><br />";
                                });
                                $("#message_erro").html(html);
                            }
                        }
                    });
                }

            </script>
        </div>
    </body>
</html>