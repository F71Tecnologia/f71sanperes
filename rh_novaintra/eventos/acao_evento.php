<?php
/*
 * CONTROLLER: eventos/intex.php 
 * TELA:       acao_evento
 */
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Eventos :: <?= $row_clt['nome'] ?></title>

        <link rel="shortcut icon" href="../../favicon.png" />

        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <!--<link href="../../resources/css/jquery-ui-1.9.2.custom-teste.css" rel="stylesheet" media="screen">-->
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../css/progress.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap-rh.css" rel="stylesheet" type="text/css">

        <style type="text/css">
            .secao {
                text-align:right !important; padding-right:3px; font-weight:bold;
            }
            .ui-datepicker {
                font-size: 12px;
            }
            .hidden, #row_retorno_final, #row_retorno, #row_dias, #row_data, #row_obs {
                display:none;
            }
            .show {
                display:block;
            }
            .btn.btn-rounded {
                border-radius: 9999px;
            }
        </style>
    </head>
    <body>

        <?php include("../../template/navbar_default.php"); ?>

        <div class="container">

            <div class="row">

                <div class="col-lg-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS<small> - Formulário de Evento</small></h2></div>


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

                        <form action="<?= $_SERVER['REQUEST_URI'] ?>" method="post" name="form1" id="form1" class="form-horizontal top-margin1" enctype="multipart/form-data">
                            <div class="panel panel-default">
                                <div class="panel-body">

                                    <div class="row">
                                        <div class="form-group">
                                            <label class="col-lg-2 control-label">Funcionário:</label>
                                            <label class="col-lg-9 control-label text-left"><?= '(' . $clt . ') ' . $row_clt['nome'] ?></label>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group">
                                            <label for="evento" class="col-lg-2 control-label">Ocorrência:</label>
                                            <div id="cel_evento" class="col-lg-9">
                                                <?php
                                                if ($row_clt['status'] != 10) {
                                                    echo "<b>Atividade Normal</b><input type=\"hidden\" name=\"evento\" id=\"evento\" value=\"10\">\n";
                                                } else {
                                                    echo montaSelect($options, null, 'name="evento" id="evento" class="form-control validate[required,custom[select]]"');
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="row_data" class="row">
                                        <div class="form-group">
                                            <!-- <?= $class ?> <-- tem que fazer alguma coisa com isso -->
                                            <label for="data" class="col-lg-2 control-label">Data da Ocorrência:</label>
                                            <div class="col-lg-4">
                                                <div class="input-group">
                                                    <input name="data" id="data" class="form-control data validate[required]">
                                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="row_dias" class="row">
                                        <div class="form-group">
                                            <label for="dias" class="col-lg-2 control-label">Duração da Ocorrência:</label>
                                            <div class="col-lg-4">
                                                <div class="input-group">
                                                    <input name="dias" id="dias" class="form-control dias" type="number" min="0" <?= $required ?>>
                                                    <span class="input-group-addon">dias</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!--                                    <div id="row_retorno" class="row">
                                                                            <div class="form-group">
                                                                                <label for="data_retorno" class="col-lg-2 control-label">Data da Perícia:</label> Nome Anterior: Retorno da Ocorrência 
                                                                                <div class="col-lg-4">
                                                                                    <div class="input-group">
                                                                                        <input name="data_retorno" id="data_retorno" class="form-control data" type="text" >
                                                                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>-->

                                    <div id="row_retorno_final" class="row">
                                        <div class="form-group">
                                            <label for="data_final" class="col-lg-2 control-label">Data Final de Retorno:</label><!-- Nome Anterior: Retorno da Ocorrência -->
                                            <div class="col-lg-4">
                                                <div class="input-group">
                                                    <input name="data_final" id="data_final" class="form-control data" type="text">
                                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="row_obs" class="row">
                                        <div class="form-group">
                                            <label for="observacao" class="col-lg-2 control-label">Observação:</label>
                                            <div class="col-lg-9">
                                                <textarea name="observacao" id="observacao" class="form-control" rows="3"></textarea>
                                            </div>
                                        </div>
                                    </div>

                                </div><!-- /.panel-body -->

                                <div class="panel-footer text-right">
                                    <input type="hidden" name="id_clt"  id="id_clt"  value="<?= $clt ?>" />
                                    <input type="hidden" name="projeto" id="projeto" value="<?= $row_clt['id_projeto'] ?>" />
                                    <input type="hidden" name="status_clt" id="status_clt" value="<?= $row_clt['status'] ?>" />
                                    <input type="hidden" name="regiao"  id="regiao"  value="<?= $id_regiao ?>" />
                                    <input type="hidden" name="pronto"  id="pronto"  value="1" />
                                    <input type="hidden" name="home" id="home" value="">
                                    <input type="hidden" name="method" id="method" value="acao_evento">
                                    <input type="hidden" name="id_evento" id="id_evento" value="">
                                    <div class="col-xs-6 text-left">
                                        <a href="index.php" class="btn btn-default"><span class="fa fa-reply"></span>&nbsp;&nbsp;Voltar</a>
                                        <!--<button type="button" class="btn btn-default" id="volta_index" name="voltar"><span class="fa fa-reply"></span>&nbsp;&nbsp;Voltar</button>-->
                                        <a href="../alter_clt.php?clt=<?= $clt ?>&AMP;pro=<?= $row_clt['id_projeto'] ?>" target="_blank" class="btn btn-default"><i class="fa fa-edit"></i> Editar Cadastro</a>
                                    </div>
                                    <div class="col-xs-6 text-right">
                                        <?php if ($row_clt['status'] != 10) { ?>
                                            <button type="button" class="btn btn-default" id="novo_evento">Novo Evento</button>
                                        <?php } ?>
                                        <input type="submit" class="btn btn-primary" value="Concluir">
                                    </div>
                                    <div class="clear"></div>
                                </div><!-- /.panel-footer -->
                            </div><!-- /.panel-defaut -->

                            <hr>
                            <h4>Histórico de Eventos</h4>

                            <table id="table1" class="table table-condensed table-bordered table-hover">
                                <thead>
                                    <tr class="bg-primary valign-middle">
                                        <?php if (in_array($_COOKIE['logado'], $usuarios_f71)) { ?>
                                            <th class="text-center">ID *</th>
                                        <?php } ?>
                                        <th style="width:30%;">Evento</th>
                                        <th class="text-center" style="width:15%;">Início</th>
                                        <th class="text-center" style="width:15%;">Fim</th>
    <!--                                        <th class="text-center">Data de Retorno Final</th>-->
                                        <th class="text-center" style="width:10%;">Dias</th>
                                        <th colspan="6">&emsp;</th>
                                    </tr>                       
                                </thead>
                                <tbody>
                                    <?php
                                    $count = 0;
                                    foreach ($hist_eventos as $row_evento) {
                                        $count++;
                                        $link_encri = str_replace('+', '--', encrypt("$regiao&$clt&$row_evento[id_evento]&$row_evento[data]"));
                                        ?>
                                        <tr class="valign-middle">
                                            <?php if (in_array($_COOKIE['logado'], $usuarios_f71)) { ?>
                                                <td class="text-center"><?= $row_evento['id_evento'] ?></td>
                                            <?php } ?>
                                            <td><?= $row_evento['nome_status'] ?></td>
                                            <td class="text-center"><?= $row_evento['data_br'] ?></td>
                                            <td class="text-center"><?= (!empty($row_evento['data_retorno_br']) && $row_evento['data_retorno_br'] != '00/00/0000') ? $row_evento['data_retorno_br'] : '-'; ?></td>
                                            <!--<td class="text-center"><?= (!empty($row_evento['data_retorno_br']) && $row_evento['data_retorno_final_br'] != '00/00/0000') ? $row_evento['data_retorno_final_br'] : '-'; ?></td>-->
                                            <td class="text-center"><?= ($row_evento['dias']) ? $row_evento['dias'] : '-' ?></td>
                                            <td class="text-center">
                                                <?php
                                                if ($count == 1 && $row_evento['cod_status'] != 10) { // só fica habilitado na primeira linha e se cod_status != 10
                                                    ?> 
                                                    <a class="tip btn btn-xs btn-success btn-rounded link_go" data-toggle="tooltip" data-placement="top" title="Editar" 
                                                       href="#" data-method="form_evento" data-id-evento="<?= $row_evento['id_evento'] ?>">
                                                        <i class="fa fa-pencil"></i>
                                                    </a>
                                                <?php } else { ?>
                                                    <button disabled class="tip btn btn-xs btn-rounded btn-success"><i class="fa fa-pencil"></i></button>
                                                <?php } ?>
                                            </td>
                                            <td class="text-center"> 
                                                <?php
                                                if ($count == 1 && $row_evento['prorrogavel']) {
                                                    ?> 
                                                    <a class="tip prorrogar btn btn-xs btn-rounded btn-warning" data-toggle="tooltip" data-placement="top" title="Prorrogar" href="#" data-id="<?= $row_evento['id_evento'] ?>">
                                                        <i class="fa fa-calendar"></i>
                                                    </a>
                                                <?php } else { ?> 
                                                    <button disabled class="btn btn-xs btn-rounded btn-warning"><i class="fa fa-calendar"></i></button>
                                                <?php } ?>
                                            </td>
                                            <td class="text-center"> 
                                                <?php if ($count == 1 && $ACOES->verifica_permissoes(93)) { ?>  
                                                    <a class="excluir tip btn btn-xs btn-rounded btn-danger" data-toggle="tooltip" data-placement="top" title="Excluir" href="#" 
                                                       data-id-evento="<?= $row_evento['id_evento'] ?>" data-id-clt="<?= $row_evento['id_clt'] ?>" title="Excluir">
                                                        <i class="fa fa-times"></i>
                                                    </a>
                                                <?php } else { ?> 
                                                    <button disabled class="btn btn-xs btn-rounded btn-danger"><i class="fa fa-times"></i></button>
                                                <?php } ?>
                                            </td>
                                            <td class="text-center">
                                                <a class="tip btn btn-xs btn-rounded btn-default" data-toggle="tooltip" data-placement="top" title="Imprimir" href="form_evento.php?enc=<?php echo $link_encri; ?>" target="_blank">
                                                    <i class="fa fa-print"></i>
                                                </a>
                                            </td>
                                            <td class="text-center">
                                                <a class="anexar tip btn btn-xs btn-rounded btn-default" data-toggle="tooltip" data-placement="top" title="Anexar" href="#" data-id="<?= $row_evento['id_evento'] ?>">
                                                    <i class="fa fa-paperclip"></i>
                                                </a>
                                            </td>
                                            <td class="text-center">
                                                <a class="tip btn btn-xs btn-info btn-rounded link_go" data-toggle="tooltip" data-placement="top" title="Ver Anexo" 
                                                   href="#" data-id-evento="<?= $row_evento['id_evento'] ?>" data-method="lista_anexo">
                                                    <i class="fa fa-search"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                            <!-- para guardar a ulr completa da pagina (usado no jquery) -->
                        </form>
                    <?php } ?>
                </div><!-- /.col-lg-12 --> 
            </div><!-- /.row -->

            <?php include_once '../../template/footer.php'; ?>
        </div><!-- /.container -->

        <div class="teste" data-key="a" data-id="x"></div>

        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/tooltip.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script src="../../js/jquery.maskedinput-1.3.1.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../js/jquery.form.js" type="text/javascript"></script>
        <script src="../../js/jquery.ui.datepicker-pt-BR.js" type="text/javascript"></script>
        <script src="../../js/jquery.maskedinput.min.js" type="text/javascript"></script>
        <!--<script src="../../js/ramon.js" type="text/javascript"></script>-->

        <!-- scripts da pagina -->
        <script src="../../resources/js/rh/eventos/edit_evento.js" type="text/javascript"></script>
        <script src="../../resources/js/rh/eventos/acao_evento.js" type="text/javascript"></script>
        <script src="../../resources/js/rh/eventos/prorrogar_evento.js" type="text/javascript"></script>
        <script src="../../resources/js/rh/eventos/eventos.js" type="text/javascript"></script>

    </body>
</html>