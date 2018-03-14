<?php
/*
 * CONTROLLER: eventos/intex.php 
 * TELA:       acao_evento
 */
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>:: Intranet :: Eventos :: <?= $row_clt['nome'] ?></title>
        <link rel="shortcut icon" href="../../favicon.ico" />
        <link href="../../net1.css" rel="stylesheet" type="text/css" />
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css"/>
        <link href="../../css/progress.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">               
        <script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../../js/jquery.ui.datepicker-pt-BR.js" type="text/javascript"></script>
        <script src="../../js/ramon.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine-2.6.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js" type="text/javascript"></script>
        <script src="../../js/global.js" type="text/javascript"></script>
        <script src="edit_evento.js" type="text/javascript"></script>
        <script src="../../js/jquery.form.js" type="text/javascript"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <!--<script src="http://malsup.github.com/jquery.form.js" type="text/javascript"></script>-->
        <!--        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
                <script src="../../resources/js/bootstrap-dialog.min.js"></script>-->



        <script type="text/javascript">
            // funcao para converter data
            function converteData(data) {
                var dataArr = data.split('/');
                return dataArr[2] + "-" + dataArr[1] + "-" + dataArr[0];
            }

            $(document).ready(function () {
                $('.data').datepicker({
                    dateFormat: 'dd/mm/yy',
                    changeMonth: true,
                    changeYear: true,
                    yearRange: '2005:c+1'
                });

                // recupera o evento anterior e verifica se a data de encerramento é maior q a data de inicio deste
                $("#data").change(function () {
                    var id_clt = $("#id_clt").val();
                    var dataIniNovo = converteData($(this).val()); // converte a data para o formato em ingles
                    $.post("../../methods.php", {method: 'verificaRetorno', id_clt: id_clt}, function (data) {
                        console.log("data nova: " + dataIniNovo + "data retorno: " + data.dados.data_retorno);
                        if (data.dados.data_retorno > dataIniNovo) {
                            alert('ATENÇÂO: Data de início é anteriro à data de retorno do evento atual.');
                        }
                    }, 'json');
                });

                // habilita novo evento sem que o funcionário volte para atv normal
                var novo_evento = true;
                $("#novo_evento").click(function () {
                    if (novo_evento) {
                        var confirmacao = confirm("ATENÇÃO! Ao criar um novo evento para este funcionário, ele passará direto do evento atual para o novo evento sem passar por \"Atividade Normal\". Deseja realmente criar um novo evento?");
                        if (confirmacao) {
                            $("#cel_evento").html('<?= montaSelect($options, null, 'name="evento" id="evento" class="validate[required,custom[select]]"') ?>');
//                            $("#dias").addClass("validate[required]");
                            novo_evento = false;
                            $(this).html("Cancelar");
                            $.post("../../methods.php", {method: 'verificaRetorno', id_clt: '<?= $clt ?>'}, function (data) {
//                                var nova_data = (data.dados.pericia == 1) ? data.dados.data_retornoBR : data.dados.data_retornoFinalBR;
                                var nova_data = data.dados.data_retornoBR;
                                
                                // td isso é para colocar um dia a mais na data-
                                var arr_data = nova_data.split('/');
                                var dia = parseInt(arr_data[0])+1;
                                dia = ((dia < 10)?"0"+dia:dia);
                                var mes = arr_data[1];
                                var ano = arr_data[2];
                                var nova_data = dia+"/"+mes+"/"+ano;
                                // fim do dia a mais na data--------------------
                                
                                $("#data").val(nova_data);
//                                $("#data").after("<span id='label-data'>" + nova_data + "</span>");
//                                $("#data").css("display", "none");
                            }, 'json');
                        }
                    } else {
                        $("#cel_evento").html('<b>Atividade Normal</b><input type="hidden" name="evento" value="10">');
                        $("#row_dias").css('display', 'none');
                        $("#row_retorno").css('display', 'none');
                        $("#row_retorno_final").css('display', 'none');
                        $("#data_retorno").val('');
//                        $("#dias").removeClass("validate[required]").val('');
                        $("#data").val('');
                        $("#observacao").val('');
                        $("#label-data").remove();
                        $("#data").css("display", "initial");
                        novo_evento = true;
                        $(this).html("Novo Evento");
                    }    
                });

                // habilita o validate engine
                $("form[name=form1]").validationEngine();


                // UPLOAD DO ARQUIVO DE EVENTO
                $(".anexar").click(function () {
                    var evento = $(this).data("id");
                    //var click = $(this).data("click");
                    $("#id_evento").val(evento); // muda o val do input #id_evento
                    //$("#form_up_evento").removeClass('hidden'); // exibe o form de upload
                    //$("#form_up_evento").show('fast'); // exibe o form de upload
                    thickBoxModal("Anexar Documento", "#modal_anexo", 180, 450);
                });

                var bar = $('.bar');
                var percent = $('.percent');
                var status = $('#status');

                $('#form_up_evento').validationEngine({promptPosition: "bottomLeft"});
                $('#form_up_evento').ajaxForm({
                    clearForm: true,
                    beforeSend: function () {
                        status.empty();
                        var percentVal = '0%';
                        bar.width(percentVal);
                        percent.html(percentVal);
                    },
                    uploadProgress: function (event, position, total, percentComplete) {
                        var percentVal = percentComplete + '%';
                        $('progress').attr('value', percentComplete);
                        $(".progress-bar span").css("width", percentComplete + "%");
                        percent.html(percentVal);
                    },
                    success: function () {
                        var percentVal = '100%';
                        $('progress').attr('value', '100');
                        $(".progress-bar span").css("width", "100%");
                        percent.html(percentVal);
                    },
                    complete: function (xhr) {
                        status.html(xhr.responseText);
                        status.removeClass("hidden");
                    }
                });
                // FIM DO UPLOAD DO ARQUIVO DE EVENTO


                // habilita os campos que devem ser exibidos no caso de o 
                // funcionario for voltar para atividade normal
                if (<?= $row_clt['status'] ?> != 10) {
                    $("#row_data").css("display", "block");
                    $("#row_obs").css("display", "block");
                }


                // habilita e desabilita data final
                $("body").on("change", "#evento", function () {
//                    var evento = $(this).val();
                    $("#row_data").fadeIn();
                    $("#row_dias").fadeIn();
                    $("#row_obs").fadeIn();
                    $("#row_retorno").fadeIn();

//                    $.post("../../methods.php",
//                            {method: 'getPericia', evento: evento},
//                    function (data) {
//                        if (data.pericia == 1) {
//                            $("#row_retorno_final").css('display', 'none');
//                            $("#row_retorno").fadeIn();
//                        } else {
//                            $("#row_retorno_final").fadeIn();
//                            $("#row_retorno").css('display', 'none');
//                        }
//                    }, 'json');
        });

                $("body").on('change', '#data', function(){
                    var id_clt = $(this).data("id");
                    var data_ocorrencia = $(this).val();
//                    alert(id_status);
//                        var confirma =  confirm("ATENÇÃO O FUNCIONARIO ESTÁ EM PERIODO DE FÉRIAS! Ao criar um novo evento para este funcionário, ele passará direto do evento atual para o novo evento sem passar por \"Atividade Normal\". Deseja realmente criar um novo evento?"); 
                            
                            $.post('verificar-atividade.php', {id_clt: id_clt, data_ocorrencia: data_ocorrencia }, function (data){
                                if(data > 0){
                                  alert("ATENÇÃO! O FUNCIONÁRIO ESTÁ AGENDADO PARA ENTRAR DE FÉRIAS NESTE PERIODO, DESEJA CONTINUAR MESMO ASSIM?");
//                                    if (confirmar){
//                                        $("#cel_evento").html('<?= montaSelect($options, null, 'name="evento" id="evento" class="validate[required,custom[select]]"') ?>');
//                                        $('#novo_evento').trigger('click');
//                                    } else {
                                        $('#data').val('');
//                                    }
                                }
                                
//                               alert("gravado");
//                               
                            }, 'json');
                           
                      
                });

            });
        </script>
        <style type="text/css">
            .secao {
                text-align:right !important; padding-right:3px; font-weight:bold;
            }
            .ui-datepicker{
                font-size: 12px;
            }
            .hidden, #row_retorno_final, #row_retorno, #row_dias, #row_data, #row_obs{
                display:none;
            }
            .show{
                display:block;
            }
            .data{
                width:7em;
            }
        </style>
    </head>
    <body class="novaintra">
        <div id="content">
            <div id="head">
                <img src="../../imagens/logomaster<?php echo $usuario['id_master']; ?>.gif" class="fleft" style="margin-right: 25px;" alt="">
                <div class="fleft">
                    <h2>Eventos</h2>
                    <!--<h3><?= $regiao['regiao'] ?> <?= (isset($projeto)) ? " - " . $projeto['nome'] : ""; ?></h3>-->
                    <h3><?= '(' . $clt . ') ' . $row_clt['nome'] ?></h3>
                </div>
            </div>
            <br class="clear">
            <br/>

            <?php if ($clt_em_evento) { // ------------------------------------- ?>

                <h2>NÃO É POSSÍVEL CRIAR EVENTO.</h2>
                <h3>Verifique as data de Início e termino do evento.</h3>
                <p><button onClick="javascript:history.back()" class="botao">Voltar</button></p>

            <?php } else if ($sucesso) { // ------------------------------------ ?>

                <script type="text/javascript">
                    parent.window.location.href = '<?= $_SERVER['REQUEST_URI'] ?>&AMP;enc=<?= str_replace('+', '--', encrypt("$id_regiao&$clt&$ultimo_evento&$data")) ?>';
                        if (parent.window.hs) {
                            var exp = parent.window.hs.getExpander();
                            if (exp) {
                                exp.close();
                            }
                        }
                </script>

            <?php } else { // -------------------------------------------------- ?>

                <?php if (isset($resp)) {
                    ?>
                    <div class="<?= $resp['class'] ?>" style="margin:10px 0; padding: 10px;"><?= $resp['msg'] ?></div>
                <?php }
                ?>

                <form action="<?= $_SERVER['REQUEST_URI'] ?>" method="post" name="form1" style="width: 100%;">
                    <a class="botao" href="index.php"> &lt;&lt; Voltar</a>
                    <br>
                    <fieldset>
                        <legend>Formulário de Evento</legend>
                        <a href="../alter_clt.php?clt=<?= $clt ?>&AMP;pro=<?= $row_clt['id_projeto'] ?>" target="_blank" class="botao" style="float:right; margin-right: 20px;"><img src="../../imagens/icones/icon-edit.gif" alt=""> Editar Cadastro</a>
                        <?php 
                            if (!$ACOES->verifica_permissoes(127)) {
                                $disabled = 'disabled';
                            }
                            if ($row_clt['status'] != 10) { 
                        ?>
                            <p><label class="first">&emsp;</label> <button <?= $disabled ?> type="button" " id="novo_evento">Novo Evento</button></p>
                        <?php } ?>
                        <p>
                            <label class="first">Ocorrência:</label>
                            <span id="cel_evento">
                                <?php
                                if ($row_clt['status'] != 10) {
                                    echo '<b>Atividade Normal</b><input type="hidden" name="evento" value="10">';    
                                } else {
                                    echo montaSelect($options, null, "  $disabled name='evento' id='evento' class='validate[required,custom[select]]'");
                                }
                                ?>
                            </span>
                        </p>
                      
                        <p id="row_data" <?= $class ?>>
                            <label class="first">Data da Ocorrência:</label>
                            <input <?= $disabled ?> name="data" id="data" class="data validate[required]" data-id="<?php echo ($row_clt['id_clt'])?>" type="text" size="11" maxlength="10" onKeyUp="mascara_data(this)">
                        </p>
                        <?php ?>
                        <p id="row_dias" >
                            <label class="first">Duração da Ocorrência:</label>
                            <input <?= $disabled ?> name="dias" id="dias" class="dias" type="number" min="0" style="width:5em;"> <b>dias</b>
                        </p>
                        <p id="row_retorno">
                            <label class="first">Data de Retorno:</label><!-- Nome Anterior: Retorno da Ocorrência -->
                            <input <?= $disabled ?> name="data_retorno" id="data_retorno" class="data" type="text" onKeyUp="mascara_data(this)">
                        </p>
    <!--                        <p id="row_retorno_final" >
                            <label class="first">Data Final de Retorno:</label> Nome Anterior: Retorno da Ocorrência 
                            <input name="data_final" id="data_final" class="data" type="text" onKeyUp="mascara_data(this)">
                        </p>-->
                        <p id="row_obs" <?= $class ?>>
                            <label class="first">Observação:</label>
                            <textarea <?= $disabled ?> name="observacao" id="observacao" cols="20" rows="3"></textarea>
                        </p>
                        <input <?= $disabled ?> type="hidden" name="id_clt"  id="id_clt"  value="<?= $clt ?>" />
                        <input <?= $disabled ?> type="hidden" name="projeto" id="projeto" value="<?= $row_clt['id_projeto'] ?>" />
                        <input <?= $disabled ?> type="hidden" name="regiao"  id="regiao"  value="<?= $id_regiao ?>" />
                        <input <?= $disabled ?> type="hidden" name="pronto"  id="pronto"  value="1" />
                        <p class="controls">
                            <input type="submit" id="botao_concluir" value="Concluir">
                        </p>
                    </fieldset>

                    <br>



                    <table id="table1" class="grid" style="border-collapse: collapse; width:100%; page-break-after:auto; border:0;">
                        <thead>
                            <tr>   
                                <?php if (in_array($_COOKIE['logado'], $usuarios_f71)) { ?>
                                    <th>ID *</th>
                                <?php } ?>
                                <th>Evento</th>
                                <th>Data</th>
                                <th>Data da Retorno</th>
                                <!--th>Data de Retorno Final</th-->
                                <th>Dias</th>
                                <th colspan="4">AÇÕES</th>
                                <th colspan="2">ANEXOS</th>
                            </tr>                       
                        </thead>
                        <tbody>
                            <?php
                            $count = 0;
                            foreach ($hist_eventos as $row_evento) {

                                $class = ($cont++ % 2 == 0) ? "even" : "odd";
                                $count++;
                                $link_encri = str_replace('+', '--', encrypt("$id_regiao&$clt&$row_evento[id_evento]&$row_evento[data]"));
                                ?>
                                <tr class="<?php echo $class ?>">
                                    <?php if (in_array($_COOKIE['logado'], $usuarios_f71)) { ?>
                                        <td style="text-align: center;"><?= $row_evento['id_evento'] ?></td>
                                    <?php } ?>
                                    <td style="text-align: center;"><?= $row_evento['nome_status'] ?></td>
                                    <td style="text-align: center;"><?= $row_evento['data_br'] ?></td>
                                    <td style="text-align: center;"><?= (!empty($row_evento['data_retorno_br']) && $row_evento['data_retorno_br'] != '00/00/0000') ? $row_evento['data_retorno_br'] : '-'; ?></td>
                                    <!--td style="text-align: center;"><?= (!empty($row_evento['data_retorno_br']) && $row_evento['data_retorno_final_br'] != '00/00/0000') ? $row_evento['data_retorno_final_br'] : '-'; ?></td-->
                                    <td style="text-align: center;"><?= ($row_evento['dias']) ? $row_evento['dias'] : '-' ?></td>
                                    <td style="text-align: center;"> 
                                        <?php
                                        if ($count == 1 && $row_evento['cod_status'] != 10) { // só fica habilitado na primeira linha e se cod_status != 10
                                            ?> 
                                            <a href="index.php?tela=form_evento&AMP;enc=<?php echo $link_encri; ?>"><img src="../../imagens/icon-edit.gif" class="editarInf" title="Editar" alt="Editar"></a>
                                        <?php } else { ?> 
                                            <img src="../../imagens/icones/icon-edit-dis.gif" class="editarInf" title="Editar" alt="Editar">
                                        <?php } ?>
                                    </td>
                                    <td style="text-align: center;"> 
                                        <?php
                                        if ($count == 1 && $row_evento['prorrogavel']) {
                                            ?>
                                            <a href="#" class="prorrogar" data-id="<?= $row_evento['id_evento'] ?>"><img src="../../imagens/icones/icon-calendar.gif" title="Prorrogar" alt="Prorrogar"></a>
                                        <?php } else { ?> 
                                            <img src="../../imagens/icones/icon-calendar-old.png" alt="Não é possível prorrogar este evento." title="Não é possível prorrogar este evento."> 
                                        <?php } ?>
                                    </td>
                                    <td style="text-align: center;"> 
                                        <?php if ($count == 1 && $ACOES->verifica_permissoes(93)) { ?> 
                                            <a href="#" onclick="zerarStatus(<?= $row_evento['id_evento'] ?>,<?= $row_evento['id_clt'] ?>)" title="Excluir"><img src="../../imagens/icon-excluir.png" alt="Excluir"></a>
                                        <?php } else { ?>
                                            <img src="../../imagens/icon-excluir-old.png" alt="Não é possível excluir este evento." title="Não é possível excluir este evento."> 
                                        <?php } ?>
                                    </td>
                                    <td style="text-align: center;"><a href="form_evento.php?enc=<?php echo $link_encri; ?>" target="_blank"><img src="../../imagens/impressora2.png" width="18" id="imprime<?= $count ?>" title="Imprimir" alt="Imprimir"></a></td>
                                    <td style="text-align: center;"><a href="#" class="anexar" data-id="<?= $row_evento['id_evento'] ?>"><img src="../../imagens/icones/icon-pdf.gif" width="18" id="imprime<?= $count ?>" title="Anexar" alt="Anexar"></a></td>
                                    <td style="text-align: center;"><a href="../lista_AnexoEventos.php?id=<?= $row_evento['id_evento'] ?>&AMP;voltar=1"><img src="../../imagens/ver_anexo.gif" width="18" id="imprime<?= $count ?>" title="Ver Anexo" alt="Ver Anexo"></a></td>
                                </tr>
                                <?php
                            }
                            ?>
                        </tbody>
                    </table>
                    <!-- modal de prorrogacao -->
                    <div id="dialog-form" title="Motivo da Prorrogação" style="font-size:14px; display: none;">
                        <input type="hidden" name="id_evento" id="id_evento" value="" />
                        <input type="hidden" name="id_user" id="id_user" value="<?php echo $_COOKIE['logado'] ?>" />
                        <input type="hidden" name="data_retorno2" id="data_retorno2" value="" />
                        <p style="text-align: left;">
                            <label for="dias" style=" font-weight: bold;" >Quantidade de dias (a partir da data atual de retorno):</label><br />
                            <input type="number" name="dias2" id="dias2" class="dias" min="0" style=" width: 4em; height: 30px; margin: 3px 0px;" > <button type="button" id="calc-data">Calcular Prorrogação</button>
                        </p>
                        <p style="text-align: left;">
                            <label for="data_prorrogada" style=" font-weight: bold;" >Data de Prorrogação:</label><br />
                            <input type="text" name="data_prorrogada" id="data_prorrogada"  class="data" style=" width: 8em; height: 30px; margin: 3px 0px;" >
                        </p>
                        <p style="text-align: left;">
                            <label for="obs" style=" font-weight: bold;" >Motivo:</label><br />
                            <textarea name="obs" id="obs" style=" width: 425px; height: 80px; margin: 3px 0px;"></textarea>
                        </p>
                        <div id="message_erro"></div>
                        <!-- Allow form submission with keyboard without duplicating the dialog button -->
                        <input type="button" tabindex="-1" style="position:absolute; top:-1000px" id="submit-prorrogar" value="x">
                    </div>
                    <!-- fim do modal de prorrogacao -->
                </form>

                <!-- upload de eventos -->
                <div id="modal_anexo" class="hidden">
                    <form action="../../include/upload_atestado.php" method="post" id="form_up_evento" enctype="multipart/form-data">
                        <div style="margin: .5em 0;">
                            <input type="file" name="atestado" id="atestado" class="validate[required,custom[docsType]]">
                            <input type="hidden" name="id_evento" id="id_evento" value="">
                            <input type="hidden" name="reg" id="reg" value="<?= sprintf('%03d', $row_clt['id_regiao']); ?>">
                            <input type="hidden" name="projeto" id="projeto" value="<?= sprintf('%03d', $row_clt['id_projeto']); ?>">
                            <input type="hidden" name="ID_participante" id="id_participante" value="<?= sprintf('%03d', $row_clt['id_clt']); ?>">
                            <input type="hidden" name="tipo_contratacao" id="tipo_contratacao" value="2">
                            <input type="submit" value="Salvar">
                        </div>

                        <progress max="100" value="0">
                            <!-- Browsers that support HTML5 progress element will ignore the html inside `progress` element. Whereas older browsers will ignore the `progress` element and instead render the html inside it. -->
                            <div class="progress-bar">
                                <span style="width:0%"></span>
                            </div>
                        </progress>
                        <div id="status" class="hidden back-green"></div>
                    </form>
                </div>
                <!-- fim do upload de eventos -->
                <script>
                    $(function () {
                        $('.prorrogar').click(function () {
                            var input_data_prorrogada = $("#data_prorrogada");
                            var input_obs = $("#obs");
                            var id = $(this).data('id');


                            $.post('<?= $_SERVER['REQUEST_URI']; ?>', {id: id, carregaCampos: true}, function (data) {
                                if (data != 0) {
    //                                    var valor_campo = ((data.pericia == 1) ? data.data_retorno_br : data.data_retorno_final_br); // comentado enquanto nao normaliza
                                    var valor_campo = ((data.data_retorno_br !== '') ? data.data_retorno_br : data.data_retorno_final_br); // provisório enquanto nao normaliza
                                    input_data_prorrogada.val(valor_campo);
                                    input_obs.val(data.obs);

                                    $("#data_retorno2").val(data.data_retorno_br);
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
                                Cancel: function () {
                                    dialog.dialog("close");
                                }
                            }
                        });

                        $('#calc-data').click(function () {
                            var id = $("#id_evento").val();
                            var dias = $("#dias2").val();
                            $.post('../../methods.php', {id: id, calcData: true, qtdDias: dias}, function (data) {
                                if (data != 0) {
                                    $("#data_prorrogada").val(data.data);
                                } else {
                                    alert('Falha ao carregar evento!');
                                    exit();
                                }
                            }, 'json');
                        });
                    
                       
                    });
                    function zerarStatus(id, clt) {

                        //console.log($(this).parents("tr"));

                        $.post('<?= $_SERVER['REQUEST_URI']; ?>', {id: id, clt: clt, set_status: 'zerar'}, function (data) {
                            if (data.status) {
                                alert(data.msg);
                                window.location.reload();
                            } else {
                                alert(data.msg);
                            }
                        }, 'json');
                    }
                    function salvarProrrogacao() {
                        var id_evento = $("#id_evento").val();
                        var id_user = $("#id_user").val();
                        var data_retorno = $("#data_retorno2").val();
                        var mensagem = $("#obs").val();
                        var data_prorrogada = $("#data_prorrogada").val();
                        $.ajax({
                            url: "../../methods.php",
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
                            success: function (data) {
                                if (data.status) {
                                    thickBoxClose("#dialog-form");
                                    //history.go(0);
                                    window.location.reload();
                                } else {
                                    var html = "";
                                    $.each(data.erro, function (key, value) {
                                        html += "<p>" + value + "</p><br />";
                                    });
                                    $("#message_erro").html(html);
                                }
                            }
                        });
                    }

                </script>
            <?php } ?>
        </div>
    </body>
</html>