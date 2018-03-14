<?php
if(empty($_COOKIE['logado'])){
    print "Efetue o Login<br><a href='../../login.php'>Logar</a> ";
    exit;
}

include("../../conn.php");
include("../../wfunction.php");

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$regiao = $usuario['id_regiao'];
$id_user = $usuario['id_funcionario'];

$qr_master = mysql_query("SELECT * FROM master WHERE id_master = '$usuario[id_master]'");
$row_master = mysql_fetch_assoc($qr_master);

$verifica = $usuario['tipo_usuario'];

if($verifica == "1" or $verifica == "2"){		
    $class_3 = "";
}else{
    $class_3 = "style='display:none'";
}

$sql1 = "SELECT A.*,B.nome1,
                            date_format(A.data_requisicao, '%d/%m/%Y')as data_requisicao 
                    FROM    compra AS A 
               LEFT JOIN    funcionario AS B ON (A.id_user_pedido = B.id_funcionario)
                   WHERE    A.status_requisicao = '1' AND A.acompanhamento = '1' AND 
                            A.id_regiao = '$regiao'";
$result_1 = mysql_query($sql1) or die("erro re1: <!-- {$sql1} -->".  mysql_error());
$total1 = mysql_num_rows($result_1);

$sql2 = "SELECT A.*,B.nome1,
                            date_format(A.data_requisicao, '%d/%m/%Y')as data_requisicao 
                    FROM    compra AS A
               LEFT JOIN    funcionario AS B ON (A.id_user_pedido = B.id_funcionario)
                   WHERE    A.status_requisicao = '2' AND A.acompanhamento = '2' 
                     AND    A.id_regiao = '$regiao'";
$result_2 = mysql_query($sql2) or die("erro re2: <!-- {$sql2} -->".  mysql_error());
$total2 = mysql_num_rows($result_2);

$sql3 = "SELECT A.*,B.nome1,
                            date_format(A.data_requisicao, '%d/%m/%Y')as data_requisicao 
                    FROM    compra AS A
               LEFT JOIN    funcionario AS B ON (A.id_user_pedido = B.id_funcionario)
                   WHERE    A.acompanhamento = '3' AND A.id_regiao = '$regiao'";
$result_3 = mysql_query($sql3) or die("erro re3: <!-- {$sql3} -->".  mysql_error());
$total3 = mysql_num_rows($result_3);

$sql4 = "SELECT A.*,B.nome1 as nome_escolha,C.c_razao AS fornecedor, A.prazo,
                            date_format(A.data_requisicao, '%d/%m/%Y')as data_requisicao,
                            date_format(A.prazo, '%d/%m/%Y')as prazo 
                    FROM    compra AS A
               LEFT JOIN    funcionario AS B ON (A.id_user_escolha = B.id_funcionario)
               LEFT JOIN    prestadorservico AS C ON (A.fornecedor_escolhido = C.id_prestador)
                   WHERE    A.acompanhamento = '4' AND A.id_regiao = '$regiao'";
$result_4 = mysql_query($sql4) or die("erro re4: <!-- {$sql4} -->".  mysql_error());
$total4 = mysql_num_rows($result_4);

$sql5 = "SELECT A.*,B.nome1,C.nome AS fornecedor,date_format(A.data_produto, '%d/%m/%Y')as data_produto, 
                            date_format(A.prazo, '%d/%m/%Y')as prazo 
                    FROM    compra AS A
               LEFT JOIN    funcionario AS B ON (A.id_user_pedido = B.id_funcionario)
               LEFT JOIN    fornecedores AS C ON (A.fornecedor_escolhido = C.id_fornecedor)
                   WHERE    A.id_regiao = '$regiao' AND A.acompanhamento >= '5' OR 
                            A.acompanhamento = '0' AND A.id_regiao = '$regiao'";
$result_5 = mysql_query($sql5) or die("erro re5: <!-- {$sql5} -->".  mysql_error());
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
            <?php if($total1 > 0){ ?>
            <div class="panel panel-default">
                <div class="panel-heading text-bold hidden-print"><i class="fa fa-paper-plane-o"></i> Pedidos em andamento</div>
                <div class="panel-body">
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
                                <td><?php echo $row_1['data_requisicao'] ?></td>
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
                </div>
            </div>
            <?php } ?>
            
            <!-- PARTE 2-->
            <?php if($total2 > 0){ ?>
            <div class="panel panel-default">
                <div class="panel-heading text-bold hidden-print"><i class="fa fa-search-plus"></i> Processos aguardando pesquisa</div>
                <div class="panel-body">
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
                </div>
            </div>
            <?php } ?>
            
            <!-- PARTE 3-->
            <?php if($total3 > 0){ ?>
            <div class="panel panel-default">
                <div class="panel-heading text-bold hidden-print"><i class="fa fa-suitcase"></i> Decisões a serem tomadas</div>
                <div class="panel-body">
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
                </div>
            </div>
            <?php } ?>
            
            <!-- PARTE 4-->
            <?php if($total4 > 0){ ?>
            <div class="panel panel-default">
                <div class="panel-heading text-bold hidden-print"><i class="fa fa-gavel"></i> Decisões</div>
                <div class="panel-body">
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
                </div>
            </div>
            <?php } ?>
            
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