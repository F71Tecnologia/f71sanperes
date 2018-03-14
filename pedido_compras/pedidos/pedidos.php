<?php
if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include("../../conn.php");
include("../../classes/global.php");
include("../../classes/pedidosClass.php");
include("../../classes/PedidosTipoClass.php");
include("../../wfunction.php");
include "../../classes/uploadfile.php";


$usuario = carregaUsuario();
$global = new GlobalClass();

include ("../php_pedidos.php");
?>

<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet ::</title>

        <link rel="shortcut icon" href="../../favicon.png">

        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/ui-autocomplete-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" >
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../css/progress.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap-compras.css" rel="stylesheet" type="text/css">
        <link href="../../resources/dropzone/dropzone.css" rel="stylesheet" media="screen">
        <style>
            .btn-sq-lg {
                width: 150px !important;
                height: 150px !important;
              }
        </style>
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header box-compras-header">
                        <h2><span class="fa fa-archive"></span> - COMPRAS <small>- Pedidos</small></h2>
                    </div>
                    <div role="tabpanel">
                        <ul class="nav nav-tabs nav-justified " style="margin-bottom: 15px;">
                            <li class="<?= checkAba('solicitapedido', $abasel) ?>"><a href="#solicitapedido" data-toggle="tab">Pedido de Compra</a></li> 
                            <li class="<?= checkAba('confirmapedidos', $abasel) ?>"><a href="#confirmapedidos" data-toggle="tab">Orçamento de Compras</a></li>
                            <li class="<?= checkAba('confirmapedidos2', $abasel) ?>"><a href="#confirmapedidos2" data-toggle="tab">Orçamento em Espera</a></li>
                            <li class="<?= checkAba('enviarpedidos', $abasel) ?>"><a href="#enviarpedidos" data-toggle="tab">Orçamentos Aprovados</a></li>
                            <li class="<?= checkAba('pedidosfinalizado', $abasel) ?>"><a href="#pedidosenviados" data-toggle="tab">Acompanhar Compras</a></li>
                            <li class="<?= checkAba('pedidoscancelados', $abasel) ?>"><a href="#pedidoscancelados" data-toggle="tab">Finalizar Compras</a></li>
                        </ul>

                        <!-- solicitação de do pedido -->
                        <div class="tab-content">
                            <div class="tab-pane fade <?= checkAba('solicitapedido', $abasel, 1) ?>" id="solicitapedido">
                                <br>
                                <div class="alert alert-warning">
                                    <strong>Atenção!</strong> O pedido criado deve conter os items de mesmo grupo de compras.<br>
                                    <strong>Atenção!</strong> Insira os itens ou anexe o pdf de pedido.
                                </div>
                                <form action="" method="post" class="form-horizontal" id="form-pedido" enctype="multipart/form-data">
                                    <input type="hidden" name="home" id="home" value="">
                                    <input type="hidden" name="method" id="method" value="dados">
                                    <input type="hidden" name="action" id="upload_anexo" value="upload_anexo">
                                    <fieldset>
                                        <legend>Pedido de Compra</legend>
                                        <div class="panel panel-default">
                                            <div class="panel-body">                                              
                                            <div id="lista_item">
                                                <div class="form-group">
                                                    <label for="" class="col-lg-2 control-label" onblur="">Urgência</label>
                                                    <div class="col-lg-9">
                                                        <input style="" type="text" name="urgencia[]" class="form-control urgencia" id="urgencia"></input> 
                                                    </div>
                                                </div>     
                                                <div class="form-group">
                                                    <label for="" class="col-lg-2 control-label" onblur="">Descrição</label>
                                                    <div class="col-lg-9">
                                                        <textarea rows="3" class="form-control" id="descricao" name="descricao[]"></textarea>
                                                    </div>
                                                </div>
                                                <div class="form-group col-sm-12" id="itens_pedidos">
                                                    <label for="" class="col-lg-2 control-label" onblur="">Item</label>
                                                    <div class="col-lg-7">
                                                          <input type="text" name="item[]" class="form-control" id="item"></input> 
                                                    </div>
                                                    <label for="" class="col-lg-1 control-label" onblur="">Quantidade</label>
                                                    <div class="col-lg-1">
                                                        <input type="text" name="quantidade_item[]" class="form-control" id="quantidade_item"></input> 
                                                    </div>
                                                    <div class="col-lg-1">
                                                           <a class="btn btn-default fa fa-plus" id="plus_item" title=""></a> 
                                                    </div>
                                                </div>
                                              
                                                <div class="form-group">
                                                    <label for="" class="col-lg-2 control-label" onblur="">Justificativa</label>
                                                    <div class="col-lg-9">
                                                        <textarea rows="3" class="form-control" id="justificativa" name="justificativa[]"></textarea>
                                                    </div>
                                                </div>
                                                         
                                                <div class="form-group col-sm-12" id="itens_projeto">
                                                    <label for="" class="col-lg-2 control-label" onblur="">Projeto</label>
                                                    <div class="col-lg-4">
                                                        <select class="form-control projeto_uni" name="projetos[]"><option>Selecionar o Projeto</option>
                                                            <?php while ($row = mysql_fetch_assoc($qr_projeto)) {
                                                                echo "<option value='{$row["id_projeto"]}'>{$row["id_projeto"]} - {$row["nome"]}</option>";
                                                            } ?>
                                                        </select>
                                                    </div>
                                                    <label for="" class="col-lg-1 control-label" onblur="">Unidade</label>
                                                    <div class="col-lg-5">
                                                        <select class="form-control">
                                                            <option>Selecionar a Unidade</option>
                                                            <?php while ($row = mysql_fetch_assoc($qr_projeto)) {
                                                                echo "<option value='{$row["id_regiao"]}'>{$row["regiao"]}</option>";
                                                            } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="" class="col-lg-2 control-label" onblur="">Ou anexar pedido</label>
                                            <div class="col-lg-9">
                                                <hr>
                                            </div>
                                        </div> 
                                        <div class="form-group col-sm-12" id="anexo_pedidos">
                                            <div class="col-lg-12">
                                                <div id="dropzone" class="dropzone">
                                                    <div class="fallback">
                                                        <input name="file" type="file" multiple />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>   
                                            <div class="panel-footer text-right">
                                                <!--<a href="#" class="btn btn-default" id="gera_excel"><i class="fa fa-file-excel-o text-success"></i> Gerar Excel para UPAs</a>-->
                                                <button type="button" id="enviaprods" name="buscarprods" value="Produtos" class="btn btn-primary"><i class="fa fa-list-ul"></i> Fazer Pedido</button>
                                            </div>
                                    
                                    
                                                
                                    </fieldset>
                                    
                                                
                                              
                                </form>
                            </div>
                            <!-- solicitação de do pedido -->

                            <!-- confirmacao do pedido -->
                            <div class="tab-pane fade <?= checkAba('confirmapedidos', $abasel, 1) ?>" id="confirmapedidos">
                                <form action="" id="form_orcamento" method="post" class="form-horizontal" enctype="multipart/form-data">
                                    <input type="hidden" name="method" id="method" value="dados2">
                                    <input type="hidden" name="action" id="upload_anexo2" value="upload_anexo2">
                                    <div id="confirmarPedido" class="loading">
                                        <legend>Orçamento de Compras</legend>
                                        <?php if (count($qr_item_pedido) > 0) { ?>
                                        
                                            <table class="table table-striped table-hover" id="table-confirma-pedido">
                                                <thead>
                                                    <tr>
                                                        <th>Descrição</th>
                                                        <th>Item</th>
                                                        <th>Quantidade</th>
                                                        <th>Projeto</th>
                                                        <th>Unidade</th>
                                                        <th>Data do Pedido</th>
                                                        <th>Pedido Anexado</th>
                                                        <th>Selecionar para orçamento</th>
                                                        
                                                    </tr>
                                                </thead>
                                                <tbody>  
                                                    <?php while ($value = mysql_fetch_assoc($qr_item_pedido))  { ?>
                                                    
                                                        <tr class="text text-sm" id="tr-<?= $value['id_pedido'] ?>">
                                                            <td><?= $value['descricao'] ?></td>
                                                             <td><?= $value['item'] ?></td>
                                                            <td><?= $value['quantidade'] ?></td>
                                                            <td><?= $value['projeto_nome'] ?></td>
                                                            <td><?= $value['unidade_nome'] ?></td>
                                                            <td><?= converteData($value['data_do_pedido'], "d/m/Y") ?></td>
                                                            <?php if($value['anexo']){ ?><td class="text-center"><a target="_blank" class="btn btn-danger btn-sm" href="/intranet/pedido_compras/pedidos<?= $value['anexo'] ?>"><i class="fa fa-file-pdf-o"><?= $value['pedido_anexado'] ?></i></a></td> <?php }else{ ?>
                                                            <td class="text-center"></td><?php } ?>
                                                            <td class="text-center"><input name="id_pedido" type="radio" value="<?= $value['id_item'] ?>" data-id="<?= $value['id_item'] ?>" class="check"></td>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody> 
                                            </table>
                                        <?php }   ?>
                                      
<!--                                            <div class="form-group">
                                                    <label for="fornecedor" class="col-lg-2 control-label" onblur="">Fornecedor</label>
                                                    <div class="col-lg-9">
                                                        <?php echo montaSelect($fornecedor, null, "name='id_prestador' id='contrato' class='form-control validate[required,custom[select]]'"); ?>
                                                    </div>
                                                    <div class="btn btn-default fa fa-plus" title=""></div>
                                            </div>-->
                                            <div class="form-group">
                                                <hr>
                                                    <label for="fornecedor" class="col-lg-2 control-label" onblur="">Cadastrar Fornecedor:</label>
                                            </div>
                                        <div class="col-sm-10">
                                            <div class="form-group">
                                                     <label for="fornecedor" class="col-lg-2 control-label" onblur="">CNPJ</label>
                                                    <div class="col-lg-4">
                                                         <input type="text" name="cnpj_fornecedor" class="form-control" id="cnpj_fornecedor"></input> 
                                                    </div>
                                                     
                                            </div>
                                           
                                            <div class="form-group">
                                                <label for="fornecedor" class="col-lg-2 control-label" onblur="">Nome</label>
                                                    <div class="col-lg-4">
                                                        <input type="text" name="nome_fornecedor" class="form-control" id="nome_forn"></input> 
                                                    </div>
                                                    <label for="fornecedor" class="col-lg-1 control-label" onblur="">Razão</label>
                                                    <div class="col-lg-4">
                                                        <input type="text" name="razao_fornecedor" class="form-control" id="razao_fornecedor"></input> 
                                                    </div>
                                            </div>
                                            <div class="form-group">
                                                    <label for="fornecedor" class="col-lg-2 control-label" onblur="">Endereço</label>
                                                    <div class="col-lg-9">
                                                        <input type="text" name="endereco_fornecedor" class="form-control" id="endereco_fornecedor"></input> 
                                                    </div>
                                            </div>
                                            <div class="form-group">
                                                     <label for="fornecedor" class="col-lg-2 control-label" onblur="">Telefone</label>
                                                    <div class="col-lg-4">
                                                         <input type="text" name="tel_fornecedor" class="form-control tel" id="tel_fornecedor"></input> 
                                                    </div>
                                                       <label for="fornecedor" class="col-lg-1 control-label" onblur="">E-mail</label>
                                                    <div class="col-lg-4">
                                                         <input type="email" name="email_fornecedor" class="form-control" id="email_fornecedor"></input> 
                                                    </div>
                                            </div>
                                            <div class="form-group ">
                                                     <label for="fornecedor" class="col-lg-9 control-label" onblur="">Valor</label>
                                                    <div class="col-lg-2">
                                                        <div class="input-group">
                                                            <input type="text" name="valor_fornecedor" class="form-control valor" id="valor_fornecedor"></input> 
                                                            <span class="input-group-addon"><i class="fa fa-dollar"></i></span>
                                                        </div>
                                                    </div>
                                            </div>
                                           </div>

                                            <div class="col-sm-2">
                                                <button type="button" href="#" class="btn btn-sq-lg btn-success " data-id="" id="upload_principal">
                                                    <i class="fa fa-arrow-up fa-5x"></i><br/>
                                                    Adicionar <br>arquivo
                                                </button>
                                                <p id="upload_principal_sucesso">Arquivo Enviado com Sucesso!</p>
<!--                                                <button type="button" href="#" class="btn btn-sq-lg btn-danger doc_upload" data-id="" id="upload_principal_contem">
                                                    <i class="fa fa-times fa-5x"></i><br/>
                                                    Remover<br>Arquivo Principal
                                                </button>-->
                                            </div>
                                            <div class="form-group col-sm-12" id="anexo_orcamento">
<!--                                                <div class="col-lg-12">
                                                    <div id="dropzone2" class="dropzone">
                                                        <div class="fallback">
                                                            <input name="file" type="file" multiple />
                                                        </div>
                                                    </div>
                                                </div>-->
 <div class="form-group">
                                                <div class="col-lg-12">
                                                    <label for="fornecedor" class="col-lg-2 control-label" onblur="">Outros orçamentos:</label>
                                                </div>
 </div>
                                                <div class="col-lg-2">
                                                    <button type="button" href="#" class="btn btn-sq-lg btn-default doc_upload" data-id="" id="">
                                                        <i class="fa fa-arrow-up fa-5x"></i><br/>
                                                        Adicionar <br>arquivo(s)
                                                    </button>
                                                </div><p class="doc_upload_sucesso">Arquivo Enviado com Sucesso!</p>
                                            </div>
                                            <div class="form-group">
                                                <br>
                                                <div class="col-lg-12 text-right">
                                                     <a class="btn btn-default" id="botao_orcamento">Cadastrar</a>
                                                </div>
                                            </div>
                                    </div>
                                    
                                </form>
                            </div>   <!-- analise e confirmação do pedido -->
                            <div class="tab-pane fade <?= checkAba('confirmapedidos2', $abasel, 1) ?>" id="confirmapedidos2">
                                <form action="" id="form_espera" method="post" class="form-horizontal" enctype="multipart/form-data">
                                    <input type="hidden" name="method" id="method" value="dados2">
                                    <input type="hidden" name="action" id="upload_anexo2" value="upload_anexo2">
                                    <div id="confirmarPedido" class="loading">
                                          <legend>Orçamentos em Espera</legend>
                                        <?php if (count($qr_item_pedido2) > 0) { ?>
                                        
                                            <table class="table table-striped table-hover" id="table-confirma-pedido">
                                                <thead>
                                                    <tr>
                                                        <th>Nº Pedido</th>
                                                        <th>Descrição</th>
                                                        <th>Quantidade</th>
                                                        <th>Justificativa</th>
                                                        <th>Fornecedor</th>
                                                        <th>Data do Pedido</th>
                                                        <th>Valor Total</th>
                                                        <th>Anexo</th>
                                                        <th>Deletar</th>
                                                    </tr>
                                                </thead>
                                                <tbody> 
                                                    <?php while ($value = mysql_fetch_assoc($qr_item_pedido2))  {
                                                        if($value['flag']==1){
                                                            if($item_nome != $value['item']){ ?>
                                                    <tr><td colspan="8"><h4><?= $value['item'] ?></h4></td></tr>
                                                                <?php 
                                                                $item_nome = $value['item']; 
                                                            } ?>
                                                
                                                        <tr class="text text-sm" id="tr-<?= $value['id_pedido'] ?>">
                                                            <td><?= $value['id_pedido'] ?></td>
                                                            <td><?= $value['descricao'] ?></td>
                                                            <td>
                                                                <?php if($value['quantidade'] > 0){
                                                                   echo $value['quantidade'];      
                                                                }else{
                                                                    echo "Em Anexo!";
                                                                } ?>
                                                            </td>
                                                            <td><?= $value['justificativa'] ?></td>
                                                            <td><?= $value['nome_fornecedor'] ?></td>
                                                            <td><?= converteData($value['data_do_pedido'], "d/m/Y") ?></td>
                                                            <td><?= $value['valor'] ?></td>
                                                            <td class=""><a href="/intranet/pedido_compras/pedidos<?= $value['anexo'] ?>" class=" btn btn-default btn-xs"><i class="fa fa-file-pdf-o text-danger"></i></a></td>
                                                            <td class="">
                                                                <!--<a class="btn btn-success btn-sm" data-key="<?= $value['id_pedido'] ?>" id="aceitar_orcamento"><i class="fa fa-check"></i></a>-->
                                                                <a class="btn btn-danger btn-sm" data-key="<?= $value['id_pedido'] ?>" id="excluir_orcamento"><i class="fa fa-close"></i></a>
                                                            </td>
                                                         
                                                        </tr>
                                                    <?php 
                                                    } }?>
                                                </tbody>
                                            </table>
                                        <?php }  ?>
                                          
                    
                                    </div>
                                </form>
                            </div>   <!-- analise e confirmação do pedido -->

                            <div class="tab-pane fade <?= checkAba('enviarpedidos', $abasel, 1) ?>" id="enviarpedidos">
                                <div class="alert alert-warning">
                                    <strong>Atenção!</strong> Anexar Nota, Boleto e Documentos para pagamento.
                                </div>
                                <form action="" id="form_aprovado" method="post" class="form-horizontal" enctype="multipart/form-data">
                                    <input type="hidden" name="method" id="method" value="dados3">
                                    <input type="hidden" name="action" id="upload_anexo3" value="upload_anexo3">
                                    <div id="confirmarPedido" class="loading">
                                           <legend>Orçamentos Aprovados</legend>
                                        <?php if (count($qr_item_pedido3) > 0) { ?>
                                        
                                            <table class="table table-striped table-hover" id="table-confirma-pedido">
                                                <thead>
                                                    <tr>
                                                        <th>Nº Pedido</th>
                                                        <th>Descrição</th>
                                                        <th>Quantidade</th>
                                                        <th>Justificativa</th>
                                                        <th>Fornecedor</th>
                                                        <th>Data do Pedido</th>
                                                        <th>Valor Total</th>
                                                        <th>Anexo</th>
                                                        <th>Selecionar</th>
                                                    </tr>
                                                </thead>
                                                <tbody> 
                                                    <?php while ($value = mysql_fetch_assoc($qr_item_pedido3))  {
                                                        if($value['flag']==2){
                                                            if($item_nome != $value['item']){ ?>
                                                    <tr><td colspan="8"><h4><?= $value['item'] ?></h4></td></tr>
                                                                <?php 
                                                                $item_nome = $value['item']; 
                                                            } ?>
                                                
                                                        <tr class="text text-sm" id="tr-<?= $value['id_pedido'] ?>">
                                                            <td><?= $value['id_pedido'] ?></td>
                                                            <td><?= $value['descricao'] ?></td>
                                                            <td>
                                                                <?php if($value['quantidade'] > 0){
                                                                   echo $value['quantidade'];      
                                                                }else{
                                                                    echo "Em Anexo!";
                                                                } ?>
                                                            </td>
                                                            <td><?= $value['justificativa'] ?></td>
                                                            <td><?= $value['nome_fornecedor'] ?></td>
                                                            <td><?= converteData($value['data_do_pedido'], "d/m/Y") ?></td>
                                                            <td><?= $value['valor'] ?></td>
                                                            <td class=""><a href="/intranet/pedido_compras/pedidos<?= $value['anexo'] ?>" class=" btn btn-default btn-xs"><i class="fa fa-file-pdf-o text-danger"></i></a></td>
                                                            <td class="text-center">
                                                                <!--<a class="btn btn-success btn-sm" data-key="<?= $value['id_pedido'] ?>" id="aceitar_orcamento"><i class="fa fa-check"></i></a>-->
                                                                <!--<a class="btn btn-danger btn-sm" data-key="<?= $value['id_pedido'] ?>" id="excluir_orcamento"><i class="fa fa-close"></i></a>-->
                                                                <input name="id_pedido" id="id_pedido" type="radio" value="<?= $value['id_orcamento'] ?>">
                                                                <input name="id_projeto"  id="id_projeto"  type="hidden" value="<?= $value['id_projeto'] ?>">
                                                                <input name="id_unidade"  id="id_unidade"  type="hidden" value="<?= $value['id_unidade'] ?>">
                                                                <input name="id_valor" id="id_valor" type="hidden" value="<?= $value['valor'] ?>">
                                                            </td>
                                                         
                                                        </tr>
                                                    <?php 
                                                    } ?>
                                                </tbody>
                                            </table>
                                        <?php } } ?>
                                            <div class="form-group">
                                                <hr>
                                                    <label for="fornecedor" class="col-lg-2 control-label" onblur="">Informação de Pagamento:</label>
                                            </div>
                                            <div class="form-group">
                                                     <label for="fornecedor" class="col-lg-2 control-label" onblur="">Descrição</label>
                                                    <div class="col-lg-4">
                                                         <input type="text" name="cnpj_fornecedor" class="form-control" id="cnpj_fornecedor"></input> 
                                                    </div>
                                                     
                                            </div>
                                           
                                            <div class="form-group">
                                                <label for="fornecedor" class="col-lg-2 control-label" onblur="">Data</label>
                                                    <div class="col-lg-4">
                                                        <div class="input-daterange input-group" id="bs-datepicker-range">
                                                            <select id="mes" name="mes" class="validate[required,custom[select]] form-control"><option value="-1">« Selecione »</option><option value="1">Janeiro</option><option value="2">Fevereiro</option><option value="3">Março</option><option value="4">Abril</option><option value="5">Maio</option><option value="6">Junho</option><option value="7" selected="selected">Julho</option><option value="8">Agosto</option><option value="9">Setembro</option><option value="10">Outubro</option><option value="11">Novembro</option><option value="12">Dezembro</option></select>                                            <span class="input-group-addon">Ano</span>
                                                            <select id="ano" name="ano" class="validate[required,custom[select]] form-control"><option value="2006">2006</option><option value="2007">2007</option><option value="2008">2008</option><option value="2009">2009</option><option value="2010">2010</option><option value="2011">2011</option><option value="2012">2012</option><option value="2013">2013</option><option value="2014">2014</option><option value="2015">2015</option><option value="2016" selected="selected">2016</option><option value="2017">2017</option><option value="2018">2018</option></select>                                
                                                        </div>
                                                    </div>
                                                     <label for="fornecedor" class="col-lg-1 control-label" onblur="">Valor Bruto</label>
                                                    <div class="col-lg-4">
                                                         <div class="input-group">
                                            <input name="valor_bruto" type="text" id="valor_bruto" class="form-control valor" value="">
                                            <span class="input-group-addon"><i class="fa fa-dollar"></i></span>
                                        </div>
                                                    </div>
                                            </div>
                                           
                                           <div class="form-group">
                                    <label for="valor_multa" class="col-sm-2 control-label">Valor Multa</label>
                                    <div class="col-sm-4">
                                        <div class="input-group">
                                            <input name="valor_multa" type="text" id="valor_multa" class="form-control valor" value="0,00">
                                            <span class="input-group-addon"><i class="fa fa-dollar"></i></span>
                                        </div>
                                    </div>
                                    <label for="valor_juros" class="col-sm-2 control-label">Valor Juros</label>
                                    <div class="col-sm-3">
                                        <div class="input-group">
                                            <input name="valor_juros" type="text" id="valor_juros" class="form-control valor" value="0,00">
                                            <span class="input-group-addon"><i class="fa fa-dollar"></i></span>
                                        </div>
                                    </div>
                                </div>

                                            <div class="form-group col-sm-12" id="anexo_orcamento">
                                                <div class="col-lg-12">
                                                    <div id="dropzone3" class="dropzone">
                                                        <div class="fallback">
                                                            <input name="file" type="file" multiple />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <br>
                                                <div class="col-lg-12 text-right">
                                                     <a class="btn btn-default" id="botao_aprovado">Enviar</a>
                                                </div>
                                            </div>
                                    </div>
                                    
                                </form>
                            </div>   <!-- envio do pedido ao fornecedor -->

                            <div class="tab-pane fade <?= checkAba('pedidosfinalizado', $abasel, 1) ?>" id="pedidosenviados">
                                <form method="post" class="form-horizontal" id="form_filtro_finalizados" action="pedidos_methods.php">
                                    <div class="panel panel-default">
                                        <div class="panel-body">

                                            <!--div class="form-group">
                                                <label for="tipo_finalizado" class="col-lg-2 control-label">Tipo de Pedido</label>
                                                <div class="col-lg-6">
                                                    <?php echo montaSelect(array('-1' => 'Selecione', '1' => 'Material Hospitalar', '2' => 'Medicamentos'), NULL, 'name="tipo_finalizado" id="tipo_finalizado" class="validate[required,custom[select]] form-control"') ?>
                                                </div> 
                                            </div-->
                                            <div class="form-group col-sm-12" id="itens_projeto">
                                                    <label for="" class="col-lg-2 control-label" onblur="">Projeto</label>
                                                    <div class="col-lg-4">
                                                        <select class="form-control projeto_uni" name="projetos"><option>Selecionar o Projeto</option>
                                                            <?php while ($row = mysql_fetch_assoc($qr_projeto)) {
                                                                echo "<option value='{$row["id_projeto"]}'>{$row["id_projeto"]} - {$row["nome"]}</option>";
                                                            } ?>
                                                        </select>
                                                    </div>
                                                    <label for="" class="col-lg-1 control-label" onblur="">Unidade</label>
                                                    <div class="col-lg-5">
                                                        <select class="form-control">
                                                            <option>Selecionar a Unidade</option>
                                                            <?php while ($row = mysql_fetch_assoc($qr_projeto)) {
                                                                echo "<option value='{$row["id_regiao"]}'>{$row["regiao"]}</option>";
                                                            } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            <div class="form-group">
                                                <label for="tipo_finalizado" class="col-lg-2 control-label">Mes/Ano do Pedido</label>
                                                <div class="col-lg-5">
                                                    <div class="input-group">
                                                        <?php echo montaSelect(mesesArray(), date('m'), 'name="mes" id="mes" class="form-control"') ?>
                                                        <span class="input-group-addon">/</span>
                                                        <?php echo montaSelect(anosArray(2016, date('Y')), NULL, 'name="ano" id="ano" class="form-control"') ?>
                                                    </div>
                                                </div> 
                                            </div>

                                        </div>
                                        <div class="panel-footer text-right">
                                            <button type="submit" class="btn btn-primary" name="filtrar_finalizados" value="1"><i class="fa fa-filter"></i> Filtrar</button>
                                        </div>
                                    </div>
                                </form>
                                <table class="table table-striped table-hover table-condensed">
                                    <thead>
                                        <tr>
                                            <th class="text-center">#</th>
                                            <th>Data</th>
                                            <th>Projeto</th>
                                            <th>Tipo</th>
                                            <th>Fornecedor</th>
                                            <th style="width: 260px"></th>                                            
                                        </tr>
                                    </thead>
                                    <tbody id="ped_finalizados">
                                        <?php foreach ($listaEnviados as $value) { ?>
                                            <tr id="tr-<?= $value['id_pedido'] ?>">
                                                <td class="text-center"><?= $value['id_pedido'] ?></td>
                                                <td><?= converteData($value['dtpedido'], "d/m/Y") ?></td>
                                                <td><?= $value['upa'] ?></td>
                                                <td>
                                                    <?php
                                                    if ($value['tipo'] == 2) {
                                                        echo 'Medicamentos';
                                                    } else if ($value['tipo'] == 1) {
                                                        echo 'Material';
                                                    }
                                                    ?>
                                                </td>
                                                <td><?= $value['razao'] ?></td>
                                                <td class="text-right">
                                                    <a href="pdf/PED<?= $value['id_pedido'] ?>.pdf" target="_blank" class="btn btn-default btn-xs"><i class="fa fa-file-pdf-o text-danger"></i> PDF</a>
                                                    <button type="button" class="btn btn-info btn-xs conferencia" data-id="<?= $value['id_pedido'] ?>"><i class="fa fa-list"></i> Conferência</button>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div><!-- historico de pedidos -->

                            <div class="tab-pane fade <?= checkAba('pedidoscancelados', $abasel, 1) ?>" id="pedidoscancelados">

                                <?php if (count($pedidos_cancelados) > 0) { ?>
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Data</th>
                                                <th>Fornecedor</th>
                                                <th>Projeto</th>
                                                <th>Motivo</th>
                                                <th>Funcionário</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-uppercase text-sm text-danger">
                                            <?php foreach ($pedidos_cancelados as $value) { ?>
                                                <tr id="tr-<?= $value['id_pedido'] ?>">
                                                    <td><?= $value['id_pedido'] ?></td>
                                                    <td><?= converteData($value['dtpedido'], "d/m/Y") ?></td>
                                                    <td><?= $value['razao'] ?></td>
                                                    <td><?= $value['upa'] ?></td>
                                                    <td><?= $value['observacao'] ?></td>
                                                    <td><?= $value['confirmado'] ?></td>
                                                    <td>
                                                        <a href="#" class="btn  btn-warning btn-xs pedido_reabrir" data-id="<?= $value['nrpedido'] ?>" >
                                                            <i class="fa fa-file-text-o"></i> Reabrir <?= $values[''] ?>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                <?php } else { ?>
                                    <div class="alert alert-dismissable alert-warning text text-center">
                                        <h4>Não há Compras...</h4>
                                    </div>
                                <?php } ?>
                            </div><!-- historico de pedidos -->
                        </div>
                    </div>
                </div>
            </div>
                <input type="hidden" id="upload_documento">
            <?php include_once '../../template/footer.php'; ?>
        </div>
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.11.2.custom/jquery-ui.min.js"></script>
        <script src="../../js/jquery.form.js"></script>
        <script src="../../js/jquery.maskMoney_3.0.2.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../resources/dropzone/dropzone.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../js/global.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../resources/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
        <script src="js_pedidos.js" type="text/javascript"></script>
<!--        <script src="js/pedidos.js" type="text/javascript"></script>-->
        <script>
          $(function() {
              $("#upload_principal_sucesso").hide();
              $(".doc_upload_sucesso").hide();
                    
            $(".check").change(function(){
                    if($(this).prop("checked") == true){
                    var id_chek = $(this).data('id');
//                    alert(id_chek);
                    $(".doc_upload").attr('data-id',id_chek);
                    $("#upload_principal").attr('data-id',id_chek);
                    
                     /* Configura a requisição AJAX */
//                            $.ajax({
//                                 url : 'consulta_fornecedor.php?method=arquivoPrincipal', /* URL que será chamada */ 
//                                 type : 'POST', /* Tipo da requisição */ 
//                                 data: 'id=' + id_chek, /* dado que será enviado via POST */
//                                 dataType: 'json', /* Tipo de transmissão */
//                                 success: function(data){
//                                     if(data.sucesso == 1){
//                                         if(data.sucesso == 1){
//                                             $("#upload_principal").hide();
//                                             $("#upload_principal_contem").show();
//                                         };
//                                     }
//                                 }
//                            });   
//                    return false; 
                }             
            });
              
              Dropzone.autoDiscover = false;
//DROPZONE PEDIDO DE COMPRAS
                var myDropzoneAnexo = new Dropzone("#dropzone",{
                    url: "pedidos.php?method=up",
                    addRemoveLinks : true,
                    maxFilesize: 50,
                    //envio automatico
                    autoQueue: false,
                    dictResponseError: "Erro no servidor!",
                    dictCancelUpload: "Cancelar",
                    dictFileTooBig: "Tamanho máximo: 50MB",
                    dictRemoveFile: "Remover Arquivo",
                    canceled: "Arquivo Cancelado",
                    acceptedFiles: '.jpg,.gif,.png,.pdf,.JPG,.GIF,.PNG,.PDF'
                    , complete: function(file, responseText){
                        if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
                            bootAlert('Pedido enviado com sucesso!', 'Pedido Cadastrado!', function(){ window.location.href = "pedidos.php"; }, 'success');
                        }
                    }
                });
                
                $("#enviaprods").on('click', function(){
                    if ($("#form-pedido").validationEngine('validate')) {
                        var dados = $('#form-pedido').serialize();
                        console.log(dados);
                        var totalAnexos = 0;
                        cria_carregando_modal();
                        $.post("", dados, function(resposta){
//                            console.log(resposta);return false;
                                myDropzoneAnexo.on('sending',function(file, xhr, formData) {
                                    formData.append("id_pedido", resposta); // Append all the additional input data of your form here!
                                    formData.append("action", 'upload_anexo'); // Append all the additional input data of your form here!
                                });

                                myDropzoneAnexo.enqueueFiles(myDropzoneAnexo.getFilesWithStatus(Dropzone.ADDED));

                                if(myDropzoneAnexo.files.length === 0){
                                    remove_carregando_modal();
//                                    bootAlert('Orçamento Cadastrado Com Sucesso!', 'Saída Cadastrada!', function(){ window.location.href = "pedidos.php"; }, 'success');
                                }
                        }); 
                    }
                });
//DROPZONE ORÇAMENTO DE COMPRAS
//                var myDropzoneAnexo2 = new Dropzone("#dropzone2",{
//                    url: "pedidos.php?method=up2",
//                    addRemoveLinks : true,
//                    maxFilesize: 50,
//                    //envio automatico
//                    autoQueue: false,
//                    dictResponseError: "Erro no servidor!",
//                    dictCancelUpload: "Cancelar",
//                    dictFileTooBig: "Tamanho máximo: 50MB",
//                    dictRemoveFile: "Remover Arquivo",
//                    canceled: "Arquivo Cancelado",
//                    acceptedFiles: '.jpg,.gif,.png,.pdf,.JPG,.GIF,.PNG,.PDF'
//                    , complete: function(file, responseText){
//                        if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
//                            bootAlert('Pedido enviado com sucesso!', 'Pedido Cadastrado!', function(){ window.location.href = "pedidos.php"; }, 'success');
//                        }
//                    }
//                });
                var myDropzoneDocumentos = new Dropzone('#upload_documento', { // Make the whole body a dropzone
                    url: "pedidos.php?method=up2", // Set the url
    //                maxFiles: 1,
                    acceptedFiles: ".jpg,.gif,.png,.pdf,.JPG,.GIF,.PNG,.PDF",
                    autoQueue: true, // Make sure the files aren't queued until manually added
                    clickable: '#upload_principal', // Define the element that should be used as click trigger to select files.
                    sending: function(file, xhr, formData) {
                        formData.append("id_orcamento", $("#upload_principal").data("id")); // Append all the additional input data of your form here!
                        formData.append("action", 'upload_anexo2'); // Append all the additional input data of your form here!
                    },
                    complete: function(progress) {
                    console.log(progress);
                        $("#upload_principal").hide();
                        $("#upload_principal_sucesso").show();
                    }
                });
            var myDropzoneDocumentos2 = new Dropzone('#upload_documento', { // Make the whole body a dropzone
                    url: "pedidos.php?method=up2", // Set the url
//                    maxFiles: 1,
                    acceptedFiles: ".jpg,.gif,.png,.pdf,.JPG,.GIF,.PNG,.PDF",
                    autoQueue: true, // Make sure the files aren't queued until manually added
                    clickable: '.doc_upload', // Define the element that should be used as click trigger to select files.
                    sending: function(file, xhr, formData) {
                        formData.append("id_orcamento", $(".doc_upload").data("id")); // Append all the additional input data of your form here!
                        formData.append("action", 'upload_anexo2'); // Append all the additional input data of your form here!
                    },
                    complete: function(progress) {
                    console.log(progress);
                        $(".doc_upload").hide();
                        $(".doc_upload_sucesso").show();
                    }
                });
                $('body').on('click', '.doc_upload', function(){
//                $this = $(this);
//                
//                myDropzoneDocumentos.on('sending',function(file, xhr, formData) {
//                    formData.append("id_documento", $this.data('id')); // Append all the additional input data of your form here!
//                });
//                
//                myDropzoneDocumentos.on('complete',function(progress) {
//                    $.post('', { action: 'fim_upload_doc', id_doc: $this.data('id') }, function (data){
//                        $('#trdoc'+$this.data('id')).find('.ver_anexo').remove();
//                        $('#trdoc'+$this.data('id')).prepend('<button type="button" class="btn btn-xs btn-info ver_anexo" data-clt="<?= $row['id_clt'] ?>" data-upload="'+$this.data('id')+'"><i class="fa fa-search"></i></button>');
//                        $('#trdocdata'+$this.data('id')).html(data);
//                    });
//                });
//                
//                $('#upload_documento').trigger('click');
            });
                
                $("#botao_orcamento").on('click', function(){
                    if ($("#form_orcamento").validationEngine('validate')) {
                        var dados = $('#form_orcamento').serialize();
                        console.log(dados);
                        var totalAnexos = 0;
                        cria_carregando_modal();
                        $.post("", dados, function(resposta){
//                            console.log(resposta);return false;
                                bootAlert('Orçamento Enviado com Sucesso!', 'Orçamento Cadastrado!', function(){ window.location.href = "pedidos.php"; }, 'success');
                        }); 
                    }
                });
//DROPZONE ORÇAMENTO APROVADOS - SUBIR BOLETOS
                var myDropzoneAnexo3 = new Dropzone("#dropzone3",{
                    url: "pedidos.php?method=up3",
                    addRemoveLinks : true,
                    maxFilesize: 50,
                    //envio automatico
                    autoQueue: false,
                    dictResponseError: "Erro no servidor!",
                    dictCancelUpload: "Cancelar",
                    dictFileTooBig: "Tamanho máximo: 50MB",
                    dictRemoveFile: "Remover Arquivo",
                    canceled: "Arquivo Cancelado",
                    acceptedFiles: '.jpg,.gif,.png,.pdf,.JPG,.GIF,.PNG,.PDF'
                    , complete: function(file, responseText){
                        if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
                            bootAlert('Orçamento Anexos Enviado com Sucesso!', 'Pedido Cadastrado!', function(){ window.location.href = "pedidos.php"; }, 'success');
                        }
                    }
                });
                
                $("#botao_aprovado").on('click', function(){
                    if ($("#form_aprovado").validationEngine('validate')) {
                        var dados = $('#form_aprovado').serialize();
                        console.log(dados);
                        var totalAnexos = 0;
                        cria_carregando_modal();
                        $.post("", dados, function(resposta){
//                            console.log(resposta);return false;
                                myDropzoneAnexo3.on('sending',function(file, xhr, formData) {
                                    formData.append("id_orcamento", resposta); // Append all the additional input data of your form here!
                                    formData.append("action", 'upload_anexo3'); // Append all the additional input data of your form here!
                                });

                                myDropzoneAnexo3.enqueueFiles(myDropzoneAnexo3.getFilesWithStatus(Dropzone.ADDED));

                                if(myDropzoneAnexo3.files.length === 0){
                                    remove_carregando_modal();
//                                    bootAlert('Orçamento Cadastrado Com Sucesso!', 'Saída Cadastrada!', function(){ window.location.href = "pedidos.php"; }, 'success');
                                }
                        }); 
                    }
                });
//final 
                $('body').on('click','#id_pedido',function(){
                    var id_valor = $("#id_valor").val();
                   // alert(id_pedido);
                    $("#valor_bruto").val(id_valor);
                });
                
                $('body').on('click', '#add_unidade', function(){
                    var n = $('.unidade_saida').length;
                    var disabled = ($('#caixinha').prop('checked')) ? false : true;
                    var html = 
                        $('<div>', { class: 'panel-footer unidade_saida' }).append(
                            $('<div>', { class: 'form-group', 'data-key':n }).append(
                                $('<div>', { class: 'col-sm-5' }).append(
                                    $('<div>', { class: 'text-bold', html: 'Projeto' + (n+1) }),
                                    $('<div>', { class: '' }).append(
                                        $('<select>', { class:'form-control projeto_uni', 'data-key':n, name:'unidades['+n+'][id_projeto]', id:'id_projeto'+n }).html(
                                            '<?php foreach ($arrayProjetos as $key => $value) { echo "<option value=\"{$key}\">{$value}</option>"; } ?>'
                                        )
                                    )
                                ),
                                $('<div>', { class: 'col-sm-5' }).append(
                                    $('<div>', { class: 'text-bold', html: 'Unidade' + (n+1) }),
                                    $('<div>', { class: '', id:'div_uni' + n}).append(
                                        $('<select>', { class:'form-control input-sm', name:'unidades['+n+'][id_unidade]', id:'uni'+n }).append(
                                            $('<option>', { value:'', html:'-- SELECIONE --' })
                                        )
                                    )
                                ),
                                $('<div>', { class: 'col-sm-1' }).append(
                                    $('<div>', { class: 'text-bold', html: '&nbsp;' }),
                                    $('<button>', { type:"button", class:"del_uni btn btn-danger" }).append(
                                        $('<i>', { class:"fa fa-trash-o" })
                                    )
                                )
                            )
                        )
                        html.find('.valor').maskMoney({prefix:'R$ ', allowNegative: true, thousands:'.', decimal:','});
                        html.find('.gst').prop('disabled', !$('#caixinha').val());
                    $('#div_unidades').append(html);
                });
                
                $('body').on('change', '.projeto_uni', function(){
                    var $this = $(this);
//                    console.log($this);
                    $.post("", { method:'unidades', id_projeto: $this.val(), id_unidade: $this.data('unidade') }, function(resposta){
                        $this.parent().next().next().html(resposta);
//                        $('#div_uni' + $this.data('key')).html(resposta);
                    });
                });
                
                $('body').on('click', '.del_uni', function(){
                    var $this = $(this);
//                    console.log($this);
                    
                    if($this.data('assoc')){
                        bootConfirm('Confimar exclusão da unidade?','Confirmação',function(data){
                            if(data){
                                $.post("", { method:'del_unidades_saida', id: $this.data('assoc') }, function(resposta){
//                                    console.log(resposta);
                                    bootAlert('Unidade excluida com sucesso','Sucesso',null,'success');
                                    $this.parent().parent().parent().remove();
                                });
                            }
                        },'warning');
                    } else {
                        $this.parent().parent().parent().remove();
                    }
                });
              
               
                   var campos_max = 20;   //max de 10 campos
                   var x = 1; // campos iniciais
                   $('#plus_item').click (function(e) {
                           e.preventDefault();     //prevenir novos clicks
                           if (x < campos_max) {
                                $('#lista_item').append('<div><div class="col-lg-12"><a href="#" class="remover_campo btn btn-danger fa fa-trash"></a></div><div class="form-group col-sm-12" id="itens_pedidos"><label for="" class="col-lg-2 control-label" onblur="">Item</label><div class="col-lg-7"><input type="text" name="item[]" class="form-control" id="item"></input></div><label for="" class="col-lg-1 control-label" onblur="">Quantidade</label><div class="col-lg-1"><input type="text" name="quantidade_item[]" class="form-control" id="quantidade_item"></input></div></div><div class="form-group"><label for="" class="col-lg-2 control-label" onblur="">Descrição</label><div class="col-lg-9"><textarea rows="3" class="form-control" id="descricao" name="descricao[]"></textarea></div></div><div class="form-group"><label for="" class="col-lg-2 control-label" onblur="">Justificativa</label><div class="col-lg-9"><textarea rows="3" class="form-control" id="justificativa" name="justificativa[]"></textarea></div></div><div class="form-group col-sm-12" id="itens_pedidos"><label for="" class="col-lg-2 control-label" onblur="">Projeto</label><div class="col-lg-4"><select class="form-control projeto_uni" name="projetos[]"><option>Selecionar o Projeto</option><?php while ($row = mysql_fetch_assoc($qr_projeto1)) {echo '<option value="'.$row["id_projeto"].'">'.$row["id_projeto"] .' - '.$row["nome"].'</option>';} ?></select></div><label for="" class="col-lg-1 control-label" onblur="">Unidade</label><div class="col-lg-5"><select class="form-control"><option>Selecionar a Unidade</option></select></div></div><hr></div>');
                                x++;
                           }
                   });

                   // Remover o div anterior
                   $('body').on("click",".remover_campo",function(e) {
                           e.preventDefault();
                           $(this).parent().parent().remove();
                           x--;
                   });
                   
                    $('#cnpj_fornecedor').blur(function(){
                            /* Configura a requisição AJAX */
                            $.ajax({
                                 url : 'consulta_fornecedor.php?method=consultar', /* URL que será chamada */ 
                                 type : 'POST', /* Tipo da requisição */ 
                                 data: 'cnpj_fornecedor=' + $('#cnpj_fornecedor').val(), /* dado que será enviado via POST */
                                 dataType: 'json', /* Tipo de transmissão */
                                 success: function(data){
                                     if(data.sucesso == 1){
                                         $('#nome_forn').val(data.nome);
                                         $('#razao_fornecedor').val(data.razao);
                                         $('#endereco_fornecedor').val(data.endereco);
                                         $('#tel_fornecedor').val(data.tel);
                                         $('#email_fornecedor').val(data.email);
                                     }
                                 }
                            });   
                    return false;    
                    });
                   
            });
        </script>
    </body>
</html>