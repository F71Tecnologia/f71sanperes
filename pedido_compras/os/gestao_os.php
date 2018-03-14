<?php
if(empty($_COOKIE['logado'])){
    print "Efetue o Login<br><a href='../../login.php'>Logar</a> ";
    exit;
}

include("../../conn.php");
include("../../wfunction.php");
include('../../classes/ComprasOs.php');

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"35", "area"=>"Compras e Contratos", "id_form"=>"form1", "ativo"=>"Gestão de Compras da OS");
$regiao = $usuario['id_regiao'];
$id_user = $usuario['id_funcionario'];

$objComprasOs = new ComprasOs();
$objComprasOs->setRegiao($regiao);

$result_1 = $objComprasOs->listSolicitacoesByTipo(1);
$total1 = mysql_num_rows($result_1);

$result_2 = $objComprasOs->listSolicitacoesByTipo(2);
$total2 = mysql_num_rows($result_2);

$result_3 = $objComprasOs->listSolicitacoesByTipo(3);
$total3 = mysql_num_rows($result_3);

$result_4 = $objComprasOs->listSolicitacoesByTipo(4);
$total4 = mysql_num_rows($result_4);

$result_5 = $objComprasOs->listSolicitacoesByTipo(5);
$total5 = mysql_num_rows($result_5);


/*echo "<pre>";
print_r($sql1);
print_r($sql2);
print_r($sql3);
print_r($sql4);
echo "<pre>";*/
$arrTipo = array('1'=>"PRODUTO", '2'=>"SERVIÇO");

?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <title>:: Intranet :: Gestão de Compas da OS</title>
        
        <link href="../../favicon.png" rel="shortcut icon" />
        
        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
    </head>

    <body>
        <?php include("../../template/navbar_default.php"); ?>
        
        <div class="container">
            <div class="page-header box-compras-header"><h2><span class="glyphicon glyphicon-shopping-cart"></span> - COMPRAS E CONTRATOS <small> - Gestão de Compras da OS</small></h2></div>
            
            <!-- PARTE 1-->
            <div class="panel panel-default">
                <div class="panel-heading text-bold hidden-print"><!-- 1 --><i class="fa fa-paper-plane-o"></i> Pedidos aguardando aceitação</div>
                <div class="panel-body">
                    <?php if($total1 > 0){ ?>
                    <table class="table table-striped table-hover table-condensed table-bordered">
                        <thead>
                            <tr>
                                <th>N. REQUISIÇÃO</th>
                                <th>DATA</th>
                                <th>TIPO</th>
                                <th>NOME</th>
                                <th>SOLICITADO POR</th>
                                <th>VALOR</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php while ($row_1 = mysql_fetch_array($result_1)) { ?>
                            <tr>
                                <td><?php echo $row_1['num_processo']?></td>
                                <td><?php echo $row_1['data_requisicaoBR'] ?></td>
                                <td><?php echo $arrTipo[$row_1['tipo']] ?></td>
                                <td><?php echo $row_1['nome_produto'] ?></td>
                                <td><?php echo $row_1['nome1'] ?></td>
                                <td>R$ <?php echo $row_1['valor_medio'] ?></td>
                                <td class="text-center"> 
                                    <a href="javascript:;" class="btn btn-xs btn-warning btview" data-key="<?php echo $row_1[0]?>"  data-tipo='1'> <i class="fa fa-search"></i> </a> 
                                    <!--a href="<?php echo "pedidos.php?id=1&compra={$row_1[0]}&regiao={$regiao}"; ?>" class="btn btn-xs btn-warning"> <i class="fa fa-search"></i> </a--> 
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                    <?php }else{
                        echo '<div class="alert alert-dismissable alert-success">
                                <strong>Boa!</strong> Nenhuma solicitação aguardando aceitação.
                             </div>';
                    } ?>
                </div>
            </div>
            
            <!-- PARTE 2-->
            <div class="panel panel-default">
                <div class="panel-heading text-bold hidden-print"><!-- 2 --><i class="fa fa-search-plus"></i> Processos aguardando pesquisa</div>
                <div class="panel-body">
                    <?php if($total2 > 0){ ?>
		    <table class="table table-striped table-hover table-condensed table-bordered">
                        <thead>
                            <tr>
                                <th>N. PROCESSO</th>
                                <th>DATA</th>
                                <th>TIPO</th>
                                <th>NOME</th>
                                <th>SOLICITADO POR</th>
                                <th>VALOR</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php while($row_2 = mysql_fetch_array($result_2)){ ?>								
                            <tr>
                                <td><?php echo $row_2['num_processo']?></td>
                                <td><?php echo $row_2['data_requisicao'] ?></td>
                                <td><?php echo $arrTipo[$row_2['tipo']] ?></td>
                                <td><?php echo $row_2['nome_produto'] ?></td>
                                <td><?php echo $row_2['nome1'] ?></td>
                                <td>R$ <?php echo $row_2['valor_medio'] ?></td>
                                <td class="text-center"><a href="<?php echo "cotacoes.php?id=1&compra=$row_2[0]&regiao=$regiao"; ?>" class="btn btn-xs btn-warning"> <i class="fa fa-search"></i> </a> </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                    <?php }else{
                        echo '<div class="alert alert-dismissable alert-success">
                                <strong>Boa!</strong> Nenhuma processo aguardando pesquisa.
                             </div>';
                    } ?>
                </div>
            </div>
            
            <!-- PARTE 3-->
            <div class="panel panel-default">
                <div class="panel-heading text-bold hidden-print"><!-- 3 --><i class="fa fa-suitcase"></i> Decisões a serem tomadas</div>
                <div class="panel-body">
                    <?php if($total3 > 0){ ?>
		    <table class="table table-striped table-hover table-condensed table-bordered">
                        <thead>
                            <tr>
                                <th>N. PROCESSO</th>
                                <th>DATA</th>
                                <th>TIPO</th>
                                <th>NOME</th>
                                <th>SOLICITADO POR</th>
                                <th>VALOR</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php while($row_3 = mysql_fetch_array($result_3)){ ?>								
                            <tr>
                                <td><?php echo $row_3['num_processo']?></td>
                                <td><?php echo $row_3['data_requisicao'] ?></td>
                                <td><?php echo $arrTipo[$row_3['tipo']] ?></td>
                                <td><?php echo $row_3['nome_produto'] ?></td>
                                <td><?php echo $row_3['nome1'] ?></td>
                                <td>R$ <?php echo $row_3['valor_medio'] ?></td>
                                <td class="text-center">
                                    <a href="javascript:;" class="btn btn-xs btn-success btview" data-key='<?php echo $row_3[0];?>' data-tipo='2'> <i class="fa fa-paperclip"></i> </a> 
                                    <!--a href="compras2-old/visualiza_anexo.php?compra=<?php echo $row_3['id_compra'];?>" class="btn btn-xs btn-success"> <i class="fa fa-paperclip"></i> </a--> 
                                    <a href="<?php echo "cotacoes.php?id=1&compra=$row_3[0]&regiao=$regiao"; ?>" class="btn btn-xs btn-warning"> <i class="fa fa-search"></i> </a> 
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                    <?php }else{
                        echo '<div class="alert alert-dismissable alert-success">
                                <strong>Boa!</strong> Nenhuma decisão a ser tomada.
                             </div>';
                    } ?>
                </div>
            </div>
            
            <!-- PARTE 4-->
            <div class="panel panel-default">
                <div class="panel-heading text-bold hidden-print"><!-- 4 --><i class="fa fa-gavel"></i> Decisões</div>
                <div class="panel-body">
                    <?php if($total4 > 0){ ?>
		    <table class="table table-striped table-hover table-condensed table-bordered">
                        <thead>
                            <tr>
                                <th>N. PROCESSO</th>
                                <th>NECESSÁRIO PARA</th>
                                <th>DATA</th>
                                <th>TIPO</th>
                                <th>NOME</th>
                                <th>DECIDIDO POR</th>
                                <th>VALOR</th>
                                <th>FORNECEDOR <br/> DATA ENTREGA </th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php while($row_4 = mysql_fetch_array($result_4)){ ?>								
                            <tr>
                                <td><?php echo $row_4['num_processo'];?></td>
                                <td><?php echo $row_4['necessidade']; ?></td>
                                <td><?php echo $row_4['data_requisicao'] ?></td>
                                <td><?php echo $arrTipo[$row_4['tipo']] ?></td>
                                <td><?php echo $row_4['nome_produto'] ?></td>
                                <td><?php echo $row_4['nome_escolha']; ?></td>
                                <td><?php echo 'R$ '.$row_4['preco_final']; ?></td>
                                <td><?php echo $row_4['fornecedor'].'<br>'.$row_4['prazo']; ?></td>
                                <td class="text-center">
                                    <a href='javascript:;' class="btn btn-xs btn-warning btview" data-key="<?php echo $row_4[0] ?>" data-tipo='1'> <i class="fa fa-search"></i> </a> 
                                    <!--a href='compras2-old/confirmandocompra.php?compra=<?php echo $row_4[0];?>&regiao=<?php echo $regiao; ?>' class="btn btn-xs btn-warning"> <i class="fa fa-search"></i> </a--> 
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                    <?php }else{
                        echo '<div class="alert alert-dismissable alert-warning">
                                <strong>Opa!</strong> Nenhuma decisão tomada.
                             </div>';
                    } ?>
                </div>
            </div>
            
            <!-- PARTE 5-->
            <?php if($total5 > 0){ ?>
            <!--div class="panel panel-default">
                <div class="panel-heading text-bold hidden-print"><i class="fa fa-shopping-cart"> </i> Acompanhamento Geral</div>
                <div class="panel-body">
		    <table class="table table-striped table-hover table-condensed table-bordered">
                        <thead>
                            <tr>
                                <td>N. PROCESSO</td>
                                <td>NECESS&Aacute;RIO PARA</td>
                                <td>DATA</td>
                                <td>NOME</td>
                                <td>PEDIDO POR</td>
                                <td>VALOR</td>
                                <td>STATUS</td>
                                <td></td>
                                <td></td>
                            </tr>
                        </thead>
                        <tbody>
                        <?php while($row_5 = mysql_fetch_array($result_5)){ ?>								
                            <tr>
                                <td><?php echo $row_5['num_processo'];?></td>
                                <td><?php echo $row_5['necessidade']; ?></td>
                                <td><?php echo $row_5['data_requisicao'] ?></td>
                                <td><?php echo $arrTipo[$row_5['tipo']] ?></td>
                                <td><?php echo $row_5['nome_produto'] ?></td>
                                <td><?php echo $row_5['nome_escolha']; ?></td>
                                <td><?php echo 'R$ '.$row_5['preco_final']; ?></td>
                                <td><?php echo $row_5['fornecedor'].'<br>'.$row_5['prazo']; ?></td>
                                <td class="text-center">
                                    <a href='compras2-old/confirmandocompra.php?compra=<?php echo $row_5[0];?>&regiao=<?php echo $regiao; ?>' class="btn btn-xs btn-warning"> <i class="fa fa-search"></i> </a> 
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div-->
            <?php } ?>
        <?php include('../../template/footer.php'); ?>
    </div>
        
    <script src="../../js/jquery-1.10.2.min.js"></script>
    <script src="../../resources/js/bootstrap.min.js"></script>
    <script src="../../resources/js/bootstrap-dialog.min.js"></script>
    <script src="../../js/jquery.validationEngine-2.6.js"></script>
    <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
    <script src="../../resources/js/main.js"></script>
    <script src="../../js/global.js"></script>
    
    <script>
        $(function() {                
            /*$("#form1").validationEngine();
            $(".modal-lg").css('width','80%');

            //datepicker
            var options = new Array();
            options['language'] = 'pt-BR';
            $('.datepicker').datepicker(options);*/

            $(".btview").on("click", function() {
                var id = $(this).data("key");
                var tipo = $(this).data("tipo");
                var url = '';
                if(tipo === 1){
                    url = 'popup.detalhe.php';
                }else{
                    url = 'visualiza_anexo.php';
                }
                
                $.post(url,{id:id, method:"detalhe"},function(data){
                    //bootDialog(data,'Detalhes do '+texto);
                    $(".modal-lg").css('width','80%');

                    new BootstrapDialog({
                        nl2br: false,
                        size: BootstrapDialog.SIZE_WIDE,
                        title: 'Detalhes da Solicitação',
                        message: data,
                        closable: false,
                        type: 'type-primary',
                        buttons: [
                            {
                                label: 'Fechar',
                                cssClass: 'btn-default',
                                action: function (dialog) {
                                    dialog.close();
                                }
                            }]
                    }).open();


                });
            });
        });
    </script>
    </body>
</html>