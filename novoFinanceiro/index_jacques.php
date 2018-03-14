<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
    exit;
}

include ("include/restricoes.php");
include "../conn.php";
include "../funcoes.php";
include "../classes_permissoes/acoes.class.php";
include("../classes_permissoes/regioes.class.php");

$obj_regiao = new Regioes();
$acoes = new Acoes();

function format_date($data) {
    return implode('/', array_reverse(explode('-', $data)));
}

function link_editar_saida($tipo_saida, $id_saida, $link_enc) {
    ////ESTE ARRAY CONTÉM OS TIPOS DE SAÌDA QUE SÒ PODEM SER EDITADO A DATA DE VENCIMENTO Ex: RESCISÔES QUE VEM DOS "PAGAMENTOS" NA 
    //GESTÃO DE RH
    $array_nao_editaveis = array(167, 175, 168, 169, 260);


    if ($tipo_saida > 4) {

        if (!in_array($tipo_saida, $array_nao_editaveis)) {

            $editar = "<a href=\"cad_edit_saida.php?id=$id_saida&tipo=saida&enc=$link_enc&rel&keepThis=true&TB_iframe=true&width=800&height=600\" class=\"thickbox\"> <img src='../imagens/icone_lapis.png' width='16' height='16' border='0' title='EDITAR SAÍDA'/></a>";
        } else {
            $editar = "<a href=\"editar_data.php?id=$id_saida&tipo=saida&enc=$link_enc\"  onclick=\"return hs.htmlExpand(this, { objectType: 'iframe', width: 650 } )\"><img src='../imagens/icone_lapis.png' width='16' height='16' border='0' title='EDITAR SAÍDA'/></a>";
        }
    } else {

        $editar = "<a href='view/editar.saida.naopaga.php?id=$id_saida&tipo=saida'  onclick=\"return hs.htmlExpand(this, { objectType: 'iframe' } )\" ><img src='image/editar.gif' width='16' height='16' border='0'></a>";
    }
    return $editar;
}

if (isset($_GET['encriptar'])) {

    $link_enc = encrypt($_GET['encriptar']);
    $link_enc = str_replace('+', '--', $link_enc);
    echo $link_enc;
    exit();
}

//$regiao = $_GET['regiao'];
// RECEBENDO A VARIAVEL CRIPTOGRAFADA
list($regiao) = explode('&', decrypt(str_replace('--', '+', $_REQUEST['enc'])));


$qr_regiao = mysql_query("SELECT id_regiao,regiao,id_master FROM regioes WHERE id_regiao = '$regiao'");
$rw_regiao = mysql_fetch_array($qr_regiao);


$query_funcionario = mysql_query("SELECT id_funcionario, nome, tipo_usuario,id_master,id_regiao FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]'");
$row_funcionario = mysql_fetch_array($query_funcionario);
$tipo_user = $row_funcionario['tipo_usuario'];

$id_master = $row_funcionario['id_master'];

if ($rw_regiao['id_master'] != $row_funcionario['id_master']) {

    $link_enc = encrypt($row_funcionario['id_regiao']);
    $link_enc = str_replace('+', '--', $link_enc);
    header("Location:" . $_SERVER['PHP_SELF'] . "?enc=$link_enc");
    exit();
}


/* Controle de combustivel */
if (!empty($_REQUEST['apro'])) {
    $apro = $_REQUEST['apro'];
    $vale = $_REQUEST['vale'];
    $valor = $_REQUEST['valor'];
    $regiao = $_REQUEST['regiao'];
    $idComb = $_REQUEST['idcomb'];
    $dataCad = date('Y-m-d');
    if ($apro == 1) {
        mysql_query("UPDATE fr_combustivel SET status_reg = '2', data_libe = '$dataCad', numero='$vale', user_libe = '$id_user' WHERE 
		id_combustivel = '$idComb'");
        $link = "../frota/printcombustivel.php?com=$idComb&regiao=$regiao";
    } else {
        mysql_query("UPDATE fr_combustivel SET status_reg = '0', data_libe = '$dataCad', user_libe = '$id_user' WHERE id_combustivel = '$idComb'");
        $link = "index.php?regiao=$regiao";
    }
    print "<script>
	location.href=\"$link\";
	</script>";
    exit;
}

/**/
if ($_REQUEST['method'] == "destruirSession") {
    $return = array("staus" => true);
    unset($_SESSION['msgError']);
    json_encode($return);
    exit;
}


$link_enc = encrypt($regiao);
$link_enc = str_replace('+', '--', $link_enc);
/* FIM do CONT|ROLE de COMBUSTIVEL */

// Bloqueio Administração
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
        <title>Financeiro</title> 
        <script type="text/javascript" src="../js/highslide-with-html.js"></script>
        <link rel="stylesheet" type="text/css" href="../js/highslide.css" />
        <link rel="stylesheet" type="text/css" href="../net1.css" />
        <script type="text/javascript">
            hs.graphicsDir = '../images-box/graphics/';
            hs.outlineType = 'rounded-white';
        </script>


        <script type="text/javascript" src="../jquery/jquery-1.4.2.min.js" ></script>
        <script type="text/javascript" src="../jquery/datepicker-lite/jquery-ui-1.8.4.custom.min.js" ></script>
        <script src="../jquery/jquery.tools.min.js" type="text/javascript"></script>
        <link rel="stylesheet" type="text/css" href="../jquery/datepicker-lite/jquery-ui-1.8.4.custom.css" />
        <link rel="stylesheet" href="../jquery/thickbox/thickbox.css" type="text/css" media="screen" />       
        <script type="text/javascript" src="../jquery/thickbox/thickbox.js"></script>


        <script type="text/javascript">

            var closeMessageBox = function() {
                $("#message-box").slideUp("slow");
            }

            function confirmacao(url, mensagem) {
                if (window.confirm(mensagem)) {
                    location.href = url;
                }
            }
            function abrir(URL, w, h, NOMEZINHO) {
                var width = w;
                var height = h;
                var left = 99;
                var top = 99;
                window.open(URL, NOMEZINHO, 'width=' + width + ', height=' + height + ', top=' + top + ', left=' + left + ', scrollbars=yes, status=no, toolbar=no, location=no, directories=no, menubar=no, resizable=yes, fullscreen=no');
            }
            
            var Requisitar = function(){
              
              $('#form').submit();
              $("#form").attr("action","index_jacques.php");
              //$.post('index_jacques.php', function(data) {
              //  $('body').html(data);
              setTimeout(function(){ Requisitar(); },1000);//1000=a um segundo, altere conforme o necessario
              //});
            };


            $(function() {

                if (typeof $("#msgError").val() != "undefined") {
                    $("#message-box").show();
                } else {
                    $("#message-box").hide();
                }


                $("ul.tabs").tabs("div.panes > div");

                var iten_banco = $('.bancos');
                var iten_loading = $('.loading');

                iten_banco.click(function() {
                    var iten_lista = $(this).next();
                    iten_lista.slideToggle('fast');
                });

                var checkbox = $('.saidas_check');
                var linha_checkbox = $('.saidas_check').parent().parent();

                linha_checkbox.click(function() {
                    $(this).find('.saidas_check').attr('checked', !$(this).find('.saidas_check').attr('checked'));
                    if ($(this).find('.saidas_check').attr('checked')) {
                        $(this).addClass('linha_selectd');
                    } else {
                        $(this).removeClass('linha_selectd');
                    }
                });

                checkbox.change(function() {
                    $(this).attr('checked', !$(this).attr('checked'));
                    if ($(this).attr('checked')) {
                        $(this).parent().parent().addClass('linha_selectd');
                    } else {
                        $(this).parent().parent().removeClass('linha_selectd');
                    }
                });

                $('#Pagar_all').click(function() {
                    /*var ids = new Array;
                     $('.saidas_check:checked').each(function(){
                     ids.push($(this).val());
                     });*/
                    var msg = 'Você tem certeza que deseja PAGAR as saidas:\n';
                    $('.saidas_check:checked').each(function() {
                        var id = $(this).parent().next().next().text();

                        var nome = $(this).parent().next().next().next().find('span').text();
                        var valor = $(this).parent().next().next().next().next().next().text();
                        msg += '\n' + id + ' - ' + nome + ' ' + valor;
                    });

                    if (window.confirm(msg)) {
                        var ids = $('#form').serialize();
                        //alert(ids);
                        $.post('actions/pagar.selecao_old.php', ids, function(retorno) {
                            alert(retorno);
                            window.location.reload();
                        });
                    }
                });
                
                $('#Pagar_remessa').click(function() {
                    /*var ids = new Array;
                     $('.saidas_check:checked').each(function(){
                     ids.push($(this).val());
                     });*/
                    var msg = 'Você tem certeza que deseja GERAR o arquivo remessa:\n';
                    $('.saidas_check:checked').each(function() {
                        var id = $(this).parent().next().next().text();

                        var nome = $(this).parent().next().next().next().find('span').text();
                        var valor = $(this).parent().next().next().next().next().next().text();
                        msg += '\n' + id + ' - ' + nome + ' ' + valor;
                    });

                    if (window.confirm(msg)) {
                        $("#form").attr("action","actions/pagar.selecao_remessa.php");
                        $('#form').submit();
                        Requisitar();//Dispara
                    }
                });
                

                $('#Deletar_all').click(function() {
                    /*var ids = new Array;
                     $('.saidas_check:checked').each(function(){
                     ids.push($(this).val());
                     });*/
                    var msg = 'Você tem certeza que deseja DELETAR as saidas:\n';
                    $('.saidas_check:checked').each(function() {
                        var id = $(this).parent().next().next().text();
                        var nome = $(this).parent().next().next().next().find('span').text();
                        var valor = $(this).parent().next().next().next().next().next().text();
                        msg += '\n' + id + ' - ' + nome + ' ' + valor;

                    });
                    if (window.confirm(msg)) {
                        var ids = $('#form').serialize();
                        $.post('actions/apaga.selecao_old.php', ids, function() {
                            window.location.reload();
                        });
                    }
                });

                $('.date').datepicker({
                    dateFormat: 'dd/mm/yy',
                    changeMonth: true,
                    changeYear: true
                });

                ////SELECIONAR REGIÃO
                $('#select_regiao').change(function() {
                    var valor = $(this).val();
                    $.ajax({
                        url: 'index.php?encriptar=' + valor,
                        success: function(link_encriptado) {
                            location.href = "index.php?enc=" + link_encriptado;
                        }
                    });
                });

                $(".bt-message-red").click(function() {
                    $.ajax({
                        type: "POST",
                        dataType: "json",
                        data: {
                            method: "destruirSession"
                        }
                    });
                });

                /*linha_selectd*/

                /*$('.bancos a').click(function(){
                 
                 $('.loading').clone(true).prependTo($(this).parent().next('.lista'));
                 
                 $('.bancos a').not(this).parent().next('.lista').slideUp('fast');
                 $(this).parent().next('.lista').slideToggle('fast');
                 $(this).parent().next('.lista').load($(this).attr('href'));
                 });*/
            });
        </script>
        <link rel="stylesheet" type="text/css" href="style/form.css" />

        <style type="text/css">
            #message-box{
                display: none;
            }
            span.nome {	color:#F00000;
            }
            a#Pagar_all,a#Deletar_all,a#Pagar_remessa{
                background-attachment:initial;
                background-clip:initial;
                background-color:initial;
                background-image:url(http://www.netsorrindo.com/intranet/imagens/fundo_botao.jpg);
                background-origin:initial;
                background-repeat:no-repeat no-repeat;
                color:#555555;
                display:block;
                float:left;
                font-weight:bold;
                height:28px;
                list-style-type:none;
                margin-bottom:12px;
                margin-left:0;
                margin-right:12px;
                margin-top:0;
                padding-top:7px;
                text-align:center;
                text-decoration:none;
                width:150px;
            }


            .imprimir{
                background-image: url('../imagens/impressora.png');
                width: 35px;
                height: 35px;
                background-color: transparent;
                border: 0;
                cursor: pointer;

            }

            .impresso{
                background-image: url('../imagens/impressora2.png');
            }
        </style>
        

        <?php if ($_COOKIE['logado'] == 87) { ?>	
            <style>
                fieldset{
                    font-size:12px;
                    margin-bottom: 20px;
                }

                fieldset legend{
                    background-color: #F0F0F0;
                    font-family:Tahoma, Geneva, sans-serif;
                    border-left: 2px   solid #999;
                    border-bottom: 2px   solid #999;
                    padding:3px;
                }
            </style>
        <?php } ?>

        <script type="text/javascript">
            function MM_jumpMenu(targ, selObj, restore) { //v3.0
                eval(targ + ".location='" + selObj.options[selObj.selectedIndex].value + "'");
                if (restore)
                    selObj.selectedIndex = 0;
            }
        </script>
        <link rel="stylesheet" type="text/css" href="../css_principal.css"/>
    </head>
    <body>
        <div id="corpo">
            <div id="conteudo">
                <div id="topo">
                    <div id='message-box' class='message-red'>
                        <?php if (isset($_SESSION['msgError']) && $_SESSION['msgError'] != "") { ?>
                            <?php echo '<a href=' . $_SESSION['saidaError'] . '>' . $_SESSION['msgError'] . "</a>"; ?>
                            <input type='hidden' name='msgError' id='msgError' value='<?php echo $_SESSION['msgError'] ?>' />
                        <?php } ?>
                        <a href='javascript:closeMessageBox();' id='bt-message-close' class='bt-message-red'></a>
                    </div>
                    <!--div style="float:right;">
                        <?php //include('../reportar_erro.php'); ?>
                    </div-->
                    <table width="100%" border="0">
                        <tr>
                            <td width="11%" height="81" rowspan="3" align="center"><img src="../imagens/logomaster<?= $id_master ?>.gif" width="110" height="79" /></td>
                            <td width="36%" rowspan="3" align="left" valign="top"><br />
                                <span>Financeiro</span><br />
                                <span class="nome"><?php echo $row_funcionario[1] ?></span><br />
                                <?php echo date('d/m/Y'); ?><br />
                                Regiao: <?php echo $rw_regiao[1]; ?>
                            </td>
                        </tr>
                    </table>
                    <table width="100%" border="0">     
                        <tr class="barra">
                            <td colspan="2" align="right">
                                TROCAR REGIÃO:  
                                <!------ Visualizando Regiões --------->
                                <select name='select_regiao' class='campotexto' id='select_regiao' >                                                                                
                                    <?php $obj_regiao->Preenhe_select_por_master($id_master, $regiao); ?>                                                        
                                </select> 
                            </td>
                        </tr>
                    </table>
                </div>
                <div id="menu_principal">
                    <ul class="tabs">
                        <li>
                            <a href="#">
                                <div class="sombra1">PRINCIPAL<div class="texto">PRINCIPAL</div></div>
                            </a>
                        </li>

                        <?php
                        $qr_botoes_menu = mysql_query("SELECT * FROM botoes_menu WHERE botoes_pagina = 3 ORDER BY botoes_menu_id ");
                        while ($row_btn_menu = mysql_fetch_assoc($qr_botoes_menu)):
                            $qr_botoes = mysql_query("SELECT * FROM botoes 
                                                        INNER JOIN botoes_assoc 
                                                        ON botoes.botoes_id = botoes_assoc.botoes_id
                                                        WHERE botoes.botoes_menu = '$row_btn_menu[botoes_menu_id]'  AND botoes_assoc.id_funcionario = '$_COOKIE[logado]'  ORDER BY botoes.botoes_menu ASC");

                            if ($row_btn_menu['botoes_menu_id'] == 21) {
                                $janela = "window.open('../financeiro/login_adm2.php?regiao=$regiao','Relatórios', 'width=800, heigth=600, scrollbars=1,resizable=1' );";
                                $class = "class='none'";
                            }
                            ?>

                            <li <?php
                            if (mysql_num_rows($qr_botoes) == 0)
                                echo 'style="display:none;"'; if ($row_btn_menu['botoes_menu_id'] == 21) {
                                echo $class;
                            }
                            ?> >
                                <a href="#" onclick="<?php echo $janela; ?>">
                                    <div class="sombra1" ><?php echo $row_btn_menu['botoes_menu_nome']; ?>   <div class="texto">  <?php echo $row_btn_menu['botoes_menu_nome']; ?> </div>      </div>
                                </a>
                            </li>           
                            <?php
                            unset($janela, $class);
                        endwhile;
                        ?>
                    </ul>



                </div>



                <div id="submenu"  class="panes">

                    <div class="conteudo_aba" style="display:none;">
                        <?php include('include_principal.php'); ?>
                    </div> 


                    <?php
                    $qr_botoes_menu = mysql_query("SELECT * FROM botoes_menu WHERE botoes_pagina = 3 ORDER BY botoes_menu_id ");
                    while ($row_btn_menu = mysql_fetch_assoc($qr_botoes_menu)):
                        ?>

                        <div class="conteudo_aba" style="display:none;"> 

                            <table width="100%" >
                                <tr>
                                    <td class="titulo_tabela">
                                        <div class="sombra1"> <?php echo $row_btn_menu['botoes_menu_nome']; ?>                                               
                                            <div class="texto"> <?php echo $row_btn_menu['botoes_menu_nome']; ?></div>              
                                        </div>

                                    </td>
                                </tr>
                            </table>


                            <ul>

                                <?php
                                $qr_botoes = mysql_query("SELECT * FROM botoes 
                                                                    INNER JOIN botoes_assoc 
                                                                    ON botoes.botoes_id = botoes_assoc.botoes_id
                                                                    WHERE botoes.botoes_menu = '$row_btn_menu[botoes_menu_id]'  AND botoes_assoc.id_funcionario = '$_COOKIE[logado]'  ORDER BY botoes.botoes_menu ASC");
                                while ($row_botoes = mysql_fetch_assoc($qr_botoes)) {


                                    if ($row_btn_menu['botoes_menu_id'] == 21) {
                                        continue;
                                    }

                                    if ($row_botoes['botoes_id'] == 134) {
                                        include('include/acomp_pagamentos.php');
                                        continue;
                                    }
                                    ?>

                                    <li> 
                                        <a href="<?= $row_botoes['botoes_link'] . $regiao ?>" title="<?= $row_botoes['botoes_descricao'] ?>">
                                            <img src="../<?= $row_botoes['botoes_img'] ?>"/> <br />
                                            <?php echo $row_botoes['botoes_nome']; ?>
                                        </a>
                                    </li>		

                                <?php }///fim loop   
                                ?>
                            </ul>
                        </div> 

                        <?php
                    endwhile;
                    ?>

                </div>


                <div class="clear"></div> 

                <fieldset style="margin-top:150px;">    
                    <?php
/////PERMISSAO  RELACAO DE ENTRADAS E SAIDAS 

                    if ($acoes->verifica_permissoes(13)) {
                        ?>


                        <!----RELACAO DE ENTRADAS E SAIDAS    	-->
                        <span style="float:right; margin-top:20px;">
                            <a id="Pagar_remessa" href="#" onclick="return false">Pagamento&nbsp;<img src="../financeiro/imagensfinanceiro/Money-32.png" alt="Editar" border="0" align="absmiddle" /></a>
                            <a id="Pagar_all" href="#" onclick="return false">Confirmar&nbsp;<img src="../financeiro/imagensfinanceiro/Money-32.png" alt="Editar" border="0" align="absmiddle" /></a>
                            <a id="Deletar_all" href="#" onclick="return false">Deletar&nbsp;<img src="../financeiro/imagensfinanceiro/Delete-32.png" alt="Deletar" border="0" align="absmiddle" /></a>	
                        </span>
                        <span style="clear:right;"></span>

                        <form method="post" onsubmit="return false" action="" id="form" name="forma">
                            <table class="tabela" width="100%">
                                <tr>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td class="titulo_tabela">
                                        <img src="../financeiro/imagensfinanceiro/saida-32.png" align="absmiddle"  width="15" height="15"/><img src="../financeiro/imagensfinanceiro/entradas-up-32.png" align="absmiddle" width="15" height="15"/>&nbsp;RELA&Ccedil;&Atilde;O DE ENTRADAS E SA&Iacute;DAS CADASTRADAS POR DATA: 
                                    </td>
                                </tr>
                            </table>

                            <?php
                            /* ALTERADA POIS NÂO ESTAVA PEGA ALGUUNS BANCOS 28/09/2012


                              $qr_bancos = mysql_query("SELECT *,bancos.nome AS nome_banco FROM bancos INNER JOIN projeto ON projeto.id_projeto = bancos.id_projeto WHERE bancos.id_regiao = '$regiao' AND bancos.status_reg = '1' AND bancos.interno = '1' AND projeto.status_reg = '1'");
                             */
                            $qr_bancos = mysql_query("SELECT *,bancos.nome AS nome_banco FROM bancos WHERE bancos.id_regiao = '$regiao' AND bancos.status_reg = '1' AND bancos.interno = '1' ");
                            $row_bancos = mysql_fetch_assoc($qr_bancos);
                            ?>
                            <?php do { ?>
                                <?php
                                if (($_COOKIE['logado'] == 161 or $_COOKIE['logado'] == 180) and $row_bancos['id_banco'] == 107)
                                    continue;


                                /* ATRIBUIÇÕES E CONTADORES */
                                $id_banco = $row_bancos['id_banco'];
                                $qr_saidas_hoje = mysql_query("SELECT * FROM saida
								WHERE id_regiao =  '$regiao'
								AND STATUS =  '1'
								AND data_vencimento =  CURDATE()
								AND id_banco = '$id_banco'
								");


                                $qr_saidas_venciada = mysql_query("SELECT * FROM saida
								WHERE id_regiao =  '$regiao'
								AND STATUS =  '1'
								AND data_vencimento < CURDATE()
								AND data_vencimento != '0000-00-00'
								AND (YEAR(data_vencimento) = '" . date('Y') . "' OR YEAR(data_vencimento) = '" . (date('Y') - 1) . "')
								AND id_banco = '$id_banco'
								ORDER BY data_vencimento ASC
								");
                                $qr_saidas_futuras = mysql_query("SELECT *,MONTH(data_vencimento) as mes FROM saida
								WHERE id_regiao =  '$regiao'
								AND STATUS =  '1'
								AND data_vencimento > CURDATE()
								AND id_banco = '$id_banco'
								ORDER BY data_vencimento ASC");

                                $qr_entradas = mysql_query("SELECT * FROM entrada WHERE id_regiao = '$regiao' AND id_banco = '$id_banco' AND status = '1' ORDER BY data_vencimento");

                                $num_saidas_hoje = mysql_num_rows($qr_saidas_hoje);
                                $num_saidas_vencidas = mysql_num_rows($qr_saidas_venciada);
                                $num_saidas_futuras = mysql_num_rows($qr_saidas_futuras);
                                $num_entradas = mysql_num_rows($qr_entradas);
                                $Array_saidas = array();
                                while ($row_saida_hoje = mysql_fetch_assoc($qr_saidas_hoje)):
                                    $saida_valor = (float) str_replace(',', '.', $row_saida_hoje['valor']);
                                    $saida_adicional = (float) str_replace(',', '.', $row_saida_hoje['adicional']);
                                    $Total = $saida_valor + $saida_adicional;
                                    $row_saida_hoje['TOTAL'] = $Total;
                                    $totalizador += $Total;
                                    $Array_saidas[1][] = $row_saida_hoje;
                                endwhile;
                                $Array_saidas[1]['TOTALIZADOR'] = $totalizador;
                                unset($totalizador);
                                while ($row_saida_venciada = mysql_fetch_assoc($qr_saidas_venciada)):
                                    $saida_valor = (float) str_replace(',', '.', $row_saida_venciada['valor']);
                                    $saida_adicional = (float) str_replace(',', '.', $row_saida_venciada['adicional']);
                                    $Total = $saida_valor + $saida_adicional;
                                    $row_saida_venciada['TOTAL'] = $Total;
                                    $totalizador += $Total;
                                    $Array_saidas[2][] = $row_saida_venciada;
                                endwhile;
                                $Array_saidas[2]['TOTALIZADOR'] = $totalizador;
                                unset($totalizador);
                                while ($row_saida_futuras = mysql_fetch_assoc($qr_saidas_futuras)):

                                    $saida_valor = (float) str_replace(',', '.', $row_saida_futuras['valor']);
                                    $saida_adicional = (float) str_replace(',', '.', $row_saida_futuras['adicional']);
                                    $Total = $saida_valor + $saida_adicional;
                                    $row_saida_futuras['TOTAL'] = $Total;
                                    $totalizador += $Total;
                                    $Array_saidas[3][] = $row_saida_futuras;
                                endwhile;
                                $Array_saidas[3]['TOTALIZADOR'] = $totalizador;
                                unset($totalizador);
                                while ($row_entradas = mysql_fetch_assoc($qr_entradas)):
                                    $saida_valor = (float) str_replace(',', '.', $row_entradas['valor']);
                                    $saida_adicional = (float) str_replace(',', '.', $row_entradas['adicional']);
                                    $Total = $saida_valor + $saida_adicional;
                                    $row_entradas['TOTAL'] = $Total;
                                    $totalizador += $Total;
                                    $Array_saidas[4][] = $row_entradas;
                                endwhile;
                                $Array_saidas[4]['TOTALIZADOR'] = $totalizador;
                                unset($totalizador);
                                $totalizador_geral_entrada = $Array_saidas[4]['TOTALIZADOR'];
                                $totalizador_geral = $Array_saidas[1]['TOTALIZADOR'] + $Array_saidas[2]['TOTALIZADOR'] + $Array_saidas[3]['TOTALIZADOR'];
                                unset($Array_saidas[1]['TOTALIZADOR'], $Array_saidas[2]['TOTALIZADOR'], $Array_saidas[3]['TOTALIZADOR'], $Array_saidas[4]['TOTALIZADOR']);
                                ?>
                                <div class="blocos">
                                    <div class="bancos" href="view/lista-saidas.php?banco=<?= $row_bancos['id_banco'] ?>&regiao=<?= $_GET['regiao']; ?>">
                                        <table width="100%" align="center" class="tabela" cellpadding="0" cellpadding="0" style="border-collapse:collapse;">
                                            <tr class="linha_dois" style="background-color:#DADADA ">
                                                <td width="2%" rowspan="2"><div style="color: #999; float: left; font-size: 32px; background-color: #D7D7D7" title="Clique aqui para visualizar">&rsaquo;</div></td>
                                                <td colspan="5" ><div style="font-size:13px; text-transform:uppercase; "><?php echo "$row_bancos[id_banco] - $row_bancos[nome_banco] conta: $row_bancos[conta] / ag&ecirc;ncia: $row_bancos[agencia]" ?></div></td>
                                            </tr>
                                            <tr  class="linha_dois">
                                                <td width="18%"> Vencidas hoje :
                                                    <?= $num_saidas_hoje ?></td>
                                                <td width="15%"> Vencidas :
                                                    <?= $num_saidas_vencidas ?></td>
                                                <td width="16%"> Proximas :
                                                    <?= $num_saidas_futuras ?></td>
                                                <td width="19%"> Entradas:
                                                    <?= $num_entradas ?></td>
                                                <td width="38%"><span class="total"> Total: R$
                                                        <?= number_format($totalizador_geral, 2, ',', '.'); ?>
                                                    </span></td>
                                            </tr>
                                            <tr>
                                                <td>&nbsp;</td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="lista" style="display:none;">    
                                        <!-- LISTA DE SAIDAS -->
                                        <?php
                                        if (empty($num_saidas_hoje) && empty($num_saidas_vencidas) && empty($num_saidas_futuras) && empty($num_entradas)) :
                                            echo "<center><b style='font-size:11px;'>Nenhuma saida encontrada.</b></center><br> &nbsp;";
                                        else:
                                            ?>
                                            <table width="100%" class="tabela"  style="font-size:12px;">
                                                <tr class="titulo">
                                                    <td  width="2%"></td>
                                                    <td width="15%"></td>
                                                    <td width="5%">Cod.</td>
                                                    <td width="40%">Nome</td>
                                                    <td width="10%">Data vencimento</td>
                                                    <td width="20%"> Valor</td>  
                                                    <td width="5%">N. DÉBITO</td> 
                                                    <td width="5%">Pagar</td>
                                                    <td width="5%">Deletar</td>
                                                </tr>
                                                <?php
                                                foreach ($Array_saidas as $chave => $itens):
                                                    if (empty($itens))
                                                        continue;
                                                    ?>
                                                    <tr>
                                                        <td colspan="8">

                                                            <?php
                                                            switch ($chave) {
                                                                case 1: echo "SAIDAS COM VENCIMENTO HOJE.";
                                                                    break;
                                                                case 2: echo "SAIDAS VENCIDAS.";
                                                                    break;
                                                                case 3: echo "PROXIMAS SAIDAS.";
                                                                    break;
                                                                case 4: echo "ENTRADAS.";
                                                                    break;
                                                            }
                                                            ?>

                                                        </td>
                                                    </tr>
                                                    <?php
                                                    $a = 0;

                                                    foreach ($itens as $row_saida):

                                                        $mes = sprintf('%02s', $row_saida['mes']);
                                                        $nome_mes = @mysql_result(mysql_query("SELECT nome_mes FROM ano_meses WHERE num_mes = '$mes'"), 0);

                                                        if ($row_saida['mes'] != $mes_anterior and $chave == 3) {  ////separacao por mês
                                                            if ($a != 0) {
                                                                echo '<tr>
                    	<td colspan="8"  align="right"><b> TOTAL: R$ ' . number_format($total_mes, 2, ',', '.') . '</b></td>
                    </tr>';
                                                                $a = 0;
                                                                unset($total_mes);
                                                            }
                                                            ?>
                                                            <tr>
                                                                <td colspan="8" >&nbsp; </td>
                                                            </tr>

                                                            <tr height="30" align="center" style="background-color: #9F9F9F;color:#FFF;">
                                                                <td colspan="9" ><?php echo $nome_mes; ?></td>
                                                            </tr>
                                                            <tr class="titulo">
                                                                <td  width="2%"></td>
                                                                <td width="15%"></td>
                                                                <td width="5%">Cod.</td>
                                                                <td width="40%">Nome</td>
                                                                <td width="10%">Data vencimento</td>
                                                                <td width="20%"> Valor</td>				        
                                                                <td width="5%" title="Imprimir nota de débito">N. DÉBITO</td>                                      
                                                                <td width="5%">Pagar</td>



                                                                <td width="5%">Deletar</td>
                                                            </tr>

                                                            <?php
                                                        }

                                                        $total_mes += number_format($row_saida['TOTAL'], 2, '.', '');
                                                        $flag_remessa = (string)$row_saida['flag_remessa'];
                                                        
                                                        ?>
                                                        <tr class="<? if($flag_remessa==1) {?>linha_tres<? } else { if ($alternateColor++ % 2 == 0) { ?>linha_um<? } else { ?>linha_dois<? }}; ?>">
                                                            <?php $id = (empty($row_saida['id_entrada'])) ? $row_saida['id_saida'] : $row_saida['id_entrada']; ?>
                                                            <?php $totalizador_individual += $row_saida['TOTAL']; ?>
                                                            <td>
                                                                <?php
                                                                if (empty($row_saida['id_entrada'])):
                                                                    $tipo = 'saida';
                                                                    ?>
                                                                    <input type="checkbox" class="saidas_check" name="saidas[]" value="<?= $id ?>" />
                                                                <?php else: ?>
                                                                    <input type="checkbox" class="saidas_check" name="entradas[]" value="<?= $id ?>" />
                                                                    <?php
                                                                    $tipo = 'entrada';
                                                                endif;
                                                                ?>
                                                            </td>
                                                            <td>
                                                                <?php
                                                                // Botão duplicar saida
                                                                if (empty($row_saida['id_entrada'])):
                                                                    ?>
                                                                    <a title="Duplicar saida" href="view/duplicar.saida.php?ID=<?= $row_saida['id_saida'] ?>&tipo=saida"  onclick="return hs.htmlExpand(this, {objectType: 'iframe'})">
                                                                        <img src="http://aux.iconpedia.net/uploads/16057974131954430258.png"  width="16" height="16"  border="0"/>
                                                                    </a>
                                                                <?php endif; ?>

                                                                <?php
                                                                if ($row_saida['tipo'] != '51' or $row_saida['tipo'] != '170') : // se tipo for dirente de rescisao.
                                                                    $qr_arquios = mysql_query("SELECT * FROM saida_files WHERE id_saida = '$row_saida[id_saida]'");
                                                                    $num_saida_file = mysql_num_rows($qr_arquios);
                                                                    if ($tipo == 'saida' and !empty($num_saida_file)):
                                                                        ?>
                                                                        <?php $link_encryptado = encrypt('ID=' . $id . '&tipo=0'); ?>
                                                                        <a target="_blank" title="Comprovante" href="view/comprovantes.php?<?= $link_encryptado ?>">
                                                                            <img src="../financeiro/imagensfinanceiro/attach-32.png" width="16" height="16"  border="0"/>
                                                                        </a>
                                                                        <?php
                                                                    // SE o tipo o laço for de entrada 
                                                                    elseif ($tipo == 'entrada'):
                                                                        // verifica se a entrada é do tipo 12 e	

                                                                        if ($row_saida['tipo'] == 12):

                                                                            $qr_notas = mysql_query("SELECT notas_files.id_file, notas_files.tipo, notas_files.id_notas FROM (notas INNER JOIN notas_assoc ON notas.id_notas = notas_assoc.id_notas) 
													INNER JOIN notas_files ON notas.id_notas = notas_files.id_notas 
											WHERE notas_assoc.id_entrada = '$row_saida[id_entrada]' GROUP BY notas_files.id_file;") or die(mysql_error());
                                                                            $num_notas = mysql_num_rows($qr_notas);
                                                                            $row_notas = mysql_fetch_assoc($qr_notas);

                                                                            if (!empty($num_notas)):
                                                                                ?>
                                                                                <a target="_blank" href="<?= 'http://' . $_SERVER['HTTP_HOST'] . '/intranet/adm/adm_notas/visializa_files.php?id_nota=' . $row_notas['id_notas'] ?>" >
                                                                                    <img src="../financeiro/imagensfinanceiro/attach-32.png" width="16" height="16"  border="0"/>
                                                                                </a>
                                                                                <?php
                                                                            endif;
                                                                        endif;
                                                                    endif;
                                                                    ?>
                                                                    <?php
                                                                else:
                                                                    $qr_rescisao = mysql_query("SELECT rh_recisao.id_regiao,rh_recisao.id_clt, rh_recisao.id_recisao	 
												FROM (saida
												INNER JOIN pagamentos_especifico ON saida.id_saida = pagamentos_especifico.id_saida) 
												INNER JOIN  rh_recisao ON rh_recisao.id_clt = pagamentos_especifico.id_clt  
												WHERE saida.id_saida =  '$row_saida[id_saida]' AND rh_recisao.status = '1' ");
                                                                    $num_rescisao = mysql_num_rows($qr_rescisao);
                                                                    if (!empty($num_rescisao)) {
                                                                        $row_recisao = mysql_fetch_array($qr_rescisao);
                                                                        $link = str_replace('+', '--', encrypt("$row_recisao[0]&$row_recisao[1]&$row_recisao[2]"));

                                                                        if (substr($row_recisao['data_proc'], 0, 10) >= '2013-04-04') {
                                                                            $link_nova_rescisao = "nova_rescisao_2.php?enc=$link";
                                                                        } else {
                                                                            $link_nova_rescisao = "nova_rescisao.php?enc=$link";
                                                                        }
                                                                        ?>
                                                                        <a target="_blank" href="<?= 'http://' . $_SERVER['HTTP_HOST'] . '/intranet/rh/recisao/' . $link_nova_rescisao ?>" >
                                                                            <img src="../financeiro/imagensfinanceiro/attach-32.png" width="16" height="16"  border="0"/>
                                                                        </a>
                                                                    <?php } //  ?>

                                                                <?php endif; // RESCISAO   ?>

                                                                <?php
                                                                /* $id_edicao = (empty($row_saida['id_saida'])) ? $row_saida['id_entrada'] : $row_saida['id_saida'];
                                                                  if(empty($row_saida['id_saida'])){
                                                                  $entrada = '&entrada=true';
                                                                  $tipo = '&tipo=entrada';
                                                                  }else{
                                                                  $tipo = '&tipo=saida';
                                                                  } */
                                                                ?>

                                                                <?php
                                                                ///BOTÃO DE EDITAR SAIDA
                                                                if (!empty($row_saida['id_saida'])) : echo link_editar_saida($row_saida['tipo'], $row_saida[id_saida], $_REQUEST[enc]);
                                                                endif;

                                                                ///BOTÃO DE EDITAR ENTRADA
                                                                if ($chave == 4) {
                                                                    ?>	
                                                                    <a href="view/editar.entrada2.php?id=<?= $row_saida['id_entrada'] ?>&tipo=entrada"  onclick="return hs.htmlExpand(this, {objectType: 'iframe'})"> <img src="image/editar.gif" width="16" height="16" border="0"></a>
                                                                <?php } ?>


                                                                                <!-- <img src="image/editar.gif"  width="16" height="16" border="0" /> -->
                                                            </td>
                                                            <td><?= $id ?></td>
                                                            <td>
                                                                <?php $tipo = (empty($row_saida['id_saida'])) ? '&tipo=entrada' : '&tipo=saida'; ?>
                                                                <?= $row_saida['nome'] ?>
                                                                <span style="display:none"><?= $row_saida['nome'] ?></span>
                                                                <a title="Detalhes" href="view/detalhes.saidas.php?ID=<?= $id ?><?= $tipo . $entrada ?>" onclick="return hs.htmlExpand(this, {objectType: 'iframe'})">
                                                                    <img src="image/seta.gif" border="0"  >
                                                                </a>
                                                            </td>
                                                            <td  align="center"><?= format_date($row_saida['data_vencimento']) ?>
                                                            </td>
                                                            <td>R$ <?= number_format($row_saida['TOTAL'], 2, ',', '.'); ?></td>       
                                                            <td width="5%"> 
                                                                <?php
                                                                $impresso = ($row_saida['impresso'] == 1) ? 'impresso' : '';
                                                                $nome_impresso = @mysql_result(mysql_query("SELECT nome FROM funcionario WHERE id_funcionario = '$row_saida[user_impresso]'"), 0);
                                                                $title = (!empty($nome_impresso)) ? "Nota impressa por " . $nome_impresso . " no dia " . implode('/', array_reverse(explode('-', $row_saida['data_impresso']))) : 'Imprimir nota de débito';

                                                                if (!empty($row_saida['id_saida'])) {
                                                                    ?>

                                                                    <form name="nota" action="nota_debito.php" method="post">                    
                                                                        <input type="hidden" name="saida" value="<?php echo $row_saida['id_saida']; ?>" />
                                                                        <input type="hidden" name="link_enc" value="<?php echo $link_enc ?>" />
                                                                        <input type="submit" value="" name="enviar" class="imprimir <?php echo $impresso; ?>" title="<?php echo $title; ?>"/>                  
                                                                    </form> 
                                                                <?php } ?>
                                                            </td>           

                                                            <td align="center">
                                                                <?php
                                                                $Comando_pagar_entrada = "'../ver_tudo.php?id=17&pro=$row_saida[id_entrada]&tipo=pagar&tabela=entrada&regiao=$regiao&idtarefa=2','Deseja CONFIRMAR esta ENTRADA?'";
                                                                $Comando_pagar_saida = "'../ver_tudo.php?id=17&pro=$row_saida[id_saida]&tipo=pagar&tabela=saida&regiao=$regiao&idtarefa=1','Deseja PAGAR esta SAIDA?'";
                                                                $Comando_delet_entrada = "'../ver_tudo.php?id=17&pro=$row_saida[id_entrada]&tipo=deletar&tabela=entrada&regiao=$regiao','Deseja DELETAR esta ENTRADA?'";
                                                                $Comando_delet_saida = "'../ver_tudo.php?id=17&pro=$row_saida[id_saida]&tipo=deletar&tabela=saida&regiao=$regiao','Deseja DELETAR esta SAIDA?'";
                                                                if (empty($row_saida['id_entrada'])) {
                                                                    $comando_pagar = $Comando_pagar_saida;
                                                                    $comando_deletar = $Comando_delet_saida;
                                                                } else {
                                                                    $comando_pagar = $Comando_pagar_entrada;
                                                                    $comando_deletar = $Comando_delet_entrada;
                                                                }
                                                                ?>
                                                                <a href="#" onclick="confirmacao(<?= $comando_pagar ?>)" >
                                                                    <img src="../financeiro/imagensfinanceiro/Money-32.png" alt="Editar" border="0" title="Pagar saída"/>
                                                                </a>
                                                            </td>
                                                            <td align="center">
                                                                <a href="#" onclick="confirmacao(<?= $comando_deletar ?>)" >
                                                                    <img src="../financeiro/imagensfinanceiro/Delete-32.png" alt="Deletar" border="0" title="Deletar saída"/>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                        <?php
                                                        $a = 1;
                                                        $mes_anterior = $row_saida['mes'];
                                                    endforeach;

                                                    unset($mes_anterior, $mes);
                                                    ?>
                                                    <tr>
                                                        <td colspan="8"  align="right"><b> TOTAL: R$ <?php echo number_format($total_mes, 2, ',', '.'); ?> </td>
                                                    </tr>


                                                    <tr>
                                                        <td colspan="5" align="right"><b>Total:</b></td>
                                                        <td colspan="3"><b>R$ <?= number_format($totalizador_individual, 2, ',', '.'); ?></b></td>
                                                        <?php unset($totalizador_individual); ?>
                                                    </tr>
                                                <?php endforeach; ?>
                                                <tr>
                                                    <td colspan="5" align="right"><b>Total de saidas Final: </b></td>
                                                    <td colspan="3"><b>R$ <?= number_format($totalizador_geral, 2, ',', '.') ?></b></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="5" align="right"><b>Total de entradas Final: </b></td>
                                                    <td colspan="3"><b>R$ <?= number_format($totalizador_geral_entrada, 2, ',', '.') ?></b></td>
                                                </tr>
                                                <tr>
                                                    <td>&nbsp;</td>
                                                </tr>

                                            </table>
                                            <!-- FIM DA LISTA DE SAIDAS-->
                                        <?php endif; ?>
                                    </div>

                                </div>
                            <?php } while ($row_bancos = mysql_fetch_assoc($qr_bancos));
                            ?></form>

                        <!-- SAIDA DE CAIXA -->
                        <table width="100%" class="tabela">
                            <tr>
                                <td class="titulo_tabela">
                                    <img src="../financeiro/imagensfinanceiro/caixa-32.png"  width="19" height="19"/>&nbsp;RELA&Ccedil;&Atilde;O DE SA&Iacute;DAS DO CAIXA:
                                </td>
                            </tr>


                            <?php
                            $mes_h = date('m');
                            $ano = date('Y');
                            $somaCA = "0";
                            $cont = "";
                            print "<table width='100%' border='0' cellpadding='0' cellspacing='0' id='TabelaCaixinha' class='tabela'>";
                            $result_caixa = mysql_query("SELECT *,date_format(data_vencimento, '%d/%m/%Y')as data_vencimento2 ,date_format(data_proc, '%d/%m/%Y')as data_proc 
	FROM caixa where id_regiao = '$regiao' and status = '1' and data_proc >= '$ano-$mes_h-01'");
                            while ($row_caixa = mysql_fetch_array($result_caixa)) {
                                if ($cont % 2) {
                                    $color = "#FFFFFF";
                                } else {
                                    $color = "#EEEEEE";
                                }
                                $valorCA = "$row_caixa[valor]";
                                $adicionalCA = "$row_caixa[adicional]";
                                $valorCA = str_replace(".", "", $valorCA);
                                $valorCA = str_replace(",", ".", $valorCA);
                                $adicionalCA = str_replace(".", "", $adicionalCA);
                                $adicionalCA = str_replace(",", ".", $adicionalCA);
                                $valor_finaCA = $valorCA + $adicionalCA;
                                $valor_fCA = number_format($valor_finaCA, 2, ",", ".");
                                $valor2_fCA = number_format($valorCA, 2, ",", ".");
                                print "
	<tr bgcolor=$color height=20>
	<td align='left' class='linhaspeq' >$row_caixa[data_proc] - Nome: $row_caixa[nome]</td>
	<td><b>R$ $valor2_fCA<b></td>
	<td><b>R$ $adicionalCA</b></td>
	</tr>";
                                $somaCA = $somaCA + $valor_finaCA;
                                $cont++;
                            }
                            $somaCA_F = number_format($somaCA, 2, ",", ".");
                            $result_caixinha = mysql_query("SELECT saldo FROM caixinha WHERE id_regiao = '$regiao'");
                            while ($row_caixinha = mysql_fetch_array($result_caixinha)) {
                                $saldo_caixinha = (float) str_replace(",", ".", $row_caixinha['saldo']);
                                $saldo_caixinha_formatado = number_format($saldo_caixinha, 2, ",", ".");
                                $soma_saldo = $soma_saldo + $saldo_caixinha;
                            }
                            $saldo_caixinha = number_format($soma_saldo, 2, ",", ".");
                            $calculo_caixinha = $soma_saldo - $soma2;
                            $calculo_caixinha_f = number_format($calculo_caixinha, 2, ",", ".");
                            print "
    <tr class='linhaspeq' >
	<td height='18' colspan='3' align='center'>
	<table width='100%' class='tabela'>
	<tr> 
    <td width='50%' bgcolor='#DDD'><div align='center' style='color:#000000; font-size:12px'>TOTAL DE SA&Iacute;DAS DO CAIXA</b></div></td>
    <td width='50%' bgcolor='#DDD'><div align='center' style='color:#000000; font-size:12px'>SALDO DO CAIXA</b></div></td>
	</tr>
	<tr class='linhaspeq' >
	<td class='linhaspeq'><div align='center' style='color:#000000; font-size:12px'>R$ $somaCA_F</b></div></td>
	<td class='linhaspeq'><div align='center' style='color:#000000; font-size:12px'>R$ $saldo_caixinha_formatado</div></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr>
	</table>
	</td></tr></table>";
                            unset($soma_f);
                            unset($cont);
                            unset($soma);
                            unset($valor);
                            ?>

                        <?php } ?>
                        <!-- FIM SAIDA DE CAIXA -->


                </fieldset>



                <div class="rodape2">

                    <?php
                    $qr_master = mysql_query("SELECT * FROM master WHERE id_master = '$Master'");
                    $master = mysql_fetch_assoc($qr_master);
                    ?>
                    <?= $master['razao'] ?>
                    &nbsp;&nbsp;ACESSO RESTRITO &Agrave; FUNCION&Aacute;RIOS    
                </div>


            </div>
        </div>

    </body>
</html>