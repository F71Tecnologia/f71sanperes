<?php
session_start();
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../login.php?entre=true';</script>";
}

include("../conn.php");
include("../wfunction.php");
include("../funcoes.php");
include("../classes/BotoesClass.php");
include("../classes/EntradaClass.php");
include("../classes/SaidaClass.php");
include("../classes/BancoClass.php");
include("../classes/global.php");
include("../classes/LogClass.php");

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$id_saida = (isset($_REQUEST['id_saida'])) ? $_REQUEST['id_saida'] : $_SESSION['saida'];

$log = new Log();

$entrada = new Entrada();
$saida = new Saida();
$global = new GlobalClass();
$banco = new Banco();

$regiao_selecionada = $usuario['id_regiao'];
$id_master = $usuario['id_master'];

$arraySaidaRh = array(171, 168, 167, 169, 156, 76, 51, 170);

if($id_saida != ""){
    $row_saida = $saida->getSaidaID($id_saida);
    $regiao_bd = $row_saida['id_regiao'];
    $projeto_bd = $row_saida['id_projeto'];
    $banco_bd = $row_saida['id_banco'];
    $subgrupo_bd = $row_saida['entradaesaida_subgrupo_id'];
    $row_grupo = $saida->getGrupoBd($subgrupo_bd);
    $grupo_bd = $row_grupo['entradaesaida_grupo'];
    $tipo_bd = $row_saida['tipo'];
    $prestador_bd = $row_saida['id_prestador'];
    $nome_bd = $row_saida['id_nome'];
}

//FORMATANDO PARA EXIBIR DOS CÓDIGOS DE BARRA
$cod_barra_consumo[] = substr($row_saida['cod_barra_consumo'], 0, 11);
$cod_barra_consumo[] = substr($row_saida['cod_barra_consumo'], 11, 1);
$cod_barra_consumo[] = substr($row_saida['cod_barra_consumo'], 12, 11);
$cod_barra_consumo[] = substr($row_saida['cod_barra_consumo'], 23, 1);
$cod_barra_consumo[] = substr($row_saida['cod_barra_consumo'], 24, 11);
$cod_barra_consumo[] = substr($row_saida['cod_barra_consumo'], 35, 1);
$cod_barra_consumo[] = substr($row_saida['cod_barra_consumo'], 36, 11);
$cod_barra_consumo[] = substr($row_saida['cod_barra_consumo'], 47, 1);

$cod_barra_gerais[] = substr($row_saida['cod_barra_gerais'], 0, 5);
$cod_barra_gerais[] = substr($row_saida['cod_barra_gerais'], 5, 5);
$cod_barra_gerais[] = substr($row_saida['cod_barra_gerais'], 10, 5);
$cod_barra_gerais[] = substr($row_saida['cod_barra_gerais'], 15, 6);
$cod_barra_gerais[] = substr($row_saida['cod_barra_gerais'], 21, 5);
$cod_barra_gerais[] = substr($row_saida['cod_barra_gerais'], 26, 6);
$cod_barra_gerais[] = substr($row_saida['cod_barra_gerais'], 32, 1);
$cod_barra_gerais[] = substr($row_saida['cod_barra_gerais'], 33, 14);

$mesR = (isset($row_saida['mes_competencia'])) ? $row_saida['mes_competencia'] : date('m');
$anoR = (isset($row_saida['ano_competencia'])) ? $row_saida['ano_competencia'] : date('Y');
$regiaoR = (isset($regiao_bd)) ? $regiao_bd : $regiao_selecionada;

//Saidas do Rh
if(isset($_REQUEST['atualizar_saida_rh']) && $_REQUEST['atualizar_saida_rh'] == "atualizar_saida_rh"){
    print_array($_REQUEST);
    echo $id_saida = $saida->editaSaidaRh(); 
    $log->gravaLog('Editar Saída', 'Edição Saída '.$id_saida); exit;
    exit;
}

//para desaparecer com alguns inputs, quando for edição
$some = false;

//trata insert/update
if($id_saida == ''){
    $acao = 'Cadastrar';
    $botao = 'Cadastrar';
    $projeto = montaSelect(array("-1" => "« Selecione a Região »"),$projetoR, "id='projeto' name='projeto' class='form-control validate[required,custom[select]]'");
    $projeto_prestador = montaSelect(array("-1" => "« Selecione a Região »"),$projeto_prestadorR, "id='projeto_prestador' name='projeto_prestador' class='form-control validate[required,custom[select]]'");
    $banco = montaSelect(array("-1" => "« Selecione o Projeto »"),$bancoR, "id='banco' name='banco' class='form-control validate[required,custom[select]]'");
    $prestador = montaSelect(array("-1" => "« Selecione o Projeto »"),$prestadorR, "id='prestador' name='prestador' class='form-control validate[required,custom[select]]'");
    $prestador_inativo = montaSelect(array("-1" => "« Selecione o Projeto »"),$prestadorR, "id='prestador_inativo' name='prestador_inativo' class='form-control validate[required,custom[select]]'");
    $prestador_outros = montaSelect(array("-1" => "« Selecione o Projeto »"),$prestadorR, "id='prestador_outros' name='prestador_outros' class='form-control validate[required,custom[select]]'");
    $nome = montaSelect(array("-1" => "« Selecione o Tipo »"),$nomeR, "id='nome' name='nome' class='form-control'");
    $tipo = montaSelect($entrada->getTipo(),null, "id='tipo' name='tipo' class='form-control validate[required,custom[select]]'");
    $referencia = montaSelect($saida->getReferencia(array("-1"=>"« Selecione »")), null, "id='referencia' name='referencia' class='form-control'");
    $bens = montaSelect($saida->getBens(array("-1"=>"« Selecione »")), null, "id='bens' name='bens' class='form-control'");
    $tipo_pg = montaSelect($saida->getTipoPg(array("-1"=>"« Selecione »")), null, "id='tipo_pg' name='tipo_pg' class='form-control'");
    $tipo_boleto = montaSelect($saida->getTipoBoleto(array("-1"=>"« Selecione »")), null, "id='tipo_boleto' name='tipo_boleto' class='form-control'");
}else{
    $acao = 'Editar';
    $botao = 'Atualizar';
    $projeto = montaSelect(array("-1" => "« Selecione a Região »"),$projeto_bd, "id='projeto' name='projeto' $readOnly class='form-control validate[required,custom[select]]'");
    $projeto_prestador = montaSelect(array("-1" => "« Selecione a Região »"),$projeto_prestadorR, "id='projeto_prestador' $readOnly name='projeto_prestador' class='form-control validate[required,custom[select]]'");
    $banco = montaSelect(array("-1" => "« Selecione o Projeto »"),$bancoR, "id='banco' name='banco' $readOnly class='form-control validate[required,custom[select]]'");
    $prestador = montaSelect(array("-1" => "« Selecione o Projeto »"),$prestadorR, "id='prestador' $readOnly name='prestador' class='form-control validate[required,custom[select]]'");
    $prestador_inativo = montaSelect(array("-1" => "« Selecione o Projeto »"),$prestadorR, "id='prestador_inativo' $readOnly name='prestador_inativo' class='form-control validate[required,custom[select]]'");
    $prestador_outros = montaSelect(array("-1" => "« Selecione o Projeto »"),$prestadorR, "id='prestador_outros' $readOnly name='prestador_outros' class='form-control validate[required,custom[select]]'");
    $nome = montaSelect(array("-1" => "« Selecione o Tipo »"),$nome_bd, "id='nome' name='nome' $readOnly class='form-control'");
    $tipo = montaSelect($entrada->getTipo(),null, "id='tipo' name='tipo' $readOnly class='form-control validate[required,custom[select]]'");
    $referencia = montaSelect($saida->getReferencia(array("-1"=>"« Selecione »")), $row_saida['id_referencia'], "id='referencia' $readOnly name='referencia' class='form-control'");
    $bens = montaSelect($saida->getBens(array("-1"=>"« Selecione »")), $row_saida['id_bens'], "id='bens' $readOnly name='bens' class='form-control'");
    $tipo_pg = montaSelect($saida->getTipoPg(array("-1"=>"« Selecione »")), $row_saida['id_tipo_pag_saida'], "id='tipo_pg' $readOnly name='tipo_pg' class='form-control'");
    $tipo_boleto = montaSelect($saida->getTipoBoleto(array("-1"=>"« Selecione »")), $row_saida['tipo_boleto'], "id='tipo_boleto' $readOnly name='tipo_boleto' class='form-control'");
    $some = true;
}

if(isset($_REQUEST['cad_nome'])){
    $saida->cadNome();
}

//verifica se prestador e ativo ou inativo
$verifica_prestador = mysql_query("SELECT status, encerrado_em
        FROM prestadorservico
        WHERE id_prestador = {$prestador_bd}");
$row_ver_prest = mysql_fetch_assoc($verifica_prestador);

$data_ver_prest = $row_ver_prest['encerrado_em'];
$status_ver_prest = $row_ver_prest['status'];

if($data_ver_prest >= date('Y-m-d')){
    $status_prestador = "ativo";
}elseif(($data_ver_prest < date('Y-m-d')) && ($data_ver_prest != "")){
    $status_prestador = "inativo";
}else{
    $status_prestador = "outros";
}

$breadcrumb_config = array("nivel"=>"../", "key_btn"=>"4", "area"=>"Financeiro", "id_form"=>"form1", "ativo"=>"$acao de Saída");
$breadcrumb_pages = array("Principal" => "index.php");
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: <?=$acao?> de Saída <?=$id_saida?></title>
        <link href="../favicon.png" rel="shortcut icon" />
        <!-- Bootstrap -->        
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css">
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../resources/css/bootstrap-dialog.min.css" rel="stylesheet" media="screen">
        <link href="../resources/dropzone/dropzone.css" rel="stylesheet" media="screen">
        <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
    </head>
    <body>
        <?php include("../template/navbar_default.php"); ?>
        <div class="container">            
            <div class="col-sm-12">
                <div class="page-header box-financeiro-header"><h2><span class="glyphicon glyphicon-usd"></span> - Financeiro<small> - <?=$acao?> de Saída <?=$id_saida?></small></h2></div>
                <!--resposta de algum metodo realizado-->
                <form action="" method="post" id="form1" class="form-horizontal top-margin1" enctype="multipart/form-data" autocomplete="off">
                    <div class="panel panel-default">
                        <div class="panel-heading text-bold">
                            <?php echo $acao; ?> Saída
                            <?php if (isset($id_saida)) { ?>
                            <p class="text-light-gray">
                                <?php echo $id_saida; ?> - <?php echo acentoMaiusculo($row_saida['nome']); ?>
                            </p>
                            <?php } ?>
                            <input type="hidden" name="hide_banco" id="hide_banco" value="<?php echo $bancoR; ?>" />
                            <input type="hidden" name="projeto_bd" id="projeto_bd" value="<?php echo $projeto_bd; ?>" />
                            <input type="hidden" name="banco_bd" id="banco_bd" value="<?php echo $banco_bd; ?>" />
                            <input type="hidden" name="subgrupo_bd" id="subgrupo_bd" value="<?php echo $subgrupo_bd; ?>" />
                            <input type="hidden" name="tipo_bd" id="tipo_bd" value="<?php echo $tipo_bd; ?>" />
                            <input type="hidden" name="prestador_bd" id="prestador_bd" value="<?php echo $prestador_bd; ?>" />
                            <input type="hidden" name="id_saida" id="id_saida" value="<?php echo $id_saida; ?>" />
                            <input type="hidden" name="status_prestador" id="status_prestador" value="<?php echo $status_prestador; ?>"/>
                            <input type="hidden" name="nome_pessoa" id="nome_pessoa" value="<?php echo $nome_bd; ?>"/>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="form-group">
                                    <label for="mensagem" class="col-sm-2 control-label">Descrição</label>
                                    <div class="col-sm-9">
                                        <textarea class="form-control" rows="5" id="descricao" name="descricao" <?=$readOnly?> ><?php echo $row_saida['especifica']; ?></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group">
                                    <label for="projeto_prestador" class="col-sm-2 control-label text-sm">Data para Pagamento</label>
                                    <div class="col-sm-3">
                                        <div class="input-group">
                                            <input type="text" class="form-control data" id="data_vencimento" name="data_vencimento" value="<?php echo implode('/', array_reverse(explode('-', $row_saida['data_vencimento']))); ?>" <?=$readOnly?> >
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr class="panel-wide">
                            <?php if (isset($id_saida) && $row_saida['status'] == 2) { ?>
                            <div class="row">
                                <div class="form-group">
                                    <label for="select" class="col-sm-2 control-label">Estorno</label>
                                    <div class="col-sm-9">                                                        
                                        <select name="estorno" id="estorno" class="form-control">
                                            <option value="-1">« Selecione »</option>
                                            <option value="1" <?php echo ($row_saida['estorno'] == 1) ? 'selected="selected"' : ''; ?>>INTEGRAL</option>
                                            <option value="2" <?php echo ($row_saida['estorno'] == 2) ? 'selected="selected"' : ''; ?>>PARCIAL</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row" id="intera_valest">
                                <div class="form-group">
                                    <label for="select" class="col-sm-2 control-label">Valor do Estorno</label>   
                                    <div class="col-sm-9">
                                        <div class="input-group">
                                            <input name="valor_estorno_parcial" type="text" id="valor_estorno_parcial" class="form-control" value="<?php echo formataMoeda($row_saida['valor_estorno_parcial'], 1); ?>" />
                                            <span class="input-group-addon"><i class="fa fa-dollar"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row" id="intera_descest">
                                <div class="form-group">
                                    <label for="mensagem" class="col-sm-2 control-label">Descrição do estorno</label>
                                    <div class="col-sm-9">
                                        <textarea class="form-control" rows="5" id="descricao_estorno" name="descricao_estorno"><?php echo trim($row_saida['estorno_obs']); ?></textarea>
                                    </div>
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                        <!--<div class="panel-heading text-bold border-t">Anexo</div>-->
                        <div class="panel-body">
                            <?php 
                            if(!empty($id_saida)){
                                $dadosSaidaFile = $saida->getSaidaFile($id_saida);
                                while($row_files = mysql_fetch_assoc($dadosSaidaFile)){
                                    if(file_exists("../comprovantes/$row_files[id_saida_file].$id_saida$row_files[tipo_saida_file]")){ ?>
                                    <div class="col-xs-2 margin_b5 <?=$row_files['id_saida_file']?>">
                                        <div class="thumbnail">
                                            <a href="../comprovantes/<?=$row_files['id_saida_file']?>.<?=$id_saida.$row_files['tipo_saida_file']?>" target="_blank">
                                                <img class="h-100" src="../imagens/icons/att-<?=str_replace('.', '', $row_files['tipo_saida_file'])?>.png">
                                            </a>
                                            <span class="btn btn-sm btn-danger fa fa-trash-o margin_t5 deleteAnexoSaida" style="width: 100%;" data-key="<?=$row_files['id_saida_file']?>"> Deletar</span>
                                        </div>
                                    </div>
                                    <?php } else { 
                                        $rescisao = $saida->verificaSaidaRescisao($row_saida['id_saida']); ?>
                                    <div class="col-xs-2 margin_b5 <?=$row_files[id_saida_file]?>">
                                        <div class="thumbnail">
                                            <a href="/intranet/rh/recisao/<?=$rescisao?>" target="_blank">
                                                <img class="h-100" src="../imagens/icons/att-<?=str_replace('.', '', $row_files['tipo_saida_file'])?>.png">
                                            </a>
                                            <span class="btn btn-sm btn-danger fa fa-trash-o margin_t5 deleteAnexoSaida" style="width: 100%;" data-key="<?=$row_files['id_saida_file']?>"> Deletar</span>
                                        </div>
                                    </div>
                                    <?php } ?>
                                <?php } 
                                $dadosSaidaPg = $saida->getSaidaFilePg($id_saida);
                                while($row_files = mysql_fetch_assoc($dadosSaidaPg)){
                                    if(file_exists("../comprovantes/{$row_files['id_pg']}.{$id_saida}_pg{$row_files['tipo_pg']}")){ ?>
                                    <div class="col-xs-2 margin_b5 <?=$row_files['id_pg']?>">
                                        <div class="thumbnail tr-bg-success">
                                            <a href="../comprovantes/<?=$row_files['id_pg']?>.<?=$id_saida?>_pg<?=$row_files['tipo_pg']?>" target="_blank">
                                                <img class="h-100" src="../imagens/icons/att-<?=str_replace('.', '', $row_files['tipo_pg'])?>.png">
                                            </a>
                                            <span class="btn btn-sm btn-danger fa fa-trash-o margin_t5 deleteComprovanteSaida" style="width: 100%;" data-key="<?=$row_files['id_pg']?>"> Deletar</span>
                                        </div>
                                    </div>
                                    <?php } else { ?>
                                    <div class="col-xs-2 margin_b5 <?=$row_files[id_pg]?>">
                                        <div class="thumbnail tr-bg-success">
                                            <a href="comprovantes/saida/<?=$row_files['id_pg']?>.<?=$id_saida?>_pg<?=$row_files['tipo_pg']?>" target="_blank">
                                                <img class="h-100" src="../imagens/icons/att-<?=str_replace('.', '', $row_files['tipo_pg'])?>.png">
                                            </a>
                                            <span class="btn btn-sm btn-danger fa fa-trash-o margin_t5 deleteComprovanteSaida" style="width: 100%;" data-key="<?=$row_files['id_pg']?>"> Deletar</span>
                                        </div>
                                    </div>
                                    <?php } ?>
                                <?php } ?>
                            <?php } ?>
                            <div class="clear"></div>
                            <div class="<?=($row_saida['status'] == 2)? 'col-sm-6':'col-sm-12';?>">
                                <h4>Anexos:</h4>
                                <div id="dropzoneAnexo" class="dropzone"></div>
                            </div>
                            
                            <div class="<?=($row_saida['status'] == 2)? 'col-sm-6':'col-sm-12';?> <?=($row_saida['status'] == 2) ? null : 'hide' ?>">
                                <h4>Comprovante de Pagamento:</h4>
                                <div id="dropzoneComprovante" class="dropzone"></div>
                            </div>
                            <div class="clear"></div>
                        </div>
                        <div class="panel-footer text-right">
                            <input type="button" class="btn btn-primary botao_atualizar_saida_rh" value="Atualizar" />
                            <input type="hidden" name="atualizar_saida_rh" id="atualizar_saida_rh" value="atualizar_saida_rh" />
                        </div>
                    </div>
                </form>
            </div>
            
            <div class="clear"></div>
            
            <?php include("../template/footer.php"); ?>
        </div>
        
        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../resources/js/tooltip.js"></script>
        <script src="../resources/dropzone/dropzone.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/global.js"></script>
        <script src="../js/jquery.maskedinput-1.3.1.js"></script>
        <script src="../resources/js/financeiro/saida.js"></script>
        <script src="../js/jquery.validationEngine-2.6.js"></script>
        <script src="../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../js/jquery.maskMoney.js"></script>
        <script>
            $(function() {          
                
                var id_saida = $("#id_saida").val();
                
                $("#regiao").ajaxGetJson("../methods.php", {method: "carregaProjetos"}, function(){
                    var projeto_bd = $("#projeto_bd").val();
                    
                    if(projeto_bd != ''){
                        $("#projeto").val(projeto_bd);
                        $("#projeto").change();                        
                    }
                }, "projeto");
                
                $("#projeto").ajaxGetJson("../methods.php", {method: "carregaBancos"}, function(){
                    var banco = $("#banco_bd").val();
                    
                    if(banco != ''){
                        $("#banco").val(banco);
                    }
                }, "banco");
                
                $("#select_grupo").ajaxGetJson("actions/action.saida.php", {action: "load_subgrupo", opt: "« Selecione »"}, function(){                    
                    $("#projeto_prestador").change();
                    
                    var subgrupo = $("#subgrupo_bd").val();
                    
                    if(subgrupo != ''){
                        $("#subgrupo").val(subgrupo);
                        $("#subgrupo").change();
                    }
                }, "subgrupo");
                
                $("#subgrupo").ajaxGetJson("actions/action.saida.php", {action: "load_tipo", opt: "« Selecione »"}, function(){
                    var tipo = $("#tipo_bd").val();
                    
                    if(tipo != ''){
                        $("#tipo").val(tipo);
                        $("#tipo").change();                        
                    }
                }, "tipo");
                
                $("#regiao_prestador").ajaxGetJson("../methods.php", {method: "carregaProjetos", request: "regiao_prestador"}, function(){
                    var projeto = $("#projeto").val();
                    $("#projeto_prestador").val(projeto);
                    $("#projeto_prestador").change();
                }, "projeto_prestador");
                
                $("#projeto_prestador").ajaxGetJson("../methods.php", {method: "carregaPrestadores", request: "projeto_prestador"}, function(){
                    if(id_saida != ''){
                        $("#prestador").val($("#prestador_bd").val());
                    }
                }, "prestador");
                
                $("#projeto_prestador").ajaxGetJson("../methods.php", {method: "carregaPrestadoresInativos", request: "projeto_prestador"}, function(){
                    if(id_saida != ''){
                        $("#prestador_inativo").val($("#prestador_bd").val());
                    }
                }, "prestador_inativo");
                
                $("#projeto_prestador").ajaxGetJson("../methods.php", {method: "carregaPrestadoresOutros", request: "projeto_prestador"}, function(){
                    if(id_saida != ''){
                        $("#prestador_outros").val($("#prestador_bd").val());
                    }
                }, "prestador_outros");
                
                $("#regiao_prestador").ajaxGetJson("../methods.php", {method: "carregaFornecedores", request: "regiao_prestador"}, null, "fornecedor");
                
                $("#tipo").ajaxGetJson("../methods.php", {method: "carregaNomes"}, function(){
                    if(id_saida != ''){
                        $("#nome").val($("#nome_pessoa").val());
                    }
                }, "nome");
                
                $("#dt_emissao_nf, #data_vencimento").mask("99/99/9999");
                $("#valor_liquido, #valor_bruto, #valor_estorno_parcial, #adicional").maskMoney({prefix:'R$ ', allowNegative: true, thousands:'.', decimal:','});
                
                $("#form1").validationEngine({promptPosition : "topRight"});
                
                //datepicker
                $('.data').datepicker({
                    dateFormat: 'dd/mm/yy',
                    changeMonth: true,
                    changeYear: true,
                    yearRange: '2005:c+1'
                });
                
                Dropzone.autoDiscover = false;
                <?php if(empty($id_saida)) { ?>
                var myDropzoneAnexo = new Dropzone("#dropzoneAnexo",{
                    url: "actions/action.saida.php?tipo_anexo=1",
                    addRemoveLinks : true,
                    maxFilesize: 30,
                    //envio automatico
                    autoQueue: false,
                    dictResponseError: "Erro no servidor!",
                    dictCancelUpload: "Cancelar",
                    dictFileTooBig: "Tamanho máximo: 30MB",
                    dictRemoveFile: "Remover Arquivo",
                    canceled: "Arquivo Cancelado",
                    acceptedFiles: '.jpg,.gif,.png,.pdf,.JPG,.GIF,.PNG,.PDF'
                    , totaluploadprogress: function(p){
                        if(p >= 100) {
                            bootDialog(
                                'Saída Cadastrada Com Sucesso!', 
                                'Saída Cadastrada!', 
                                [{
                                    label: 'Fechar',
                                    action: function(){
                                        window.location.href = "../finan";
                                    }
                                }], 
                                'success'
                            );
                        }
                    }
                });
                <?php } else if(!empty($id_saida)) { ?>
                var myDropzoneAnexo = new Dropzone("#dropzoneAnexo",{
                    url: "actions/action.saida.php?tipo_anexo=1&id_saida=<?=$id_saida?>&action=upload_anexo",
                    maxFilesize: 30,
                    dictResponseError: "Erro no servidor!",
                    dictCancelUpload: "Cancelar",
                    dictFileTooBig: "Tamanho máximo: 30MB",
                    dictRemoveFile: "Remover Arquivo",
                    canceled: "Arquivo Cancelado",
                    acceptedFiles: '.jpg,.gif,.png,.pdf,.JPG,.GIF,.PNG,.PDF'
                });
                
                var myDropzoneComprovante = new Dropzone("#dropzoneComprovante",{
                    url: "actions/action.saida.php?tipo_anexo=2&id_saida=<?=$id_saida?>&action=upload_anexo",
                    maxFilesize: 30,
                    dictResponseError: "Erro no servidor!",
                    dictCancelUpload: "Cancelar",
                    dictFileTooBig: "Tamanho máximo: 30MB",
                    dictRemoveFile: "Remover Arquivo",
                    canceled: "Arquivo Cancelado",
                    acceptedFiles: '.jpg,.gif,.png,.pdf,.JPG,.GIF,.PNG,.PDF'
                });
                <?php } ?>
                
                $(".botao_atualizar_saida_rh").on('click', function(){
                    $.post("form_saida_rh.php", {
                        id_saida: $('#id_saida').val(),
                        data_vencimento: $('#data_vencimento').val(),
                        descricao: $('#descricao').val(),
                        estorno: $('#estorno').val(),
                        valor_estorno_parcial: $('#valor_estorno_parcial').val(),
                        descricao_estorno: $('#descricao_estorno').val(),
                        atualizar_saida_rh: 'atualizar_saida_rh'
                    }, function(resposta){
                        console.log(resposta);
                        bootDialog(
                            'Saída Cadastrada Com Sucesso!', 
                            'Saída Cadastrada!', 
                            [{
                                label: 'Fechar',
                                action: function(){
                                    window.location.href = "../finan";
                                }
                            }], 
                            'success'
                        );
                    });
                });
                
                $("body").on('click', ".deleteAnexoSaida", function(){
                    var idFileSaida = $(this).data("key");
                    bootConfirm("Deseja Excluir este Comprovante?","Excluir Comprovante", function(data){
                        if(data == true){
                            $.post("actions/action.saida.php", {bugger:Math.random(), id:idFileSaida, action:'deleteAnexoSaida'}, function(resultado){
                                cria_carregando_modal();
                                bootDialog(
                                    resultado, 
                                    'Exclusão de Anexo', 
                                    [{
                                        label: 'Fechar',
                                        action: function (dialog) {
                                            $('.'+idFileSaida).remove();
                                            dialog.close();
                                        }
                                    }],
                                    'info'
                                );
                                if(resultado){
                                    remove_carregando_modal();
                                }
                            });
                        }
                    },"warning");
                }); 
                
                $("body").on('click', ".deleteComprovanteSaida", function(){
                    var idFileSaida = $(this).data("key");
                    bootConfirm("Deseja Excluir este Comprovante?","Excluir Comprovante", function(data){
                        if(data == true){
                            $.post("actions/action.saida.php", {bugger:Math.random(), id:idFileSaida, action:'deleteComprovanteSaida'}, function(resultado){
                                cria_carregando_modal();
                                bootDialog(
                                    resultado, 
                                    'Exclusão de Comprovante', 
                                    [{
                                        label: 'Fechar',
                                        action: function (dialog) {
                                            $('.'+idFileSaida).remove();
                                            dialog.close();
                                        }
                                    }],
                                    'info'
                                );
                                if(resultado){
                                    remove_carregando_modal();
                                }
                            });
                        }
                    },"warning");
                }); 
            });
        </script>
    </body>
</html>