<?php
include_once('../../../classes_permissoes/acoes.class.php');
$objAcao = new Acoes();
?>
<?php if (count($pagamentos) > 0) { ?>



<?php

$sqlBancos = mysql_query("SELECT id_banco, nome FROM bancos WHERE status_reg = 1 ORDER BY nome") or die(mysql_error());

$arrayBancos = array("" => "-- SELECIONE --");

while ($rowBancos = mysql_fetch_assoc($sqlBancos)) {

    $arrayBancos[$rowBancos['id_banco']] = $rowBancos['id_banco'] . " - " . utf8_encode($rowBancos['nome']);



}

?>

<script>



    $("body").on('click', ".editar_valor", function(){

        var id = $(this).data("valor");

        var tipo = $(this).data("tipo");

        cria_carregando_modal();

        $.post("includes/ajax_edit_saida.php", {bugger:Math.random(), id:id, tipo:tipo}, function(resultado){

            bootAlert(resultado, 'Detalhe da '+tipo, null, 'primary');

            remove_carregando_modal();

        });

    });







    //            $('body').on('click', '.editar_valor', function(){

    //                var $this = $(this);

    //                var html =

    //                    $('<div>', { class: 'form-group' }).append(

    //                        $('<div>', { class: "col-sm-8" }).append(

    //                                $('<div>', { class: "text-bold", html:"Competência:" }),

    //                                $('<div>', { class: "input-group" }).append(

    //                                    $('<?= montaSelect(mesesArray(), date('m'), 'class="form-control input-sm validate[required]" id="mes" name="ir[mes_competencia]"') ?>'),

    //                                    $('<div>', { class: "input-group-addon", html:"/" }),

    //                                    $('<?= montaSelect(anosArray(2015), date('Y'), 'class="form-control input-sm validate[required]" id="ano" name="ir[ano_competencia]"') ?>')

    //                                )

    //                        ),

    //                        $('<div>', { class: "col-sm-4" }).append(

    //                            $('<div>', { class: "text-bold", html:"Data de Venc.:" }),

    //                            $('<div>', { class: "", id: "" }).append(

    //                                $('<input>', { type:"text", class: "data form-control input-sm validate[required]", id:"", name:"ir[data_vencimento]" })

    //                            )

    //                        ),

    //                    $('<div>', { class: "form-group" }).append(

    //                        $('<div>', { class: "col-sm-6" }).append(

    //                            $('<div>', { class: "text-bold", html:"Valor Bruto:" }),

    //                            $('<div>', { class: "", id:"" }).append(

    //                                $('<input>', { type:"text", class: "valor form-control input-sm validate[required]", id:"valors_r", name:"ir[valor]", value:$this.data('valor') })

    //                            )

    //                        ),

    //                        $('<div>', { class: "col-sm-6" }).append(

    //                            $('<div>', { class: "text-bold", html:"Valor Líquido:" }),

    //                            $('<div>', { class: "", id:"" }).append(

    //                                $('<input>', { type:"text", class: "valor form-control input-sm validate[required]", id:"valors_r", name:"ir[valor]", value:$this.data('valor') })

    //                            )

    //                        )

    //                    ),

    //                    $('<div>', { class: "form-group" }).append(

    //                        $('<div>', { class: "col-sm-6" }).append(

    //                            $('<div>', { class: "text-bold", html:"Valor Mão de Obra:" }),

    //                            $('<div>', { class: "", id:"" }).append(

    //                                $('<input>', { type:"text", class: "valor form-control input-sm validate[required]", id:"valors_r", name:"ir[valor]", value:$this.data('valor_mao_obra') })

    //                            )

    //                        ),

    //                        $('<div>', { class: "col-sm-6" }).append(

    //                            $('<div>', { class: "text-bold", html:"Numero do Documento:" }),

    //                            $('<div>', { class: "", id:"" }).append(

    //                                $('<input>', { type:"text", class: "valor form-control input-sm validate[required]", id:"valors_r", name:"ir[valor]", value:$this.data('valor') })

    //                            )

    //                        )

    //                    ),

    //                    $('<div>', { class: "form-group" }).append(

    //                        $('<div>', { class: "col-sm-6" }).append(

    //                            $('<div>', { class: "text-bold", html:"Banco:" }),

    //                            $('<div>', { class: "", id:"" }).append('<?= montaSelect($arrayBancos,null, 'id="id_banco" name="ir[id_banco]" class="form-control input-sm validate[required,custom[select]]"') ?>')

    //                        ),

    //                        $('<div>', { class: "col-sm-6" }).append(

    //                            $('<div>', { class: "text-bold", html:"Data de Emissão da NF:" }),

    //                            $('<div>', { class: "", id:"" }).append(

    //                                $('<input>', { type:"text", class: "valor form-control input-sm validate[required]", id:"valors_r", name:"ir[valor]", value:$this.data('dt_emissao_nf') })

    //                            )

    //                        )

    //                    ),

    //                    $('<div>', { class: 'clear' }),

    //                    $('<br>', { class: "clear" }),

    //                        $('<br>', { class: "clear" }),

    //                        $('<div>', { class: "panel panel-default", id:"div_unidades_r" }).append(

    //                            $('<div>', { class: "panel-heading text-bold", html:"Unidades da saída" }).append(

    //                                $('<button>', { type:"button", name:"dupl", id:"add_unidade_r", class: "btn btn-xs btn-info pull-right" }).append(

    //                                    $('<i>', { class: "fa fa-plus" })

    //                                ),

    //                                $('<div>', { class: "clear" })

    //                            )

    //                        ),

    //                        $('<div>', { class: "form-group" }).append(

    //                            $('<div>', { class: "col-sm-12" }).append(

    //                                $('<div>', { id:"dropzone_ir", class: "dropzone", style:"height: 250px!important; min-height: 250px!important;" })

    //                            )

    //                        ),

    //                        $('<div>', { class: "clear" }),

    //                        $('<script>').html("var myDropzone_ir = new Dropzone('#dropzone_ir',{ url: 'action_gera_saida.php', addRemoveLinks : true, maxFilesize: 50, autoQueue: false, dictResponseError: 'Erro no servidor!', dictCancelUpload: 'Cancelar', dictFileTooBig: 'Tamanho máximo: 50MB', dictRemoveFile: 'Remover Arquivo', canceled: 'Arquivo Cancelado', acceptedFiles: '.jpg,.gif,.png,.pdf,.JPG,.GIF,.PNG,.PDF' /*, success: function(file, responseText){ console.log(responseText); }*/ });")

    //                    );

    //

    //                html.find('.valor').maskMoney({prefix:'R$ ', allowNegative: true, thousands:'.', decimal:','});

    //                html.find('.data').datepicker({ dateFormat: 'dd/mm/yy', changeMonth: true, changeYear: true, yearRange: '2005:c+1' });

    //

    //

    //                new BootstrapDialog({

    //                    title: 'Edição',

    //                    message: html,

    //                    closable: false,

    //                    type: 'type-info',

    //                    buttons: [{

    //                            label: 'Cancelar',

    //                            action: function (dialog) {

    ////                                typeof dialog.getData('callback') === 'function' && dialog.getData('callback')(false);

    //                                dialog.close();

    //                            }

    //                        }, {

    //                            label: 'Salvar',

    //                            cssClass: 'btn-info',

    //                            action: function (dialog) {

    //                                $.post("includes/item_2.php", { method:'editar_valor_saida', id_saida:$('#id_saida').val(), valor: $('#valor_nova_saida').val(),id_saida:$('#id_saida').val(), valor: $('#valor_nova_saida').val(),id_saida:$('#id_saida').val(), valor: $('#valor_nova_saida').val() }, function(resposta){

    //                                    console.log(resposta);

    //                                    bootAlert('Valor Alteradoo!', '', function(){ window.location.reload(); }, 'success');

    //                                });

    //                            }

    //                        }]

    //                }).open();

    //            });



    $('body').on('click', '.excluir_saida', function(){

        var id_saida = $(this).data('key');

        new BootstrapDialog({

            title: 'Exclusão',

            message: "Confirmar exclusão de saída?",

            closable: false,

            type: 'type-info',

            buttons: [{

                label: 'Cancelar',

                action: function (dialog) {

//                                typeof dialog.getData('callback') === 'function' && dialog.getData('callback')(false);

                    dialog.close();

                }

            }, {

                label: 'Confirmar',

                cssClass: 'btn-info',

                action: function (dialog) {

                    $.post("includes/item_2.php", { method:'excluir_saida', id_saida:id_saida }, function(resposta){

                        console.log(resposta);

                        bootAlert('Saida excluida!', '', function(){ window.location.reload(); }, 'success');

                    });

                }

            }]

        }).open();

    });



</script>




<div style="padding:0 0 10px;">
    <button type="button" id="btn_provisionamento">Provisionamento</button>
    <button type="button" id="btn_historico">Histórico de Lançamentos</button>
</div>

<div id="tb_historico">
    <table class="table table-bordered table-condensed table-striped valign-middle">

        <thead>

        <tr class="active">

            <th colspan="8">Histórico de Lançamentos</th>

        </tr>

        <tr class="active">

            <th>Número</th>

            <th>Competência</th>

            <th>Nome</th>

            <th>Valor</th>

            <th>Vencimento</th>

            <th>Boleto</th>

            <th>Comprovante <br>de Pagamento</th>

            <th></th>

        </tr>

        </thead>

        <tbody>

        <?php

        foreach ($pagamentos as $pagamento) { ?>

            <tr class="<?= ($pagamento['status'] == 1) ? 'danger' : 'success'; ?>">

                <td><?= $pagamento['id_saida'] ?></td>

                <td><?= $pagamento['mes_competencia'].'/'.$pagamento['ano_competencia'] ?></td>

                <td><?= $pagamento['nome'] ?></td>

                <td class="center">R$ <?= number_format($pagamento['valor'],2,',','.'); ?></td>

                <td class="center"><?= $pagamento['data_vencimento_f'] ?></td>

                <td class="center">

                    <?php

                    $data_vencimento = explode('-',$pagamento['data_vencimento']);

//                    $diretorio = "comprovantes/saida/{$data_vencimento[0]}/{$data_vencimento[1]}";
                    $diretorio = "comprovantes";
                    

                    $sql_file_pg = 'SELECT * FROM saida_files WHERE id_saida = '.$pagamento['id_saida'];

                    $res_file_pg = mysql_query($sql_file_pg);

                    while($res = mysql_fetch_array($res_file_pg)){

                        $link_encryptado_pg = ''; ?>

                        <a class="btn btn-xs btn-info" target="_blank" title="Comprovante de pagamento" href="/intranet/<?=$diretorio?>/<?= $res['id_saida_file'].'.'. $pagamento['id_saida'].$res['tipo_saida_file'] ?>"><i class="fa fa-paperclip"></i></a>

                    <?php } ?>

                </td>

                <td class="center">

                    <?php

                    $sql_file_pg = 'SELECT * FROM saida_files_pg WHERE id_saida = '.$pagamento['id_saida'];

                    $res_file_pg = mysql_query($sql_file_pg);

                    while($res = mysql_fetch_array($res_file_pg)){

                        $link_encryptado_pg = '';

                        ?>

                        <!-- ALTERAÇÃO SOLICITADA POR SABINO, DIA 27/10/2016.

                        title="Comprovante de pagamento" => title="Nota ou Boleto"-->

                        <a class="btn btn-xs btn-info" target="_blank" title="Nota ou Boleto" href="/intranet/<?=$diretorio?>/<?= $res['id_pg'].'.'. $pagamento['id_saida'].'_pg'.$res['tipo_pg'] ?>"><i class="fa fa-paperclip"></i></a>

                    <?php } ?>

                </td>

                <td>

                    <!--                    <button title="Editar Valor"  data-valor="<?= $pagamento['id_saida'] ?>" data-key="<?= $pagamento['id_saida'] ?>" type="button" class="btn btn-xs btn-warning editar_valor"><i class="fa fa-edit"></i></button>-->

                    <?php if($pagamento['status'] == 1 && $objAcao->verifica_permissoes(123)) { ?>

                        <!--<button title="Editar Valor"  data-valor="<?= $pagamento['id_saida'] ?>" data-key="<?= $pagamento['id_saida'] ?>" type="button" class="btn btn-xs btn-warning editar_valor"><i class="fa fa-edit"></i></button>-->

                    <?php } ?>

                    <?php if($pagamento['status'] == 1 && $objAcao->verifica_permissoes(125)) { ?>

                        <button title="Excluir Saida" data-key="<?= $pagamento['id_saida'] ?>" type="button" class="btn btn-xs btn-danger excluir_saida"><i class="fa fa-trash-o"></i></button>

                    <?php } ?>

                </td>

            </tr>

        <?php } ?>

        </tbody>

    </table>



    <?php } else { ?>

        <div class="message-box message-yellow">

            <p>Não há registros.</p>

        </div>

        <?php

    } ?>
</div>

<div id="tb_provisionamento">
    <table class="table table-bordered table-condensed table-striped valign-middle">
        <thead>
        <tr class="active">
            <th colspan="7">Provisionamento</th>
        </tr>
        <tr class="active">
            <th>Competência</th>
            <th><span title="Valor mensal previsto no contrato">Valor Previsto em Contrato</span></th>
            <th><span title="Saídas Pagas">Valor Despendido</span></th>
            <th><span title="Saídas Pagas">Valor Pago</span></th>
            <th><span title="Saídas que ainda não foram pagas">Dívida em aberto</span></th>
            <th><span title="Valor Previsto + ívida em aberto - Valor Pago">Provisão Acumulada</span></th>
            <th>Situação</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($arr_tabela as $valor) { ?>
            <?php
            $class = ($valor['class']) ? 'alert-'. $valor['class'] : '';
            ?>
            <tr class="<?= $class ?>">
                <td><?php echo $valor['competencia'] ?></td>
                <td class="text-right"><?php echo $valor['valor_contrato'] ?></td>
                <td class="text-right"><?php echo $valor['valor_tespendido'] ?></td>
                <td class="text-right"><?php echo $valor['valor_pago'] ?></td>
                <td class="text-right"><?php echo $valor['valor_a_pagar'] ?></td>
                <td class="text-right"><?php echo $valor['provisao_acumulada'] ?></td>
                <td class="center"><?= $valor['status'] ?></td>
            </tr>
        <?php } ?>
        </tbody>
        <tfoot>
        <tr>
            <td><strong>TOTAL:</strong></td>
            <td class="text-right"><?= number_format($tot_contrato_previsto, 2, ',', '.') ?></td>
            <td class="text-right"><?= number_format($tot_tespendido, 2, ',', '.') ?></td>
            <td class="text-right"><?= number_format($tot_pago_periodo, 2, ',', '.') ?></td>
            <td class="text-right"><?= number_format($tot_a_pago_periodo, 2, ',', '.') ?></td>
            <td class="text-right"><?= number_format($tot_a_pagar_previsto, 2, ',', '.') ?></td>
            <td>&emsp;</td>
        </tr>
        </tfoot>
    </table>
</div>

