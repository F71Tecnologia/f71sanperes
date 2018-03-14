<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: LIB GIT</title>

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
            <div class="row">
                <div class="page-header box-repositorio-header"><h3><span class="fa fa-git"></span> - Manual Git F71</h3></div>
                <h3><span class="label label-primary"><span class="fa fa-caret-right"></span> Conta Bitbucket</span></h3>
                <div class="panel panel-primary">
<!--                    <div class="panel-heading text-bold hidden-print">Conta Bitbucket</div>-->
                        <div class="panel-body">
                        <div class="col-sm-6">
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <label class="form-control">Criar conta</label>
                                    <div class="input-group-btn">
                                        <a href="https://bitbucket.org/account/signup/" class="btn btn-warning"><span class="fa fa-bitbucket"></span> Criar</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-sm-6">
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <label class="form-control">Já tenho conta</label>
                                    <div class="input-group-btn">
                                        <a href="https://bitbucket.org/account/signin/" class="btn btn-success"><span class="fa fa-bitbucket"></span> Logar</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <h3><span class="label label-primary"><span class="fa fa-caret-right"></span> Instalação no Windows (CYGWIN)</span></h3>
                <div class="panel panel-primary">
                    <div class="panel-body">
                        <div class="col-sm-6">
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <label class="form-control">Versão 32 Bits</label>
                                    <div class="input-group-btn">
                                        <a href="https://cygwin.com/setup-x86.exe" class="btn btn-success"><span class="fa fa-code-fork"></span> Baixar</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-6">
                            <div class="col-lg-8">
                                <div class="input-group">
                                    <label class="form-control">Versão 64 Bits</label>
                                    <div class="input-group-btn">
                                        <a href="https://cygwin.com/setup-x86.exe" class="btn btn-success"><span class="fa fa-code-fork"></span> Baixar</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                                
                
                <h3><span class="label label-primary"><span class="fa fa-caret-right"></span> Instalação do Plugin no NetBeans</span></h3>
                <div class="panel panel-primary">
                    <!--<div class="panel-heading text-bold hidden-print">Instalação do Plugin no NetBeans</div>-->
                    <div class="panel-body">
                        <p>Para gerenciar o git pelo NetBeans, é necessário a instalação do Plugin Git Toolbar.</p>
                          <pre>
               Ferramentas > Plugin > Plugins Disponíveis > Digite Git Toolbar (No campo pesquisar) > Localize e instale o plugin.
                           </pre>
                    </div>
                </div>
                
                <h3><span class="label label-primary"><span class="fa fa-caret-right"></span> Clonando o repositório</span></h3>
                <div class="panel panel-primary">
                    <div class="panel-body">
                        <div class="col-sm-12 text-center">
                            <p>Vá ao site do <a href="https://bitbucket.org/f71sistemas/">Bitbucket - F71 Sistemas</a>, escolha o repositório e copie a url que aparecerá no canto superior direito da tela</p>
                            <img src="imagens/lib_git/link_repositorio.jpg"  />
                        </div>
                        
                        <div class="col-sm-6">
                            <h4><span class="label label-default">NetBeans Versão 8.1</span></h4>
                            <div class="thumbnail">
                                <img src="imagens/lib_git/clone_git_81.jpg"  />
                            </div>
                        </div>
                        
                        <div class="col-sm-6">
                            <h4><span class="label label-default">NetBeans Versão 8.2 +</span></h4>
                            <div class="thumbnail">   
                                <img src="imagens/lib_git/clone_git_81plus.jpg"  />
                            </div>
                        </div>
                    </div>
                </div>
                
                <h3><span class="label label-primary"><span class="fa fa-caret-right"></span> Adicionando outro repositório</span></h3>
                <div class="panel panel-primary">
                    <div class="panel-body">
                        <div class="col-sm-12 text-center">
                            <p>Após clicar em <mark><em>clonar</em></mark> a janela abaixo abrirá, isso fará com que você possa clonar um novo repositório</p>
                        </div>
                        
                        <div class="col-sm-6">
                            <h4><span class="label label-default">Copie e cole a url</span></h4>
                            <div class="thumbnail">
                                <img src="imagens/lib_git/url_repositorio.jpg" class="img-responsive"/>
                            </div>
                        </div>
                        
                        <div class="col-sm-6">
                            <h4><span class="label label-default">Escolha o destino do seu repositório</span></h4>
                            <div class="thumbnail">
                                <img src="imagens/lib_git/destino_repositorio.jpg" class="img-responsive"/>
                            </div>
                        </div>
                        
                    </div>
                </div>
                
                <h3><span class="label label-primary"><span class="fa fa-caret-right"></span> Branch (Ramificações) Remotas</span></h3>
                <div class="panel panel-primary">
                    <div class="panel-body">
                        <div class="col-sm-12 text-center">
                            <p>Ramificação <mark>Master</mark> e <mark>Testing</mark> criará um mirror (espelho) dos arquivos que estão no repositório Bitbucket.</p>
                        </div>
                        
                        <div class="col-sm-6">
                            <h4><span class="label label-default">Branch (Ramificações)</span></h4>
                            <div class="thumbnail">
                                <img src="imagens/lib_git/branch1.jpg" class="img-responsive"/>
                            </div>
                        </div>
                        
                        <div class="col-sm-6">
                            <h4><span class="label label-default">Diretório de Destino</span></h4>
                            <div class="thumbnail">
                                <img src="imagens/lib_git/branch2.jpg" class="img-responsive"/>
                            </div>
                        </div>
                        
                    </div>
                </div>
                
                <h3><span class="label label-primary"><span class="fa fa-caret-right"></span> Alternar Branch (Ramificações)</span></h3>
                <div class="panel panel-primary">
                    <div class="panel-body">
                        <div class="col-sm-12 text-center">
                            <p>Escolha para qual ramificação deseja alternar.</p>
                        </div>
                        
                        <div class="col-sm-6 col-sm-offset-3">
                            <h4><span class="label label-default">Alternarnando Branch</span></h4>
                            <div class="thumbnail">
                                <img src="imagens/lib_git/alternar_ramificacao.jpg"/>
                            </div>
                        </div>
                        
<!--                        <div class="col-sm-6">
                            <h4><span class="label label-default">Branch (Ramificações)</span></h4>
                            <div class="thumbnail">
                                <img src="imagens/lib_git/alternar_ramificacao.jpg"/>
                            </div>
                        </div>-->
                        
                    </div>
                </div>
                
                <h3><span class="label label-primary"><span class="fa fa-caret-right"></span> Criando novo projeto</span></h3>
                <div class="panel panel-primary">
                    <div class="panel-body">
                        <div class="col-sm-12 text-center">
                            <p>Crie um novo projeto, escolha a opção de projetos existentes, coloque a pasta de fonte, o nome e finalize. </p>
                        </div>
                        
                        <div class="col-sm-6 col-sm-offset-3">
                            <h4><span class="label label-default">Novo Projeto</span></h4>
                            <div class="thumbnail">
                                <img src="imagens/lib_git/novo_projeto.jpg"/>
                            </div>
                        </div>
                        
<!--                        <div class="col-sm-6">
                            <h4><span class="label label-default">Branch (Ramificações)</span></h4>
                            <div class="thumbnail">
                                <img src="imagens/lib_git/alternar_ramificacao.jpg"/>
                            </div>
                        </div>-->
                        
                    </div>
                </div>
           
            </div>
        
        <?php include_once 'template/footer.php'; ?>

        </div>

<!--        <script src="js/jquery-1.10.2.min.js"></script>
        <script src="js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="resources/js/bootstrap.min.js"></script>
        <script src="resources/js/bootstrap-dialog.min.js"></script>
        <script src="resources/js/tooltip.js"></script>
        <script src="resources/js/main.js"></script>
        <script src="js/global.js"></script>
        <script src="resources/js/financeiro/detalhado.js"></script>
        <script src="js/jquery.validationEngine-2.6.js" type="text/javascript"></script>
        <script src="js/jquery.validationEngine-pt_BR-2.6.js" type="text/javascript"></script>-->
        
    </body>
</html>