<?php

/* * *********LISTA DE LOCALIZAÇÃO DE CADA INSTÂNCIA DE CLASS PARA LOG****************** */
/* * *********************************************************************************** */
/* * INCLUIR USUÁRIO NA FOLHA - logIncluirNaFolha (folha2.php)************************** */
/* * EXCLUIR USUARIO DA FOLHA - logRemoveDaFolha - TIPO - 1 - (Folha2.php) - JAVASCRIPT* */
/* * DESFAZER EXCLUSÃO - logRemoveDaFolha - TIPO - 2 - (Folha2.php) - JAVASCRIPT******** */
/* * CRIAR FOLHA - logCriarFolha - (folha.php)****************************************** */
/* * EXCLUIR FOLHA - logExcluirFolhaAberta - (folha.php)******************************** */
/* * FINALIZAR FOLHA - logFecharFolha - (acao_folha.php)******************************** */
/* * DESPROCESSAR FOLHA - logDesprocessaFolha - (desprocessar.php)********************** */

if (empty($_COOKIE['logado'])) {
    print "Efetue o Login<br><a href='www.netsorrindo.com.br/intranet/login.php'>Logar</a> ";
    exit;
}

include "../../conn.php";
include "../../funcoes.php";
include "../../classes/funcionario.php";
include "../../classes_permissoes/acoes.class.php";
include "../../classes/FolhaClass.php";
require_once("../../classes/LogClass.php");

$Func = new funcionario();
$ACOES = new Acoes();
$f = new Folha();
$log = new Log();

$id_user = $_COOKIE['logado'];
$tela = $_REQUEST['tela'];

$sql = "SELECT * FROM funcionario WHERE id_funcionario = '$id_user'";
$result_user = mysql_query($sql, $conn);
$row_user = mysql_fetch_array($result_user);

$result_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_user[id_master]'");
$row_master = mysql_fetch_array($result_master);

$ano = date("Y");
$mesNow = date('m');

$anosConsultaFinalizado = array(date('Y'),date('Y')-1);

/*
  Função para converter a data
  De formato nacional para formato americano.trado
  Muito útil para você inserir data no mysql e visualizar depois data do mysql.
 */

//////////////////////LIBERAÇÃO DE OLERITE//////////////////////
if(isset($_REQUEST['method']) && !empty($_REQUEST['method'])){
     if($_REQUEST['method'] == "verLiberacaoOlerite") {
        
        $retorno = array("status" => 0); 
        $idFolhaOlerite = $_POST['folha'];
        $flagAtualOlerite = $_POST['flagAtual'];
        $valLiberado = 0;
        
        if($flagAtualOlerite == 0) {
            $logText = "Folha ID{$idFolhaOlerite} Liberada.";
        } else {
            $logText = "Folha ID{$idFolhaOlerite} Bloqueada.";
        }
        
        if($flagAtualOlerite == 0){
            $valLiberado = 1;
        }
        
        $sql = "UPDATE rh_folha SET liberado = '{$valLiberado}' WHERE id_folha = {$idFolhaOlerite}";
        $query = mysql_query($sql);
        $log->gravaLog("Folha de Pagamento", $logText);
        if($query){
            $retorno = array("status" => 1, "folha" => $idFolhaOlerite, "flag" => $valLiberado);
        }
        
        echo json_encode($retorno);
        exit();
     }
    
}
////////////////////////////////////////////////////////////////////

function ConverteData($Data) {
    if (strstr($Data, "/")) {//verifica se tem a barra /
        $d = explode("/", $Data); //tira a barra
        $rstData = "$d[2]-$d[1]-$d[0]"; //separa as datas $d[2] = ano $d[1] = mes etc...
        return $rstData;
    } elseif (strstr($Data, "-")) {
        $d = explode("-", $Data);
        $rstData = "$d[2]/$d[1]/$d[0]";
        return $rstData;
    } else {
        return "";
    }
}

switch ($tela) {

//--------------------------------MOSTRANDO A TELA NORMALMENTE-----------------------------------
    case 1:

        //RECEBENDO A VARIAVEL CRIPTOGRAFADA
        $enc = $_REQUEST['enc'];
        $enc = str_replace("--", "+", $enc);
        $link = decrypt($enc);

        $decript = explode("&", $link);

        //$regiao = $decript[0];
        $regiao = $Func->id_regiao;

        $link = "0";
        $enc = "0";
        $decript = "0";
        //RECEBENDO A VARIAVEL CRIPTOGRAFADA

        $qr_regiao = mysql_query("SELECT regiao FROM regioes WHERE id_regiao = '$regiao'");
        $row_regiao = mysql_fetch_assoc($qr_regiao);
        ?>
        <html>
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
                <title>:: Intranet :: Folha de Pagamento</title>
                <link rel="shortcut icon" href="../../favicon.ico" />
                <link href="../../adm/css/estrutura.css" rel="stylesheet" type="text/css" />
                <link href="../../js/jquery.ui.theme.css" rel="stylesheet" type="text/css" />
                <link href="../../js/jquery.ui.datepicker.css" rel="stylesheet" type="text/css" />
                <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css"><!-- -->

                <script type="text/javascript" src="../../js/jquery-1.10.2.min.js"></script>
                <script type="text/javascript" src="../../js/abas_anos.js"></script>
                <script type="text/javascript" src="../../js/jquery.ui.core.js"></script>
                <script type="text/javascript" src="../../js/jquery.ui.widget.js"></script>
                <script type="text/javascript" src="../../js/jquery.ui.datepicker.js"></script>
                <script type="text/javascript" src="../../js/jquery.ui.datepicker-pt-BR.js"></script>
                <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script><!-- -->
                <script type="text/javascript">
                    $(function () {
                        $('#data_ini').datepicker({
                            changeMonth: true,
                            changeYear: true
                                    //        minDate:"-1M",
                                    //        maxDate:"+1M"
                        });

                        /********VALIDAÇÃO DE DATAS DIFERENTES NA FOLHA DE PAGAMENTO*******/
                        $("#mes").change(function () {
                            var mes = $(this).val();
                            var ano = $("#ano").val();
                            $("#mesSelected").val(mes);
                            $("#anoSelected").val(ano);
                        });

                        /********VALIDAÇÃO DE DATAS DIFERENTES NA FOLHA DE PAGAMENTO*******/
                        $("#ano").change(function () {
                            var ano = $(this).val();
                            var mes = $("#mes").val();
                            $("#mesSelected").val(mes);
                            $("#anoSelected").val(ano);
                        });

                        /********VALIDAÇÃO DE DATAS DIFERENTES NA FOLHA DE PAGAMENTO*******/
                        $("#data_ini").change(function () {
                            var data_inicial = $(this).val();
                            var data = data_inicial.split("/");
                            var mesInicio = data[1];
                            var anoInicio = data[2];
                            var mesReferente = $("#mesSelected").val();
                            var anoReferente = $("#anoSelected").val();
                            var data1 = String(mesInicio + "/" + anoInicio);
                            var data2 = String(mesReferente + "/" + anoReferente);

                            if (data1 != data2) {
                                //console.log("Data de Início da folha não corresponde com a competência selecionada");
                                $(".esconderError").removeClass("esconderError").addClass("mensagensError");
                                $("#gerar").addClass("esconderError");
                            } else {
                                //console.log("Datas correspondem");
                                $(".mensagensError").removeClass("mensagensError").addClass("esconderError");
                                $("#gerar").removeClass("esconderError");
                            }

                        });

                        /********VALIDAÇÃO DE DATAS DIFERENTES NA FOLHA DE PAGAMENTO*******/
                        $("#mes").change(function () {
                            var data_inicial = $("#data_ini").val();
                            var data = data_inicial.split("/");
                            var mesInicio = data[1];
                            var anoInicio = data[2];
                            var mesReferente = $("#mesSelected").val();
                            var anoReferente = $("#anoSelected").val();
                            var data1 = String(mesInicio + "/" + anoInicio);
                            var data2 = String(mesReferente + "/" + anoReferente);

                            if (data1 != data2) {
                                //console.log("Data de Início da folha não corresponde com a competência selecionada");
                                $(".esconderError").removeClass("esconderError").addClass("mensagensError");
                                $("#gerar").addClass("esconderError");
                            } else {
                                //console.log("Datas correspondem");
                                $(".mensagensError").removeClass("mensagensError").addClass("esconderError");
                                $("#gerar").removeClass("esconderError");
                            }
                        });

                        /********VALIDAÇÃO DE DATAS DIFERENTES NA FOLHA DE PAGAMENTO*******/
                        $("#ano").change(function () {
                            var data_inicial = $("#data_ini").val();
                            var data = data_inicial.split("/");
                            var mesInicio = data[1];
                            var anoInicio = data[2];
                            var mesReferente = $("#mesSelected").val();
                            var anoReferente = $("#anoSelected").val();
                            var data1 = String(mesInicio + "/" + anoInicio);
                            var data2 = String(mesReferente + "/" + anoReferente);

                            if (data1 != data2) {
                                //console.log("Data de Início da folha não corresponde com a competência selecionada");
                                $(".esconderError").removeClass("esconderError").addClass("mensagensError");
                                $("#gerar").addClass("esconderError");
                            } else {
                                //console.log("Datas correspondem");
                                $(".mensagensError").removeClass("mensagensError").addClass("esconderError");
                                $("#gerar").removeClass("esconderError");
                            }
                        });
                        
                        /////////////LIBERAÇÃO DE OLERITE///////////////
                        
                        $("body").on("click",".libOlerite",function(){
                           var flag = $(this).attr('data-flag');
//                           console.log(flag);
                           var folha = $(this).attr('data-folha');
//                           console.log(folha);
                           
                           $.ajax({
                              url:"",
                              dataType:"json",
                              type:"post",
                              data:{
                                  method:"verLiberacaoOlerite",
                                  folha:folha,
                                  flagAtual:flag
                              },
                              success:function(data){
                                  if(data.status){                               
                                      
                                        if(data.flag == 1){
                                            $(".libOlerite"+data.folha).attr({'title':'Bloquear Holerite'});
                                            $(".libOlerite"+data.folha).attr({'src':'../../imagens/icones/icon-delete.gif'});
                                            $(".libOlerite"+data.folha).attr({'data-flag':1});
                                        }else{
                                            $(".libOlerite"+data.folha).attr({'title':'Liberar Holerite'});
                                            $(".libOlerite"+data.folha).attr({'src':'../../imagens/icones/icon-accept-disabled.gif'});
                                            $(".libOlerite"+data.folha).attr({'data-flag':0});
                                        }
                                  }                                  
                              }
                              
                           });
                        });
                        
                        /////////////////////////////////////////////
                        
                        $( document ).tooltip();
                    });

                    function MM_preloadImages() {
                        var d = document;
                        if (d.images) {
                            if (!d.MM_p)
                                d.MM_p = new Array();
                            var i, j = d.MM_p.length, a = MM_preloadImages.arguments;
                            for (i = 0; i < a.length; i++)
                                if (a[i].indexOf("#") != 0) {
                                    d.MM_p[j] = new Image;
                                    d.MM_p[j++].src = a[i];
                                }
                        }
                    }
                </script>
                <style>

                    fieldset {
                        padding:2px;
                        margin-top:30px;

                    }

                    table.folhas {
                        width:100%;
                        font-weight:bold;
                        margin:0px auto;
                        font-size:11px;
                        text-align:center;
                    }

                    .mensagensError{
                        width: 606px;
                        height: 40px;
                        border: 1px solid #F4BCBC;;
                        margin: 0 auto;
                        background: #FFDADA;
                        display: block;
                    }

                    .mensagensError p{
                        margin-top: 15px;
                        font-size: 12px;
                        color: red;
                    }

                    .esconderError{
                        display: none;
                    }

                </style>
            </head>
        </head>
        <body onLoad="MM_preloadImages('imagens/processar2.gif')">
            <div id="corpo">
                <div id="conteudo">
                    <input type="hidden" name="mesSelected" id="mesSelected" value="<?php echo $mesNow; ?>" />   
                    <input type="hidden" name="anoSelected" id="anoSelected" value="<?php echo $ano; ?>" />   

                    <div style="float:right;"><?php include('../../reportar_erro.php'); ?></div>       
                    <div class="right"></div>             

                    <br /><img src="../../imagens/logomaster<?= $row_user['id_master'] ?>.gif" width="110" height="79">

                    <h3>GERENCIAMENTO DE FOLHA DE PAGAMENTO</h3>

                    <?php if ($ACOES->permissoes_folha(4, $regiao)) { ?>

                        <table width="90%" border="0" align="center" style="margin-top:25px;" class="relacao">
                            <tr>
                                <td colspan="3" style="font-weight:bold; background-color:#555; color:#FFF; text-align:center; padding:8px;">
                                    REGI&Atilde;O DE <?php echo strtoupper(strtr($row_regiao['regiao'], "áéíóúâêôãõàèìòùç", "ÁÉÍÓÚÂÊÔÃÕÀÈÌÒÙÇ")); ?>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3" align="center" valign="middle" bgcolor="#FAFAFA" class="title">
                                    <form action="folha.php" method="post" name="form1"  onSubmit="return validaForm()">
                                        <br>
                                        <table  border="0" cellspacing="0" cellpadding="0" style="width:70%; border:solid 1px #DDD; background-color:#EEE;" class="relacao">
                                            <tr>
                                                <td align="right" valign="middle" width="28%" class="linha">Projeto:</td>
                                                <td width="72%" height="34" colspan="2" valign="middle" class="linha" align="left">&nbsp;
                                                    <select name="projeto" id="projeto" class='campotexto' >
                                                        <?php
                                                        $result_pro = mysql_query("SELECT * FROM projeto WHERE id_regiao = '$regiao'");
                                                        $i = "0";
                                                        while ($row_pro = mysql_fetch_array($result_pro)) {
                                                            echo "<option value='$row_pro[0]'>$row_pro[nome]</option>";
                                                            $projetos_regi[$i] = $row_pro[0];
                                                            $i ++;
                                                        }
                                                        ?>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td align="right" valign="middle" class="linha">M&ecirc;s de Refer&ecirc;ncia:</td>
                                                <td height="34" class="linha" align="left">&nbsp;
                                                    <select name="mes" id="mes" class='campotexto' >
                                                        <?php
                                                        $mesNow = date('m');
                                                        $result_meses = mysql_query("SELECT * FROM ano_meses");
                                                        while ($row_meses = mysql_fetch_array($result_meses)) {
                                                            if ($mesNow == $row_meses['num_mes']) {
                                                                echo "<option value=$row_meses[num_mes] selected>$row_meses[nome_mes]</option>";
                                                            } else {
                                                                echo "<option value=$row_meses[num_mes]>$row_meses[nome_mes]</option>";
                                                            }
                                                        }

                                                        $link = "folha.php?id=10&id_projeto=$id_projeto&regiao=$regiao";
                                                        ?>
                                                    </select>
                                                    de 
                                                    <select id="ano" name="ano" class="campotexto">
                                                        <?php
                                                        for ($i = 2007; $i <= date('Y') + 1; $i ++) {
                                                            if ($i == date('Y')) {
                                                                echo '<option value="' . $i . '" selected>' . $i . '</option>';
                                                            } else {
                                                                echo '<option value="' . $i . '" >' . $i . '</option>';
                                                            }
                                                        }
                                                        ?>
                                                    </select>

                                                </td>
                                            </tr>
                                            <tr>
                                                <td height="34" align="right" valign="middle" class="linha">Inicio da Folha:</td>
                                                <td height="34" align="left">&nbsp;
                                                    <input name="data_ini" type="text" id="data_ini" size="11" class="campotexto" maxlength="10"
                                                           onKeyUp="mascara_data(this)">
                                                </td>
                                            </tr>
                                            <!--<tr>
                                              <td height="34" align="right" valign="middle" class="linha">F&eacute;rias:</td>
                                              <td height="34" class="linha">
                                                  <label><input type="radio" value="1" name="ferias"> sim</label>&nbsp;&nbsp;
                                                  <label><input type="radio" value="0" name="ferias" checked> não</label>	
                                              </td>
                                            </tr>
                                            -->
                                            <tr>
                                                <td height="34" align="right" valign="middle" class="linha">D&eacute;cimo Terceiro:</td>
                                                <td height="34" class="linha" align="left">
                                                    <label><input type="radio" value="1" name="terceiro" onClick="document.all.linhatipo.style.display = ''"> sim</label>&nbsp;&nbsp;
                                                    <label><input type="radio" value="2" name="terceiro" checked onClick="document.all.linhatipo.style.display = 'none'"> não</label></td>
                                            </tr>
                                            <tr style="display:none" id="linhatipo">
                                                <td height="34" align="right" valign="middle" class="linha">Tipo de Pagamento:</td>
                                                <td height="34" class="linha" align="left">&nbsp;
                                                    <select name="tipo_terceiro" id="tipo_terceiro">
                                                        <option value="1">PRIMEIRA PARCELA</option>
                                                        <option value="2">SEGUNDA PARCELA</option>
                                                        <option value="3" selected="selected">INTEGRAL</option>
                                                    </select>
                                                    <span id="spanteste"></span>
                                                </td>
                                            </tr>
                                        </table>
                                        <br>
                                        <input type="hidden" name="regiao" id="regiao" value="<?= $regiao ?>">
                                        <input type="hidden" name="tela" id="tela" value="2">
                                        <input type="hidden" name="id_usuario" value="<?= $id_user ?>">
                                        <input name="gerar" type="submit" id="gerar" value="GERAR FOLHA" />
                                        <br>
                                    </form>
                                </td>
                            </tr>
                        </table>

                        <!-- MENSAGEM DE ERRO  -->
                        <div class="esconderError"><p>Data de Início da folha não corresponde com a competência selecionada</p></div> 

                    <?php
                    } else {


                        $result_pro = mysql_query("SELECT * FROM projeto WHERE id_regiao = '$regiao'");
                        $i = "0";
                        while ($row_pro = mysql_fetch_array($result_pro)) {
                            echo "<option value='$row_pro[0]'>$row_pro[nome]</option>";
                            $projetos_regi[$i] = $row_pro[0];
                            $i ++;
                        }
                    }///FIM PERMISSAO GERAR FOLHA
                    ?>              


                    <fieldset>

                        <h3    class="titulo_folha_andamento">  FOLHAS EM ANDAMENTO</h3>


                        <?php
                        $meses = array('Erro', 'Janeiro', 'Fevereiro', 'Mar&ccedil;o', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro');

                        $cores = array('#E3ECE3', '#ECF2EC', '#ECF3F6', '#F5F9FA', '#FEF9EB', '#FFFDF7');
                        $projetos_flip = array_flip($projetos_regi);

                        print "<table width='100%' border='0' cellspacing='0' cellpadding='0' class='relacao2'>";

                        $result_folhas = mysql_query("SELECT *,date_format(data_inicio, '%d/%m/%Y')as data_inicio,date_format(data_fim, '%d/%m/%Y')as data_fim 
			FROM rh_folha WHERE regiao = '$regiao' AND status = '1' ORDER BY projeto,mes");


                        if (mysql_num_rows($result_folhas) != 0) {

                            while ($row_folhas = mysql_fetch_array($result_folhas)) {

                                $qr_total = mysql_query("SELECT * FROM rh_folha_proc WHERE status = '1' AND id_folha = '$row_folhas[id_folha]'");
                                $total = mysql_num_rows($qr_total);

                                //-- ENCRIPTOGRAFANDO A VARIAVEL
                                $linkreg = encrypt("$regiao&$row_folhas[0]");
                                $linkreg = str_replace("+", "--", $linkreg);
                                // -----------------------------

                                $result_pro = mysql_query("SELECT id_projeto,nome,tema FROM projeto WHERE id_projeto = '$row_folhas[projeto]'");
                                $row_pro = mysql_fetch_array($result_pro);

                                $id_projeto_agora = $row_pro['0'];
                                $num_cor = $projetos_flip[$id_projeto_agora];
                                $cor_agora = $cores[$num_cor];

                                $Func->MostraUser($row_folhas['user']);
                                $nomefun = $Func->nome1;

                                print "
								<tr bgcolor='$cor_agora' height='34'>
								  <td width='5%' align='center'><a href='folha2.php?m=1&enc=$linkreg'>";

                                if ($ACOES->permissoes_folha(75, $regiao)) {
                                    echo "<img src='imagens/profolha.gif' border='0' align='absmiddle' alt='PROCESSAR'></a></td>";
                                }

                                echo"  <td width='40%'>$row_folhas[0] - $row_pro[nome]</td>
                                            <td width='10%'>$nomefun</td>
                                            <td width='20%'><b>$mes_da_folha</b></td>
                                            <td width='25%'>$row_folhas[data_inicio] at&eacute; $row_folhas[data_fim]</td>
                                            <td width='5%' align='center'>";



                                ///permissão para DELETAR FOLHA
                                if ($ACOES->permissoes_folha(6, $regiao)) {
                                    if($_COOKIE['logado'] != 395){
                                    echo " <a href='#' onClick='confirm_entry($regiao,$row_folhas[0])'>
                                                                            <img src='imagens/delfolha.gif' border='0' align='absmiddle' alt='DELETAR' data-key='{$row_folhas[0]}'></a>";
                                    }
                                }

                                echo '</td>
								</tr>';
                            }
                            ?>
                            </table>
                            <?php
                            $meses = array('OPS', 'Janeiro', 'Fevereiro', 'Mar&ccedil;o', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro');

                            $cores = array('#E3ECE3', '#ECF2EC', '#ECF3F6', '#F5F9FA', '#FEF9EB', '#FFFDF7');
                            $projetos_flip = array_flip($projetos_regi);
                        }



                        print " <table width='100%' border='0' cellspacing='0' cellpadding='0' class='relacao2'>";

                        $result_folhas2 = mysql_query("SELECT *,date_format(data_inicio, '%d/%m/%Y')as data_inicio,date_format(data_fim, '%d/%m/%Y')as data_fim 
							FROM rh_folha WHERE regiao = '$regiao' AND status = '2' ORDER BY projeto,mes");
                        while ($row_folhas2 = mysql_fetch_array($result_folhas2)) {

                            $qr_total = mysql_query("SELECT * FROM rh_folha_proc WHERE status = '2' AND id_folha = '$row_folhas2[id_folha]'");
                            $total = mysql_num_rows($qr_total);

                            //-- ENCRIPTOGRAFANDO A VARIAVEL
                            $linkreg2 = encrypt("$regiao&$row_folhas2[0]");
                            $linkreg2 = str_replace("+", "--", $linkreg2);
                            // -----------------------------

                            $result_pro2 = mysql_query("SELECT id_projeto,nome,tema FROM projeto WHERE id_projeto = '$row_folhas2[projeto]'");
                            $row_pro2 = mysql_fetch_array($result_pro2);

                            $mes_int2 = (int) $row_folhas2['mes'];

                            $mes_da_folha2 = $meses[$mes_int2];

                            $id_projeto_agora2 = $row_pro2['0'];
                            $num_cor2 = $projetos_flip[$id_projeto_agora2];
                            $cor_agora2 = $cores[$num_cor2];

                            $Func->MostraUser($row_folhas2['user']);
                            $nomefun = $Func->nome1;

                            if ($row_folhas2['terceiro'] == 1) {
                                switch ($row_folhas2['tipo_terceiro']) {
                                    case 1:
                                        $exibi = "<b>13º Primeira parcela</b>";
                                        break;
                                    case 2:
                                        $exibi = "<b>13º Segunda parcela</b>";
                                        break;
                                    case 3:
                                        $exibi = "<b>13º Integral</b>";
                                        break;
                                }
                            } else {
                                $exibi = "<b>$mes_da_folha2 - $row_folhas2[parte]</b>";
                            }

                            print "
                                    <tr bgcolor='$cor_agora2' height='34'>
                                      <td width='5%' align='center'><a href='sintetica.php?enc=$linkreg2'>
                                      <img src='imagens/verfolha.gif' border='0' align='absmiddle' title='VISUALIZAR FOLHA $row_folhas2[0]'></a>";

                            // if($_COOKIE['logado'] == 87 or $_COOKIE['logado'] == 82 or $_COOKIE['logado'] == 9 ){
                            if ($_COOKIE['logado'] == 87) {
                                print "

                                    <a href='sintetica_teste.php?enc=$linkreg2'>
                                      <img src='../../imagens/ver_folha_horista.png' border='0' align='absmiddle' title='TESTE HORISTA FOLHA $row_folhas2[0]'></a>";
                            }



                            echo "</td>
                                    <td width='25%'>$row_folhas2[0] - $row_pro2[nome]</td>
                                    <td width='10%'><b>$nomefun</b></td>
                                    <td width='15%'>$exibi</td>
                                    <td width='30%'>$row_folhas2[data_inicio] at&eacute; $row_folhas2[data_fim]</td>
                                    <td width='10%'>CLT's: $total</td>
                                    <td width='5%'>";

                            ///permissão para DELETAR FOLHA
                            $verifica_acoes = mysql_num_rows($qr_acoes_assoc = mysql_query("SELECT * FROM funcionario_acoes_assoc WHERE id_funcionario = '$_COOKIE[logado]' AND acoes_id = '5' AND id_regiao = '$regiao'"));
                            if ($verifica_acoes != 0) {
                                if($_COOKIE['logado'] != 395){
                                    echo "<a href='#' onClick='confirm_entry($regiao,$row_folhas2[0])'>  <img src='imagens/delfolha.gif' border='0' align='absmiddle' alt='DELETAR'/> </a>";
                                }
                            }
                        }


                        echo '</td>
					</tr>		
				</table>';
                        ?>

                        <a href="folha.php"></a>
                    </fieldset>          

                    <fieldset>

                        <h3  class="titulo_folha_finalizada"> FOLHAS FINALIZADAS</h3>

                        <?php
                        $meses = array('OPS', 'Janeiro', 'Fevereiro', 'Mar&ccedil;o', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro');
                        $cores = array('#E3ECE3', '#ECF2EC', '#ECF3F6', '#F5F9FA', '#FEF9EB', '#FFFDF7');
                        $projetos_flip = array_flip($projetos_regi);
                        $cont_linhas = 0;

                        $result_pro3 = mysql_query("SELECT id_projeto,nome,tema FROM projeto WHERE id_regiao = '$regiao'");
                        while ($row_pro3 = mysql_fetch_array($result_pro3)):

                            $result_folhas3 = mysql_query("SELECT * , date_format(data_inicio, '%d/%m/%Y') AS data_inicio,date_format(data_fim, '%d/%m/%Y') AS data_fim FROM rh_folha WHERE regiao = '$regiao' AND status = '3' AND  projeto = '$row_pro3[id_projeto]' AND ano IN (".  implode(",", $anosConsultaFinalizado).") ORDER BY projeto,ano,mes  ASC");


                            if (mysql_num_rows($result_folhas3) != 0) {

                                echo '<h3 class="titulo_projeto">' . $row_pro3['nome'] . '</h3>';


                                while ($row_folhas3 = mysql_fetch_array($result_folhas3)) {

                                    //$qr_total = mysql_query("SELECT * FROM rh_folha_proc WHERE status = '3' AND id_folha = '$row_folhas3[id_folha]'");
                                    //$total = mysql_num_rows($qr_total);
                                    $total = $row_folhas3['clts'];


                                    $cont_linhas++;

                                    if ($ultimo_ano != $row_folhas3['ano']) {

                                        $total_folhas_ano = mysql_num_rows(mysql_query("SELECT * , date_format(data_inicio, '%d/%m/%Y') AS data_inicio,date_format(data_fim, '%d/%m/%Y') AS data_fim FROM rh_folha WHERE regiao = '$regiao' AND status = '3' AND ano = '$row_folhas3[ano]' AND projeto = '$row_pro3[id_projeto]' ORDER BY projeto,ano,mes"));
                                        ?>


                                        <a href="#" class="titulo_ano" ><?php echo $row_folhas3['ano']; ?></a>

                                        <?php $display = ($row_folhas3['ano'] == date('Y')) ? 'block' : 'none'; ?>

                                        <table width='100%' border='0' cellspacing='0' cellpadding='4' id='tabfin' style='display:<?php echo $display; ?>' class="folhas">


                                        <?php
                                        }


                                        $ultimo_projeto = $row_folhas3['projeto'];
                                        $ultimo_ano = $row_folhas3['ano'];

                                        // ENCRIPTOGRAFANDO A VARIAVEL
                                        $linkreg3 = str_replace("+", "--", encrypt("$regiao&$row_folhas3[0]"));
//                                        echo $regiao;
//                                        echo $row_folha3[0];
                                        //


                                        $mes_int3 = (int) $row_folhas3['mes'];

                                        $mes_da_folha3 = $meses[$mes_int3];

                                        $id_projeto_agora3 = $row_pro3['0'];
                                        $num_cor3 = $projetos_flip[$id_projeto_agora3];
                                        $cor_agora3 = $cores[$num_cor3];

                                        $Func->MostraUser($row_folhas3['user']);
                                        $nomefun = $Func->nome1;

                                        if ($row_folhas3['terceiro'] == 1) {
                                            switch ($row_folhas3['tipo_terceiro']) {
                                                case 1:
                                                    $exibicao = "<b>13º Primeira parcela</b>";
                                                    break;
                                                case 2:
                                                    $exibicao = "<b>13º Segunda parcela</b>";
                                                    break;
                                                case 3:
                                                    $exibicao = "<b>13º Integral</b>";
                                                    break;
                                            }
                                        } else {
                                            $exibicao = "<b>$mes_da_folha3</b>";
                                        }
                                        ?>

                                        <tr bgcolor="<?php echo $cor_agora3; ?>">
                                            <td width="5%" align="center">
                                                <?php
                                                ///permissão para VER FOLHA
                                                if ($ACOES->permissoes_folha(5, $regiao)) {

                                                    echo '<a href="ver_folha.php?enc=' . $linkreg3 . '"><img src="imagens/verfolha.gif" alt="VISUALIZAR"></a>';
                                                }
                                                    
//                                                echo $liberado;
                                                $classOlerite = $row_folhas3['id_folha'];;
                                                if($row_folhas3['liberado'] == 1) {
                                                    $liberado = "Bloquear";
                                                    $tolTipOl = "Bloquear Holerite";
                                                    $iconOle = "../../imagens/icones/icon-delete.gif";
                                                } else {
                                                    $liberado = "Liberar";
                                                    $tolTipOl = "Liberar Holerite";
                                                    $iconOle = "../../imagens/icones/icon-accept-disabled.gif";
                                                }
                                                
                                                
                                                ?>
                                            </td>
                                            <td width="25%"><?php echo $row_folhas3['id_folha'] . ' - ' . $row_pro3['nome']; ?></td>
                                            <td width="11%"><?php echo $nomefun; ?></td>
                                            <td width="12%"><?php echo $exibicao; ?></td>
                                            <td width="7%">
                                                <?php if($_COOKIE['logado'] != 395){ ?>
                                                <?php echo "<img style='cursor:pointer;' title='".$tolTipOl."' src='{$iconOle}' data-folha='".$row_folhas3['id_folha']."' data-flag='".$row_folhas3['liberado']."' class='libOlerite libOlerite".$row_folhas3['id_folha']."'>"; ?>
                                                <?php } ?>
                                            </td>
                                            <!--<td width="7%"><?php // echo '<a title="'.$tolTipOl.'">'?><?php // echo "<span data-folha='".$row_folhas3['id_folha']."' data-flag='".$row_folhas3['liberado']."' class='libOlerite libOlerite".$row_folhas3['id_folha']."'>".$liberado."</span>"; ?></a></td>-->
                                            <td width="30%"><?php echo $row_folhas3['data_inicio'] . ' at&eacute; ' . $row_folhas3['data_fim']; ?></td>
                                            <td width="10%"><?php echo $total; ?> CLTs</td>
                                            <td>

                                                <?php
                                                // trava solicitada pelo SABINO, feito por MAX (permitir desprocessar somente folhas do mês atual)
                                                 // trava solicitada pelo SABINO, feito por Rodrigo Antunes (permitir desprocessar somente folhas do ano atual 17/02/2017)
                                                $data_folhaFin = $row_folhas3['ano'];
                                                $data_atualFin = 2016;


                                                $query_verifica = "SELECT * , date_format(data_inicio, '%d/%m/%Y') AS data_inicio,date_format(data_fim, '%d/%m/%Y') AS data_fim FROM rh_folha WHERE regiao = '{$regiao}' AND status = '3' AND terceiro = '2' AND  projeto = '{$row_folhas3['projeto']}' ORDER BY id_folha DESC LIMIT 1 ";
                                                $sql_verifica = mysql_query($query_verifica) or die("Erro ao verificar folha");
                                                $id_folha_ultima = 0;
                                                while ($rows_verifica = mysql_fetch_assoc($sql_verifica)) {
                                                    $id_folha_ultima = $rows_verifica['id_folha'];
                                                }

                                                if ($data_folhaFin > $data_atualFin){
                                                    // permissão para DESPROCESSAR folha
//                                                    if ($ACOES->permissoes_folha(8, $regiao)) {
                                                        ?>
                                                        <?php if($_COOKIE['logado'] != 395){ ?>                                                        
                                                        <a href="desprocessar.php?folha=<?php echo $row_folhas3['id_folha']; ?>" title="Desprocessar Folha" onClick="return window.confirm('Você tem certeza que quer desprocessar esta folha?');"><img src="../imagensrh/deletar.gif" /></a>
                                                        <?php } ?>
                                                        <?php
//                                                    }
                                              }
                                                ?>

                                            </td>
                                        </tr>

                                        <?php
                                        if ($cont_linhas == $total_folhas_ano) {
                                            $cont_linhas = 0;

                                            echo '
                                                <tr height="50">
                                                    <td>&nbsp;</td>
                                                </tr>										
                                                </table>';
                                        }
                                    }
                                }
                                unset($ultimo_ano);
                            endwhile;
                            ?>

                    </fieldset>

                </div>
            </div>

        </body>
        </html>

        <script language="javascript">
        
            function validaForm() {

                d = document.form1;

                if (d.data_ini.value == "") {
                    alert("A data deve ser preenchida!");
                    d.data_ini.focus();
                    return false;
                }

                return true;
            }


            function mascara_data(d) {

                var mydata = '';
                data = d.value;
                mydata = mydata + data;
                if (mydata.length == 2) {
                    mydata = mydata + '/';
                    d.value = mydata;
                }
                if (mydata.length == 5) {
                    mydata = mydata + '/';
                    d.value = mydata;
                }
                if (mydata.length == 10) {
                    verifica_data(d);
                }
            }

            function verifica_data(d) {

                dia = (d.value.substring(0, 2));
                mes = (d.value.substring(3, 5));
                ano = (d.value.substring(6, 10));


                situacao = "";
                // verifica o dia valido para cada mes  
                if ((dia < 01) || (dia < 01 || dia > 30) && (mes == 04 || mes == 06 || mes == 09 || mes == 11) || dia > 31) {
                    situacao = "falsa";
                }

                // verifica se o mes e valido  
                if (mes < 01 || mes > 12) {
                    situacao = "falsa";
                }

                // verifica se e ano bissexto  
                if (mes == 2 && (dia < 01 || dia > 29 || (dia > 28 && (parseInt(ano / 4) != ano / 4)))) {
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

            function confirm_entry(a, b){
            var Regiao = a;
                var Folha = b;
                input_box = confirm("Deseja realmente DELETAR?");
                if (input_box == true){
                    // Output when OK is clicked
                    // alert (\"You clicked OK\"); 
                    location.href = "folha.php?tela=3&regiao=" + Regiao + "&folha=" + Folha;
                } else{
                    // Output when Cancel is clicked
                    // alert (\"You clicked cancel\");
                }
            }
            
        //-->
        </script>
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

        $data_inif = explode("/", $data_ini);
        $ultimo_dia_mes = cal_days_in_month(CAL_GREGORIAN, $data_inif[1], $data_inif[2]);
        $data_fim = $data_inif[2] . '-' . $data_inif[1] . '-' . $ultimo_dia_mes;

        $data_ini = ConverteData($data_ini);
        $data_proc = date('Y-m-d');

        if ($terceiro == 1 and $tipo_terceiro == 2) {

            mysql_query("INSERT INTO rh_folha (parte,data_proc,mes,ano,ferias,data_inicio,data_fim,regiao,projeto,terceiro,tipo_terceiro,user) VALUES 
		('1','$data_proc','$mes','$ano','$ferias','$data_ini', '$data_fim', '$regiao', '$projeto', '$terceiro', '$tipo_terceiro', '$id_user')")
                    or die("Erro<br>" . mysql_error());

            $folha = mysql_insert_id();
            
            //-- ENCRIPTOGRAFANDO A VARIAVEL
            $linkcontinue = encrypt("$regiao&$folha");
            $linkcontinue = str_replace("+", "--", $linkcontinue);
            // -----------------------------	
            //GRAVANDO LOG DE CRIAÇÃO DE FOLHA
            $f->logCriarFolha($folha, $id_user);
            $log->gravaLog('Folha de Pgto.', "Cadastro de Folha de Pgto CLT de 13º: ID{$folha}");
                        
            print "<script>
		location.href=\"folha2.php?m=1&enc=$linkcontinue\"
		</script>";

            exit;
        }



        //VERIFICANDO SE JA EXISTE ALGUMA FOLHA EM ABERTO NO MESMO MES DO MESMO PROJETO SELECIONADO ANTERIORMENTE
        if ($terceiro == 2) {
            $result = mysql_query("SELECT * FROM rh_folha WHERE projeto = '$projeto' AND mes = '$mes' AND ano = '$ano' AND (status = '2' or  status = '1' ) 
            AND terceiro = '3'");
            $con_result = mysql_num_rows($result);
        } else {
            $con_result = 0;
        }

        //-- ENCRIPTOGRAFANDO A VARIAVEL
        $linkreg = encrypt("$regiao&1");
        $linkreg = str_replace("+", "--", $linkreg);
        // ----------------------------


        if ($con_result >= 1) {

            print "<script>
		alert(\"Você precisa FINALIZAR a folha deste mesmo projeto nesse mesmo mes para continuar\");
		location.href=\"folha.php?tela=1&enc=$linkreg\"
		</script>";
            exit;
        } else {
            //VERIFICANDO CLTS ATIVOS PARA GERAR A FOLHA (10)
            $RSverificaCLT = mysql_query("SELECT * FROM rh_clt WHERE id_projeto = '$projeto' AND status < '60'");
            $row_verificaCLT = mysql_num_rows($RSverificaCLT);
            //VERIFICANDO CLTS JA PROCESSADOS E FINALIZANDOS NO MESMO MES DA FOLHA SELECIONADA
            $RSverificaCLTProc = mysql_query("SELECT * FROM rh_folha_proc WHERE id_projeto = '$projeto' AND mes = '$mes' AND ano='$ano' AND status = '2'");
            $row_verificaCLTProc = mysql_num_rows($RSverificaCLTProc);
            //COMPARA SE TODOS OS CLTS DO MES SELECIONADOS JA ESTÃO EM OUTRA FOLHA JA FINALIZADA
            //if($row_verificaCLT > $row_verificaCLTProc){// CASO ESTE MES JA TENHA FOLHA GERADA.. ELE CRIA UMA OUTRA PARTE DA FOLHA
            //PEGA A ULTIMA PARTE DA FOLHA GERADA
            $result_max = mysql_query("SELECT MAX(parte) FROM rh_folha WHERE projeto = '$projeto' AND mes = '$mes' AND status = '3'");
            $row_max = mysql_fetch_array($result_max);

            $parte = $row_max['0'] + 1;

            $mes = sprintf("%02d", $mes);

            mysql_query("INSERT INTO rh_folha (parte,data_proc,mes,ano,ferias,data_inicio,data_fim,regiao,projeto,terceiro,tipo_terceiro,user) VALUES 
		('$parte','$data_proc','$mes','$ano','$ferias','$data_ini', '$data_fim', '$regiao', '$projeto', '$terceiro', '$tipo_terceiro', '$id_user')")
                    or die("Erro<br>" . mysql_error());

            $folha = mysql_insert_id();
            //GRAVANDO LOG DE CRIAÇÃO DE FOLHA
            $f->logCriarFolha($folha, $id_user);
            $log->gravaLog('Folha de Pgto.', "Cadastro de Folha de Pgto. CLT: ID{$folha}");

            //-- ENCRIPTOGRAFANDO A VARIAVEL
            $linkcontinue = encrypt("$regiao&$folha");
            $linkcontinue = str_replace("+", "--", $linkcontinue);
            // -----------------------------	

            print "<script>
                location.href=\"folha2.php?m=1&terceiro={$terceiro}&tipoterceiro={$tipo_terceiro}&mes={$mes}&ano={$ano}&enc=$linkcontinue\"
		</script>";

            exit;
        }

        break;
    case 3:
        // ------------------------ DELETANDO FOLHA GERADA ----------------------------//

        $regiao = $_REQUEST['regiao'];
        $folha = $_REQUEST['folha'];

        //DELETANDO O REGISTRO DA TABELA RH_FOLHA
        mysql_query("DELETE FROM rh_folha WHERE id_folha = '$folha' LIMIT 1") or die(mysql_error());

        //DELETANDO OS CLTS DA FOLHA PROCESSADA
        mysql_query("DELETE FROM rh_folha_proc WHERE id_folha = '$folha'");

        //-- ENCRIPTOGRAFANDO A VARIAVEL
        $linkreg = encrypt("$regiao&1");
        $linkreg = str_replace("+", "--", $linkreg);
        // -----------------------------	
        //GRAVANDO LOG DE EXCLUSÃO DE FOLHA
        $f->logExcluirFolhaAberta($folha, $id_user);
        $log->gravaLog('Folha de Pgto.', "Exclusão de Folha de Pgto. CLT: ID{$folha}");
        
        /**
         * 
         */
        $verificaMovEmFolha = "SELECT A.ids_movimentos_estatisticas FROM rh_folha AS A WHERE A.id_folha = '{$folha}'";
        $sqlVerificaMovEmFolha = mysql_query($verificaMovEmFolha) or die("Erro ao Selecionar Ids Estatisticas");
        $idsEstatisticas = "";
        while($rows = mysql_fetch_assoc($sqlVerificaMovEmFolha)){
            $idsEstatisticas = $rows['ids_movimentos_estatisticas'];
        }
        
        $rcemoveMovs = mysql_query("DELETE FROM rh_movimentos_clt WHERE id_movimento IN({$idsEstatisticas}) AND lancado_folha = 1");
            

        print "<script>
	location.href=\"folha.php?tela=1&enc=$linkreg\"
	</script>";
}
?>
</body>
</html>
