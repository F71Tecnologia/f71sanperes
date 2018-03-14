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
include("../classes/PrestadorServicoClass.php");
include("../classes/ViagemClass.php");

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$id_saida = (isset($_REQUEST['id_saida'])) ? $_REQUEST['id_saida'] : $_SESSION['saida'];

$log = new Log();

$entrada = new Entrada();
$saida = new Saida();
$global = new GlobalClass();
$banco = new Banco();
$objViagem = new ViagemClass();

$regiao_selecionada = $usuario['id_regiao'];
$id_master = $usuario['id_master'];

$arraySaidaRh = array(/*171, 168, 167, 169, 156, 76, 51, 170*/);

if($id_saida != ""){
    $row_saida = $saida->getSaidaID($id_saida);
    $regiao_bd = $row_saida['id_regiao'];
    $projeto_bd = $row_saida['id_projeto'];
    $banco_bd = $row_saida['id_banco'];
//    $subgrupo_bd = $row_saida['subgrupo'];
    $row_grupo = $saida->getGrupoBd($row_saida['subgrupo']);
    $subgrupo_bd = $row_grupo['id'];
//    $row_grupo = $saida->getGrupoBd($subgrupo_bd);
    $grupo_bd = $row_grupo['entradaesaida_grupo'];
    $tipo_bd = $row_saida['tipo'];
    $prestador_bd = $row_saida['id_prestador'];
    $nome_bd = ($row_saida['id_clt']) ? $row_saida['id_clt'] : $row_saida['id_nome'];
    if (in_array($row_saida['tipo'], $arraySaidaRh)) {
        $readOnly = " READONLY ";
    }
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
//    echo $id_saida = $saida->cadSaida(); 
//    $log->gravaLog('Cadastrar Saída', 'Cadastro Saída '.$id_saida); 
    exit;
}

//insert
if(isset($_REQUEST['cadastrar']) && $_REQUEST['cadastrar'] == "Cadastrar"){
     echo $id_saida = $saida->cadSaida(); 
    $log->gravaLog('Cadastrar Saída', 'Cadastro Saída '.$id_saida); exit;
}

//update
if(isset($_REQUEST['atualizar']) && $_REQUEST['atualizar'] == "Atualizar"){
    echo $id_saida = $saida->editSaida(); 
    $log->gravaLog('Editar Saída', 'Edição Saída '.$id_saida); exit;
}

//para desaparecer com alguns inputs, quando for edição
$some = false;
$sqlTipoPG = mysql_query("SELECT bloqueio FROM tipos_pag_saida");
$rowTipoPG = mysql_fetch_assoc($sqlTipoPG);

$sqlB = "SELECT * FROM bancos";
$qryB = mysql_query($sqlB);
while ($rowB = mysql_fetch_assoc($qryB)) {
    $arrayBancos[$rowB['id_banco']] = $rowB['id_banco'] . ' - ' . $rowB['nome'];
    //$arrIdBanco[$rowB['id_projeto']] = $rowB['id_banco']; // tava dando merda, melhor comentar
}

//trata insert/update
if($id_saida == ''){
    $acao = 'Cadastrar';
    $botao = 'Cadastrar';
    $projeto = montaSelect(array("-1" => "« Selecione a Região »"),$projetoR, "id='projeto' name='projeto' class='form-control validate[required,custom[select]]'");
    $projeto_prestador = montaSelect(array("-1" => "« Selecione a Região »"),$projeto_prestadorR, "id='projeto_prestador' name='projeto_prestador' class='form-control validate[required,custom[select]]'");
    $banco = montaSelect($arrayBancos,$row_saida['id_banco'], "id='banco' name='banco' class='form-control validate[required,custom[select]]'");
    $prestador = montaSelect(array("-1" => "« Selecione o Projeto »"),$prestadorR, "id='prestador' name='prestador' class='form-control validate[required,custom[select]]'");
    $prestador_inativo = montaSelect(array("-1" => "« Selecione o Projeto »"),$prestadorR, "id='prestador_inativo' name='prestador_inativo' class='form-control validate[required,custom[select]]'");
//    $prestador_outros = montaSelect(array("-1" => "« Selecione o Projeto »"),$prestadorR, "id='prestador_outros' name='prestador_outros' class='form-control validate[required,custom[select]]'");
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
    $banco = montaSelect($arrayBancos,$row_saida['id_banco'], "id='banco' name='banco' $readOnly class='form-control validate[required,custom[select]]'");
    $prestador = montaSelect(array("-1" => "« Selecione o Projeto »"),$prestadorR, "id='prestador' $readOnly name='prestador' class='form-control validate[required,custom[select]]'");
    $prestador_inativo = montaSelect(array("-1" => "« Selecione o Projeto »"),$prestadorR, "id='prestador_inativo' $readOnly name='prestador_inativo' class='form-control validate[required,custom[select]]'");
//    $prestador_outros = montaSelect(array("-1" => "« Selecione o Projeto »"),$prestadorR, "id='prestador_outros' $readOnly name='prestador_outros' class='form-control validate[required,custom[select]]'");
    $nome = montaSelect(array("-1" => "« Selecione o Tipo »"),$nome_bd, "id='nome' name='nome' $readOnly class='form-control'");
    $tipo = montaSelect($entrada->getTipo(),null, "id='tipo' name='tipo' $readOnly class='form-control validate[required,custom[select]]'");
    $referencia = montaSelect($saida->getReferencia(array("-1"=>"« Selecione »")), $row_saida['id_referencia'], "id='referencia' $readOnly name='referencia' class='form-control'");
    $bens = montaSelect($saida->getBens(array("-1"=>"« Selecione »")), $row_saida['id_bens'], "id='bens' $readOnly name='bens' class='form-control'");
    $tipo_pg = montaSelect($saida->getTipoPg(array("-1"=>"« Selecione »")), $row_saida['id_tipo_pag_saida'], "id='tipo_pg' $readOnly name='tipo_pg' class='form-control'");
    $tipo_boleto = montaSelect($saida->getTipoBoleto(array("-1"=>"« Selecione »")), $row_saida['tipo_boleto'], "id='tipo_boleto' $readOnly name='tipo_boleto' class='form-control'");
    $some = true;
    
    if($row_saida['valor_bruto'] == 0){ 
        $row_saida['valor_bruto'] = str_replace(",", ".", $row_saida['valor']) - $row_saida['valor_multa'] - $row_saida['valor_juros'] - $row_saida['taxa_expediente'] - $row_saida['valor_ir'] + $row_saida['desconto'];
    }
}

if(isset($_REQUEST['cad_nome'])){
    echo $saida->cadNome();
    exit;
}

if(isset($_REQUEST['cad_prestador'])){
    $objPrestador = new PrestadorServico();
    echo $objPrestador->cadastraPrestadorBasico($_REQUEST);
    exit;
}

//verifica se prestador e ativo ou inativo
$query_prestador_ativo = "SELECT status, encerrado_em
        FROM prestadorservico
        WHERE id_prestador = '{$prestador_bd}'";
$verifica_prestador = mysql_query($query_prestador_ativo);
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

//VERIFICA SE É UMA SAÍDA AGRUPADA, LISTA AS SAÍDAS (SÓ INFORMATIVO)
if($id_saida != "" && $row_saida['agrupada'] == 2){
    
    $resultSaidasAgrupadas = $saida->getSaidasAgrupadas($id_saida);
    
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
                            <!--<input type="hidden" name="nome" id="nome" value="<?php echo $row_saida['nome']; ?>"/>-->
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="form-group">
                                    <label for="regiao" class="col-sm-2 control-label">Região</label>
                                    <div class="col-sm-4">
                                        <?php echo montaSelect($global->carregaRegioes($usuario['id_master']), $regiaoR, "id='regiao' name='regiao' $readOnly class='validate[required,custom[select]] form-control'"); ?>
                                    </div>
                                    <label for="projeto" class="col-sm-1 control-label">Projeto</label>
                                    <div class="col-sm-4">
                                        <?php echo $projeto; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group">
                                    <label for="banco" class="col-sm-2 control-label">Conta para débito</label>
                                    <div class="col-sm-4">
                                        <?php echo $banco; ?>
                                    </div>
                                    <label for="grupo" class="col-sm-1 control-label">Grupo</label>
                                    <div class="col-sm-4">
                                        <?php 
//                                                        if ($id_master == 1 or $id_master['id_master'] == 4) {
//                                                            echo montaSelect(array('-1' => '« Selecione »', '1' => 'Folha', '2' => 'Reserva', '3' => 'Taxa administrativa', '4' => 'Tranferências ISPV', '10' => 'PESSOAL', '20' => 'MATERIAL DE CONSUMO', '30' => 'SERVIÇOS DE TERCEIROS', '40' => 'TAXAS / IMPOSTOS / CONTRIBUIÇÕES', '50' => 'SERVIÇOS PÚBLICOS', '60' => 'DESPESAS BANCÁRIAS', '70' => 'OUTRAS DESPESAS OPERACIONAIS', '80' => 'INVESTIMENTOS'), $grupoR, " name='grupo' id='select_grupo' class='form-control'");
//                                                        } else { 
//                                            echo montaSelect($saida->getGrupo(array('-1' => '« Selecione »')), $grupo_bd, " name='grupo' id='select_grupo' $readOnly class='form-control validate[required,custom[select]]'"); 
//                                                        }
                                        $sqlGrupo = mysql_query("SELECT * FROM entradaesaida_grupo WHERE id_grupo >= 10");
                                            echo "<select name='grupo' id='select_grupo' $readOnly class='form-control validate[required,custom[select]]'>";
                                            while($rowGrupo = mysql_fetch_assoc($sqlGrupo)) {
                                                $selectedGrupo = ($grupo_bd == $rowGrupo['id_grupo']) ? 'SELECTED' : null;
                                                echo "<option value='{$rowGrupo['id_grupo']}' $selectedGrupo data-terceiro='{$rowGrupo['terceiro']}'>{$rowGrupo['id_grupo']} - {$rowGrupo['nome_grupo']}</option>";
                                            }
                                            echo "</select>";
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div id="intera_subgrupo">
                                <div class="row sub_hide">
                                    <div class="form-group">
                                        <label for="subgrupo" class="col-sm-2 control-label">Subgrupo</label>
                                        <div class="col-sm-9">
                                            <?php echo montaSelect(array('-1' => '« Selecione o Grupo »'), null, " name='subgrupo' $readOnly id='subgrupo' class='form-control validate[required,custom[select]]'"); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group">
                                        <label for="tipo" class="col-sm-2 control-label">Conta</label>
                                        <div class="col-sm-9">
                                            <?php echo montaSelect(array('-1' => '« Selecione o Subgrupo »'), $tipoR, " name='tipo' $readOnly id='tipo' class='form-control validate[required,custom[select]]''"); ?>
                                        </div>
                                    </div>
                                </div>
<!--                                <div class="row">
                                    <div class="form-group">
                                        <label for="tipo" class="col-sm-2 control-label">&nbsp;</label>
                                        <div class="col-sm-9 no-padding">
                                            <div class="col-sm-2">
                                                <div class="input-group">
                                                    <div class="input-group-addon">
                                                        <input type="checkbox" class="" name="caixinha" id="caixinha" value="1" <?= ($row_saida['caixinha'] == 1) ? 'checked' : null ?>>
                                                    </div>
                                                    <label class="form-control text-default" for="caixinha">Caixinha</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>-->
                            <div id="intera_prestador">
                                <hr class="panel-wide">
                                <h6 class="text-light-gray text-semibold text-xs">PRESTADOR DE SERVIÇO</h6>
                                <div class="row">
                                    <div class="form-group">
                                        <label for="regiao_prestador" class="col-sm-2 control-label">Região</label>
                                        <div class="col-sm-4">
                                            <?php echo $global->regioesMaster("<option value='-1'>« Selecione »</option>","name='regiao_prestador' $readOnly id='regiao_prestador' class='form-control'"); ?>                                                        
                                        </div>
                                        <label for="projeto_prestador" class="col-sm-1 control-label">Projeto</label>
                                        <div class="col-sm-4">
                                            <?php echo $projeto_prestador; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group">
                                        <label for="mensagem" class="col-sm-2 control-label">Tipo de Prestador</label>
                                        <div class="col-sm-9 text-left">
                                            <div class="radio radio-inline">
                                                <label>                                                                    
                                                    <input type="radio" id="tipo_empresa1" name="tipo_empresa" class="validate[required]" value="1" 
                                                    <?php if($row_saida['tipo_empresa'] == '1'){ echo "checked"; } ?> /> Ativo
                                                </label>
                                            </div>
                                            <div class="radio radio-inline">
                                                <label>
                                                    <input type="radio" id="tipo_empresa2" name="tipo_empresa" class="validate[required]" value="2"
                                                    <?php if($row_saida['tipo_empresa'] == '2'){ echo "checked"; } ?> /> Inativo
                                                </label>
                                            </div>
<!--                                            <div class="radio radio-inline">
                                                <label>
                                                    <input type="radio" id="tipo_empresa3" name="tipo_empresa" class="validate[required]" value="3"
                                                    <?php if($row_saida['tipo_empresa'] == '3'){ echo "checked"; } ?> /> Outros
                                                </label>
                                            </div>-->
                                        </div>
                                    </div>
                                </div>
                                <div class="row" id="nome_prestador">
                                    <div class="form-group">
                                        <label for="mensagem" class="col-sm-2 control-label">Prestador Ativo</label>
                                        <div class="col-sm-9">
                                            <?php echo $prestador; ?>
                                        </div>                                                        
                                    </div>
                                </div>
                                <div class="row" id="nome_fornecedor">
                                    <div class="form-group">
                                        <label for="mensagem" class="col-sm-2 control-label">Prestador Inativo</label>
                                        <div class="col-sm-9">
                                            <?php echo $prestador_inativo; ?>
                                        </div>
                                    </div>
                                </div>
<!--                                <div class="row" id="nome_outros">
                                    <div class="form-group">
                                        <label for="mensagem" class="col-sm-2 control-label">Outros Prestadores</label>
                                        <div class="col-sm-9">
                                            <?php echo $prestador_outros; ?>
                                        </div>
                                    </div>
                                </div>-->
                                <?php if($_COOKIE['logado'] == "158"){?>
                                <div class="row">
                                    <div class="form-group">
                                        <label for="mensagem" class="col-sm-2 control-label">Adicionar Prestador</label>
                                        <div class="col-sm-9">
                                            <button type="button" name="adicionar" id="add_prestador" class="btn btn-sm btn-success"> <i class="fa fa-truck"></i> Adicionar Prestador</button>
                                        </div>
                                    </div>
                                </div>
                                <?php } ?>
                                <hr class="panel-wide">
                            </div>
                            <div class="row" id="intera_nome">
                                <!--aqui ze-->
                                <div class="form-group">
                                    
                                    <div class="col-sm-offset-2 col-sm-9">
                                        <select name="tipo_nome" id="tipo_nome" class="form-control">
                                            <option value="">SELECIONE</option>
                                            <option value="clt" <?=(!empty($row_saida['id_clt'])) ? ' SELECTED ' : null?> >CLT</option>
                                            <option value="autonomo" <?=(!empty($row_saida['id_autonomo'])) ? ' SELECTED ' : null?> >Autonomo</option>
                                            <option value="cooperado" <?=(!empty($row_saida['id_coop'])) ? ' SELECTED ' : null?> >Cooperado</option>
                                            <option value="pj" <?=(!empty($row_saida['id_pj'])) ? ' SELECTED ' : null?> >PJ</option>
                                            <option value="outro" <?=(!empty($row_saida['id_nome']) && empty($row_saida['id_pj']) && empty($row_saida['id_coop']) && empty($row_saida['id_autonomo']) && empty($row_saida['id_clt'])) ? ' SELECTED ' : null?> >Outro</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="mensagem" class="col-sm-2 control-label">Nome</label>
                                    <div class="col-sm-8">
                                        <?php echo $nome; ?>
                                    </div>
                                    <div class="col-sm-2">
                                        <a href="javascript:;" id="add_nome">Adicionar</a>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group">
                                    <label for="mensagem" class="col-sm-2 control-label">Descrição</label>
                                    <div class="col-sm-9">
                                        <textarea class="form-control" rows="5" id="descricao" name="descricao" <?=$readOnly?> ><?php echo $row_saida['especifica']; ?></textarea>
                                    </div>
                                </div>
                            </div>
                            <?php if (isset($id_saida)) { ?>      
                            <div class="row">
                                <div class="form-group">
                                    <label for="mensagem" class="col-sm-2 control-label">Valor Adicional</label>
                                    <div class="col-sm-9">
                                        R$ <?php echo number_format($row_saida['adicional'], 2, ',', '.') ?>
                                    </div>
                                </div>
                            </div>
                            <?php }else{ ?>
                            <div class="row">
                                <div class="form-group">
                                    <label for="mensagem" class="col-sm-2 control-label">Valor Adicional</label>
                                    <div class="col-sm-9">
                                        <input name="adicional" type="hidden" id="adicional" value="<?php echo $row_saida['adicional'] ?>" <?=$readOnly?> />
                                    </div>
                                </div>
                            </div>
                            <?php } ?>
                            <div class="row">
                                <div class="form-group">
                                    <label for="mensagem" class="col-sm-2 control-label">Referência</label>
                                    <div class="col-sm-9">
                                        <?php echo $referencia; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row" id="intera_bens">
                                <div class="form-group">
                                    <label for="mensagem" class="col-sm-2 control-label">Tipos de Bens</label>
                                    <div class="col-sm-9">
                                        <?php echo $bens; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group">
                                    <label for="mensagem" class="col-sm-2 control-label">Tipo de Pagamento</label>
                                    <div class="col-sm-9">
                                        <?php

                                        $sqlGrupo = mysql_query("SELECT *
                                        FROM tipos_pag_saida");
                                        echo "<select name='tipo_pg' id='tipo_pg' $readOnly class='form-control'>";
                                        while($rowTipoPG = mysql_fetch_assoc($sqlGrupo)) {
                                            $selectedTipoPG = ($row_saida['id_tipo_pag_saida'] == $rowTipoPG["id_tipo_pag"]) ? 'SELECTED' : '';
                                            echo "<option value='{$rowTipoPG["id_tipo_pag"]}' $selectedTipoPG data-bloqueio='{$rowTipoPG["bloqueio"]}'>{$rowTipoPG['id_tipo_pag']} - {$rowTipoPG['descricao']}</option>";
                                        }
                                        echo "</select>";
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row" id="intera_boleto">
                                <div class="form-group">
                                    <label for="mensagem" class="col-sm-2 control-label">Tipo de Boleto</label>
                                    <div class="col-sm-9">
                                        <?php echo $tipo_boleto; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row" id="intera_numero">
                                <div class="form-group">
                                    <label for="mensagem" class="col-sm-2 control-label">Nosso número</label>
                                    <div class="col-sm-9">
                                        <input name="nosso_numero" type="text" id="nosso_numero" class="form-control" value="<?php echo $row_saida['nosso_numero']; ?>" <?=$readOnly?> />
                                    </div>
                                </div>
                            </div>
                            <div class="row" id="cod_barra_consumo">
                                <div class="form-group">
                                    <label for="select" class="col-sm-2 control-label">Linha digitável / Código de Barras</label>
                                    <div class="col-sm-9 bar_gr">
                                        <div class="input-daterange input-group" id="bs-datepicker-range">
                                            <input type="text" class="input form-control datepicker bar_gr_i sonumeros" name="barra_consumo[]" id="barra_consumo01" maxlength="11" value="<?php echo $cod_barra_consumo[0]; ?>" <?=$readOnly?> >
                                            <span class="input-group-addon">-</span>
                                            <input type="text" class="input form-control datepicker bar_gr_o sonumeros" name="barra_consumo[]" id="barra_consumo02" maxlength="1" value="<?php echo $cod_barra_consumo[1]; ?>" <?=$readOnly?> >
                                            <span class="input-group-addon bar_gr_u"> </span>
                                            <input type="text" class="input form-control datepicker bar_gr_i sonumeros" name="barra_consumo[]" id="barra_consumo03" maxlength="11" value="<?php echo $cod_barra_consumo[2]; ?>" <?=$readOnly?> >
                                            <span class="input-group-addon">-</span>
                                            <input type="text" class="input form-control datepicker bar_gr_o sonumeros" name="barra_consumo[]" id="barra_consumo04" maxlength="1" value="<?php echo $cod_barra_consumo[3]; ?>" <?=$readOnly?> >
                                            <span class="input-group-addon bar_gr_u"> </span>
                                            <input type="text" class="input form-control datepicker bar_gr_i sonumeros" name="barra_consumo[]" id="barra_consumo05" maxlength="11" value="<?php echo $cod_barra_consumo[4]; ?>" <?=$readOnly?> >
                                            <span class="input-group-addon">-</span>
                                            <input type="text" class="input form-control datepicker bar_gr_o sonumeros" name="barra_consumo[]" id="barra_consumo06" maxlength="1" value="<?php echo $cod_barra_consumo[5]; ?>" <?=$readOnly?> >
                                            <span class="input-group-addon bar_gr_u"> </span>
                                            <input type="text" class="input form-control datepicker bar_gr_i sonumeros" name="barra_consumo[]" id="barra_consumo07" maxlength="11" value="<?php echo $cod_barra_consumo[6]; ?>" <?=$readOnly?> >
                                            <span class="input-group-addon">-</span>
                                            <input type="text" class="input form-control datepicker bar_gr_o sonumeros" name="barra_consumo[]" id="barra_consumo08" maxlength="1" value="<?php echo $cod_barra_consumo[7]; ?>" <?=$readOnly?> >
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row" id="cod_barra_geral">
                                <div class="form-group">
                                    <label for="select" class="col-sm-2 control-label">Linha digitável / Código de Barras</label>
                                    <div class="col-sm-9 bar_gr">
                                        <div class="input-daterange input-group" id="bs-datepicker-range">
                                            <input type="text" class="input form-control datepicker bar_gr_a sonumeros" name="barra_geral[]" id="barra_geral01" maxlength="5" value="<?php echo $cod_barra_gerais[0]; ?>" <?=$readOnly?> >
                                            <span class="input-group-addon">.</span>
                                            <input type="text" class="input form-control datepicker bar_gr_a sonumeros" name="barra_geral[]" id="barra_geral02" maxlength="5" value="<?php echo $cod_barra_gerais[1]; ?>" <?=$readOnly?> >
                                            <span class="input-group-addon">.</span>     
                                            <input type="text" class="input form-control datepicker bar_gr_a sonumeros" name="barra_geral[]" id="barra_geral03" maxlength="5" value="<?php echo $cod_barra_gerais[2]; ?>" <?=$readOnly?> >
                                            <span class="input-group-addon bar_gr_u"> </span>                                                            
                                            <input type="text" class="input form-control datepicker bar_gr_b sonumeros" name="barra_geral[]" id="barra_geral04" maxlength="6" value="<?php echo $cod_barra_gerais[3]; ?>" <?=$readOnly?> >
                                            <span class="input-group-addon">.</span>     
                                            <input type="text" class="input form-control datepicker bar_gr_a sonumeros" name="barra_geral[]" id="barra_geral05" maxlength="5" value="<?php echo $cod_barra_gerais[4]; ?>" <?=$readOnly?> >
                                            <span class="input-group-addon bar_gr_u"> </span>
                                            <input type="text" class="input form-control datepicker bar_gr_b sonumeros" name="barra_geral[]" id="barra_geral06" maxlength="6" value="<?php echo $cod_barra_gerais[5]; ?>" <?=$readOnly?> >
                                            <span class="input-group-addon">.</span>
                                            <input type="text" class="input form-control datepicker bar_gr_o sonumeros" name="barra_geral[]" id="barra_geral07" maxlength="1" value="<?php echo $cod_barra_gerais[6]; ?>" <?=$readOnly?> >
                                            <span class="input-group-addon bar_gr_u"> </span>
                                            <input type="text" class="input form-control datepicker sonumeros" name="barra_geral[]" id="barra_geral08" maxlength="14" value="<?php echo $cod_barra_gerais[7]; ?>" <?=$readOnly?> >
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group">
                                    <label for="mensagem" class="col-sm-2 control-label">Nº do Documento</label>
                                    <div class="col-sm-4">
                                        <input name="n_documento" type="text" id="n_documento" class="form-control" value="<?php echo $row_saida['n_documento'] ?>" <?=$readOnly?>  />
                                    </div>
                                    <label for="projeto_prestador" class="col-sm-2 control-label text-sm">Dt. de Emissão do Doc.</label>
                                    <div class="col-sm-3">
                                        <div class="input-group">
                                            <input type="text" class="form-control data validate[required]" id="dt_emissao_nf" name="dt_emissao_nf" value="<?php echo ($row_saida['dt_emissao_nf_br'] != '00/00/0000') ? $row_saida['dt_emissao_nf_br'] : ''; ?>" <?=$readOnly?> >
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group" id="dados_nf" <?= $row_saida['id_tipo_pag_saida'] != 3 ? 'style="display:none"' : '' ?>>
                                  <label for="mensagem" class="col-sm-2 control-label">Tipo de Nota</label>
                                  <div class="col-sm-4">
                                    <select name="tipo_de_nota" id="tipo_de_nota" class="form-control">
                                      <option value="-1" selected="selected">« Selecione »</option>
                                      <option class="btn_cont1" value="1" <?= selected($row_saida['tipo_de_nota'], 1) ?>>Cupom Fiscal</option>
                                      <option class="btn_cont2" value="2" <?= selected($row_saida['tipo_de_nota'], 2) ?>>Nota Fiscal de Mercadoria</option>
<!--                                      <option class="btn_cont3" value="3" <?= selected($row_saida['tipo_de_nota'], 3) ?>>Nota Fiscal de Servi&ccedil;o</option>-->
                                      <option class="btn_cont4" value="4" <?= selected($row_saida['tipo_de_nota'], 4) ?>>Nota Fiscal Consumidor Eletrônico</option>
                                      <option class="btn_cont5" value="5" <?= selected($row_saida['tipo_de_nota'], 5) ?>>Nota Fiscal - D1</option>
                                      <option class="btn_cont6" value="6" <?= selected($row_saida['tipo_de_nota'], 6) ?>>Conhecimento de Transporte Eletrônico</option>
                                    </select>
                                  </div>
                                  <label class="col-sm-2 control-label text-sm" >Chave da Nota</label>
                                  <div class="col-sm-3">
                                      <input type="text" class="form-control" name="chave_nota" id="chave_nota" value="<?php echo $row_saida['chave_nota']; ?>" <?=$readOnly?> >
                                  </div>
                                </div>
                                <div class="form-group" id="dados_cheque" <?= (in_array($row_saida['id_tipo_pag_saida'], [8,15])) ? '' : 'style="display:none"' ?>>
                                    <label for="agencia_cheque" class="col-sm-2 control-label">Agencia Cheque</label>
                                    <div class="col-sm-4">
                                        <input name="agencia_cheque" type="text" id="agencia_cheque" class="form-control" value="<?php echo $row_saida['agencia_cheque'] ?>" <?=$readOnly?>  />
                                    </div>
                                    <label for="conta_cheque" class="col-sm-2 control-label">Conta Cheque</label>
                                    <div class="col-sm-3">
                                        <input name="conta_cheque" type="text" id="conta_cheque" class="form-control" value="<?php echo $row_saida['conta_cheque'] ?>" <?=$readOnly?>  />
                                    </div>
                                </div>
                            </div>
                            <?php if($row_saida['id_tipo_pag_saida'] == 3){ ?>
                            <div class="row">
                                <div class="form-group">
                                    <label for="mensagem" class="col-sm-2 control-label">Link da NF-E</label>
                                    <div class="col-sm-9">
                                        <input name="link_nfe" type="text" id="link_nfe" class="form-control" />
                                    </div>
                                </div>
                            </div>
                            <?php } ?>
                            <div class="row">
                                <div class="form-group">
                                    <label for="select" class="col-sm-2 control-label">Tipo de Retenção</label>
                                    <div class="col-sm-9">
                                        <select name="tipo_nf" id="tipo_nf" class="form-control" <?=$readOnly?> >
                                            <option value="-1">« Selecione »</option>
                                            <?php
                                            $tipo_nf = array(1 => "IR", 2 => "ISS", 3 => "PIS/COFINS/CSLL", 4 => "INSS");                                                            

                                            foreach ($tipo_nf as $chave => $valor) {
                                                $selected = ($chave == $row_saida['tipo_nf']) ? "selected='selected'" : '';
                                                echo "<option value='$chave' $selected>$chave -  $valor</option>";
                                            }
                                            ?>                                  
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group">
                                    <label for="projeto_prestador" class="col-sm-2 control-label text-sm">Data para Pagamento</label>
                                    <div class="col-sm-3">
                                        <div class="input-group">
                                            <input type="text" class="form-control data validate[required]" id="data_vencimento" name="data_vencimento" value="<?php echo implode('/', array_reverse(explode('-', $row_saida['data_vencimento']))); ?>" <?=$readOnly?> >
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        </div>
                                    </div>
                                    <label for="select" class="col-sm-2 control-label">Competência</label>
                                    <div class="col-sm-4">
                                        <div class="input-daterange input-group" id="bs-datepicker-range">
                                            <?php echo montaSelect(mesesArray(null,$key='-1',$opcao='« Selecione »'),$mesR, "id='mes' name='mes' $readOnly  class='validate[required,custom[select]] form-control'"); ?>
                                            <span class="input-group-addon">Ano</span>
                                            <?php echo montaSelect(SanperesAnosArray(null,null),$anoR, "id='ano' name='ano' $readOnly class='validate[required,custom[select]] form-control'"); ?>                                
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php if (!isset($id_saida) || $row_saida['status'] == 1) { ?>
                        <div class="panel-footer">
                            <div class="form-group no-margin-b">
                                <div class="col-sm-2">
                                    <!--<label for="mensagem" class="control-label">&nbsp;</label>-->
                                    <div class="input-group">
                                        <label class="input-group-addon" for="viagem"><input type="checkbox" name="viagem" id="viagem" <?php echo ($row_saida['id_viagem']) ? 'CHECKED' : null ?> /></label>
                                        <label class="form-control" for="viagem">Viagem?</label>
                                    </div>
                                </div>
                                <div class="col-sm-4 <?php echo (!$row_saida['id_viagem']) ? 'hide' : null ?>" id="div_viagem">
                                    <!--<label for="mensagem" class="control-label">&nbsp;</label>-->
                                    <!--<div class="input-group">-->
                                        <?php echo montaSelect($objViagem->montaSelectViagemByStatus([2]), $row_saida['id_viagem'], "class='form-control' name='id_viagem' id='id_viagem'")?>
                                    <!--</div>-->
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                        <div class="panel-footer">
                            <div class="form-group no-margin-b">
                                <div class="col-sm-4">
                                    <label for="mensagem" class="control-label">Valor Bruto</label>
                                    <div class="input-group">
                                        <input name="valor_bruto" type="text" id="valor_bruto" class="form-control validate[required]" value="<?php echo formataMoeda($row_saida['valor_bruto'],1); ?>" <?=$readOnly?>  />
                                        <span class="input-group-addon"><i class="fa fa-dollar"></i></span>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <label for="valor_juros" class="control-label">Valor Juros</label>
                                    <div class="input-group">
                                        <input name="valor_juros" type="text" id="valor_juros" class="form-control valor" value="<?php echo formataMoeda($row_saida['valor_juros'],1); ?>" <?=$readOnly?>  />
                                        <span class="input-group-addon"><i class="fa fa-dollar"></i></span>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <label for="valor_multa" class="control-label">Valor Multa</label>
                                    <div class="input-group">
                                        <input name="valor_multa" type="text" id="valor_multa" class="form-control valor" value="<?php echo formataMoeda($row_saida['valor_multa'],1); ?>" <?=$readOnly?>  />
                                        <span class="input-group-addon"><i class="fa fa-dollar"></i></span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group no-margin-b">
                                <div class="col-sm-4">
                                    <label for="taxa_expediente" class="control-label">Taxa de Expediente</label>
                                    <div class="input-group">
                                        <input name="taxa_expediente" type="text" id="taxa_expediente" class="form-control valor" value="<?php echo formataMoeda($row_saida['taxa_expediente'],1); ?>" <?=$readOnly?>  />
                                        <span class="input-group-addon"><i class="fa fa-dollar"></i></span>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <label for="desconto" class="control-label">Desconto</label>
                                    <div class="input-group">
                                        <input name="desconto" type="text" id="desconto" class="form-control valor" value="<?php echo formataMoeda($row_saida['desconto'],1); ?>" <?=$readOnly?>  />
                                        <span class="input-group-addon"><i class="fa fa-dollar"></i></span>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <label for="mensagem" class="control-label">Valor IR</label>
                                    <div class="input-group">
                                        <?php // echo "R$ <span id='valor_ir2'>" . number_format(str_replace(",", ".", $row_saida['valor_ir']), 2, ',', '.'). "</span>"; ?>
                                        <input name="valor_ir" type="" id="valor_ir" readonly="" class="form-control" value="<?php echo formataMoeda(str_replace(",", ".", $row_saida['valor_ir']),1); ?>" />
                                        <span class="input-group-addon"><i class="fa fa-dollar"></i></span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group no-margin-b">
                                <div class="col-sm-4">
                                    <label for="mensagem" class="control-label">Valor Líquido</label>
                                    <div class="input-group">
                                        <input name="valor_liquido" type="text" id="valor_liquido" readonly="" class="form-control validate[required]" value="<?php echo formataMoeda(str_replace(",", ".", $row_saida['valor']),1); ?>" <?=$readOnly?>  />
                                        <span class="input-group-addon"><i class="fa fa-dollar"></i></span>
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
                                        $rescisao = $saida->verificaSaidaRescisao($row_saida['id_saida']); 
                                        if(!empty($rescisao)){ ?>
                                        <div class="col-xs-2 margin_b5 <?=$row_files[id_saida_file]?>">
                                            <div class="thumbnail">
                                                <a href="/intranet/rh/recisao/<?=$rescisao?>" target="_blank">
                                                    <img class="h-100" src="../imagens/icons/att-<?=str_replace('.', '', $row_files['tipo_saida_file'])?>.png">
                                                </a>
                                                <!--<span class="btn btn-sm btn-danger fa fa-trash-o margin_t5 deleteAnexoSaida" style="width: 100%;" data-key="<?=$row_files['id_saida_file']?>"> Deletar</span>-->
                                            </div>
                                        </div>
                                        <?php } else { ?>
                                            <div class="col-xs-2 margin_b5 <?=$row_files['id_saida_file']?>">
                                                <div class="thumbnail tr-bg-danger">
                                                    <a href="../comprovantes/<?=$row_files['id_saida_file']?>.<?=$id_saida.$row_files['tipo_saida_file']?>" target="_blank">
                                                        <img class="h-100" src="../imagens/icons/att-<?=str_replace('.', '', $row_files['tipo_saida_file'])?>.png">
                                                    </a>
                                                    <span class="btn btn-sm btn-danger fa fa-trash-o margin_t5 deleteAnexoSaida" style="width: 100%;" data-key="<?=$row_files['id_saida_file']?>"> Deletar</span>
                                                </div>
                                            </div>
                                        <?php } ?>
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
                                        <div class="thumbnail tr-bg-danger">
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
                            
                            <br/><br/>
                            <!--<hr class="panel-wide">-->
                            <?php if($row_saida['agrupada'] == 2){ ?>
                            <div class="col-md-12">
                                <h4>Saídas Agrupadas</h4>
                                <table class="table table-bordered table-striped table-hover text-sm valign-middle">
                                    <thead>
                                        <tr>
                                            <th>COD</th>
                                            <th>Comp</th>
                                            <th>Especifica</th>
                                            <th>N Documento</th>
                                            <th>Valor</th>
                                            <th>Vencimento</th>
                                            <th>Competência</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while($rowAgrupadas = mysql_fetch_assoc($resultSaidasAgrupadas)){ ?>
                                        <tr>
                                            <td><?php echo $rowAgrupadas['id_saida'] ?></td>
                                            <td><?php echo $rowAgrupadas['nome'] ?></td>
                                            <td><?php echo $rowAgrupadas['especifica'] ?></td>
                                            <td><?php echo $rowAgrupadas['n_documento'] ?></td>
                                            <td><?php echo $rowAgrupadas['valor'] ?></td>
                                            <td><?php echo $rowAgrupadas['vencimento'] ?></td>
                                            <td><?php echo $rowAgrupadas['comp'] ?></td>
                                        </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                                
                                <!--input type="button" class="btn btn-danger pull-right" value="Desprocessar Agrupamento" /-->
                            </div>
                            <?php } ?>
                        </div>
                        <div class="panel-footer text-right">
                            <?php if (in_array($row_saida['tipo'], $arraySaidaRh)) { ?>
                                <!--<input type="button" class="btn btn-primary botao_atualizar_saida_rh" value="Atualizar" />
                                <input type="hidden" name="atualizar_saida_rh" id="atualizar_saida_rh" value="atualizar_saida_rh" />-->
                            <?php } else { ?>
                                <input type="button" class="btn btn-primary botaoSubmit pull-right" value="<?=$botao?>" />
                                <input type="hidden" name="<?=strtolower($botao)?>" id="<?=strtolower($botao)?>" value="<?=$botao?>" />
                                <?php if(!$row_saida) { ?>
                                    <div class="col-md-3 pull-right">
                                        <div class="input-group">
                                            <label class="input-group-addon" for="confirmacao"><input type="checkbox" id="confirmacao" name="confirmacao" value="1"></label>
                                            <label class="form-control text-left pointer" for="confirmacao">Gerar Confirmação</label>
                                        </div>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                        </div>
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
                var ID_SAIDA_GLOBAL = null;
                
                // Só aceita números
                $('#n_documento').keypress(function (evt) {
                    var theEvent = evt || window.event;
                    var key = theEvent.keyCode || theEvent.which;
                    key = String.fromCharCode(key);
                    var regex = /\D/g;
                    if (regex.test(key)) {
                        theEvent.returnValue = false;
                        if (theEvent.preventDefault) theEvent.preventDefault();
                    }
                });

                $('#tipo_pg').change(function () {
                    if ($('#tipo_pg option:selected').attr('data-bloqueio') == 1) {
                        $('#n_documento').addClass('validate[required]');
                    } else {
                        $('#n_documento').removeClass('validate[required]');

                    }
                });

                var id_saida = $("#id_saida").val();
                
                $("#regiao").ajaxGetJson("../methods.php", {method: "carregaProjetos"}, function(){
                    var projeto_bd = $("#projeto_bd").val();
                    
                    if(projeto_bd != ''){
                        $("#projeto").val(projeto_bd);
                        $("#projeto").change();                        
                    }
                }, "projeto");
                
//                $("#projeto").ajaxGetJson("../methods.php", {method: "carregaBancos"}, function(){
//                    var banco = $("#banco_bd").val();
//                    
//                    if(banco != ''){
//                        $("#banco").val(banco);
//                    }
//                }, "banco");
                
                $("#select_grupo").ajaxGetJson("actions/action.saida.php", {action: "load_subgrupo", opt: "« Selecione »"}, function(){                    
                    $("#regiao_prestador").change();
                    $("#projeto_prestador").change();
                    
                    var subgrupo = $("#subgrupo_bd").val();
                    //alert(subgrupo);
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
                    $("#projeto_prestador").trigger('change');
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
                
                $("#tipo, #tipo_nome").ajaxGetJson("actions/action.saida.php", {action: "carregaNomes", tipo_nome: $('#tipo_nome').val(), tipo: $("#tipo").val(), regiao: $('#regiao').val()}, function(){
                    if(id_saida != ''){
                        $("#nome").val($("#nome_pessoa").val());
                    }
                    if($('#tipo_nome').val() != 'outro'){
                        $("#add_nome").hide();
                    } else {
                        $("#add_nome").show();
                    }
                }, "nome");
                
                $("#dt_emissao_nf, #data_vencimento").mask("99/99/9999");
                $("#valor_liquido, #valor_bruto, #valor_estorno_parcial, #adicional, .valor").maskMoney({prefix:'R$ ', allowNegative: true, thousands:'.', decimal:','});
                
                $("#form1").validationEngine({promptPosition : "topRight"});
                
                //datepicker
                $('.data').datepicker({
                    dateFormat: 'dd/mm/yy',
                    changeMonth: true,
                    changeYear: true,
                    yearRange: '2005:c+1'
                });
                var totalAnexos = 0; 
                Dropzone.autoDiscover = false;
                <?php if(empty($id_saida)) { ?>
                var myDropzoneAnexo = new Dropzone("#dropzoneAnexo",{
                    url: "actions/action.saida.php?tipo_anexo=1",
                    addRemoveLinks : true,
                    maxFilesize: 800,
                    //envio automatico
                    autoQueue: false,
                    dictResponseError: "Erro no servidor!",
                    dictCancelUpload: "Cancelar",
                    dictFileTooBig: "Tamanho máximo: 80MB",
                    dictRemoveFile: "Remover Arquivo",
                    canceled: "Arquivo Cancelado",
                    acceptedFiles: '.jpg,.gif,.png,.pdf,.JPG,.GIF,.PNG,.PDF,.rar',
                    init: function() {
                        this.on("addedfile", function(file) { totalAnexos = totalAnexos+1; });
                        this.on("removedfile", function(file) { alert(totalAnexos); totalAnexos = totalAnexos-1; alert(totalAnexos); });
                        this.on("totaluploadprogress", function(p, file) { 
//                            console.log(p, file, totalAnexos);
                            if(p >= 100 && totalAnexos > 0 && file > 0) {
//                                upload_ok = false;
                                remove_carregando_modal();
                                bootDialog(
                                    'Saída Cadastrada Com Sucesso!!!', 
                                    'Saída Cadastrada!!!', 
                                    [{
                                        label: 'Fechar',
                                        action: function(){
                                            window.location.href = ($('#confirmacao').prop('checked')) ? "confirmacao_cadastro.php?id=" + ID_SAIDA_GLOBAL : "../finan";
                                        }
                                    }], 
                                    'success'
                                );
                            } 
                        });
                    }
                });
                <?php } else if(!empty($id_saida)) { ?>
                var myDropzoneAnexo = new Dropzone("#dropzoneAnexo",{
                    url: "actions/action.saida.php?tipo_anexo=1&id_saida=<?=$id_saida?>&action=upload_anexo",
                    maxFilesize: 80,
                    dictResponseError: "Erro no servidor!",
                    dictCancelUpload: "Cancelar",
                    dictFileTooBig: "Tamanho máximo: 80MB",
                    dictRemoveFile: "Remover Arquivo",
                    canceled: "Arquivo Cancelado",
                    acceptedFiles: '.jpg,.gif,.png,.pdf,.JPG,.GIF,.PNG,.PDF'
                });
                
                var myDropzoneComprovante = new Dropzone("#dropzoneComprovante",{
                    url: "actions/action.saida.php?tipo_anexo=2&id_saida=<?=$id_saida?>&action=upload_anexo",
                    maxFilesize: 80,
                    dictResponseError: "Erro no servidor!",
                    dictCancelUpload: "Cancelar",
                    dictFileTooBig: "Tamanho máximo: 20MB",
                    dictRemoveFile: "Remover Arquivo",
                    canceled: "Arquivo Cancelado",
                    acceptedFiles: '.jpg,.gif,.png,.pdf,.JPG,.GIF,.PNG,.PDF'
                });
                <?php } ?>
                
                $(".botao_atualizar_saida_rh").on('click', function(){
                    $.post("form_saida.php", {
                        id_saida: $('#id_saida').val(),
                        estorno: $('#estorno').val(),
                        valor_estorno_parcial: $('#valor_estorno_parcial').val(),
                        descricao_estorno: $('#valor_estorno_parcial').html(),
                        atualizar_saida_rh: 'atualizar_saida_rh'
                    }, function(resposta){
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
                    
                    
                $(".botaoSubmit").on('click', function(){
//                    cria_carregando_modal();
//                    myDropzoneAnexo.on('sending',function(file, xhr, formData) {
//                        formData.append("id_saida", 9999); // Append all the additional input data of your form here!
//                        formData.append("action", 'upload_anexo'); // Append all the additional input data of your form here!
//                    });
//
//                    myDropzoneAnexo.enqueueFiles(myDropzoneAnexo.getFilesWithStatus(Dropzone.ADDED));
//                    return false;
                    if ($("#form1").validationEngine('validate')) {
                        var valor_liquido = ($('#valor_liquido').val()) ? parseFloat($('#valor_liquido').val().replace(/\./g, '').replace(/\,/g, '.')) : 0.00;
                        if(valor_liquido <= 0){
                            bootAlert('O valor líquido (' + number_format(valor_liquido, 2, ',', '.') + ') não pode ser menor que 0,00', 'Rateio', null, 'danger');
                            return false;
                        }
                        
                        var dados = $('#form1').serialize();
                        
                        cria_carregando_modal();
                        $.post("form_saida.php", dados, function(resposta){
                            ID_SAIDA_GLOBAL = resposta;
                            var res = JSON.parse(resposta);
                            //console.log(res.error); return false;
                            if(res.error == 1){
                                remove_carregando_modal();
                                bootAlert(
                                    'Esta saída já está cadastrada!',
                                    'Saída já cadastrada',
                                    null,
                                    'info'
                                );
                                return false;
                            }

                            <?php if(empty($id_saida)) { ?>

                            myDropzoneAnexo.on('sending',function(file, xhr, formData) {
                                formData.append("id_saida", resposta); // Append all the additional input data of your form here!
                                formData.append("action", 'upload_anexo'); // Append all the additional input data of your form here!
                            });

                            myDropzoneAnexo.enqueueFiles(myDropzoneAnexo.getFilesWithStatus(Dropzone.ADDED));

                            <?php } ?>
                            //console.log(resposta); return false;
                            if(totalAnexos == 0 && !res.error) {

                                remove_carregando_modal();

                                bootDialog(
                                    'Saída Cadastrada Com Sucesso!',
                                    'Saída Cadastrada!',
                                    [{
                                        label: 'Fechar',
                                        action: function(){
                                            window.location.href = ($('#confirmacao').prop('checked')) ? "confirmacao_cadastro.php?id=" + ID_SAIDA_GLOBAL : "../finan";
                                        }
                                    }],
                                    'success'
                                );
                            }
                        }); 
                    }
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
                
                
                /**
                * IR prestador pessoa fisica
                 */
                var idPrestador = <?php echo ($prestador_bd) ? $prestador_bd : 0 ?>;
                $('body').on('change', '#prestador_inativo, #prestador_outros, #prestador', function(){
                    idPrestador = $(this).val();
                });
                
                function calculaLiquido() {
                    var key = $(this).data('key');
                    var valor_bruto =     ($('#valor_bruto').val()) ? parseFloat($('#valor_bruto').val().replace(/\./g, '').replace(/\,/g, '.')) : 0.00;
                    var desconto =        ($('#desconto').val()) ? parseFloat($('#desconto').val().replace(/\./g, '').replace(/\,/g, '.')) : 0.00;
                    var taxa_expediente = ($('#taxa_expediente').val()) ? parseFloat($('#taxa_expediente').val().replace(/\./g, '').replace(/\,/g, '.')) : 0.00;
                    var valor_multa =     ($('#valor_multa').val()) ? parseFloat($('#valor_multa').val().replace(/\./g, '').replace(/\,/g, '.')) : 0.00;
                    var valor_juros =     ($('#valor_juros').val()) ? parseFloat($('#valor_juros').val().replace(/\./g, '').replace(/\,/g, '.')) : 0.00;
                    var valor_ir = 0.00;
                    
                    valor = valor_bruto + valor_multa + valor_juros + taxa_expediente - desconto;
                    
                    $.post('actions/action.saida.php', { action: 'calculaIR', id: idPrestador, valor: valor }, function(data){
                        if(idPrestador > 0 && $('#tipo').val() == 327) { 
                            if(data.status == 1){
                                $('#valor_ir').val(number_format(data.ir, 2, ',', '.'));
                                valor_ir = number_format(data.ir, 2, '.', '');
                            } else {
                                $('#valor_ir').val('0,00');
                            }
                        } else {
                            valor_ir = 0.00;
                        }
                        console.log(data);
                        valor = valor - valor_ir;

                        valor = number_format(valor.toFixed(2), 2, ',', '.');
                        $('#valor_ir').val(number_format(valor_ir, 2, ',', '.'));
                        $('#valor_liquido').val(valor);

                    }, 'json');
                }
//                $('body').on('keyup', '#valor_bruto, #desconto, #taxa_expediente, #valor_multa, #valor_juros', function(){
//                    calculaLiquido();
//                });
                $('body').on('blur', '#valor_bruto, #desconto, #taxa_expediente, #valor_multa, #valor_juros, #valor_liquido, #valor_ir', function(){
                    calculaLiquido();
                });
                
//                upload_ok = true;
//                window.onbeforeunload = function(){ if(upload_ok) { return true; } }; 
                
                $('body').on('change', '#viagem', function(){
                    if($(this).prop('checked')) {
                        $('#div_viagem').removeClass('hide');
                        $('#id_viagem').val(0);
                    } else {
                        $('#div_viagem').addClass('hide');
                    }
                });
            });
        </script>
    </body>
</html>
