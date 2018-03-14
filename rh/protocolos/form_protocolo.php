<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Protocolos de Entregas</title>
        <link href="../../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-note.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../css/progress.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap-rh.css" rel="stylesheet" type="text/css">
        <link href="../../resources/dropzone/dropzone.css" rel="stylesheet" type="text/css">
    </head>
    <body>
        <?php include_once("../../template/navbar_default.php"); ?>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small> - Protocolos de Entregas</small></h2></div>
                </div><!-- /.col-lg-12 -->
            </div><!-- /.row -->
            <form class="form-horizontal" action="?m=salvar" method="post" name="form1" id="form1">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <?php if ($objProtocoloEntrega->getIdProtocolosEntregas()) { ?>
                            <input type="hidden" name="id_protocolos_entregas" value="<?= $objProtocoloEntrega->getIdProtocolosEntregas() ?>">
                        <?php } ?>
                        <div class="form-group">
                            <label for="tipo_protocolo" class="col-sm-2 control-label">Região</label>
                            <div class="col-sm-4">
                                <?= montaSelect(getRegioes(), $objProtocoloEntrega->getIdRegiao(), 'class="form-control" id="id_regiao" name="id_regiao"'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="tipo_protocolo" class="col-sm-2 control-label">Projeto</label>
                            <div class="col-sm-4">
                                <?= montaSelect(getProjetos($objProtocoloEntrega->getIdRegiao()), $objProtocoloEntrega->getIdProjeto(), 'class="form-control" id="id_projeto" name="id_projeto"'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="tipo_protocolo" class="col-sm-2 control-label">Identificador</label>
                            <div class="col-sm-4">
                                <input type="text" name="identificador" class="form-control" value="<?= $objProtocoloEntrega->getIdentificador() ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="tipo_protocolo" class="col-sm-2 control-label">Tipo de Protocolo</label>
                            <div class="col-sm-4">
                                <?= montaSelect($optTiposProtocolos, $objProtocoloEntrega->getIdTipoProtocolo(), 'class="form-control" id="id_tipo_protocolo" name="id_tipo_protocolo"'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="tipo_protocolo" class="col-sm-2 control-label">Competência</label>
                            <div class="col-sm-4">
                                <div class="input-group">
                                    <?php $selectMes = $objProtocoloEntrega->getMesCompetencia() ?$objProtocoloEntrega->getMesCompetencia(): date('m'); ?>
                                    <?= montaSelect(mesesArray(), $selectMes, 'class="form-control" id="mes_competencia" name="mes_competencia"'); ?>
                                    <div class="input-group-addon">/</div>
                                    <?php $selectAno = $objProtocoloEntrega->getAnoCompetencia() ?$objProtocoloEntrega->getAnoCompetencia(): date('Y'); ?>
                                    <?= montaSelect(anosArray(date('Y') - 5, date('Y')), $selectAno, 'class="form-control" id="ano_competencia" name="ano_competencia"'); ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="tipo_protocolo" class="col-sm-2 control-label">Descricao</label>
                            <div class="col-sm-9">
                                <textarea name="descricao" id="descricao" rows="4" class="form-control"><?= $objProtocoloEntrega->getDescricao() ?></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="tipo_protocolo" class="col-sm-2 control-label">Anexos</label>
                            <div class="col-sm-9">
                                <?php if (TRUE) { ?>
                                    <div class="row">
                                        <?php foreach ($listaArquivos as $values) { ?>
                                            <?php $x = explode('.', $values['nome']); ?>
                                            <?php $icon = ['txt' => 'fa-file-text-o', 'pdf' => 'fa-file-pdf-o text-danger', 'doc' => 'fa-file-word-o', 'docx' => 'fa-file-word-o']; ?>
                                            <div class="col-sm-4 col-md-3">
                                                <div class="thumbnail">
                                                    <a class="btn btn-link btn-block" href="uploads/<?= $values['nome'] ?>" target="_blank">
                                                        <i class="fa <?= $icon[$x[1]] ?> fa-5x"></i>
                                                    </a>

                                                    <div class="caption">
                                                        <button type="button" class="btn btn-danger btn-block excluir" role="button" data-id="<?= $values['id_protocolos_arquivos'] ?>"><i class="fa fa-trash-o"></i> Excluir</button>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>

                                    </div>
                                <?php } ?>



                                <div id="dropzone" class="dropzone"></div>
                                <div id="ids_arquivos"></div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer text-right">
                        <a class="btn btn-default" href="?m=index" onclick="window.history.back();"><i class="fa fa-reply"></i> Voltar</a>
                        <button class="btn btn-primary" type="submit"><i class="fa fa-floppy-o"></i> Salvar</button>
                    </div>
                </div>

            </form>
            <?php include_once '../../template/footer.php'; ?>
        </div><!-- /.container -->
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../js/jquery.maskMoney.js"></script>
        <script src="../../js/jquery.maskedinput.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>

        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script src="../../resources/js/dropzone.js"></script>

        <script>
                            $(document).ready(function () {
                                Dropzone.autoDiscover = false;
                                var myDropzone = new Dropzone("#dropzone", {
                                    url: "?m=dropzone_up",
                                    addRemoveLinks: true,
                                    maxFilesize: 10,

                                    autoQueue: true,

                                    dictResponseError: "Erro no servidor!",
                                    dictCancelUpload: "Cancelar",
                                    dictFileTooBig: "Tamanho máximo: 10MB",
                                    dictRemoveFile: "Remover Arquivo",
                                    canceled: "Arquivo Cancelado",
                                    acceptedFiles: '.doc,.docx,.pdf,.txt',
                                    init: function () {
                                        this.on("success", function (file, response) {
                                            var json = JSON.parse(response);
                                            console.log(typeof json);
                                            $('#ids_arquivos').append($('<input>', {type: 'hidden', name: 'id_protocolo_arquivo[]', value: json.id}));
                                        });
                                    }
                                });

                                $('.excluir').click(function () {
                                    var $this = $(this);
                                    bootConfirm('Tem certeza que deseja excluir?', 'Atenção', function (confirm) {
                                        if (confirm) {
                                            $.post('?m=excluir_arquivo', {id: $this.data('id')}, function (data) {
                                                bootAlert(data.msg, 'Atenção', null, data.status);
                                                if (data.status == 'success') {
                                                    $this.parent().parent().parent().remove();
                                                }
                                            }, 'json');
                                        }
                                    }, 'danger');
                                });

                                $('#id_regiao').change(function () {
                                    var id_regiao = $(this).val()
                                    $.post('/intranet/methods.php', {method: 'carregaProjetos', regiao: id_regiao, 'default': 2}, function (retorno) {
                                        console.log(retorno);
                                        $("#id_projeto").html(retorno);
                                    });
                                });
                            });

        </script>
    </body>
</html>
