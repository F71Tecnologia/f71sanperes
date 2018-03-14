<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: LIB</title>

        <link rel="shortcut icon" href="favicon.png" />

        <!-- Bootstrap -->
        <link href="resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="resources/css/bootstrap-dialog.min.css" rel="stylesheet" media="screen">
        <link href="resources/css/main.css" rel="stylesheet" media="screen">
        <link href="resources/css/bootstrap-note.css" rel="stylesheet" media="screen">
        <link href="resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" media="screen">
        <link href="css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <div class="container">
            <div class="page-header box-principal-header"><h2><span class="fa fa-book"></span> - Biblioteca F71</h2></div>

            <fieldset>
                <legend>Botões</legend>                
                <button type="submit" class="btn btn-primary"><i class="fa fa-filter"></i> Filtrar</button>
                <button type="button" class="btn btn-success"><span class="fa fa-file-excel-o"></span>&nbsp;&nbsp;Exportar para Excel</button>
                <button type="button" class="btn btn-primary"><span class="fa fa-share-square-o"></span>&nbsp;&nbsp;Exportar</button>                
                <a class="btn btn-success" href="#"><i class="fa fa-plus"></i> Novo Cadastro</a>
                <a class="btn btn-warning"><span class="fa fa-power-off"></span>&nbsp;&nbsp;Finalizar</a>
                <button type="submit" class="btn btn-primary">Enviar</button>
                <button type="button" class="btn btn-default"><span class="fa fa-reply"></span>&nbsp;&nbsp;Voltar</button>
                <input type="submit" class="btn btn-primary" value="Cadastrar">
                <input type="submit" value="Gerar" class="btn btn-primary">
            </fieldset>

            <br /><br />

            <fieldset>
                <legend>Mensagens</legend>
                <div class="alert alert-dismissable alert-danger">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <strong>Oh snap!</strong> <a href="#" class="alert-link">Change a few things up</a> and try submitting again.
                </div>
                <div class="alert alert-dismissable alert-warning">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <strong>Oh snap!</strong> <a href="#" class="alert-link">Change a few things up</a> and try submitting again.
                </div>
                <div class="alert alert-dismissable alert-info">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <strong>Oh snap!</strong> <a href="#" class="alert-link">Change a few things up</a> and try submitting again.
                </div>
                <div class="alert alert-dismissable alert-success">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <strong>Oh snap!</strong> <a href="#" class="alert-link">Change a few things up</a> and try submitting again.
                </div>
            </fieldset>
            
            <br /><br />
            
            <fieldset>
                <legend>Notes</legend>
                Adicionar: <span><</span><span>link href="resources/css/bootstrap-note.css" rel="stylesheet" media="screen"</span><span>></span>
                <div class="note">
                    <h4 class="note-title">Default note title</h4>
                    Default note text here.
                </div>

                <div class="note note-success">
                    <h4 class="note-title">Success note title</h4>
                    Success note text here.
                </div>

                <div class="note note-danger">
                    <h4 class="note-title">Danger note title</h4>
                    Danger note text here.
                </div>

                <div class="note note-warning">
                    <h4 class="note-title">Warning note title</h4>
                    Warning note text here.
                </div>

                <div class="note note-info">
                    <h4 class="note-title">Info note title</h4>
                    Info note text here.
                </div>
            </fieldset>

            <br /><br />

            <fieldset>
                <legend>Breadcrumb</legend>
                <ol>
                    <li>Criar array: <strong>$breadcrumb_config = array("nivel"=>"../", "key_btn"=>"36", "area"=>"Prestação de Contas", "ativo"=>"Contratos de Serviços Terceirizados", "id_form"=>"form1");</strong></li>
                    <li>Criar array (opcional): <strong>$breadcrumb_pages = array("Page1"=>"teste1.php", "Page2"=>"teste2.php"); </strong></li>
                    <li>Adicionar input hidden: <strong>name="home" id="home" value=""</strong></li>
                </ol>
            </fieldset>

            <br /><br />

            <fieldset>
                <legend>Ícones</legend>
                <ol>
                    <li><img src="imagens/icones/icon-view.gif" /> Visualizar</li>
                    <li><img src="imagens/icones/icon-delete.gif" /> Excluir</li>
                    <li><img src="imagens/icones/icon-delete-disabled.gif" /> Excluir Desabilitado</li>
                    <li><img src="imagens/icones/icon-edit.gif" /> Editar</li>
                    <li><img src="imagens/icones/icon-copy.png" /> Duplicar / Copiar</li>
                </ol>                                                                
            </fieldset>

            <br /><br />

            <fieldset>
                <legend>Tabela</legend>
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Criado EM</th>
                            <th>Título</th>
                            <th>Ultima alteração</th>
                            <th>Respondido Por</th>
                            <th>Prioridade</th>
                            <th>Status</th>
                            <th colspan="2">Açoes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="" id="1551">
                            <td>1551</td>
                            <td>18/11/2014 20:11</td>
                            <td>TESTE</td>
                            <td>19/11/2014</td>
                            <td>F71 Amanda</td>
                            <td>Baixa</td>
                            <td>Respondido</td>
                            <td><img src="imagens/icones/icon-view.gif" title="Visualizar" class="bt-image center-block" data-type="ver" data-key="1551"></td>
                            <td><img src="imagens/icones/icon-delete.gif" title="Fechar" class="bt-image center-block" data-type="fechar" data-key="1551"></td>
                        </tr>
                        <tr class="" id="1551">
                            <td>1551</td>
                            <td>18/11/2014 20:11</td>
                            <td>TESTE</td>
                            <td>19/11/2014</td>
                            <td>F71 Amanda</td>
                            <td>Baixa</td>
                            <td>Respondido</td>
                            <td><img src="imagens/icones/icon-view.gif" title="Visualizar" class="bt-image center-block" data-type="ver" data-key="1551"></td>
                            <td><img src="imagens/icones/icon-delete.gif" title="Fechar" class="bt-image center-block" data-type="fechar" data-key="1551"></td>
                        </tr>
                        <tr class="" id="1551">
                            <td>1551</td>
                            <td>18/11/2014 20:11</td>
                            <td>TESTE</td>
                            <td>19/11/2014</td>
                            <td>F71 Amanda</td>
                            <td>Baixa</td>
                            <td>Respondido</td>
                            <td><img src="imagens/icones/icon-view.gif" title="Visualizar" class="bt-image center-block" data-type="ver" data-key="1551"></td>
                            <td><img src="imagens/icones/icon-delete.gif" title="Fechar" class="bt-image center-block" data-type="fechar" data-key="1551"></td>
                        </tr>
                    </tbody>
                </table>
            </fieldset>

            <br /><br />

            <fieldset>
                <legend>Includes</legend>
                <ol>
                    <li>Rodapé: <strong>template/footer.php</strong></li>
                </ol>
            </fieldset>

            <fieldset>
                <legend>Modal</legend>
                
                <p class="text-justify">Abaixo seguem exemplos de como usar o Modal no Bootstrap e alguns alias usados no sistema. 
                    Para usá-los basta fazer os includes dos aquivos: <code>resources/js/main.js</code>, 
                    <code>resources/js/bootstrap-dialog.min.js</code> e <code>resources/css/bootstrap-dialog.min.css</code>.</p>
                <p>Maiores detalhes <a href="http://nakupanda.github.io/bootstrap3-dialog/" target="_blank">clique aqui</a>.</p>
                
                <h4>Alert</h4>
                
                <p>Funciona como o alert do javascript, mas com layout do Bootstrap.</p>
                
                <pre>
bootAlert(message, title, callback, type);
                </pre>
                
                <p><button type="button" id="btn-alert" class="btn btn-primary">Modal Exemplo</button></p>
                
                <ul>
                    <li><strong>message: </strong> Mensagem exibida.</li>
                    <li><strong>title: </strong> Título do Modal.</li>
                    <li><strong>callback: </strong> callback Opcional.</li>
                    <li><strong>type: </strong> Determina cor do Modal. Vide cores dos botões. Default: primary.</li>
                </ul>
                
                <h4>Confirm</h4>
                
                <p>Funciona como o confirm do javascript, mas com layout do Bootstrap.</p>
                
                <pre>
bootConfirm(message, title, callback, type);
                </pre>
                
                <p><button type="button" id="btn-confirm" class="btn btn-primary">Modal Exemplo</button></p>
                
                <ul>
                    <li><strong>message: </strong> Mensagem exibida.</li>
                    <li><strong>title: </strong> Título do Modal.</li>
                    <li><strong>callback: </strong> callback Opcional.</li>
                    <li><strong>type: </strong> Determina cor do Modal. Vide cores dos botões. Default: primary.</li>
                </ul>
                
                <h4>Modal Comum</h4>
                
                <p>Opção costumizável.</p>
                
                <pre>
bootDialog(message, title, buttons, type);
                </pre>
                
                <p><button type="button" id="btn-msg" class="btn btn-primary">Modal Exemplo</button></p>
                
                <ul>
                    <li><strong>message: </strong> Mensagem exibida.</li>
                    <li><strong>title: </strong> Título do Modal.</li>
                    <li>
                        <p><strong>buttons: </strong> Botões do modal. Ex:</p>
                        <pre>
bootDialog(
    'mensagem do modal', 
    'Titulo', 
    [{
        label: 'Botão 1',
        action: function (dialog) {
            typeof dialog.getData('callback') === 'function' && dialog.getData('callback')(false);
            dialog.close();
        }
    }, {
        label: 'Botão 2',
        cssClass: 'btn-danger' ,
        action: function (dialog) {
            typeof dialog.getData('callback') === 'function' && dialog.getData('callback')(true);
            dialog.close();
        }
    }],
    'danger'
);
                        </pre>
                    </li>
                    <li><strong>type: </strong> Determina cor do Modal. Vide cores dos botões. Default: primary.</li>
                </ul>
                
            </fieldset>

            <?php include_once 'template/footer.php'; ?>

        </div>

        <script src="js/jquery-1.10.2.min.js"></script>
        <script src="js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="resources/js/bootstrap.min.js"></script>
        <script src="resources/js/bootstrap-dialog.min.js"></script>
        <script src="resources/js/tooltip.js"></script>
        <script src="resources/js/main.js"></script>
        <script src="js/global.js"></script>
        <script src="resources/js/financeiro/detalhado.js"></script>
        <script src="js/jquery.validationEngine-2.6.js" type="text/javascript"></script>
        <script src="js/jquery.validationEngine-pt_BR-2.6.js" type="text/javascript"></script>
        <script>
            $(document).ready(function () {
                $("#btn-alert").click(function () {
                    bootAlert("Mensagem aqui", "Titulo aqui", null, "danger");
                });
                $("#btn-confirm").click(function () {
                    bootConfirm("Mensagem aqui", "Titulo aqui", null, "success");
                });
                $("#btn-msg").click(function () {
                    bootDialog(
                            "Mensagem aqui",
                            "Titulo aqui",
                            [{
                                    label: 'Botão 1',
                                    action: function (dialog) {
                                        typeof dialog.getData('callback') === 'function' && dialog.getData('callback')(false);
                                        dialog.close();
                                    }
                                }, {
                                    label: 'Botão 2',
                                    cssClass: 'btn-warning' ,
                                    action: function (dialog) {
                                        typeof dialog.getData('callback') === 'function' && dialog.getData('callback')(true);
                                        dialog.close();
                                    }
                                }],
                            "warning"
                            );
                });

            });
        </script>
    </body>
</html>