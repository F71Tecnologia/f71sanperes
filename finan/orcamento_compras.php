<?php
if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include("../conn.php");

include("../classes/global.php");

include("../classes/pedidosClass.php");

include("../classes/PedidosTipoClass.php");
include("../wfunction.php");

$usuario = carregaUsuario();
$global = new GlobalClass(); 



if(isset($_REQUEST['buscarprods'])){
    $item = $_REQUEST['item'];
    $quantidade_item = $_REQUEST['quantidade_item'];
    $descricao = $_REQUEST['descricao'];
    $justificativa = $_REQUEST['justificativa'];
    $projetos = $_REQUEST['projetos'];
    $unidade = $_REQUEST['unidades'];
    $data_da_compra = $_REQUEST['data_da_compra'];
    
    //print_array($_REQUEST);
    $sql_row_pedido = mysql_query("SELECT * FROM novo_pedido");
    $sql_rows_pedido = mysql_num_rows($sql_row_pedido);
    
   
    mysql_query( "INSERT INTO novo_pedido (id_func) VALUES ({$usuario['id_funcionario']})") or die(mysql_error());
    $novo_id = mysql_insert_id();

    $sql_item = "INSERT INTO item_pedido (id_pedido,item,quantidade,descricao,justificativa,id_projeto,id_unidade,data_da_compra) VALUES ";

    $dados = array(); 
    // concatena os dados linha por linha
    for($i = 0;$i < count($item); $i++) {
        $dados[] = "('$novo_id', '$item[$i]', '$quantidade_item[$i]', '$descricao[$i]', '$justificativa[$i]', '$projetos[$i]', '$unidade[$i]', '".implode("-",array_reverse(explode("/",$data_da_compra[$i])))."')";
    }
    print_array($dados);
    // concatena a consulta com os valores
    $sql_item .= implode(',', $dados);
    mysql_query($sql_item) or die(mysql_error());
    header('location:pedidos.php');
    //FIM DO ARRAY
}



$id_regiao = $usuario['id_regiao'];

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$abasel = (isset($_REQUEST['aba'])) ? $_REQUEST['aba'] : 'solicitapedido';

$projeto1 = montaSelect($global->carregaProjetosByRegiao($id_regiao), $projetoR, "id='projeto1' name='projeto' class='form-control validate[required,custom[select]]' data-for='contrato'");
$arrayProjetos = $global->carregaProjetosByRegiao($id_regiao);

$qr_unidade = mysql_query("SELECT * FROM unidade WHERE campo1 = {$id_projeto} AND status_reg = 1 ORDER BY unidade");
        $unidade = $default;
        while ($row_unidade = mysql_fetch_assoc($qr_unidade)) {
            $unidade[$row_unidade['id_unidade']] = $row_unidade['id_unidade'] . " - " . $row_unidade['unidade'];
        }

$unidade1 = montaSelect($unidade, $unidade, "id='unidade1' name='unidade1' class='form-control validate[required,custom[select]]' data-for='contrato'");

function checkAba($aba1, $aba2, $fade = FALSE) {
    $return = ($aba1 == $aba2 && $fade) ? 'in ' : '';
    $return .= ($aba1 == $aba2) ? ' active' : '';
    return $return;
}

$breadcrumb_config = array("nivel" => "../", "key_btn" => "41", "area" => "Estoque", "ativo" => "Gestão de Pedidos", "id_form" => "form-pedido");

$pedido = new pedidosClass();
$objPedidosTipo = new PedidosTipoClass();
   

if(isset($_REQUEST['method']) && $_REQUEST['method'] == 'unidades'){
    $sqlUnidades = mysql_query("SELECT id_unidade, unidade FROM unidade WHERE campo1 = {$_REQUEST['id_projeto']} ORDER BY unidade");
    $arrayUnidades = array("" => "-- SELECIONE --");
//    echo "<select>";
    
    while ($rowUnidades = mysql_fetch_assoc($sqlUnidades)) {
//        echo '<option value="'.$rowUnidades['id_unidade'].'">'.$rowUnidades['id_unidade'] . " - " . utf8_encode($rowUnidades['unidade']).'</option>';
        $arrayUnidades[$rowUnidades['id_unidade']] = $rowUnidades['id_unidade'] . " - " . utf8_encode($rowUnidades['unidade']);
    }
//    echo "</select>";
    $auxDisabled = ($_REQUEST['id_unidade']) ? 'disabled' : null;
    echo montaSelect($arrayUnidades, $_REQUEST['id_unidade'], 'class="form-control"  name="unidades[]" $auxDisabled ');
    
    exit;
}

$qr_pedidos = mysql_query("SELECT * FROM pedidos_tipo WHERE status = 1");
$qr_item = mysql_query("SELECT * FROM nfe_produtos");

//$qr_item_pedido = mysql_query("SELECT *,C.nome as projeto_nome,D.unidade as unidade_nome
//FROM item_pedido AS A
//LEFT JOIN novo_pedido AS B ON (B.id_pedido = A.id_pedido)
//LEFT JOIN projeto AS C ON (A.id_projeto = C.id_projeto)
//LEFT JOIN unidade AS D ON (A.id_unidade = D.id_unidade)
//WHERE B.id_func = {$usuario['id_funcionario']}");

$qr_item_pedido = mysql_query("SELECT A.*,B.nome as nome_fornecedor,C.* FROM item_orcamento as A LEFT JOIN fornecedores as B ON (A.id_fornecedor = B.id_fornecedor) LEFT JOIN item_pedido as C ON (A.id_item_pedido = C.id_item)");


$qr_projeto = mysql_query("SELECT * FROM projeto WHERE id_regiao = {$usuario['id_regiao']}");
$qr_projeto1 = mysql_query("SELECT * FROM projeto WHERE id_regiao = {$usuario['id_regiao']}");
$qr_unidade = mysql_query("SELECT * FROM unidades WHERE id_projeto = ");

?>

<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet ::</title>

        <link rel="shortcut icon" href="../favicon.png">

        <!-- Bootstrap -->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/ui-autocomplete-theme.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" >
        <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../css/progress.css" rel="stylesheet" type="text/css">
        <link href="../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../resources/css/bootstrap-compras.css" rel="stylesheet" type="text/css">
        <link href="../resources/dropzone/dropzone.css" rel="stylesheet" media="screen">

    </head>
    <body>
        <?php include("../template/navbar_default.php"); ?>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header box-compras-header">
                        <h2><span class="fa fa-archive"></span> - FINANCEIRO <small>- Orçamentos de Compras</small></h2>
                    </div>
                    <div role="tabpanel">
                       

                        <!-- solicitação de do pedido -->
                        <div class="tab-content">
                            
                            <!-- solicitação de do pedido -->

                            <!-- confirmacao do pedido -->
                            <div  id="confirmapedidos">
                               
                                   
                                    <div id="confirmarPedido" class="loading">
                                        <legend>Orçamento de Compras</legend>
                                        <?php if (count($qr_item_pedido) > 0) { ?>
                                        
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
                                                        <th>Aprovar</th>
                                                    </tr>
                                                </thead>
                                                <tbody> 
                                                    <?php while ($value = mysql_fetch_assoc($qr_item_pedido))  {
                                                        
                                                            if($item_nome != $value['item']){ ?>
                                                    <tr><td colspan="8"><h4><?= $value['item'] ?></h4></td></tr>
                                                                <?php 
                                                                $item_nome = $value['item']; 
                                                            } ?>
                                                <?php if($value['flag'] == 1){ ?>
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
                                                                <a class="btn btn-success btn-sm aceitar_orcamento" data-key="<?= $value['id_orcamento'] ?>" id="aceitar_orcamento"><i class="fa fa-check"></i></a>
                                                                <a class="btn btn-danger btn-sm excluir_orcamento" data-key="<?= $value['id_orcamento'] ?>" id="excluir_orcamento"><i class="fa fa-close"></i></a>
                                                            </td>
                                                         
                                                        </tr>
                                                    <?php 
                                                    } }?>
                                                </tbody>
                                            </table>
                                        <?php }  ?>
                                    </div>
                            </div>   <!-- analise e confirmação do pedido -->

                            <!-- historico de pedidos -->
                        </div>
                    </div>
                </div>
            </div>
            <?php include_once '../template/footer.php'; ?>
        </div>
        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../js/jquery-ui-1.11.2.custom/jquery-ui.min.js"></script>
        <script src="../js/jquery.form.js"></script>
        <script src="../js/jquery.maskMoney_3.0.2.js"></script>
        <script src="../js/jquery.validationEngine-2.6.js"></script>
        <script src="../resources/dropzone/dropzone.js"></script>
        <script src="../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../js/global.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../resources/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
<!--        <script src="js/pedidos.js" type="text/javascript"></script>-->
        <script>
            $(function() {

             $(".aceitar_orcamento").click(function(){

                var id = $(this).data('key');
                     bootDialog(
                         'Deseja Aceitar o Orçamento?', 
                         'Orçamento', 
                         [{
                             label: 'Aceitar',
                             cssClass: 'btn-success',
                             action: function (dialog) {
                                 typeof dialog.getData('callback') === 'function' && dialog.getData('callback')(false);
                            /* Configura a requisição AJAX */
                                $.ajax({
                                    url : 'orcamento_financeiro.php?method=aceitar', /* URL que será chamada */ 
                                    type : 'POST', /* Tipo da requisição */ 
                                    data: 'orcamento_financeiro=' + id, /* dado que será enviado via POST */
                                    dataType: 'json', /* Tipo de transmissão */
                                    success: function(data){
                                        location.reload();
                                    }
                                });   
                                return false;    
                                
                             }
                         }, {
                             label: 'Sair',
                             cssClass: 'btn-danger' ,
                             action: function (dialog) {
                                 typeof dialog.getData('callback') === 'function' && dialog.getData('callback')(true);
                                 dialog.close();
                             }
                         }],
                         'success'
                 );
             });
             
             $(".excluir_orcamento").click(function(){
                    var id = $(this).data('key');
                     bootDialog(
                         'Deseja Excluir o Orçamento?', 
                         'Orçamento', 
                         [{
                             label: 'Excluir',
                             cssClass: 'btn-danger',
                             action: function (dialog) {
                                 typeof dialog.getData('callback') === 'function' && dialog.getData('callback')(false);
                                 $.ajax({
                                    url : 'orcamento_financeiro.php?method=excluir', /* URL que será chamada */ 
                                    type : 'POST', /* Tipo da requisição */ 
                                    data: 'orcamento_financeiro=' + id, /* dado que será enviado via POST */
                                    dataType: 'json', /* Tipo de transmissão */
                                    success: function(data){
                                        location.reload();
                                    }
                                });   
                                return false;  
                             }
                         }, {
                             label: 'Sair',
                             cssClass: 'btn-success',
                             action: function (dialog) {
                                 typeof dialog.getData('callback') === 'function' && dialog.getData('callback')(true);
                                 dialog.close();
                             }
                         }],
                         'danger'
                 );
             });

         });
        </script>
    </body>
</html>