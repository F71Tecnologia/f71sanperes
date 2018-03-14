<?php

/***********LISTA DE LOCALIZAÇÃO DE CADA INSTÂNCIA DE CLASS PARA LOG*******************/
/**************************************************************************************/
/**INCLUIR USUÁRIO NA FOLHA - logIncluirNaFolha (folha2.php)***************************/
/**EXCLUIR USUARIO DA FOLHA - logRemoveDaFolha - TIPO - 1 - (Folha2.php) - JAVASCRIPT**/
/**DESFAZER EXCLUSÃO - logRemoveDaFolha - TIPO - 2 - (Folha2.php) - JAVASCRIPT*********/
/**CRIAR FOLHA - logCriarFolha - (folha.php)*******************************************/
/**EXCLUIR FOLHA - logExcluirFolhaAberta - (folha.php)*********************************/
/**FINALIZAR FOLHA - logFecharFolha - (acao_folha.php)*********************************/
/**DESPROCESSAR FOLHA - logDesprocessaFolha - (desprocessar.php)***********************/


if(empty($_COOKIE['logado'])){
    print "Efetue o Login<br><a href='www.netsorrindo.com.br/intranet/login.php'>Logar</a> ";
    exit;
}

include "../../conn.php";
include "../../funcoes.php";
include "../../wfunction.php";
include "../../classes/funcionario.php";
include "../../classes_permissoes/acoes.class.php";
include "../../classes/FolhaClass.php";

$Func = new funcionario();
$ACOES = new Acoes();
$f = new Folha();
$usuario = carregaUsuario();

$id_user = $_COOKIE['logado'];
$tela = (!empty($_REQUEST['tela'])) ? $_REQUEST['tela'] : 1;

$sql = "SELECT * FROM funcionario WHERE id_funcionario = '$id_user'";
$result_user = mysql_query($sql, $conn);
$row_user = mysql_fetch_array($result_user);

$result_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_user[id_master]'");
$row_master = mysql_fetch_array($result_master);
$ano = date("Y");

switch ($tela){
    
//--------------------------------MOSTRANDO A TELA NORMALMENTE-----------------------------------
case 1:
	
/**
 *  RECEBENDO A VARIAVEL CRIPTOGRAFADA
 */
$regiao = $usuario[id_regiao];

/**
 * INICIANDO VARIÁVEIS
 */    
$link = "0";
$enc = "0";
$decript = "0";

/**
 * DADOS DA REGIÃO 
 */
$qr_regiao = mysql_query("SELECT regiao FROM regioes WHERE id_regiao = '$regiao'");
$row_regiao = mysql_fetch_assoc($qr_regiao);

/**
 * CABEÇALHO DA PAGINA
 */
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form1", "ativo"=>"Folha de Pagamento");
$breadcrumb_pages = array("Gestão de RH" => "../index.php");

/**
 * FOLHAS FINALIZADAS
 */
$qry_folha_finalizada = "SELECT A.id_folha, B.nome as projeto, C.nome as funcionario, A.mes, A.ano, 
            CONCAT(DATE_FORMAT(A.data_inicio,'%d/%m/%Y'),' até ', DATE_FORMAT(A.data_fim,'%d/%m/%Y')) as periodo, CONCAT(A.clts,' CLTs ') as participantes
            FROM rh_folha AS A 
            LEFT JOIN projeto AS B ON(A.projeto = B.id_projeto)
            LEFT JOIN funcionario AS C ON(A.user = C.id_funcionario)
            WHERE A.regiao = {$regiao} AND A.`status` = 3
            ORDER BY A.ano, A.mes";
$sql_folha_finalizadas = mysql_query($qry_folha_finalizada) or die ('Erro ao selecionar folhas finalizadas');


?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Folha de Pagamento</title>
        <link href="../../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" >
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../css/progress.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap-note.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/add-ons.min.css" rel="stylesheet">
        <link rel="stylesheet" href="../../jquery/thickbox/thickbox.css" type="text/css" media="screen" />
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        <div class="container">
            <form name="form" action="" method="post" id="form1" class="form-horizontal">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small> - Folha de Pagamento</small></h2></div>
                </div>
            </div>
            <?php if($ACOES->permissoes_folha(4,$regiao)) { ?>
                <div class="panel panel-dark-gray">
                    <div class="panel-heading text-primary text-center text-bold">
                        REGI&Atilde;O DE <?php echo strtoupper(strtr($row_regiao['regiao'] ,"áéíóúâêôãõàèìòùç","ÁÉÍÓÚÂÊÔÃÕÀÈÌÒÙÇ")); ?>
                    </div>
                    <div class="panel-body">
                        <form action="" method="post" name="form1" class="form-horizontal" onSubmit="return validaForm()">
                            <div class="form-group">
                                <label class="col-xs-2 control-label">Projeto:</label>
                                <div class="col-xs-10">
                                    <select name="projeto" id="projeto" class='form-control' >
                                    <?php
                                    $result_pro = mysql_query("SELECT * FROM projeto WHERE id_regiao = '$regiao'");
                                    $i = "0";
                                    while($row_pro = mysql_fetch_array($result_pro)){
                                        echo "<option value='$row_pro[0]'>$row_pro[nome]</option>";
                                        $projetos_regi[$i] = $row_pro[0];
                                        $i ++;
                                    } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-2 control-label">M&ecirc;s de Refer&ecirc;ncia:</label>
                                <div class="col-xs-4">
                                    <div class="input-group">
                                        <?=montaSelect(mesesArray(), date('m'), 'name="mes" id="mes" class="form-control"')//$link = "folha.php?id=10&id_projeto=$id_projeto&regiao=$regiao"; ?>
                                        </select>
                                        <div class="input-group-addon">de</div>
                                        <?=montaSelect(anosArray(), date('Y'), 'id="ano" name="ano" class="form-control"')?>
                                    </div>
                                </div>
                                <label class="col-xs-2 control-label">Inicio da Folha:</label>
                                <div class="col-xs-4">
                                    <input name="data_ini" type="text" id="data_ini" class="form-control" maxlength="10" onKeyUp="mascara_data(this)">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-2 control-label">D&eacute;cimo Terceiro:</label>
                                <div class="col-xs-1 checkbox">
                                    <label><input type="radio" value="1" name="terceiro" onClick="document.all.linhatipo.style.display=''"> Sim</label>
                                </div>
                                <div class="col-xs-1 checkbox">
                                    <label><input type="radio" value="2" name="terceiro" checked onClick="document.all.linhatipo.style.display='none'"> Não</label>
                                </div>
                                <div class="col-xs-8" id="linhatipo" style="display:none">
                                    <label class="col-xs-4 control-label">Tipo de Pagamento:</label>
                                    <div class="col-xs-8">
                                        <select name="tipo_terceiro" id="tipo_terceiro" class="form-control">
                                            <option value="1">PRIMEIRA PARCELA</option>
                                            <option value="2">SEGUNDA PARCELA</option>
                                            <option value="3" selected="selected">INTEGRAL</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="panel-footer text-right">
                        <input type="hidden" name="regiao" id="regiao" value="<?=$regiao?>">
                        <input type="hidden" name="tela" id="tela" value="2">
                        <input type="hidden" name="id_usuario" value="<?=$id_user?>">
                        <input name="gerar" type="submit" class="btn btn-primary" id="gerar" value="GERAR FOLHA" />
                    </div>
                    </form>
                </div>
            <?php  } else { 
                $result_pro = mysql_query("SELECT * FROM projeto WHERE id_regiao = '$regiao'");
                $i = "0";
                while($row_pro = mysql_fetch_array($result_pro)){
                    echo "<h5 class='text-center'>$row_pro[nome]</h5>";
                    $projetos_regi[$i] = $row_pro[0];
                    $i ++;
                } 	
            }///FIM PERMISSAO GERAR FOLHA ?>
            
            <div class="row">
                <div class="col-xs-12">
                    <ul class="nav nav-tabs margin_b10">
                        <li class="tab active"><a href="#emandamento" data-toggle="tab">Em Andamento</a></li>
                        <li class="tab"><a href="#finalizadas" data-toggle="tab">Finalizadas</a></li>
                    </ul>
                </div>
            </div>
                
            <?php
            $meses = array('Erro','Janeiro','Fevereiro','Mar&ccedil;o','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro');
            $cores = array('#E3ECE3','#ECF2EC','#ECF3F6','#F5F9FA','#FEF9EB','#FFFDF7');
            $projetos_flip = array_flip($projetos_regi);
            $result_folhas = mysql_query("SELECT *,date_format(data_inicio, '%d/%m/%Y')as data_inicio,date_format(data_fim, '%d/%m/%Y')as data_fim 
            FROM rh_folha WHERE regiao = '$regiao' AND status = '1' ORDER BY projeto,mes");?>
            <div id="myTabContent" class="tab-content">
                <div class="tab-pane active" id="emandamento">
                    <div class="note note-info">
                        <h3 >FOLHAS EM ANDAMENTO</h3>
                        <hr class="hr-info">
                        <?php if(mysql_num_rows($result_folhas) != 0) { ?>
                            <table class="table table-condensed table-hover">
                                <?php
                                while($row_folhas = mysql_fetch_array($result_folhas)){

                                    $qr_total = mysql_query("SELECT * FROM rh_folha_proc WHERE status = '1' AND id_folha = '$row_folhas[id_folha]'");
                                    $total = mysql_num_rows($qr_total);

                                    //-- ENCRIPTOGRAFANDO A VARIAVEL
                                    $linkreg = encrypt("$regiao&$row_folhas[0]"); 
                                    $linkreg = str_replace("+","--",$linkreg);
                                    // -----------------------------

                                    $result_pro = mysql_query("SELECT id_projeto,nome,tema FROM projeto WHERE id_projeto = '$row_folhas[projeto]'");
                                    $row_pro = mysql_fetch_array($result_pro);

                                    $id_projeto_agora = $row_pro['0'];
                                    $num_cor = $projetos_flip[$id_projeto_agora];
                                    $cor_agora = $cores[$num_cor];

                                    $Func -> MostraUser($row_folhas['user']);
                                    $nomefun = $Func -> nome1; ?>


                                    <tr class="valign-middle">
                                        <td width='5%' align='center'>
                                            <?php if($ACOES->permissoes_folha(75,$regiao)) { ?>
                                                <a href='folha2.php?m=1&enc=<?=$linkreg?>'>
                                                    <img src='imagens/profolha.gif' border='0' align='absmiddle' alt='PROCESSAR'>
                                                </a>
                                            <?php } ?>
                                        </td>
                                        <td width='25%'><?="$row_folhas[0] - $row_pro[nome]"?></td>
                                        <td width='25%'><?=$nomefun?></td>
                                        <td width='15%'><b><?=$mes_da_folha?></b></td>
                                        <td width='25%'><?="$row_folhas[data_inicio] at&eacute; $row_folhas[data_fim]"?></td>
                                        <td width='5%' align='center'>
                                            <?php if($ACOES->permissoes_folha(6,$regiao)) {///permissão para DELETAR FOLHA ?>
                                                <a href='#' onClick='confirm_entry($regiao,$row_folhas[0])'>
                                                    <img src='imagens/delfolha.gif' border='0' align='absmiddle' alt='DELETAR' data-key='<?=$row_folhas[0]?>'></a>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </table>
                        <?php } ?>
                        <?php
                        $result_folhas2 = mysql_query("SELECT *,date_format(data_inicio, '%d/%m/%Y')as data_inicio,date_format(data_fim, '%d/%m/%Y')as data_fim 
                        FROM rh_folha WHERE regiao = '$regiao' AND status = '2' ORDER BY projeto,mes"); ?>
                        <?php if(mysql_num_rows($result_folhas2) != 0) { ?>
                            <table class="table table-condensed table-hover">
                                <?php
                                while($row_folhas2 = mysql_fetch_array($result_folhas2)){

                                    $qr_total = mysql_query("SELECT * FROM rh_folha_proc WHERE status = '2' AND id_folha = '$row_folhas2[id_folha]'");
                                    $total = mysql_num_rows($qr_total);

                                    //-- ENCRIPTOGRAFANDO A VARIAVEL
                                    $linkreg2 = encrypt("$regiao&$row_folhas2[0]"); 
                                    $linkreg2 = str_replace("+","--",$linkreg2);
                                    // -----------------------------

                                    $result_pro2 = mysql_query("SELECT id_projeto,nome,tema FROM projeto WHERE id_projeto = '$row_folhas2[projeto]'");
                                    $row_pro2 = mysql_fetch_array($result_pro2);

                                    $mes_int2 = (int)$row_folhas2['mes'];

                                    $mes_da_folha2 = $meses[$mes_int2];

                                    $id_projeto_agora2 = $row_pro2['0'];
                                    $num_cor2 = $projetos_flip[$id_projeto_agora2];
                                    $cor_agora2 = $cores[$num_cor2];

                                    $Func -> MostraUser($row_folhas2['user']);
                                    $nomefun = $Func -> nome1;

                                    if($row_folhas2['terceiro'] == 1){
                                        switch ($row_folhas2['tipo_terceiro']){
                                            case 1: $exibi = "<b>13º Primeira parcela</b>"; break;
                                            case 2: $exibi = "<b>13º Segunda parcela</b>"; break;
                                            case 3: $exibi = "<b>13º Integral</b>"; break;
                                        }
                                    }else{
                                        $exibi = "<b>$mes_da_folha2 - $row_folhas2[parte]</b>";
                                    } ?>

                                    <tr class="valign-middle">
                                        <td width='5%' class="text-center">
                                            <a class="btn btn-xs btn-primary" href='../../rh/folha/sintetica.php?enc=<?=$linkreg2?>' title="VISUALIZAR FOLHA <?=$row_folhas2[0]?>">
                                                <i class="fa fa-search"></i>
                                            </a>
                                        </td>
                                        <td width='25%'><?="$row_folhas2[0] - $row_pro2[nome]"?></td>
                                        <td width='25%'><b><?=$nomefun?></b></td>
                                        <td width='10%'><?=$exibi?></td>
                                        <td width='20%'><?="$row_folhas2[data_inicio] at&eacute; $row_folhas2[data_fim]"?></td>
                                        <td width='10%'>CLT's: <?=$total?></td>
                                        <td width='5%' class="text-center">
                                            <?php
                                            $verifica_acoes  = mysql_num_rows($qr_acoes_assoc = mysql_query("SELECT * FROM funcionario_acoes_assoc WHERE id_funcionario = '$_COOKIE[logado]' AND acoes_id = '5' AND id_regiao = '$regiao'"));
                                            if($verifica_acoes != 0) {///permissão para DELETAR FOLHA ?>
                                                <a href='#' class="btn btn-xs btn-danger" onClick='confirm_entry(<?="$regiao,$row_folhas2[0]"?>)' title="DELETAR">
                                                    <i class="fa fa-ban"></i>
                                                </a>
                                            <?php } ?>
                                        </td>
                                    <?php } ?>
                                </tr>		
                            </table>
                        <?php } else { ?>
                            <div class="alert alert-warning">Nenhuma Folha Em Andamento!</div>
                        <?php } ?>
                    </div>
                </div>
                <div class="tab-pane" id="finalizadas">
                    <div class="note">
                        <h3>FOLHAS FINALIZADAS</h3>
                        <hr>                        
                        <?php $cont_linhas = 0; 
                        $result_pro3 = mysql_query("SELECT id_projeto,nome,tema FROM projeto WHERE id_regiao = '$regiao'");
                        while( $row_pro3    = mysql_fetch_array($result_pro3)){
                            $result_folhas3 = mysql_query("SELECT * , date_format(data_inicio, '%d/%m/%Y') AS data_inicio,date_format(data_fim, '%d/%m/%Y') AS data_fim FROM rh_folha WHERE regiao = '$regiao' AND status = '3' AND  projeto = '$row_pro3[id_projeto]' ORDER BY projeto,ano,mes  ASC");
                            if(mysql_num_rows($result_folhas3) != 0){ ?>
                                <label class="margin_t5"><?=$row_pro3['nome']?></label>
                        <div class="panel-group" id="accordion">
                            <div class="panel panel-info transparent">
                                        <?php
                                        while($row_folhas3 = mysql_fetch_array($result_folhas3)) {
                                            $qr_total = mysql_query("SELECT * FROM rh_folha_proc WHERE status = '3' AND id_folha = '$row_folhas3[id_folha]'");
                                            $total    = mysql_num_rows($qr_total);
                                            $cont_linhas++;
if($ultimo_ano != $row_folhas3['ano']) {
                                            $total_folhas_ano = mysql_num_rows(mysql_query("SELECT * , date_format(data_inicio, '%d/%m/%Y') AS data_inicio,date_format(data_fim, '%d/%m/%Y') AS data_fim FROM rh_folha WHERE regiao = '$regiao' AND status = '3' AND ano = '$row_folhas3[ano]' AND  projeto = '$row_pro3[id_projeto]' ORDER BY projeto,ano,mes")); ?>
                                            <div class="panel-heading text-center text-bold pointer" data-toggle="collapse" data-parent="#accordion" href="#collapse<?=$row_pro3['id_projeto'].$row_folhas3['ano']?>">
                                        <h5 class="panel-title text-bold" style="color: #000!important;">
                                                    <?=$row_folhas3['ano']?>
                                        </h5>
                                    </div>
                                            <div id="collapse<?=$row_pro3['id_projeto'].$row_folhas3['ano']?>" class="panel-collapse collapse <?=(date('Y') == $row_folhas3['ano']) ? 'in' : '';?>">
                                    <div class="panel-body">
                                        <table class="table table-condensed table-hover folhas">
                                                <?php }
                                                    $ultimo_projeto = $row_folhas3['projeto'];
                                                    $ultimo_ano		= $row_folhas3['ano'];

                                                    // ENCRIPTOGRAFANDO A VARIAVEL
                                                    $linkreg3 = str_replace("+","--",encrypt("$regiao&$row_folhas3[0]"));
                                                    $mes_int3 = (int)$row_folhas3['mes'];
                                                    $mes_da_folha3 = $meses[$mes_int3];

                                                    $id_projeto_agora3 = $row_pro3['0'];
                                                    $num_cor3   = $projetos_flip[$id_projeto_agora3];
                                                    $cor_agora3 = $cores[$num_cor3];

                                                    $Func   -> MostraUser($row_folhas3['user']);
                                                    $nomefun = $Func -> nome1;

                                                    if($row_folhas3['terceiro'] == 1) {
                                                        switch ($row_folhas3['tipo_terceiro']) {
                                                            case 1: $exibicao = "<b>13º Primeira parcela</b>"; break;
                                                            case 2: $exibicao = "<b>13º Segunda parcela</b>"; break;
                                                            case 3: $exibicao = "<b>13º Integral</b>"; break;
                                                        }
                                                    } else {
                                                        $exibicao = "<b>$mes_da_folha3</b>";
                                                    } ?>
                                    <tr class="valign-middle">
                                        <td width="5%" class="text-center">
                                            <?php if($ACOES->permissoes_folha(5,$regiao)) {///permissão para VER FOLHA ?>
                                                <a class="btn btn-xs btn-primary" href="ver_folha.php?enc=<?=$linkreg3?>" title="VISUALIZAR">
                                                    <i class="fa fa-search"></i>
                                                </a>
                                            <?php } ?>
                                        </td>
                                                            <td width="25%"><?=$row_folhas3['id_folha'].' - '.$row_pro3['nome']?></td>
                                                            <td width="25%"><?=$nomefun?></td>
                                                            <td width="10%"><?=$exibicao?></td>
                                                            <td width="20%"><?=$row_folhas3['data_inicio'].' at&eacute; '.$row_folhas3['data_fim']?></td>
                                                            <td width="10%"><?=$total?> CLTs</td>
                                        <td width="5%" class="text-center">
                                            <?php
                                                                // trava solicitada pelo SABINO, feito por MAX (permitir desprocessar somente folhas do mês atual)
                                                                $data_folhaFin = "{$row_folhas3['mes']}/{$row_folhas3['ano']}";
                                            $data_atualFin = date('m/Y');

                                                                if($data_folhaFin == $data_atualFin){
                                                                    if($ACOES->permissoes_folha(8,$regiao)) { // permissão para DESPROCESSAR folha ?>
                                                                        <a class="btn btn-xs btn-danger" href="desprocessar.php?folha=<?=$row_folhas3['id_folha']?>" title="Desprocessar Folha" onClick="return window.confirm('Você tem certeza que quer desprocessar esta folha?');"><i class="fa fa-ban"></i></a>
                                                                    <?php }
                                                                } ?>
                                        </td>
                                    </tr>
                                    <?php if($cont_linhas == $total_folhas_ano) { $cont_linhas = 0;  ?>
                                            </table> 
                                        </div>
                                    </div>
                                    <?php } ?>
                                <?php } ?>
                            </div>
                        </div>
                            <?php  }
                            unset($ultimo_ano);
                        } ?>
                    </div>
                </div>
            </div>
        <?php include_once '../../template/footer.php'; ?>
        </div>
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../js/jquery.mask.min.js" type="text/javascript"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script src="../../uploadfy/scripts/jquery.uploadify.v2.1.0.js" type="text/javascript"></script>
        <script src="../../uploadfy/scripts/swfobject.js" type="text/javascript"></script>
        <script src="../../jquery/priceFormat.js" type="text/javascript"></script>
        <script type="text/javascript">
            $(function() {
                $('#data_ini').datepicker({
                    changeMonth: true,
                    changeYear: true
                });
            });

            function MM_preloadImages() {
              var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
                var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
                if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
            }
            
            function mascara_data(d){
	        var mydata = '';  
                data = d.value;  
                mydata = mydata + data;  
                if(mydata.length == 2){  
                    mydata = mydata + '/';  
                    d.value = mydata;  
                }  
                if (mydata.length == 5){  
                    mydata = mydata + '/';  
                    d.value = mydata;  
                }  
                if (mydata.length == 10){  
                    verifica_data(d);  
                }  
            } 

            function verifica_data (d) {  

                dia = (d.value.substring(0,2));  
                mes = (d.value.substring(3,5));  
                ano = (d.value.substring(6,10));  

                situacao = "";  
                // verifica o dia valido para cada mes  
                if ((dia < 01)||(dia < 01 || dia > 30) && (  mes == 04 || mes == 06 || mes == 09 || mes == 11 ) || dia > 31) {  
                    situacao = "falsa";  
                }  

                // verifica se o mes e valido  
                if (mes < 01 || mes > 12 ) {  
                       situacao = "falsa";  
                }  

                // verifica se e ano bissexto  
                if (mes == 2 && ( dia < 01 || dia > 29 || ( dia > 28 && (parseInt(ano / 4) != ano / 4)))) {  
                     situacao = "falsa";  
                }  

                if (d.value == "") {  
                   situacao = "falsa";  
                }  

                if (situacao == "falsa") {  
                    alert("Data digitada é inválida, digite novamente!"); 
                    d.value = "";  
                    d.focus();  
                }  
            }

            function confirm_entry(a,b){
                var Regiao = a;
                var Folha = b;

                input_box=confirm("Deseja realmente DELETAR?");

                if (input_box==true){ 
                    // Output when OK is clicked
                    // alert (\"You clicked OK\"); 
                    location.href="folha.php?tela=3&regiao=" + Regiao + "&folha=" + Folha;
                    }else{
                    // Output when Cancel is clicked
                    // alert (\"You clicked cancel\");
                }
            }
            
        </script>
    </body>
</html>

<?php
$regiao = "0";

break;
//--------------------------------CADASTRANDO A FOLHA-----------------------------------
case 2:

    $mes = $_REQUEST['mes'];
    $ano = $_REQUEST['ano'];
    $regiao = $_REQUEST['regiao'];
    $projeto = $_REQUEST['projeto'];
    $data_ini = $_REQUEST['data_ini'];
    $ferias = $_REQUEST['ferias'];
    $terceiro = $_REQUEST['terceiro'];
    $tipo_terceiro = $_REQUEST['tipo_terceiro'];
    $folha = "";

    $data_inif = explode("/",$data_ini);	
    $ultimo_dia_mes = cal_days_in_month(CAL_GREGORIAN, $data_inif[1], $data_inif[2]);
    $data_fim =   $data_inif[2].'-'.$data_inif[1].'-'.$ultimo_dia_mes;

    $data_ini = ConverteData($data_ini);
    $data_proc = date('Y-m-d');

    if($terceiro == 1 and $tipo_terceiro == 2){

        mysql_query("INSERT INTO rh_folha (parte,data_proc,mes,ano,ferias,data_inicio,data_fim,regiao,projeto,terceiro,tipo_terceiro,user) VALUES 
        ('1','$data_proc','$mes','$ano','$ferias','$data_ini', '$data_fim', '$regiao', '$projeto', '$terceiro', '$tipo_terceiro', '$id_user')") 
        or die ("Erro<br>".mysql_error());

        $folha = mysql_insert_id();

        //-- ENCRIPTOGRAFANDO A VARIAVEL
        $linkcontinue = encrypt("$regiao&$folha"); 
        $linkcontinue = str_replace("+","--",$linkcontinue);
        // -----------------------------	

        //GRAVANDO LOG DE CRIAÇÃO DE FOLHA
        $f->logCriarFolha($folha,$id_user);

        print "<script>
        location.href=\"../../rh/folha/folha2.php?m=1&enc=$linkcontinue\"
        </script>"; exit;
    }

    //VERIFICANDO SE JA EXISTE ALGUMA FOLHA EM ABERTO NO MESMO MES DO MESMO PROJETO SELECIONADO ANTERIORMENTE
    if($terceiro == 2){
        $result = mysql_query("SELECT * FROM rh_folha WHERE projeto = '$projeto' AND mes = '$mes' AND ano = '$ano' AND (status = '2' or  status = '1' ) 
        AND terceiro = '3'");
        $con_result = mysql_num_rows($result);
    }else{
        $con_result = 0;
    }

    //-- ENCRIPTOGRAFANDO A VARIAVEL
    $linkreg = encrypt("$regiao&1"); 
    $linkreg = str_replace("+","--",$linkreg);
    // ----------------------------

    if($con_result >= 1){
        print "<script>
        alert(\"Você precisa FINALIZAR a folha deste mesmo projeto nesse mesmo mes para continuar\");
        location.href=\"../../rh_novaintra/folha/folha.php?tela=1&enc=$linkreg\"
        </script>";
        exit;
    }else{
        //VERIFICANDO CLTS ATIVOS PARA GERAR A FOLHA (10)
        $RSverificaCLT = mysql_query("SELECT * FROM rh_clt WHERE id_projeto = '$projeto' AND status < '60'");
        $row_verificaCLT = mysql_num_rows($RSverificaCLT);

        //VERIFICANDO CLTS JA PROCESSADOS E FINALIZANDOS NO MESMO MES DA FOLHA SELECIONADA
        $RSverificaCLTProc = mysql_query("SELECT * FROM rh_folha_proc WHERE id_projeto = '$projeto' AND mes = '$mes' AND ano='$ano' AND status = '3'");
        $row_verificaCLTProc = mysql_num_rows($RSverificaCLTProc);

        //COMPARA SE TODOS OS CLTS DO MES SELECIONADOS JA ESTÃO EM OUTRA FOLHA JA FINALIZADA
        //if($row_verificaCLT > $row_verificaCLTProc){// CASO ESTE MES JA TENHA FOLHA GERADA.. ELE CRIA UMA OUTRA PARTE DA FOLHA

        //PEGA A ULTIMA PARTE DA FOLHA GERADA
        $result_max = mysql_query("SELECT MAX(parte) FROM rh_folha WHERE projeto = '$projeto' AND mes = '$mes' AND status = '3'");
        $row_max = mysql_fetch_array($result_max);

        $parte = $row_max['0'] + 1;

        $mes = sprintf("%02d",$mes);

        mysql_query("INSERT INTO rh_folha (parte,data_proc,mes,ano,ferias,data_inicio,data_fim,regiao,projeto,terceiro,tipo_terceiro,user) VALUES 
        ('$parte','$data_proc','$mes','$ano','$ferias','$data_ini', '$data_fim', '$regiao', '$projeto', '$terceiro', '$tipo_terceiro', '$id_user')") 
        or die ("Erro<br>".mysql_error());

        $folha = mysql_insert_id();
        //GRAVANDO LOG DE CRIAÇÃO DE FOLHA
        $f->logCriarFolha($folha,$id_user); 


        //-- ENCRIPTOGRAFANDO A VARIAVEL
        $linkcontinue = encrypt("$regiao&$folha"); 
        $linkcontinue = str_replace("+","--",$linkcontinue);
        // -----------------------------	

        print "<script>
        location.href=\"../../rh/folha/folha2.php?m=1&terceiro={$terceiro}&tipoterceiro={$tipo_terceiro}&mes={$mes}&ano={$ano}&enc=$linkcontinue\"
        </script>"; exit;
    }

break;
case 3:            
// ------------------------ DELETANDO FOLHA GERADA ----------------------------//

    $regiao = $_REQUEST['regiao'];
    $folha = $_REQUEST['folha'];

    //DELETANDO O REGISTRO DA TABELA RH_FOLHA
    mysql_query("DELETE FROM rh_folha WHERE id_folha = '$folha' LIMIT 1") or die (mysql_error());

    //DELETANDO OS CLTS DA FOLHA PROCESSADA
    mysql_query("DELETE FROM rh_folha_proc WHERE id_folha = '$folha'");

    //-- ENCRIPTOGRAFANDO A VARIAVEL
    $linkreg = encrypt("$regiao&1"); 
    $linkreg = str_replace("+","--",$linkreg);
    // -----------------------------	

    //GRAVANDO LOG DE EXCLUSÃO DE FOLHA
    $f->logExcluirFolhaAberta($folha,$id_user);

    print "<script>
    location.href=\"../../rh_novaintra/folha/folha.php?tela=1&enc=$linkreg\"
    </script>";
	
} ?>