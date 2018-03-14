<?php
if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="../../login.php">Logar</a>';
    exit;
}

include('../../conn.php');
include('../../funcoes.php');
include('../../wfunction.php');
include('../../classes/global.php');
include('../../classes/ComprasOs.php');

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"35", "area"=>"Compras e Contratos", "id_form"=>"form1", "ativo"=>"Solicitações");
//$breadcrumb_pages = array("Principal" => "index.php");

$arStatus = array("1" => "Aberto", "2" => "Aprovado");
$message = false;

$objComprasOs = new ComprasOs();

$rs = $objComprasOs->listSolicitacoesByUsuario($usuario);
$total = count($rs);

$tipos = $objComprasOs->getTipos();
$patrimonio = array("" => "« Selecione »", "BEM DE CONSUMO" => "BEM DE CONSUMO", "BEM DURÁVEL" => "BEM DURÁVEL");

if(isset($_REQUEST['method']) && $_REQUEST['method'] == "salvar"){
    
    $valores = array(
        $usuario['id_regiao'],
        $_REQUEST['id_projeto'],
        $usuario['id_funcionario'],
        date("Y-m-d H:i:s"),
        date("Y-m-d H:i:s"),
        $_REQUEST['tipo'],
        $_REQUEST['urgente'],
        utf8_decode($_REQUEST['nome']),
        utf8_decode($_REQUEST['descricao']),
        utf8_decode($_REQUEST['justificativa']),
        $_REQUEST['valor'],
        $_REQUEST['qnt'],
        1
    );
    
    $objComprasOs->solicita($valores);
    
    echo json_encode(array('status'=>1));
    exit;
}else if(isset($_REQUEST['method']) && $_REQUEST['method'] == "excluir"){
    $id = $_REQUEST['id'];
    $objComprasOs->exclui($id);
    
    echo json_encode(array('status'=>1));
    exit;
}else if(isset($_REQUEST['method']) && $_REQUEST['method'] == "ver"){
    $id = $_REQUEST['id'];
    $ompra = $objComprasOs->getSolicitacao($id);
    
    echo "<div>Teste</div>";
    exit;
}

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
        <link href="../../resources/css/jquery-ui-1.9.2.custom-teste.css" rel="stylesheet" media="all">
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" media="all">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
    </head>

    <body>
        <?php include("../../template/navbar_default.php"); ?>

        <div class="container">
            <div class="page-header box-compras-header"><h2><span class="glyphicon glyphicon-shopping-cart"></span> - COMPRAS E CONTRATOS <small> - Minhas solicitações</small></h2></div>

            <?php if ($message !== false) { ?>
                <div id='message-box' class='message-yellow'><p><?php echo $message ?></p></div>
            <?php } ?>


            <?php if ($total > 0) { ?>
                <!--p style="text-align: right;"><input type="button" onclick="tableToExcel('tabela', 'Solicitações de Compras')" value="Exportar para Excel" class="exportarExcel"></p-->
                <table class="table table-striped table-hover table-condensed table-bordered" id="tabela">
                    <thead>
                        <tr>
                            <th>Número</th>
                            <th>Data da Solicitação</th>
                            <th>Pedido</th>
                            <th>Status</th>
                            <th>Urgente?</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rs as $solicitacao) { ?>
                            <tr>
                                <td><?php echo $solicitacao['num_processo'] ?></td>
                                <td><?php echo $solicitacao['requisicao_emBR'] ?></td>
                                <td><?php echo $solicitacao['nome_produto'] ?></td>
                                <td><?php echo $arStatus[$solicitacao['acompanhamento']] ?></td>
                                <td><?php echo ($solicitacao['urgencia'] == 0) ? "não" : "sim" ?></td>
                                <td class="text-center">
                                    <!--button class="btn btn-xs btn-success btn-action" data-action="ver" data-key="<?php echo $solicitacao['id_compra'] ?>" title="Acompanhamento Detalhado" alt="Acompanhamento Detalhado"> <i class="fa fa-search"></i></button-->
                                    <!--button class="btn btn-xs btn-warning btn-action" data-action="edt" data-key="<?php echo $solicitacao['id_compra'] ?>" title="Editar" alt="Editar" > <i class="fa fa-pencil"></i></button-->
                                    <button class="btn btn-xs btn-danger  btn-action" data-action="exc" data-key="<?php echo $solicitacao['id_compra'] ?>" title="Cancelar Solicitação" alt="Cancelar Solicitação" > <i class="fa fa-trash"></i></button>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } else { ?>
                <div id='message-box' class='message-green'>
                    <p>Nenhuma solicitação encontrada</p>
                </div>
            <?php } ?>

            <button type="button" name="solicitar" id="solicitar" value="Nova Solicitação" class="btn btn-success pull-right nova-solicita" data-toggle="modal" data-target=".bs-example-modal-lg"> <i class="fa fa-plus"></i> Nova Solicitação </button>

            <form action="" method="post" name="form1" id="form1">
                <input type="hidden" name="id_solicitacao" id="id_solicitacao" value="" />
            </form>

            <?php include('../../template/footer.php'); ?>
        </div>
        
        <!-- Modal -->
        <div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Nova Solicitação de Compras</h4>
                    </div>
                    <div class="modal-body">
                        
                        <!-- FORM -->
                        <form action="" method="post" name="formSolicita" id="formSolicita" class="form-horizontal top-margin1" autocomplete="off">
                            <div class="panel panel-default">
                                <div class="panel-heading text-bold"> Dados da Solicitação </div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="form-group">
                                            <label for="mensagem" class="col-sm-2 control-label">Urgente</label>
                                            <div class="col-sm-4 text-left">
                                                <div class="radio radio-inline">
                                                    <label>                                                                    
                                                        <input type="radio" id="urgente[]" name="urgente" class="validate[required]" value="1"> Sim
                                                    </label>
                                                </div>
                                                <div class="radio radio-inline">
                                                    <label>
                                                        <input type="radio" id="urgente[]" name="urgente" class="validate[required]" value="0"> Não
                                                    </label>
                                                </div>
                                            </div>
                                            
                                            <label for="mensagem" class="col-sm-1 control-label">Tipo</label>
                                            <div class="col-sm-4 text-left">
                                                <?php echo montaSelect($tipos, $row['tipo'], "id='tipo' name='tipo' class='validate[required] form-control'") ?>
                                            </div>
                                            
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group">
                                            <label for="regiao" class="col-sm-2 control-label">Projeto</label>
                                            <div class="col-sm-9">
                                                <?php echo montaSelect(GlobalClass::carregaProjetosByRegiao($usuario['id_regiao']), null, "id='id_projeto' name='id_projeto' class='validate[required] form-control'") ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group">
                                            <label for="regiao" class="col-sm-2 control-label">Integração com Estoque</label>
                                            <div class="col-sm-9">
                                                <?php echo montaSelect($patrimonio, $row['patrimonio'], "id='patrimonio' name='patrimonio' class='validate[required] form-control'") ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group">
                                            <label for="banco" class="col-sm-2 control-label">Nome do Produto/Serviço</label>
                                            <div class="col-sm-9">
                                                <input name="nome" id="nome" class="validate[required] form-control" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group">
                                            <label for="banco" class="col-sm-2 control-label">Descrição</label>
                                            <div class="col-sm-9">
                                                <textarea rows="5" cols="45" name="descricao" id="descricao" class="validate[required] form-control"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group">
                                            <label for="banco" class="col-sm-2 control-label">Justificativa</label>
                                            <div class="col-sm-9">
                                                <textarea rows="5" cols="45" name="justificativa" id="justificativa" class="validate[required] form-control"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group">
                                            <label for="banco" class="col-sm-2 control-label">Valor médio</label>
                                            <div class="col-sm-3">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class="fa fa-dollar"></i></span>
                                                    <input type="text" class="input form-control" name="valor" id="valor" />
                                                </div>
                                            </div>
                                            <label for="banco" class="col-sm-2 control-label">Qauntidade</label>
                                            <div class="col-sm-4">
                                                <input type="text" class="input form-control" name="qnt" id="qnt" />
                                            </div>
                                        </div>
                                    </div>
                                    <!--div class="row">
                                        <div class="form-group">
                                            <label for="banco" class="col-sm-2 control-label">Destinatário</label>
                                            <div class="col-sm-5">
                                                <input type="text" class="input form-control" name="destinatario" id="destinatario" />
                                            </div>
                                        </div>
                                    </div-->
                                </div>
                            </div>
                        </form>
                        
                        <!-- FORM -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-success bt-save-solicitacao"><i class="fa fa-save"></i> Solicitar</button>
                    </div>
                </div>
            </div>
        </div>
                
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>

        <script src="../../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>

        <script src="../../js/global.js" type="text/javascript"></script>
        <script>
            $(function () {
                //$("#message-box").delay(4000).slideUp('slow');
                
                $(".modal").on('click', ".bt-save-solicitacao", function(){
                    var dados = $("#formSolicita").serialize();
                    $.post('solicitacoes.php?method=salvar&'+dados,null,function(re){
                        if(re.status === 1){
                            $('.modal').modal('hide');
                            location.reload();
                        }
                    },'json');
                });
                
                $("#tabela").on("click",".btn-action", function(){
                    var $this = $(this);
                    var acao = $this.data('action');
                    var id = $this.data('key');
                    
                    if(acao === "ver"){
                        
                    }else if(acao === "edt"){
                        $.post('solicitacoes.php',{method:"ver", id: id},function(html){
                            $("#body-modal-view").html(html);
                        },'html');
                    }else{
                        bootConfirm("Atenção, essa ação é irreversivel. Deseja continuar?","Excluir Solicitação", function(data){
                            if(data){
                                $.post('solicitacoes.php',{method:"excluir", id: id},function(re){
                                    if(re.status === 1){
                                        location.reload();
                                    }
                                },'json');
                            }
                        },"danger");
                    }
                    
                });
                
                /*
                $(".btimage").click(function () {
                    var $this = $(this);
                    var acao = $this.data("action");
                    var key = $this.data("key");
                    if (acao === "det") {
                        thickBoxIframe("Detalhe", "popup.detalhe.php", {method: "detalhe", id: key}, 680, 400);
                    } else if (acao === "alt") {
                        $("#id_solicitacao").val(key);
                        $("#form1").attr("action", "solicita.php");
                        $("#form1").submit();
                    }
                });*/
            });
        </script>
    </body>
</html>