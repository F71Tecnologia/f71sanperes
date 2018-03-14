<?php session_start(); ?>
<!doctype html>
<html lang="pt-br">
    <head>
        <title>Webmail</title>
        <meta charset='iso-8859-1'>
        <!-- <link type="text/css" rel="stylesheet" href="assets/css/jquery-ui.css" /> -->
        <link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css" />
        <link type="text/css" rel="stylesheet" href="assets/js/jquery-tagit/css/tagit-simple-blue.css" />
        <link type="text/css" rel="stylesheet" href="assets/css/global.css" />
        <link type="text/css" rel="stylesheet" href="assets/css/jquery.treeview.css" />
	<link type="text/css" rel="stylesheet" href="assets/css/smartpaginator.css" />

        <script type="text/javascript" src="//ajax.aspnetcdn.com/ajax/jQuery/jquery-2.0.3.min.js"></script>
        <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
        <!-- <script type="text/javascript" src="assets/js/jquery-ui.js"></script> -->
        <script type="text/javascript" src="assets/js/jquery.form.min.js"></script>
        <script type="text/javascript" src="assets/js/jquery-tagit/js/tagit.min.js"></script>
        <script type="text/javascript" src="assets/js/balloon.js"></script>
        <!--<script type="text/javascript" src="inc/ckeditor/ckeditor-min.js"></script>-->
        <script type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.0.1/ckeditor.js"></script>
        <script type="text/javascript" src="assets/js/jquery.treeview/jquery.treeview.min.js"></script>
        <script type="text/javascript" src="assets/js/utils.js"></script>
        <script type="text/javascript" src="assets/js/global.js"></script>
	<script type="text/javascript" src="assets/js/smartpaginator.js"></script>
        <!--<script type="text/javascript" src="assets/js/global.min.js"></script> -->
    </head>
    <body>
        <input type="hidden" id="self_mail" value="<?php echo $_SESSION['email'] ?>" />
        <div class="container">
            <nav class="navbar navbar-menu-top">
                <a href="?box=Inbox&boxfull=INBOX" class="home-button">Caixa de Entrada</a>
                <ul class="top-menu principal active">
                    <li><a href="javascript:;" class="opt-new">Novo</a></li>
                    <li><a href="javascript:;" class="opt-spam">Spam</a></li>
                    <li><a href="javascript:;" class="opt-delete">Excluir</a></li>
                    <li><a href="javascript:;" class="opt-move">Mover</a></li>
                    <li><a href="javascript:;" class="opt-mark-unread">Marcar como não-lido</a></li>
					<li><a href="javascript:;" class="opt-mark-read">Marcar como lido</a></li>
                    <li class="right" style="margin-right: 10px; padding: 0px;">
                        <a href="?box=Inbox&boxfull=INBOX" target="_blank"><img src="assets/img/undock.png" title="Abrir em nova janela"/></a>
                    </li>
                    <li class="right" style="margin-right: 10px; padding: 0px;">
                        <a href="javascript:;" class="config-button">
                            <span>Preferencias</span>
                            <img src="assets/img/config-button.png">
                        </a>
                    </li>
                </ul>

                <ul class="top-menu email" style="display: none;">
                    <li><a href="javascript:;" class="opt-response">Responder</a></li>
                    <li><a href="javascript:;" class="opt-response-all">Responder a todos</a></li>
		            <li><a href="javascript:;" class="opt-forward">Encaminhar</a></li>
                    <li><a href="javascript:;" class="opt-print">Imprimir</a></li>
                    <li><a href="javascript:;" class="opt-spam">Spam</a></li>
                    <li><a href="javascript:;" class="opt-delete">Excluir</a></li>
                    <li><a href="javascript:;" class="opt-move">Mover</a></li>
                    <li class="right" style="margin-right: 10px; padding: 0px;">
                        <a href="?box=Inbox&boxfull=INBOX" target="_blank"><img src="assets/img/undock.png" title="Abrir em nova janela"/></a>
                    </li>
                    <li class="right" style="margin-right: 10px; padding: 0px;">
                        <a href="javascript:;" class="config-button">
                            <span>Preferencias</span>
                            <img src="assets/img/config-button.png">
                        </a>
                    </li>
                </ul>

                <ul class="top-menu drafts" style="display: none;">
                    <li><a href="javascript:;" class="opt-delete">Excluir rascunho</a></li>
                </ul>

                <ul class="top-menu new" style="display: none;">
                    <li><a href="javascript:;" class="opt-save-draft">Salvar rascunho</a></li>
                </ul>

                <ul class="top-menu trash" style="display: none;">
                    <li><a href="javascript:;" class="opt-restore">Restaurar</a></li>
                    <li><a href="javascript:;" class="opt-delete">Excluir permanentemente</a></li>
                    <li><a href="javascript:;" class="opt-empty">Esvaziar lixeira</a></li>
                    <li><a href="javascript:;" class="opt-move">Mover</a></li>
                </ul>

                <ul class="top-menu spam" style="display: none;">
                    <li><a href="javascript:;" class="opt-restore">Restaurar</a></li>
                    <li><a href="javascript:;" class="opt-delete">Excluir permanentemente</a></li>
                    <li><a href="javascript:;" class="opt-empty">Esvaziar caixa de spam</a></li>
                    <li><a href="javascript:;" class="opt-move">Mover</a></li>
                </ul>

            </nav>
            <div style="clear: both;"></div>
            <nav class="sidebar">
                <nav class="pastas">
                    <h2 style="float: left;" class="opt-search"><img src="assets/img/search.png">Buscar Emails</h2>
                    <div style="clear: both;"></div>
                    <h2 style="float: left;" class="folder_option" title="Gerenciar Pastas"><img src="assets/img/folder_edit.png">Pastas</h2>
                    <div style="clear: both;"></div>
                    <nav class="sub-sidebar sidebar-pastas">
                        <ul id="lista-pastas"></ul>
                    </nav>
                </nav>

                <nav class="contatos">
                    <h2 style="float: left;">Contatos</h2>
                    <div style="margin: 23px 0 0 63px; float: left;">
                        <a href="#" class="add_contact" title="Adicionar contato"><img src="assets/img/btn-add.png" width="15" height="15" /></a>
                    </div>
                    <div style="clear: both;"></div>
                    <input type="text" class="search contact_search" id="contact_search" placeholder="Procurar contato [enter]"/>
                    <nav class="sub-sidebar sidebar-contatos">

                    </nav>
                </nav>
            </nav>
            <div class="stage">
                <ul class="mail_list"></ul>
                <div style="clear: both;"></div>
                <div class="pagination"></div>
            </div>
        </div>
        <footer>
            <a href="index.php">Fmail</a>
        </footer>

        <!-- Modal -->
        <div id="modal-background"></div>
        <div id="modal-content">
            <!-- content of modal dialog -->
        </div>
    </body>
</html>
