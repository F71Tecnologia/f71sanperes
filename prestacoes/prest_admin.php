<?php
if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="login.php">Logar</a>';
    exit;
}

include('../conn.php');
include('../funcoes.php');
include('../wfunction.php');
include('PrestacaoContas.class.php');

$usuarioW = carregaUsuario();

$qrMaster = "SELECT nome,cod_os FROM master WHERE id_master = {$usuarioW['id_master']}";
$reMaster = mysql_query($qrMaster);
$roMaster = mysql_fetch_assoc($reMaster);

$meses = mesesArray(null);
$anos = anosArray(null, null);
$mesR = (isset($_REQUEST['mes'])) ? $_REQUEST['mes'] : date('m') - 1;
$anoR = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : date('Y');


?>
<html>
    <head>
        <title>:: Intranet :: PRESTAÇÃO DE CONTAS - ADMINISTRAÇÃO</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../favicon.ico" />
        <link href="../net1.css" rel="stylesheet" type="text/css" />
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../favicon.ico" rel="shortcut icon" />
        <script src="../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine.js" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine-pt.js" type="text/javascript"></script>
        <script src="../js/jquery.maskMOney_2.1.2.js" type="text/javascript"></script>
        <script src="../js/jquery.maskedinput.js" type="text/javascript"></script>

        <script src="../js/global.js" type="text/javascript"></script>

        <script>
            $(function() {
                $(".bt-menu").click(function(){
                    var $bt = $(this);
                    var id = $bt.attr("data-item");                   
                    $(".itens").hide();
                    $("#item"+id).show();
                    $(".bt-menu").removeClass("aselected");
                    $bt.addClass("aselected");
                });
            });
        </script>
        <style>
            #showResumo{
                border: 1px solid #E2E2E2;
                width: 80%;
                margin: 0 auto;
                margin-top: 22px;
                margin-bottom: 22px;
                background: #F7F7F7;
                min-height: 300px;
            }
            
            #geral{ position: relative; width: auto; overflow: hidden; border: 1px solid #ccc; padding: 10px; margin-bottom: 100px;}
            #topo { height: 105px; width: 100%;border-bottom: 1px solid #ccc;}
            #topo .conteudoTopo{ margin: 10px; border:0px solid #ccc; width: auto; height: auto;}
            #conteudo{ width: 100%; padding-top: 10px; height: auto; border: 0px solid #ccc;}
            .colEsq{ width: 200px; min-height: 400px; border-right: 1px solid #ccc; margin-right: 10px; float: left;} 
            .colDir{ width: auto; min-width: 800px; margin-left: 210px; min-height: 400px; border: 1px solid #ccc; padding: 10px;}
            .colEsq ul{ margin: 0px; padding: 0px; }
            .colEsq ul li{ list-style: none; border-bottom: 1px solid #CCC; }
            .colEsq ul li a{ text-decoration: none; color: #333; font-family: arial; font-size: 12px; line-height: 25px; display: block; padding: 3px; padding-left: 8px;}
            .colEsq ul li a:hover {background: #19609F;color: #fff;}
            .colEsq ul li a.aselected {background: #19609F;color: #fff;}
            .titleEsq{height: 20px; text-align: center; padding: 10px; background: #DFDFDF;}
        </style>

    </head>
    <body id="page-despesas" class="novaintra">
        <div id="content">
            
            <div id="geral">
                <div id="topo">
                    <div class="conteudoTopo">
                        <div class="imgTopo">
                            <img src="../imagens/logomaster<?php echo $usuarioW['id_master']; ?>.gif" class="fleft" style="margin-right: 25px;"/>
                        </div>
                        <h2><?php echo $roMaster['nome'] ?></h2>
                        <h3>ADMINISTRAÇÃO DE PRESTAÇÕES DE CONTAS</h3>
                    </div> 
                </div>
                <div id="conteudo">
                    <div class="colEsq">
                        <div class="titleEsq">Ações</div>
                        <ul>
                            <li><a href="javascript:;" data-item="1" class="bt-menu aselected">Resumo de Prestações</a></li>
                            <li><a href="javascript:;" data-item="2" class="bt-menu">Desprocessar</a></li>
                            <li><a href="javascript:;" data-item="3" class="bt-menu">Desabilitar Empresa</a></li>
                            <li><a href="javascript:;" data-item="4" class="bt-menu">Recursos Humanos</a></li>
                        </ul>
                    </div>
                    <div class="colDir">
                        <div id="item1" class="itens">
                            <h3>Resumo de Prestações</h3>
                            <p><label class="first" style="vertical-align: middle!important;">Mês:</label> 
                                <?php echo montaSelect($meses, $mesR, "id='mes' name='mes'") ?>  
                                <?php echo montaSelect($anos, $anoR, "id='ano' name='ano'") ?>
                                <input type="button" name="filtroResumo" id="filtroResumo" value="Gerar Resumo" />
                            </p>
                            <div id="showResumo">
                                
                            </div>
                        </div>
                        
                        <div id="item2" class="itens">
                            <h3>Desprocessar Prestações Finalizadas</h3>
                            <p><label>Tipo:</label> <?php echo montaSelect(PrestacaoContas::getTiposPrestacoes(), null, "id='desprocessatipo' name='desprocessatipo'") ?>  </p>
                            <p>
                                <label class="first" style="vertical-align: middle!important;">Mês:</label> 
                                
                                <?php echo montaSelect($meses, date('m'), "id='desprocessames' name='desprocessames'") ?>  
                                <?php echo montaSelect($anos, date('Y'), "id='desprocessaano' name='desprocessaano'") ?>
                                <input type="button" name="continuar" id="continuar" value="Próximo Passo" />
                            </p>
                        </div>
                        
                        <div id="item3" class="itens">
                            <h3>Desabilitar Empresa que não tem Contrato</h3>
                        </div>
                        
                        <div id="item4" class="itens">
                            <h3>Recursos Humanos</h3>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>