<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../login.php?entre=true';</script>";
    exit;
}

include "../../conn.php";
include "../../classes/funcionario.php";
include '../../classes_permissoes/regioes.class.php';
include "../../wfunction.php";
include "../../classes_permissoes/acoes.class.php";
include_once('../../classes/ArquivoTxtBancoClass.php');
$ArquivoTxtBancoClass = new ArquivoTxtBancoClass();

$usuario = carregaUsuario();
$optRegiao = getRegioes();
$ACOES = new Acoes();

if(isset($_GET['delArq']) AND !empty($_GET['delArq'])){
    $ArquivoTxtBancoClass->deletarRegistro($_GET['delArq']);
    header("Location: arquivo_banco_rescisao.php");exit;
}else if(isset($_GET['arq'])){
    $download = $ArquivoTxtBancoClass->downloadArquivo($_GET['arq']);
}

$arrayArquivos = $ArquivoTxtBancoClass->getRegistrosRescisao($usuario['id_regiao']);
//echo "<pre>"; print_r($arrayArquivos); echo '</pre>';
?>
<html>
    <head>
        <title>:: Intranet ::</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../../favicon.ico" />
        <link href="../../net1.css" rel="stylesheet" type="text/css" />
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" type="text/css" />
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../../favicon.ico" rel="shortcut icon" />
        <script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../../js/global.js" type="text/javascript"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <style>
            .hid { display: none; }
            .arquivo { cursor: pointer; } 
            .table-header thead tr th{
                background-image:url('data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4gPHN2ZyB2ZXJzaW9uPSIxLjEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGRlZnM+PGxpbmVhckdyYWRpZW50IGlkPSJncmFkIiBncmFkaWVudFVuaXRzPSJ1c2VyU3BhY2VPblVzZSIgeDE9IjUwJSIgeTE9IjAlIiB4Mj0iNTAlIiB5Mj0iMTAwJSI+PHN0b3Agb2Zmc2V0PSIwJSIgc3RvcC1jb2xvcj0iI2Y4ZjhmOCIvPjxzdG9wIG9mZnNldD0iMTAwJSIgc3RvcC1jb2xvcj0iI2U4ZThlOCIvPjwvbGluZWFyR3JhZGllbnQ+PC9kZWZzPjxyZWN0IHg9IjAiIHk9IjAiIHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiIGZpbGw9InVybCgjZ3JhZCkiIC8+PC9zdmc+IA==');background-size:100%;background-image:-webkit-gradient(linear, 50% 0%, 50% 100%, color-stop(0%, #f8f8f8), color-stop(100%, #e8e8e8));background-image:-webkit-linear-gradient(top, #f8f8f8,#e8e8e8);background-image:-moz-linear-gradient(top, #f8f8f8,#e8e8e8);background-image:-o-linear-gradient(top, #f8f8f8,#e8e8e8);background-image:linear-gradient(top, #f8f8f8,#e8e8e8);-webkit-box-shadow:0 1px #fff inset;-moz-box-shadow:0 1px #fff inset;box-shadow:0 1px #fff inset;text-shadow:0 1px #fff
            }
            .table-bordered{
                *border:1px solid #ddd;*border-collapse:separate;*border-collapse:collapsed;*border-left:0;*-webkit-border-radius:4px;*-moz-border-radius:4px;*border-radius:4px
            }
            .imgDown { width: 30px; height: 30px; }
            .table > thead > tr > th { vertical-align: middle; }
            legend { width: auto; }
        </style>
        <script>
            $(function(){
                $('.arquivo').click(function(){
                    var id = $(this).data('id');
                    $('.'+id).toggle();
                });
                
                $('.link').click(function(e){
                    e.preventDefault();
                    var targetUrl = $(this).attr("href");
                    thickBoxConfirm('Excluir','Deseja realmente excluir o arquivo?','auto','auto',function(data){
                        if(data == true){
                            window.location.href = targetUrl;
                        }
                    });
                });
                <?php if(!$download AND isset($_GET['arq'])){ ?>
                    thickBoxAlert('Erro','Arquivo não encontrado!','auto','auto',null);
                <?php } ?>
            });
        </script>
    </head>
    <body class="novaintra" >     
        <div id="content">
            <form  name="form" action="" id="form1" method="post" id="form">
                <div id="head">
                    <img src="../../imagens/logomaster<?php echo $usuario['id_master']; ?>.gif" class="fleft" style="margin-right: 25px;"/>
                    <div class="fleft">
                        <h2>Arquivos de Banco para Rescisão</h2>
                    </div>
                </div>
                <br class="clear">
                <br/>
                <!--<fieldset class="noprint">
                    <div>
                        <p><label class="first">Região:</label> <?php echo montaSelect($optRegiao, $id_regiao, array('name' => "regiao", 'id' => 'regiao')); ?> </p>                        
                    </div>
                    <div>
                        <fieldset class="noprint">
                            <legend>Periodo</legend>
                        <p>
                            <label class="first">Inicio: </label> 
                            <?php echo montaSelect($meses, $mesRI, "id='mes_inicio' name='mes_inicio' class='validate[custom[select]]'") ?>
                            <?php echo montaSelect($anos, $anoRI, "id='ano_inicio' name='ano_inicio' class='validate[custom[select]]'") ?>
                        </p>
                        <p>
                            <label class="first">Fim: </label> 
                            <?php echo montaSelect($meses, $mesRF, "id='mes_fim' name='mes_fim' class='validate[custom[select]]'") ?>
                            <?php echo montaSelect($anos, $anoRF, "id='ano_fim' name='ano_fim' class='validate[custom[select]]'") ?>
                        </p>
                        </fieldset>
                    </div>
                    <br class="clear"/>
                    <p class="controls" style="margin-top: 10px;">
                        <input type="submit" name="gerar" value="Gerar" id="gerar"/>
                    </p>
                </fieldset>
            </form>-->

            <?php //if(isset($_REQUEST['mes_inicio'])){
                $count = 0;
                if(is_array($arrayArquivos)){
                    foreach ($arrayArquivos as $key => $value) {
                        if($value['tipo_conta'] == 'c'){$tipoConta = 'CORRENTE';}
                        if($value['tipo_conta'] == 's'){$tipoConta = 'SALARIO';}
                        if($auxNomeArquivo != $value['nome_arquivo']){
                            $count++;
                            $nomeArquivo = explode('/', $value['nome_arquivo']);
                            $linha .= "
                            <thead>
                                <tr>
                                    <th>{$nomeArquivo[2]}</th>
                                    <th>Pago em: {$value['data']}</th>
                                    <th><a class='arquivo' data-id='{$count}' href='javascript:void(0);'><img class='imgDown' src='../../img_menu_principal/rel_gerencial.png'> Ver Participantes</a></th>
                                    <th><a href='?arq=".md5($key)."'><img class='imgDown' src='../../img_menu_principal/relatorios_gestao.png'> Baixar</a></th>
                                    <th><a class='link' href='?delArq=".md5($value['nome_arquivo'])."'><img class='imgDown' src='../../img_menu_principal/delete.png'> Deletar</a></th>
                                </tr>
                                <tr class='hid {$count}'>
                                    <th colspan='2'>NOME</th>
                                    <th>BANCO</th>
                                    <th>VALOR</th>
                                    <th>TIPO CONTA</th>
                                </tr>
                            </thead>";
                            $auxNomeArquivo = $value['nome_arquivo'];
                        }
                        $linha .= "
                        <tr class='hid {$count}'>
                            <td colspan='2'>{$value['nome']}</td>
                            <td>{$value['razao']}</td>
                            <td>".number_format($value['total_liquido'],2,',','.')."</td>
                            <td>{$tipoConta}</td>
                        </tr>";
                    }
                }else{
                    $linha = "
                    <tr>
                        <td>Nenhum Arquivo Encontrado!</td>
                    </tr>";
                }
                ?>
                <!--<table class="table table-bordered table-striped table-header table-action" style="width: 95%; margin: 5% 2.5% 0% 2.5%;">-->
                <table class="table table-bordered table-striped table-header table-action" style="margin: 1% 0% 0% 0%;">
                    <?php echo $linha; ?>
                </table>
            <?php //} ?>
        </div>
    </body>
</html>